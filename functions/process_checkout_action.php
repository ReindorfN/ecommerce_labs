<?php
require_once '../settings/core.php';
require_once '../controllers/order_controller.php';
require_once '../controllers/cart_controller.php';
require_once '../controllers/product_controller.php';
require_once '../functions/fetchCartItems_action.php';

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to checkout.'
    ]);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit;
}

try {
    // Initialize controllers
    $orderController = new OrderController();
    $cartController = new CartController();
    $productController = new ProductController();

    // Get customer ID from session
    $customer_id = $_SESSION['user_id'];
    
    // Get IP address
    $ip_address = getClientIp();

    // Get cart items
    $cartItems = $cartController->get_cart_items_ctr($ip_address, $customer_id);

    if (empty($cartItems)) {
        echo json_encode([
            'success' => false,
            'message' => 'Your cart is empty. Please add items before checkout.'
        ]);
        exit;
    }

    // Calculate total amount and prepare order details
    $orderTotal = 0;
    $orderItems = [];

    foreach ($cartItems as $item) {
        // Get current product details to ensure price consistency
        $product = $productController->get_product_by_id_ctr($item['p_id']);
        
        if (!$product) {
            echo json_encode([
                'success' => false,
                'message' => 'One or more products in your cart are no longer available.'
            ]);
            exit;
        }

        $quantity = intval($item['qty']);
        $price = floatval($product['product_price']);
        $subtotal = $price * $quantity;
        $orderTotal += $subtotal;

        $orderItems[] = [
            'product_id' => $product['product_id'],
            'quantity' => $quantity,
            'price' => $price,
            'product_title' => $product['product_title']
        ];
    }

    // Start transaction (simulated - we'll do sequential operations)
    // Create order (no order_total field in orders table - it's calculated from orderdetails + products)
    $orderParams = [
        'customer_id' => $customer_id
    ];

    $orderResult = $orderController->create_order_ctr($orderParams);

    if (!$orderResult['success']) {
        echo json_encode([
            'success' => false,
            'message' => $orderResult['message']
        ]);
        exit;
    }

    $order_id = $orderResult['order_id'];
    $invoice_no = $orderResult['invoice_no'];

    // Add order details (no price field in orderdetails table - price comes from products table)
    $allDetailsAdded = true;
    foreach ($orderItems as $item) {
        $detailParams = [
            'order_id' => $order_id,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity']
        ];

        $detailResult = $orderController->add_order_details_ctr($detailParams);
        
        if (!$detailResult['success']) {
            $allDetailsAdded = false;
            break;
        }
    }

    if (!$allDetailsAdded) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add order details. Please try again.'
        ]);
        exit;
    }

    // Record payment (payment table requires customer_id and currency)
    $paymentParams = [
        'order_id' => $order_id,
        'amount' => $orderTotal,
        'customer_id' => $customer_id,
        'currency' => 'GHS'
    ];

    $paymentResult = $orderController->record_payment_ctr($paymentParams);

    if (!$paymentResult['success']) {
        echo json_encode([
            'success' => false,
            'message' => $paymentResult['message']
        ]);
        exit;
    }

    // Empty the cart after successful checkout
    $cartController->empty_cart_ctr($ip_address, $customer_id);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_id' => $order_id,
        'invoice_no' => $invoice_no,
        'order_total' => $orderTotal,
        'order_items' => $orderItems
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>

