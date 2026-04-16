<?php

namespace App\Modules\Auth\Requests;

use App\Core\Abstracts\Request;

class LoginRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }
}
