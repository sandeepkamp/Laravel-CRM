    <li class="timeline-item timeline-item-transparent">
        @if(strtotime($item->time_from) <= strtotime(now())) <span class="timeline-point timeline-point-danger"></span>
        @else
        <span class="timeline-point timeline-point-info"></span>
        @endif
        <div class="timeline-event">
            <div class="timeline-header mb-2 pb-1">
                @if(strtotime($item->time_from) <= strtotime(now())) 
                <h6 class="mb-0 text-danger">
                @else
                <h6 class="mb-0">
                @endif
                <i class="tf-icons mdi mdi-account-group-outline"></i>
                <span class="me-3">{{ $item->title ? $item->title : 'Meeting' }}</span>
                <div class="float-end">
                    <form method="POST" action="{{ route('deal.activity.delete', [$deal->id, $item->id]) }}">
                      @csrf
                      @method('DELETE')
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('deal.activity.edit', [$deal->id, $item->id]) }}">
                                <i class="mdi mdi-pencil-outline me-1"></i>
                                Edit
                                </a>
                                <button class="dropdown-item" type="submit">
                                <i class="mdi mdi-trash-can-outline me-1"></i>
                                Delete
                                </button>
                                <a class="dropdown-item" href="{{ route('deal.activity.done', [$deal->id, $item->id]) }}">
                                <i class="tf-icons mdi mdi-check-outline"></i>
                                Mark as done
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                </h6>
                @if(strtotime($item->time_from) <= strtotime(now())) 
                    <small class="text-muted">
                       <span class="badge rounded-pill bg-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ date('M j, Y h:i a', strtotime($item->time_from)) }}">
                          {{ date('M j, Y h:i a', strtotime($item->time_from)) }}
                       </span>
                    </small>
                 @else
                    <small class="text-muted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ date('M j, Y h:i a', strtotime($item->time_from)) }}">
                       {{ date('M j, Y h:i a', strtotime($item->time_from)) }}
                    </small>
                 @endif
            </div>
            <p class="mb-2">{!! $item->description !!}</p>
            <div class="d-flex flex-wrap align-items-center">
                <div class="avatar avatar-xs me-2">
                    <img src="{{ url('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                </div>
                <h6 class="mb-0 text-body">{{ $deal->full_name }}</h6>
            </div>
        </div>
    </li>
               