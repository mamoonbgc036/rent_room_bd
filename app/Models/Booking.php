<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'package_id',
        'from_date',
        'to_date',
        'milestone_breakdown' => 'array',
        'room_ids',
        'number_of_days',
        'price_type',
        'price',
        'booking_price',
        'payment_option',
        'total_amount',
        'status',
        'payment_status',
        'auto_renewal',
        'renewal_period_days',
        'next_renewal_date',
        'renewal_status'
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'room_ids' => 'json',
        'auto_renewal' => 'boolean',
        'renewal_period_days' => 'integer',
        'next_renewal_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentsPackage()
    {
        return $this->hasMany(PackagePayment::class);
    }

    public function bookingAmenities()
    {
        return $this->hasMany(BookingAmenity::class);
    }

    public function bookingMaintains()
    {
        return $this->hasMany(BookingMaintain::class);
    }

    public function bookingRoomPrices()
    {
        return $this->hasMany(BookingRoomPrice::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'booking_amenities')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function maintains()
    {
        return $this->belongsToMany(Maintain::class, 'booking_maintains')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function bookingPayments()
    {
        return $this->hasMany(BookingPayment::class);
    }
    public function paymentLinks()
    {
        return $this->hasMany(PaymentLink::class);
    }


    public function generateMilestonePayments()
    {
        $startDate = Carbon::parse($this->from_date);
        $totalAmount = $this->total_amount;
        $bookingFee = $this->booking_price;

        $firstMilestoneAmount = $this->milestone_amount + $bookingFee;

        for ($i = 0; $i < $this->total_milestones; $i++) {
            $dueDate = match ($this->price_type) {
                'Month' => $startDate->copy()->addMonths($i),
                'Week' => $startDate->copy()->addWeeks($i),
                'Day' => $startDate->copy()->addDays($i)
            };

            $milestoneAmount = $i === 0 ? $firstMilestoneAmount : $this->milestone_amount;

            $this->payments()->create([
                'milestone_type' => $this->price_type,
                'milestone_number' => $i + 1,
                'due_date' => $dueDate,
                'amount' => $milestoneAmount,
                'payment_status' => $i === 0 ? 'paid' : 'pending',
                'is_booking_fee' => $i === 0 ? true : false
            ]);
        }
    }

   /**
     * Scope a query to only include active bookings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope a query to only include upcoming bookings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming(Builder $query)
    {
        return $query->where('payment_status', 'pending');
    }

}
