<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HostingDetails;
use App\Mail\ExpiryReminderMail;
use Illuminate\Support\Facades\Mail;

class SendExpiryReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:expiry-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to clients and admin for hosting plans nearing expiry';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $reminderDays = [30, 7, 5];
        $today = now();

        foreach ($reminderDays as $days) {
            $targetDate = $today->copy()->addDays($days)->format('Y-m-d');
            $hostingPlans = HostingDetails::with('client', 'hostingProvider')
                ->whereDate('expiry_date', $targetDate)
                ->get();

            foreach ($hostingPlans as $plan) {
                $emailData = [
                    'client_name' => $plan->client->first_name . ' ' . $plan->client->last_name,
                    'provider_name' => $plan->hostingProvider->provider_name,
                    'plan_details' => $plan->plan_details,
                    'expiry_date' => $plan->expiry_date,
                    'days_remaining' => $days,
                ];

                // Send email to the client
                Mail::to($plan->client->email)->send(new ExpiryReminderMail($emailData));

                // Send email to the admin
                $adminEmails = [
                    'k.sandeep@digitextechnologies.com',

                ];

                foreach ($adminEmails as $adminEmail) {
                    Mail::to($adminEmail)->send(new ExpiryReminderMail($emailData));
                }

                $this->info("Reminder sent for Plan ID: {$plan->id} - {$days} days remaining.");
            }
        }

        return Command::SUCCESS;
    }
}
