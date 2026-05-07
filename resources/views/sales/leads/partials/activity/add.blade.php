<div class="card-body">
    <div class="tab-content p-0">
        <div class="tab-pane fade {{ old('form_type') == 'activity' || !old('form_type') ? 'show active' : '' }} " id="navs-top-home" role="tabpanel">
            <form method="POST" action="{{ route('lead.activity.add', $lead->id) }}" class="needs-validation" enctype="multipart/form-data">
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
                            <!-- <div class="form-check form-check-inline" id="div_type_6">
                                <input type="radio" name="type" value="6" data-title="Note" class="form-check-input" id="type_6" {{ old('type') == 6 ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_6">Note</label>
                            </div> -->
                            <!-- <div class="form-check form-check-inline" id="div_type_7">
                                <input type="radio" name="type" value="7" data-title="File" class="form-check-input" id="type_7" {{ old('type') == 7 ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_7">File</label>
                            </div> -->
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
                                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ old('from_date', now()->toDateString()) }}"  min="{{date('Y-m-d')}}" onclick="this.showPicker();">
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
                                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ old('to_date', now()->toDateString()) }}" min="{{date('Y-m-d')}}" onclick="this.showPicker();">
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
                    <!-- <a href="{{ route('contract.create', [$lead->id]) }}" class="float-end mx-5">eSignature</a> -->
                </div>
            </form>
        </div>

        <!-- note tab  -->
        <div class="tab-pane fade {{ old('form_type') == 'note' ? 'show active' : '' }}" id="navs-top-profile" role="tabpanel">
            <form method="POST" action="{{ route('lead.activity.add', $lead->id) }}" class="needs-validation">
                <input type="hidden" name="form_type" value="note">
                @csrf
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control h-px-100" placeholder="type here.." name="note" id="note">{{ old('note') }}</textarea>
                            <!--<label class="form-label" for="note">Note</label>-->
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
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fromDateInput = document.getElementById('from_date');
        const toDateInput = document.getElementById('to_date');

        fromDateInput.addEventListener('change', function () {
            const fromDate = this.value;
            toDateInput.setAttribute('min', fromDate);

            // If `to_date` is less than `from_date`, reset it
            if (toDateInput.value && toDateInput.value < fromDate) {
                toDateInput.value = '';
            }
        });
    });
    
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('input[name="type"]');
        const titleInput = document.getElementById('title');

        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.checked) {
                    titleInput.value = this.getAttribute('data-title');
                }
            });
        });
    });

</script>