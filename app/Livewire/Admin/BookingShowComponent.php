<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use Livewire\Component;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use App\Models\Payment;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingShowComponent extends Component
{

    public $booking;
    public $selectedStatus;
    public $bookingDue;
    public $paymentsDue;
    public $dueBill;
    public $rooms = [];
    public bool $canManageAutoRenewal = false;
    public int $renewalPeriodDays = 30;

    protected $rules = [
        'selectedStatus' => 'required|in:approve,pending,decline',
    ];



    public function mount($id)
    {
        $this->booking = Booking::with([
            'package',
            'payments',
            'user', // Add this
            'user.bookings' // Add this for booking stats
        ])->findOrFail($id);


        $this->renewalPeriodDays = (int) ($this->booking->renewal_period_days ?? 30);
        $this->updateCanManageAutoRenewal();

        $this->bookingDue = Booking::findOrFail($id);
        $this->paymentsDue = Payment::where('booking_id', $this->booking->id)
            ->where('status', '!=', 'rejected')
            ->get();
        $this->dueBill = $this->bookingDue->price + $this->bookingDue->booking_price - $this->paymentsDue->sum('amount');
        $this->updateDueBill();

        // Load room information
        $roomIds = json_decode($this->booking->room_ids, true) ?? [];
        $this->rooms = Room::whereIn('id', $roomIds)->get();
    }

    public function toggleAutoRenewal()
    {
        if (!$this->canManageAutoRenewal) {
            session()->flash('error', 'Cannot manage auto-renewal for this booking.');
            return;
        }

        try {
            $newState = !$this->booking->auto_renewal;

            // Only allow auto-renewal for monthly packages
            if ($newState && $this->booking->price_type !== 'Month') {
                session()->flash('error', 'Auto-renewal is only available for monthly packages.');
                return;
            }

            // Calculate next renewal date (7 days before package end)
            $nextRenewalDate = $newState
                ? Carbon::parse($this->booking->to_date)->subDays(7)
                : null;

            $this->booking->update([
                'auto_renewal' => $newState,
                'renewal_period_days' => 30, // Fixed to 30 days for monthly packages
                'next_renewal_date' => $nextRenewalDate,
                'renewal_status' => $newState ? 'pending' : null
            ]);

            session()->flash('message', $newState
                ? "Auto-renewal enabled. Package will be automatically extended by 1 month."
                : 'Auto-renewal disabled successfully.');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update auto-renewal settings: ' . $e->getMessage());
        }
    }

    public function processAutoRenewal()
    {
        if (!$this->booking->auto_renewal || $this->booking->price_type !== 'Month') {
            return;
        }

        DB::beginTransaction();
        try {
            // Get the room with prices
            $roomIds = json_decode($this->booking->room_ids, true);
            $room = Room::with('roomPrices')->find($roomIds[0]);

            if (!$room) {
                throw new \Exception('Room not found');
            }

            // Get monthly price
            $monthlyPrice = $room->roomPrices
                ->where('type', 'Month')
                ->first();

            if (!$monthlyPrice) {
                throw new \Exception('Monthly price not found');
            }

            $price = $monthlyPrice->discount_price ?? $monthlyPrice->fixed_price;

            // Calculate new dates
            $newToDate = Carbon::parse($this->booking->to_date)->addMonth();

            // Create milestone payment
            $milestone = [
                'type' => 'Month',
                'quantity' => 1,
                'price' => $price,
                'total' => $price,
                'description' => $newToDate->format('F Y'),
            ];

            // Update booking
            $this->booking->update([
                'to_date' => $newToDate,
                'number_of_days' => Carbon::parse($this->booking->from_date)->diffInDays($newToDate),
                'price' => $this->booking->price + $price,
                'total_amount' => $this->booking->total_amount + $price,
                'next_renewal_date' => $newToDate->copy()->subDays(7),
                'milestone_breakdown' => array_merge(
                    $this->booking->milestone_breakdown ?? [],
                    [$milestone]
                )
            ]);

            // Create new payment record
            DB::table('booking_payments')->insert([
                'booking_id' => $this->booking->id,
                'milestone_type' => 'Month',
                'milestone_number' => $this->getNextMilestoneNumber(),
                'due_date' => $newToDate->copy()->startOfMonth(),
                'amount' => $price,
                'payment_status' => 'pending',
                'payment_method' => $this->booking->payments->last()?->payment_method ?? 'bank_transfer',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();
            session()->flash('message', 'Booking automatically extended for another month.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Auto-renewal failed: ' . $e->getMessage());
        }
    }

    private function getNextMilestoneNumber()
    {
        return DB::table('booking_payments')
            ->where('booking_id', $this->booking->id)
            ->where('milestone_number', '>', 0)
            ->orderByDesc('milestone_number')
            ->value('milestone_number') + 1;
    }

    protected function updateCanManageAutoRenewal(): void
    {
        if (!$this->booking) {
            $this->canManageAutoRenewal = false;
            return;
        }

        // Allow managing if already enabled
        if ($this->booking->auto_renewal) {
            $this->canManageAutoRenewal = true;
            return;
        }

        $toDate = Carbon::parse($this->booking->to_date);

        $this->canManageAutoRenewal =
            $this->booking->price_type === 'Month' && // Only for monthly packages
            !in_array($this->booking->status, ['cancelled', 'rejected']) &&
            $this->booking->from_date &&
            $this->booking->to_date &&
            $toDate->isFuture();
    }

    public function updateStatus()
    {
        $this->validate();

        $this->booking->update([
            'status' => $this->selectedStatus,
        ]);

        flash()->success('Booking status updated successfully!');
    }

    public function cancelBooking()
    {
        if ($this->booking->status === 'cancelled') {
            flash()->error('Booking is already cancelled.');
            return;
        }

        $this->booking->update(['status' => 'cancelled']);
        flash()->success('Booking cancelled successfully!');
    }

    protected function updateBookingPaymentStatus()
    {
        $completedPayments = $this->booking->payments->where('status', 'completed')->count();
        $totalPayments = $this->booking->payments->count();

        if ($completedPayments === $totalPayments) {
            $this->booking->update(['payment_status' => 'Approved']);
        } elseif ($totalPayments > 0) {
            $this->booking->update(['payment_status' => 'pending']);
        } else {
            $this->booking->update(['payment_status' => 'unpaid']);
        }
    }

    protected function updateDueBill()
    {
        $totalPayments = $this->booking->payments->where('status', '!=', 'rejected')->sum('amount');
        $this->dueBill = $this->booking->price + $this->booking->booking_price - $totalPayments;
    }

    public function generateInvoice()
    {
        $data = ['booking' => $this->booking];
        $pdf = Pdf::loadView('invoice', $data);
        $fileName = 'invoice_' . $this->booking->id . '.pdf';
        $filePath = 'public/invoices/' . $fileName;

        if (!Storage::exists('public/invoices')) {
            Storage::makeDirectory('public/invoices');
        }

        Storage::put($filePath, $pdf->output());

        return response()->streamDownload(
            fn() => Storage::get($filePath),
            $fileName
        );
    }

    public function sendInvoiceEmail()
    {
        $data = ['booking' => $this->booking];
        $pdf = Pdf::loadView('invoice', $data);
        $fileName = 'invoice_' . $this->booking->id . '.pdf';
        $filePath = 'public/invoices/' . $fileName;

        if (!Storage::exists('public/invoices')) {
            Storage::makeDirectory('public/invoices');
        }

        Storage::put($filePath, $pdf->output());

        Mail::to($this->booking->user->email)->send(new InvoiceMail($this->booking, Storage::path($filePath)));

        flash()->success('Invoice sent to customer successfully!');
    }

    public function getRoomsProperty()
    {
        $roomIds = json_decode($this->booking->room_ids, true) ?? [];
        return Room::whereIn('id', $roomIds)->get();
    }

    public function approveBooking()
    {
        try {
            $this->booking->update([
                'status' => 'approved'
            ]);

            session()->flash('message', 'Booking has been approved successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error approving booking: ' . $e->getMessage());
        }
    }

    public function rejectBooking()
    {
        try {
            $this->booking->update([
                'status' => 'rejected'
            ]);

            session()->flash('message', 'Booking has been rejected successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error rejecting booking: ' . $e->getMessage());
        }
    }

    public function getStatusColorProperty()
    {
        return match ($this->booking->status) {
            'approved', 'paid' => '#252525',
            'pending' => '#404040',
            'cancelled', 'rejected' => '#666666',
            default => '#808080',
        };
    }


    public function getCanManageAutoRenewalProperty(): bool
    {
        if (!$this->booking) {
            return false;
        }

        // Allow managing if already enabled
        if ($this->booking->auto_renewal) {
            return true;
        }

        $toDate = \Carbon\Carbon::parse($this->booking->to_date);

        return !in_array($this->booking->status, ['cancelled', 'finished']) &&
            $this->booking->from_date &&
            $this->booking->to_date &&
            $toDate->isFuture();
    }

    public function updateRenewalPeriod()
    {
        $this->validate([
            'renewalPeriodDays' => 'required|integer|min:1|max:365'
        ]);

        try {
            if (!$this->booking->auto_renewal) {
                throw new \Exception('Auto-renewal must be enabled first.');
            }

            // Calculate next renewal date
            $nextRenewalDate = Carbon::parse($this->booking->to_date)->subDays(7);

            $this->booking->update([
                'renewal_period_days' => $this->renewalPeriodDays,
                'next_renewal_date' => $nextRenewalDate
            ]);

            session()->flash('message', "Renewal period updated to {$this->renewalPeriodDays} days.");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function getAutoRenewalStatusProperty()
    {
        return [
            'enabled' => $this->booking->auto_renewal,
            'periodDays' => $this->booking->renewal_period_days,
            'nextRenewal' => $this->booking->next_renewal_date,
            'canManage' => $this->canManageAutoRenewal,
            'status' => $this->booking->renewal_status
        ];
    }

    public function render()
    {
        return view('livewire.admin.booking-show-component', [
            'bookedRooms' => $this->rooms,
            'autoRenewal' => $this->autoRenewalStatus
        ]);
    }
}
