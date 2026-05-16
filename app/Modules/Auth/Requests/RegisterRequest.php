<?php

namespace App\Modules\Auth\Requests;

use App\Core\Abstracts\Request;
use App\Core\Enums\UserLanguage;
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
            'language' => 'required|in:' . implode(',', UserLanguage::values()),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->email)),
            'language' => str_replace('-', '_', $this->language),
        ]);
    }
}
