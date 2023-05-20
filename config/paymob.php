<?php

declare(strict_types=1);


return [
   
    'base_url' => 'https://accept.paymob.com/api',

    // paymob configuration settings
    'api_key' => env('PAYMOB_API_KEY', ''),
    'card_integration_id' => env('PAYMOB_CARD_INTEGRATION_ID', ''),
    'card_iframe_id' => env('PAYMOB_CARD_IFRAME_ID', ''),
    'hmac_secret' => env('PAYMOB_HMAC_SECRET', ''),
];
