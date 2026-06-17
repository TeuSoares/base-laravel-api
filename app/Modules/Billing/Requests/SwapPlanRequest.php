<?php

namespace App\Modules\Billing\Requests;

use App\Core\Abstracts\Request;

class SwapPlanRequest extends Request
{
    public function rules(): array
    {
        return [
            'plan' => [
                'required',
                'string',
                'in:' . implode(',', array_keys(config('payment.plans'))),
            ],
        ];
    }

    public function planId(): string
    {
        return config("payment.plans.{$this->input('plan')}");
    }
}
