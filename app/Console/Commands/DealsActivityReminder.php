<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sales\DealsActivityModel;
use Mail;
use App\Mail\DealsActivityReminderMail;

class DealsActivityReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deals:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deal activity reminder to lead owner';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $leads_activities = DealsActivityModel::where('done', 0)->where('status', 0)->get();
        if($leads_activities){
            foreach ($leads_activities as $activity) {
                $advanced = 15*60*60; // 1 hour
                $scheduled = strtotime($activity->time_from);
                $now = time();
                $next_scheduled = $now + $advanced;
                if($next_scheduled >= $scheduled){
                    Mail::to($activity->lead->owner_data->email)->send(new DealsActivityReminderMail($activity));
                    $activity->status = 1;
                    $activity->save();
                }
            }
        }
    }
}
