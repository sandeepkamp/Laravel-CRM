<li class="timeline-item timeline-item-transparent" data-type="{{ $done->type }}">
    <span class="timeline-point timeline-point-success"></span>
    <div class="timeline-event">
        <div class="timeline-header mb-2">
            <h6 class="mb-0 text-success">
                <i class="tf-icons mdi mdi-alert-outline"></i>
                <span class="me-3">{{ $done->title ? $done->title : 'Deadline' }}</span>
                <div class="float-end">
                    <form method="POST" action="{{ route('lead.activity.delete', [$lead->id, $done->id]) }}">
                        @csrf
                        @method('DELETE')
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('lead.activity.edit', [$lead->id, $done->id]) }}">
                                    <i class="mdi mdi-pencil-outline me-1"></i>
                                    Edit
                                </a>
                                <button class="dropdown-item" type="submit">
                                    <i class="mdi mdi-trash-can-outline me-1"></i>
                                    Delete
                                </button>
                                <a class="dropdown-item" href="{{ route('lead.activity.todo', [$lead->id, $done->id]) }}">
                                    <i class="tf-icons mdi mdi-checkbox-marked-circle-plus-outline"></i>
                                    Mark as todo
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </h6>
        </div>
        <p class="my-3">{!! $done->description !!}</p>
        <small class="text-muted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ $done->time_from->format('l, jS F, Y h:i A') }}">{{ $done->created_at->format('M j, Y h:i a') }}</small>
    </div>
</li>