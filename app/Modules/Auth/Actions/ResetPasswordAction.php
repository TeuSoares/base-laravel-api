<?php

namespace App\Modules\Auth\Actions;

use App\Core\Abstracts\Action;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class ResetPasswordAction extends Action
{
    public function execute(array $data): void
    {
        $status = Password::broker()->reset(
            $data,
            function (User $user, string $password) {
                $this->resetPassword($user, $password);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            if ($status === Password::INVALID_USER) {
                $this->error()->validation([
                    'email' => [__($status)]
                ]);
            }

            $this->error()->http(__($status), status: 422);
        }
    }

    protected function resetPassword(User $user, string $password): void
    {
        $user->forceFill([
            'password' => $password,
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));
    }
}
