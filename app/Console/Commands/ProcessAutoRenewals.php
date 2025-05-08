<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessAutoRenewals extends Command
{
    protected $signature = 'bookings:process-renewals';
    protected $description = 'Process auto-renewals for monthly bookings';

    public function handle()
    {
        $this->info('Starting auto-renewal process...');
        Log::info('Starting auto-renewal process...');

        // Get all active monthly bookings
        $bookings = Booking::query()
            ->where('auto_renewal', true)
            ->where('price_type', 'Month')
            ->where('payment_status', '!=', 'cancelled')
            ->get();

        $this->info("Found {$bookings->count()} bookings to check");
        Log::info("Found {$bookings->count()} bookings to check");

        $count = 0;
        foreach ($bookings as $booking) {
            try {
                $shouldRenew = $this->shouldRenewBooking($booking);
                $this->info("Booking #{$booking->id} should renew: " . ($shouldRenew ? 'Yes' : 'No'));
                Log::info("Booking #{$booking->id} should renew: " . ($shouldRenew ? 'Yes' : 'No'), [
                    'booking_id' => $booking->id,
                    'to_date' => $booking->to_date,
                    'next_renewal_date' => $booking->next_renewal_date,
                    'is_past' => Carbon::parse($booking->to_date)->isPast(),
                    'days_until_expiry' => Carbon::parse($booking->to_date)->diffInDays(now(), false)
                ]);

                if ($shouldRenew && $this->processBookingRenewal($booking)) {
                    $count++;
                }
            } catch (\Exception $e) {
                $this->error("Error processing booking #{$booking->id}: " . $e->getMessage());
                Log::error("Error processing booking #{$booking->id}: " . $e->getMessage());
            }
        }

        $this->info("Processed {$count} renewals successfully.");
        Log::info("Processed {$count} renewals successfully.");
    }

    private function shouldRenewBooking(Booking $booking): bool
    {
        try {
            $toDate = Carbon::parse($booking->to_date);
            $now = Carbon::now();
            $nextRenewalDate = $booking->next_renewal_date ? Carbon::parse($booking->next_renewal_date) : null;

            // Debug information
            $this->info("Checking booking #{$booking->id}:");
            $this->info("- To Date: {$toDate->format('Y-m-d')}");
            $this->info("- Next Renewal: " . ($nextRenewalDate ? $nextRenewalDate->format('Y-m-d') : 'Not set'));
            $this->info("- Days until expiry: " . $toDate->diffInDays($now, false));

            // Case 1: Already expired
            if ($toDate->isPast()) {
                $this->info("- Status: Should renew (expired)");
                return true;
            }

            // Case 2: Next renewal date has passed
            if ($nextRenewalDate && $nextRenewalDate->lessThanOrEqualTo($now)) {
                $this->info("- Status: Should renew (renewal date passed)");
                return true;
            }

            // Case 3: Within 7 days of expiry
            if ($toDate->diffInDays($now) <= 7) {
                $this->info("- Status: Should renew (within 7 days of expiry)");
                return true;
            }

            $this->info("- Status: No renewal needed");
            return false;

        } catch (\Exception $e) {
            $this->error("Error in shouldRenewBooking: " . $e->getMessage());
            Log::error("Error in shouldRenewBooking: " . $e->getMessage());
            return false;
        }
    }

    private function processBookingRenewal(Booking $booking): bool
    {
        DB::beginTransaction();
        try {
            // Get room and price
            $roomIds = json_decode($booking->room_ids, true);
            $room = Room::with(['roomPrices' => function($query) {
                $query->where('type', 'Month');
            }])->find($roomIds[0]);

            if (!$room || !$room->roomPrices->first()) {
                throw new \Exception("Room or price not found");
            }

            $monthlyPrice = $room->roomPrices->first();
            $price = $monthlyPrice->discount_price ?? $monthlyPrice->fixed_price;

            // Calculate dates
            $currentToDate = Carbon::parse($booking->to_date);
            $now = Carbon::now();
            $startDate = $currentToDate->isPast() ? $now : $currentToDate;
            $newToDate = $startDate->copy()->addMonth();

            $this->info("Processing renewal for booking #{$booking->id}:");
            $this->info("- Current end date: {$currentToDate->format('Y-m-d')}");
            $this->info("- New end date: {$newToDate->format('Y-m-d')}");

            // Create milestone
            $milestone = [
                'type' => 'Month',
                'quantity' => 1,
                'price' => $price,
                'total' => $price,
                'description' => $newToDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $newToDate->format('Y-m-d')
            ];

            // Update booking
            $booking->update([
                'to_date' => $newToDate->format('Y-m-d'),
                'number_of_days' => Carbon::parse($booking->from_date)->diffInDays($newToDate),
                'price' => $booking->price + $price,
                'total_amount' => $booking->total_amount + $price,
                'next_renewal_date' => $newToDate->copy()->subDays(7)->format('Y-m-d'),
                'milestone_breakdown' => array_merge(
                    $booking->milestone_breakdown ?? [],
                    [$milestone]
                ),
                'last_renewed_at' => now()
            ]);

            // Create payment
            DB::table('booking_payments')->insert([
                'booking_id' => $booking->id,
                'milestone_type' => 'Month',
                'milestone_number' => $this->getNextMilestoneNumber($booking->id),
                'due_date' => $newToDate->copy()->startOfMonth()->format('Y-m-d'),
                'amount' => $price,
                'payment_status' => 'pending',
                'payment_method' => $booking->payments()->latest()->first()?->payment_method ?? 'bank_transfer',
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $newToDate->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();
            $this->info("Successfully renewed booking #{$booking->id}");
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to renew booking #{$booking->id}: " . $e->getMessage());
            Log::error("Failed to renew booking #{$booking->id}: " . $e->getMessage());
            return false;
        }
    }

    private function getNextMilestoneNumber(int $bookingId): int
    {
        return DB::table('booking_payments')
            ->where('booking_id', $bookingId)
            ->orderByDesc('milestone_number')
            ->value('milestone_number') + 1;
    }
}
