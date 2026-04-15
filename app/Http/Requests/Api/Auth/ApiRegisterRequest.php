<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Validation\Rules;

class ApiRegisterRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255','unique:users,email'],
            'phone' => ['required','string','max:255','unique:users,phone'],
            'password' => [
                'bail', // hentikan setelah satu gagal
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
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'The email field must be a valid email address.',
            'email.unique' => 'Email is already registered.',
            'phone.required' => 'Phone is required.',
            'phone.unique' => 'Phone is already registered.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ];
    }
}
