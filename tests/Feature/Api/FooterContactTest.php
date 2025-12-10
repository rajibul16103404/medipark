<?php

use App\Models\FooterContact;
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

test('admin can view footer contact', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $footerContact = FooterContact::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/footer-contact');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Footer contact retrieved successfully',
        ])
        ->assertJsonPath('data.id', $footerContact->id)
        ->assertJsonPath('data.email', $footerContact->email);
});

test('admin can create footer contact when none exists', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'email' => 'contact@example.com',
        'phone' => ['01700000000', '01800000000'],
        'status' => 'active',
    ];

    $response = $this->postJson('/api/footer-contact', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Footer contact created successfully',
        ]);

    $this->assertDatabaseHas('footer_contacts', [
        'email' => 'contact@example.com',
        'status' => 'active',
    ]);

    // Verify only one record exists
    expect(FooterContact::count())->toBe(1);
});

test('admin can update footer contact when one exists', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $footerContact = FooterContact::factory()->create([
        'email' => 'old@example.com',
        'phone' => ['01700000000'],
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'email' => 'new@example.com',
        'phone' => ['01711111111', '01822222222'],
        'status' => 'inactive',
    ];

    $response = $this->postJson('/api/footer-contact', $payload);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Footer contact updated successfully',
        ]);

    $this->assertDatabaseHas('footer_contacts', [
        'id' => $footerContact->id,
        'email' => 'new@example.com',
        'status' => 'inactive',
    ]);

    // Verify still only one record exists
    expect(FooterContact::count())->toBe(1);
});

test('footer contact returns 404 when none exists', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/footer-contact');

    $response->assertNotFound()
        ->assertJson([
            'success' => false,
            'message' => 'Footer contact not found',
        ]);
});

test('only one footer contact record can exist', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    // Create first record
    FooterContact::factory()->create([
        'email' => 'first@example.com',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    // Try to create another (should update instead)
    $payload = [
        'email' => 'second@example.com',
        'phone' => ['01700000000'],
    ];

    $response = $this->postJson('/api/footer-contact', $payload);

    $response->assertSuccessful();

    // Verify only one record exists
    expect(FooterContact::count())->toBe(1);
    expect(FooterContact::first()->email)->toBe('second@example.com');
});
