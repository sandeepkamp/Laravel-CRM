<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SeoRenewalReminderMail extends Mailable
{
    use SerializesModels;

    public $emailData;

    /**
     * Create a new message instance.
     *
     * @param array $emailData
     * @return void
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('SEO Plan Renewal Reminder')
                    ->view('mail.seo.renewalReminder')
                    ->with('emailData', $this->emailData);
    }
}
