<!DOCTYPE html>
<html>

<head>
    <title>Verification Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            padding: 0;
            margin: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            color: #1a1a1a;
            margin-bottom: 20px;
            text-align: center;
        }

        p {
            font-size: 16px;
            color: #666;
            text-align: center;
        }

        .code {
            font-size: 32px;
            font-weight: bold;
            color: #3f51b5;
            letter-spacing: 5px;
            margin: 20px 0;
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Your Verification Code</h1>
        <p>Here is your verification code:</p>
        <div class="code">{{ $verificationCode }}</div>
        <p>Please use this code to verify your email address. Don't share code to any one.</p>
        <div class="footer">
            <p>&copy; 2024 Your Company. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
