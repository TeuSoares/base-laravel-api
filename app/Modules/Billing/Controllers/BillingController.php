<?php

namespace App\Modules\Billing\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Modules\Billing\Actions\CancelSubscription;
use App\Modules\Billing\Actions\GetSubscription;
use App\Modules\Billing\Actions\ResumeSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function subscription(
        Request $request,
        GetSubscription $getSubscription,
    ): JsonResponse {
        $subscription = $getSubscription->execute($request->user());

        return $this->response()->data($subscription);
    }

    public function cancel(
        Request $request,
        CancelSubscription $cancelSubscription,
    ): JsonResponse {
        $cancelSubscription->execute($request->user());

        return $this->response()->message(__('billing.cancelled'));
    }

    public function resume(
        Request $request,
        ResumeSubscription $resumeSubscription,
    ): JsonResponse {
        $resumeSubscription->execute($request->user());

        return $this->response()->message(__('billing.resumed'));
    }
}
