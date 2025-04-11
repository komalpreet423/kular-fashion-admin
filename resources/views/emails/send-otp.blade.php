<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your OTP Code</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: white; padding: 30px; border-radius: 8px;">
        <h2 style="color: #333;">Hi {{ $user->name ?? 'User' }},</h2>
        <p style="font-size: 16px;">You requested a One-Time Password (OTP) to verify your identity.</p>
        <p style="font-size: 18px; font-weight: bold;">Your OTP is:</p>
        <p style="font-size: 32px; font-weight: bold; color: #030629; text-align: center;">{{ $otp }}</p>
        <p style="font-size: 14px; color: #666;">This OTP is valid for 10 minutes.</p>
        <p style="font-size: 14px; color: #999;">If you did not request this, please ignore this email.</p>
        <p style="margin-top: 20px; font-size: 14px;">Thanks,<br>Kular Fashion</p>
    </div>
</body>
</html>
