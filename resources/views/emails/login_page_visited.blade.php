<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page Visited</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            border-bottom: 3px solid #e74c3c;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        h1 {
            color: #e74c3c;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .info-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #e74c3c;
            border-radius: 4px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            min-width: 100px;
        }
        .info-value {
            color: #333;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #888;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Login Page Visited</h1>
            <p>Someone visited the admin login page.</p>
        </div>

        <div class="info-section">
            <div><span class="info-label">IP Address:</span> <span class="info-value">{{ $ip }}</span></div>
            <div><span class="info-label">Time:</span> <span class="info-value">{{ $timestamp }}</span></div>
            <div><span class="info-label">User Agent:</span> <span class="info-value">{{ $userAgent }}</span></div>
        </div>

        <div class="footer">
            <p>This is an automated notification from your website.</p>
        </div>
    </div>
</body>
</html>
