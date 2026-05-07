<x-mail::message>
Hi {{ ucfirst($activity->lead->owner_data->first_name) }},

@if($activity->type == 1)
It's a gentle reminder that you have a call scheduled with {{ $activity->lead->full_name }} at {{ date('h:i a', strtotime($activity->time_from)) }} today.
@elseif($activity->type == 2)
It's a gentle reminder that you have a meeting scheduled with {{ $activity->lead->full_name }} at {{ date('h:i a', strtotime($activity->time_from)) }} today.
@elseif($activity->type == 3)
It's a gentle reminder that you have a task scheduled for {{ $activity->lead->full_name }} at {{ date('h:i a', strtotime($activity->time_from)) }} today. We found some additional information which is below - <br>
{{ $activity->title }}<br>
{{ $activity->description }}
@elseif($activity->type == 4)
It's a gentle reminder that you have a deadline scheduled for {{ $activity->lead->full_name }} at {{ date('h:i a', strtotime($activity->time_from)) }} today. We found some additional information which is below - <br>
{{ $activity->title }}<br>
{{ $activity->description }}
@elseif($activity->type == 5)
It's a gentle reminder that you have a email scheduled with {{ $activity->lead->full_name }} at {{ date('h:i a', strtotime($activity->time_from)) }} today. This is the email id for this lead {{ $activity->lead->email }}
@endif


@component('mail::button', ['url' => route('deal.show', $activity->deal_id)])
    View
@endcomponent

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>