<?php
require_once '../settings/core.php';
require_once '../controllers/order_controller.php';
require_once '../controllers/product_controller.php';

// Check if order_id is provided
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header("Location: cart.php");
    exit;
}

$order_id = intval($_GET['order_id']);
$reference = isset($_GET['reference']) ? trim($_GET['reference']) : '';

$orderController = new OrderController();
$productController = new ProductController();

// Get order details
$order = $orderController->get_order_by_id_ctr($order_id);
$payment = $orderController->get_order_payment_ctr($order_id);
$orderDetails = $orderController->get_order_details_ctr($order_id);

// Calculate total from order details
$orderTotal = 0;
$items = [];

foreach ($orderDetails as $detail) {
    $product = $productController->get_product_by_id_ctr($detail['product_id']);
    if ($product) {
        $subtotal = floatval($product['product_price']) * intval($detail['qty']);
        $orderTotal += $subtotal;
        $items[] = [
            'product' => $product,
            'quantity' => $detail['qty'],
            'subtotal' => $subtotal
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | Skill-Office Africa</title>
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
        
        .success-icon {
            font-size: 80px;
            color: #28a745;
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
        }
        
        .order-info {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin: 30px 0;
            text-align: left;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-label {
            font-weight: 600;
            color: #333;
        }
        
        .info-value {
            color: #667eea;
            font-weight: 600;
        }
        
        .total-row {
            font-size: 1.3rem;
            color: #28a745;
            font-weight: 700;
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
        
        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <h1>Payment Successful!</h1>
        <p class="message">Thank you for your purchase. Your order has been confirmed.</p>
        
        <?php if ($order && $payment): ?>
            <div class="order-info">
                <div class="info-row">
                    <span class="info-label">Order ID:</span>
                    <span class="info-value">#<?php echo $order['order_id']; ?></span>
                </div>
                <?php if (!empty($order['invoice_no'])): ?>
                <div class="info-row">
                    <span class="info-label">Invoice Number:</span>
                    <span class="info-value"><?php echo $order['invoice_no']; ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($reference)): ?>
                <div class="info-row">
                    <span class="info-label">Payment Reference:</span>
                    <span class="info-value"><?php echo htmlspecialchars($reference); ?></span>
                </div>
                <?php endif; ?>
                <div class="info-row">
                    <span class="info-label">Order Date:</span>
                    <span class="info-value"><?php echo date('F j, Y', strtotime($order['order_date'])); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Status:</span>
                    <span class="info-value" style="color: #28a745;"><?php echo ucfirst($order['order_status']); ?></span>
                </div>
                <div class="info-row total-row">
                    <span>Total Amount:</span>
                    <span>GH₵<?php echo number_format($orderTotal, 2); ?></span>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="actions">
            <a href="../index.php" class="btn btn-primary">Go Home</a>
            <a href="all_products.php" class="btn btn-success">Continue Shopping</a>
        </div>
    </div>
</body>
</html>

