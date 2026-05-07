<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HostingPlanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $hostingDetails;

    /**
     * Create a new message instance.
     *
     * @param array $hostingDetails
     */
    public function __construct($hostingDetails)
    {
        $this->hostingDetails = $hostingDetails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.hosting.hosting_plan')
            ->subject('Your Hosting Plan Details')
            ->with('hostingDetails', $this->hostingDetails);
    }
}
