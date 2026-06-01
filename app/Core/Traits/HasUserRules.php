<?php

namespace App\Core\Traits;

use App\Core\Enums\UserLanguage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

trait HasUserRules
{
    protected function userRules(bool $required = true): array
    {
        $rules = $required ? ['required'] : ['sometimes', 'required'];

        return [
            'name' => [...$rules, 'string', 'max:100'],
            'email' => [
                ...$rules,
                'email',
                'max:255',
                !$required
                    ? Rule::unique('users', 'email')->ignore($this->user()->id)
                    : Rule::unique('users', 'email'),
            ],
            'language' => [
                ...$rules,
                'string',
                Rule::in(UserLanguage::values()),
            ],
        ];
    }

    protected function passwordRules(bool $required = true): array
    {
        $rule = $required ? 'required' : 'nullable';

        return [
            'password' => [
                $rule,
                'confirmed',
                Password::min(10)->letters()->numbers()->uncompromised(threshold: 10),
            ],
            'password_confirmation' => $rule,
        ];
    }

    protected function prepareUserData(): void
    {
        $data = [];

        if ($this->has('email')) {
            $data['email'] = strtolower(trim($this->email ?? ''));
        }

        if ($this->has('language')) {
            $data['language'] = str_replace('-', '_', $this->language ?? '');
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }
}
