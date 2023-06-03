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

- Make payment and get iframe url from paymob

```php

<?php

use Illuminate\Support\Facades\Route;
use Skrskr\Paymob\Facades\Paymob;


Route::get('/test', function () {

    $orderData = [
        "amount_cents"=> "100", // required, integer value in cents
        "currency"=> "EGP", // required
        "merchant_order_id"=> 1243, //	optional, A unique alpha-numeric value for each transaction
        "expiration" => 3600, // required
        // optional fields in billing data, if it isn't available, please send it to be "NA",
        "billing_data" => [
            "first_name" => "Mohamed", // required
            "last_name" => "Sakr", // required
            "email" => "mohamed@mail.com", // required
            "phone_number" => "01234567890", // required 
            "apartment" => "NA", // optional
            "floor" => "NA", // optional
            "street" => "NA", //optional
            "building" => "NA",  // optional
            "shipping_method" => "NA", // optional
            "postal_code" => "NA",  //optional
            "city" => "NA",  // optional
            "country" => "NA", // optional
            "state" => "NA" // optional
        ],
    ];

    // Get payment iframe URL
    $iframeUrl = Paymob::pay($orderData);
    return $iframeUrl;
    //redirect to ifram url
    // return redirect()->to($iframeUrl);
});


```


- Webhook transaction url:
```
POST Request: (https://yourdomain.com/paymob/webhook)

# Replace your yourdomain.com with actual domain name

For testing callback, you can use tool like [Ngrok](https://ngrok.com)
```

- Add Paymob trasaction callback to integration card [How](https://docs.paymob.com/docs/payment-integrations) 

- For handling webhook events, you should create two listeners for each event and then register events and listeners in `EventServiceProvider` 
```php 
# Events:
- Skrskr\Paymob\Events\TransactionSuccessedEvent::class
- Skrskr\Paymob\Events\TransactionFailedEvent::class

# Create two listeners for each event 
# run two commands
php artisan make:listener PaymobTransactionSuccessedListener
php artisan make:listener PaymobTransactionFailedListener

```

1- in `App\ListenersPaymobTransactionSuccessedListener.php` replace file content with class below

```php
<?php

namespace App\Listeners;

use Skrskr\Paymob\Events\TransactionSuccessedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PaymobTransactionSuccessedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Skrskr\Paymob\Events\TransactionSuccessedEvent  $event
     * @return void
     */
    public function handle(TransactionSuccessedEvent $event)
    {
        \Log::info($event->payload);
    }
}

```

2- in `App\Listeners\PaymobTransactionFailedListener.php` replace file content with class content below
```php
<?php

namespace App\Listeners;

use Skrskr\Paymob\Events\TransactionFailedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PaymobTransactionFailedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Skrskr\Paymob\Events\TransactionFailedEvent  $event
     * @return void
     */
    public function handle(TransactionFailedEvent $event)
    {
        \Log::info($event->payload);
    }
}

```
3- Register events and listeners in `App\Providers\EventServiceProvider`

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Skrskr\Paymob\Events\TransactionFailedEvent;
use Skrskr\Paymob\Events\TransactionSuccessedEvent;

use App\Listeners\PaymobTransactionSuccessedListener;
use App\Listeners\PaymobTransactionFailedListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [        
        TransactionSuccessedEvent::class => [
            PaymobTransactionSuccessedListener::class
        ],
        
        TransactionFailedEvent::class => [
            PaymobTransactionFailedListener::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

```

