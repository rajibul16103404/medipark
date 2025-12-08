<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Verify Your Email - {{ $appName }}</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {font-family: Arial, sans-serif !important;}
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f7; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f7;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 50px 40px 40px; text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px 12px 0 0;">
                            <div style="font-size: 64px; margin-bottom: 20px;">✉️</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;">
                                Verify Your Email
                            </h1>
                            <p style="margin: 15px 0 0; color: rgba(255, 255, 255, 0.9); font-size: 18px; font-weight: 300;">
                                Please verify your email address to continue
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; color: #374151; font-size: 18px; line-height: 1.6; font-weight: 600;">
                                Hello {{ $user->name }},
                            </p>
                            
                            <p style="margin: 0 0 25px; color: #6b7280; font-size: 16px; line-height: 1.7;">
                                Thank you for registering with <strong style="color: #374151;">{{ $appName }}</strong>! To complete your registration and access all features, please verify your email address.
                            </p>
                            
                            <!-- OTP Box -->
                            <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 12px; padding: 30px; margin: 30px 0; border-left: 4px solid #667eea; text-align: center;">
                                <h2 style="margin: 0 0 15px; color: #1e40af; font-size: 20px; font-weight: 600;">
                                    Your Verification Code
                                </h2>
                                <div style="font-size: 48px; font-weight: 700; color: #667eea; letter-spacing: 8px; font-family: 'Courier New', monospace; margin: 20px 0;">
                                    {{ $otp }}
                                </div>
                                <p style="margin: 15px 0 0; color: #1e3a8a; font-size: 14px; line-height: 1.6;">
                                    This code will expire in 10 minutes
                                </p>
                            </div>
                            
                            <p style="margin: 30px 0 20px; color: #6b7280; font-size: 16px; line-height: 1.7;">
                                Enter this code in the verification page to verify your email address: <strong style="color: #667eea;">{{ $user->email }}</strong>
                            </p>
                            
                            <!-- Info Box -->
                            <div style="background-color: #fef3c7; border-radius: 8px; padding: 20px; margin: 30px 0; border-left: 4px solid #f59e0b;">
                                <p style="margin: 0; color: #92400e; font-size: 14px; line-height: 1.6;">
                                    <strong>⚠️ Security Notice:</strong> Never share this code with anyone. Our team will never ask for your verification code.
                                </p>
                            </div>
                            
                            <p style="margin: 30px 0 0; color: #6b7280; font-size: 15px; line-height: 1.7; text-align: center;">
                                If you didn't create an account with us, please ignore this email.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Divider -->
                    <tr>
                        <td style="padding: 0 40px;">
                            <div style="border-top: 1px solid #e5e7eb;"></div>
                        </td>
                    </tr>
                    
                    <!-- Help Section -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #f9fafb;">
                            <h3 style="margin: 0 0 15px; color: #374151; font-size: 18px; font-weight: 600;">
                                💡 Need Help?
                            </h3>
                            <p style="margin: 0 0 10px; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                If you're having trouble verifying your email, you can request a new verification code from your account settings.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 13px; line-height: 1.6;">
                                This is an automated verification email. If you have any questions, please contact our support team.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #ffffff; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px; text-align: center; line-height: 1.6;">
                                © {{ date('Y') }} {{ $appName }}. All rights reserved.<br>
                                You're receiving this email because you registered an account with us.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

