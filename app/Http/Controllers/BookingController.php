<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookingResource;
use App\Jobs\ProcessBooking;
use App\Room;
use App\Booking;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $bookings = Booking::with(['room', 'room.hotel'])
            ->reservationCode($this->request->reservation_code)
            ->customerName($this->request->customer_name)
            ->customerEmail($this->request->customer_email)
            ->paginate();

        return BookingResource::collection($bookings);
    }

    public function store()
    {
        $data = $this->validatedData();

        $room = Room::find($this->request->room_id);

        if ($room instanceof Room && $room->IsAvailable($this->request->checkin_date, $this->request->checkout_date)) {
            $booking = $room->booking()->create($data);

            //Process booking by calling booking API in background
            ProcessBooking::dispatch($booking);

            return (new BookingResource($booking))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } else {
            return response(['error' => 'No room found or available for booking'])
                ->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function show(Booking $booking)
    {
        return new BookingResource($booking);
    }

    public function update(Booking $booking)
    {
        $data = $this->validatedData();

        $booking->update($data);

        return (new BookingResource($booking))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return response(['data' => 'Successfully deleted the product'], Response::HTTP_NO_CONTENT);
    }

    /**
     * @return array
     */
    private function validatedData(): array
    {
        return request()->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'checkin_date' => 'required|date_format:Y-m-d H:i|before:checkout_date',
            'checkout_date' => 'required|date_format:Y-m-d H:i',
        ]);
    }
}
