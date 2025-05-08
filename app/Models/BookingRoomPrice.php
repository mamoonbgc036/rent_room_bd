<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRoomPrice extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'room_id',
        'price_type',
        'fixed_price',
        'discount_price',
        'booking_price'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
