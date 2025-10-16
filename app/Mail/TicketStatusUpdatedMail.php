<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;

class TicketStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        return $this->subject('📌 Ticket Status Updated: ' . $this->ticket->title)
                    ->view('emails.ticket_status_updated')
                    ->with(['ticket' => $this->ticket]);
    }
}
    