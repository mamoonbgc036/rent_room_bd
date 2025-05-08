<?php

namespace App\Livewire\User;

use App\Models\Booking;
use App\Models\Payment;
use Livewire\Component;

class BookingComplete extends Component
{
    public $booking;
    public $payment;
    public $instructions;

    public function mount($bookingId)
    {
        $this->booking = Booking::findOrFail($bookingId);
        $this->payment = Payment::where('booking_id', $this->booking->id)->first();
        $this->instructions = $this->booking->package->instructions()
            ->orderBy('order')
            ->get();
    }

    public function render()
    {
        return view('livewire.user.booking-complete')->layout('layouts.guest');
    }
}
