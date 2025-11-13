<?php
require_once '../settings/core.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login/login.php");
    exit;
}

// Include the fetch action functions
require_once '../functions/fetch_category_action.php';
require_once '../functions/fetch_brand_action.php';

// Fetch categories and brands using the functions
$categories = fetchCategoriesForDisplay();
$brands = fetchBrandsForDisplay();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill-Office Africa | Admin - Categories & Brands</title>
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
        
        .main-container {
            max-width: 1400px;
            margin: 100px auto 50px auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
        }
        
        .categories-container {
            border-left: 5px solid #28a745;
        }
        
        .brands-container {
            border-left: 5px solid #ff6b6b;
            /* background: linear-gradient(135deg, rgba(255, 43, 107, 0.05) 0%, rgba(255, 193, 7, 0.05) 100%); */
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
        
        .add-category-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .add-category-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }
        
        .add-category-btn:active {
            transform: translateY(0);
        }
        
        .add-brand-btn {
            background: linear-gradient(135deg, #ff6b6b, #ffa726);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }
        
        .add-brand-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        }
        
        .add-brand-btn:active {
            transform: translateY(0);
        }
        
        .categories-grid, .brands-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .category-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .brand-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .brand-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #ff6b6b, #ffa726);
        }
        
        .brand-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255, 107, 107, 0.2);
        }
        
        .category-name, .brand-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 10px 0;
        }
        
        .category-id, .brand-id {
            font-size: 0.9rem;
            color: #666;
            margin: 0 0 15px 0;
        }
        
        .category-actions, .brand-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 10px;
        }
        
        .edit-category-btn, .delete-category-btn, .edit-brand-btn, .delete-brand-btn {
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
        
        .edit-category-btn:hover, .edit-brand-btn:hover {
            background-color: #e3f2fd;
            transform: scale(1.1);
        }
        
        .delete-category-btn:hover, .delete-brand-btn:hover {
            background-color: #ffebee;
            transform: scale(1.1);
        }
        
        .edit-category-btn span, .delete-category-btn span, .edit-brand-btn span, .delete-brand-btn span {
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
            margin: 5% auto;
            padding: 0;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.3s ease;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
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
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
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
            .main-container {
                margin: 80px 10px 20px 10px;
                padding: 15px;
            }
            
            .container {
                padding: 15px;
            }
            
            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .add-category-btn, .add-brand-btn {
                width: 100%;
            }
            
            .categories-grid, .brands-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 95%;
                margin: 10% auto;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php"><button>Home</button></a>
        <a href="product.php"><button>Product Dashboard</button></a>
        <a href="../functions/logout_user_action.php"><button>Logout</button></a>
    </nav>
    
    <div class="main-container">
        <div id="alert-container"></div>
        
        <!-- Categories Section -->
        <div class="container categories-container">
            <div class="header">
                <h1>📁 Category Management</h1>
                <p>Organize your products with categories</p>
            </div>
            
            <div class="action-bar">
                <button class="add-category-btn" onclick="openCategoryModal()">
                    ➕ Add New Category
                </button>
            </div>
            
            <?php if (empty($categories)): ?>
                <div class="empty-state">
                    <h3>No Categories Yet</h3>
                    <p>Start by adding your first product category to organize your shop.</p>
                </div>
            <?php else: ?>
                <div class="categories-grid">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-card">
                            <h3 class="category-name"><?php echo htmlspecialchars($category['cat_name']); ?></h3>
                            <p class="category-id">ID: <?php echo $category['cat_id']; ?></p>
                            <div class="category-actions">
                                <button class="edit-category-btn" title="Edit" data-cat-id="<?php echo $category['cat_id']; ?>" data-cat-name="<?php echo htmlspecialchars($category['cat_name']); ?>">
                                    <span aria-label="Edit">&#9998;</span>
                                </button>
                                <button class="delete-category-btn" title="Delete" data-cat-id="<?php echo $category['cat_id']; ?>">
                                    <span aria-label="Delete">&#128465;</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Brands Section -->
        <div class="container brands-container">
            <div class="header">
                <h1>🏷️ Brand Management</h1>
                <p>Manage product brands and manufacturers</p>
            </div>
            
            <div class="action-bar">
                <button class="add-brand-btn" onclick="openBrandModal()">
                    ➕ Add New Brand
                </button>
            </div>
            
            <?php if (empty($brands)): ?>
                <div class="empty-state">
                    <h3>No Brands Yet</h3>
                    <p>Start by adding your first product brand to organize your shop.</p>
                </div>
            <?php else: ?>
                <div class="brands-grid">
                    <?php foreach ($brands as $brand): ?>
                        <div class="brand-card">
                            <h3 class="brand-name"><?php echo htmlspecialchars($brand['brand_name']); ?></h3>
                            <p class="brand-id">ID: <?php echo $brand['brand_id']; ?></p>
                            <div class="brand-actions">
                                <button class="edit-brand-btn" title="Edit" data-brand-id="<?php echo $brand['brand_id']; ?>" data-brand-name="<?php echo htmlspecialchars($brand['brand_name']); ?>">
                                    <span aria-label="Edit">&#9998;</span>
                                </button>
                                <button class="delete-brand-btn" title="Delete" data-brand-id="<?php echo $brand['brand_id']; ?>">
                                    <span aria-label="Delete">&#128465;</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Add Category Modal -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Category</h2>
                <span class="close" onclick="closeCategoryModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <div class="form-group">
                        <label for="category_name">Category Name</label>
                        <input type="text" id="category_name" name="category_name" required 
                               placeholder="Enter category name (e.g., Electronics, Clothing)" 
                               maxlength="100">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeCategoryModal()">Cancel</button>
                        <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Category</h2>
                <span class="close" onclick="closeEditCategoryModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm">
                    <input type="hidden" id="edit_cat_id" name="cat_id">
                    <div class="form-group">
                        <label for="edit_category_name">Category Name</label>
                        <input type="text" id="edit_category_name" name="category_name" required 
                               placeholder="Enter category name (e.g., Electronics, Clothing)" 
                               maxlength="100">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeEditCategoryModal()">Cancel</button>
                        <button type="submit" name="update_category" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Add Brand Modal -->
    <div id="brandModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Brand</h2>
                <span class="close" onclick="closeBrandModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="brandForm">
                    <div class="form-group">
                        <label for="brand_name">Brand Name</label>
                        <input type="text" id="brand_name" name="brand_name" required 
                               placeholder="Enter brand name (e.g., Apple, Samsung, Nike)" 
                               maxlength="100">
                    </div>
                    <div class="form-actions"> 
                        <button type="button" class="btn btn-secondary" onclick="closeBrandModal()">Cancel</button>
                        <button type="submit" name="add_brand" class="btn btn-primary">Add Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Brand Modal -->
    <div id="editBrandModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Brand</h2>
                <span class="close" onclick="closeEditBrandModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editBrandForm">
                    <input type="hidden" id="edit_brand_id" name="brand_id">
                    <div class="form-group">
                        <label for="edit_brand_name">Brand Name</label>
                        <input type="text" id="edit_brand_name" name="brand_name" required 
                               placeholder="Enter brand name (e.g., Apple, Samsung, Nike)" 
                               maxlength="100">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeEditBrandModal()">Cancel</button>
                        <button type="submit" name="update_brand" class="btn btn-primary">Update Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/unified_management.js"></script>
</body>
</html>
