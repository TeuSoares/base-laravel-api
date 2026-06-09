<?php

namespace App\Modules\Billing\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \Laravel\Cashier\Subscription $resource
 */
class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'plan' => $this->resource->stripe_price,
            'status' => $this->resource->stripe_status,
            'ends_at' => $this->resource->ends_at?->toIso8601String(),
            'trial_ends' => $this->resource->trial_ends_at?->toIso8601String(),
            'on_trial' => $this->resource->onTrial(),
            'cancelled' => $this->resource->cancelled(),
            'active' => $this->resource->active(),
        ];
    }
}
