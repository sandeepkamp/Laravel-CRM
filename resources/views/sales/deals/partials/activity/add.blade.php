<div class="tab-content p-0">
   <!-- activity tab  -->
   <div class="tab-pane fade {{ old('form_type') == 'activity' || !old('form_type') ? 'show active' : '' }} " id="navs-top-home" role="tabpanel">
      <form method="POST" action="{{ route('deal.activity.add', $deal->id) }}" class="needs-validation" enctype="multipart/form-data">
         <input type="hidden" name="form_type" value="activity">
         @csrf
         <div class="form-body">
            <div class="row">
               <div class="col-md-12 mb-3">
                  <div class="form-check form-check-inline" id="div_type_1">
                     <input type="radio" name="type" value="1" data-title="Call" class="form-check-input" id="type_1" {{ old('type') == 1 ? 'checked' : '' }} required>
                     <label class="form-check-label" for="type_1">Call</label>
                  </div>
                  <div class="form-check form-check-inline" id="div_type_2">
                     <input type="radio" name="type" value="2" data-title="Meeting" class="form-check-input" id="type_2" {{ old('type') == 2 ? 'checked' : '' }}>
                     <label class="form-check-label" for="type_2">Meeting</label>
                  </div>
                  <div class="form-check form-check-inline" id="div_type_3">
                     <input type="radio" name="type" value="3" data-title="Task" class="form-check-input" id="type_3" {{ old('type') == 3 ? 'checked' : '' }}>
                     <label class="form-check-label" for="type_3">Task</label>
                  </div>
                  <div class="form-check form-check-inline" id="div_type_4">
                     <input type="radio" name="type" value="4" data-title="Deadline" class="form-check-input" id="type_4" {{ old('type') == 4 ? 'checked' : '' }}>
                     <label class="form-check-label" for="type_4">Deadline</label>
                  </div>
                  <div class="form-check form-check-inline" id="div_type_5">
                     <input type="radio" name="type" value="5" data-title="Email" class="form-check-input" id="type_5" {{ old('type') == 5 ? 'checked' : '' }}>
                     <label class="form-check-label" for="type_5">Email</label>
                  </div>
                  @error('type')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
                  <div class="form-floating form-floating-outline mb-3">
                     <input type="text" name="title" id="title" maxlength="50" placeholder="Call with John" class="form-control" value="{{ old('title') }}">
                     <label class="form-label" for="title">Title</label>
                     @error('title')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-3 col-sm-6 mb-3">
                  <div class="form-floating form-floating-outline mb-3">
                     <input type="date" name="from_date" id="from_date" class="form-control" value="{{ old('from_date', now()->toDateString()) }}" min="{{date('Y-m-d')}}" onclick="this.showPicker();">
                     <label class="form-label" for="from_date">Start Date</label>
                     @error('from_date')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
               </div>
               <div class="col-md-3 col-sm-6 mb-3">
                  <div class="form-floating form-floating-outline mb-3">
                     <input type="time" name="from_time" id="from_time" class="form-control" value="{{ old('from_time') }}" onclick="this.showPicker();">
                     <label class="form-label" for="from_time">Start Time</label>
                     @error('from_time')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
               </div>
               <div class="col-md-3 col-sm-6 mb-3">
                  <div class="form-floating form-floating-outline mb-3">
                     <input type="time" name="to_time" id="to_time" class="form-control" value="{{ old('to_time') }}" onclick="this.showPicker();">
                     <label class="form-label" for="to_time">End Time</label>
                     @error('to_time')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
               </div>
               <div class="col-md-3 col-sm-6 mb-3">
                  <div class="form-floating form-floating-outline">
                     <input type="date" name="to_date" id="to_date" class="form-control" value="{{ old('from_date', now()->toDateString()) }}" min="{{date('Y-m-d')}}" onclick="this.showPicker();">
                     <label class="form-label" for="to_date">End Date</label>
                     @error('to_date')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12 mb-3">
                  <div class="form-floating form-floating-outline">
                     <textarea class="form-control h-px-100" placeholder="type here.." name="activity_note" id="activity_note">{{ old('activity_note') }}</textarea>
                     <div class="form-text">Maximum 5000 chars</div>
                     @error('activity_note')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12 mb-3">
                  <div class="form-check">
                     <input type="checkbox" name="mark_done" id="mark_done" class="form-check-input" value="1" {{ old('mark_done') == 1 ? 'checked' : ''  }}>
                     <label class="form-check-label" for="mark_done">Mark as done</label>
                     @error('mark_done')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
               </div>
            </div>
         </div>
         <div class="form-actions">
            <button type="submit" class="btn btn-outline-primary waves-effect float-end">SAVE</button>
            <!-- <a href="{{ route('contract.create', [$deal->id]) }}" class="float-end mx-5">eSignature</a> -->
         </div>
      </form>
   </div>
   <!-- For the Note Tab (type 6) -->
   <div class="tab-pane fade {{ old('form_type') == 'note' ? 'show active' : '' }}" id="navs-top-profile" role="tabpanel">
      <form method="POST" action="{{ route('deal.activity.add', $deal->id) }}" class="needs-validation">
         <input type="hidden" name="form_type" value="note">
         @csrf
         <div class="row">
            <div class="col-md-12 mb-3">
               <div class="form-floating form-floating-outline">
                  <textarea class="form-control h-px-100" placeholder="type here.." name="note" id="note">{{ old('note') }}</textarea>
                  <div class="form-text">Maximum 1000 chars</div>
                  @error('note')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
         </div>
         <div class="form-actions">
            <button type="submit" class="btn btn-outline-primary waves-effect float-end">SAVE</button>
         </div>
      </form>
   </div>
   <!-- file tab -->

   <div class="tab-pane fade {{ old('form_type') == 'file' ? 'show active' : '' }}" id="navs-top-messages" role="tabpanel">

      <form method="POST" action="{{ route('deal.activity.add', $deal->id) }}" class="needs-validation" enctype="multipart/form-data">

         <input type="hidden" name="form_type" value="file">

         @csrf

         <div class="row">

            <div class="col-md-12 mb-3">

               <div class="form-floating form-floating-outline">

                  <textarea class="form-control h-px-100" placeholder="type here.." name="file_note" id="file_note">{{ old('file_note') }}</textarea>

                  <div class="form-text">Maximum 1000 chars</div>

                  @error('file_note')

                  <div class="invalid-feedback">{{ $message }}</div>

                  @enderror

               </div>

            </div>

         </div>

         <div class="row">

            <div class="col-md-12 mb-3">

               <div class="form-floating form-floating-outline">

                  <input type="file" name="document" id="file" class="form-control" required />

                  <label class="form-label" for="file">File</label>

                  @error('file')

                  <div class="invalid-feedback">{{ $message }}</div>

                  @enderror

               </div>

            </div>

         </div>

         <div class="form-actions">

            <button type="submit" class="btn btn-outline-primary waves-effect float-end">SAVE</button>

            <!--<a href="{{ route('contract.create', [$deal->id]) }}" class="float-end mx-5">eSignature</a>-->

         </div>

      </form>

   </div>

   <!-- docs tab  -->

   <div class="tab-pane fade {{ old('form_type') == 'doc' ? 'show active' : '' }}" id="docs-top-messages" role="tabpanel">

      <form method="POST" enctype="multipart/form-data" id="upload-file" action="{{ route('contract.send', [$deal->id]) }}">
         @csrf
         <div class="form-body">
            <div id="fileUpload" class="file-container">
               <label for="fileUpload-1" class="file-upload">
                  <div>
                     <span>Drag &amp; Drop Files Here</span>
                     <h5>OR</h5>
                     <div>Browse Files</div>
                  </div>
                  <input type="file" id="fileUpload-1" name="formFile" hidden="" accept="application/pdf">
               </label>
            </div>
            <!-- modal  -->
            <div id="modal_div" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
               <div class="modal-dialog modal-xl modal-simple modal-add-new-address">
                  <div class="modal-content">
                     <div class="modal-body p-0">
                        <button type="reset" class="btn-close close_button" data-bs-dismiss="modal" aria-label="Close"></button>
                        {{--  
                        <div class="text-center mb-6">
                           <object id="showpdfid" data='' type="application/pdf" width="100%" height="600">
                           </object>
                        </div>
                        --}}
                        <div><input type="text" name="" id="file_name" class="form-control"></div>
                        <div class="d-flex">
                           <div id="pdfContainer">
                              <div id="pdfViewer"></div>
                           </div>
                           <div id="sidebar">
                              <ul id="pageList"></ul>
                           </div>
                        </div>
                        <div class="modal-footer mt-5 pt-3">
                           <button type="reset" class="btn-close close_button btn btn-outline-secondary btn-reset waves-effect" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                           <button id="docusign_button" type="button" class="btn btn-outline-primary">Create a Docusign Envelope</button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- modal end -->
            <!-- Docusign Sign Frame Modal -->
            <div id="docusign_div_sign_frame" class="modal fade" tabindex="-1" aria-hidden="true">
               <div class="modal-dialog modal-xl modal-simple modal-docusign">
                  <div class="modal-content">
                     <div class="modal-body p-0">
                        <button type="button" id="close_docusign_modal" class="btn-close" aria-label="Close"></button>
                        <iframe id="docusignFrame" src="" width="100%" height="600" frameborder="0"></iframe>
                     </div>
                  </div>
               </div>
            </div>
            <div id="email_body" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
               <div class="modal-dialog modal-lg modal-simple modal-docusign">
                  <div class="modal-content">
                        <div class="modal-body p-0">
                            <button type="button" class="btn-close close_button" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form>
                            <div class="row">
                              <div class="form-group">
                                <label for="floatingInput">Email Subject</label>
                                <input type="text" class="form-control emailsubject" required name="name" id="floatingInput" placeholder="Enter Email Subject" value="Please Complete Docusign">
                              </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                  <label for="exampleFormControlTextarea1">Email Body</label>
                                  <textarea class="form-control emailbody" rows="5" placeholder="Enter Email Body"></textarea>
                                </div>
                            </div>
                        </form>
                        <div class="modal-footer">
                            <div class="justify-content-start">
                                <input type="hidden" id="deal_id" value="{{ $deal->id }}">
                                <button id="edit_docusign" class="btn btn-outline-primary" type="button">Edit In Docusign</button>
                            </div>
                        </div>
                    </div>                                     
                </div>
            </div>
            <div id="loader-overlay" style="display: none;position: fixed;z-index: 9999; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255,255,255,0.8);text-align: center; padding-top: 200px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Processing... Please wait</p>
            </div>
            <!-- docusign modal  -->
            <div id="docusign_div" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-simple modal-docusign">
                    <div class="modal-content">
                         <div class="modal-body p-0">
                            <button type="button" class="btn-close close_button" data-bs-dismiss="modal" aria-label="Close"></button>
                            <!--<form name="recipient_form" id="recipient_form" method="POST" action="{{ route('contract.send', [$deal->id]) }}">-->
                                @csrf
                                <div id="recipient_container" class="text-center mb-6">
                                    <div class="row formsubmit">
                                        <div class="col-md-3">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control name" required name="name[]" placeholder="Enter name">
                                                <label for="floatingInput">Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating form-floating-outline">
                                                <input type="email" class="form-control email" required name="email[]" placeholder="Enter email">
                                                <label for="floatingemail">Email</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select role" required name="role[]">
                                                    <option value="sign">Needs to sign</option>
                                                    <option value="copy">Receives a copy</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="delete_btn btn btn-outline-primary">Delete</button>
                                        </div>
                                    </div>
                                    <hr class="mt-3">
                                </div>
                                <div class="modal-footer">
                                    <a class="text-primary float-start" id="add_recipient" type="button">+ Add recipient</a>
                                    <!--<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#email_body">Edit</button>-->
                                    <button type="button" class="btn btn-primary" id="validate_and_open_email_modal">Edit</button>
                                    <!--<button type="submit" name="submit" class="btn btn-outline-primary wave-effect float-end">Send</button>-->
                                </div>
                            <!--</form>-->
                        </div>
                    </div>
                </div>
            </div>
            <div id="docusign_success_modal" class="modal fade" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4">
                        <!--<img src="/images/success-icon.png" alt="Success" style="width: 80px;">-->
                        <h5 class="mt-3">DocuSign envelope sent</h5>
                        <p>All recipients will receive an email with enclosed documents.</p>
                        <button type="button" class="btn btn-primary mt-2" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
         </div>
        </form>
   </div>
