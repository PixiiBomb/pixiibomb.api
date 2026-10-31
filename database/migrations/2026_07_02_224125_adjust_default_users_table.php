<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->foreignId('role_id')
                ->nullable()
                ->after('id')
                ->constrained('roles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
             * Relative path to the user's avatar image.
             *
             * Examples:
             * - /images/avatars/pixii.png
             * - /storage/avatars/uuid-file.png
             */
            $table->string('avatar')
                ->nullable()
                ->after('email');

            $table->boolean('is_archived')
                ->default(false);

            $table->timestamp('archived_at')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);

            $table->dropColumn([
                'role_id',
                'avatar',
                'is_archived',
                'archived_at',
            ]);

            $table->renameColumn('username', 'name');
        });
    }
};
