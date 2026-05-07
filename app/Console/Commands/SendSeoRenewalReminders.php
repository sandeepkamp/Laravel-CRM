<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SeoService;
use App\Mail\SeoRenewalReminderMail;
use Illuminate\Support\Facades\Mail;

class SendSeoRenewalReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:send-renewal-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to clients for SEO plans nearing renewal';

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
            $seoPlans = SeoService::with('client')
                ->whereDate('renewal_date', $targetDate)
                ->get();

            foreach ($seoPlans as $plan) {
                $emailData = [
                    'client_name' => $plan->client->first_name . ' ' . $plan->client->last_name,
                    'website_url' => $plan->website_url,
                    'seo_package' => $plan->seo_package,
                    'renewal_date' => $plan->renewal_date,
                    'days_remaining' => $days,
                ];

                // Send email to the client
                Mail::to($plan->client->email)->send(new SeoRenewalReminderMail($emailData));

                // Send email to the admin (you can add more admins if needed)
                $adminEmails = [
                    'k.sandeep@digitextechnologies.com',
                ];

                foreach ($adminEmails as $adminEmail) {
                    Mail::to($adminEmail)->send(new SeoRenewalReminderMail($emailData));
                }

                $this->info("Reminder sent for Plan ID: {$plan->id} - {$days} days remaining.");
            }
        }

        return Command::SUCCESS;
    }
}
