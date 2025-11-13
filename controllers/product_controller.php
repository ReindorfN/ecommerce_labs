<?php
require_once "../models/product_model.php";

class ProductController{
    private $productModel;
    public function __construct() {
        $this->productModel = new Product();
    }

    // Handle adding a new product
    public function add_product_ctr(
        $product_cat,
        $product_brand,
        $product_title,
        $product_price,
        $product_desc,
        $product_image,
        $product_keywords
    ) {
        // Validate category and brand IDs
        if (!is_numeric($product_cat) || $product_cat <= 0) {
            return ['success' => false, 'message' => 'Invalid category selected.'];
        }
        if (!is_numeric($product_brand) || $product_brand <= 0) {
            return ['success' => false, 'message' => 'Invalid brand selected.'];
        }

        // Validate product title
        if (empty(trim($product_title))) {
            return ['success' => false, 'message' => 'Please enter a product title.'];
        }
        if (strlen($product_title) < 2) {
            return ['success' => false, 'message' => 'Product title must be at least 2 characters long.'];
        }
        if (strlen($product_title) > 100) {
            return ['success' => false, 'message' => 'Product title must be less than 100 characters.'];
        }

        // Validate price
        if (!is_numeric($product_price) || $product_price <= 0) {
            return ['success' => false, 'message' => 'Please enter a valid product price.'];
        }

        // Validate description
        if (empty(trim($product_desc))) {
            return ['success' => false, 'message' => 'Please enter a product description.'];
        }

        // Validate product keywords
        if (empty(trim($product_keywords))) {
            return ['success' => false, 'message' => 'Please enter product keywords.'];
        }

        // Validate image
        if (empty(trim($product_image))) {
            return ['success' => false, 'message' => 'Please provide a product image filename or path.'];
        }

        // Sanitize all fields
        $product_cat = intval($product_cat);
        $product_brand = intval($product_brand);
        $product_title = trim($product_title);
        $product_price = floatval($product_price);
        $product_desc = trim($product_desc);
        $product_image = trim($product_image);
        $product_keywords = trim($product_keywords);

        // Use the product model to add the product
        try {
            $result = $this->productModel->addProduct(
                $product_cat,
                $product_brand,
                $product_title,
                $product_price,
                $product_desc,
                $product_image,
                $product_keywords
            );
            return $result;
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Unexpected error: ' . $e->getMessage()];
        }
    }

    // Handling fetching all products
    public function fetch_products_ctr() {
        return $this->productModel->getAllProducts();
    }


    // Handling updates to products
    public function update_product_ctr($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords) {
        // Validate product ID
        if (!is_numeric($product_id) || $product_id <= 0) {
            return ['success' => false, 'message' => 'Invalid Product ID.'];
        }

        // Validate category and brand
        if (!is_numeric($product_cat) || $product_cat <= 0) {
            return ['success' => false, 'message' => 'Please select a valid product category.'];
        }
        if (!is_numeric($product_brand) || $product_brand <= 0) {
            return ['success' => false, 'message' => 'Please select a valid brand.'];
        }

        // Validate product title
        if (empty(trim($product_title))) {
            return ['success' => false, 'message' => 'Please enter a product title.'];
        }
        if (strlen(trim($product_title)) < 2) {
            return ['success' => false, 'message' => 'Product title must be at least 2 characters long.'];
        }

        // Validate product price
        if (!is_numeric($product_price) || floatval($product_price) <= 0) {
            return ['success' => false, 'message' => 'Please enter a valid product price.'];
        }

        // Validate description
        if (empty(trim($product_desc))) {
            return ['success' => false, 'message' => 'Please enter a product description.'];
        }

        // Validate product keywords
        if (empty(trim($product_keywords))) {
            return ['success' => false, 'message' => 'Please enter product keywords.'];
        }

        // Validate image
        if (empty(trim($product_image))) {
            return ['success' => false, 'message' => 'Please provide a product image filename or path.'];
        }

        // Sanitize all fields
        $product_cat = intval($product_cat);
        $product_brand = intval($product_brand);
        $product_title = trim($product_title);
        $product_price = floatval($product_price);
        $product_desc = trim($product_desc);
        $product_image = trim($product_image);
        $product_keywords = trim($product_keywords);

        // Use the product model to update the product
        try {
            $result = $this->productModel->updateProduct(
                $product_id,
                $product_cat,
                $product_brand,
                $product_title,
                $product_price,
                $product_desc,
                $product_image,
                $product_keywords
            );
            return $result;
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Unexpected error: ' . $e->getMessage()];
        }
    }

    // Handling deletion of products
    public function delete_product_ctr($product_id) {
        if (!is_numeric($product_id) || $product_id <= 0) {
            return ['success' => false, 'message' => 'Invalid product ID.'];
        }

        // Useing model to delete products
        return $this->productModel->deleteProduct($product_id);
    }


    // Getting products by ID
    public function get_product_by_id_ctr($product_id) {
        if (!is_numeric($product_id) || $product_id <= 0) {
            return null;
        }
        //Using the model to get product by ID
        return $this->productModel->getProductById($product_id);
    }

    // View all products controller method
    public function view_all_products_ctr() {
        try {
            return $this->productModel->view_all_products();
        } catch (Exception $e) {
            return [];
        }
    }

    // Search products controller method
    public function search_products_ctr($query) {
        // Validate search query
        if (empty(trim($query))) {
            return [];
        }

        // Sanitize the search query
        $query = trim($query);

        try {
            return $this->productModel->search_products($query);
        } catch (Exception $e) {
            return [];
        }
    }

    // Filter products by category controller method
    public function filter_products_by_category_ctr($cat_id) {
        // Validate category ID
        if (!is_numeric($cat_id) || $cat_id <= 0) {
            return [];
        }

        $cat_id = intval($cat_id);

        try {
            return $this->productModel->filter_products_by_category($cat_id);
        } catch (Exception $e) {
            return [];
        }
    }

    // Filter products by brand controller method
    public function filter_products_by_brand_ctr($brand_id) {
        // Validate brand ID
        if (!is_numeric($brand_id) || $brand_id <= 0) {
            return [];
        }

        $brand_id = intval($brand_id);

        try {
            return $this->productModel->filter_products_by_brand($brand_id);
        } catch (Exception $e) {
            return [];
        }
    }

    // View single product controller method
    public function view_single_product_ctr($id) {
        // Validate product ID
        if (!is_numeric($id) || $id <= 0) {
            return null;
        }

        $id = intval($id);

        try {
            return $this->productModel->view_single_product($id);
        } catch (Exception $e) {
            return null;
        }
    }

}
?>