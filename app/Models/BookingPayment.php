<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'milestone_type',
        'milestone_number',
        'due_date',
        'amount',
        'payment_status',
        'payment_method',
        'transaction_reference',
        'paid_at'
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function paymentLink()
    {
        return $this->hasOne(PaymentLink::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
