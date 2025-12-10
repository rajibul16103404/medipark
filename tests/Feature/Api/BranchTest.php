<?php

use App\Models\Branch;
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

test('admin can create a branch', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'name' => 'Main Branch',
        'address' => '123 Main Street, Dhaka',
        'phone' => '01700000000',
        'email' => 'main@example.com',
        'status' => 'active',
    ];

    $response = $this->postJson('/api/branches', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Branch created successfully',
        ]);

    $this->assertDatabaseHas('branches', [
        'name' => 'Main Branch',
        'email' => 'main@example.com',
        'status' => 'active',
    ]);
});

test('admin can list branches', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    Branch::factory()->count(3)->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/branches');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Branches retrieved successfully',
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

test('admin can view a specific branch', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $branch = Branch::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson("/api/branches/{$branch->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Branch retrieved successfully',
        ])
        ->assertJsonPath('data.id', $branch->id)
        ->assertJsonPath('data.name', $branch->name);
});

test('admin can update a branch', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $branch = Branch::factory()->create([
        'name' => 'Old Name',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'name' => 'Updated Name',
        'address' => 'Updated Address',
        'phone' => '01811111111',
        'email' => 'updated@example.com',
        'status' => 'inactive',
    ];

    $response = $this->postJson("/api/branches/{$branch->id}", $payload);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Branch updated successfully',
        ]);

    $this->assertDatabaseHas('branches', [
        'id' => $branch->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'status' => 'inactive',
    ]);
});

test('admin can delete a branch', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $branch = Branch::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->deleteJson("/api/branches/{$branch->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Branch deleted successfully',
        ]);

    $this->assertSoftDeleted('branches', [
        'id' => $branch->id,
    ]);
});

test('admin can set branch as active', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $branch = Branch::factory()->create([
        'status' => 'inactive',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/branches/{$branch->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Branch set as active successfully',
        ]);

    $this->assertDatabaseHas('branches', [
        'id' => $branch->id,
        'status' => 'active',
    ]);
});

test('admin can set branch as inactive', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $branch = Branch::factory()->create([
        'status' => 'active',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/branches/{$branch->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Branch set as inactive successfully',
        ]);

    $this->assertDatabaseHas('branches', [
        'id' => $branch->id,
        'status' => 'inactive',
    ]);
});

test('multiple branches can be created', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    // Create first branch
    $payload1 = [
        'name' => 'Branch 1',
        'address' => 'Address 1',
        'phone' => '01700000001',
        'email' => 'branch1@example.com',
    ];

    $response1 = $this->postJson('/api/branches', $payload1);
    $response1->assertCreated();

    // Create second branch
    $payload2 = [
        'name' => 'Branch 2',
        'address' => 'Address 2',
        'phone' => '01700000002',
        'email' => 'branch2@example.com',
    ];

    $response2 = $this->postJson('/api/branches', $payload2);
    $response2->assertCreated();

    // Verify both exist
    expect(Branch::count())->toBe(2);
    $this->assertDatabaseHas('branches', ['name' => 'Branch 1']);
    $this->assertDatabaseHas('branches', ['name' => 'Branch 2']);
});
