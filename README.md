# Paymob Laravel Package

This is a laravel package to facilate integartion with paymob apis [Paymob docs](https://docs.paymob.com/docs/accept-standard-redirect).

## Installation

1- You can install the package via composer:

```
composer require skrskr/paymob
```

2- Publish config file for editing if needed:

```
php artisan vendor:publish --tag=config --provider="Skrskr\Paymob\PaymobServiceProvider"
```

## Usage
- Register new merchant account or login if you already have one ([Register](https://accept.paymob.com/portal2/en/register?flash=true)).
- Get Paymob credentials from Paymob Dashboard ([How](https://docs.paymob.com/docs/profile)) and update `.env` file.
```
PAYMOB_API_KEY             = 
PAYMOB_CARD_INTEGRATION_ID = 
PAYMOB_CARD_IFRAME_ID      = 
PAYMOB_HMAC_SECRET         = 
```

- Webhook transaction url:
```
POST Request: (https://yourdomain.com/paymob/webhook)

# Replace your yourdomain.com with actual domain name

For testing callback, you can use tool like [ngrok](https://ngrok.com/)
```

- Add Paymob trasaction callback to integration card [How](https://docs.paymob.com/docs/payment-integrations) 

- For handling webhook events, you should create two listeners for each event
```
- Skrskr\Paymob\Events\TransactionSuccessedEvent::class
- Skrskr\Paymob\Events\TransactionFailedEvent::class

php artisan make:listener PaymobTransactionSuccessedListener
php artisan make:listener PaymobTransactionFailedListener

```