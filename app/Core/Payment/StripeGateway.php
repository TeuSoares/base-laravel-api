<?php

namespace App\Core\Payment;

use App\Core\Contracts\PaymentGateway;
use App\Models\User;
use Laravel\Cashier\Subscription;

class StripeGateway implements PaymentGateway
{
    public function createCustomer(User $user): string
    {
        $user->createOrGetStripeCustomer();
        return $user->stripe_id;
    }

    public function createCheckoutSession(User $user, string $planId, string $locale): string
    {
        $user->createOrGetStripeCustomer();

        return $user->newSubscription('default', $planId)
            ->checkout([
                'success_url' => config('app.frontend_url') . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => config('app.frontend_url') . '/checkout/cancel',
                'locale'      => $locale,
            ])
            ->url;
    }

    public function cancelSubscription(User $user): void
    {
        $user->subscription('default')?->cancel();
    }

    public function resumeSubscription(User $user): void
    {
        $user->subscription('default')?->resume();
    }

    public function getSubscription(User $user): ?Subscription
    {
        $subscription = $user->subscription('default');

        if (!$subscription) return null;

        return $subscription;
    }
}
