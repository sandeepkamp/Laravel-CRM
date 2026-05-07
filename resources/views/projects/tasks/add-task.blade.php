@extends('app')

@section('title', 'Add Task - '.$project->project_name)

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('project.details', $project->id) }}">{{ $project->project_name }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('project.task.list', $project->id) }}">{{ __('Tasks List') }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('Add Task') }}</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <form method="POST" action="{{ route('project.task.add', $project->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="card-title">{{ __('Add Task for '. $project->project_name) }}</h5>
                    <div class="card-header-elements ms-auto bg-primary-subtle rounded-3 p-2">
                        Project Allocated  {{ $project->project_hours }} Hours
                    </div>
                </div>
                <div class="card-body">
                    @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <strong><i class="tf-icons mdi mdi-check-circle-outline"></i> Success -</strong>
                        {{ session()->get('success') }}
                    </div>
                    @endif
                    @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <strong><i class="tf-icons mdi mdi-alert-outline"></i> Error -</strong>
                        {{ session()->get('error') }}
                    </div>
                    @endif
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('task_name') is-invalid @enderror" id="task_name" name="task_name" value="{{ old('task_name') }}" placeholder="{{ __('Task Name') }}" maxlength="200" required>
                                    <span class="form-text">{{ __('Maximum 200 chars') }}</span>
                                    <label class="form-label" for="task_name">{{ __('Task Name') }} <span class="text-danger">*</span></label>
                                    @error('task_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" class="form-control @error('estimated_time') is-invalid @enderror" id="estimated_time" name="estimated_time" value="{{ old('estimated_time') }}" placeholder="{{ __('Estimated Time') }}" min="1" required onkeypress="return isNumberKey(event);">
                                    <span class="form-text">{{ __('in minutes') }}</span>
                                    <label class="form-label" for="estimated_time">{{ __('Estimated Time') }} <span class="text-danger">*</span></label>
                                    @error('task_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                 <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select @error('milestone') is-invalid @enderror select2" name="milestone" id="milestone" required>
                                        @if($project->milestones()->count() > 0)
                                            @foreach($project->milestones()->get() as $milestone)
                                            <option value="{{ $milestone->id }}">{{ $milestone->milestone_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label class="form-label" for="milestone">{{ __('Milestone') }} <span class="text-danger">*</span></label>
                                    @error('milestone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select @error('status') is-invalid @enderror select2" name="status" id="status" required>
                                        @if($tasks_status->count() > 0)
                                            @foreach($tasks_status as $status)
                                            <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label class="form-label" for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline mb-3">
                                    <textarea class="form-control @error('task_description') is-invalid @enderror" name="task_description" id="task_description" maxlength="1000">{{ old('task_description') }}</textarea>
                                    <span class="form-text">{{ __('Maximum 1000 chars') }}</span>
                                    <label class="form-label" for="task_description">{{ __('Task Description') }}</label>
                                    @error('task_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                 <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select @error('priority') is-invalid @enderror select2" name="priority" id="priority" required>
                                        @foreach($priorities as $key => $priority)
                                            <option value="{{ $key }}">{{ $priority['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <label class="form-label" for="priority">{{ __('Priority') }} <span class="text-danger">*</span></label>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('due_date') is-invalid @enderror datepicker" id="due_date" name="due_date" value="{{ old('due_date') }}" placeholder="{{ __('Due Date') }}" required>
                                    <label class="form-label" for="due_date">{{ __('Due Date') }} <span class="text-danger">*</span></label>
                                    @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label" for="task_files">{{ __('Upload Files') }}</label>
                                <input type="file" class="form-control" name="task_files[]" id="task_files" multiple>
                                <span class="form-text">{{ __('Supported format mp4, pdf, jpg, png, doc at max size 10mb') }}</span>
                                @error('task_files')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary" name="submit">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
<script src="{{ url('vendor/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<!-- Or for RTL support -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.standalone.min.css" integrity="sha512-D5/oUZrMTZE/y4ldsD6UOeuPR4lwjLnfNMWkjC0pffPTCVlqzcHTNvkn3dhL7C0gYifHQJAIrRTASbMvLmpEug==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;
        return true;
    }
    tinymce.init({
        selector: 'textarea#task_description',
        license_key: 'gpl',
        branding: false,
        promotion: false,
        theme: 'silver',
        skin: 'tinymce-5'
    });

    $(function(){
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: 'resolve',
            placeholder: "{{ __('Select milestone') }}",
        });
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true,
            todayBtn: true,
            startDate: new Date()
        });

        let userRole = "{{ auth()->user()->role }}";
        if (userRole != 1 && userRole != 7) {
            let maxMinutes = "{{ $project->project_hours * 60 }}"; 
            $('#estimated_time').on('input', function () {
                let enteredMinutes = parseInt($(this).val(), 10) || 0;
                console.log(userRole);
                
                if (enteredMinutes > maxMinutes) {
                    alert("You cannot enter more than " + maxMinutes + " minutes.");
                    $(this).val(''); // Reset to max allowed value
                }
            });
        }
    });
</script>
@endpush