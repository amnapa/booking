<?php

namespace Tests\Feature;

use App\Hotel;
use App\Jobs\ProcessBooking;
use App\Room;
use App\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->room = factory(Room::class)->create([
            'hotel_id' => factory(Hotel::class)->create()
        ]);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_booking_can_be_stored()
    {
        $this->withoutExceptionHandling();

        Queue::fake();

        $response = $this->post('/api/bookings', array_merge($this->data(), ['room_id' => $this->room->id]));

        Queue::assertPushed(ProcessBooking::class);

        $booking = Booking::first();

        $this->assertCount(1, Booking::all());
        $this->assertEquals($this->room->name, $booking->room->name);
        $this->assertEquals('John', $booking->first_name);
        $this->assertEquals('Doe', $booking->last_name);
        $this->assertEquals('customer@gmail.com', $booking->email);
        $this->assertEquals('22222222', $booking->phone_number);
        $this->assertEquals('2020-08-01 10:00', $booking->checkin_date);
        $this->assertEquals('2020-08-20 10:00', $booking->checkout_date);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'booking_id' => $booking->id,
                'room_name' => $booking->room->name,
            ]
        ]);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_booking_can_be_retrieved()
    {
        $booking = factory(Booking::class)->create(['room_id' => $this->room->id]);

        $response = $this->get('/api/bookings/' . $booking->id);

        $response->assertJson([
            'data' => [
                'booking_id' => $booking->id,
                'room_name' => $booking->room->name,
                'customer' => [
                    'first_name' => $booking->first_name,
                    'last_name' => $booking->last_name,
                    'email' => $booking->email,
                    'phone_number' => $booking->phone_number,
                ],
                'price' => $booking->price,
                'reservation_code' => $booking->reservation_code,
                'last_updated' => $booking->updated_at->diffForHumans(),
            ]
        ]);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_booking_can_be_updated()
    {
        $booking = factory(Booking::class)->create(['room_id' => $this->room->id]);

        $response = $this->patch('/api/bookings/' . $booking->id, $this->data());

        $booking = $booking->fresh();

        $this->assertCount(1, Booking::all());
        $this->assertEquals($this->room->name, $booking->room->name);
        $this->assertEquals('John', $booking->first_name);
        $this->assertEquals('Doe', $booking->last_name);
        $this->assertEquals('customer@gmail.com', $booking->email);
        $this->assertEquals('22222222', $booking->phone_number);
        $this->assertEquals('2020-08-01 10:00', $booking->checkin_date);
        $this->assertEquals('2020-08-20 10:00', $booking->checkout_date);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'booking_id' => $booking->id,
            ]
        ]);
    }


    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_booking_can_be_deleted()
    {
        $booking = factory(Booking::class)->create(['room_id' => $this->room->id]);

        $response = $this->delete('/api/bookings/' . $booking->id);

        $this->assertCount(0, Booking::all());

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_list_of_rooms_can_be_retrieved()
    {
        $booking = factory(Booking::class)->create(['room_id' => $this->room->id]);
        factory(Booking::class)->create(['room_id' => $this->room->id]);

        $this->assertCount(2, Booking::all());

        $response = $this->get('/api/bookings');

        //Considering pagination, the response has three elements
        $response->assertJsonCount(3)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'booking_id' => $booking->id
                        ],
                    ]
                ]
            ]);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function fields_are_required()
    {
        collect(['checkin_date', 'checkout_date', 'first_name', 'last_name', 'email', 'phone_number'])
            ->each(function ($field) {
                $response = $this->post('/api/bookings', array_merge($this->data(), [$field => '']));

                $response->assertSessionHasErrors($field);
                $this->assertCount(0, Booking::all());
            });
    }

    /**
     * @return string[]
     */
    private function data(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'customer@gmail.com',
            'phone_number' => '22222222',
            'checkin_date' => '2020-08-01 10:00',
            'checkout_date' => '2020-08-20 10:00',
        ];
    }
}
