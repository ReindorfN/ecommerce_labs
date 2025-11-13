<?php
// Clean any existing output buffers
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once '../settings/core.php';
require_once '../controllers/cart_controller.php';

// Function to get client IP address
function getClientIp() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    // Validate required fields
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
        exit;
    }
    
    if (!isset($_POST['quantity']) || empty($_POST['quantity'])) {
        echo json_encode(['success' => false, 'message' => 'Quantity is required.']);
        exit;
    }
    
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $increment = isset($_POST['increment']) && $_POST['increment'] == 'true';
    $ip_address = getClientIp();
    // customer_id for logged-in users, NULL for guests
    $customer_id = isLoggedIn() ? $_SESSION['user_id'] : null;
    
    // Validate quantity
    if ($quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1.']);
        exit;
    }
    
    // Create controller instance
    $cartController = new CartController();
    
    // Update cart using controller
    $result = $cartController->update_cart_ctr($product_id, $ip_address, $customer_id, $quantity, $increment);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Cart updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update cart.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Output JSON and exit
exit;

?>

