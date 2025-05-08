<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'stay_status',
        'package_id',
        'booking_type',
        'duration_type',
        'payment_status',
        'occupied_address',
        'entry_date',
        'package',
        'package_price',
        'security_amount',
        'security_payment_status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
