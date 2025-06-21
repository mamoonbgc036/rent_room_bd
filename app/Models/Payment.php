<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'payment_method',
        'amount',
        'transaction_id',
        'booking_payment_id',
        'payment_type',
        'paymentOption',
        'status'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    public function bookingPayment()
    {
        return $this->belongsTo(BookingPayment::class);
    }
}
