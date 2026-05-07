<li class="timeline-item timeline-item-transparent alert{{ strtotime($item->time_from) <= strtotime(now()) ? ' alert-danger' : '' }}" data-type="{{ $item->type }}">
    <form method="POST" action="{{ route('lead.activity.delete', [$lead->id, $item->id]) }}">
        @csrf
        @method('DELETE')
        <span class="timeline-point {{ strtotime($item->time_from) <= strtotime(now()) ? 'timeline-point-danger' : 'timeline-point-success' }}"></span>
        <div class="timeline-event">
            <div class="timeline-header mb-2 pb-1">
                <h6 class="mb-0 {{ strtotime($item->time_from) <= strtotime(now()) ? 'text-danger' : 'text-seccess' }}">
                    <i class="tf-icons mdi mdi-phone-outline"></i>
                    <span class="me-3">{{ $item->title ? $item->title : 'Call' }}</span>
                </h6>
                <div class="btn-group" role="group">
                    <a class="btn btn-outline{{ strtotime($item->time_from) <= strtotime(now()) ? '-danger' : '-success' }}" href="{{ route('lead.activity.done', [$lead->id, $item->id]) }}"><i class="tf-icons mdi mdi-check-outline me-2"></i>Mark as done</a>
                    <button type="button" class="btn btn-outline{{ strtotime($item->time_from) <= strtotime(now()) ? '-danger' : '-success' }} dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="mdi mdi-dots-vertical"></i>
                        Actions
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('lead.activity.edit', [$lead->id, $item->id]) }}">
                            <i class="mdi mdi-pencil-outline me-1"></i>
                            Edit
                        </a>
                        <button class="dropdown-item" type="submit">
                            <i class="mdi mdi-trash-can-outline me-1"></i>
                            Delete
                        </button>
                        <a class="dropdown-item" href="{{ route('lead.activity.done', [$lead->id, $item->id]) }}">
                            <i class="tf-icons mdi mdi-check-outline"></i>
                            Mark as done
                        </a>
                    </div>
                </div>
            </div>
            <p class="mb-2">
                <span>{!! $item->description !!}</span>
            </p>
            <div class="d-flex flex-wrap align-items-center mb-3">
                <div class="avatar avatar-xs me-2">
                    <img src="{{ url('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                </div>
                <a href="tel:{{ $lead->phone }}" title="Click to call" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Click to call">
                    <h6 class="mb-0 text-body">
                        <span>{{ $lead->full_name }}</span>
                        <span class="ms-3 text-primary">
                            <i class="mdi mdi-phone"></i>
                            {{ $lead->phone }}
                        </span>
                    </h6>
                </a>
            </div>
            <!--@if(strtotime($item->time_from) <= strtotime(now()))-->
            <!--    <small class="text-muted">-->
            <!--        <span class="badge rounded-pill bg-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ $item->time_from->format('l, jS F, Y h:i A') }}">{{ $item->time_from->format('l, jS F, Y h:i A') }}</span>-->
            <!--    </small>-->
            <!--@else-->
            <!--    <small class="text-muted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ $item->time_from->format('l, jS F, Y h:i A') }}">{{ $item->time_from->format('l, jS F, Y h:i A') }}</small>-->
            <!--@endif-->
            @php
                $timeFrom = $item->time_from;
                $showTime = $timeFrom->format('H:i:s') !== '00:00:00';
                $formatted = $showTime 
                    ? $timeFrom->format('l, jS F, Y h:i A') 
                    : $timeFrom->format('l, jS F, Y');
            @endphp
            
            @if(strtotime($item->time_from) <= strtotime(now()))
                <small class="text-muted">
                    <span class="badge rounded-pill bg-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ $formatted }}">{{ $formatted }}</span>
                </small>
            @else
                <small class="text-muted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ $formatted }}">{{ $formatted }}</small>
            @endif
        </div>
    </form>
</li>