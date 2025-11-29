<?php
require_once '../settings/core.php';
require_once '../controllers/order_controller.php';
require_once '../controllers/cart_controller.php';
require_once '../models/paystack_handler.php';
require_once '../functions/fetchCartItems_action.php';

// Check if order_id is provided
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header("Location: ../views/checkout.php?status=error&message=" . urlencode("Invalid order reference."));
    exit;
}

$order_id = intval($_GET['order_id']);

// Check if reference is provided (Paystack sends this)
$reference = isset($_GET['reference']) ? trim($_GET['reference']) : '';

try {
    $orderController = new OrderController();
    $cartController = new CartController();
    $paystackHandler = new PaystackHandler();

    // Get order details
    $order = $orderController->get_order_by_id_ctr($order_id);

    if (!$order) {
        header("Location: ../views/checkout.php?status=error&message=" . urlencode("Order not found."));
        exit;
    }

    // If reference is provided, verify the transaction
    if (!empty($reference)) {
        // Verify the transaction with Paystack
        $verificationResponse = $paystackHandler->verifyTransaction($reference);

        if ($verificationResponse['status'] && isset($verificationResponse['data'])) {
            $transactionData = $verificationResponse['data'];

            // Log transaction data for debugging
            error_log("Paystack Transaction Data - Order ID: $order_id, Status: " . ($transactionData['status'] ?? 'N/A') . ", Gateway Response: " . ($transactionData['gateway_response'] ?? 'N/A'));

            // Check if transaction was successful
            $isSuccessful = (isset($transactionData['status']) && $transactionData['status'] === 'success');

            if ($isSuccessful) {
                
                // Get customer ID
                $customer_id = $order['customer_id'];
                
                // Get payment amount (convert from pesewas to cedis)
                $amount_paid = $transactionData['amount'] / 100;
                $currency = $transactionData['currency'] ?? 'GHS';

                // Check if payment already recorded for this order
                $existingPayment = $orderController->get_order_payment_ctr($order_id);

                if (!$existingPayment) {
                    // Record payment
                    $paymentParams = [
                        'order_id' => $order_id,
                        'amount' => $amount_paid,
                        'customer_id' => $customer_id,
                        'currency' => $currency
                    ];

                    error_log("Attempting to record payment - Order ID: $order_id, Amount: $amount_paid, Customer ID: $customer_id");

                    $paymentResult = $orderController->record_payment_ctr($paymentParams);

                    if ($paymentResult['success']) {
                        error_log("Payment recorded successfully - Order ID: $order_id");
                        
                        // Empty the cart after successful payment
                        $ip_address = getClientIp();
                        $cartController->empty_cart_ctr($ip_address, $customer_id);

                        // Clear session variables
                        unset($_SESSION['paystack_reference']);
                        unset($_SESSION['pending_order_id']);

                        // Redirect to success page
                        header("Location: ../views/payment_success.php?order_id=" . $order_id . "&reference=" . urlencode($reference));
                        exit;
                    } else {
                        // Payment recording failed - log the error message
                        $errorMsg = isset($paymentResult['message']) ? $paymentResult['message'] : 'Unknown error';
                        error_log("Paystack payment recording failed - Order ID: $order_id, Error: $errorMsg");
                        error_log("Payment params: " . print_r($paymentParams, true));
                        
                        // Payment recording failed
                        header("Location: ../views/payment_failed.php?order_id=" . $order_id . "&message=" . urlencode("Payment verified but failed to update order: " . $errorMsg));
                        exit;
                    }
                } else {
                    // Payment already recorded - redirect to success
                    error_log("Payment already exists for Order ID: $order_id");
                    header("Location: ../views/payment_success.php?order_id=" . $order_id . "&reference=" . urlencode($reference));
                    exit;
                }
            } else {
                // Transaction not successful
                $statusMsg = "Status: " . ($transactionData['status'] ?? 'N/A') . ", Gateway: " . ($transactionData['gateway_response'] ?? 'N/A');
                error_log("Paystack transaction failed - Order ID: $order_id, $statusMsg");
                header("Location: ../views/payment_failed.php?order_id=" . $order_id . "&message=" . urlencode("Payment was not successful. Status: " . ($transactionData['status'] ?? 'unknown') . ", Response: " . ($transactionData['gateway_response'] ?? 'unknown')));
                exit;
            }
        } else {
            // Verification failed
            header("Location: ../views/payment_failed.php?order_id=" . $order_id . "&message=" . urlencode("Failed to verify payment: " . ($verificationResponse['message'] ?? 'Unknown error')));
            exit;
        }
    } else {
        // No reference provided - user might have cancelled or there's an issue
        // Check if order is already completed
        if ($order['order_status'] === 'completed') {
            header("Location: ../views/payment_success.php?order_id=" . $order_id);
            exit;
        } else {
            header("Location: ../views/payment_failed.php?order_id=" . $order_id . "&message=" . urlencode("Payment was cancelled or incomplete."));
            exit;
        }
    }

} catch (Exception $e) {
    header("Location: ../views/payment_failed.php?order_id=" . $order_id . "&message=" . urlencode("An error occurred: " . $e->getMessage()));
    exit;
}
?>

