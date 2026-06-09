<?php

namespace App\Modules\User\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\User $this */

        $subscription = $this->subscription('default');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'language' => $this->language,
            'has_active_subscription' => $this->subscribed('default'),
            'subscription_status' => $subscription ? $subscription->stripe_status : null,
            'subscription_ends_at' => $subscription?->ends_at?->toIso8601String(),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
