@component('mail::message')
# Hello from Digitex CRM

You are receiving this email because we received a password reset request.

@component('mail::button', ['url' => $actionUrl])
Reset Password
@endcomponent

This link will expire in 30 minutes.

If you did not request this, no action is required.

Thanks,<br>
Digitex CRM Team
@endcomponent