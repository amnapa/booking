<?php

namespace App\Http\Controllers;

use App\Room;
use App\RoomSchedule;
use App\Http\Resources\RoomScheduleResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoomScheduleController extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $roomSchedules = RoomSchedule::with(['room', 'room.hotel'])->paginate();

        return RoomScheduleResource::collection($roomSchedules);
    }

    public function store()
    {
        $data = $this->validatedData();

        $room = Room::find($this->request->room_id);

        if ($room instanceof Room) {
            $roomSchedule = $room->schedule()->create($data);

            return (new RoomScheduleResource($roomSchedule))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } else {
            return response(['error' => 'No room found with id of: ' . $this->request->room_id])
                ->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function show(RoomSchedule $roomSchedule)
    {
        return new RoomScheduleResource($roomSchedule);
    }

    public function update(RoomSchedule $roomSchedule)
    {
        $data = $this->validatedData();

        $roomSchedule->update($data);

        return (new RoomScheduleResource($roomSchedule))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(RoomSchedule $roomSchedule)
    {
        $roomSchedule->delete();

        return response(['data' => 'Successfully deleted the product'], Response::HTTP_NO_CONTENT);
    }

    /**
     * @return array
     */
    private function validatedData(): array
    {
        return request()->validate([
            'price' => 'required|numeric',
            'start_date' => 'required|date_format:Y-m-d H:i||before:end_date',
            'end_date' => 'required|date_format:Y-m-d H:i',
            'cancellation_penalty_percentage' => 'required|numeric|between:0,100',
        ]);
    }
}
