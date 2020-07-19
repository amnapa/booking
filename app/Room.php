<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'type', 'capacity', 'price'];

    //Define One to Many inverse relationship between Room and Hotel Models
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    //Define One to Many relationship between Room and RoomSchedule Models
    public function schedule()
    {
        return $this->hasMany(RoomSchedule::class);
    }

    //Find schedules for current Room model in given interval
    public function scheduledBetween($startDate, $endDate)
    {
        return $this->hasMany(RoomSchedule::class)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($query) use ($startDate) {
                    $query->where('start_date', '<=', $startDate)
                        ->where('end_date', '>', $startDate);
                })->orWhere(function ($query) use ($endDate) {
                    $query->where('start_date', '<=', $endDate)
                        ->where('end_date', '>', $endDate);
                });
            });
    }

    //Define One to Many relationship between Room and Booking Models
    public function booking()
    {
        return $this->hasMany(Booking::class);
    }

    //Scope search
    public function scopeName($query, $name)
    {
        if ($name)
            $query->where('name', 'LIKE', '%' . $name . '%');
    }

    //Find Room Models based on type
    public function scopeType($query, $type)
    {
        if ($type)
            $query->where('type', $type);
    }

    //Check if Room is not already reserved and is available for booking
    public function scopeIsAvailable($query, $startDate, $endDate)
    {
        return $query->where('id', $this->id)->whereHas('booking', function ($query) use ($startDate, $endDate) {
            $query->where(function ($query) use ($startDate, $endDate) {
                $query->where('checkin_date', '<=', $startDate)
                    ->where('checkout_date', '>', $startDate);
            })->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('checkin_date', '<=', $endDate)
                    ->where('checkout_date', '>', $endDate);
            })->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('checkin_date', '>=', $startDate)
                    ->where('checkout_date', '<=', $endDate);
            });
        })->doesntExist();
    }
}
