<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLink extends Model
{
    protected $fillable = [
        'unique_id',
        'booking_id',
        'user_id',
        'booking_payment_id',
        'amount',
        'status',
        'transaction_id'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookingPayment()
    {
        return $this->belongsTo(BookingPayment::class, 'booking_payment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
