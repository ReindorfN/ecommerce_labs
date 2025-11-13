<?php 
require_once "../settings/db_model.php";

class Cart extends db_connection{
    private $cart_id;
    private $user_id;
    private $product_id;
    private $quantity;
    private $price;
    private $total;


    public function __construct($cart_id = null){
        parent::db_connect();
        if($cart_id){
            $this->cart_id = $cart_id;
        }
    }


    //getter and setter functions

    // 1. Add items to cart
    // $customer_id is the customer ID for logged-in users (NULL for guests)
    public function addToCart($p_id, $ip_add, $customer_id = null, $qty = 1) {
        if ($this->db_connect()) {
            // If the item is already in the cart for this customer/ip, update qty
            if ($this->isProductInCart($p_id, $ip_add, $customer_id)) {
                return $this->updateCart($p_id, $ip_add, $customer_id, $qty, true);
            }
            // Insert with customer_id for logged-in users, NULL for guests
            $stmt = $this->db->prepare("INSERT INTO cart (p_id, ip_add, customer_id, qty) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isii", $p_id, $ip_add, $customer_id, $qty);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    // 2. Remove items from cart
    // $customer_id is the customer ID for logged-in users (NULL for guests)
    public function removeFromCart($p_id, $ip_add, $customer_id = null) {
        if ($this->db_connect()) {
            // For logged-in users: match by customer_id ONLY (not ip_add to avoid cross-user contamination)
            // For guests: match by ip_add only where customer_id IS NULL
            if ($customer_id !== null) {
                $stmt = $this->db->prepare("DELETE FROM cart WHERE p_id = ? AND customer_id = ?");
                $stmt->bind_param("ii", $p_id, $customer_id);
            } else {
                $stmt = $this->db->prepare("DELETE FROM cart WHERE p_id = ? AND ip_add = ? AND customer_id IS NULL");
                $stmt->bind_param("is", $p_id, $ip_add);
            }
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    // 3. Update cart (update qty). $increment = true will add to existing qty, else replace qty
    // $customer_id is the customer ID for logged-in users (NULL for guests)
    public function updateCart($p_id, $ip_add, $customer_id = null, $qty = 1, $increment = false) {
        if ($this->db_connect()) {
            if ($increment) {
                // Increment quantity
                if ($customer_id !== null) {
                    // For logged-in users: use customer_id ONLY
                    $stmt = $this->db->prepare("UPDATE cart SET qty = qty + ? WHERE p_id = ? AND customer_id = ?");
                    $stmt->bind_param("iii", $qty, $p_id, $customer_id);
                } else {
                    // For guests: use ip_add only where customer_id IS NULL
                    $stmt = $this->db->prepare("UPDATE cart SET qty = qty + ? WHERE p_id = ? AND ip_add = ? AND customer_id IS NULL");
                    $stmt->bind_param("iis", $qty, $p_id, $ip_add);
                }
            } else {
                // Set quantity
                if ($customer_id !== null) {
                    // For logged-in users: use customer_id ONLY
                    $stmt = $this->db->prepare("UPDATE cart SET qty = ? WHERE p_id = ? AND customer_id = ?");
                    $stmt->bind_param("iii", $qty, $p_id, $customer_id);
                } else {
                    // For guests: use ip_add only where customer_id IS NULL
                    $stmt = $this->db->prepare("UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ? AND customer_id IS NULL");
                    $stmt->bind_param("iis", $qty, $p_id, $ip_add);
                }
            }
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    // 4. Empty cart (all items by ip or customer id)
    // $customer_id is the customer ID for logged-in users (NULL for guests)
    public function emptyCart($ip_add, $customer_id = null) {
        if ($this->db_connect()) {
            if ($customer_id !== null) {
                // For logged-in users: use customer_id ONLY
                $stmt = $this->db->prepare("DELETE FROM cart WHERE customer_id = ?");
                $stmt->bind_param("i", $customer_id);
            } else {
                // For guests: use ip_add only where customer_id IS NULL
                $stmt = $this->db->prepare("DELETE FROM cart WHERE ip_add = ? AND customer_id IS NULL");
                $stmt->bind_param("s", $ip_add);
            }
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }

    // 5. Check if product already exists in cart for an ip/customer_id
    // $customer_id is the customer ID for logged-in users (NULL for guests)
    public function isProductInCart($p_id, $ip_add, $customer_id = null) {
        if ($this->db_connect()) {
            if ($customer_id !== null) {
                // For logged-in users: use customer_id ONLY
                $stmt = $this->db->prepare("SELECT * FROM cart WHERE p_id = ? AND customer_id = ?");
                $stmt->bind_param("ii", $p_id, $customer_id);
            } else {
                // For guests: use ip_add only where customer_id IS NULL
                $stmt = $this->db->prepare("SELECT * FROM cart WHERE p_id = ? AND ip_add = ? AND customer_id IS NULL");
                $stmt->bind_param("is", $p_id, $ip_add);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $exists = $result && $result->num_rows > 0;
            $stmt->close();
            return $exists;
        }
        return false;
    }

    // 6. Retrieve all items in the cart for an ip/customer_id
    // $customer_id is the customer ID for logged-in users (NULL for guests)
    public function getCartItems($ip_add, $customer_id = null) {
        $items = [];
        if ($this->db_connect()) {
            if ($customer_id !== null) {
                // For logged-in users: get items by customer_id ONLY (not ip_add to avoid cross-user contamination)
                $stmt = $this->db->prepare("SELECT * FROM cart WHERE customer_id = ?");
                $stmt->bind_param("i", $customer_id);
            } else {
                // For guests: get items by ip_add only where customer_id IS NULL
                $stmt = $this->db->prepare("SELECT * FROM cart WHERE ip_add = ? AND customer_id IS NULL");
                $stmt->bind_param("s", $ip_add);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $items[] = $row;
                }
            }
            $stmt->close();
        }
        return $items;
    }

}


?>