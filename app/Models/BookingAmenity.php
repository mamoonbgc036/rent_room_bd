<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingAmenity extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'amenity_id',
        'price'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function amenity()
    {
        return $this->belongsTo(Amenity::class);
    }
}
