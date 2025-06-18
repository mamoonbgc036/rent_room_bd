<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingAutoRenewalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Booking $booking
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Auto-Renewed')
            ->greeting("Hello {$notifiable->name}")
            ->line('Your booking has been automatically renewed.')
            ->line("New booking period: {$this->booking->from_date->format('M d, Y')} to {$this->booking->to_date->format('M d, Y')}")
            ->line("Amount due: ৳" . number_format($this->booking->price, 2))
            ->action('View Booking', route('bookings.show', $this->booking))
            ->line('Thank you for using our service!')
            ->with([
                'booking_id' => $this->booking->id,
                'amount' => $this->booking->price,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'message' => 'Your booking has been automatically renewed.',
            'amount' => $this->booking->price,
            'from_date' => $this->booking->from_date,
            'to_date' => $this->booking->to_date,
        ];
    }
}
