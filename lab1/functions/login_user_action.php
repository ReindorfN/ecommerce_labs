<?php 

header('Content-Type: application/json');

session_start();

$response = array();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/customer_controller.php';

// Validate required fields
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    $response['status'] = 'error';
    $response['message'] = 'Email and password are required';
    echo json_encode($response);
    exit();
}

$email = trim($_POST['email']);
$password = $_POST['password'];

// Basic validation
if (empty($email) || empty($password)) {
    $response['status'] = 'error';
    $response['message'] = 'Email and password cannot be empty';
    echo json_encode($response);
    exit();
}

try {
    $user_data = login_user_ctr($email, $password);
    
    if ($user_data) {
        // Set session variables
        $_SESSION['user_id'] = $user_data['customer_id'];
        $_SESSION['user_name'] = $user_data['customer_name'];
        $_SESSION['user_email'] = $user_data['customer_email'];
        $_SESSION['user_role'] = $user_data['user_role'];
        $_SESSION['user_country'] = $user_data['customer_country'];
        $_SESSION['user_city'] = $user_data['customer_city'];
        $_SESSION['user_contact'] = $user_data['customer_contact'];
        $_SESSION['date_created'] = $user_data['date_created'];
        
        $response['status'] = 'success';
        $response['message'] = 'Login successful';
        $response['user_data'] = [
            'user_id' => $user_data['customer_id'],
            'name' => $user_data['customer_name'],
            'email' => $user_data['customer_email'],
            'role' => $user_data['user_role']
        ];
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid email or password';
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'Login failed: ' . $e->getMessage();
}

echo json_encode($response);

?>