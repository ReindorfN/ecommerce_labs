<?php
require_once '../models/customer_model.php';

function register_user_ctr($name, $email, $password, $country, $city, $phone_number, $role)
{
    $user = new User();
    $user_id = $user->createUser($name, $email, $password, $country, $city,$phone_number, $role);
    if ($user_id) {
        return $user_id;
    }
    return false;
}

function login_user_ctr($email, $password){
    $user = new User();
    $user_data = $user->loginUser($email, $password);
    
    if ($user_data) {
        return $user_data;
    }
    return false;
}

?>