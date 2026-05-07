<x-mail::message>
Hi {{ ucfirst($emailData['client_name']) }},

Your SEO plan for the website {{ $emailData['website_url'] }} is due for renewal in {{ $emailData['days_remaining'] }} days.

<strong>SEO Package: {{ $emailData['seo_package'] }}</strong>
<strong>Renewal Date: {{ $emailData['renewal_date'] }}</strong>

Thanks,<br>
{{ config('app.name') }}<br>
Your SEO Team
</x-mail::message>
