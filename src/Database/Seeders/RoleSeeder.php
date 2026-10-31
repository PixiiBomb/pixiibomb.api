<?php

namespace PixiiBomb\Api\Database\Seeders;

use Illuminate\Database\Seeder;
use PixiiBomb\Api\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's default roles.
     */
    public function run(): void
    {
        $roles = [
            [
                'key' => 'guest',
                'display_name' => 'Guest',
                'description' => 'Unauthenticated or limited-access user.',
                'is_system' => true,
                'priority' => 0,
            ],
            [
                'key' => 'user',
                'display_name' => 'User',
                'description' => 'Standard authenticated user.',
                'is_system' => true,
                'priority' => 1,
            ],
            [
                'key' => 'silver',
                'display_name' => 'Silver User',
                'description' => 'Entry-level premium member.',
                'is_system' => false,
                'priority' => 2,
            ],
            [
                'key' => 'gold',
                'display_name' => 'Gold User',
                'description' => 'Advanced premium member.',
                'is_system' => false,
                'priority' => 3,
            ],
            [
                'key' => 'moderator',
                'display_name' => 'Moderator',
                'description' => 'Community moderation permissions.',
                'is_system' => true,
                'priority' => 4,
            ],
            [
                'key' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Administrative permissions.',
                'is_system' => true,
                'priority' => 5,
            ],
            [
                'key' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full unrestricted system access.',
                'is_system' => true,
                'priority' => 6,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['key' => $role['key']],
                $role
            );
        }
    }
}
