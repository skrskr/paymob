<?php

declare(strict_types=1);


namespace Skrskr\Paymob\Traits;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

trait Helper {

    /**
     * Make http post request
     * @param string $url
     * @param array $payload
     * @return Response
     */
    private function makeRequest(string $url, array $payload): Response {
        $response = Http::withHeaders([
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ])->post(
            $url,
            $payload
        );
        return $response;
    }

    /**
     * build iframe url using payment key and iframe id.
     * @param string $paymentKey
     * @param string $iframeId
     * @return string
     */
    private function buildIframeUrl(string $paymentKey, string $iframeId): string
    {
        return 'https://accept.paymobsolutions.com/api/acceptance/iframes/' . $iframeId . '?payment_token=' . $paymentKey;
    }
}