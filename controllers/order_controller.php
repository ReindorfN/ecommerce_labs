<?php

require_once "../models/order_model.php";

class OrderController {
    private $order_model;

    public function __construct() {
        $this->order_model = new Order();
    }

    // Create order controller method
    public function create_order_ctr($params) {
        // Validate required parameters
        if (!isset($params['customer_id']) || !is_numeric($params['customer_id'])) {
            return ['success' => false, 'message' => 'Invalid customer ID.'];
        }

        // Generate invoice number if not provided (must be integer)
        $invoice_no = isset($params['invoice_no']) && is_numeric($params['invoice_no']) 
            ? intval($params['invoice_no']) 
            : $this->order_model->generateInvoiceNo();

        // Sanitize inputs
        $customer_id = intval($params['customer_id']);

        try {
            $order_id = $this->order_model->createOrder($customer_id, $invoice_no);
            
            if ($order_id) {
                return ['success' => true, 'order_id' => $order_id, 'invoice_no' => $invoice_no, 'message' => 'Order created successfully.'];
            } else {
                return ['success' => false, 'message' => 'Failed to create order.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error creating order: ' . $e->getMessage()];
        }
    }

    // Add order details controller method
    // Note: No price field in orderdetails table - price is retrieved from products table
    public function add_order_details_ctr($params) {
        // Validate required parameters
        if (!isset($params['order_id']) || !is_numeric($params['order_id'])) {
            return ['success' => false, 'message' => 'Invalid order ID.'];
        }

        if (!isset($params['product_id']) || !is_numeric($params['product_id'])) {
            return ['success' => false, 'message' => 'Invalid product ID.'];
        }

        if (!isset($params['quantity']) || !is_numeric($params['quantity']) || $params['quantity'] <= 0) {
            return ['success' => false, 'message' => 'Invalid quantity.'];
        }

        // Sanitize inputs
        $order_id = intval($params['order_id']);
        $product_id = intval($params['product_id']);
        $quantity = intval($params['quantity']);

        try {
            $result = $this->order_model->addOrderDetails($order_id, $product_id, $quantity);
            
            if ($result) {
                return ['success' => true, 'message' => 'Order detail added successfully.'];
            } else {
                return ['success' => false, 'message' => 'Failed to add order detail.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error adding order detail: ' . $e->getMessage()];
        }
    }

    // Record payment controller method
    // Note: payment table requires customer_id and currency fields
    public function record_payment_ctr($params) {
        // Validate required parameters
        if (!isset($params['order_id']) || !is_numeric($params['order_id'])) {
            return ['success' => false, 'message' => 'Invalid order ID.'];
        }

        if (!isset($params['amount']) || !is_numeric($params['amount']) || $params['amount'] <= 0) {
            return ['success' => false, 'message' => 'Invalid payment amount.'];
        }

        if (!isset($params['customer_id']) || !is_numeric($params['customer_id'])) {
            return ['success' => false, 'message' => 'Invalid customer ID.'];
        }

        // Sanitize inputs
        $order_id = intval($params['order_id']);
        $amount = floatval($params['amount']);
        $customer_id = intval($params['customer_id']);
        $currency = isset($params['currency']) ? trim($params['currency']) : 'GHS';

        try {
            $result = $this->order_model->recordPayment($order_id, $amount, $customer_id, $currency);
            
            if ($result) {
                return ['success' => true, 'message' => 'Payment recorded successfully.'];
            } else {
                return ['success' => false, 'message' => 'Failed to record payment.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error recording payment: ' . $e->getMessage()];
        }
    }

    // Get past orders for a user
    public function get_past_orders_ctr($customer_id) {
        if (!is_numeric($customer_id) || $customer_id <= 0) {
            return [];
        }

        $customer_id = intval($customer_id);

        try {
            return $this->order_model->getPastOrders($customer_id);
        } catch (Exception $e) {
            return [];
        }
    }

    // Get order by ID
    public function get_order_by_id_ctr($order_id) {
        if (!is_numeric($order_id) || $order_id <= 0) {
            return null;
        }

        $order_id = intval($order_id);

        try {
            return $this->order_model->getOrderById($order_id);
        } catch (Exception $e) {
            return null;
        }
    }

    // Get order details for a specific order
    public function get_order_details_ctr($order_id) {
        if (!is_numeric($order_id) || $order_id <= 0) {
            return [];
        }

        $order_id = intval($order_id);

        try {
            return $this->order_model->getOrderDetails($order_id);
        } catch (Exception $e) {
            return [];
        }
    }

    // Get payment for a specific order
    public function get_order_payment_ctr($order_id) {
        if (!is_numeric($order_id) || $order_id <= 0) {
            return null;
        }

        $order_id = intval($order_id);

        try {
            return $this->order_model->getOrderPayment($order_id);
        } catch (Exception $e) {
            return null;
        }
    }
}

?>

