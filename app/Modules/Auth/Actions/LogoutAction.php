<?php

namespace App\Modules\Auth\Actions;

use App\Core\Abstracts\Action;
use Illuminate\Support\Facades\Auth;

class LogoutAction extends Action
{
    public function execute(): void
    {
        Auth::guard('web')->logout();
    }
}
