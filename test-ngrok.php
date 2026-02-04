<?php
/**
 * Test page to verify ngrok connectivity
 */

// Detect base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_dir = dirname($_SERVER['SCRIPT_NAME']);
$base_url = $protocol . '://' . $host . $script_dir;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ngrok Test - Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .test-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            animation: bounce 1s ease infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .info-label {
            font-weight: 600;
            color: #667eea;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            color: #333;
            word-break: break-all;
        }
        .btn-access {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 30px;
            color: white;
            font-weight: 600;
            border-radius: 50px;
            transition: transform 0.2s;
        }
        .btn-access:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="test-card text-center">
        <div class="success-icon mb-4">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        
        <h1 class="mb-2">✅ Ngrok Connected!</h1>
        <p class="text-muted mb-4">Your Student Management System is accessible</p>
        
        <div class="info-box text-start">
            <div class="info-label">Protocol</div>
            <div class="info-value"><?php echo $protocol; ?></div>
        </div>
        
        <div class="info-box text-start">
            <div class="info-label">Host</div>
            <div class="info-value"><?php echo $host; ?></div>
        </div>
        
        <div class="info-box text-start">
            <div class="info-label">Base URL</div>
            <div class="info-value"><?php echo $base_url; ?></div>
        </div>
        
        <div class="info-box text-start">
            <div class="info-label">Full URL</div>
            <div class="info-value"><?php echo $protocol . '://' . $host . $_SERVER['REQUEST_URI']; ?></div>
        </div>
        
        <div class="info-box text-start">
            <div class="info-label">User Agent</div>
            <div class="info-value" style="font-size: 0.8rem;"><?php echo htmlspecialchars($_SERVER['HTTP_USER_AGENT']); ?></div>
        </div>

        <div class="info-box text-start">
            <div class="info-label">Server Info</div>
            <div class="info-value">
                <small>
                    <strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?><br>
                    <strong>PHP:</strong> <?php echo phpversion(); ?><br>
                    <strong>Method:</strong> <?php echo $_SERVER['REQUEST_METHOD']; ?>
                </small>
            </div>
        </div>
        
        <hr class="my-4">
        
        <a href="<?php echo $base_url; ?>/auth/login.php" class="btn btn-access btn-lg w-100 mb-2">
            <i class="bi bi-box-arrow-in-right"></i> Go to Login
        </a>
        
        <a href="<?php echo $base_url; ?>/index.php" class="btn btn-outline-secondary btn-sm w-100">
            <i class="bi bi-speedometer2"></i> Go to Dashboard
        </a>
        
        <div class="mt-4">
            <small class="text-muted">
                <i class="bi bi-phone"></i> Share this URL with your phone to test on mobile!
            </small>
        </div>
    </div>
</body>
</html>
