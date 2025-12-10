<?php

use App\Models\Role;
use App\Models\SocialLink;
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

test('admin can create a social link', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'name' => 'Facebook',
        'image' => 'http://example.com/facebook.png',
        'link' => 'https://facebook.com/example',
        'status' => 'active',
    ];

    $response = $this->postJson('/api/social-links', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Social link created successfully',
        ]);

    $this->assertDatabaseHas('social_links', [
        'name' => 'Facebook',
        'link' => 'https://facebook.com/example',
        'status' => 'active',
    ]);
});

test('admin can list social links', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    SocialLink::factory()->count(3)->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/social-links');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Social links retrieved successfully',
        ])
        ->assertJsonStructure([
            'data',
            'pagination' => [
                'per_page',
                'total_count',
                'total_page',
                'current_page',
                'current_page_count',
                'next_page',
                'previous_page',
            ],
        ]);
});

test('admin can view a specific social link', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $socialLink = SocialLink::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson("/api/social-links/{$socialLink->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Social link retrieved successfully',
        ])
        ->assertJsonPath('data.id', $socialLink->id)
        ->assertJsonPath('data.name', $socialLink->name);
});

test('admin can update a social link', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $socialLink = SocialLink::factory()->create([
        'name' => 'Old Name',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'name' => 'Updated Name',
        'link' => 'https://updated.com',
        'status' => 'inactive',
    ];

    $response = $this->postJson("/api/social-links/{$socialLink->id}", $payload);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Social link updated successfully',
        ]);

    $this->assertDatabaseHas('social_links', [
        'id' => $socialLink->id,
        'name' => 'Updated Name',
        'link' => 'https://updated.com',
        'status' => 'inactive',
    ]);
});

test('admin can delete a social link', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $socialLink = SocialLink::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->deleteJson("/api/social-links/{$socialLink->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Social link deleted successfully',
        ]);

    $this->assertSoftDeleted('social_links', [
        'id' => $socialLink->id,
    ]);
});

test('admin can set social link as active', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $socialLink = SocialLink::factory()->create([
        'status' => 'inactive',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/social-links/{$socialLink->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Social link set as active successfully',
        ]);

    $this->assertDatabaseHas('social_links', [
        'id' => $socialLink->id,
        'status' => 'active',
    ]);
});

test('admin can set social link as inactive', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $socialLink = SocialLink::factory()->create([
        'status' => 'active',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/social-links/{$socialLink->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Social link set as inactive successfully',
        ]);

    $this->assertDatabaseHas('social_links', [
        'id' => $socialLink->id,
        'status' => 'inactive',
    ]);
});

test('multiple social links can be created', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    // Create first social link
    $payload1 = [
        'name' => 'Facebook',
        'link' => 'https://facebook.com',
        'status' => 'active',
    ];

    $response1 = $this->postJson('/api/social-links', $payload1);
    $response1->assertCreated();

    // Create second social link
    $payload2 = [
        'name' => 'Twitter',
        'link' => 'https://twitter.com',
        'status' => 'active',
    ];

    $response2 = $this->postJson('/api/social-links', $payload2);
    $response2->assertCreated();

    // Verify both exist
    expect(SocialLink::count())->toBe(2);
    $this->assertDatabaseHas('social_links', ['name' => 'Facebook']);
    $this->assertDatabaseHas('social_links', ['name' => 'Twitter']);
});
