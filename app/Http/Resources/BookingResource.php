<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'booking_id' => $this->id,
                'room_name' => $this->room->name,
                'hotel_name' => $this->room->hotel->name,
                'customer' => [
                  'first_name' => $this->first_name,
                  'last_name' => $this->last_name,
                  'email' => $this->email,
                  'phone_number' => $this->phone_number,
                ],
                'checkin_date' => $this->checkin_date,
                'checkout_date' => $this->checkout_date,
                'price' => $this->price,
                'cancellation_penalty' => $this->cancellation_penalty_percentage . '%',
                'reservation_code' => $this->reservation_code,
                'last_updated' => $this->updated_at->diffForHumans(),
            ]
        ];
    }
}
