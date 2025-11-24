<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    Route::middleware(['auth:api', 'role:admin'])->get('/test-admin', function () {
        return response()->json(['message' => 'Access granted']);
    });

    Route::middleware(['auth:api', 'role:manager'])->get('/test-manager', function () {
        return response()->json(['message' => 'Access granted']);
    });
});

test('user with required role can access protected route', function () {
    $role = Role::create([
        'name' => 'Admin',
        'slug' => 'admin',
    ]);

    $user = User::factory()->create();
    $user->roles()->attach($role);

    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/test-admin');

    $response->assertSuccessful()
        ->assertJson([
            'message' => 'Access granted',
        ]);
});

test('user without required role cannot access protected route', function () {
    $role = Role::create([
        'name' => 'User',
        'slug' => 'user',
    ]);

    $user = User::factory()->create();
    $user->roles()->attach($role);

    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/test-admin');

    $response->assertForbidden()
        ->assertJson([
            'message' => 'You do not have the required role to access this resource',
        ]);
});

test('user with any of multiple roles can access protected route', function () {
    $adminRole = Role::create([
        'name' => 'Admin',
        'slug' => 'admin',
    ]);

    $user = User::factory()->create();
    $user->roles()->attach($adminRole);

    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/test-admin');

    $response->assertSuccessful();
});

test('unauthenticated user cannot access role protected route', function () {
    $response = $this->getJson('/test-admin');

    $response->assertUnauthorized()
        ->assertJson([
            'message' => 'Unauthenticated',
        ]);
});

test('suspended user cannot access role protected route', function () {
    $role = Role::create([
        'name' => 'Admin',
        'slug' => 'admin',
    ]);

    $user = User::factory()->create([
        'suspended_at' => now(),
        'suspension_reason' => 'Account suspended',
    ]);
    $user->roles()->attach($role);

    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/test-admin');

    $response->assertForbidden()
        ->assertJson([
            'message' => 'Your account has been suspended. Reason: Account suspended',
        ]);
});

test('user can have multiple roles', function () {
    $adminRole = Role::create([
        'name' => 'Admin',
        'slug' => 'admin',
    ]);

    $managerRole = Role::create([
        'name' => 'Manager',
        'slug' => 'manager',
    ]);

    $user = User::factory()->create();
    $user->roles()->attach([$adminRole->id, $managerRole->id]);

    expect($user->hasRole('admin'))->toBeTrue();
    expect($user->hasRole('manager'))->toBeTrue();
    expect($user->hasAnyRole(['admin', 'manager']))->toBeTrue();
});
