<?php
require_once '../settings/core.php';

// Include controllers
require_once '../controllers/product_controller.php';
require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';

// Initialize controllers
$productController = new ProductController();
$categoryController = new CategoryController();
$brandController = new BrandController();

// Get filter parameters
$filterCategory = isset($_GET['category']) ? intval($_GET['category']) : 0;
$filterBrand = isset($_GET['brand']) ? intval($_GET['brand']) : 0;
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;

// Fetch all categories and brands for filters
$allCategories = $categoryController->fetch_categories_ctr();
$allBrands = $brandController->fetch_brands_ctr();

// Create lookup arrays for category and brand names
$categoryNames = [];
$brandNames = [];
foreach ($allCategories as $cat) {
    $categoryNames[$cat['cat_id']] = $cat['cat_name'];
}
foreach ($allBrands as $brand) {
    $brandNames[$brand['brand_id']] = $brand['brand_name'];
}

// Fetch products based on filters
$allProducts = [];
if (!empty($searchQuery)) {
    $allProducts = $productController->search_products_ctr($searchQuery);
} elseif ($filterCategory > 0) {
    $allProducts = $productController->filter_products_by_category_ctr($filterCategory);
} elseif ($filterBrand > 0) {
    $allProducts = $productController->filter_products_by_brand_ctr($filterBrand);
} else {
    $allProducts = $productController->view_all_products_ctr();
}

