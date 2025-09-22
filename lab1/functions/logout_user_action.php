<?php

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'You are not logged in.'
    ]);
    exit;
}

try {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy session cookie if it exists
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    // Destroy the session
    session_destroy();
    header('Location: ../login/login.php');
    
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Logout failed: ' . $e->getMessage()
    ]);
}

exit;

?>