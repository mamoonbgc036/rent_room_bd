<?php

namespace App\Services;

use App\Models\Booking;
use App\Notifications\BookingAutoRenewalNotification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Events\BookingAutoRenewed;
use Illuminate\Support\Facades\Log;
use App\Exceptions\RenewalException;

class AutoRenewalService
{
    /**
     * Process the renewal of a booking
     */
    public function processRenewal(Booking $booking): ?Booking
    {
        try {
            // Validate booking state before processing
            $this->validateBookingForRenewal($booking);

            DB::beginTransaction();

            // Calculate new booking dates
            $dates = $this->calculateRenewalDates($booking);

            // Create new booking
            $newBooking = $this->createRenewalBooking($booking, $dates);

            // Handle associated data (rooms, etc)
            $this->handleAssociatedData($booking, $newBooking);

            // Update original booking status
            $this->finalizeOriginalBooking($booking);

            DB::commit();

            // Handle post-renewal actions
            $this->handlePostRenewalActions($booking, $newBooking);

            return $newBooking;

        } catch (RenewalException $e) {
            DB::rollBack();
            Log::error('Booking renewal failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Unexpected error during booking renewal', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Validate if a booking is eligible for renewal
     */
    protected function validateBookingForRenewal(Booking $booking): void
    {
        if (!$booking->auto_renewal) {
            throw new RenewalException('Auto-renewal is not enabled for this booking.');
        }

        if (!$booking->next_renewal_date) {
            throw new RenewalException('No renewal date is set for this booking.');
        }

        if ($booking->payment_status === 'cancelled') {
            throw new RenewalException('Cannot renew a cancelled booking.');
        }

        if ($booking->payment_status === 'finished') {
            throw new RenewalException('Cannot renew a finished booking.');
        }

        if ($booking->renewal_status === 'completed') {
            throw new RenewalException('This booking has already been renewed.');
        }

        // Additional validation as needed
        if (!$booking->to_date || !$booking->from_date) {
            throw new RenewalException('Booking dates are not properly set.');
        }
    }

    /**
     * Calculate the dates for the renewed booking
     */
    protected function calculateRenewalDates(Booking $booking): array
    {
        $fromDate = Carbon::parse($booking->to_date)->addDay();
        $toDate = $fromDate->copy()->addDays($booking->renewal_period_days);
        $nextRenewalDate = $toDate->copy()->subDays(7);

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'next_renewal_date' => $nextRenewalDate
        ];
    }

    /**
     * Create the renewal booking
     */
    protected function createRenewalBooking(Booking $booking, array $dates): Booking
    {
        // Replicate booking without these attributes
        $newBooking = $booking->replicate([
            'payment_status',
            'last_renewal_date',
            'next_renewal_date'
        ]);

        // Calculate the new price
        $dailyRate = $booking->price / $booking->number_of_days;
        $newPrice = $dailyRate * $booking->renewal_period_days;

        $newBooking->fill([
            'from_date' => $dates['from_date'],
            'to_date' => $dates['to_date'],
            'number_of_days' => $booking->renewal_period_days,
            'payment_status' => 'pending',
            'auto_renewal' => true,
            'renewal_period_days' => $booking->renewal_period_days,
            'next_renewal_date' => $dates['next_renewal_date'],
            'last_renewal_date' => now(),
            'renewal_status' => 'renewed',
            'price' => $newPrice,
            'parent_booking_id' => $booking->id // Add this if you want to track relationship
        ]);

        $newBooking->save();

        return $newBooking;
    }

    /**
     * Handle associated data for the new booking
     */
    protected function handleAssociatedData(Booking $originalBooking, Booking $newBooking): void
    {
        // Replicate rooms
        if ($originalBooking->rooms) {
            foreach ($originalBooking->rooms as $room) {
                $newBooking->rooms()->create($room->toArray());
            }
        }

        // Handle other relationships as needed
        // Example: Copy booking preferences
        if ($originalBooking->preferences) {
            $newBooking->preferences()->createMany(
                $originalBooking->preferences->map->only(['key', 'value'])->toArray()
            );
        }
    }

    /**
     * Finalize the original booking
     */
    protected function finalizeOriginalBooking(Booking $booking): void
    {
        $booking->update([
            'payment_status' => 'finished',
            'renewal_status' => 'completed',
            'auto_renewal' => false // Disable auto-renewal on the original booking
        ]);
    }

    /**
     * Handle post-renewal actions
     */
    protected function handlePostRenewalActions(Booking $originalBooking, Booking $newBooking): void
    {
        // Dispatch event
        event(new BookingAutoRenewed($newBooking));

        // Send notification to user
        if ($originalBooking->user) {
            $originalBooking->user->notify(new BookingAutoRenewalNotification($newBooking));
        }

        // Log the renewal
        Log::info('Booking renewed successfully', [
            'original_booking_id' => $originalBooking->id,
            'new_booking_id' => $newBooking->id,
            'renewal_date' => now()
        ]);
    }

    /**
     * Check if a booking should be renewed
     */
    public function shouldRenew(Booking $booking): bool
    {
        try {
            $this->validateBookingForRenewal($booking);
            return now()->gte($booking->next_renewal_date);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Process all pending renewals
     */
    public function checkAndProcessRenewals(): void
    {
        $bookings = Booking::query()
            ->where('auto_renewal', true)
            ->where('next_renewal_date', '<=', now())
            ->whereNotIn('payment_status', ['finished', 'cancelled'])
            ->whereNull('renewal_status')
            ->chunk(100, function ($bookings) {
                foreach ($bookings as $booking) {
                    try {
                        if ($this->shouldRenew($booking)) {
                            $this->processRenewal($booking);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to process renewal for booking', [
                            'booking_id' => $booking->id,
                            'error' => $e->getMessage()
                        ]);
                        continue;
                    }
                }
            });
    }
}
