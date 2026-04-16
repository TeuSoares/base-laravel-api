<?php

namespace App\Modules\Auth\Requests;

use App\Core\Abstracts\Request;

class ForgotPasswordRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }
}
