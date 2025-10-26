<?php
// Ensure clean output buffer
ob_clean();

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once '../settings/core.php';
require_once '../settings/db_model.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['product_image'])) {
    $uploadDir = '../uploads/';
    $userId = $_SESSION['user_id'] ?? 0;
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    // Check if file was uploaded
    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'File upload failed.']);
        exit;
    }
    
    $file = $_FILES['product_image'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only images are allowed.']);
        exit;
    }
    
    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit.']);
        exit;
    }
    
    // Ensure uploads directory exists and is writable
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            echo json_encode(['success' => false, 'message' => 'Failed to create uploads directory.']);
            exit;
        }
    }
    
    if (!is_writable($uploadDir)) {
        echo json_encode(['success' => false, 'message' => 'Uploads directory is not writable.']);
        exit;
    }
    
    // Generate filename: {catID}_{brandID}_{productID}_productImage_{timestamp}.{extension}
    $catId = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
    $brandId = isset($_POST['brand_id']) ? intval($_POST['brand_id']) : 0;
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $catId . '_' . $brandId . '_' . $productId . '_productImage_' . time() . '.' . $extension;
    $targetPath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Return relative path from the uploads directory
        $relativePath = 'uploads/' . $filename;
        
        echo json_encode([
            'success' => true,
            'message' => 'Image uploaded successfully.',
            'image_path' => $relativePath
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to move uploaded file.'
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
}

ob_end_flush();
exit;

?>
