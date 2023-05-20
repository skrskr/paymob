<?php

declare(strict_types=1);


namespace Skrskr\Paymob\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Skrskr\Paymob\Events\TransactionFailedEvent;
use Skrskr\Paymob\Events\TransactionSuccessedEvent;
use Skrskr\Paymob\Http\Middleware\VerifyWebhookHmac;

class WebhookController extends Controller
{

    public function __construct()
    {
        $this->middleware(VerifyWebhookHmac::class);
    }

    /**
     * Handle Webhook Callback.
     * @param Request $request
     * @return void
     */
    public function handleWebhook(Request $request): void
    {

        \Log::info("Transaction Process Request : " . json_encode($request->toArray()));
        $type = $request->get('type');
        $obj = $request->get('obj');
        $payload = json_decode($request->getContent(), true);

        
        if (($type == "TRANSACTION")) {
            // $orderId = $obj['order']['merchant_order_id'];

            if (($obj['success'] == true) && ($obj['pending'] == false) && ($obj['is_refunded'] == false)) {
                \Log::info("Transaction Process Success");
                TransactionSuccessedEvent::dispatch($payload);
            } else if (($obj['success'] == false) && ($obj['pending'] == false) && ($obj['is_refunded'] == false)) {
                \Log::info('Transaction failed');
                TransactionFailedEvent::dispatch($payload);
            }
        }
    }
}
