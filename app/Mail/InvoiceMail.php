<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $filePath;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($booking, $filePath)
    {
        $this->booking = $booking;
        $this->filePath = $filePath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.invoice')
                    ->subject('Your Invoice')
                    ->attach($this->filePath);
    }
}
