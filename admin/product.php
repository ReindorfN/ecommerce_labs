<?php
require_once '../settings/core.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login/login.php");
    exit;
}

// Include the fetch action functions
require_once '../functions/fetch_product_action.php';
require_once '../functions/fetch_category_action.php';
require_once '../functions/fetch_brand_action.php';

// Fetch products, categories and brands using the functions
$products = fetchProductsForDisplay();
$categories = fetchCategoriesForDisplay();
$brands = fetchBrandsForDisplay();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill-Office Africa | Admin - Product Management</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .navbar {
            position: fixed;
            top: 20px;
            left: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 150px;
        }
        
        .navbar a {
            text-decoration: none;
        }
        
        .navbar button {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }
        
        .navbar button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .navbar button:active {
            transform: translateY(0);
        }
        
        .container {
            max-width: 1400px;
            margin: 100px auto 50px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border-left: 5px solid #8b5cf6;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .header p {
            color: #666;
            margin: 10px 0 0 0;
            font-size: 1.1rem;
        }
        
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .add-product-btn {
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }
        
        .add-product-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }
        
        .add-product-btn:active {
            transform: translateY(0);
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .product-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.2);
        }
        
        .product-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 10px 0;
        }
        
        .product-details {
            font-size: 0.9rem;
            color: #666;
            margin: 5px 0;
        }
        
        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #8b5cf6;
            margin: 15px 0;
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 10px;
        }
        
        .edit-product-btn, .delete-product-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
        }
        
        .edit-product-btn:hover {
            background-color: #e3f2fd;
            transform: scale(1.1);
        }
        
        .delete-product-btn:hover {
            background-color: #ffebee;
            transform: scale(1.1);
        }
        
        .edit-product-btn span, .delete-product-btn span {
            font-size: 1.2em;
            line-height: 1;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state h3 {
            margin: 0 0 10px 0;
            font-size: 1.5rem;
        }
        
        .empty-state p {
            margin: 0;
            font-size: 1.1rem;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background-color: white;
            margin: 3% auto;
            padding: 0;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-header {
            background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 20px 20px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }
        
        .close:hover {
            opacity: 0.7;
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
            font-family: inherit;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 25px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 92, 246, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 80px 10px 20px 10px;
                padding: 15px;
            }
            
            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .add-product-btn {
                width: 100%;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 95%;
                margin: 5% auto;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php"><button>Home</button></a>
        <a href="category.php"><button>Categories & Brands</button></a>
        <a href="../functions/logout_user_action.php"><button>Logout</button></a>
    </nav>
    
    <div class="container">
        <div class="header">
            <h1>🛍️ Product Management</h1>
            <p>Create and manage products in your shop</p>
        </div>
        
        <div id="alert-container"></div>
        
        <div class="action-bar">
            <button class="add-product-btn" onclick="openProductModal()">
                ➕ Add New Product
            </button>
        </div>
        
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <h3>No Products Yet</h3>
                <p>Start by adding your first product to your shop.</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php 
                // Helper: create mapping for category and brand names
                $catMap = [];
                foreach ($categories as $cat) {
                    $catMap[$cat['cat_id']] = $cat['cat_name'];
                }
                $brandMap = [];
                foreach ($brands as $brand) {
                    $brandMap[$brand['brand_id']] = $brand['brand_name'];
                }
                ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['product_title']); ?></h3>
                        <div class="product-modal-vertical-split" style="display: flex; flex-direction: row; gap: 16px;">
                            <div class="product-modal-image-section" style="flex: 1 1 40%; display: flex; align-items: flex-start; justify-content: center;">
                                <?php if (!empty($product['product_image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($product['product_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['product_title']); ?>" 
                                         style="max-width: 100%; max-height: 180px; border-radius:8px; border:1px solid #eee;">
                                <?php else: ?>
                                    <div style="width:100px;height:120px;display:flex;align-items:center;justify-content:center;background:#f2f2f2;color:#aaa;border-radius:8px; border:1px solid #eee">
                                        No Image
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="product-modal-info-section" style="flex: 2 1 60%;">
                                <p class="product-details"><strong>Category:</strong>
                                    <?php 
                                        $catName = isset($catMap[$product['product_cat']]) ? $catMap[$product['product_cat']] : 'Unknown';
                                        echo htmlspecialchars($catName); 
                                    ?>
                                </p>
                                <p class="product-details"><strong>Brand:</strong>
                                    <?php 
                                        $brandName = isset($brandMap[$product['product_brand']]) ? $brandMap[$product['product_brand']] : 'Unknown';
                                        echo htmlspecialchars($brandName);
                                    ?>
                                </p>
                                <p class="product-details"><strong>Description:</strong> 
                                    <?php echo htmlspecialchars(substr($product['product_desc'], 0, 50)) . '...'; ?>
                                </p>
                                <p class="product-price">GH₵ <?php echo number_format($product['product_price'], 2); ?></p>
                                <div class="product-actions">
                                    <button class="edit-product-btn" title="Edit"
                                            data-product-id="<?php echo $product['product_id']; ?>"
                                            data-product-title="<?php echo htmlspecialchars($product['product_title']); ?>">
                                        <span aria-label="Edit">&#9998;</span>
                                    </button>
                                    <button class="delete-product-btn" title="Delete"
                                            data-product-id="<?php echo $product['product_id']; ?>">
                                        <span aria-label="Delete">&#128465;</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Add Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Product</h2>
                <span class="close" onclick="closeProductModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="productForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="product_title">Product Title *</label>
                        <input type="text" id="product_title" name="product_title" required 
                               placeholder="Enter product title (e.g., iPhone 15 Pro)" 
                               maxlength="200">
                    </div>
                    
                    <div class="form-group">
                        <label for="product_cat">Category *</label>
                        <select id="product_cat" name="product_cat" required>
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['cat_id']; ?>">
                                    <?php echo htmlspecialchars($cat['cat_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="product_brand">Brand *</label>
                        <select id="product_brand" name="product_brand" required>
                            <option value="">Select a brand</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo $brand['brand_id']; ?>">
                                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="product_price">Price *</label>
                        <input type="number" id="product_price" name="product_price" required 
                               placeholder="0.00" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="product_desc">Description *</label>
                        <textarea id="product_desc" name="product_desc" required 
                                  placeholder="Enter product description..." 
                                  maxlength="500"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="product_keywords">Keywords *</label>
                        <input type="text" id="product_keywords" name="product_keywords" required 
                               placeholder="e.g., smartphone, mobile, ios" 
                               maxlength="100">
                    </div>
                    
                    <div class="form-group">
                        <label for="product_image">Product Image *</label>
                        <input type="file" id="product_image" name="product_image" required accept="image/*">
                        <small style="color: #666; display: block; margin-top: 5px;">Max size: 5MB. Formats: JPG, PNG, GIF, WebP</small>
                        <div id="imagePreview" style="margin-top: 10px; display: none;">
                            <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeProductModal()">Cancel</button>
                        <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Product</h2>
                <span class="close" onclick="closeEditProductModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editProductForm" enctype="multipart/form-data">
                    <input type="hidden" id="edit_product_id" name="product_id">

                    <div class="form-group">
                        <label for="edit_product_title">Product Title *</label>
                        <input type="text" id="edit_product_title" name="product_title" required 
                               placeholder="Enter product title" 
                               maxlength="200">
                    </div>

                    <div class="form-group">
                        <label for="edit_product_cat">Category *</label>
                        <select id="edit_product_cat" name="product_cat" required>
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['cat_id']; ?>">
                                    <?php echo htmlspecialchars($cat['cat_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_product_brand">Brand *</label>
                        <select id="edit_product_brand" name="product_brand" required>
                            <option value="">Select a brand</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo $brand['brand_id']; ?>">
                                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_product_price">Price *</label>
                        <input type="number" id="edit_product_price" name="product_price" required 
                               placeholder="0.00" step="0.01" min="0">
                    </div>

                    <div class="form-group">
                        <label for="edit_product_desc">Description *</label>
                        <textarea id="edit_product_desc" name="product_desc" required 
                                  placeholder="Enter product description..." 
                                  maxlength="500"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit_product_keywords">Keywords *</label>
                        <input type="text" id="edit_product_keywords" name="product_keywords" required 
                               placeholder="e.g., smartphone, mobile, ios" 
                               maxlength="100">
                    </div>

                    <div class="form-group">
                        <label for="edit_product_image">Product Image *</label>
                        <div id="editImageSection">
                            <div id="currentImageWrapper" style="margin-bottom: 10px;">
                                <img id="edit_current_product_image" src="" alt="Current Image" style="max-width: 100%; max-height: 200px; border-radius: 8px; display: none;">
                                <button type="button" id="deleteImageBtn" style="display:none; margin-top: 5px;" onclick="deleteEditProductImage()">Delete Current Image</button>
                            </div>
                            <input type="file" id="edit_product_image" name="product_image" accept="image/*" style="display: block;">
                            <small style="color: #666; display: block; margin-top: 5px;">
                                Max size: 5MB. Formats: JPG, PNG, GIF, WebP
                            </small>
                            <div id="editImagePreview" style="margin-top: 10px; display: none;">
                                <img id="editPreviewImg" src="" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                            </div>
                        </div>
                        <input type="hidden" id="edit_product_image_old" name="product_image_old">
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeEditProductModal()">Cancel</button>
                        <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Fill the edit product form with current product details
    // This function must be called with product data when opening modal
    function populateEditProductModal(product) {
        $('#edit_product_id').val(product.product_id);
        $('#edit_product_title').val(product.product_title);
        $('#edit_product_cat').val(product.product_cat);
        $('#edit_product_brand').val(product.product_brand);
        $('#edit_product_price').val(product.product_price);
        $('#edit_product_desc').val(product.product_desc);
        $('#edit_product_keywords').val(product.product_keywords);

        // Handle image
        if (product.product_image) {
            $('#edit_current_product_image')
                .attr('src', '../product_images/' + product.product_image)
                .show();
            $('#deleteImageBtn').show();
            $('#edit_product_image_old').val(product.product_image);
        } else {
            $('#edit_current_product_image').hide();
            $('#deleteImageBtn').hide();
            $('#edit_product_image_old').val('');
        }
        // Reset file input and preview
        $('#edit_product_image').val('');
        $('#editImagePreview').hide();
        $('#editPreviewImg').attr('src', '');
    }

    // Image preview for new upload
    $('#edit_product_image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#editPreviewImg').attr('src', e.target.result);
                $('#editImagePreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#editImagePreview').hide();
            $('#editPreviewImg').attr('src', '');
        }
    });

    // Delete current image
    function deleteEditProductImage() {
        $('#edit_current_product_image').hide();
        $('#deleteImageBtn').hide();
        $('#edit_product_image_old').val(''); // Indicate deletion on server/save
    }
    </script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/product_management.js"></script>
</body>
</html>
