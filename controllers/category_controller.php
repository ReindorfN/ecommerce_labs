<?php

require_once '../models/category_model.php';

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new Category();
    }

    // Handle adding a new category
    public function add_category_ctr($category_name) {
        // Validate input
        if (empty(trim($category_name))) {
            return ['success' => false, 'message' => 'Please enter a category name.'];
        }

        if (strlen(trim($category_name)) < 2) {
            return ['success' => false, 'message' => 'Category name must be at least 2 characters long.'];
        }

        if (strlen(trim($category_name)) > 100) {
            return ['success' => false, 'message' => 'Category name must be less than 100 characters.'];
        }

        // Sanitize input
        $category_name = trim($category_name);
        
        // Use model to add category
        return $this->categoryModel->addCategory($category_name);
    }

    // Handle fetching all categories
    public function fetch_categories_ctr() {
        return $this->categoryModel->getAllCategories();
    }

    // Handle updating a category
    public function update_category_ctr($cat_id, $category_name) {
        // Validate input
        if (empty(trim($category_name))) {
            return ['success' => false, 'message' => 'Please enter a category name.'];
        }

        if (strlen(trim($category_name)) < 2) {
            return ['success' => false, 'message' => 'Category name must be at least 2 characters long.'];
        }

        if (strlen(trim($category_name)) > 100) {
            return ['success' => false, 'message' => 'Category name must be less than 100 characters.'];
        }

        if (!is_numeric($cat_id) || $cat_id <= 0) {
            return ['success' => false, 'message' => 'Invalid category ID.'];
        }

        // Sanitize input
        $category_name = trim($category_name);
        
        // Use model to update category
        return $this->categoryModel->updateCategory($cat_id, $category_name);
    }

    // Handle deleting a category
    public function delete_category_ctr($cat_id) {
        if (!is_numeric($cat_id) || $cat_id <= 0) {
            return ['success' => false, 'message' => 'Invalid category ID.'];
        }

        // Use model to delete category
        return $this->categoryModel->deleteCategory($cat_id);
    }

    // Get category by ID
    public function get_category_by_id_ctr($cat_id) {
        if (!is_numeric($cat_id) || $cat_id <= 0) {
            return null;
        }

        return $this->categoryModel->getCategoryById($cat_id);
    }
}

?>
