<?php

namespace App\Http\Requests\Floor;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFloorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get valid manager IDs first
        $managerIds = User::role(['Admin', 'Manager'])->pluck('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'manager_id' => [
                'required',
                Rule::in($managerIds), // Use Rule::in() instead of exists with where clause
            ],
            // Number is auto-generated in the model, but we can validate it if provided
            'number' => ['sometimes', 'integer', 'min:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The floor name is required.',
            'name.max' => 'The floor name must not exceed 255 characters.',
            'manager_id.required' => 'Please select a manager for this floor.',
            'manager_id.in' => 'The selected manager is invalid.',
        ];
    }
}
