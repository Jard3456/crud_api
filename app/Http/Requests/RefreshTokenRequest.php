<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefreshTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refresh_token' => ['required', 'string'],
            'client_id' => ['required', 'string'],
            'client_secret' => ['required', 'string'],
            'scope' => ['nullable', 'string'],
        ];
    }
}
