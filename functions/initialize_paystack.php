<?php
require_once '../settings/core.php';
require_once '../controllers/order_controller.php';
require_once '../controllers/cart_controller.php';
require_once '../controllers/product_controller.php';
require_once '../models/paystack_handler.php';
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
    $paystackHandler = new PaystackHandler();

    // Get customer ID and email from session
    $customer_id = $_SESSION['user_id'];
    $customer_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

    if (empty($customer_email)) {
        echo json_encode([
            'success' => false,
            'message' => 'Customer email not found. Please update your profile.'
        ]);
        exit;
    }
    
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

    // Calculate total amount
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

    // Create order first (with pending status)
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

    // Add order details (but don't record payment yet - wait for Paystack confirmation)
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

    // Generate unique reference for Paystack (use order_id + timestamp)
    $paystack_reference = 'PSK_' . $order_id . '_' . time();

    // Initialize Paystack transaction
    $callback_url = PAYSTACK_CALLBACK_URL . '?order_id=' . $order_id;
    
    $paystackParams = [
        'email' => $customer_email,
        'amount' => $orderTotal,
        'reference' => $paystack_reference,
        'callback_url' => $callback_url,
        'metadata' => [
            'order_id' => $order_id,
            'invoice_no' => $invoice_no,
            'customer_id' => $customer_id
        ]
    ];

    $paystackResponse = $paystackHandler->initializeTransaction($paystackParams);

    if (!$paystackResponse['status']) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to initialize payment: ' . $paystackResponse['message']
        ]);
        exit;
    }

    // Store Paystack reference in session for verification
    $_SESSION['paystack_reference'] = $paystack_reference;
    $_SESSION['pending_order_id'] = $order_id;

    // Return success with Paystack authorization URL
    echo json_encode([
        'success' => true,
        'message' => 'Payment initialized successfully.',
        'authorization_url' => $paystackResponse['data']['authorization_url'],
        'access_code' => $paystackResponse['data']['access_code'],
        'reference' => $paystack_reference,
        'order_id' => $order_id,
        'invoice_no' => $invoice_no
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>

