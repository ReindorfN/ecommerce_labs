<?php

require_once '../settings/db_model.php';

class User extends db_connection{
    private $user_id;
    private $name;
    private $email;
    private $country;
    private $city;
    private $password;
    private $role;
    private $date_created;
    private $phone_number;


    public function __construct($user_id = null){
        parent::db_connect();
        if($user_id){
            $this -> user_id =$user_id;
            $this -> loadUser();
        }
    }


    private function loadUser($user_id = null){
        if($user_id){
            $this-> user_id = $user_id;
        }
        if(!$this->$user_id){
            return false;
        }

        $stmt = $this->db->prepare('SELECT * FROM customer WHERE customer_id = ?');
        $stmt -> bind_param('i', $this->user_id);
        $stmt -> execute();
        $results = $stmt->get_result()->fetch_assoc();
        if($results){
            $this->name = $results['customer_name'];
            $this->email = $results['customer_email'];
            $this->country = $results['customer_country'];
            $this->city = $results['customer_city'];
            $this->role = $results['role'];
            $this-> date_created = $results["date_created"];
            $this->phone_number = $results['customer_contact'];
        }
    }

    public function createUser($name, $email, $password, $country, $city, $phone_number, $role){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $name, $email, $hashed_password, $country, $city, $phone_number, $role);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function loginUser($email, $password){
        $stmt = $this->db->prepare("SELECT customer_id, customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result && password_verify($password, $result['customer_pass'])) {
            return $result;
        }
        return false;
    }

}
?>