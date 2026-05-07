<li class="timeline-item timeline-item-transparent alert alert-warning" data-type="{{ $done->type }}">
    <span class="timeline-point timeline-point-success"></span>
    <div class="timeline-event">
        <div class="timeline-header mb-2">
            <h6 class="mb-0">
                <i class="tf-icons mdi mdi-note-text-outline"></i>
                <span>Note</span>
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
                            </div>
                        </div>
                    </form>
                </div>
            </h6>
        </div>
        <p class="my-3">{!! $done->description !!}</p>
        <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ $done->created_at->format('l, jS F, Y h:i A') }}">
            <p class="my-3"><i class="mdi mdi-clock-outline me-2"></i> {{ $done->created_at->format('l, jS F, Y h:i A') }}</p>
        </span>
    </div>
</li>