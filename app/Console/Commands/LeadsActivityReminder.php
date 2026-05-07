<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sales\LeadsActivityModel;
use Mail;
use App\Mail\LeadsActivityReminderMail;

class LeadsActivityReminder extends Command
{
    protected $signature = 'leads:reminder';
    protected $description = 'Send lead activity reminder to lead owner';

    public function handle()
    {
        $leads_activities = LeadsActivityModel::where('done', 0)->where('status', 0)->get();

        if ($leads_activities->isEmpty()) {
            \Log::info("No pending lead activities.");
            return;
        }

        foreach ($leads_activities as $activity) {
            $advanced = 15*60*60; // 1 hour
            $scheduled = strtotime($activity->time_from);
            $now = time();
            $next_scheduled = $now + $advanced;
            if($next_scheduled >= $scheduled){
                Mail::to($activity->lead->owner_data->email)->send(new LeadsActivityReminderMail($activity));
                
                $activity->status = 1; // Mark as processed
                $activity->save();
            }
        }
    }
}
