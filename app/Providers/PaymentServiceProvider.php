<?php

namespace App\Providers;

use App\Core\Contracts\PaymentGateway;
use App\Core\Payment\StripeGateway;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, function () {
            return match (config('payment.gateway')) {
                'stripe' => new StripeGateway(),
                default  => new StripeGateway(),
            };
        });
    }
}
