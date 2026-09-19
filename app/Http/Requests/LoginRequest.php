<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravel\Passport\Passport;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'scopes' => ['sometimes', 'array'],
            'scopes.*' => ['string', Rule::in(Passport::scopeIds())],
        ];
    }
}
