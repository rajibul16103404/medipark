<?php

use App\Models\Facility;
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

test('admin can create a facility', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'title' => 'ICU',
        'short_description' => 'State-of-the-art intensive care unit.',
        'image' => 'http://example.com/icu.jpg',
        'status' => 'active',
    ];

    $response = $this->postJson('/api/facilities', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Facility created successfully',
        ]);

    $this->assertDatabaseHas('facilities', [
        'title' => 'ICU',
        'status' => 'active',
    ]);
});

test('admin can list facilities', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    Facility::factory()->count(3)->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/facilities');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Facilities retrieved successfully',
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

test('admin can view a specific facility', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $facility = Facility::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson("/api/facilities/{$facility->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Facility retrieved successfully',
        ])
        ->assertJsonPath('data.id', $facility->id)
        ->assertJsonPath('data.title', $facility->title);
});

test('admin can update a facility', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $facility = Facility::factory()->create([
        'title' => 'Old Title',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'title' => 'Updated Title',
        'status' => 'inactive',
    ];

    $response = $this->postJson("/api/facilities/{$facility->id}", $payload);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Facility updated successfully',
        ]);

    $this->assertDatabaseHas('facilities', [
        'id' => $facility->id,
        'title' => 'Updated Title',
        'status' => 'inactive',
    ]);
});

test('admin can delete a facility', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $facility = Facility::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->deleteJson("/api/facilities/{$facility->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Facility deleted successfully',
        ]);

    $this->assertSoftDeleted('facilities', [
        'id' => $facility->id,
    ]);
});

test('admin can set facility as active', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $facility = Facility::factory()->create([
        'status' => 'inactive',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/facilities/{$facility->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Facility set as active successfully',
        ]);

    $this->assertDatabaseHas('facilities', [
        'id' => $facility->id,
        'status' => 'active',
    ]);
});

test('admin can set facility as inactive', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $facility = Facility::factory()->create([
        'status' => 'active',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/facilities/{$facility->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Facility set as inactive successfully',
        ]);

    $this->assertDatabaseHas('facilities', [
        'id' => $facility->id,
        'status' => 'inactive',
    ]);
});
