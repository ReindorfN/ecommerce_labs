<?php
ob_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['get_product'])) {
    $product_id = $_POST['product_id'];

    $productController = new ProductController();
    $product = $productController->get_product_by_id_ctr($product_id);

    if ($product) {
        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

ob_end_flush();
?>
