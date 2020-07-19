<?php

namespace App\Http\Controllers;

use App\Hotel;
use App\Room;
use App\Http\Resources\RoomResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoomController extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $rooms = Room::Name($this->request->name)
            ->Type($this->request->type)
            ->with(['hotel', 'schedule'])->paginate();

        return RoomResource::collection($rooms);
    }

    public function store()
    {
        $data = $this->validatedData();

        $hotel = Hotel::find($this->request->hotel_id);

        if ($hotel instanceof Hotel) {
            $room = $hotel->room()->create($data);

            return (new RoomResource($room))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } else {
            return response(['error' => 'No hotel found with id of: ' . $this->request->hotel_id])
                ->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function show(Room $room)
    {
        return new RoomResource($room);
    }

    public function update(Room $room)
    {
        $data = $this->validatedData();

        $room->update($data);

        return (new RoomResource($room))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return response(['data' => 'Successfully deleted the product'], Response::HTTP_NO_CONTENT);
    }

    /**
     * @return array
     */
    private function validatedData(): array
    {
        return request()->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'capacity' => 'required|numeric',
            'type' => 'required|in:single,double,twin,triple,suite',
        ]);
    }
}
