<?php

use App\InstallmentStatus;
use App\Models\Investor;
use App\Models\InvestorInstallment;
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

test('admin can list investor installments', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    InvestorInstallment::factory()->count(3)->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/investor-installments');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Investor installments retrieved successfully',
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

test('admin can create investor installment', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $investor = Investor::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'investor_id' => $investor->id,
        'installment_number' => 1,
        'amount' => 50000.00,
        'due_date' => '2024-12-31',
        'status' => InstallmentStatus::Pending->value,
        'payment_method' => 'bank_transfer',
        'transaction_reference' => 'TXN-1234',
        'notes' => 'First installment payment',
    ];

    $response = $this->postJson('/api/investor-installments', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Investor installment created successfully',
        ]);

    $this->assertDatabaseHas('investor_installments', [
        'investor_id' => $investor->id,
        'installment_number' => 1,
        'amount' => 50000.00,
        'status' => InstallmentStatus::Pending->value,
    ]);
});

test('admin can view investor installment', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $installment = InvestorInstallment::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson("/api/investor-installments/{$installment->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Investor installment retrieved successfully',
        ])
        ->assertJsonPath('data.id', $installment->id);
});

test('admin can update investor installment', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $installment = InvestorInstallment::factory()->create([
        'status' => InstallmentStatus::Pending->value,
        'amount' => 50000.00,
        'due_date' => '2024-12-10',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'status' => InstallmentStatus::Paid->value,
        'paid_date' => '2024-12-15', // After due_date
        'payment_method' => 'bank_transfer',
        'transaction_reference' => 'TXN-5678',
    ];

    $response = $this->postJson("/api/investor-installments/{$installment->id}", $payload);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Investor installment updated successfully',
        ]);

    $this->assertDatabaseHas('investor_installments', [
        'id' => $installment->id,
        'status' => InstallmentStatus::Paid->value,
        'paid_date' => '2024-12-15 00:00:00',
    ]);
});

test('admin can delete investor installment', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $installment = InvestorInstallment::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->deleteJson("/api/investor-installments/{$installment->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Investor installment deleted successfully',
        ]);

    $this->assertSoftDeleted('investor_installments', [
        'id' => $installment->id,
    ]);
});

test('installment creation requires investor_id', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'installment_number' => 1,
        'amount' => 50000.00,
        'due_date' => '2024-12-31',
    ];

    $response = $this->postJson('/api/investor-installments', $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['investor_id']);
});

test('installment creation validates amount is numeric', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $investor = Investor::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'investor_id' => $investor->id,
        'installment_number' => 1,
        'amount' => 'invalid',
        'due_date' => '2024-12-31',
    ];

    $response = $this->postJson('/api/investor-installments', $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['amount']);
});

test('installment update validates paid_date is after due_date', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $installment = InvestorInstallment::factory()->create([
        'due_date' => '2024-12-31',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'paid_date' => '2024-12-01', // Before due_date
    ];

    $response = $this->postJson("/api/investor-installments/{$installment->id}", $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['paid_date']);
});
