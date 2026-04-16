<?php

namespace App\Modules\Auth\Requests;

use App\Core\Abstracts\Request;
use App\Modules\Auth\Enums\DocumentType;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends Request
{
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:255|unique:users',
            'password' => ['required', Password::min(8)->max(12), 'confirmed'],
            'password_confirmation' => 'required',

            'document_type' => ['nullable', new Enum(DocumentType::class)],

            'document_number' => $this->input('document_type') === DocumentType::CPF->value
                ? 'nullable|digits:11|unique:users,document_number'
                : 'nullable|string|max:20|unique:users,document_number',

            'phone' => 'nullable|regex:/^\+?[0-9]{8,20}$/',

            'birth_date' => [
                'nullable',
                'date',
                'before:today',
                'after:1900-01-01'
            ],

            'country_code' => 'nullable|string|max:5',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('country_code') === 'BR' || $this->input('document_type') === 'CPF') {
            $this->merge([
                'document_number' => preg_replace('/\D/', '', $this->input('document_number', '')),
                'phone'           => preg_replace('/\D/', '', $this->input('phone', '')),
            ]);
        }
    }
}
