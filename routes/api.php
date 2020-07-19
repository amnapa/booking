<?php

use Illuminate\Support\Facades\Route;

Route::resource('hotels','HotelController');
Route::resource('rooms','RoomController');
Route::resource('room-schedules','RoomScheduleController');
Route::resource('bookings','BookingController');
