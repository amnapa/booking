<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomScheduleResource extends JsonResource
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
                'schedule_id' => $this->id,
                'room_name' => $this->room->name,
                'hotel_name' => $this->room->hotel->name,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'price' => $this->price,
                'cancellation_penalty' => $this->cancellation_penalty_percentage . '%',
                'last_updated' => $this->updated_at->diffForHumans(),
            ]
        ];
    }
}
