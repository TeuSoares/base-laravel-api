<?php

namespace App\Modules\Auth\Actions;

use App\Core\Abstracts\Action;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegisterAction extends Action
{
    public function execute(array $data): User
    {
        $user = User::create($data);

        Auth::login($user);

        return $user;
    }
}
