<?php

namespace App\Modules\Billing\Actions;

use App\Core\Abstracts\Action;
use App\Core\Contracts\PaymentGateway;
use App\Models\User;

class CreateCheckoutSession extends Action
{
    public function __construct(
        private readonly PaymentGateway $gateway
    ) {}

    public function execute(User $user, string $planId, string $locale): string
    {
        if ($user->subscribed('default')) {
            $this->error()->forbidden(__('billing.already_subscribed'));
        }

        return $this->gateway->createCheckoutSession($user, $planId, $locale);
    }
}
