<?php

return [
    'gateway' => env('PAYMENT_GATEWAY', 'stripe'),

    'plans' => [
        'monthly' => env('PAYMENT_MONTHLY_PLAN'),
        'yearly'  => env('PAYMENT_YEARLY_PLAN'),
    ],

    'default_plan' => env('PAYMENT_DEFAULT_PLAN', 'monthly'),
];
