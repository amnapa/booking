<?php

namespace Tests\Feature;

use App\Hotel;
use App\Room;
use App\RoomSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RoomScheduleTest extends TestCase
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
    public function a_room_schedule_can_be_stored()
    {
        $response = $this->post('/api/room-schedules', array_merge($this->data(), ['room_id' => $this->room->id]));

        $roomSchedule= RoomSchedule::first();

        $this->assertCount(1, RoomSchedule::all());
        $this->assertEquals($this->room->name, $roomSchedule->room->name);
        $this->assertEquals('2020-08-01 10:00', $roomSchedule->start_date);
        $this->assertEquals('2020-08-20 10:00', $roomSchedule->end_date);
        $this->assertEquals(100, $roomSchedule->price);
        $this->assertEquals(20, $roomSchedule->cancellation_penalty_percentage);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'schedule_id' => $roomSchedule->id,
                'room_name' => $roomSchedule->room->name,
            ]
        ]);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_room_schedule_can_be_retrieved()
    {
        $roomSchedule = factory(RoomSchedule::class)->create(['room_id' => $this->room->id]);

        $response = $this->get('/api/room-schedules/' . $roomSchedule->id);

        $response->assertJson([
            'data' => [
                'schedule_id' => $roomSchedule->id,
                'room_name' => $roomSchedule->room->name,
                'price' => $roomSchedule->price,
                'cancellation_penalty' => $roomSchedule->cancellation_penalty_percentage . '%',
                'last_updated' => $roomSchedule->updated_at->diffForHumans(),
            ]
        ]);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_room_schedule_can_be_updated()
    {
        $roomSchedule = factory(RoomSchedule::class)->create(['room_id' => $this->room->id]);

        $response = $this->patch('/api/room-schedules/' . $roomSchedule->id, $this->data());

        $roomSchedule = $roomSchedule->fresh();

        $this->assertCount(1, RoomSchedule::all());
        $this->assertEquals($this->room->name, $roomSchedule->room->name);
        $this->assertEquals('2020-08-01 10:00', $roomSchedule->start_date);
        $this->assertEquals('2020-08-20 10:00', $roomSchedule->end_date);
        $this->assertEquals(100, $roomSchedule->price);
        $this->assertEquals(20, $roomSchedule->cancellation_penalty_percentage);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'schedule_id' => $roomSchedule->id,
            ]
        ]);
    }


    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_room_schedule_can_be_deleted()
    {
        $roomSchedule = factory(RoomSchedule::class)->create(['room_id' => $this->room->id]);

        $response = $this->delete('/api/room-schedules/' . $roomSchedule->id);

        $this->assertCount(0, RoomSchedule::all());

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
        $roomSchedule = factory(RoomSchedule::class)->create(['room_id' => $this->room->id]);
        factory(RoomSchedule::class)->create(['room_id' => $this->room->id]);

        $this->assertCount(2, RoomSchedule::all());

        $response = $this->get('/api/room-schedules');

        //Considering pagination, the response has three elements
        $response->assertJsonCount(3)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'schedule_id' => $roomSchedule->id
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
        collect(['start_date', 'end_date', 'price', 'cancellation_penalty_percentage'])
            ->each(function ($field) {
                $response = $this->post('/api/room-schedules', array_merge($this->data(), [$field => '']));

                $response->assertSessionHasErrors($field);
                $this->assertCount(0, RoomSchedule::all());
            });
    }

    /**
     * @return string[]
     */
    private function data(): array
    {
        return [
            'start_date' => '2020-08-01 10:00',
            'end_date' => '2020-08-20 10:00',
            'price' => '100',
            'cancellation_penalty_percentage' => '20',
        ];
    }
}
