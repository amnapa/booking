<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
                'room_id' => $this->id,
                'name' => $this->name,
                'hotel_name' => $this->hotel->name,
                'type' => $this->type,
                'capacity' => $this->capacity,
                'price' => $this->price,
                'last_updated' => $this->updated_at->diffForHumans(),
                'schedules' => RoomScheduleResource::collection($this->schedule),
            ]
        ];
    }
}
