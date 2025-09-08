<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Cyber Travel</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8fafc;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-text {
            font-size: 32px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 20px;
            text-align: center;
        }
        .title {
            color: #1e40af;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #6b7280;
            font-size: 16px;
        }
        .content {
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background-color: #1e40af;
            color: #ffffff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #1e3a8a;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #92400e;
        }
        .info {
            background-color: #dbeafe;
            border: 1px solid #3b82f6;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-text">✈️ Cyber Travel</div>
            <div class="title">Welcome to Cyber Travel!</div>
            <div class="subtitle">Your trusted travel partner</div>
        </div>

        <div class="content">
            <p>Hello {{ $user->name }}!</p>
            
            <p>Thank you for creating your account with Cyber Travel! We're excited to have you on board and can't wait to help you plan your next adventure.</p>
            
            <p>To get started with our travel services, please verify your email address by clicking the button below:</p>
            
            <div style="text-align: center;">
                <a href="{{ $url }}" class="button">Verify Email Address</a>
            </div>
            
            <div class="warning">
                <strong>Important:</strong> This verification link will expire in {{ $expireMinutes }} minutes for security reasons.
            </div>
            
            <div class="info">
                <strong>What happens next?</strong> After verifying your email, you'll have full access to:
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Browse our travel packages and services</li>
                    <li>Make applications and reservations</li>
                    <li>Access your personal dashboard</li>
                    <li>Receive exclusive travel offers</li>
                </ul>
            </div>
            
            <p>If you did not create an account with Cyber Travel, please ignore this email. No further action is required.</p>
        </div>

        <div class="footer">
            <p><strong>Best regards,</strong><br>The Cyber Travel Team</p>
            <p style="margin-top: 15px;">
                <small>
                    This is an automated message. Please do not reply to this email.<br>
                    If you need assistance, please contact our support team.
                </small>
            </p>
        </div>
    </div>
</body>
</html>
