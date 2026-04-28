<?php

namespace App\Modules\Auth\Requests;

use App\Core\Abstracts\Request;
use App\Core\Enums\CountryCode;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends Request
{
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(10)
                    ->letters()
                    ->numbers()
                    ->uncompromised(),
            ],
            'password_confirmation' => 'required',
            'country_code' => [
                'required',
                new Enum(CountryCode::class)
            ],
        ];
    }
}
