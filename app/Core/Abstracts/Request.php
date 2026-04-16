<?php

namespace App\Core\Abstracts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

abstract class Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    abstract public function rules(): array;

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }

    protected function failedAuthorization()
    {
        throwApi()->forbidden(__('auth.forbidden'));
    }
}
