<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Welcome to {{ $appName }}</title>
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
                            <div style="font-size: 64px; margin-bottom: 20px;">🎉</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;">
                                Welcome to {{ $appName }}!
                            </h1>
                            <p style="margin: 15px 0 0; color: rgba(255, 255, 255, 0.9); font-size: 18px; font-weight: 300;">
                                We're thrilled to have you on board
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
                                Thank you for joining <strong style="color: #374151;">{{ $appName }}</strong>! We're excited to have you as part of our community.
                            </p>
                            
                            <p style="margin: 0 0 30px; color: #6b7280; font-size: 16px; line-height: 1.7;">
                                Your account has been successfully created with the email: <strong style="color: #667eea;">{{ $user->email }}</strong>
                            </p>
                            
                            <!-- Features Box -->
                            <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 12px; padding: 30px; margin: 30px 0; border-left: 4px solid #667eea;">
                                <h2 style="margin: 0 0 20px; color: #1e40af; font-size: 20px; font-weight: 600;">
                                    ✨ What's Next?
                                </h2>
                                <ul style="margin: 0; padding-left: 20px; color: #1e3a8a; font-size: 15px; line-height: 1.8;">
                                    <li style="margin-bottom: 10px;">Explore all the features we have to offer</li>
                                    <li style="margin-bottom: 10px;">Complete your profile to get started</li>
                                    <li style="margin-bottom: 10px;">Reach out if you need any assistance</li>
                                </ul>
                            </div>
                            
                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 30px 0 20px;">
                                        <a href="{{ config('app.url') }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);">
                                            Get Started →
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin: 30px 0 0; color: #6b7280; font-size: 15px; line-height: 1.7; text-align: center;">
                                If you have any questions, feel free to reach out to our support team. We're here to help!
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
                                Check out our documentation or contact our support team for assistance.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 13px; line-height: 1.6;">
                                This is an automated welcome email. If you have any questions, please don't hesitate to contact us.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #ffffff; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px; text-align: center; line-height: 1.6;">
                                © {{ date('Y') }} {{ $appName }}. All rights reserved.<br>
                                You're receiving this email because you created an account with us.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

