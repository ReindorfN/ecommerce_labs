<?php
// Ensure clean output buffer
ob_clean();

// Set proper headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    
    // Create controller instance
    $productController = new ProductController();
    
    // Deleting product using controller
    $result = $productController->delete_product_ctr($product_id);
    
    // Return JSON response
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Ensure clean output
ob_end_flush();
exit;

?>