</div>
<script>
   // Show the modal
   document.querySelector('#docusign_button').addEventListener('click', function() {
       var modal = new bootstrap.Modal(document.getElementById('docusign_div'));
       modal.show(); // Show modal
   });
   // Hide the modal manually
   document.querySelector('.close_button').addEventListener('click', function() {
       var modal = new bootstrap.Modal(document.getElementById('docusign_div'));
       modal.hide(); // Hide modal
   });
    // add and remove recipient  
   $(document).ready(function () {
      $('#docusignFrame .footer-send-button-text').on('click', function() {
         $('#docusign_div_sign_frame').modal('hide');
         setTimeout(function () {
            location.reload();
        }, 500);
      });
        $('#add_recipient').click(function () {

            let recipientRow = `

            <div class="row formsubmit">

                <div class="col-md-3">

                    <div class="form-floating form-floating-outline">

                        <input type="text" class="form-control name" required name="name[]" placeholder="Enter name">

                        <label>Name</label>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="form-floating form-floating-outline">

                        <input type="email" class="form-control email" required name="email[]" placeholder="Enter email">

                        <label>Email</label>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="form-floating form-floating-outline">

                        <select class="form-select role" required name="role[]">

                            <option value="sign">Needs to sign</option>

                            <option value="copy">Receives a copy</option>

                        </select>

                    </div>

                </div>

                <div class="col-md-3">

                    <button type="button" class="delete_btn btn btn-outline-primary">Delete</button>

                </div>

                <hr class="mt-3">

            </div>`;
            $('#recipient_container').append(recipientRow);
        });
        $(document).on('click', '.delete_btn', function () {
            $(this).closest('.formsubmit').remove();
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('input[name="type"]');
        const titleInput = document.getElementById('title');
        const markDoneCheckbox = document.getElementById('mark_done');
    
        function updateTitle() {
            const selectedRadio = document.querySelector('input[name="type"]:checked');
            let baseTitle = selectedRadio ? selectedRadio.getAttribute('data-title') : '';
            if (markDoneCheckbox.checked) {
                baseTitle += ' (Done)';
            }
            titleInput.value = baseTitle;
        }
    
        radios.forEach(radio => {
            radio.addEventListener('change', updateTitle);
        });
    
        markDoneCheckbox.addEventListener('change', updateTitle);
    
        // Optional: Run on page load if values are preselected
        updateTitle();
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modalId = 'docusign_div_sign_frame';
        const iframe = document.getElementById("docusignFrame");
    
        // Manual modal open
        window.showDocusignModal = function (url) {
            iframe.src = url;
    
            const modalElement = document.getElementById(modalId);
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        };
    
        // Manual modal close
        document.getElementById("close_docusign_modal").addEventListener("click", function () {
            const modalElement = document.getElementById(modalId);
    
            // 1. Bootstrap hide
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalElement);
            }
            modalInstance.hide();
    
            // 2. Fallback using jQuery if needed
            $('#'+modalId).modal('hide');
    
            // 3. Remove classes manually if modal persists
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    
            // 4. Clear iframe
            iframe.src = '';
        });
    });
