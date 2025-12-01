<?php

namespace Database\Seeders;

use App\Models\Privilege;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrivilegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $privileges = [
            // Profile privileges
            [
                'name' => 'Read Profile',
                'slug' => 'read-profile',
                'description' => 'Can view own profile',
            ],
            [
                'name' => 'Update Profile',
                'slug' => 'update-profile',
                'description' => 'Can update own profile information',
            ],
            // User privileges
            [
                'name' => 'Read Users',
                'slug' => 'read-users',
                'description' => 'Can view user list and details',
            ],
            [
                'name' => 'Create Users',
                'slug' => 'create-users',
                'description' => 'Can create new user accounts',
            ],
            [
                'name' => 'Update Users',
                'slug' => 'update-users',
                'description' => 'Can modify user information',
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'delete-users',
                'description' => 'Can delete user accounts',
            ],
            [
                'name' => 'Suspend Users',
                'slug' => 'suspend-users',
                'description' => 'Can suspend and unsuspend user accounts',
            ],
            // Role privileges
            [
                'name' => 'Read Roles',
                'slug' => 'read-roles',
                'description' => 'Can view roles and their details',
            ],
            [
                'name' => 'Create Roles',
                'slug' => 'create-roles',
                'description' => 'Can create new roles',
            ],
            [
                'name' => 'Update Roles',
                'slug' => 'update-roles',
                'description' => 'Can modify role information',
            ],
            [
                'name' => 'Delete Roles',
                'slug' => 'delete-roles',
                'description' => 'Can delete roles',
            ],
            [
                'name' => 'Assign Privileges to Roles',
                'slug' => 'assign-privileges-to-roles',
                'description' => 'Can assign or remove privileges from roles',
            ],
            // Privilege privileges
            [
                'name' => 'Read Privileges',
                'slug' => 'read-privileges',
                'description' => 'Can view privileges list and details',
            ],
            // Homepage privileges
            [
                'name' => 'Read Homepages',
                'slug' => 'read-homepages',
                'description' => 'Can view homepage list and details',
            ],
            [
                'name' => 'Create Homepages',
                'slug' => 'create-homepages',
                'description' => 'Can create new homepages',
            ],
            [
                'name' => 'Update Homepages',
                'slug' => 'update-homepages',
                'description' => 'Can modify homepage information',
            ],
            [
                'name' => 'Delete Homepages',
                'slug' => 'delete-homepages',
                'description' => 'Can delete homepages',
            ],
            // About Us privileges
            [
                'name' => 'Read About Us',
                'slug' => 'read-about-us',
                'description' => 'Can view about us page list and details',
            ],
            [
                'name' => 'Create About Us',
                'slug' => 'create-about-us',
                'description' => 'Can create new about us pages',
            ],
            [
                'name' => 'Update About Us',
                'slug' => 'update-about-us',
                'description' => 'Can modify about us page information',
            ],
            [
                'name' => 'Delete About Us',
                'slug' => 'delete-about-us',
                'description' => 'Can delete about us pages',
            ],
        ];

        foreach ($privileges as $privilege) {
            Privilege::firstOrCreate(
                ['slug' => $privilege['slug']],
                $privilege
            );
        }

        // Assign privileges to roles
        $adminRole = Role::where('slug', 'admin')->first();
        $managerRole = Role::where('slug', 'manager')->first();
        $userRole = Role::where('slug', 'user')->first();

        if ($adminRole) {
            // Admin gets all privileges
            $adminRole->privileges()->sync(Privilege::pluck('id'));
        }

        if ($managerRole) {
            // Manager gets profile, read users, create users, update users, and suspend users
            $managerPrivileges = Privilege::whereIn('slug', [
                'read-profile',
                'update-profile',
                'read-users',
                'create-users',
                'update-users',
                'suspend-users',
                'read-roles',
                'read-privileges',
            ])->pluck('id');
            $managerRole->privileges()->sync($managerPrivileges);
        }

        if ($userRole) {
            // User gets only profile privileges
            $userPrivileges = Privilege::whereIn('slug', [
                'read-profile',
                'update-profile',
            ])->pluck('id');
            $userRole->privileges()->sync($userPrivileges);
        }
    }
}
