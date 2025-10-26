<?php
require_once "../models/brand_model.php";

class BrandController{
    private $brandModel;

    public function __construct(){
        $this-> brandModel = new Brand();
    }

    //handling adding of new brands
    public function add_brand_ctr($brand_name){
        // Validating input
        if (empty(trim($brand_name))) {
            return ['success' => false, 'message' => 'Please enter a brand name.'];
        }

        if (strlen(trim($brand_name)) < 2) {
            return ['success' => false, 'message' => 'Brand name must be at least 2 characters long.'];
        }

        if (strlen(trim($brand_name)) > 100) {
            return ['success' => false, 'message' => 'Brand name must be less than 100 characters.'];
        }

        //Sanitizing the inputs
        $brand_name = trim($brand_name);

        //using the model to add a brand
        return $this->brandModel->addBrand($brand_name);
    }

    // Handling fetching al brands
    public function fetch_brands_ctr() {
        return $this->brandModel->getAllbrands();
    }


    // Handling updates to brands
    public function update_brand_ctr($brand_id, $brand_name) {
        // Validating input
        if (empty(trim($brand_name))) {
            return ['success' => false, 'message' => 'Please enter a brand name.'];
        }

        if (strlen(trim($brand_name)) < 2) {
            return ['success' => false, 'message' => 'Brand name must be at least 2 characters long.'];
        }

        if (strlen(trim($brand_name)) > 100) {
            return ['success' => false, 'message' => 'Brand name must be less than 100 characters.'];
        }

        if (!is_numeric($brand_id) || $brand_id <= 0) {
            return ['success' => false, 'message' => 'Invalid Brand ID.'];
        }

        // Sanitizing input
        $brand_name = trim($brand_name);
        
        // Useing model to update brand information
        return $this->brandModel->updateBrand($brand_id, $brand_name);
    }


    // Handling deletion of brands
    public function delete_brand_ctr($brand_id) {
        if (!is_numeric($brand_id) || $brand_id <= 0) {
            return ['success' => false, 'message' => 'Invalid brand ID.'];
        }

        // Useing model to delete brands
        return $this->brandModel->deleteBrand($brand_id);
    }

    // Getting brands by ID
    public function get_brand_by_id_ctr($brand_id) {
        if (!is_numeric($brand_id) || $brand_id <= 0) {
            return null;
        }

        return $this->brandModel->getBrandById($brand_id);
    }
}

?>