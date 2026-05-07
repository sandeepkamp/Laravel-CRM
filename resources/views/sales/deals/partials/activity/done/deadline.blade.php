<li class="timeline-item timeline-item-transparent">
  <span class="timeline-point timeline-point-success"></span>
  <div class="timeline-event pb-1 mb-n4">
     <div class="timeline-header">
        <h6 class="mb-0 text-success">
           <i class="tf-icons mdi mdi-alert-outline"></i>
           <span class="me-3">{{ $done->title ? $done->title : 'Deadline' }}</span>
           <div class="float-end">
              <form method="POST" action="{{ route('deal.activity.delete', [$deal->id, $done->id]) }}">
                 @csrf
                 @method('DELETE')
                 <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="mdi mdi-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu">
                       <a class="dropdown-item" href="{{ route('deal.activity.edit', [$deal->id, $done->id]) }}">
                       <i class="mdi mdi-pencil-outline me-1"></i>
                       Edit
                       </a>
                       <button class="dropdown-item" type="submit">
                       <i class="mdi mdi-trash-can-outline me-1"></i>
                       Delete
                       </button>
                       <!--<a class="dropdown-item" href="{{ route('lead.activity.todo', [$deal->id, $done->id]) }}">-->
                       <!--<i class="tf-icons mdi mdi-checkbox-marked-circle-plus-outline"></i>-->
                       <!--Mark as todo-->
                       <!--</a>-->
                    </div>
                 </div>
              </form>
           </div>
        </h6>
        <small class="text-muted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ date('M j, Y h:i a', strtotime($done->time_from)) }}">{{ date('M j, Y', strtotime($done->time_from)) }}</small>
     </div>
     <p class="mb-0">{!! $done->description !!}</p>
  </div>
</li>