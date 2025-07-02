<?php

namespace App\Providers;

use App\Console\Scheduler;
use App\Models\Message;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Scheduler::class);
    }

    public function boot(): void
    {
        // Cleanup old messages
        // $threshold = Carbon::now()->subHours(24);
        // Message::where('created_at', '<', $threshold)->delete();

        // // Check and process auto-renewals
        // $this->processAutoRenewals();
        
        DB::listen(function($query){
            $threshold = 500;
            if($query->time > $threshold){
                Log::channel('daily')->warning('Slow Quries : ', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . ' ms'
                ]);
            }
        });
    }

    private function processAutoRenewals(): void
    {
        try {
            // Get monthly bookings that need renewal
            $bookings = Booking::where('auto_renewal', true)
                ->where('price_type', 'Month')
                ->where('payment_status', '!=', 'cancelled')
                ->where(function ($query) {
                    $query->where('to_date', '<', now()) // Expired bookings
                        ->orWhere('next_renewal_date', '<=', now()); // Due for renewal
                })
                ->get();

            foreach ($bookings as $booking) {
                $this->renewBooking($booking);
            }
        } catch (\Exception $e) {
            Log::error('Auto-renewal error: ' . $e->getMessage());
        }
    }

    private function renewBooking(Booking $booking): void
    {
        DB::beginTransaction();
        try {
            // Get room and price
            $roomIds = json_decode($booking->room_ids, true);
            $room = Room::with(['roomPrices' => function ($query) {
                $query->where('type', 'Month');
            }])->find($roomIds[0]);

            if (!$room || !$room->roomPrices->first()) {
                throw new \Exception("Room or price not found for booking #{$booking->id}");
            }

            $monthlyPrice = $room->roomPrices->first();
            $price = $monthlyPrice->discount_price ?? $monthlyPrice->fixed_price;

            // Calculate dates
            $currentToDate = Carbon::parse($booking->to_date);
            $now = Carbon::now();
            $startDate = $currentToDate->isPast() ? $now : $currentToDate;
            $newToDate = $startDate->copy()->addMonth();

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
            Log::info("Successfully renewed booking #{$booking->id} to {$newToDate->format('Y-m-d')}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to renew booking #{$booking->id}: " . $e->getMessage());
        }
    }

    private function getNextMilestoneNumber(int $bookingId): int
    {
        return DB::table('booking_payments')
            ->where('booking_id', $bookingId)
            ->orderByDesc('milestone_number')
            ->value('milestone_number') + 1 ?? 1;
    }
}
