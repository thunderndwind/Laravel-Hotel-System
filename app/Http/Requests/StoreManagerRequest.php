<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManagerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'national_id' => ['required', 'digits:9', 'unique:managers,national_id'],
            'phone_number' => ['required'],
            'avatar_image' => ['nullable', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'national_id.required' => 'National ID is required.',
            'national_id.digits' => 'National ID must be exactly 9 digits.',
            'national_id.unique' => 'This national ID is already in use.',

            'phone_number.required' => 'Phone number is required.',

            'name.required' => 'Name is required.',
            'name.max' => 'Name is too long.',

            'email.required' => 'Email is required.',
            'email.email' => 'Email format is invalid.',
            'email.unique' => 'This email is already in use.',

            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
        ];
    }
}
