<?php

namespace App\Modules\Billing\Actions;

use App\Core\Abstracts\Action;
use App\Core\Contracts\PaymentGateway;
use App\Models\User;
use Laravel\Cashier\Subscription;

class GetSubscription extends Action
{
    public function __construct(
        private readonly PaymentGateway $gateway
    ) {}

    public function execute(User $user): ?Subscription
    {
        return $this->gateway->getSubscription($user);
    }
}
