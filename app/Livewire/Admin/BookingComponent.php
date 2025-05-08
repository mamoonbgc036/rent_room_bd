<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BookingComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $bookings;
    public $showDeleteModal = false;
    public $bookingToDelete;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $filters = [
        'status' => '',
        'date_range' => '',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    private function getBookings()
    {
        $user = Auth::user();
        $query = Booking::query()
            ->with(['user', 'package'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    })
                        ->orWhereHas('package', function ($q) {
                            $q->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere('id', 'like', '%' . $this->search . '%');
                });
            });

        if (!$user->hasRole('Super Admin')) {
            $packageIds = Package::where('user_id', $user->id)->pluck('id');
            $query->whereIn('package_id', $packageIds);
        }

        if ($this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        $bookings = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Load rooms for each booking
        foreach ($bookings as $booking) {
            $roomIds = json_decode($booking->room_ids, true) ?? [];
            $booking->rooms = Room::whereIn('id', $roomIds)->get();
        }

        return $bookings;
    }

    public function confirmDelete($bookingId)
    {
        $booking = Booking::find($bookingId);
        if (!$booking) {
            session()->flash('error', 'Booking not found.');
            return;
        }

        // Check if user has permission to delete this booking
        $user = Auth::user();
        if (!$user->hasRole('Super Admin')) {
            $packageIds = Package::where('user_id', $user->id)->pluck('id');
            if (!$packageIds->contains($booking->package_id)) {
                session()->flash('error', 'You do not have permission to delete this booking.');
                return;
            }
        }

        $this->bookingToDelete = $booking;
        $this->showDeleteModal = true;
    }

    public function deleteBooking()
    {
        if (!$this->bookingToDelete) {
            session()->flash('error', 'No booking selected for deletion.');
            return;
        }

        try {
            \DB::beginTransaction();

            // Delete related records
            \DB::table('booking_room_prices')
                ->where('booking_id', $this->bookingToDelete->id)
                ->delete();

            \DB::table('booking_payments')
                ->where('booking_id', $this->bookingToDelete->id)
                ->delete();

            // Delete payments
            $this->bookingToDelete->payments()->delete();

            // Finally delete the booking
            $this->bookingToDelete->delete();

            \DB::commit();

            session()->flash('success', 'Booking deleted successfully.');
            $this->showDeleteModal = false;
            $this->bookingToDelete = null;
        } catch (\Exception $e) {
            \DB::rollBack();
            session()->flash('error', 'Error deleting booking: ' . $e->getMessage());
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->bookingToDelete = null;
    }

    public function render()
    {
        return view('livewire.admin.booking-component', [
            'bookingsList' => $this->getBookings()
        ]);
    }
}
