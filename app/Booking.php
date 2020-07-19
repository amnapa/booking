<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = ['first_name', 'last_name', 'email', 'phone_number', 'checkin_date',
        'checkout_date', 'reference_number'];


    //Define One to Many inverse relationship between Booking and Room Models
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    protected static function boot()
    {
        parent::boot();

        //Get booking price and cancellation penalty based on room schedules
        static::creating(function ($booking) {
            $roomSchedule = $booking->room->scheduledBetween($booking->checkin_date, $booking->checkout_date)->first();
            if ($roomSchedule) {
                $price = $roomSchedule->price;
                $cancellationPenalty = $roomSchedule->cancellation_penalty_percentage;
            } else {
                $price = $booking->room->price;
                $cancellationPenalty = 0;
            }

            $booking->price = $price;
            $booking->cancellation_penalty_percentage = $cancellationPenalty;

            //Generate a unique reservation code
            $booking->reservation_code = (string)Str::uuid();
        });
    }

    //Scope search
    public function scopeCustomerName($query, $name)
    {
        if ($name)
            $query->where('first_name', 'LIKE', '%' . $name . '%')
                ->orWhere('last_name', 'LIKE', '%' . $name . '%');
    }

    public function scopeCustomerEmail($query, $email)
    {
        if ($email)
            $query->where('email', 'LIKE', '%' . $email . '%');
    }

    public function scopeReservationCode($query, $code)
    {
        if ($code)
            $query->where('reservation_code', $code);
    }
}
