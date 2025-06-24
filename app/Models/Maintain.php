<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintain extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'maintain_type_id', 'name', 'photo'];

    public function maintainType()
    {
        return $this->belongsTo(MaintainType::class);
    }
    public function packages()
    {
        return $this->belongsToMany(Package::class)
            ->withPivot('is_paid', 'price', 'user_id')
            ->withTimestamps();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_maintains')
            ->withPivot('price')
            ->withTimestamps();
    }
}