// Pagination
$totalProducts = count($allProducts);
$totalPages = ceil($totalProducts / $perPage);
$offset = ($page - 1) * $perPage;
$products = array_slice($allProducts, $offset, $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products | Skill-Office Africa</title>
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
        
        .container {
            max-width: 1400px;
            margin: 100px auto 50px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
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
        
        .filters-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .filter-group label {
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
        }
        
        .filter-group select,
        .filter-group input {
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .filter-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .clear-filters {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .clear-filters:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        
        .product-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
            background: #f8f9fa;
        }
        
        .product-id {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .product-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 10px 0;
            line-height: 1.4;
        }
        
        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            margin: 10px 0;
        }
        
        .product-meta {
            font-size: 0.9rem;
            color: #666;
            margin: 5px 0;
        }
        
        .product-meta strong {
            color: #333;
        }
        
        .add-to-cart-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: auto;
            transition: all 0.3s ease;
        }
        
        .add-to-cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }
        
        .pagination a,
        .pagination span {
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .pagination a {
            background: #667eea;
            color: white;
        }
        
        .pagination a:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }
        
        .pagination .current {
            background: #764ba2;
            color: white;
        }
        
        .pagination .disabled {
            background: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
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
        
        .results-info {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        
        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 2000;
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }
        
        .modal-overlay.active {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .modal-content {
            background: white;
            border-radius: 20px;
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
            position: relative;
        }
        
        .modal-header {
            position: sticky;
            top: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 20px 20px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }
        
        .modal-header h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 28px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        
        .modal-body {
            padding: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .modal-image-section {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .modal-product-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            background: #f8f9fa;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .modal-details-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .modal-product-id {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .modal-product-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin: 0 0 15px 0;
            line-height: 1.3;
        }
        
        .modal-product-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
            margin: 15px 0;
        }
        
        .modal-product-desc {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
            margin: 15px 0;
        }
        
        .modal-product-meta {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 15px 0;
        }
        
        .modal-meta-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .modal-meta-item:last-child {
            border-bottom: none;
        }
        
        .modal-meta-label {
            font-weight: 600;
            color: #333;
        }
        
        .modal-meta-value {
            color: #666;
        }
        
        .modal-keywords {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        
        .modal-keyword-tag {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .modal-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .modal-add-to-cart {
            flex: 1;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .modal-add-to-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .modal-login-prompt {
            flex: 1;
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .modal-login-prompt:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 80px 10px 20px 10px;
                padding: 15px;
            }
            
            .filters-section {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-body {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .modal-product-image {
                height: 300px;
            }
            
            .modal-header {
                padding: 15px 20px;
            }
            
            .modal-header h2 {
                font-size: 1.4rem;
            }
            
            .modal-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php"><button>Home</button></a>
        <?php if (isLoggedIn()): ?>
            <?php if (isAdmin()): ?>
                <a href="../admin/category.php"><button>Category & Brands</button></a>
                <a href="../admin/product.php"><button>Product Dashboard</button></a>
            <?php endif; ?>
            <a href="../functions/logout_user_action.php"><button>Logout</button></a>
        <?php else: ?>
            <a href="../login/login.php"><button>Login</button></a>
            <a href="../login/register.php"><button>Register</button></a>
        <?php endif; ?>
    </nav>
    
    <div class="container">
        <div class="header" style="position: relative;">
            <h1>🛍️ All Products</h1>
            <p>Browse our complete product catalog</p>
            <a href="../views/cart.php" 
               style="
                    position: absolute;
                    top: 15px;
                    right: 0;
                    text-decoration: none;
                    background: #667eea;
                    color: #fff;
                    border-radius: 50%;
                    width: 42px;
                    height: 42px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 2px 8px rgba(60,30,90,.16);
                    transition: background 0.2s;
                    font-size: 1.2rem;
                "
               title="View Cart">
                <span style="display: inline-block;">
                🛒
                </span>
            </a>
        </div>
        
        <!-- Filters Section -->
        <div class="filters-section">
            <form method="GET" action="" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; width: 100%;">
                <div class="filter-group">
                    <label for="search">Search Products</label>
                    <input type="text" id="search" name="search" placeholder="Search by name, description..." 
                           value="<?php echo htmlspecialchars($searchQuery); ?>">
                </div>
                
                <div class="filter-group">
                    <label for="category">Filter by Category</label>
                    <select id="category" name="category">
                        <option value="0">All Categories</option>
                        <?php foreach ($allCategories as $cat): ?>
                            <option value="<?php echo $cat['cat_id']; ?>" 
                                    <?php echo ($filterCategory == $cat['cat_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['cat_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="brand">Filter by Brand</label>
                    <select id="brand" name="brand">
                        <option value="0">All Brands</option>
                        <?php foreach ($allBrands as $brand): ?>
                            <option value="<?php echo $brand['brand_id']; ?>" 
                                    <?php echo ($filterBrand == $brand['brand_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($brand['brand_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="filter-btn">Apply Filters</button>
                <?php if ($filterCategory > 0 || $filterBrand > 0 || !empty($searchQuery)): ?>
                    <a href="../views/all_products.php" class="clear-filters">Clear Filters</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Results Info -->
        <div class="results-info">
            Showing <?php echo count($products); ?> of <?php echo $totalProducts; ?> products
            <?php if ($totalPages > 1): ?>
                (Page <?php echo $page; ?> of <?php echo $totalPages; ?>)
            <?php endif; ?>
        </div>
        
        <!-- Products Grid -->
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <h3>No Products Found</h3>
                <p>Try adjusting your filters or search query.</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-id">ID: <?php echo $product['product_id']; ?></div>
                        
                        <?php if (!empty($product['product_image'])): ?>
                            <img src="../<?php echo htmlspecialchars($product['product_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['product_title']); ?>" 
                                 class="product-image"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23f8f9fa\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ENo Image%3C/text%3E%3C/svg%3E'">
                        <?php else: ?>
                            <div class="product-image" style="display: flex; align-items: center; justify-content: center; color: #999;">
                                No Image Available
                            </div>
                        <?php endif; ?>
                        
                        <h3 class="product-title"><?php echo htmlspecialchars($product['product_title']); ?></h3>
                        
                        <div class="product-price">GH₵<?php echo number_format($product['product_price'], 2); ?></div>
                        
                        <div class="product-meta">
                            <strong>Category:</strong> 
                            <?php echo isset($categoryNames[$product['product_cat']]) ? htmlspecialchars($categoryNames[$product['product_cat']]) : 'N/A'; ?>
                        </div>
                        
                        <div class="product-meta">
                            <strong>Brand:</strong> 
                            <?php echo isset($brandNames[$product['product_brand']]) ? htmlspecialchars($brandNames[$product['product_brand']]) : 'N/A'; ?>
                        </div>
                        
                        <button
                            type="button"
                            class="view-details-link product-modal-trigger"
                            data-product='<?php echo json_encode($product, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                            data-category='<?php echo json_encode(isset($categoryNames[$product['product_cat']]) ? $categoryNames[$product['product_cat']] : 'N/A', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                            data-brand='<?php echo json_encode(isset($brandNames[$product['product_brand']]) ? $brandNames[$product['product_brand']] : 'N/A', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                            style="background: none; border: none; color: #667eea; text-align: center; margin-top: 10px; font-size: 0.9rem; cursor: pointer; text-decoration: underline;"
                        >
                            View Details →
                        </button>
                        <button class="add-to-cart-btn" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                            🛒 Add to Cart
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $filterCategory > 0 ? '&category=' . $filterCategory : ''; ?><?php echo $filterBrand > 0 ? '&brand=' . $filterBrand : ''; ?><?php echo !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; ?>">Previous</a>
                <?php else: ?>
                    <span class="disabled">Previous</span>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?><?php echo $filterCategory > 0 ? '&category=' . $filterCategory : ''; ?><?php echo $filterBrand > 0 ? '&brand=' . $filterBrand : ''; ?><?php echo !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $filterCategory > 0 ? '&category=' . $filterCategory : ''; ?><?php echo $filterBrand > 0 ? '&brand=' . $filterBrand : ''; ?><?php echo !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; ?>">Next</a>
                <?php else: ?>
                    <span class="disabled">Next</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Product Detail Modal -->
    <div id="productModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalProductTitle">Product Details</h2>
                <button class="modal-close" onclick="closeProductModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-image-section">
                    <img id="modalProductImage" src="" alt="Product Image" class="modal-product-image" 
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Crect fill=\'%23f8f9fa\' width=\'400\' height=\'400\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'16\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3ENo Image Available%3C/text%3E%3C/svg%3E'">
                </div>
                <div class="modal-details-section">
                    <div class="modal-product-id" id="modalProductId"></div>
                    <h3 class="modal-product-title" id="modalProductTitleText"></h3>
                    <div class="modal-product-price" id="modalProductPrice"></div>
                    <p class="modal-product-desc" id="modalProductDesc"></p>
                    
                    <div class="modal-product-meta">
                        <div class="modal-meta-item">
                            <span class="modal-meta-label">Category:</span>
                            <span class="modal-meta-value" id="modalProductCategory"></span>
                        </div>
                        <div class="modal-meta-item">
                            <span class="modal-meta-label">Brand:</span>
                            <span class="modal-meta-value" id="modalProductBrand"></span>
                        </div>
                        <div class="modal-meta-item">
                            <span class="modal-meta-label">Product ID:</span>
                            <span class="modal-meta-value" id="modalProductIdValue"></span>
                        </div>
                    </div>
                    
                    <div>
                        <strong style="color: #333; display: block; margin-bottom: 10px;">Keywords:</strong>
                        <div class="modal-keywords" id="modalProductKeywords"></div>
                    </div>
                    
                    <div class="modal-actions">
                        <button class="modal-add-to-cart" id="modalAddToCartBtn" onclick="addToCartFromModal()">
                            🛒 Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/all_products.js"></script>
</body>
</html>
