<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $managerRole = Role::where('slug', 'manager')->first();
        $userRole = Role::where('slug', 'user')->first();

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if ($adminRole && ! $admin->roles->contains($adminRole)) {
            $admin->roles()->attach($adminRole);
        }

        // Create manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if ($managerRole && ! $manager->roles->contains($managerRole)) {
            $manager->roles()->attach($managerRole);
        }

        // Create regular user
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if ($userRole && ! $user->roles->contains($userRole)) {
            $user->roles()->attach($userRole);
        }
    }
}
