<?php

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

function fetchCartItemsForDisplay() {
    // Get client IP address
    $ip_address = getClientIp();

    // Get customer_id for logged-in users, NULL for guests
    $customer_id = isLoggedIn() ? $_SESSION['user_id'] : null;

    // Create controller instance
    $cartController = new CartController();

    // Fetch cart items using the controller
    return $cartController->get_cart_items_ctr($ip_address, $customer_id);
}

function handleCartItemsAjaxRequest() {
    // Ensure clean output buffer
    ob_clean();

    // Set proper headers
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');

    // Get cart items using the controller
    $cartItems = fetchCartItemsForDisplay();

    // Return cart items as JSON
    echo json_encode(['success' => true, 'cart_items' => $cartItems]);
}

?>