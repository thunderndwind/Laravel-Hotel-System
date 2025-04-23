<?php

namespace App\Http\Requests\Room;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
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
            'price' => ['required', 'integer', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1'],
            'manager_id' => [
                'required',
                Rule::in($managerIds),
            ],
            'floor_id' => ['required', 'exists:floors,id'],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'price.required' => 'The room price is required.',
            'price.min' => 'The room price must be at least 0.',
            'capacity.required' => 'The room capacity is required.',
            'capacity.min' => 'The room capacity must be at least 1.',
            'manager_id.required' => 'Please select a manager for this room.',
            'manager_id.in' => 'The selected manager is invalid.',
            'floor_id.required' => 'Please select a floor for this room.',
            'floor_id.exists' => 'The selected floor is invalid.',
        ];
    }
}
