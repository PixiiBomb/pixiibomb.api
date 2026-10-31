<?php

namespace PixiiBomb\Api\Installers;

use App\Models\User;
use Illuminate\Console\Command;
use Laravel\Sanctum\HasApiTokens;
use PixiiBomb\Api\Database\Seeders\RoleSeeder;
use PixiiBomb\Api\Database\Seeders\UserSeeder;
use PixiiBomb\Api\Traits\HasPixiiApi;
use PixiiBomb\Core\Attributes\Installer;
use PixiiBomb\Core\Attributes\InstallerDetails;
use PixiiBomb\Core\Installers\PixiiBombInstaller;
use ReflectionException;

#[InstallerDetails('pixiibomb/api', priority: 10, icon: "💻")]
class PixiiApiInstaller extends PixiiBombInstaller
{
    #[Installer(
        isRequired: false,
        description: 'Reviews and optionally updates database values in the root .env file.'
    )]
    public function configureDatabase(Command $command): void
    {
        $values = $this->getDatabaseEnvValues();

        $this->log->info('Database values found in .env:');
        $this->log->line("DB_CONNECTION={$values['DB_CONNECTION']}");
        $this->log->line("DB_HOST={$values['DB_HOST']}");
        $this->log->line("DB_PORT={$values['DB_PORT']}");
        $this->log->line("DB_DATABASE={$values['DB_DATABASE']}");
        $this->log->line("DB_USERNAME={$values['DB_USERNAME']}");
        $this->log->line("DB_PASSWORD={$values['DB_PASSWORD']}");

        if (! $this->confirm($command, 'Would you like to rewrite these database values?', false)) {
            $this->log->warning('Skipped database configuration. You can update your .env file manually later.');
            return;
        }

        $databaseConnection = $this->ask($command, 'Enter DB connection', $values['DB_CONNECTION'] ?: 'mysql');
        $databaseName = $this->ask($command, 'Enter Database Name', $values['DB_DATABASE'] ?: $this->defaultDatabaseName());
        $databaseUser = $this->ask($command, 'Enter DB username', $values['DB_USERNAME'] ?: 'pixii');
        $databasePassword = $this->secret($command, 'Enter DB password');
        $databaseHost = $this->ask($command, 'Enter DB host', $values['DB_HOST'] ?: '127.0.0.1');
        $databasePort = $this->ask($command, 'Enter DB port', $values['DB_PORT'] ?: '3306');

        $this->updateDatabaseEnvFile(
            command: $command,
            databaseConnection: $databaseConnection,
            databaseName: $databaseName,
            databaseUser: $databaseUser,
            databasePassword: $databasePassword,
            databaseHost: $databaseHost,
            databasePort: $databasePort,
        );
    }

    /**
     * @throws ReflectionException
     */
    #[Installer(
        isRequired: true,
        description: "Modifies `App\\Models\\User`"
    )]
    public function modifyUserModel(Command $command): void
    {
        $this->injectFillableProperties([
            'role_id',
            'avatar',
        ]);

        $this->injectUseStatementForClass('use Laravel\Sanctum', User::class);
        $this->injectTraitIntoUserModel(
            command: $command,
            traitClass: HasApiTokens::class,
        );

        $this->injectTraitIntoUserModel(
            command: $command,
            traitClass: HasPixiiApi::class,
        );

        $this->callArtisan($command, 'install:api');
    }

    #[Installer(
        isRequired: true,
        description: 'Registers PixiiBomb API seeders in DatabaseSeeder.php.'
    )]
    public function registerSeeders(Command $command): void
    {
        $this->injectSeedersIntoDatabaseSeeder([
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }

    #[Installer(
        isRequired: false,
        description: 'Optionally runs database migrations and seeders.'
    )]
    public function migrateAndSeed(Command $command): void
    {
        $this->log->info(
            'If your database is created and ready, PixiiBomb can run migrations and/or seeders for you.'
        );

        if (! $this->confirm($command, 'Is your database created and ready to go?', true)) {
            $this->log->warning(
                'Skipped database setup. Manually run php artisan migrate and php artisan db:seed when ready.'
            );

            return;
        }

        $choice = $command->choice(
            'Which migration command would you like to run?',
            [
                'skip' => 'Skip migrations',
                'migrate' => 'php artisan migrate',
                'fresh' => 'php artisan migrate:fresh',
            ],
            'migrate'
        );

        if ($choice === 'migrate') {
            if (! $this->callArtisan($command, 'migrate')) {
                $this->log->error('Migration failed.');
                return;
            }

            $this->log->success('Migrations completed.');
        }

        if ($choice === 'fresh') {
            $this->log->caution('migrate:fresh will drop all tables and rebuild the database.');

            if (! $this->confirm($command, 'Are you sure you want to run migrate:fresh?', false)) {
                $this->log->warning('Skipped migrate:fresh.');
            } elseif (! $this->callArtisan($command, 'migrate:fresh')) {
                $this->log->error('migrate:fresh failed.');
                return;
            } else {
                $this->log->success('Database refreshed.');
            }
        }

        if (! $this->confirm($command, 'Would you like to seed the database?', true)) {
            $this->log->warning('Skipped database seeding.');
            return;
        }

        if (! $this->callArtisan($command, 'db:seed')) {
            $this->log->error('Database seeding failed.');
            return;
        }

        $this->log->success('Database seeded.');
    }
}
