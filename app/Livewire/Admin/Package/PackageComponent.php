<?php
namespace App\Livewire\Admin\Package;

use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class PackageComponent extends Component
{
    use WithFileUploads;

    public $countries, $cities = [], $areas = [], $properties = [];
    public $maintains, $amenities, $packages;
    public $selectedPackageId;
    public $selectedPackage;
    public $selectedUserId;
    public $showAssignModal = false;

    public $showDeleteModal = false;
    public $packageToDelete = null;

    public function mount()
    {
        $this->refreshPackages();
    }

    private function refreshPackages()
    {
        $user = Auth::user();
        $with = ['creator', 'assignedPartner', 'assignedBy', 'country', 'city', 'area', 'property'];

        if ($user->roles->pluck('name')->contains('Super Admin')) {
            $this->packages = Package::with($with)->get();
        } else {
            $this->packages = Package::with($with)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('assigned_to', $user->id);
                })
                ->get();
        }
    }

    public function assignUser()
    {
        if (empty($this->selectedUserId)) {
            // Handle unassignment
            $this->removeAssignment();
            return;
        }

        $this->validate([
            'selectedUserId' => 'required|exists:users,id',
        ]);

        $package = Package::find($this->selectedPackageId);

        if (! auth()->user()->hasRole('Super Admin') && auth()->id() !== $package->user_id) {
            session()->flash('error', 'You do not have permission to assign this package.');
            return;
        }

        $package->update([
            'assigned_to' => $this->selectedUserId,
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
        ]);

        $this->refreshPackages();
        session()->flash('message', 'Package assigned successfully.');
        $this->closeModal();
    }

    public function removeAssignment()
    {
        $package = Package::find($this->selectedPackageId);

        if (! auth()->user()->hasRole('Super Admin') && auth()->id() !== $package->user_id) {
            session()->flash('error', 'You do not have permission to remove this assignment.');
            return;
        }

        $package->update([
            'assigned_to' => null,
            'assigned_by' => null,
            'assigned_at' => null,
        ]);

        $this->refreshPackages();
        session()->flash('message', 'Package assignment removed successfully.');
        $this->closeModal();
    }

    public function openAssignModal($packageId)
    {
        $this->selectedPackageId = $packageId;
        $this->selectedPackage   = Package::with(['creator', 'assignedPartner', 'assignedBy'])->find($packageId);
        $this->selectedUserId    = $this->selectedPackage->assigned_to; // Set current assignment if exists
        $this->showAssignModal   = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showAssignModal   = false;
        $this->selectedUserId    = null;
        $this->selectedPackageId = null;
        $this->selectedPackage   = null;
        $this->dispatch('modalClosed');
    }

    public function render()
    {
        $availablePartners = User::role('Partner')
            ->when($this->selectedPackageId, function ($query) {
                $package = Package::find($this->selectedPackageId);
                return $query->where('id', '!=', $package->user_id);
            })
            ->get();

        return view('livewire.admin.package.package-component', [
            'availablePartners' => $availablePartners,
            'packages'          => $this->packages,
        ]);
    }

    public function confirmDelete($id)
    {
        $this->packageToDelete = Package::find($id);
        $this->showDeleteModal = true;
        $this->dispatch('deleteModalOpened');
    }

    public function deletePackage()
    {
        if ($this->packageToDelete) {
            $this->packageToDelete->delete();
            $this->refreshPackages();
            session()->flash('message', 'Package deleted successfully.');
            $this->closeDeleteModal();
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->packageToDelete = null;
        $this->dispatch('deleteModalClosed');
    }
}
