<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('user can register', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'name',
                'email',
            ],
            'token',
            'token_type',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);
});

test('user cannot register with invalid data', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => '',
        'email' => 'invalid-email',
        'password' => '123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

test('user cannot register with duplicate email', function () {
    User::factory()->create(['email' => 'john@example.com']);

    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'name',
                'email',
            ],
            'token',
            'token_type',
            'expires_in',
        ]);
});

test('user cannot login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertUnauthorized()
        ->assertJson([
            'message' => 'Invalid email or password',
        ]);
});

test('user cannot login with invalid email', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
    ]);

    $response->assertUnauthorized();
});

test('authenticated user can get their profile', function () {
    $user = User::factory()->create();

    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/auth/me');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
            ],
        ])
        ->assertJson([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
        ]);
});

test('unauthenticated user cannot get profile', function () {
    $response = $this->getJson('/api/auth/me');

    $response->assertUnauthorized();
});

test('authenticated user can logout', function () {
    $user = User::factory()->create();

    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/auth/logout');

    $response->assertSuccessful()
        ->assertJson([
            'message' => 'Successfully logged out',
        ]);
});

test('authenticated user can refresh token', function () {
    $user = User::factory()->create();

    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/auth/refresh');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'message',
            'user',
            'token',
            'token_type',
            'expires_in',
        ]);
});

test('suspended user cannot login', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
        'suspended_at' => now(),
        'suspension_reason' => 'Violation of terms',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response->assertForbidden()
        ->assertJson([
            'message' => 'Your account has been suspended. Reason: Violation of terms',
        ]);
});
