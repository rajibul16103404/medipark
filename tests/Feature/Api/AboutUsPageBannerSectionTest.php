<?php

use App\Models\AboutUsPageBannerSection;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PrivilegeSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['jwt.secret' => 'test-secret']);
    config([
        'auth.guards.api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],
    ]);
});

test('public can view about us page banner section', function () {
    $bannerSection = AboutUsPageBannerSection::factory()->create([
        'status' => 'active',
    ]);

    $response = $this->getJson('/api/about-us-page-banner-sections');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'About us page banner section retrieved successfully',
        ])
        ->assertJsonPath('data.id', $bannerSection->id);
});

test('public cannot view about us page banner section when none exists', function () {
    $response = $this->getJson('/api/about-us-page-banner-sections');

    $response->assertNotFound()
        ->assertJson([
            'success' => false,
            'message' => 'About us page banner section not found',
        ]);
});

test('admin can create about us page banner section', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'background_image' => 'http://example.com/banner.jpg',
        'opacity' => '0.6',
        'status' => 'active',
    ];

    $response = $this->postJson('/api/about-us-page-banner-sections', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'About us page banner section created successfully',
        ]);

    $this->assertDatabaseHas('about_us_page_banner_sections', [
        'background_image' => 'http://example.com/banner.jpg',
        'opacity' => '0.6',
        'status' => 'active',
    ]);
});

test('admin updates existing about us page banner section via store', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $bannerSection = AboutUsPageBannerSection::factory()->create([
        'background_image' => 'http://example.com/old-banner.jpg',
        'opacity' => '0.4',
        'status' => 'inactive',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'background_image' => 'http://example.com/new-banner.jpg',
        'opacity' => '0.8',
        'status' => 'active',
    ];

    $response = $this->postJson('/api/about-us-page-banner-sections', $payload);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'About us page banner section updated successfully',
        ]);

    $this->assertDatabaseHas('about_us_page_banner_sections', [
        'id' => $bannerSection->id,
        'background_image' => 'http://example.com/new-banner.jpg',
        'opacity' => '0.8',
        'status' => 'active',
    ]);
});

test('store preserves existing background image when not provided', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $bannerSection = AboutUsPageBannerSection::factory()->create([
        'background_image' => 'http://example.com/existing-banner.jpg',
        'opacity' => '0.5',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'opacity' => '0.9',
    ];

    $response = $this->postJson('/api/about-us-page-banner-sections', $payload);

    $response->assertSuccessful();

    $this->assertDatabaseHas('about_us_page_banner_sections', [
        'id' => $bannerSection->id,
        'background_image' => 'http://example.com/existing-banner.jpg',
        'opacity' => '0.9',
    ]);
});

test('admin can set about us page banner section as active', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $bannerSection = AboutUsPageBannerSection::factory()->create([
        'status' => 'inactive',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson('/api/about-us-page-banner-sections/set-active');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'About us page banner section set as active successfully',
        ]);

    $this->assertDatabaseHas('about_us_page_banner_sections', [
        'id' => $bannerSection->id,
        'status' => 'active',
    ]);
});

test('admin can set about us page banner section as inactive', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $bannerSection = AboutUsPageBannerSection::factory()->create([
        'status' => 'active',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson('/api/about-us-page-banner-sections/set-active');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'About us page banner section set as inactive successfully',
        ]);

    $this->assertDatabaseHas('about_us_page_banner_sections', [
        'id' => $bannerSection->id,
        'status' => 'inactive',
    ]);
});

test('setActive returns error when no banner section exists', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson('/api/about-us-page-banner-sections/set-active');

    $response->assertNotFound()
        ->assertJson([
            'success' => false,
            'message' => 'About us page banner section not found',
        ]);
});

