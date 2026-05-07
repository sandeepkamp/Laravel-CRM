<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;

class ExpiryReminderMail extends Mailable
{
    public $emailData;

    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    public function build()
    {
        return $this->subject('Hosting Plan Expiry Reminder')
        ->view('mail.hosting.expiry_reminder')
        ->with('data', $this->emailData);
    }
}
