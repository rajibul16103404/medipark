<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Mail\WelcomeMail;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponse;

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

        // Load roles for the response
        $user->load('roles');

        // Send welcome email
        Mail::to($user->email)->send(new WelcomeMail($user));

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'bearer',
            ],
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
            return $this->errorResponse('Your account has been suspended', 403, ['suspension_reason' => $user->suspension_reason]);
        }

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return $this->errorResponse('Invalid email or password', 401);
        }

        /** @var User|null $user */
        $user = Auth::guard('api')->user();

        // Double check after authentication
        if ($user && $user->isSuspended()) {
            // Invalidate the token if somehow it was created
            JWTAuth::invalidate($token);

            return $this->errorResponse('Your account has been suspended. Reason: '.$user->suspension_reason, 403);
        }

        // Load roles for the response
        if ($user) {
            $user->load('roles');
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated user.
     */
    public function me(): JsonResponse
    {
        /** @var User|null $user */
        $user = auth('api')->user();

        if (! $user) {
            return $this->errorResponse('Unauthenticated', 401);
        }

        if ($user->isSuspended()) {
            return $this->errorResponse('Your account has been suspended. Reason: '.$user->suspension_reason, 403);
        }

        // Load roles for the response
        $user->load('roles');

        return $this->successResponse('User retrieved successfully', new UserResource($user));
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return $this->successResponse('Successfully logged out');
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        /** @var User|null $user */
        $user = auth('api')->user();

        if (! $user) {
            return $this->errorResponse('Unauthenticated', 401);
        }

        if ($user->isSuspended()) {
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->errorResponse('Your account has been suspended. Reason: '.$user->suspension_reason, 403);
        }

        return $this->respondWithToken(JWTAuth::refresh(JWTAuth::getToken()));
    }

    /**
     * Get the token array structure.
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        /** @var User|null $user */
        $user = auth('api')->user();

        if (! $user) {
            return $this->errorResponse('Unauthenticated', 401);
        }

        // Load roles for the response
        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ],
        ]);
    }
}
