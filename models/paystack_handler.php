<?php
require_once "../settings/paystack_config.php";

class PaystackHandler {
    private $secretKey;
    private $publicKey;
    private $apiUrl;

    public function __construct() {
        $this->secretKey = PAYSTACK_SECRET_KEY;
        $this->publicKey = PAYSTACK_PUBLIC_KEY;
        $this->apiUrl = PAYSTACK_API_URL;
    }

    /**
     * Initialize a Paystack transaction
     * @param array $params - Contains: email, amount (in kobo/pesewas), reference, callback_url, metadata (optional)
     * @return array - Response from Paystack API
     */
    public function initializeTransaction($params) {
        $url = $this->apiUrl . '/transaction/initialize';
        
        // Convert amount to smallest currency unit (pesewas for GHS)
        $amount = $this->convertToSmallestUnit($params['amount']);
        
        $data = [
            'email' => $params['email'],
            'amount' => $amount,
            'reference' => $params['reference'],
            'callback_url' => $params['callback_url'],
            'currency' => PAYSTACK_CURRENCY,
        ];

        // Add metadata if provided
        if (isset($params['metadata']) && is_array($params['metadata'])) {
            $data['metadata'] = $params['metadata'];
        }

        $response = $this->makeRequest($url, 'POST', $data);
        return $response;
    }

    /**
     * Verify a Paystack transaction
     * @param string $reference - Transaction reference from Paystack
     * @return array - Response from Paystack API
     */
    public function verifyTransaction($reference) {
        $url = $this->apiUrl . '/transaction/verify/' . $reference;
        $response = $this->makeRequest($url, 'GET');
        return $response;
    }

    /**
     * Make HTTP request to Paystack API
     * @param string $url - API endpoint
     * @param string $method - HTTP method (GET, POST, etc.)
     * @param array $data - Data to send (for POST requests)
     * @return array - Decoded JSON response
     */
    private function makeRequest($url, $method = 'GET', $data = []) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/json'
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            return [
                'status' => false,
                'message' => 'Curl error: ' . $error
            ];
        }

        $decodedResponse = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'status' => true,
                'data' => $decodedResponse['data'] ?? $decodedResponse,
                'message' => $decodedResponse['message'] ?? 'Success'
            ];
        } else {
            return [
                'status' => false,
                'message' => $decodedResponse['message'] ?? 'Payment request failed',
                'data' => $decodedResponse
            ];
        }
    }

    /**
     * Convert amount to smallest currency unit
     * For GHS: Convert cedis to pesewas (multiply by 100)
     * @param float $amount - Amount in main currency unit
     * @return int - Amount in smallest currency unit
     */
    private function convertToSmallestUnit($amount) {
        // GHS uses pesewas (1 GHS = 100 pesewas)
        return intval($amount * 100);
    }

    /**
     * Get public key for frontend use
     * @return string - Paystack public key
     */
    public function getPublicKey() {
        return $this->publicKey;
    }
}

?>

