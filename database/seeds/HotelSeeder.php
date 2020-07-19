<?php

use Illuminate\Database\Seeder;
use App\Hotel;
use App\Room;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hotels = factory(Hotel::class, 1000)->create();

        foreach ($hotels as $hotel) {

            factory(Room::class, rand(10,25))
                ->create([
                    'hotel_id' => $hotel->id,
                ]);

            /*foreach ($rooms as $room) {
                for ($i = 0; $i < rand(0, 1); $i++) {
                    $room->schedule()->save(
                        factory(RoomSchedule::class)->create()
                    );
                }*/
            }
        }
    }
