<?php

use App\Models\Doctor;
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

test('admin can create a doctor', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'doctor_name' => 'Dr. John Doe',
        'department' => 'Cardiology',
        'specialist' => 'Cardiologist',
        'email_address' => 'johndoe@example.com',
        'mobile_number' => '01700000000',
        'gender' => 'male',
        'date_of_birth' => '1980-01-01',
        'known_languages' => ['English', 'Bengali'],
        'registration_number' => 'REG-123456',
        'about' => 'Experienced cardiologist with 10 years of practice.',
        'present_address' => '123 Main Street, Dhaka',
        'permanent_address' => '123 Main Street, Dhaka',
    ];

    $response = $this->postJson('/api/doctors', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Doctor created successfully',
        ]);

    $this->assertDatabaseHas('doctors', [
        'doctor_name' => 'Dr. John Doe',
        'email_address' => 'johndoe@example.com',
    ]);
});

test('admin can list doctors', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    Doctor::factory()->count(3)->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/doctors');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Doctors retrieved successfully',
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

test('admin can view a specific doctor', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $doctor = Doctor::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson("/api/doctors/{$doctor->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Doctor retrieved successfully',
        ])
        ->assertJsonPath('data.id', $doctor->id)
        ->assertJsonPath('data.doctor_name', $doctor->doctor_name);
});

test('admin can update a doctor', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $doctor = Doctor::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'doctor_name' => 'Dr. Jane Smith',
        'department' => 'Neurology',
    ];

    $response = $this->putJson("/api/doctors/{$doctor->id}", $payload);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Doctor updated successfully',
        ]);

    $this->assertDatabaseHas('doctors', [
        'id' => $doctor->id,
        'doctor_name' => 'Dr. Jane Smith',
        'department' => 'Neurology',
    ]);
});

test('admin can delete a doctor', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $doctor = Doctor::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->deleteJson("/api/doctors/{$doctor->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Doctor deleted successfully',
        ]);

    $this->assertSoftDeleted('doctors', [
        'id' => $doctor->id,
    ]);
});

test('doctor identity number is auto-generated if not provided', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'doctor_name' => 'Dr. Test Doctor',
        'email_address' => 'test@example.com',
    ];

    $response = $this->postJson('/api/doctors', $payload);

    $response->assertCreated();

    $doctor = Doctor::where('doctor_name', 'Dr. Test Doctor')->first();
    expect($doctor)->not->toBeNull();
    expect($doctor->doctor_identity_number)->toStartWith('DOC');
});
