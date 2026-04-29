<?php

namespace App\Modules\Auth\Actions;

use App\Core\Abstracts\Action;
use Illuminate\Support\Facades\Password;

class ForgotPasswordAction extends Action
{
    public function execute(array $data): void
    {
        $status = Password::broker()->sendResetLink($data);

        if ($status === Password::RESET_THROTTLED) {
            $this->error()->http(__($status), status: 429);
        }
    }
}
