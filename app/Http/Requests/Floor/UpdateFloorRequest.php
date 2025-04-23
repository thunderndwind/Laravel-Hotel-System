<?php

namespace App\Http\Requests\Floor;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFloorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $managerIds = User::role(['Admin', 'Manager'])->pluck('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'manager_id' => [
                'required',
                Rule::in($managerIds),
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
