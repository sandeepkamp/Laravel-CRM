<x-mail::message>
Hi {{ ucfirst($data['client_name']) }},

This is a reminder that your hosting plan with {{ $data['provider_name'] }} will expire in {{ $data['days_remaining'] }} days.

<strong>Plan Details:</strong> {{ $data['plan_details'] }}<br>
<strong>Expiry Date:</strong> {{ $data['expiry_date'] }}<br>

Please renew your plan to avoid any interruptions.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
