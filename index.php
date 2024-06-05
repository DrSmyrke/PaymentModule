<?php

class Payment
{
    private $apiKey;
    private $apiUrl = 'https://api.cryptocloud.plus/v2/invoice/create';

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    private function sendRequest($endpoint, $method = 'GET', $data = [])
    {
        $url = $this->apiUrl . $endpoint;
        $ch = curl_init($url);

        $headers = [
            'Content-Type: application/json',
            'Authorization: Token ' . $this->apiKey
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Request Error: ' . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    public function createInvoice($shopId, $amount, $currency = 'USD', $additionalFields = [], $orderId = null, $email = null)
    {
        $data = [
            'shop_id' => $shopId,
            'amount' => $amount,
            'currency' => $currency,
            'add_fields' => $additionalFields,
            'order_id' => $orderId,
            'email' => $email,
        ];

        return $this->sendRequest('/invoice/create', 'POST', $data);
    }

    public function getInvoiceStatus($invoiceId)
    {
        return $this->sendRequest('/invoice/' . $invoiceId);
    }

    public function getTransactionById($transactionId) {
        return $this->sendRequest('/transaction/' . $transactionId);
    }

    public function getCurrencies() {
        return $this->sendRequest('/currencies');
    }
    public function getTransactions($limit = 10, $offset = 0)
    {
        $params = [
            'limit' => $limit,
            'offset' => $offset
        ];

        return $this->sendRequest('/transactions?' . http_build_query($params));
    }

    public function cancelInvoice($invoiceId)
    {
        return $this->sendRequest('/invoice/' . $invoiceId, 'DELETE');
    }

    public function patchInvoice($invoiceId, $data){
        return $this->sendRequest('/invoice/' . $invoiceId, 'PATCH', $data);
    }

    public function updateInvoice($invoiceId, $data)
    {
        return $this->sendRequest('/invoice/' . $invoiceId, 'PUT', $data);
    }

}
