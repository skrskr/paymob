<?php

declare(strict_types=1);


namespace Skrskr\Paymob\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VerifyWebhookHmac
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     *
     * @throws HttpException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hmacSecret = config('paymob.hmac_secret');
        $obj = $request->get('obj');
        $hmac = $request->get('hmac');
        $calculatedHmac = $this->calculateHmac($obj, $hmacSecret);
        if($hmac !== $calculatedHmac) {
            // abort(403, 'Access denied');
        }

        return $next($request);
    }

    /**
     * Sort the data dictionary by key Lexicographical order
     * Concatenate the values (not the keys) in one string.
     * Calculate the hash of the concatenated string using SHA512 and your HMAC secret, found in the profile tab in your dashboard.
     * The resultant HMAC is Hex (base 16) lowercase.
     * Please note that they need to be in the order shown below.
     * amount_cents, created_at, currency, error_occured, has_parent_transaction, id, integration_id, is_3d_secure..etc
     * https://accept.paymobsolutions.com/docs/guide/hmac_calculation/#hmac-calculation
     * @param array $obj
     * @param string $hmacSecret
     * @return string
     */
    public function calculateHmac(array $obj, string $hmacSecret): string
    {
        $data =
            json_encode($obj['amount_cents']) .
            json_encode($obj['created_at']) .
            json_encode($obj['currency']) .
            json_encode($obj['error_occured']) .
            json_encode($obj['has_parent_transaction']) .
            json_encode($obj['id']) .
            json_encode($obj['integration_id']) .
            json_encode($obj['is_3d_secure']) .
            json_encode($obj['is_auth']) .
            json_encode($obj['is_capture']) .
            json_encode($obj['is_refunded']) .
            json_encode($obj['is_standalone_payment']) .
            json_encode($obj['is_voided']) .
            json_encode($obj['order']['id']) .
            json_encode($obj['owner']) .
            json_encode($obj['pending']) .
            json_encode($obj['source_data']['pan']) .
            json_encode($obj['source_data']['sub_type']) .
            json_encode($obj['source_data']['type']) .
            json_encode($obj['success']);
        $data = str_replace('"', '', $data);
        $hmac = hash_hmac('SHA512', $data, $hmacSecret);
        return $hmac;
    }
}
