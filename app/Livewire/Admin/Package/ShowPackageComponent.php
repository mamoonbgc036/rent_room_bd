<?php

namespace App\Livewire\Admin\Package;

use App\Models\Package;
use App\Models\Booking;
use Livewire\Component;

class ShowPackageComponent extends Component
{
    public $package;
    public $bookings;

    public function mount($packageId)
    {
        $this->package = Package::with([
            'rooms',
            'maintains',
            'amenities',
            'photos',
            'instructions' // Add this
        ])->findOrFail($packageId);
        $this->bookings = Booking::with('user')
            ->where('package_id', $packageId)
            ->orderBy('from_date', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.package.show-package-component');
    }
}
