<?php

namespace Tests\Feature;

use App\Room;
use App\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = factory(Hotel::class)->create();
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_room_can_be_stored()
    {
        $response = $this->post('/api/rooms', array_merge($this->data(), ['hotel_id' => $this->hotel->id]));

        $room = Room::first();

        $this->assertCount(1, Room::all());
        $this->assertEquals('Room Name', $room->name);
        $this->assertEquals($this->hotel->name, $room->hotel->name);
        $this->assertEquals('single', $room->type);
        $this->assertEquals(1, $room->capacity);
        $this->assertEquals(500, $room->price);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'room_id' => $room->id,
                'hotel_name' => $room->hotel->name,
            ]
        ]);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_room_can_be_retrieved()
    {
        $room = factory(Room::class)->create(['hotel_id' => $this->hotel->id]);

        $response = $this->get('/api/rooms/' . $room->id);

        $response->assertJson([
            'data' => [
                'room_id' => $room->id,
                'hotel_name' => $room->hotel->name,
                'name' => $room->name,
                'type' => $room->type,
                'capacity' => $room->capacity,
                'price' => $room->price,
                'last_updated' => $room->updated_at->diffForHumans(),
            ]
        ]);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_room_can_be_updated()
    {
        $room = factory(Room::class)->create(['hotel_id' => $this->hotel->id]);

        $response = $this->patch('/api/rooms/' . $room->id, $this->data());

        $room = $room->fresh();

        $this->assertCount(1, Room::all());
        $this->assertEquals('Room Name', $room->name);
        $this->assertEquals($this->hotel->name, $room->hotel->name);
        $this->assertEquals('single', $room->type);
        $this->assertEquals(1, $room->capacity);
        $this->assertEquals(500, $room->price);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'room_id' => $room->id,
            ]
        ]);
    }


    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_room_can_be_deleted()
    {
        $room = factory(Room::class)->create(['hotel_id' => $this->hotel->id]);

        $response = $this->delete('/api/rooms/' . $room->id);

        $this->assertCount(0, Room::all());

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
        $room = factory(Room::class)->create(['hotel_id' => $this->hotel->id]);
        factory(Room::class)->create(['hotel_id' => $this->hotel->id]);

        $this->assertCount(2, Room::all());

        $response = $this->get('/api/rooms');

        //Considering pagination, the response has three elements
        $response->assertJsonCount(3)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'room_id' => $room->id
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
        collect(['name', 'type', 'capacity', 'price'])
            ->each(function ($field) {
                $response = $this->post('/api/rooms', array_merge($this->data(), [$field => '']));

                $response->assertSessionHasErrors($field);
                $this->assertCount(0, Room::all());
            });
    }

    /**
     * @return string[]
     */
    private function data(): array
    {
        return [
            'name' => 'Room Name',
            'type' => 'single',
            'price' => '500',
            'capacity' => '1',
        ];
    }
}
