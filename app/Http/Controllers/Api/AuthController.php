<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\InvestorResource;
use App\Http\Resources\UserResource;
use App\Mail\EmailVerificationMail;
use App\Mail\OtpMail;
use App\Mail\WelcomeMail;
use App\Models\Investor;
use App\Models\Otp;
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

        // Load roles with privileges for the response
        $user->load('roles.privileges');

        // Generate and send email verification OTP
        $this->sendVerificationEmail($user);

        // Send welcome email
        Mail::to($user->email)->send(new WelcomeMail($user));

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully. Please check your email to verify your account.',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'bearer',
            ],
        ], 201);
    }

    /**
     * Resend email verification.
     */
    public function resendVerificationEmail(): JsonResponse
    {
        request()->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        // Find user by email
        $user = User::where('email', request()->input('email'))->first();

        if (! $user) {
            return $this->errorResponse('User not found', 404);
        }

        // Check if email is already verified
        if ($user->email_verified_at != null) {
            return $this->errorResponse('Email is already verified', 400);
        }

        $this->sendVerificationEmail($user);

        return $this->successResponse('Verification email sent successfully. Please check your email.');
    }

    /**
     * Verify email using OTP.
     */
    public function verifyEmail(): JsonResponse
    {
        request()->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        // Find user by email
        $user = User::where('email', request()->input('email'))->first();

        if (! $user) {
            return $this->errorResponse('User not found', 404);
        }

        if ($user->email_verified_at) {
            return $this->errorResponse('Email is already verified', 400);
        }

        // Find valid OTP for this email
        $otpRecord = Otp::where('email', $user->email)
            ->where('otp', request()->input('otp'))
            ->where('type', 'email_verification')
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otpRecord) {
            return $this->errorResponse('Invalid or expired verification code', 400);
        }

        // Mark OTP as used
        $otpRecord->markAsUsed();

        // Verify user email - update email_verified_at in database
        $user->email_verified_at = now();
        $user->save();

        // Refresh the user model to get updated data
        $user->refresh();
        $user->load('roles.privileges');

        return $this->successResponse('Email verified successfully. Now you can successfully login.');
    }

    /**
     * Request investor login OTP.
     */
    public function requestInvestorLogin(): JsonResponse
    {
        request()->validate([
            'email' => ['required', 'email', 'exists:investors,email'],
        ]);

        // Find investor by email
        $investor = Investor::where('email', request()->input('email'))->first();

        if (! $investor) {
            return $this->errorResponse('Investor not found', 404);
        }

        // Invalidate any existing unused login OTPs for this email
        Otp::where('email', $investor->email)
            ->where('type', 'investor_login')
            ->where('used', false)
            ->update(['used' => true]);

        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP (expires in 10 minutes)
        Otp::create([
            'email' => $investor->email,
            'otp' => $otp,
            'type' => 'investor_login',
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP email
        Mail::to($investor->email)->send(new OtpMail($otp, 'investor_login', 10));

        return $this->successResponse('Login OTP sent successfully. Please check your email.');
    }

    /**
     * Investor login with OTP.
     */
    public function investorLogin(): JsonResponse
    {
        request()->validate([
            'email' => ['required', 'email', 'exists:investors,email'],
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        // Find investor by email
        $investor = Investor::where('email', request()->input('email'))->first();

        if (! $investor) {
            return $this->errorResponse('Investor not found', 404);
        }

        // Find valid OTP for this email
        $otpRecord = Otp::where('email', $investor->email)
            ->where('otp', request()->input('otp'))
            ->where('type', 'investor_login')
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otpRecord) {
            return $this->errorResponse('Invalid or expired OTP', 400);
        }

        // Mark OTP as used
        $otpRecord->markAsUsed();

        // Generate JWT token - JWT works with the guard specified in auth config
        // The guard is determined when validating, not when generating
        $token = JWTAuth::fromUser($investor);

        return $this->respondWithTokenForInvestor($token, $investor);
    }

    /**
     * Get the authenticated investor.
     */
    public function investorMe(): JsonResponse
    {
        /** @var Investor|null $investor */
        $investor = auth('investor')->user();

        if (! $investor) {
            return $this->errorResponse('Unauthenticated', 401);
        }

        return $this->successResponse('Investor retrieved successfully', new InvestorResource($investor));
    }

    /**
     * Logout the authenticated investor.
     */
    public function investorLogout(): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
        } catch (\Exception $e) {
            // Token might already be invalid
        }

        return $this->successResponse('Successfully logged out');
    }

    /**
     * Refresh the investor token.
     */
    public function investorRefresh(): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();
            $newToken = JWTAuth::refresh($token);
            /** @var Investor|null $investor */
            $investor = auth('investor')->user();

            return $this->respondWithTokenForInvestor($newToken, $investor);
        } catch (\Exception $e) {
            return $this->errorResponse('Could not refresh token', 401);
        }
    }

    /**
     * Get the token array structure for investor.
     */
    protected function respondWithTokenForInvestor(string $token, ?Investor $investor = null): JsonResponse
    {
        if (! $investor) {
            /** @var Investor|null $investor */
            $investor = auth('investor')->user();
        }

        if (! $investor) {
            return $this->errorResponse('Unauthenticated', 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'investor' => new InvestorResource($investor),
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ],
        ]);
    }

    /**
     * Get the token array structure for a specific guard.
     */
    protected function respondWithTokenForGuard(string $token, ?User $user = null): JsonResponse
    {
        if (! $user) {
            // Try to get user from investor guard first, then api guard
            /** @var User|null $user */
            $user = auth('investor')->user() ?? auth('api')->user();
        }

        if (! $user) {
            return $this->errorResponse('Unauthenticated', 401);
        }

        // Load roles with privileges for the response
        /** @var User $user */
        $user->load('roles.privileges');

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

    /**
     * Send verification email with OTP.
     */
    private function sendVerificationEmail(User $user): void
    {
        // Invalidate any existing unused OTPs for this email
        Otp::where('email', $user->email)
            ->where('type', 'email_verification')
            ->where('used', false)
            ->update(['used' => true]);

        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP (expires in 10 minutes)
        Otp::create([
            'email' => $user->email,
            'otp' => $otp,
            'type' => 'email_verification',
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send verification email
        Mail::to($user->email)->send(new EmailVerificationMail($user, $otp));
    }

    /**
     * Authenticate a user and return a JWT token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // Check if user exists and load roles with privileges before attempting authentication
        $user = User::with('roles.privileges')->where('email', $credentials['email'])->first();

        if (! $user) {
            return $this->errorResponse('Invalid email or password', 401);
        }

        if ($user->isSuspended()) {
            return $this->errorResponse('Your account has been suspended', 403, ['suspension_reason' => $user->suspension_reason]);
        }

        // Check email verification for non-admin users
        // Query fresh from database to ensure we have the latest email_verified_at value
        $freshUser = User::with('roles.privileges')->find($user->id);
        if ($freshUser && ! $freshUser->hasRole('admin')) {
            if (! $freshUser->email_verified_at) {
                return $this->errorResponse('Please verify your email address before logging in', 403);
            }
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

        // Load roles with privileges for the response
        if ($user) {
            $user->load('roles.privileges');
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

        // Load roles with privileges for the response
        $user->load('roles.privileges');

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
    protected function respondWithToken(string $token, ?User $user = null): JsonResponse
    {
        if (! $user) {
            /** @var User|null $user */
            $user = auth('api')->user();
        }

        if (! $user) {
            return $this->errorResponse('Unauthenticated', 401);
        }

        // Load roles with privileges for the response
        $user->load('roles.privileges');

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
