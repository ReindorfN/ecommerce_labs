<?php
// Paystack API Configuration
// Replace these with your actual Paystack test/live API keys

// Test Mode - Set to true for test API, false for live
if(!defined('PAYSTACK_TEST_MODE')){
    define('PAYSTACK_TEST_MODE', true);
}

// Paystack Public Key (for frontend)
if(!defined('PAYSTACK_PUBLIC_KEY')){
    // Replace with your Paystack test public key
    define('PAYSTACK_PUBLIC_KEY', 'pk_test_54fbf060481d331dd17f20bf33187428b03d67aa');
}

// Paystack Secret Key (for backend - keep this secure!)
if(!defined('PAYSTACK_SECRET_KEY')){
    // Replace with your Paystack test secret key
    define('PAYSTACK_SECRET_KEY', 'sk_test_da2809bf894241352ad7163982eaf4dbbd7d27bd');
}

// Paystack API Base URL (same for test and live)
if(!defined('PAYSTACK_API_URL')){
    define('PAYSTACK_API_URL', 'https://api.paystack.co');
}

// Payment Currency
if(!defined('PAYSTACK_CURRENCY')){
    define('PAYSTACK_CURRENCY', 'GHS'); // Ghana Cedis
}

// Callback URL (where Paystack redirects after payment)
// This is dynamically generated based on your server configuration
// You can override this by manually setting the URL below
if(!defined('PAYSTACK_CALLBACK_URL')){
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    // Get base path dynamically
    $script_path = $_SERVER['SCRIPT_NAME'];
    $base_path = str_replace('\\', '/', dirname(dirname($script_path)));
    if ($base_path === '/' || $base_path === '\\' || $base_path === '.') {
        $base_path = '';
    } else {
        $base_path = rtrim($base_path, '/');
    }
    // Manual override option - uncomment and set your full callback URL:
    // define('PAYSTACK_CALLBACK_URL', 'http://localhost/ecommerce_labs/functions/paystack_callback.php');
    define('PAYSTACK_CALLBACK_URL', $protocol . $host . $base_path . '/functions/paystack_callback.php');
}

?>

