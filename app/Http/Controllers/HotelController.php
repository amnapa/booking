<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\HotelResource;
use App\Hotel;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class HotelController extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $hotels = Hotel::Name(request('name'))
            ->Slug(request('slug'))
            ->with('room')
            ->paginate();

        return HotelResource::collection($hotels);
    }

    public function store()
    {
        $hotel = Hotel::create($this->validatedData());

        return (new HotelResource($hotel))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Hotel $hotel)
    {
        return new HotelResource($hotel);
    }

    public function update(Hotel $hotel)
    {
        $data = $this->validatedData($hotel->id);

        $hotel->update($data);

        return (new HotelResource($hotel))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();

        return response(['data' => 'Successfully deleted the product'], Response::HTTP_NO_CONTENT);
    }

    /**
     * @param mixed $hotelId
     * @return array
     */
    private function validatedData($hotelId = ''): array
    {
        $this->request['slug'] = Str::slug($this->request['slug'], '-');

        return request()->validate([
            'name' => 'required',
            'slug' => 'required|unique:hotels,slug,' . $hotelId,
            'description' => 'required',
        ]);
    }
}
