<?php

use App\Http\Controllers\Api\AuthController;
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
        Route::post('/{role}/privileges', [RoleController::class, 'assignPrivileges'])->middleware('privilege:assign-privileges-to-roles');
        Route::delete('/{role}/privileges', [RoleController::class, 'removePrivileges'])->middleware('privilege:assign-privileges-to-roles');
    });

    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('privilege:read-users');
        Route::get('/{user}', [UserController::class, 'show'])->middleware('privilege:read-users');
        Route::post('/{user}/suspend', [UserController::class, 'suspend'])->middleware('privilege:suspend-users');
        Route::post('/{user}/unsuspend', [UserController::class, 'unsuspend'])->middleware('privilege:suspend-users');
    });
});

