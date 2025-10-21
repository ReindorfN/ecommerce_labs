<?php
// Ensure clean output buffer
ob_clean();

// Set proper headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once '../settings/core.php';
require_once '../controllers/brand_controller.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_brand'])) {
    $brand_id = $_POST['brand_id'];
    $brand_name = $_POST['brand_name'];
    
    // Create controller instance
    $brandController = new BrandController();
    
    // Updating brand using controller
    $result = $brandController->update_brand_ctr($brand_id, $brand_name);
    
    // Return JSON response
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Ensure clean output
ob_end_flush();
?>