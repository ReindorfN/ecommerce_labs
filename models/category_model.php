<?php

require_once '../settings/db_model.php';

class Category extends db_connection
{
    private $cat_id;
    private $cat_name;

    public function __construct($cat_id = null)
    {
        parent::db_connect();
        if ($cat_id) {
            $this->cat_id = $cat_id;
        }
    }

    // Getter and setter methods
    public function getCatId()
    {
        return $this->cat_id;
    }

    public function setCatId($cat_id)
    {
        $this->cat_id = $cat_id;
    }

    public function getCatName()
    {
        return $this->cat_name;
    }

    public function setCatName($cat_name)
    {
        $this->cat_name = $cat_name;
    }

    // Get all categories
    public function getAllCategories()
    {
        $categories = [];

        if ($this->db_connect()) {
            $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY cat_name ASC");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $categories[] = $row;
                }
            }
            $stmt->close();
        }

        return $categories;
    }

    // Add new category
    public function addCategory($category_name){
        if ($this->db_connect()) {
            // Check if category already exists (case insensitive)
            $check_query = "SELECT cat_id FROM categories WHERE LOWER(cat_name) = ?";
            $stmt = $this->db->prepare($check_query);
            $category_name_lower = strtolower($category_name);
            $stmt->bind_param("s", $category_name_lower);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                // Insert new category
                $insert_query = "INSERT INTO categories (cat_name) VALUES (?)";
                $stmt = $this->db->prepare($insert_query);
                $stmt->bind_param("s", $category_name);

                if ($stmt->execute()) {
                    $stmt->close();
                    return ['success' => true, 'message' => "Category '$category_name' added successfully!"];
                } else {
                    $stmt->close();
                    return ['success' => false, 'message' => "Error adding category. Please try again."];
                }
            } else {
                $stmt->close();
                return ['success' => false, 'message' => "Category '$category_name' already exists!"];
            }
        } else {
            return ['success' => false, 'message' => "Database connection failed."];
        }
    }

    // Get category by ID
    public function getCategoryById($cat_id)
    {
        if ($this->db_connect()) {
            $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_id = ?");
            $stmt->bind_param("i", $cat_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $category = $result->fetch_assoc();
                $stmt->close();
                return $category;
            }
            $stmt->close();
        }
        return null;
    }

    // Update category
    public function updateCategory($cat_id, $category_name)
    {
        if ($this->db_connect()) {
            // Check if category already exists (case insensitive) excluding current category
            $check_query = "SELECT cat_id FROM categories WHERE LOWER(cat_name) = ? AND cat_id != ?";
            $stmt = $this->db->prepare($check_query);
            $category_name_lower = strtolower($category_name);
            $stmt->bind_param("si", $category_name_lower, $cat_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                // Update category
                $update_query = "UPDATE categories SET cat_name = ? WHERE cat_id = ?";
                $stmt = $this->db->prepare($update_query);
                $stmt->bind_param("si", $category_name, $cat_id);

                if ($stmt->execute()) {
                    $stmt->close();
                    return ['success' => true, 'message' => "Category updated successfully!"];
                } else {
                    $stmt->close();
                    return ['success' => false, 'message' => "Error updating category. Please try again."];
                }
            } else {
                $stmt->close();
                return ['success' => false, 'message' => "Category '$category_name' already exists!"];
            }
        } else {
            return ['success' => false, 'message' => "Database connection failed."];
        }
    }

    // Delete category
    public function deleteCategory($cat_id)
    {
        if ($this->db_connect()) {
            $stmt = $this->db->prepare("DELETE FROM categories WHERE cat_id = ?");
            $stmt->bind_param("i", $cat_id);

            if ($stmt->execute()) {
                $stmt->close();
                return ['success' => true, 'message' => "Category deleted successfully!"];
            } else {
                $stmt->close();
                return ['success' => false, 'message' => "Error deleting category. Please try again."];
            }
        } else {
            return ['success' => false, 'message' => "Database connection failed."];
        }
    }
}

?>