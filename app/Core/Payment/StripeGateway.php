<?php

namespace App\Core\Payment;

use App\Core\Contracts\PaymentGateway;
use App\Models\User;

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

    public function getSubscription(User $user): ?array
    {
        $subscription = $user->subscription('default');

        if (!$subscription) return null;

        return [
            'plan'       => $subscription->stripe_price,
            'status'     => $subscription->stripe_status,
            'ends_at'    => $subscription->ends_at,
            'trial_ends' => $subscription->trial_ends_at,
            'on_trial'   => $subscription->onTrial(),
            'cancelled'  => $subscription->cancelled(),
            'active'     => $subscription->active(),
        ];
    }
}
