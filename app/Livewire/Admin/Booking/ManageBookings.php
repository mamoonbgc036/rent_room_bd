<?php

namespace App\Livewire\Admin\Booking;

use App\Models\Booking;
use App\Models\Package;
use App\Models\RoomPrice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManageBookings extends Component
{
    public $name;
    public $phone;
    public $email;
    public $package_id;
    public $payment_option;
    public $booking_amount;
    public $full_payment_amount;
    public $payment_status;
    public $packages;
    public $roomPrices;

    public function mount()
    {
        $user = Auth::user();

        if ($user->roles->pluck('name')->contains('Super Admin')) {
            $this->packages = Package::all();
        } else {
            $this->packages = Package::where('user_id', $user->id)->get();
        }
    }


    public function updatedPackageId($value)
    {
        if ($value) {
            $this->roomPrices = RoomPrice::whereHas('room', function ($query) use ($value) {
                $query->where('package_id', $value);
            })->get();
        } else {
            $this->roomPrices = [];
        }
    }
    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'package_id' => 'required|exists:packages,id',
        'payment_option' => 'required|in:Booking Amount,Full Payment',
        'booking_amount' => 'nullable|required_if:payment_option,Booking Amount|numeric|min:0',
        'full_payment_amount' => 'nullable|required_if:payment_option,Full Payment|numeric|min:0',
        'payment_status' => 'required|in:Due,Paid',
    ];

    public function saveBooking()
    {
        $this->validate();

        Booking::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'package_id' => $this->package_id,
            'payment_option' => $this->payment_option,
            'booking_amount' => $this->booking_amount,
            'full_payment_amount' => $this->full_payment_amount,
            'payment_status' => $this->payment_status,
        ]);

        session()->flash('message', 'Booking created successfully.');

        return redirect()->route('admin.bookings');
    }

    public function render()
    {
        return view('livewire.admin.booking.manage-bookings');
    }
}
