<?php
require_once "../settings/db_model.php";

class Product extends db_connection{
    private $product_id;
    private $product_cat;
    private $product_brand;
    private $product_title;
    private $product_price;
    private $product_desc;
    private $product_image;
    private $product_keywords;


    public function __construct($product_id = null){
        parent::db_connect();
        if($product_id){
            $this-> product_id = $product_id;
        }
    }

    //Getter and setter functions
    public function getProductId(){
        return $this->product_id;
    }

    public function setProductId($product_id){
        $this->product_id = $product_id;
    }

    public function getProductBrand(){
        return $this->product_brand;
    }

    public function getProductCategory(){
        return $this->product_cat;
    }

    //fetching all products
    public function getAllProducts(){
        $products =[];

        if($this->db_connect()){
            $stmt = $this->db->prepare("SELECT * FROM products ORDER BY product_title ASC");
            $stmt->execute();
            $result = $stmt->get_result();

            if($result){
                while($row = $result->fetch_assoc()){
                    $products[] = $row;
                }
            }

            $stmt ->close();
        }

        return $products;
    }

    //Adding new product

    public function addProduct($product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords) {
        if ($this->db_connect()) {
            // Check if product with the exact same title already exists (case-sensitive)
            $check_query = "SELECT product_id FROM products WHERE product_title = ?";
            $stmt = $this->db->prepare($check_query);
            $stmt->bind_param("s", $product_title);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) {
                $stmt->close();
                
                // Insert new product
                $insert_query = "INSERT INTO products 
                    (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($insert_query);
                $stmt->bind_param(
                    "iisisss", 
                    $product_cat, 
                    $product_brand, 
                    $product_title, 
                    $product_price, 
                    $product_desc, 
                    $product_image, 
                    $product_keywords
                );
    
                if ($stmt->execute()) {
                    $stmt->close();
                    return ['success' => true, 'message' => "Product '$product_title' added successfully!"];
                } else {
                    $stmt->close();
                    return ['success' => false, 'message' => "Error adding product. Please try again."];
                }
            } else {
                $stmt->close();
                return ['success' => false, 'message' => "Product '$product_title' already exists!"];
            }
        } else {
            return ['success' => false, 'message' => "Database connection failed."];
        }
    }


    //Getting product by id
    public function getProductById($product_id)
    {
        if ($this->db_connect()) {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE product_id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $product = $result->fetch_assoc();
                $stmt->close();
                return $product;
            }
            $stmt->close();
        }
        return null;
    }

    //Updating products
    public function updateProduct($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords)
    {
        if ($this->db_connect()) {
            // Check if a product with the same details already exists (excluding current product)
            $check_query = "SELECT product_id FROM products 
                WHERE product_cat = ? 
                AND product_brand = ? 
                AND product_title = ? 
                AND product_price = ? 
                AND product_desc = ? 
                AND product_image = ? 
                AND product_keywords = ?
                AND product_id != ?";
            $stmt = $this->db->prepare($check_query);
            $stmt->bind_param(
                "iisisssi",
                $product_cat,
                $product_brand,
                $product_title,
                $product_price,
                $product_desc,
                $product_image,
                $product_keywords,
                $product_id
            );
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                // Update the product details
                $update_query = "UPDATE products SET 
                    product_cat = ?, 
                    product_brand = ?, 
                    product_title = ?, 
                    product_price = ?, 
                    product_desc = ?, 
                    product_image = ?, 
                    product_keywords = ?
                    WHERE product_id = ?";
                $stmt = $this->db->prepare($update_query);
                $stmt->bind_param(
                    "iisisssi",
                    $product_cat,
                    $product_brand,
                    $product_title,
                    $product_price,
                    $product_desc,
                    $product_image,
                    $product_keywords,
                    $product_id
                );

                if ($stmt->execute()) {
                    $stmt->close();
                    return ['success' => true, 'message' => "Product updated successfully!"];
                } else {
                    $stmt->close();
                    return ['success' => false, 'message' => "Error updating product. Please try again."];
                }
            } else {
                $stmt->close();
                return ['success' => false, 'message' => "Product '$product_title' already exists!"];
            }
        } else {
            return ['success' => false, 'message' => "Database connection failed."];
        }
    }

    //deleting a product
    public function deleteProduct($product_id)
    {
        if ($this->db_connect()) {
            $stmt = $this->db->prepare("DELETE FROM products WHERE product_id = ?");
            $stmt->bind_param("i", $product_id);

            if ($stmt->execute()) {
                $stmt->close();
                return ['success' => true, 'message' => "Product deleted successfully!"];
            } else {
                $stmt->close();
                return ['success' => false, 'message' => "Error deleting product. Please try again."];
            }
        } else {
            return ['success' => false, 'message' => "Database connection failed."];
        }
    }

    //View all products
    public function view_all_products()
    {
        $products = [];

        if ($this->db_connect()) {
            $stmt = $this->db->prepare("SELECT * FROM products ORDER BY product_id DESC");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
            }

            $stmt->close();
        }

        return $products;
    }

    //Search products by query (searches in title, description, and keywords)
    public function search_products($query)
    {
        $products = [];

        if ($this->db_connect()) {
            // Sanitize the search query
            $searchQuery = '%' . trim($query) . '%';
            
            $stmt = $this->db->prepare("SELECT * FROM products 
                WHERE product_title LIKE ? 
                OR product_desc LIKE ? 
                OR product_keywords LIKE ?
                ORDER BY product_title ASC");
            $stmt->bind_param("sss", $searchQuery, $searchQuery, $searchQuery);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
            }

            $stmt->close();
        }

        return $products;
    }

    //Filter products by category
    public function filter_products_by_category($cat_id)
    {
        $products = [];

        if ($this->db_connect()) {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE product_cat = ? ORDER BY product_title ASC");
            $stmt->bind_param("i", $cat_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
            }

            $stmt->close();
        }

        return $products;
    }

    //Filter products by brand
    public function filter_products_by_brand($brand_id)
    {
        $products = [];

        if ($this->db_connect()) {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE product_brand = ? ORDER BY product_title ASC");
            $stmt->bind_param("i", $brand_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
            }

            $stmt->close();
        }

        return $products;
    }

    //View single product by ID
    public function view_single_product($id)
    {
        if ($this->db_connect()) {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE product_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $product = $result->fetch_assoc();
                $stmt->close();
                return $product;
            }
            $stmt->close();
        }
        return null;
    }
}

?>