</script>
<script>
    window.addEventListener('message', function(event) {
        if (event.data === 'docusign-complete') {
            // Hide the signing iframe modal
            const signModal = bootstrap.Modal.getInstance(document.getElementById('docusign_div_sign_frame'));
            if (signModal) signModal.hide();

            // Show your success modal (create one like below if needed)
            const successModal = new bootstrap.Modal(document.getElementById('docusign_success_modal'));
            successModal.show();
        }
    });
     // Reload page when the success modal is closed
    const successModalEl = document.getElementById('docusign_success_modal');
    successModalEl.addEventListener('hidden.bs.modal', function () {
        location.reload(); // This will reload the current page
    });
</script>

<script>
document.getElementById('validate_and_open_email_modal').addEventListener('click', function (e) {
    let isValid = true;

    $('#recipient_container .formsubmit').each(function () {
        const name = $(this).find('input.name');
        const email = $(this).find('input.email');

        name.removeClass('is-invalid');
        email.removeClass('is-invalid');

        if (!name.val().trim()) {
            name.addClass('is-invalid');
            isValid = false;
        }

        if (!email.val().trim() || !validateEmail(email.val())) {
            email.addClass('is-invalid');
            isValid = false;
        }
    });

    if (isValid) {
        // First, hide the current modal
        const currentModal = bootstrap.Modal.getInstance(document.getElementById('docusign_div'));
        if (currentModal) currentModal.hide();

        // Then, show the next modal
        const emailModal = new bootstrap.Modal(document.getElementById('email_body'));
        emailModal.show();
    } else {
        alert('Please fill in all name and valid email fields before proceeding.');
    }
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email.toLowerCase());
}

</script>



