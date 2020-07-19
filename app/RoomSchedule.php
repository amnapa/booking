<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = ['start_date','end_date','price', 'cancellation_penalty_percentage'];

    //Define One to Many inverse relationship between RoomSchedule and Room Models
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
