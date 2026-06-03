<?php

return [
    'gateway'      => env('PAYMENT_GATEWAY', 'stripe'),
    'default_plan' => env('PAYMENT_DEFAULT_PLAN', 'monthly'),

    'plans' => [
        'monthly'  => env('PAYMENT_MONTHLY_PLAN'),
        'yearly'   => env('PAYMENT_YEARLY_PLAN'),
        'lifetime' => env('PAYMENT_LIFETIME_PLAN'),
    ],
];
