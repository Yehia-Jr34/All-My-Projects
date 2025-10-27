<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background-color: #4CAF50;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 20px;
            line-height: 1.6;
        }
        .otp-code {
            display: inline-block;
            background-color: #f4f4f4;
            color: #4CAF50;
            font-size: 24px;
            font-weight: bold;
            padding: 10px 20px;
            border: 1px solid #dddddd;
            border-radius: 4px;
            margin: 20px 0;
            text-align: center;
        }
        .email-footer {
            background-color: #f4f4f4;
            color: #666666;
            text-align: center;
            padding: 10px;
            font-size: 12px;
        }
        .email-footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .email-container {
                width: 100%;
                margin: 10px;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <!-- Header -->
    <div class="email-header">
        <h1>Your OTP Code</h1>
    </div>

    <!-- Body -->
    <div class="email-body">
        <p>Hello,</p>
        <p>Thank you for using our service. To proceed with your request, please use the following One-Time Password (OTP):</p>
        <div class="otp-code">{{ $code }}</div>
        <p>This OTP is valid for the next 5 minutes. Please do not share this code with anyone for security purposes.</p>
        <p>If you did not request this, please ignore this email or contact our support team.</p>
        <p>Best regards,</p>

    </div>

    <!-- Footer -->
    <div class="email-footer">
        <p>Made with love ❤️</p>
    </div>
</div>
</body>
</html>
