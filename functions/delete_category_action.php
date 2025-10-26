<?php
// Ensure clean output buffer
ob_clean();

// Set proper headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once '../settings/core.php';
require_once '../controllers/category_controller.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_category'])) {
    $cat_id = $_POST['cat_id'];
    
    // Create controller instance
    $categoryController = new CategoryController();
    
    // Delete category using controller
    $result = $categoryController->delete_category_ctr($cat_id);
    
    // Return JSON response
    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Ensure clean output
ob_end_flush();
exit;
?>
