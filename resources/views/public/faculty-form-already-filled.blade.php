<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Already Submitted</title>
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
        
        .info-container {
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
        
        .info-icon {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 8px 30px rgba(243, 156, 18, 0.3);
        }
        
        .info-icon svg {
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
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
            border-left: 4px solid #f39c12;
        }
        
        .info-box strong {
            color: #e67e22;
        }
    </style>
</head>
<body>
    <div class="info-container">
        <div class="info-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
        </div>
        <h2>Form Already Submitted</h2>
        <p class="lead mb-4">This application form has already been completed and submitted.</p>
        <p class="text-muted">Your application was successfully submitted on <strong>{{ $faculty->form_filled_at->format('F d, Y') }}</strong>. Each faculty member can only submit the form once.</p>
        <div class="info-box">
            <p class="mb-0"><strong>Note:</strong> If you need to make any changes to your submitted information, please contact the administration team.</p>
        </div>
    </div>
</body>
</html>
