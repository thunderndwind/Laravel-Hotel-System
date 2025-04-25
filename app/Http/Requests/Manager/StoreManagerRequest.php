<?php

namespace App\Http\Requests\Manager;


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
            'national_id' => ['required', 'string', 'min:15', 'max:25', 'unique:managers,national_id'],
            'phone_number' => ['required'],
            'avatar_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'national_id.required' => 'National ID is required.',
            'national_id.min' => 'National ID must be at least 15 characters.',
            'national_id.max' => 'National ID must not exceed 25 characters.',
            'national_id.unique' => 'This national ID is already in use.',

            'phone_number.required' => 'Phone number is required.',

            'name.required' => 'Name is required.',
            'name.max' => 'Name is too long.',

            'email.required' => 'Email is required.',
            'email.email' => 'Email format is invalid.',
            'email.unique' => 'This email is already in use.',

            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',

            'avatar_image.image' => 'Avatar image must be an image file.',
            'avatar_image.mimes' => 'Avatar image must be a file of type: jpeg, png, jpg, gif.',
            'avatar_image.max' => 'Avatar image size must not exceed 2MB.',
        ];
    }
}
