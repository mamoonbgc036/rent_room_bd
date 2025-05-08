<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPrice extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'room_id',
        'entire_property_id',
        'type',
        'fixed_price',
        'discount_price',
        'booking_price'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function entireProperty()
    {
        return $this->belongsTo(EntireProperty::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
