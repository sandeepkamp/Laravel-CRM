<li class="timeline-item timeline-item-transparent">
   <span class="timeline-point timeline-point-success"></span>
   <div class="timeline-event pb-1 mb-n4">
      <div class="timeline-header">
         <h6 class="mb-0 text-success">
            <i class="tf-icons mdi mdi-paperclip"></i>
            <span><a href="{{ url('uploads/sales/deals/'.$done->files->file_name) }}" title="{{ $done->files->original_name }}">{{ $done->files->original_name }} </a></span>
            <div class="float-end">
               <form method="POST" action="{{ route('deal.activity.delete', [$deal->id, $done->id]) }}">
                  @csrf
                  @method('DELETE')
                  <div class="dropdown">
                     <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                     <i class="mdi mdi-dots-vertical"></i>
                     </button>
                     <div class="dropdown-menu">
                        <!--<a class="dropdown-item" href="{{ route('deal.activity.edit', [$deal->id, $done->id]) }}">-->
                        <!--<i class="mdi mdi-pencil-outline me-1"></i>-->
                        <!--Edit-->
                        <!--</a>-->
                        <button class="dropdown-item" type="submit">
                        <i class="mdi mdi-trash-can-outline me-1"></i>
                        Delete
                        </button>
                     </div>
                  </div>
               </form>
            </div>
         </h6>
         <small class="text-muted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ date('M j, Y h:i a', strtotime($done->created_at)) }}">{{ date('M j, Y', strtotime($done->created_at)) }}</small>
      </div>
      <p class="mb-2">{!! $done->description !!}</p>
   </div>
</li>