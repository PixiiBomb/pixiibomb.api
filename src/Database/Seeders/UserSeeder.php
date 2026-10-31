<?php

namespace PixiiBomb\Api\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use PixiiBomb\Api\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users.
     */
    public function run(): void
    {
        $role = Role::where('key', 'super_admin')->first();

        User::updateOrCreate(
            ['email' => 'pixiibomb@gmail.com'],
            [
                'role_id' => $role?->id,
                'name' => 'PixiiBomb',
                'password' => Hash::make('nothing'),
                'avatar' => 'pixii.png'
            ]
        );
    }
}
