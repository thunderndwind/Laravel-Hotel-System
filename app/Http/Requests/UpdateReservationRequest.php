<?php

namespace App\Http\Requests;

use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class UpdateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('reservation'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $reservation = $this->route('reservation');

        // Clients can only update check-in/out dates for upcoming reservations
        if ($this->user()->hasRole('Client')) {
            return [
                'check_in_date' => [
                    'required',
                    'date',
                    'after_or_equal:today',
                    function ($attribute, $value, $fail) use ($reservation) {
                        $this->validateRoomAvailability($reservation, $value, $this->check_out_date, $fail);
                    }
                ],
                'check_out_date' => [
                    'required',
                    'date',
                    'after:check_in_date',
                ],
            ];
        }

        // Admin/Receptionist can update more fields
        return [
            'room_id' => [
                'sometimes',
                'exists:rooms,id',
                function ($attribute, $value, $fail) use ($reservation) {
                    if ($value != $reservation->room_id) {
                        $this->validateRoomAvailability(
                            $reservation,
                            $this->input('check_in_date', $reservation->check_in_date),
                            $this->input('check_out_date', $reservation->check_out_date),
                            $fail,
                            $value
                        );
                    }
                }
            ],
            'check_in_date' => [
                'sometimes',
                'date',
                function ($attribute, $value, $fail) use ($reservation) {
                    // Only validate date if it's changed
                    if (Carbon::parse($value)->ne(Carbon::parse($reservation->check_in_date))) {
                        $this->validateRoomAvailability(
                            $reservation,
                            $value,
                            $this->input('check_out_date', $reservation->check_out_date),
                            $fail
                        );
                    }
                }
            ],
            'check_out_date' => [
                'sometimes',
                'date',
                'after:check_in_date',
                function ($attribute, $value, $fail) use ($reservation) {
                    // Only validate date if it's changed
                    if (Carbon::parse($value)->ne(Carbon::parse($reservation->check_out_date))) {
                        $this->validateRoomAvailability(
                            $reservation,
                            $this->input('check_in_date', $reservation->check_in_date),
                            $value,
                            $fail
                        );
                    }
                }
            ],
            'accompany_number' => [
                'sometimes',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($reservation) {
                    $roomId = $this->input('room_id', $reservation->room_id);
                    $room = Room::find($roomId);
                    if ($room && $value > $room->capacity - 1) {
                        $fail('The number of accompanying guests exceeds the room capacity (max: ' . ($room->capacity - 1) . ').');
                    }
                }
            ],
            'paid_price' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
        ];
    }

    /**
     * Validate room availability for the given dates
     */
    protected function validateRoomAvailability(Reservation $reservation, $checkIn, $checkOut, $fail, $roomId = null)
    {
        $roomId = $roomId ?? $reservation->room_id;
        $room = Room::find($roomId);

        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);

        $existing = $room->reservations()
            ->where('id', '!=', $reservation->id)
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
}
