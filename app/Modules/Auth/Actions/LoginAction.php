<?php

namespace App\Modules\Auth\Actions;

use App\Core\Abstracts\Action;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginAction extends Action
{
    public function execute(array $data): ?User
    {
        $rateLimiterKey = 'fail-login:' . $data['email'];

        if (RateLimiter::tooManyAttempts($rateLimiterKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimiterKey);
            $this->error()->http(__('auth.throttle', ['seconds' => $seconds]), status: 429);
        }

        if (Auth::attempt($data)) {
            RateLimiter::clear($rateLimiterKey);
            return Auth::user();
        }

        RateLimiter::hit($rateLimiterKey, 300);

        return $this->error()->validation([
            'login' => [__('auth.invalid_credentials')]
        ]);
    }
}
