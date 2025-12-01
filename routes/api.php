<?php

use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomepageController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\PrivilegeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Password reset routes (public)
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    // OTP routes (public)
    Route::post('/send-otp', [OtpController::class, 'sendOtp']);
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

// Public Routes
Route::get('/homepages/active', [HomepageController::class, 'show']);
Route::get('/about-us/active', [AboutUsController::class, 'show']);

Route::middleware('auth:api')->group(function () {
    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->middleware('privilege:read-profile');
        Route::put('/', [ProfileController::class, 'update'])->middleware('privilege:update-profile');
        Route::patch('/', [ProfileController::class, 'update'])->middleware('privilege:update-profile');
    });

    // Privilege routes
    Route::prefix('privileges')->group(function () {
        Route::get('/', [PrivilegeController::class, 'index'])->middleware('privilege:read-privileges,read-roles,assign-privileges-to-roles');
        Route::get('/{privilege}', [PrivilegeController::class, 'show'])->middleware('privilege:read-privileges,read-roles,assign-privileges-to-roles');
    });

    // Role routes
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('privilege:read-roles,assign-privileges-to-roles');
        Route::get('/{role}', [RoleController::class, 'show'])->middleware('privilege:read-roles,assign-privileges-to-roles');
        Route::post('/', [RoleController::class, 'store'])->middleware('privilege:create-roles');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware('privilege:update-roles');
        Route::patch('/{role}', [RoleController::class, 'update'])->middleware('privilege:update-roles');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('privilege:delete-roles');
        Route::post('/{role}/privileges', [RoleController::class, 'assignPrivileges'])->middleware('privilege:assign-privileges-to-roles');
        Route::delete('/{role}/privileges', [RoleController::class, 'removePrivileges'])->middleware('privilege:assign-privileges-to-roles');
        Route::delete('/{role}/privileges/{privilege}', [RoleController::class, 'removePrivilege'])->middleware('privilege:assign-privileges-to-roles');
    });

    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('privilege:read-users');
        Route::get('/{user}', [UserController::class, 'show'])->middleware('privilege:read-users');
        Route::post('/{user}/suspend', [UserController::class, 'suspend'])->middleware('privilege:suspend-users');
        Route::post('/{user}/unsuspend', [UserController::class, 'unsuspend'])->middleware('privilege:suspend-users');
    });

    // Homepage routes (Admin)
    Route::prefix('homepages')->group(function () {
        Route::get('/', [HomepageController::class, 'index'])->middleware('privilege:read-homepages');
        // Route::get('/{homepage}', [HomepageController::class, 'showById'])->middleware('privilege:read-homepages');
        Route::post('/', [HomepageController::class, 'store'])->middleware('privilege:create-homepages');
        Route::put('/{homepage}', [HomepageController::class, 'update'])->middleware('privilege:update-homepages');
        // Route::patch('/{homepage}', [HomepageController::class, 'update'])->middleware('privilege:update-homepages');
        Route::delete('/{homepage}', [HomepageController::class, 'destroy'])->middleware('privilege:delete-homepages');
        // Route::post('/{homepage}/set-active', [HomepageController::class, 'setActive'])->middleware('privilege:update-homepages');
    });

    // About Us routes (Admin)
    Route::prefix('about-us')->group(function () {
        Route::get('/', [AboutUsController::class, 'index'])->middleware('privilege:read-about-us');
        // Route::get('/{aboutUs}', [AboutUsController::class, 'showById'])->middleware('privilege:read-about-us');
        Route::post('/', [AboutUsController::class, 'store'])->middleware('privilege:create-about-us');
        Route::put('/{aboutUs}', [AboutUsController::class, 'update'])->middleware('privilege:update-about-us');
        Route::delete('/{aboutUs}', [AboutUsController::class, 'destroy'])->middleware('privilege:delete-about-us');
        // Route::post('/{aboutUs}/set-active', [AboutUsController::class, 'setActive'])->middleware('privilege:update-about-us');
    });
});
