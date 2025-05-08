<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingMaintain extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'maintain_id',
        'price'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function maintain()
    {
        return $this->belongsTo(Maintain::class);
    }
}
