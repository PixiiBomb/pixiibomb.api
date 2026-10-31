<?php

namespace PixiiBomb\Api;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use PixiiBomb\Api\Installers\PixiiApiInstaller;
use PixiiBomb\Core\Support\PixiiBomb;

class PixiiBombApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(
            $this->getPackagePath('database/migrations')
        );

        /*$this->loadRoutesFrom(
            $this->getPackagePath('routes/api.php')
        ); */

        Route::prefix('api')
            ->group($this->getPackagePath('routes/api.php'));

        $this->configureApp();
    }

    private function configureApp(): void
    {
        app(PixiiBomb::class)
            ->registerInstaller(PixiiApiInstaller::class, 10);
    }

    private function getPackagePath(string $path): string
    {
        return __DIR__.'/../'.$path;
    }
}
