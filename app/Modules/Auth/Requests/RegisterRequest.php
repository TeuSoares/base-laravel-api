<?php

namespace App\Modules\Auth\Requests;

use App\Core\Abstracts\Request;
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
                    ->uncompromised(threshold: 3)
            ],
            'password_confirmation' => 'required',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->email)),
        ]);
    }
}
