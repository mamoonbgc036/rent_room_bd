<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'package_id',
        'name',
        'number_of_beds',
        'number_of_bathrooms',
        'day_deposit',
        'weekly_deposit',
        'monthly_deposit'
    ];

    protected $appends = ['total_amount'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function prices()
    {
        return $this->hasMany(RoomPrice::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function roomPrices()
    {
        return $this->hasMany(RoomPrice::class);
    }

    public function bookingRoomPrices()
    {
        return $this->hasMany(BookingRoomPrice::class);
    }
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'booking_rooms')
            ->withPivot(['room_type', 'price'])
            ->withTimestamps();
    }

    /**
     * Get the total amount (base price + booking price).
     *
     * @return float
     */
    public function getTotalAmountAttribute()
    {
        // If you have a room_price table
        $latestPrice = $this->roomPrices()->latest()->first();

        if ($latestPrice) {
            return $latestPrice->fixed_price + $latestPrice->booking_price;
        }

        // If price is directly on rooms table
        return ($this->price ?? 0) + ($this->booking_price ?? 0);
    }

    public function getRoomsAttribute()
    {
        $roomIds = json_decode($this->room_ids, true) ?? [];
        return Room::whereIn('id', $roomIds)->get();
    }
}
