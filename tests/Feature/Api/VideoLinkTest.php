<?php

use App\Models\Role;
use App\Models\User;
use App\Models\VideoLink;
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

test('public can view video link', function () {
    $videoLink = VideoLink::factory()->create([
        'status' => 'active',
    ]);

    $response = $this->getJson('/api/video-links');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Video link retrieved successfully',
        ])
        ->assertJsonPath('data.id', $videoLink->id);
});

test('public cannot view video link when none exists', function () {
    $response = $this->getJson('/api/video-links');

    $response->assertNotFound()
        ->assertJson([
            'success' => false,
            'message' => 'Video link not found',
        ]);
});

test('admin can create a video link', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'title' => 'Sample Video',
        'description' => 'This is a sample video description',
        'video' => 'http://example.com/video.mp4',
        'status' => 'active',
    ];

    $response = $this->postJson('/api/video-links', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Video link created successfully',
        ]);

    $this->assertDatabaseHas('video_links', [
        'title' => 'Sample Video',
        'description' => 'This is a sample video description',
        'video' => 'http://example.com/video.mp4',
        'status' => 'active',
    ]);
});

test('admin can update existing video link', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $videoLink = VideoLink::factory()->create([
        'title' => 'Old Title',
        'description' => 'Old description',
        'video' => 'http://example.com/old-video.mp4',
        'status' => 'active',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'title' => 'New Title',
        'description' => 'New description',
        'video' => 'http://example.com/new-video.mp4',
        'status' => 'inactive',
    ];

    $response = $this->postJson('/api/video-links', $payload);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Video link updated successfully',
        ]);

    $this->assertDatabaseHas('video_links', [
        'id' => $videoLink->id,
        'title' => 'New Title',
        'description' => 'New description',
        'video' => 'http://example.com/new-video.mp4',
        'status' => 'inactive',
    ]);
});

test('admin can set video link as active', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $videoLink = VideoLink::factory()->create([
        'status' => 'inactive',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson('/api/video-links/set-active');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Video link set as active successfully',
        ]);

    $this->assertDatabaseHas('video_links', [
        'id' => $videoLink->id,
        'status' => 'active',
    ]);
});

test('admin can set video link as inactive', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $videoLink = VideoLink::factory()->create([
        'status' => 'active',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson('/api/video-links/set-active');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Video link set as inactive successfully',
        ]);

    $this->assertDatabaseHas('video_links', [
        'id' => $videoLink->id,
        'status' => 'inactive',
    ]);
});

test('setActive returns error when no video link exists', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson('/api/video-links/set-active');

    $response->assertNotFound()
        ->assertJson([
            'success' => false,
            'message' => 'Video link not found',
        ]);
});

test('store preserves existing video when not provided', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $videoLink = VideoLink::factory()->create([
        'title' => 'Existing Title',
        'video' => 'http://example.com/existing-video.mp4',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'title' => 'Updated Title',
    ];

    $response = $this->postJson('/api/video-links', $payload);

    $response->assertSuccessful();

    $this->assertDatabaseHas('video_links', [
        'id' => $videoLink->id,
        'title' => 'Updated Title',
        'video' => 'http://example.com/existing-video.mp4',
    ]);
});
