<?php

namespace App\Modules\Billing\Requests;

use App\Core\Abstracts\Request;
use App\Core\Enums\UserLanguage;

class CheckoutRequest extends Request
{
    public function rules(): array
    {
        return [
            'plan' => [
                'nullable',
                'string',
                'in:' . implode(',', array_keys(config('payment.plans'))),
            ],
        ];
    }

    public function planId(): string
    {
        $plan = $this->input('plan') ?? config('payment.default_plan');
        return config("payment.plans.{$plan}");
    }

    public function gatewayLocale(): string
    {
        $language = $this->user()->language;

        return ($language instanceof UserLanguage ? $language : UserLanguage::from($language))
            ->toGatewayLocale();
    }
}
