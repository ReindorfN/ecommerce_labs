<?php
require_once '../settings/core.php';
require_once '../controllers/category_controller.php';

// Function to fetch categories (for direct inclusion)
function fetchCategoriesForDisplay() {
    // Check if user is logged in and is admin
    if (!isLoggedIn() || !isAdmin()) {
        return [];
    }

    // Create controller instance
    $categoryController = new CategoryController();
    
    // Fetch existing categories using the controller method
    return $categoryController->fetch_categories_ctr();
}

// Function to handle AJAX requests for categories
function handleCategoryAjaxRequest() {
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
    $categoryController = new CategoryController();

    // Fetch existing categories using the controller method
    $categories = $categoryController->fetch_categories_ctr();

    // Return JSON response with success status
    echo json_encode([
        'success' => true,
        'data' => $categories,
        'count' => count($categories)
    ]);

    // Ensure clean output
    ob_end_flush();
    exit;
}

// If this is an AJAX request, handle it
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    handleCategoryAjaxRequest();
}