<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use App\Enums\NotificationReason;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderNotificationMail extends Mailable
{

    // ici l'avantage dans un worker thread dédiée pour éviter de bloquer la requête en cours
    use Queueable, SerializesModels;

    public function __construct(
        public NotificationReason $reason,
        public Order $order,
        public User $recipient,
        public User $actor,
        public ?string $extraMessage = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->reason->getSubject($this->order->getTitle()),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-notification',
        );
    }
}
