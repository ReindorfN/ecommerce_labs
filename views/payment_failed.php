<?php
require_once '../settings/core.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$message = isset($_GET['message']) ? urldecode($_GET['message']) : 'Payment was unsuccessful.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed | Skill-Office Africa</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .error-icon {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #333;
            margin: 0 0 10px 0;
        }
        
        .message {
            color: #666;
            font-size: 1.1rem;
            margin: 20px 0;
            padding: 20px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 10px;
            color: #721c24;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">❌</div>
        <h1>Payment Failed</h1>
        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>
        
        <div class="actions">
            <a href="checkout.php" class="btn btn-primary">Try Again</a>
            <a href="cart.php" class="btn btn-danger">Return to Cart</a>
        </div>
    </div>
</body>
</html>

