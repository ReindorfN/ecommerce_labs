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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['empty_cart'])) {
    $ip_address = getClientIp();
    // customer_id for logged-in users, NULL for guests
    $customer_id = isLoggedIn() ? $_SESSION['user_id'] : null;
    
    // Create controller instance
    $cartController = new CartController();
    
    // Empty cart using controller
    $result = $cartController->empty_cart_ctr($ip_address, $customer_id);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Cart emptied successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to empty cart.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Output JSON and exit
exit;

?>
