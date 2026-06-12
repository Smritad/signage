<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; margin:0; padding:0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { text-align: center; padding: 24px 20px; background: #f0f0f0; }
        .header img { max-width: 150px; }
        .content { padding: 32px 28px; color: #333; }
        .content h2 { margin-top: 0; color: #222; }
        .content p { font-size: 15px; line-height: 1.6; color: #555; }
        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: #ab924a; color: #fff !important; padding: 12px 32px; text-decoration: none; border-radius: 5px; font-size: 15px; font-weight: bold; letter-spacing: 0.5px; }
        .validity-box { background: #fff8e1; border-left: 4px solid #ab924a; padding: 12px 16px; border-radius: 4px; margin: 20px 0; font-size: 14px; color: #7a6000; }
        .validity-box strong { color: #ab924a; }
        .divider { border: none; border-top: 1px solid #eee; margin: 24px 0; }
        .footer { text-align: center; padding: 16px 20px; background: #f5f5f5; }
        .footer p { margin: 0; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">

        {{-- Header --}}
        <div class="header">
            <img src="{{ asset('frontend/assets/images/logo/logo.webp') }}" alt="Logo">
        </div>

        {{-- Content --}}
        <div class="content">

            {{-- ✅ Show user name or email --}}
            <h2>Hello, {{ $userName }},</h2>

            <p>We received a request to reset the password for your account associated with <strong>{{ $email }}</strong>.</p>
            <p>Click the button below to reset your password:</p>

            <div class="btn-wrap">
                <a class="btn" href="{{ url('/reset-password/' . $token . '?email=' . urlencode($email)) }}">
                    Reset Password
                </a>
            </div>

            {{-- Expiry notice --}}
            <div class="validity-box">
                ⏱ <strong>This link is valid for 10 minutes only.</strong><br>
                It will expire at <strong>{{ now()->addMinutes(10)->format('h:i A') }}</strong> ({{ now()->addMinutes(10)->format('d M Y') }}).
                After that, you will need to request a new reset link.
            </div>

            <hr class="divider">

            <p style="font-size:13px; color:#999;">
                If you did not request a password reset, please ignore this email. Your password will remain unchanged.
                If you believe someone is trying to access your account, please contact our support team.
            </p>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>&copy; {{ date('Y') }} Signage. All rights reserved.</p>
        </div>

    </div>
</body>
</html>