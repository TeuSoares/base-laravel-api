<?php

namespace App\Core\Traits;

use App\Core\Enums\UserLanguage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

trait HasUserRules
{
    protected function userRules(bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes|required' : 'required';

        return [
            'name'     => $required . '|string|max:100',
            'email'    => [
                $required,
                'email',
                'max:255',
                $isUpdate
                    // Ignore the authenticated user's own email on uniqueness check
                    ? Rule::unique('users', 'email')->ignore($this->user()->id)
                    : Rule::unique('users', 'email'),
            ],
            'language' => [
                $required,
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
        $this->merge([
            'email'    => strtolower(trim($this->email ?? '')),
            'language' => str_replace('-', '_', $this->language ?? ''),
        ]);
    }
}
