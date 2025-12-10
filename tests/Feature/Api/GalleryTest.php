<?php

use App\Models\Gallery;
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

test('public can list galleries', function () {
    Gallery::factory()->count(3)->create();

    $response = $this->getJson('/api/galleries');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Galleries retrieved successfully',
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

test('admin can create a gallery', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'title' => 'Gallery Title',
        'date' => '2024-01-15',
        'image' => 'http://example.com/gallery.jpg',
        'status' => 'active',
    ];

    $response = $this->postJson('/api/galleries', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Gallery created successfully',
        ]);

    $this->assertDatabaseHas('galleries', [
        'title' => 'Gallery Title',
        'date' => '2024-01-15 00:00:00',
        'status' => 'active',
    ]);
});

test('admin can list galleries', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    Gallery::factory()->count(3)->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/galleries');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Galleries retrieved successfully',
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

test('admin can view a specific gallery', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $gallery = Gallery::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson("/api/galleries/{$gallery->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Gallery retrieved successfully',
        ])
        ->assertJsonPath('data.id', $gallery->id)
        ->assertJsonPath('data.title', $gallery->title);
});

test('admin can update a gallery', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $gallery = Gallery::factory()->create([
        'title' => 'Old Title',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'title' => 'Updated Title',
        'date' => '2024-02-20',
        'status' => 'inactive',
    ];

    $response = $this->postJson("/api/galleries/{$gallery->id}", $payload);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Gallery updated successfully',
        ]);

    $this->assertDatabaseHas('galleries', [
        'id' => $gallery->id,
        'title' => 'Updated Title',
        'date' => '2024-02-20 00:00:00',
        'status' => 'inactive',
    ]);
});

test('admin can delete a gallery', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $gallery = Gallery::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->deleteJson("/api/galleries/{$gallery->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Gallery deleted successfully',
        ]);

    $this->assertSoftDeleted('galleries', [
        'id' => $gallery->id,
    ]);
});

test('admin can set gallery as active', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $gallery = Gallery::factory()->create([
        'status' => 'inactive',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/galleries/{$gallery->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Gallery set as active successfully',
        ]);

    $this->assertDatabaseHas('galleries', [
        'id' => $gallery->id,
        'status' => 'active',
    ]);
});

test('admin can set gallery as inactive', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $gallery = Gallery::factory()->create([
        'status' => 'active',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/galleries/{$gallery->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Gallery set as inactive successfully',
        ]);

    $this->assertDatabaseHas('galleries', [
        'id' => $gallery->id,
        'status' => 'inactive',
    ]);
});
