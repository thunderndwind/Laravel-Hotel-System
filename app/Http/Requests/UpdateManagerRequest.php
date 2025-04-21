<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateManagerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'national_id' => ['required', 'digits:9', Rule::unique('managers', 'national_id')->ignore($this->manager)],
            'phone_number' => ['required'],
            'avatar_image' => ['nullable', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore(optional($this->manager->user)->id)],
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
        ];
    }
}
