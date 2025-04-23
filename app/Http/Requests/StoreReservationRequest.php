<?php

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Reservation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'room_id' => [
                'required',
                'exists:rooms,id',
                function ($attribute, $value, $fail) {
                    // Check if room is available for the selected dates
                    $room = Room::find($value);
                    $checkIn = Carbon::parse($this->input('check_in_date'));
                    $checkOut = Carbon::parse($this->input('check_out_date'));

                    $existing = $room->reservations()
                        ->where(function ($query) use ($checkIn, $checkOut) {
                            $query->where(function ($q) use ($checkIn, $checkOut) {
                                // Check if there's any reservation overlapping with our dates
                                $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                                    ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                                    ->orWhere(function ($subQuery) use ($checkIn, $checkOut) {
                                        $subQuery->where('check_in_date', '<=', $checkIn)
                                            ->where('check_out_date', '>=', $checkOut);
                                    });
                            });
                        })->exists();

                    if ($existing) {
                        $fail('The room is not available for the selected dates.');
                    }
                }
            ],
            'check_in_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'check_out_date' => [
                'required',
                'date',
                'after:check_in_date',
            ],
            'accompany_number' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->input('room_id')) {
                        $room = Room::find($this->input('room_id'));
                        if ($room && $value > $room->capacity - 1) {
                            $fail('The number of accompanying guests exceeds the room capacity (max: ' . ($room->capacity - 1) . ').');
                        }
                    }
                }
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'check_in_date.after_or_equal' => 'The check-in date must be today or a future date.',
            'check_out_date.after' => 'The check-out date must be after the check-in date.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->user()->id,
        ]);
    }
}
