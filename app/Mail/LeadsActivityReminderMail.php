<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Sales\LeadsActivityModel;
use Illuminate\Mail\Mailables\Address;

class LeadsActivityReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public LeadsActivityModel $activity)
    {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Leads Activity Reminder Mail',
            from: new Address(env('MAIL_FROM_ADDRESS', 'no-reply@digitextechnologies.com'), env('MAIL_FROM_NAME', 'Digitex CRM Team')),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.lead.activity_reminder',
            with: [
                'first_name' => $this->activity->lead->owner_data->first_name ?? '',
                'button_url' => route('lead.show', $this->activity->lead_id),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
