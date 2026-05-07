<x-mail::message>
Hi {{ ucfirst($hostingDetails['client_name']) }},

Your hosting plan has been successfully {{ $hostingDetails['action'] }}.

<h2>Details:</h2>
<ul>
    <li><strong>Provider:</strong> {{ $hostingDetails['provider_name'] }}</li>
    <li><strong>Plan Details:</strong> {{ $hostingDetails['plan_details'] }}</li>
    <li><strong>Activation Date:</strong> {{ $hostingDetails['activation_date'] }}</li>
    <li><strong>Expiry Date:</strong> {{ $hostingDetails['expiry_date'] }}</li>
    <li><strong>Price:</strong> £{{ number_format($hostingDetails['cost'], 2) }}</li>
</ul>

<p>Thank you for choosing our services!</p>

@component('mail::button', ['url' => route('hosting.show', $hostingDetails['hosting_id'])])
    View Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
