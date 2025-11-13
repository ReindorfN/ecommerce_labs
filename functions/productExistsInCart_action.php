<?php
// Ensure clean output buffer
ob_clean();

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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_exists_in_cart'])) {
    // Validate required fields
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
        exit;
    }
    
    $product_id = intval($_POST['product_id']);
    $ip_address = getClientIp();
    // customer_id for logged-in users, NULL for guests
    $customer_id = isLoggedIn() ? $_SESSION['user_id'] : null;
    
    // Create controller instance
    $cartController = new CartController();
    
    // Check if product exists in cart using controller
    $exists = $cartController->product_exists_in_cart_ctr($product_id, $ip_address, $customer_id);
    
    echo json_encode(['success' => true, 'exists' => $exists]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Ensure clean output
ob_end_flush();
exit;

?>

