<?php

namespace Database\Seeders;

use App\Models\Privilege;
use App\Models\Role;
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
            // Homepage Hero Section privileges
            [
                'name' => 'Read Homepage Hero Sections',
                'slug' => 'read-homepage-hero-sections',
                'description' => 'Can view homepage hero section list and details',
            ],
            [
                'name' => 'Create Homepage Hero Sections',
                'slug' => 'create-homepage-hero-sections',
                'description' => 'Can create new homepage hero sections',
            ],
            [
                'name' => 'Update Homepage Hero Sections',
                'slug' => 'update-homepage-hero-sections',
                'description' => 'Can modify homepage hero section information',
            ],
            [
                'name' => 'Delete Homepage Hero Sections',
                'slug' => 'delete-homepage-hero-sections',
                'description' => 'Can delete homepage hero sections',
            ],
            // Homepage About Us Section privileges
            [
                'name' => 'Read Homepage About Us Sections',
                'slug' => 'read-homepage-about-us-sections',
                'description' => 'Can view homepage about us section list and details',
            ],
            [
                'name' => 'Create Homepage About Us Sections',
                'slug' => 'create-homepage-about-us-sections',
                'description' => 'Can create new homepage about us sections',
            ],
            [
                'name' => 'Update Homepage About Us Sections',
                'slug' => 'update-homepage-about-us-sections',
                'description' => 'Can modify homepage about us section information',
            ],
            [
                'name' => 'Delete Homepage About Us Sections',
                'slug' => 'delete-homepage-about-us-sections',
                'description' => 'Can delete homepage about us sections',
            ],
            // Homepage CTA Section privileges
            [
                'name' => 'Read Homepage CTA Sections',
                'slug' => 'read-homepage-cta-sections',
                'description' => 'Can view homepage CTA section list and details',
            ],
            [
                'name' => 'Create Homepage CTA Sections',
                'slug' => 'create-homepage-cta-sections',
                'description' => 'Can create new homepage CTA sections',
            ],
            [
                'name' => 'Update Homepage CTA Sections',
                'slug' => 'update-homepage-cta-sections',
                'description' => 'Can modify homepage CTA section information',
            ],
            [
                'name' => 'Delete Homepage CTA Sections',
                'slug' => 'delete-homepage-cta-sections',
                'description' => 'Can delete homepage CTA sections',
            ],
            // About Us Page Banner Section privileges
            [
                'name' => 'Read About Us Page Banner Sections',
                'slug' => 'read-about-us-page-banner-sections',
                'description' => 'Can view about us page banner section list and details',
            ],
            [
                'name' => 'Create About Us Page Banner Sections',
                'slug' => 'create-about-us-page-banner-sections',
                'description' => 'Can create new about us page banner sections',
            ],
            [
                'name' => 'Update About Us Page Banner Sections',
                'slug' => 'update-about-us-page-banner-sections',
                'description' => 'Can modify about us page banner section information',
            ],
            [
                'name' => 'Delete About Us Page Banner Sections',
                'slug' => 'delete-about-us-page-banner-sections',
                'description' => 'Can delete about us page banner sections',
            ],
            // About Us Page Who We Are Section privileges
            [
                'name' => 'Read About Us Page Who We Are Sections',
                'slug' => 'read-about-us-page-who-we-are-sections',
                'description' => 'Can view about us page who we are section list and details',
            ],
            [
                'name' => 'Create About Us Page Who We Are Sections',
                'slug' => 'create-about-us-page-who-we-are-sections',
                'description' => 'Can create new about us page who we are sections',
            ],
            [
                'name' => 'Update About Us Page Who We Are Sections',
                'slug' => 'update-about-us-page-who-we-are-sections',
                'description' => 'Can modify about us page who we are section information',
            ],
            [
                'name' => 'Delete About Us Page Who We Are Sections',
                'slug' => 'delete-about-us-page-who-we-are-sections',
                'description' => 'Can delete about us page who we are sections',
            ],
            // About Us Page Our Mission Section privileges
            [
                'name' => 'Read About Us Page Our Mission Sections',
                'slug' => 'read-about-us-page-our-mission-sections',
                'description' => 'Can view about us page our mission section list and details',
            ],
            [
                'name' => 'Create About Us Page Our Mission Sections',
                'slug' => 'create-about-us-page-our-mission-sections',
                'description' => 'Can create new about us page our mission sections',
            ],
            [
                'name' => 'Update About Us Page Our Mission Sections',
                'slug' => 'update-about-us-page-our-mission-sections',
                'description' => 'Can modify about us page our mission section information',
            ],
            [
                'name' => 'Delete About Us Page Our Mission Sections',
                'slug' => 'delete-about-us-page-our-mission-sections',
                'description' => 'Can delete about us page our mission sections',
            ],
            // About Us Page Our Vision Section privileges
            [
                'name' => 'Read About Us Page Our Vision Sections',
                'slug' => 'read-about-us-page-our-vision-sections',
                'description' => 'Can view about us page our vision section list and details',
            ],
            [
                'name' => 'Create About Us Page Our Vision Sections',
                'slug' => 'create-about-us-page-our-vision-sections',
                'description' => 'Can create new about us page our vision sections',
            ],
            [
                'name' => 'Update About Us Page Our Vision Sections',
                'slug' => 'update-about-us-page-our-vision-sections',
                'description' => 'Can modify about us page our vision section information',
            ],
            [
                'name' => 'Delete About Us Page Our Vision Sections',
                'slug' => 'delete-about-us-page-our-vision-sections',
                'description' => 'Can delete about us page our vision sections',
            ],
            // About Us Page After Our Vision Section privileges
            [
                'name' => 'Read About Us Page After Our Vision Sections',
                'slug' => 'read-about-us-page-after-our-vision-sections',
                'description' => 'Can view about us page after our vision section list and details',
            ],
            [
                'name' => 'Create About Us Page After Our Vision Sections',
                'slug' => 'create-about-us-page-after-our-vision-sections',
                'description' => 'Can create new about us page after our vision sections',
            ],
            [
                'name' => 'Update About Us Page After Our Vision Sections',
                'slug' => 'update-about-us-page-after-our-vision-sections',
                'description' => 'Can modify about us page after our vision section information',
            ],
            [
                'name' => 'Delete About Us Page After Our Vision Sections',
                'slug' => 'delete-about-us-page-after-our-vision-sections',
                'description' => 'Can delete about us page after our vision sections',
            ],
            // About Us Page 2nd After Our Vision Section privileges
            [
                'name' => 'Read About Us Page 2nd After Our Vision Sections',
                'slug' => 'read-about-us-page-2nd-after-our-vision-sections',
                'description' => 'Can view about us page 2nd after our vision section list and details',
            ],
            [
                'name' => 'Create About Us Page 2nd After Our Vision Sections',
                'slug' => 'create-about-us-page-2nd-after-our-vision-sections',
                'description' => 'Can create new about us page 2nd after our vision sections',
            ],
            [
                'name' => 'Update About Us Page 2nd After Our Vision Sections',
                'slug' => 'update-about-us-page-2nd-after-our-vision-sections',
                'description' => 'Can modify about us page 2nd after our vision section information',
            ],
            [
                'name' => 'Delete About Us Page 2nd After Our Vision Sections',
                'slug' => 'delete-about-us-page-2nd-after-our-vision-sections',
                'description' => 'Can delete about us page 2nd after our vision sections',
            ],
            // Contact privileges
            [
                'name' => 'Read Contacts',
                'slug' => 'read-contacts',
                'description' => 'Can view contact list and details',
            ],
            [
                'name' => 'Delete Contacts',
                'slug' => 'delete-contacts',
                'description' => 'Can delete contacts',
            ],
            // Investor privileges
            [
                'name' => 'Read Investors',
                'slug' => 'read-investors',
                'description' => 'Can view investor list and details',
            ],
            [
                'name' => 'Create Investors',
                'slug' => 'create-investors',
                'description' => 'Can create new investors',
            ],
            [
                'name' => 'Update Investors',
                'slug' => 'update-investors',
                'description' => 'Can modify investor information',
            ],
            [
                'name' => 'Delete Investors',
                'slug' => 'delete-investors',
                'description' => 'Can delete investors',
            ],
            // Doctor privileges
            [
                'name' => 'Read Doctors',
                'slug' => 'read-doctors',
                'description' => 'Can view doctor list and details',
            ],
            [
                'name' => 'Create Doctors',
                'slug' => 'create-doctors',
                'description' => 'Can create new doctors',
            ],
            [
                'name' => 'Update Doctors',
                'slug' => 'update-doctors',
                'description' => 'Can modify doctor information',
            ],
            [
                'name' => 'Delete Doctors',
                'slug' => 'delete-doctors',
                'description' => 'Can delete doctors',
            ],
            // Facility privileges
            [
                'name' => 'Read Facilities',
                'slug' => 'read-facilities',
                'description' => 'Can view facility list and details',
            ],
            [
                'name' => 'Create Facilities',
                'slug' => 'create-facilities',
                'description' => 'Can create new facilities',
            ],
            [
                'name' => 'Update Facilities',
                'slug' => 'update-facilities',
                'description' => 'Can modify facility information',
            ],
            [
                'name' => 'Delete Facilities',
                'slug' => 'delete-facilities',
                'description' => 'Can delete facilities',
            ],
            // Footer Contact privileges
            [
                'name' => 'Read Footer Contact',
                'slug' => 'read-footer-contact',
                'description' => 'Can view footer contact information',
            ],
            [
                'name' => 'Update Footer Contact',
                'slug' => 'update-footer-contact',
                'description' => 'Can create or update footer contact information',
            ],
            // Social Link privileges
            [
                'name' => 'Read Social Links',
                'slug' => 'read-social-links',
                'description' => 'Can view social link list and details',
            ],
            [
                'name' => 'Create Social Links',
                'slug' => 'create-social-links',
                'description' => 'Can create new social links',
            ],
            [
                'name' => 'Update Social Links',
                'slug' => 'update-social-links',
                'description' => 'Can modify social link information',
            ],
            [
                'name' => 'Delete Social Links',
                'slug' => 'delete-social-links',
                'description' => 'Can delete social links',
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
