<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✅ Connected!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .box {
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 400px;
        }
        h1 { color: #28a745; font-size: 3rem; margin: 0; }
        p { color: #666; margin: 20px 0; }
        a {
            display: block;
            background: #000;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            margin: 10px 0;
            font-weight: bold;
        }
        a:hover { background: #333; }
        .url {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 10px;
            word-break: break-all;
            margin: 15px 0;
            font-family: monospace;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>✅</h1>
        <h2>Connection Success!</h2>
        <p>Your phone is connected to the server</p>
        
        <div class="url">
            <?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>
        </div>
        
        <p><strong>Server Time:</strong><br><?php echo date('Y-m-d H:i:s'); ?></p>
        <p><strong>Your IP:</strong><br><?php echo $_SERVER['REMOTE_ADDR']; ?></p>
        
        <a href="/amsp/auth/login.php">🔐 Go to Login</a>
    </div>
</body>
</html>
