<li class="timeline-item timeline-item-transparent">
   @if(strtotime($item->time_from) <= strtotime(now())) 
      <span class="timeline-point timeline-point-danger"></span>
   @else
      <span class="timeline-point timeline-point-success"></span>
   @endif
   <div class="timeline-event">
      <div class="timeline-header mb-2 pb-1">
         @if(strtotime($item->time_from) <= strtotime(now())) 
            <h6 class="mb-0 text-danger">
         @else
            <h6 class="mb-0">
         @endif
            <i class="tf-icons mdi mdi-phone-outline"></i>
            <span class="me-3">{{ $item->title ? $item->title : 'Call' }}</span>
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
                        @if(strtolower($item->title) === 'docusign')
                            <a class="dropdown-item view-docusign-details" href="#" data-envelope-id="{{ $item->envelop_id }}" data-bs-toggle="modal" data-bs-target="#docusignDetailsModal">
                                <i class="mdi mdi-file-document-outline me-1"></i>
                                View Recipients
                            </a>
                        @endif
                        <button class="dropdown-item" type="submit">
                           <i class="mdi mdi-trash-can-outline me-1"></i>Delete
                        </button>
                        <a class="dropdown-item" href="{{ route('deal.activity.done', [$deal->id, $item->id]) }}">
                           <i class="tf-icons mdi mdi-check-outline"></i>Mark as done
                        </a> 
                     </div>
                  </div>
               </form>
            </div>
         </h6>
            @php
                $timestamp = strtotime($item->time_from);
                $isMidnight = date('H:i:s', $timestamp) === '00:00:00';
                $formatted = $isMidnight ? date('M j, Y', $timestamp) : date('M j, Y h:i a', $timestamp);
            @endphp
            
            @if($timestamp <= strtotime(now()))
                <small class="text-muted">
                    <span class="badge rounded-pill bg-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ $formatted }}">
                        {{ $formatted }}
                    </span>
                </small>
            @else
                <small class="text-muted" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ $formatted }}">
                    {{ $formatted }}
                </small>
            @endif
      </div>
      <p class="mb-2">
         <span>{!! $item->description !!}</span>
      </p>
      <div class="d-flex flex-wrap align-items-center">
         <div class="avatar avatar-xs me-2">
            <img src="{{ url('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
         </div>
         <!--<a href="tel:{{ $deal->phone }}" title="Click to call">-->
         <!--   <h6 class="mb-0 text-body">{{ $deal->full_name }}</h6>-->
         <!--</a>-->
         @php
            $envelopeId = $item->envelop_id ?? null;
        @endphp
        <a href="tel:{{ $deal->phone }}" title="Click to call">
           <h6 class="mb-0 text-body">{{ $deal->full_name }}</h6>
        </a>
      </div>
        @if($envelopeId)
          <p id="envelopeStatusDisplay-{{ $envelopeId }}" class="text-muted small">0/0 completed</p>
           <script>
              (function() {
                 const envelopeId = "{{ $envelopeId }}";
                 const statusElem = document.getElementById('envelopeStatusDisplay-' + envelopeId);
        
                 if (statusElem) {
                    fetch(`/envelope-status/${envelopeId}`)
                       .then(response => response.json())
                       .then(data => {
                          if (data.total !== undefined) {
                             statusElem.innerText = `${data.completed}/${data.total} completed`;
                          }
                       })
                       .catch(() => {
                          statusElem.innerText = "Unable to fetch status";
                       });
                 }
              })();
           </script>
        @endif
   </div>
</li>
<!-- Modal -->
<div class="modal fade" id="docusignDetailsModal" tabindex="-1" aria-labelledby="docusignDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="docusignDetailsModalLabel">DocuSign Recipient</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Recipient</th>
              <th>Role</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="docusignDetailsBody">
            <tr>
              <td colspan="3" class="text-center">Loading...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


