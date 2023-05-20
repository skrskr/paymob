<?php

declare(strict_types=1);


namespace Skrskr\Paymob;

use Skrskr\Paymob\Exceptions\InvalidApiExceptionException;
use Skrskr\Paymob\Exceptions\PaymentKeyException;
use Skrskr\Paymob\Exceptions\RegisterOrderException;
use Skrskr\Paymob\Traits\Helper;

class Paymob
{
    use Helper;

    private string $apiKey;
    private string $baseUrl;
    private string $cardIntegrationId;
    private string $cardIframeId;

    public function __construct()
    {
        $this->apiKey = config('paymob.api_key');
        $this->baseUrl = config('paymob.base_url');
        $this->cardIntegrationId = config('paymob.card_integration_id');
        $this->cardIframeId = config('paymob.card_iframe_id');
    }

    /**
     * authenticate request.
     * @return string
     * @throws InvalidApiKeyException
     */
    private function authenticate(): string
    {
        $authUrl =  $this->baseUrl.'/auth/tokens';
        // Request body
       $payload = [
            'api_key' => $this->apiKey,
        ];
        $authResponse = $this->makeRequest($authUrl, $payload);
        if($authResponse->getStatusCode() == 201) {
            return $authResponse['token'];
        }
        throw new InvalidApiExceptionException("Authentication failed, Invalid api key");
    }

    /**
     * register order request.
     * @param string $authToken
     * @param array $data
     * @return int
     * @throws RegisterOrderException
     */
    private function registerOrder(string $authToken, array $data): int
    {
        $deliveryNeeded = $data['delivery_needed'] ?? false;
        $amountCents = $data['amount_cents'] ?? 0;
        $items = $data['items'] ?? [];
        $merchantOrderId = $data['merchant_order_id'] ?? null;

        $ordersUrl = config('paymob.base_url').'/ecommerce/orders';
        $payload = [
            'auth_token' => $authToken,
            'delivery_needed' => $deliveryNeeded,
            'amount_cents' => $amountCents,
            'items' => $items,
            'merchant_order_id' => $merchantOrderId,
        ];

        $orderResponse = $this->makeRequest($ordersUrl, $payload);
        if($orderResponse->getStatusCode() == 201) {
            return $orderResponse['id'];
        }
        throw new RegisterOrderException("Register order failed");
    }

    /**
     * create payment key request.
     * @param string $authToken
     * @param int $orderId
     * @param array $data
     * @return string
     * @throws PaymentKeyException
     */
    private function createPaymentKey(string $authToken, int $orderId, array $data): string
    {
        $amountCents = (isset($data['amount_cents']) && $data['amount_cents']) ? $data['amount_cents'] : 0;
        $expiration = (isset($data['expiration']) && $data['expiration']) ? $data['expiration'] : 3600;
        $billingData = (isset($data['billing_data']) && $data['billing_data']) ? $data['billing_data'] : [];
        $currency = (isset($data['currency']) && $data['currency']) ? $data['currency'] : 'EGP';

        $paymentKeyUrl = $this->baseUrl . '/acceptance/payment_keys';

        $payload = [
            'auth_token' => $authToken,
            'amount_cents' => $amountCents,
            'expiration' => $expiration,
            'order_id' => $orderId,
            'billing_data' => $billingData,
            'currency' => $currency,
            'integration_id' => $this->cardIntegrationId,
        ];

        $paymentKeyResponse = $this->makeRequest($paymentKeyUrl, $payload);

        if ($paymentKeyResponse->getStatusCode() == 201) {
            return $paymentKeyResponse['token'];
        }
        throw new PaymentKeyException("Create payment key failed");
    }

    /**
     * Make payment using APIs.
     * @param array $data
     * @return string
     */
    public function pay(array $data) : string
    {
        // step 1 -> Authentication
        $authToken = $this->authenticate();

        // step 2 -> Order Registration
        $orderId = $this->registerOrder($authToken, $data);

        // step 3 => Get Payment Key
        $paymentKey = $this->createPaymentKey($authToken, $orderId, $data);

        // step 4 => build iframe url
        return $this->buildIframeUrl($paymentKey, $this->cardIframeId);
    }
}
