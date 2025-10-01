<?php
session_start();


//for header redirection
ob_start();

//funtion to check for login
// if (!isset($_SESSION['id'])) {
//     header("Location: login/login.php");
//     exit;
// }


//function to get user ID
function isLoggedIn(){
    if(isset($_SESSION['user_id'])){
        return true;
    }
    else{
        return false;
    }
}

//function to check for role (admin, customer, etc)
function isAdmin() {
    if (isLoggedIn()){
        return $_SESSION['user_role'] == 2;
    }
}



?>