<?php

namespace App\Livewire\User;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;

class BookingList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with('package')
            ->latest()
            ->paginate(10);

        return view('livewire.user.booking-list', [
            'bookings' => $bookings
        ]);
    }
}
