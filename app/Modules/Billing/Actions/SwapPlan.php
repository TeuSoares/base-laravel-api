<?php

namespace App\Modules\Billing\Actions;

use App\Core\Abstracts\Action;
use App\Core\Contracts\PaymentGateway;
use App\Models\User;

class SwapPlan extends Action
{
    public function __construct(
        private readonly PaymentGateway $gateway
    ) {}

    public function execute(User $user, string $planId): ?array
    {
        if (!$user->subscribed('default')) {
            $this->error()->forbidden(__('billing.no_active_subscription'));
        }

        return $this->gateway->swapPlan($user, $planId);
    }
}
