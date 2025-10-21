<?php
require_once '../settings/core.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login/login.php");
    exit;
}

// Redirect to the unified management interface
header("Location: category.php");
exit;
?>