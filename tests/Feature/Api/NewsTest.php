<?php

use App\Models\News;
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

test('public can list news', function () {
    News::factory()->count(3)->create();

    $response = $this->getJson('/api/news');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'News retrieved successfully',
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

test('public can view a specific news', function () {
    $news = News::factory()->create();

    $response = $this->getJson("/api/news/{$news->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'News retrieved successfully',
        ])
        ->assertJsonPath('data.id', $news->id)
        ->assertJsonPath('data.title', $news->title);
});

test('admin can create a news', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'title' => 'Breaking News',
        'description' => 'This is a breaking news article.',
        'feature_image' => 'http://example.com/feature.jpg',
        'status' => 'active',
        'author_name' => 'John Doe',
        'author_image' => 'http://example.com/author.jpg',
        'author_designation' => 'Senior Reporter',
    ];

    $response = $this->postJson('/api/news', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'News created successfully',
        ]);

    $this->assertDatabaseHas('news', [
        'title' => 'Breaking News',
        'author_name' => 'John Doe',
        'author_designation' => 'Senior Reporter',
        'status' => 'active',
    ]);
});

test('admin can list news', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    News::factory()->count(3)->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/news');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'News retrieved successfully',
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

test('admin can update a news', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $news = News::factory()->create([
        'title' => 'Old Title',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'title' => 'Updated Title',
        'author_name' => 'Jane Smith',
        'status' => 'inactive',
    ];

    $response = $this->postJson("/api/news/{$news->id}", $payload);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'News updated successfully',
        ]);

    $this->assertDatabaseHas('news', [
        'id' => $news->id,
        'title' => 'Updated Title',
        'author_name' => 'Jane Smith',
        'status' => 'inactive',
    ]);
});

test('admin can delete a news', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $news = News::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->deleteJson("/api/news/{$news->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'News deleted successfully',
        ]);

    $this->assertSoftDeleted('news', [
        'id' => $news->id,
    ]);
});

test('admin can set news as active', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $news = News::factory()->create([
        'status' => 'inactive',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/news/{$news->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'News set as active successfully',
        ]);

    $this->assertDatabaseHas('news', [
        'id' => $news->id,
        'status' => 'active',
    ]);
});

test('admin can set news as inactive', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $news = News::factory()->create([
        'status' => 'active',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/news/{$news->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'News set as inactive successfully',
        ]);

    $this->assertDatabaseHas('news', [
        'id' => $news->id,
        'status' => 'inactive',
    ]);
});
