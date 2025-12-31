<?php

use App\Models\Department;
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

test('admin can create a department', function () {
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

    $response = $this->postJson('/api/departments', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Department created successfully',
        ]);

    $this->assertDatabaseHas('departments', [
        'title' => 'ICU',
        'status' => 'active',
    ]);
});

test('admin can list departments', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    Department::factory()->count(3)->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/departments');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Departments retrieved successfully',
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

test('admin can view a specific department', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $department = Department::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson("/api/departments/{$department->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Department retrieved successfully',
        ])
        ->assertJsonPath('data.id', $department->id)
        ->assertJsonPath('data.title', $department->title);
});

test('admin can update a department', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $department = Department::factory()->create([
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

    $response = $this->postJson("/api/departments/{$department->id}", $payload);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Department updated successfully',
        ]);

    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'title' => 'Updated Title',
        'status' => 'inactive',
    ]);
});

test('admin can delete a department', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $department = Department::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->deleteJson("/api/departments/{$department->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Department deleted successfully',
        ]);

    $this->assertSoftDeleted('departments', [
        'id' => $department->id,
    ]);
});

test('admin can set department as active', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $department = Department::factory()->create([
        'status' => 'inactive',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/departments/{$department->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Department set as active successfully',
        ]);

    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'status' => 'active',
    ]);
});

test('admin can set department as inactive', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $department = Department::factory()->create([
        'status' => 'active',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/departments/{$department->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Department set as inactive successfully',
        ]);

    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'status' => 'inactive',
    ]);
});
