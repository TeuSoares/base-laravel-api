<?php

namespace App\Modules\Billing\Actions;

use App\Core\Abstracts\Action;
use App\Core\Contracts\PaymentGateway;
use App\Models\User;

class CancelSubscription extends Action
{
    public function __construct(
        private readonly PaymentGateway $gateway
    ) {}

    public function execute(User $user): void
    {
        $this->gateway->cancelSubscription($user);
    }
}
