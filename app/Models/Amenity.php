<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'amenity_type_id', 'name'];

    public function amenityType()
    {
        return $this->belongsTo(AmenityType::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_amenities')
            ->withPivot('price')
            ->withTimestamps();
    }
}
