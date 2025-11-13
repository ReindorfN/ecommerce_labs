<?php

require_once "../models/cart_model.php";

class CartController {
    private $cart_model;

    public function __construct() {
        $this->cart_model = new Cart();
    }

    // $customer_id is the customer ID for logged-in users (NULL for guests)
    public function add_to_cart_ctr($product_id, $ip_address, $customer_id = null, $quantity = 1) {
        return $this->cart_model->addToCart($product_id, $ip_address, $customer_id, $quantity);
    }

    // $customer_id is the customer ID for logged-in users (NULL for guests)
    public function remove_from_cart_ctr($product_id, $ip_address, $customer_id = null) {
        return $this->cart_model->removeFromCart($product_id, $ip_address, $customer_id);
    }

    // $customer_id is the customer ID for logged-in users (NULL for guests)
    public function update_cart_ctr($product_id, $ip_address, $customer_id = null, $quantity = null, $increment = false) {
        return $this->cart_model->updateCart($product_id, $ip_address, $customer_id, $quantity, $increment);
    }

    // $customer_id is the customer ID for logged-in users (NULL for guests)
    public function empty_cart_ctr($ip_address, $customer_id = null) {
        return $this->cart_model->emptyCart($ip_address, $customer_id);
    }

    // $customer_id is the customer ID for logged-in users (NULL for guests)
    public function get_cart_items_ctr($ip_address, $customer_id = null) {
        return $this->cart_model->getCartItems($ip_address, $customer_id);
    }

    // $customer_id is the customer ID for logged-in users (NULL for guests)
    public function product_exists_in_cart_ctr($product_id, $ip_address, $customer_id = null) {
        return $this->cart_model->isProductInCart($product_id, $ip_address, $customer_id);
    }
}