<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    /**
     * Send OTP to email.
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $email = $request->email;
        $type = $request->type ?? 'general';

        // Check if user exists (optional, depending on use case)
        if ($request->has('require_user_exists') && $request->require_user_exists) {
            $user = User::where('email', $email)->first();
            if (! $user) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }
        }

        // Invalidate any existing unused OTPs for this email and type
        Otp::where('email', $email)
            ->where('type', $type)
            ->where('used', false)
            ->update(['used' => true]);

        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP (expires in 10 minutes by default)
        $expiresAt = now()->addMinutes($request->expires_in ?? 10);

        Otp::create([
            'email' => $email,
            'otp' => $otp,
            'type' => $type,
            'expires_at' => $expiresAt,
        ]);

        // Send OTP via email
        $expiresInMinutes = $request->expires_in ?? 10;
        Mail::to($email)->send(new OtpMail($otp, $type, $expiresInMinutes));

        // In development mode, also return OTP in response for testing
        if (config('app.debug')) {
            return response()->json([
                'message' => 'OTP sent successfully',
                'otp' => $otp, // Remove this in production
                'expires_at' => $expiresAt->toIso8601String(),
            ]);
        }

        return response()->json([
            'message' => 'OTP sent successfully',
            'expires_at' => $expiresAt->toIso8601String(),
        ]);
    }

    /**
     * Verify OTP.
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $email = $request->email;
        $otp = $request->otp;
        $type = $request->type ?? 'general';

        // Find valid OTP
        $otpRecord = Otp::where('email', $email)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otpRecord) {
            return response()->json([
                'message' => 'Invalid or expired OTP',
                'verified' => false,
            ], 400);
        }

        // Mark OTP as used if auto_use is true
        if ($request->auto_use ?? true) {
            $otpRecord->markAsUsed();
        }

        return response()->json([
            'message' => 'OTP verified successfully',
            'verified' => true,
        ]);
    }
}
