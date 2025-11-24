<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    /**
     * Send password reset OTP.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $email = $request->email;

        $user = User::where('email', $email)->first();

        if (! $user) {
            // Don't reveal if email exists for security
            return response()->json([
                'message' => 'If the email exists, a password reset OTP has been sent.',
            ]);
        }

        // Invalidate any existing unused OTPs for this email
        Otp::where('email', $email)
            ->where('type', 'password_reset')
            ->where('used', false)
            ->update(['used' => true]);

        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP (expires in 15 minutes)
        Otp::create([
            'email' => $email,
            'otp' => $otp,
            'type' => 'password_reset',
            'expires_at' => now()->addMinutes(15),
        ]);

        // TODO: Send OTP via email/SMS
        // For now, we'll return it in development (remove in production)
        if (config('app.debug')) {
            return response()->json([
                'message' => 'Password reset OTP sent successfully',
                'otp' => $otp, // Remove this in production
            ]);
        }

        return response()->json([
            'message' => 'Password reset OTP sent successfully',
        ]);
    }

    /**
     * Reset password using OTP.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $email = $request->email;
        $otp = $request->otp;
        $password = $request->password;

        // Find valid OTP
        $otpRecord = Otp::where('email', $email)
            ->where('otp', $otp)
            ->where('type', 'password_reset')
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otpRecord) {
            return response()->json([
                'message' => 'Invalid or expired OTP',
            ], 400);
        }

        // Find user
        $user = User::where('email', $email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        // Update password
        $user->update([
            'password' => Hash::make($password),
        ]);

        // Mark OTP as used
        $otpRecord->markAsUsed();

        // Invalidate all other unused OTPs for this email
        Otp::where('email', $email)
            ->where('type', 'password_reset')
            ->where('used', false)
            ->where('id', '!=', $otpRecord->id)
            ->update(['used' => true]);

        return response()->json([
            'message' => 'Password reset successfully',
        ]);
    }
}
