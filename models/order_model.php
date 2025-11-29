<?php 
require_once "../settings/db_model.php";

class Order extends db_connection{
    private $order_id;
    private $customer_id;
    private $invoice_no;
    private $order_date;
    private $order_status;

    public function __construct($order_id = null){
        parent::db_connect();
        if($order_id){
            $this->order_id = $order_id;
        }
    }

    // Create a new order in the orders table and return its unique ID
    // Expected parameters: customer_id, invoice_no (integer)
    public function createOrder($customer_id, $invoice_no) {
        if ($this->db_connect()) {
            $order_status = 'pending'; // Default status
            // Using CURDATE() for date field (not datetime)
            $stmt = $this->db->prepare("INSERT INTO orders (customer_id, invoice_no, order_date, order_status) VALUES (?, ?, CURDATE(), ?)");
            $stmt->bind_param("iis", $customer_id, $invoice_no, $order_status);
            
            if ($stmt->execute()) {
                $order_id = $this->db->insert_id;
                $stmt->close();
                return $order_id;
            }
            $stmt->close();
        }
        return false;
    }

    // Add order details (product ID, quantity) to the orderdetails table
    // Note: No price field in orderdetails table - price is retrieved from products table
    public function addOrderDetails($order_id, $product_id, $quantity) {
        if ($this->db_connect()) {
            $stmt = $this->db->prepare("INSERT INTO orderdetails (order_id, product_id, qty) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $order_id, $product_id, $quantity);
            
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    // Record simulated payment entries in the payment table
    // Expected parameters: order_id, amount, customer_id, currency
    public function recordPayment($order_id, $amount, $customer_id, $currency = 'GHS') {
        if ($this->db_connect()) {
            // Table name is 'payment' not 'payments'
            // Fields: pay_id (auto), amt, customer_id, order_id, currency, payment_date
            // Using CURDATE() for date field (not datetime)
            $stmt = $this->db->prepare("INSERT INTO payment (order_id, amt, customer_id, currency, payment_date) VALUES (?, ?, ?, ?, CURDATE())");
            $stmt->bind_param("idss", $order_id, $amount, $customer_id, $currency);
            
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                // Update order status to 'completed' after successful payment
                $this->updateOrderStatus($order_id, 'completed');
            }
            
            return $result;
        }
        return false;
    }

    // Update order status
    private function updateOrderStatus($order_id, $status) {
        if ($this->db_connect()) {
            $stmt = $this->db->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
            $stmt->bind_param("si", $status, $order_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    // Retrieve past orders for a user
    public function getPastOrders($customer_id) {
        $orders = [];
        if ($this->db_connect()) {
            $stmt = $this->db->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $orders[] = $row;
                }
            }
            $stmt->close();
        }
        return $orders;
    }

    // Get order by ID
    public function getOrderById($order_id) {
        if ($this->db_connect()) {
            $stmt = $this->db->prepare("SELECT * FROM orders WHERE order_id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $order = $result->fetch_assoc();
                $stmt->close();
                return $order;
            }
            $stmt->close();
        }
        return null;
    }

    // Get order details for a specific order
    public function getOrderDetails($order_id) {
        $orderDetails = [];
        if ($this->db_connect()) {
            $stmt = $this->db->prepare("SELECT * FROM orderdetails WHERE order_id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $orderDetails[] = $row;
                }
            }
            $stmt->close();
        }
        return $orderDetails;
    }

    // Get payment for a specific order
    public function getOrderPayment($order_id) {
        if ($this->db_connect()) {
            // Table name is 'payment' not 'payments'
            $stmt = $this->db->prepare("SELECT * FROM payment WHERE order_id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $payment = $result->fetch_assoc();
                $stmt->close();
                return $payment;
            }
            $stmt->close();
        }
        return null;
    }

    // Generate unique invoice number as integer
    // Format: YYMMDD + random 3 digits (e.g., 251113001)
    // This ensures we stay within INT(11) limit (max 2,147,483,647)
    public function generateInvoiceNo() {
        $datePart = date('ymd'); // Use 2-digit year (e.g., 25 for 2025)
        $randomPart = rand(100, 999); // 3 random digits (keeps total under INT limit)
        return intval($datePart . $randomPart);
    }
}

?>

