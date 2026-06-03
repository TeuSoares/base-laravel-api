<?php

namespace App\Modules\Billing\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Modules\Billing\Actions\CreateCheckoutSession;
use App\Modules\Billing\Requests\CheckoutRequest;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function initiate(
        CheckoutRequest $request,
        CreateCheckoutSession $createCheckoutSession,
    ): JsonResponse {
        $url = $createCheckoutSession->execute(
            $request->user(),
            $request->planId(),
            $request->gatewayLocale(),
        );

        return $this->response()->data(['url' => $url]);
    }
}
