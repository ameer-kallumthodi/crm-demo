<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted Successfully</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(72, 149, 239, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .success-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 60px 40px;
            max-width: 600px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .success-icon {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 8px 30px rgba(39, 174, 96, 0.3);
            animation: scaleIn 0.5s ease-out;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .success-icon svg {
            width: 70px;
            height: 70px;
            fill: white;
        }
        
        h2 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .lead {
            color: #34495e;
            font-size: 18px;
        }
        
        .info-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .info-box strong {
            color: #4A90E2;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
            </svg>
        </div>
        <h2>Application Submitted Successfully!</h2>
        <p class="lead mb-4">Thank you for submitting your application for the Online Teaching Faculty position.</p>
        <p class="text-muted">Your application has been received and will be reviewed by our team. We will contact you soon regarding the next steps.</p>
        <div class="info-box">
            <p class="mb-0"><strong>Submitted on:</strong> {{ $faculty->form_filled_at->format('F d, Y \a\t h:i A') }}</p>
        </div>
    </div>
</body>
</html>
