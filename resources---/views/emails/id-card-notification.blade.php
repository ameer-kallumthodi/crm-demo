<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Student ID Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
        }
        .highlight {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>🎓 Your Student ID Card for {{ $courseName }}</h2>
    </div>
    
    <div class="content">
        <p>Dear <span class="highlight">{{ $studentName }}</span>,</p>
        
        <p>Your Student ID Card for the course "<span class="highlight">{{ $courseName }}</span>" is now ready.</p>
        
        <p>📎 Please find your ID card attached to this email.</p>
        
        <p>If any details need correction, please reach out to us for assistance.</p>
    </div>
    
    <div class="footer">
        <p><strong>Warm regards,</strong><br>
        Support Team<br>
        Academic Operations Department</p>
        
        <p>📞 +91 9207666614, +91 9207666615</p>
    </div>
</body>
</html>
