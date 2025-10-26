<?php 
require_once "../settings/db_model.php";

class Brand extends db_connection{
    private $brand_id;
    private $brand_name;


    public function __construct($brand_id = null){
        parent::db_connect();
        if($brand_id){
            $this-> brand_id = $brand_id;
        }
    }

    //getter and setter functions

    public function getBrandId(){
        return $this->brand_id;
    }

    public function setBrandId($brand_id){
        $this->brand_id = $brand_id;
    }

    public function getCatName(){
        return $this->brand_name;
    }

    public function setBrandName($brand_name){
        $this->brand_name = $brand_name;
    }

    //fetching all brands
    public function getAllBrands(){
        $brands =[];

        if($this->db_connect()){
            $stmt = $this->db->prepare("SELECT * FROM brands ORDER BY brand_name ASC");
            $stmt->execute();
            $result = $stmt->get_result();

            if($result){
                while($row = $result->fetch_assoc()){
                    $brands[] = $row;
                }
            }

            $stmt ->close();
        }

        return $brands;
    }


    //adding new brands
    public function addBrand($brand_name){
        if($this->db_connect()){
            //Checking if brand already exists
            $check_query = "SELECT brand_id FROM brands WHERE LOWER(brand_name )=?";
            $stmt = $this->db->prepare($check_query);
            $brand_name_lower = strtolower($brand_name);
            $stmt->bind_param("s", $brand_name_lower);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows == 0){
                //inserting new brand
                $insert_query = "INSERT INTO brands (brand_name) VALUES (?)";
                $stmt = $this->db->prepare($insert_query);
                $stmt -> bind_param('s', $brand_name); 

                if($stmt->execute()){
                    $stmt->close();
                    return ['success'=>true, 'message'=> "Brand ' $brand_name 'addedd successfully"];
                } else {
                    $stmt -> close();
                    return [ 'success'=> false, 'message' => "Error adding brand '$brand_name '. Please try again!!!"];
                }
            } else {
                $stmt -> close();
                return ['succes'=> false, 'message'=> "Brand already exists!!"];
            }
        } else{
            return ['success'=> false, 'message'=> "Connection to database failed"];
        }
    }

    //getting brand by id
    public function getBrandById($brand_id)
    {
        if ($this->db_connect()) {
            $stmt = $this->db->prepare("SELECT * FROM brands WHERE brand_id = ?");
            $stmt->bind_param("i", $brand_id);
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


    //updating brand information
    public function updateBrand($brand_id, $brand_name)
    {
        if ($this->db_connect()) {
            // Check if brans already exists (case insensitive) excluding current category
            $check_query = "SELECT brand_id FROM brands WHERE LOWER(brand_name) = ? AND brand_id != ?";
            $stmt = $this->db->prepare($check_query);
            $brand_name_lower = strtolower($brand_name);
            $stmt->bind_param("si", $brand_name_lower, $brand_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                // Update category
                $update_query = "UPDATE brands SET brand_name = ? WHERE brand_id = ?";
                $stmt = $this->db->prepare($update_query);
                $stmt->bind_param("si", $brand_name, $brand_id);

                if ($stmt->execute()) {
                    $stmt->close();
                    return ['success' => true, 'message' => "Brand updated successfully!"];
                } else {
                    $stmt->close();
                    return ['success' => false, 'message' => "Error updating brand. Please try again."];
                }
            } else {
                $stmt->close();
                return ['success' => false, 'message' => "Brand '$brand_name' already exists!"];
            }
        } else {
            return ['success' => false, 'message' => "Database connection failed."];
        }
    }

    // Deleting a brand
    public function deleteBrand($brand_id)
    {
        if ($this->db_connect()) {
            $stmt = $this->db->prepare("DELETE FROM brands WHERE brand_id = ?");
            $stmt->bind_param("i", $brand_id);

            if ($stmt->execute()) {
                $stmt->close();
                return ['success' => true, 'message' => "Brand deleted successfully!"];
            } else {
                $stmt->close();
                return ['success' => false, 'message' => "Error deleting brand. Please try again."];
            }
        } else {
            return ['success' => false, 'message' => "Database connection failed."];
        }
    }
}


?>