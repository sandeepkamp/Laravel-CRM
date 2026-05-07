    <div class="tab-content p-0">

        <div class="tab-pane fade active show" id="navs-top-home" role="tabpanel">

           <form method="POST" action="{{ route('deal.activity.update', [$deal->id, $update->id]) }}" class="needs-validation" enctype="multipart/form-data">

              @csrf

              @method('PUT')

              <input type="hidden" name="form_type" value="activity">

                <div class="form-body">

                    <div class="row">

                        <div class="col-md-12 mb-3">

                           <div class="form-check form-check-inline" id="div_type_1">

                              <input type="radio" name="type" value="1" data-title="Call" class="form-check-input" id="type_1" checked required {{ old('type')==1 ? 'checked' : ($update->type == 1 ? 'checked' : '') }}>

                              <label class="form-check-label" for="type_1">Call</label>

                           </div>

                           <div class="form-check form-check-inline" id="div_type_2">

                              <input type="radio" name="type" value="2" data-title="Meeting" class="form-check-input" id="type_2" {{ old('type')==2 ? 'checked' : ($update->type == 2 ? 'checked' : '') }}>

                              <label class="form-check-label" for="type_2">Meeting</label>

                           </div>

                           <div class="form-check form-check-inline" id="div_type_3">

                              <input type="radio" name="type" value="3" data-title="Task" class="form-check-input" id="type_3" {{ old('type')==3 ? 'checked' : ($update->type == 3 ? 'checked' : '') }}>

                              <label class="form-check-label" for="type_3">Task</label>

                           </div>

                           <div class="form-check form-check-inline" id="div_type_4">

                              <input type="radio" name="type" value="4" data-title="Deadline" class="form-check-input" id="type_4" {{ old('type')==4 ? 'checked' : ($update->type == 4 ? 'checked' : '') }}>

                              <label class="form-check-label" for="type_4">Deadline</label>

                           </div>

                           <div class="form-check form-check-inline" id="div_type_5">

                              <input type="radio" name="type" value="5" data-title="Email" class="form-check-input" id="type_5" {{ old('type')==5 ? 'checked' : ($update->type == 5 ? 'checked' : '') }}>

                              <label class="form-check-label" for="type_5">Email</label>

                           </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12">

                           <div class="form-floating form-floating-outline mb-3">

                              <input type="text" name="title" id="title" maxlength="50" placeholder="Call with John" class="form-control" value="{{ old('title') ? old('title') : $update->title }}">

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

                              <input type="date" name="from_date" id="from_date" class="form-control" value="{{ old('from_date') ? old('from_date', now()->toDateString()) : date('Y-m-d', strtotime($update->time_from)) }}" onclick="this.showPicker();">

                              <label class="form-label" for="from_date">Start Date</label>

                              @error('from_date')

                              <div class="invalid-feedback">{{ $message }}</div>

                              @enderror

                           </div>

                        </div>

                        <div class="col-md-3 col-sm-6 mb-3">

                           <div class="form-floating form-floating-outline mb-3">

                              <input type="time" name="from_time" id="from_time" class="form-control" value="{{ old('from_time') ? old('from_time') : date('H:i', strtotime($update->time_from)) }}" onclick="this.showPicker();">

                              <label class="form-label" for="from_time">Start Time</label>

                              @error('from_time')

                              <div class="invalid-feedback">{{ $message }}</div>

                              @enderror

                           </div>

                        </div>

                        <div class="col-md-3 col-sm-6 mb-3">

                           <div class="form-floating form-floating-outline mb-3">

                              <input type="time" name="to_time" id="to_time" class="form-control" value="{{ old('to_time') ? old('to_time') : date('H:i', strtotime($update->time_to)) }}" onclick="this.showPicker();">

                              <label class="form-label" for="to_time">End Time</label>

                              @error('to_time')

                              <div class="invalid-feedback">{{ $message }}</div>

                              @enderror

                           </div>

                        </div>

                        <div class="col-md-3 col-sm-6 mb-3">

                           <div class="form-floating form-floating-outline">

                              <input type="date" name="to_date" id="to_date" class="form-control" value="{{ old('to_date') ? old('to_date', now()->toDateString()) : date('Y-m-d', strtotime($update->time_to)) }}" onclick="this.showPicker();">

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

                              <textarea class="form-control h-px-100" placeholder="type here.." name="activity_note" id="activity_note">{{ old('activity_note') ? old('activity_note') : (in_array($update->type, [1, 2, 3, 4, 5]) ? $update->description : '')}}</textarea>

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

                              <input type="checkbox" name="mark_done" id="mark_done" class="form-check-input" value="1" {{ old('mark_done')==1 ? 'checked' : ($update->done == 1 ? 'checked' : '') }}>

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

                      <!--<a href="http://127.0.0.1:8000/sales/deal/10/contract" class="float-end mx-5">eSignature</a> -->

                </div>

            </form>

        </div>

         <!--note tab  -->

        <div class="tab-pane fade" id="navs-top-profile" role="tabpanel">

            <form method="POST" action="{{ route('deal.activity.update', [$deal->id, $update->id]) }}" class="needs-validation">

              @csrf

              @method('PUT')

              <input type="hidden" name="form_type" value="note">

                <div class="row">

                    <div class="col-md-12 mb-3">

                        <div class="form-floating form-floating-outline">

                           <textarea class="form-control h-px-100" placeholder="type here.." name="note" id="note">{{ old('note') ? old('note') : (in_array($update->type, [6]) ? $update->description : '') }}</textarea>

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

        <!--file tab -->

        <div class="tab-pane fade" id="navs-top-messages" role="tabpanel">

            <form method="POST" action="{{ route('deal.activity.update', [$deal->id, $update->id]) }}" class="needs-validation" enctype="multipart/form-data">

                @csrf

                @method('PUT')

                <input type="hidden" name="form_type" value="file">

                <div class="row">

                    <div class="col-md-12 mb-3">

                        <div class="form-floating form-floating-outline">

                            <textarea class="form-control h-px-100" placeholder="type here.." name="file_note" id="file_note">{{ old('file_note') ? old('file_note') :(in_array($update->type, [7]) ? $update->description : '') }}</textarea>

                            <div class="form-text">Maximum 1000 chars</div>

                            @error('file_note')

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

    </div>
    
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
    