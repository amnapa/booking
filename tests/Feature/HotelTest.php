<?php

namespace Tests\Feature;

use App\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class HotelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_hotel_can_be_stored()
    {
        $response = $this->post('/api/hotels', $this->data());

        $hotel = Hotel::first();

        $this->assertCount(1, Hotel::all());
        $this->assertEquals('Hotel Name', $hotel->name);
        $this->assertEquals('hotel-slug', $hotel->slug);
        $this->assertEquals('Hotel Description', $hotel->description);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'hotel_id' => $hotel->id,
            ]
        ]);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_hotel_can_be_retrieved()
    {
        $hotel = factory(Hotel::class)->create();

        $response = $this->get('/api/hotels/' . $hotel->id);

        $response->assertJson([
            'data' => [
                'hotel_id' => $hotel->id,
                'name' => $hotel->name,
                'slug' => $hotel->slug,
                'description' => $hotel->description,
                'last_updated' => $hotel->updated_at->diffForHumans(),
            ]
        ]);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_hotel_can_be_updated()
    {
        $hotel = factory(Hotel::class)->create();

        $response = $this->patch('/api/hotels/' . $hotel->id, $this->data());

        $hotel = $hotel->fresh();

        $this->assertCount(1, Hotel::all());
         $this->assertEquals('Hotel Name', $hotel->name);
        $this->assertEquals('hotel-slug', $hotel->slug);
        $this->assertEquals('Hotel Description', $hotel->description);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'hotel_id' => $hotel->id,
            ]
        ]);
    }


    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_hotel_can_be_deleted()
    {
        $hotel = factory(Hotel::class)->create();

        $response = $this->delete('/api/hotels/' . $hotel->id);

        $this->assertCount(0, Hotel::all());

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_list_of_hotels_can_be_retrieved()
    {
        $hotel = factory(Hotel::class)->create();
        factory(Hotel::class)->create();

        $this->assertCount(2, Hotel::all());

        $response = $this->get('/api/hotels');

        //Considering pagination, the response has three elements
        $response->assertJsonCount(3)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'hotel_id' => $hotel->id
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
        collect(['name', 'slug', 'description'])
            ->each(function ($field) {
                $response = $this->post('/api/hotels', array_merge($this->data(), [$field => '']));

                $response->assertSessionHasErrors($field);
                $this->assertCount(0, Hotel::all());
            });
    }


    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function a_slug_should_be_unique()
    {
        $this->post('/api/hotels', $this->data());
        $this->assertCount(1, Hotel::all());

        $response = $this->post('/api/hotels', $this->data());
        $this->assertCount(1, Hotel::all());
        $response->assertSessionHasErrors();
    }


    /**
     * @return string[]
     */
    private function data(): array
    {
        return [
            'name' => 'Hotel Name',
            'slug' => 'Hotel Slug',
            'description' => 'Hotel Description',
        ];
    }
}
