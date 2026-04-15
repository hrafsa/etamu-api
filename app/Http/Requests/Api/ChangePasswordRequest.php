<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rules;

class ChangePasswordRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required','string'],
            'password' => [
                'bail',
                'required',
                'string',
                'min:8',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Current password is required.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ];
    }
}

