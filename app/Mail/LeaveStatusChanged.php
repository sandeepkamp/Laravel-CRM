<?php

namespace App\Mail;

use App\Models\LeaveManagementModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;
    public $statusMessage;

    public function __construct(LeaveManagementModel $leave, $statusMessage)
    {
        $this->leave = $leave;
        $this->statusMessage = $statusMessage;
    }

    public function build()
    {
        return $this->subject('Leave Request Status Update')
                    ->view('mail.leave.leave-status-changed');
    }
}
