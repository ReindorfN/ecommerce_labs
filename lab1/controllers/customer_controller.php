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

?>