<?php
require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

// function to fetch brands
function fetchProductsForDisplay() {
    // Check if user is logged in and is admin
    if (!isLoggedIn() || !isAdmin()) {
        return [];
    }

    // Create controller instance
    $productController = new ProductController();
    
    // Fetch existing categories using the controller method
    return $productController->fetch_products_ctr();
}


// Function to handle AJAX requests for products
function handleProductAjaxRequest() {
    // Ensure clean output buffer
    ob_clean();

    // Set proper headers
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');

    // Check if user is logged in and is admin
    if (!isLoggedIn() || !isAdmin()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
        exit;
    }

    // Create controller instance
    $productController = new ProductController();

    // Fetch existing brandsusing the controller method
    $products = $productController->fetch_products_ctr();

    // Return JSON response with success status
    echo json_encode([
        'success' => true,
        'data' => $products,
        'count' => count($products)
    ]);

    // Ensure clean output
    ob_end_flush();
    exit;
}

// If this is an AJAX request, handle it
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    handleProductAjaxRequest();
}

?>