<?php

use App\Models\Investor;
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

    $this->withoutMiddleware(\App\Http\Middleware\CheckSuspendedUser::class);
});

test('public can submit investor form', function () {
    $payload = [
        'file_number' => 'INV-1001',
        'applicant_full_name' => 'Jane Doe',
        'mobile_number' => '01700000000',
        'email' => 'jane@example.com',
        'project_name' => 'Project Alpha',
        'price_per_hss' => 100000.50,
        'number_of_hss' => 2,
    ];

    $response = $this->postJson('/api/investors', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Investor created successfully',
        ]);

    $this->assertDatabaseHas('investors', [
        'file_number' => 'INV-1001',
        'applicant_full_name' => 'Jane Doe',
    ]);
});

test('admin can list investors', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    Investor::factory()->count(3)->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/investors');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Investors retrieved successfully',
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
