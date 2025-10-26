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


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    // Gather posted data with fallback for missing IDs
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;
    $product_cat = isset($_POST['product_cat']) ? $_POST['product_cat'] : null;
    $product_brand = isset($_POST['product_brand']) ? $_POST['product_brand'] : null;
    $product_title = isset($_POST['product_title']) ? $_POST['product_title'] : null;
    $product_price = isset($_POST['product_price']) ? $_POST['product_price'] : null;
    $product_desc = isset($_POST['product_desc']) ? $_POST['product_desc'] : null;
    $product_image = isset($_POST['product_image']) ? $_POST['product_image'] : null;
    $product_keywords = isset($_POST['product_keywords']) ? $_POST['product_keywords'] : null;
    
    // Create controller instance
    $productController = new ProductController();
    
    // Update product using controller
    $result = $productController->update_product_ctr(
        $product_id,
        $product_cat,
        $product_brand,
        $product_title,
        $product_price,
        $product_desc,
        $product_image,
        $product_keywords
    );
    
    // Return JSON response
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Ensure clean output
ob_end_flush();
?>