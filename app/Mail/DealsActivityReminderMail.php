<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Sales\DealsActivityModel;
use Illuminate\Mail\Mailables\Address;

class DealsActivityReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public DealsActivityModel $activity
    )
    {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Deals Activity Reminder Mail',
            from: new Address(env('MAIL_FROM_ADDRESS', 'no-reply@digitextechnologies.com'), env('MAIL_FROM_NAME', 'Digitex CRM Team')),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.deal.activity_reminder_content',
            with: [
                'first_name' => $this->activity->lead->owner_data->first_name ?? '',
                'button_url' => route('deal.show', $this->activity->deal_id),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
