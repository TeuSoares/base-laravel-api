<?php

// app/Modules/User/Requests/UpdateUserRequest.php
namespace App\Modules\User\Requests;

use App\Core\Abstracts\Request;
use App\Core\Traits\HasUserRules;

class UpdateUserRequest extends Request
{
    use HasUserRules;

    public function rules(): array
    {
        return [
            ...$this->userRules(isUpdate: true),
            ...$this->passwordRules(required: false),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->prepareUserData();
    }
}
