<?php
require_once '../settings/core.php';

// Include controllers
require_once '../controllers/cart_controller.php';
require_once '../controllers/product_controller.php';
require_once '../functions/fetchCartItems_action.php';

// Initialize controllers
$cartController = new CartController();
$productController = new ProductController();

// Get cart items (getClientIp function is already defined in fetchCartItems_action.php)
$cartItems = fetchCartItemsForDisplay();

// Ensure cartItems is an array
if (!is_array($cartItems)) {
    $cartItems = [];
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
    <title>Shopping Cart | Skill-Office Africa</title>
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
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .cart-table thead {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .cart-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .cart-table td {
            padding: 20px 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .cart-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .product-details h3 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 1.1rem;
        }
        
        .product-details p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .quantity-input {
            width: 60px;
            padding: 8px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            text-align: center;
            font-size: 16px;
        }
        
        .quantity-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .quantity-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }
        
        .price {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }
        
        .subtotal {
            font-size: 1.3rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .remove-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }
        
        .cart-summary {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-top: 30px;
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
        
        .cart-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .continue-shopping {
            background: #6c757d;
            color: white;
        }
        
        .continue-shopping:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }
        
        .proceed-checkout {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .proceed-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .empty-cart {
            background: #dc3545;
            color: white;
        }
        
        .empty-cart:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        
        .empty-cart-message {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-cart-message h3 {
            margin: 0 0 10px 0;
            font-size: 1.5rem;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 80px 10px 20px 10px;
                padding: 15px;
            }
            
            .cart-table {
                font-size: 0.9rem;
            }
            
            .cart-table th,
            .cart-table td {
                padding: 10px 5px;
            }
            
            .product-image {
                width: 60px;
                height: 60px;
            }
            
            .cart-actions {
                flex-direction: column;
            }
            
            .action-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php"><button>Home</button></a>
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
            <h1>🛒 Shopping Cart</h1>
            <p>Review and manage your cart items</p>
        </div>
        
        <?php if (empty($cartItemsWithProducts)): ?>
            <div class="empty-cart-message">
                <h3>Your cart is empty</h3>
                <p>Start adding products to your cart!</p>
                <a href="all_products.php" class="action-btn continue-shopping" style="margin-top: 20px;">Continue Shopping</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cartItemsBody">
                    <?php foreach ($cartItemsWithProducts as $item): ?>
                        <tr data-product-id="<?php echo $item['product']['product_id']; ?>" data-cart-id="<?php echo isset($item['cart_item']['c_id']) ? $item['cart_item']['c_id'] : ''; ?>">
                            <td>
                                <div class="product-info">
                                    <?php if (!empty($item['product']['product_image'])): ?>
                                        <img src="../<?php echo htmlspecialchars($item['product']['product_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['product']['product_title']); ?>" 
                                             class="product-image"
                                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\'%3E%3Crect fill=\'%23f8f9fa\' width=\'80\' height=\'80\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'12\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ENo Image%3C/text%3E%3C/svg%3E'">
                                    <?php else: ?>
                                        <div class="product-image" style="display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.8rem;">
                                            No Image
                                        </div>
                                    <?php endif; ?>
                                    <div class="product-details">
                                        <h3><?php echo htmlspecialchars($item['product']['product_title']); ?></h3>
                                        <p>ID: <?php echo $item['product']['product_id']; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="price">GH₵<?php echo number_format($item['product']['product_price'], 2); ?></span>
                            </td>
                            <td>
                                <div class="quantity-controls">
                                    <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product']['product_id']; ?>, -1)">-</button>
                                    <input type="number" 
                                           class="quantity-input" 
                                           value="<?php echo $item['cart_item']['qty']; ?>" 
                                           min="1" 
                                           data-product-id="<?php echo $item['product']['product_id']; ?>"
                                           onchange="updateQuantityDirect(<?php echo $item['product']['product_id']; ?>, this.value)">
                                    <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product']['product_id']; ?>, 1)">+</button>
                                </div>
                            </td>
                            <td>
                                <span class="subtotal" data-subtotal="<?php echo $item['product']['product_id']; ?>">
                                    GH₵<?php echo number_format($item['subtotal'], 2); ?>
                                </span>
                            </td>
                            <td>
                                <button class="remove-btn" onclick="removeFromCart(<?php echo $item['product']['product_id']; ?>)">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="cartSubtotal">GH₵<?php echo number_format($totalAmount, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>GH₵0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span id="cartTotal">GH₵<?php echo number_format($totalAmount, 2); ?></span>
                </div>
            </div>
            
            <div class="cart-actions">
                <a href="all_products.php" class="action-btn continue-shopping">Continue Shopping</a>
                <?php if (isLoggedIn()): ?>
                    <a href="checkout.php" class="action-btn proceed-checkout">Proceed to Checkout</a>
                <?php else: ?>
                    <a href="../login/login.php" class="action-btn proceed-checkout" style="background: linear-gradient(135deg, #ffc107, #ff9800);">Login to Checkout</a>
                <?php endif; ?>
                <button class="action-btn empty-cart" onclick="emptyCart()">Empty Cart</button>
            </div>
            
            <?php if (!isLoggedIn()): ?>
                <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 10px; padding: 15px; margin-top: 20px; text-align: center;">
                    <strong style="color: #856404;">⚠️ Guest Checkout Not Available</strong>
                    <p style="color: #856404; margin: 10px 0 0 0;">Please <a href="../login/login.php" style="color: #667eea; font-weight: 600;">login</a> or <a href="../login/register.php" style="color: #667eea; font-weight: 600;">create an account</a> to proceed to checkout.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/cart.js"></script>
</body>
</html>

