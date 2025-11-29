<?php
require_once '../settings/core.php';

// Include controllers
require_once '../controllers/cart_controller.php';
require_once '../controllers/product_controller.php';
require_once '../functions/fetchCartItems_action.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header("Location: ../login/login.php?redirect=checkout");
    exit;
}

// Initialize controllers
$cartController = new CartController();
$productController = new ProductController();

// Get cart items
$cartItems = fetchCartItemsForDisplay();

// Ensure cartItems is an array
if (!is_array($cartItems)) {
    $cartItems = [];
}

// Redirect to cart if cart is empty
if (empty($cartItems)) {
    header("Location: cart.php");
    exit;
}

// Fetch product details for each cart item
$cartItemsWithProducts = []; 
$totalAmount = 0;

foreach ($cartItems as $item) {
    if (isset($item['p_id'])) {
        $product = $productController->get_product_by_id_ctr($item['p_id']);
        if ($product) {
            $subtotal = floatval($product['product_price']) * intval($item['qty']);
            $totalAmount += $subtotal;
            
            $cartItemsWithProducts[] = [
                'cart_item' => $item,
                'product' => $product,
                'subtotal' => $subtotal
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Skill-Office Africa</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .navbar {
            position: fixed;
            top: 20px;
            left: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 150px;
        }
        
        .navbar a {
            text-decoration: none;
        }
        
        .navbar button {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }
        
        .navbar button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .container {
            max-width: 1200px;
            margin: 100px auto 50px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .checkout-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .order-summary {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
        }
        
        .order-summary h2 {
            margin-top: 0;
            color: #333;
            font-size: 1.5rem;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-info h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .item-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .item-price {
            font-weight: 600;
            color: #667eea;
        }
        
        .payment-section {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .payment-section h2 {
            margin-top: 0;
            color: #333;
            font-size: 1.5rem;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .summary-row.total {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            padding-top: 15px;
            border-top: 2px solid #e9ecef;
        }
        
        .simulate-payment-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        
        .simulate-payment-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .simulate-payment-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }
        
        .back-to-cart {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-to-cart:hover {
            text-decoration: underline;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .modal-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .modal-header h2 {
            color: #333;
            margin: 0;
        }
        
        .modal-body {
            text-align: center;
            padding: 20px 0;
        }
        
        .modal-body p {
            color: #666;
            font-size: 1.1rem;
            margin: 10px 0;
        }
        
        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .modal-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .modal-btn.confirm {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .modal-btn.confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
        }
        
        .modal-btn.cancel {
            background: #6c757d;
            color: white;
        }
        
        .modal-btn.cancel:hover {
            background: #5a6268;
        }
        
        .success-message, .error-message {
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }
            
            .container {
                margin: 80px 10px 20px 10px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php"><button>Home</button></a>
        <a href="cart.php"><button>Back to Cart</button></a>
        <a href="all_products.php"><button>Continue Shopping</button></a>
        <?php if (isLoggedIn()): ?>
            <?php if (isAdmin()): ?>
                <a href="../admin/category.php"><button>Category & Brands</button></a>
                <a href="../admin/product.php"><button>Product Dashboard</button></a>
            <?php endif; ?>
            <a href="../functions/logout_user_action.php"><button>Logout</button></a>
        <?php else: ?>
            <a href="../login/login.php"><button>Login</button></a>
            <a href="../login/register.php"><button>Register</button></a>
        <?php endif; ?>
    </nav>
    
    <div class="container">
        <div class="header">
            <h1>🛒 Checkout</h1>
            <p>Review your order and proceed to payment</p>
        </div>
        
        <div id="checkoutMessages"></div>
        
        <div class="checkout-content">
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div id="orderItems">
                    <?php foreach ($cartItemsWithProducts as $item): ?>
                        <div class="order-item">
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars($item['product']['product_title']); ?></h4>
                                <p>Quantity: <?php echo $item['cart_item']['qty']; ?> × GH₵<?php echo number_format($item['product']['product_price'], 2); ?></p>
                            </div>
                            <div class="item-price">
                                GH₵<?php echo number_format($item['subtotal'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="payment-section">
                <h2>Payment Details</h2>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="checkoutSubtotal">GH₵<?php echo number_format($totalAmount, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>GH₵0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span id="checkoutTotal">GH₵<?php echo number_format($totalAmount, 2); ?></span>
                </div>
                
                <button class="simulate-payment-btn" id="simulatePaymentBtn" onclick="showPaymentModal()">
                    💳 Simulate Payment
                </button>
                
                <a href="cart.php" class="back-to-cart">← Back to Cart</a>
            </div>
        </div>
    </div>
    
    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>💳 Simulate Payment</h2>
            </div>
            <div class="modal-body">
                <p>Total Amount: <strong id="modalTotal">GH₵<?php echo number_format($totalAmount, 2); ?></strong></p>
                <p>This is a simulated payment. Click "Yes, I've paid" to complete the checkout.</p>
            </div>
            <div class="modal-actions">
                <button class="modal-btn confirm" onclick="processPayment()">Yes, I've paid</button>
                <button class="modal-btn cancel" onclick="closePaymentModal()">Cancel</button>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/checkout.js"></script>
</body>
</html>

