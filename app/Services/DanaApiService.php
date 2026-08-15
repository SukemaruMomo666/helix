<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DanaApiService
{
    protected $clientId;
    protected $clientSecret;
    protected $merchantId;
    protected $privateKey;
    protected $publicKey;
    protected $env;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.dana.client_id');
        $this->clientSecret = config('services.dana.client_secret');
        $this->merchantId = config('services.dana.merchant_id');
        $this->privateKey = config('services.dana.private_key');
        $this->publicKey = config('services.dana.public_key');
        $this->env = config('services.dana.env', 'sandbox');
        
        $this->baseUrl = $this->env === 'production' 
            ? 'https://api.dana.id' 
            : 'https://api.sandbox.dana.id';
    }

    public function generateSignature($method, $uri, $timestamp, $body = '')
    {
        $bodyHash = empty($body) ? '' : hash('sha256', $body);
        $stringToSign = $method . ':' . $uri . ':' . $bodyHash . ':' . $timestamp;
        
        if (empty($this->privateKey)) {
            return 'MOCK_SIGNATURE';
        }

        $privateKeyStr = "-----BEGIN PRIVATE KEY-----\n" . wordwrap($this->privateKey, 64, "\n", true) . "\n-----END PRIVATE KEY-----";
        openssl_sign($stringToSign, $signature, $privateKeyStr, OPENSSL_ALGO_SHA256);
        
        return base64_encode($signature);
    }

    public function createOrder($orderId, $amount, $title, $returnUrl)
    {
        $uri = '/v3/acquiring/order/create.htm';
        $timestamp = date('c');
        
        $payload = json_encode([
            'request' => [
                'head' => [
                    'version' => '3.0',
                    'function' => 'dana.acquiring.order.create',
                    'clientId' => $this->clientId ?? 'TEST_CLIENT_ID',
                    'reqTime' => $timestamp,
                    'reqMsgId' => uniqid('msg_'),
                ],
                'body' => [
                    'merchantId' => $this->merchantId ?? 'TEST_MERCHANT',
                    'merchantTransId' => $orderId,
                    'transAmount' => [
                        'value' => number_format($amount, 2, '.', ''),
                        'currency' => 'IDR'
                    ],
                    'orderTitle' => $title,
                    'returnUrl' => $returnUrl,
                    'envInfo' => [
                        'sourcePlatform' => 'WEB'
                    ]
                ]
            ]
        ]);

        $signature = $this->generateSignature('POST', $uri, $timestamp, $payload);

        if (empty($this->privateKey)) {
            Log::info('DANA Sandbox mode: Missing keys, returning mock URL.');
            return [
                'success' => true,
                'checkout_url' => url('/dana/sandbox/payment?orderId=' . $orderId . '&amount=' . $amount)
            ];
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-DANA-Signature' => $signature,
            'X-DANA-Client-Id' => $this->clientId,
            'X-DANA-Timestamp' => $timestamp
        ])->post($this->baseUrl . $uri, json_decode($payload, true));

        if ($response->successful() && isset($response['response']['body']['checkoutUrl'])) {
            return [
                'success' => true,
                'checkout_url' => $response['response']['body']['checkoutUrl']
            ];
        }

        Log::error('DANA Create Order Error', [
            'payload' => $payload,
            'response' => $response->body()
        ]);

        $responseBody = $response->body();
        if (empty($responseBody) && $this->env === 'sandbox') {
            Log::info('DANA Sandbox returned empty body. Falling back to Mock URL.');
            return [
                'success' => true,
                'checkout_url' => url('/dana/sandbox/payment?orderId=' . $orderId . '&amount=' . $amount)
            ];
        }

        $errorDetail = 'Gagal membuat pesanan di DANA. RESP: ' . $responseBody;
        if (isset($response['response']['body']['resultInfo']['resultMsg'])) {
            $errorDetail .= ' (' . $response['response']['body']['resultInfo']['resultMsg'] . ')';
        } else if (!$response->successful()) {
            $errorDetail .= ' (HTTP ' . $response->status() . ')';
        }

        return [
            'success' => false,
            'message' => $errorDetail
        ];
    }
    
    public function verifySignature($payload, $signature, $timestamp)
    {
        if (empty($this->publicKey)) {
            return true;
        }

        $bodyHash = hash('sha256', $payload);
        $stringToSign = "POST:/api/dana/webhook:" . $bodyHash . ":" . $timestamp;
        
        $publicKeyStr = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($this->publicKey, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
        
        $result = openssl_verify($stringToSign, base64_decode($signature), $publicKeyStr, OPENSSL_ALGO_SHA256);
        
        return $result === 1;
    }
}
