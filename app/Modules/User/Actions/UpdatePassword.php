<?php

namespace App\Modules\User\Actions;

use App\Core\Abstracts\Action;
use App\Models\User;

class UpdatePassword extends Action
{
    public function execute(User $user, string $password): void
    {
        $user->update(['password' => $password]);
    }
}
