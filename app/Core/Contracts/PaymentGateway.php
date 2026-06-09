<?php

namespace App\Core\Contracts;

use App\Models\User;
use Laravel\Cashier\Subscription;

interface PaymentGateway
{
    public function createCustomer(User $user): string;
    public function createCheckoutSession(User $user, string $planId, string $locale): string;
    public function cancelSubscription(User $user): void;
    public function resumeSubscription(User $user): void;
    public function getSubscription(User $user): ?Subscription;
}
