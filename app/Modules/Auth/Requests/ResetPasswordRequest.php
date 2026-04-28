<?php

namespace App\Modules\Auth\Requests;

use App\Core\Abstracts\Request;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends Request
{
    public function rules(): array
    {
        return [
            'token'    => 'required|string',
            'email'    => 'required|email|exists:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(10)
                    ->letters()
                    ->numbers()
                    ->uncompromised(),
            ],
            'password_confirmation' => 'required|string',
        ];
    }
}
