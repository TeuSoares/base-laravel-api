<?php

namespace App\Modules\User\Requests;

use App\Core\Abstracts\Request;
use App\Core\Traits\HasUserRules;

class UpdateUserRequest extends Request
{
    use HasUserRules;

    public function rules(): array
    {
        $passwordRules = $this->has('password') && filled($this->password)
            ? ['required', 'current_password']
            : ['nullable'];

        return [
            ...$this->userRules(required: false),
            ...$this->passwordRules(required: false),
            'current_password' => $passwordRules,
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->prepareUserData();
    }
}
