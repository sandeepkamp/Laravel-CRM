<x-mail::message>
Hi {{ ucfirst($emailData['client_name']) }},

This is a reminder that your hosting plan with {{ $emailData['provider_name'] }} is set to expire in {{ $emailData['days_left'] }} days.

<strong>Plan Details:</strong> {{ $emailData['plan_details'] }}<br>
<strong>Expiry Date:</strong> {{ $emailData['expiry_date'] }}<br>

Please take the necessary steps to renew your hosting plan to avoid any disruptions.

Thanks,<br>
{{ config('app.name') }}<br>
Your Hosting Team
</x-mail::message>
