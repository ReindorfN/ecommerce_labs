<?php
require_once '../settings/core.php';
require_once '../controllers/brand_controller.php';

// function to fetch brands
function fetchBrandsForDisplay() {
    // Check if user is logged in and is admin
    if (!isLoggedIn() || !isAdmin()) {
        return [];
    }

    // Create controller instance
    $brandController = new BrandController();
    
    // Fetch existing categories using the controller method
    return $brandController->fetch_brands_ctr();
}


// Function to handle AJAX requests for brands
function handleBrandAjaxRequest() {
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
    $brandController = new BrandController();

    // Fetch existing brandsusing the controller method
    $brands = $brandController->fetch_brands_ctr();

    // Return JSON response with success status
    echo json_encode([
        'success' => true,
        'data' => $brands,
        'count' => count($brands)
    ]);

    // Ensure clean output
    ob_end_flush();
    exit;
}

// If this is an AJAX request, handle it
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    handleBrandAjaxRequest();
}

?>