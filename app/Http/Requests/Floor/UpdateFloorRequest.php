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
        ];
    }
}
