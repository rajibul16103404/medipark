<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'bearer',
        ], 201);
    }

    /**
     * Authenticate a user and return a JWT token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // Check if user exists and is suspended before attempting authentication
        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->isSuspended()) {
            return response()->json([
                'message' => 'Your account has been suspended. Reason: '.$user->suspension_reason,
            ], 403);
        }

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }

        $user = Auth::guard('api')->user();

        // Double check after authentication
        if ($user && $user->isSuspended()) {
            // Invalidate the token if somehow it was created
            JWTAuth::invalidate($token);

            return response()->json([
                'message' => 'Your account has been suspended. Reason: '.$user->suspension_reason,
            ], 403);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated user.
     */
    public function me(): JsonResponse
    {
        $user = auth('api')->user();

        if ($user && $user->isSuspended()) {
            return response()->json([
                'message' => 'Your account has been suspended. Reason: '.$user->suspension_reason,
            ], 403);
        }

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        $user = auth('api')->user();

        if ($user && $user->isSuspended()) {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'message' => 'Your account has been suspended. Reason: '.$user->suspension_reason,
            ], 403);
        }

        return $this->respondWithToken(JWTAuth::refresh(JWTAuth::getToken()));
    }

    /**
     * Get the token array structure.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        $user = auth('api')->user();

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ]);
    }
}
