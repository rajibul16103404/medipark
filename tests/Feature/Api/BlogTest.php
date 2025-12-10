<?php

use App\Models\Blog;
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

test('public can list blogs', function () {
    Blog::factory()->count(3)->create();

    $response = $this->getJson('/api/blogs');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Blogs retrieved successfully',
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

test('public can view a specific blog', function () {
    $blog = Blog::factory()->create();

    $response = $this->getJson("/api/blogs/{$blog->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Blog retrieved successfully',
        ])
        ->assertJsonPath('data.id', $blog->id)
        ->assertJsonPath('data.title', $blog->title);
});

test('admin can create a blog', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $payload = [
        'title' => 'Blog Post Title',
        'description' => 'This is a blog post description.',
        'feature_image' => 'http://example.com/feature.jpg',
        'status' => 'active',
        'author_name' => 'John Doe',
        'author_image' => 'http://example.com/author.jpg',
        'author_designation' => 'Senior Writer',
    ];

    $response = $this->postJson('/api/blogs', $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Blog created successfully',
        ]);

    $this->assertDatabaseHas('blogs', [
        'title' => 'Blog Post Title',
        'author_name' => 'John Doe',
        'author_designation' => 'Senior Writer',
        'status' => 'active',
    ]);
});

test('admin can list blogs', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    Blog::factory()->count(3)->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->getJson('/api/blogs');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Blogs retrieved successfully',
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

test('admin can update a blog', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $blog = Blog::factory()->create([
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

    $response = $this->postJson("/api/blogs/{$blog->id}", $payload);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Blog updated successfully',
        ]);

    $this->assertDatabaseHas('blogs', [
        'id' => $blog->id,
        'title' => 'Updated Title',
        'author_name' => 'Jane Smith',
        'status' => 'inactive',
    ]);
});

test('admin can delete a blog', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $blog = Blog::factory()->create();

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->deleteJson("/api/blogs/{$blog->id}");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Blog deleted successfully',
        ]);

    $this->assertSoftDeleted('blogs', [
        'id' => $blog->id,
    ]);
});

test('admin can set blog as active', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $blog = Blog::factory()->create([
        'status' => 'inactive',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/blogs/{$blog->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Blog set as active successfully',
        ]);

    $this->assertDatabaseHas('blogs', [
        'id' => $blog->id,
        'status' => 'active',
    ]);
});

test('admin can set blog as inactive', function () {
    $this->seed([
        RoleSeeder::class,
        PrivilegeSeeder::class,
    ]);

    $blog = Blog::factory()->create([
        'status' => 'active',
    ]);

    $adminRole = Role::where('slug', 'admin')->first();
    $admin = User::factory()->create();
    $admin->roles()->sync([$adminRole->id]);

    $this->actingAs($admin, 'api');

    $response = $this->postJson("/api/blogs/{$blog->id}/set-active");

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'message' => 'Blog set as inactive successfully',
        ]);

    $this->assertDatabaseHas('blogs', [
        'id' => $blog->id,
        'status' => 'inactive',
    ]);
});
