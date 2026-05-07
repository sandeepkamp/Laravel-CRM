@extends('app')

@section('title', 'Edit '.$project->project_name)

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('project.dashboard') }}">Project Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('project.list') }}">List</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('project.details', $project->id) }}">{{ $project->project_name }}</a>
    </li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title">Project Details Form</h5>
                    <div class=" text-end pt-0 my-n5">
                        <a href="{{ route('project.list') }}" class="btn btn-outline-primary btn-sm waves-effect">
                            <span class="mdi mdi-step-backward"></span> Back
                        </a>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('project.update', $project->id) }}">
                @csrf

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
                                    <input type="text" class="form-control @error('project_name') is-invalid @enderror" id="project_name" name="project_name" value="{{ old('project_name') ? old('project_name') : $project->project_name }}" placeholder="{{ __('Project Name') }}" maxlength="200" required>
                                    <span class="form-text">{{ __('Maximum 200 chars') }}</span>
                                    <label class="form-label" for="project_name">{{ __('Project Name') }} <span class="text-danger">*</span></label>
                                    @error('project_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select @error('status') is-invalid @enderror" name="status" id="status" required>
                                        <option value="0"@if($project->status == 0) selected="selected" @endif>{{ __('Not Started') }}</option>
                                        <option value="1"@if($project->status == 1) selected="selected" @endif>{{ __('Initiation Stage') }}</option>
                                        <option value="2"@if($project->status == 2) selected="selected" @endif>{{ __('Planning Stage') }}</option>
                                        <option value="3"@if($project->status == 3) selected="selected" @endif>{{ __('Execution Stage') }}</option>
                                        <option value="4"@if($project->status == 4) selected="selected" @endif>{{ __('Monitoring & Controlling Stage') }}</option>
                                        <option value="5"@if($project->status == 5) selected="selected" @endif>{{ __('Testing Phase') }}</option>
                                        <option value="6"@if($project->status == 6) selected="selected" @endif>{{ __('Closure Stage') }}</option>
                                        <option value="7"@if($project->status == 7) selected="selected" @endif>{{ __('Completed') }}</option>
                                        <option value="8"@if($project->status == 8) selected="selected" @endif>{{ __('Hannded Over to Client') }}</option>
                                    </select>
                                    <label class="form-label" for="status">{{ __('Project Status') }} <span class="text-danger">*</span></label>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline mb-3">
                                    <textarea class="form-control @error('project_description') is-invalid @enderror" name="project_description" id="project_description">{{ old('project_description') ? old('project_description') : $project->description }}</textarea>
                                    <label class="form-label" for="project_description">{{ __('Project Description') }} <span class="text-danger">*</span></label>
                                    @error('project_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select select2 @error('client_id') is-invalid @enderror" name="client_id" id="client_id"></select>
                                    <label class="form-label" for="client_id">{{ __('Client') }}</label>
                                    @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input class="form-control @error('project_hours') is-invalid @enderror" type="number" name="project_hours" id="project_hours" value="{{ old('project_hours') ? old('project_hours') : $project->project_hours }}" placeholder="{{ __('Project Hours') }}" required>
                                    <label class="form-label" for="project_hours">{{ __('Project Hours') }} <span class="text-danger">*</span></label>
                                    @error('project_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('start_date') is-invalid @enderror datepicker" name="start_date" id="start_date" value="{{ old('start_date') ? old('start_date') : optional($project->start_date)->format('Y-m-d') }}" placeholder="{{ __('Start Date') }}" autocomplete="off">
                                    <label class="form-label" for="start_date">{{ __('Start Date') }}</label>
                                    @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('end_date') is-invalid @enderror datepicker" name="end_date" id="end_date" value="{{ old('end_date') ? old('end_date') : optional($project->end_date)->format('Y-m-d') }}" placeholder="{{ __('End Date') }}" autocomplete="off">
                                    <label class="form-label" for="end_date">{{ __('End Date') }}</label>
                                    @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary" name="submit">Save</button>
                    <a href="{{ route('project.details', $project->id) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
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
    tinymce.init({
        selector: 'textarea#project_description',
        license_key: 'gpl',
        branding: false,
        promotion: false,
        theme: 'silver',
        skin: 'tinymce-5'
    });

    $(function(){
        $('#client_id').select2({
            theme: 'bootstrap-5',
            width: 'resolve',
            multiple: false,
            minimumInputLength: 3,
            delay: 200,
            placeholder: "{{ __('Select client') }}",
            ajax: {
                url: "{{ route('client.get_clients') }}",
                dataType: 'json',
                processResults: function(data, params){
                    return {
                        results: $.map(data.items, function (item) {
                            return {
                                label: item.full_name+' ('+item.email+')',
                                id: item.id
                            }
                        })
                    }
                }
            },
            templateResult: function(item){
                if (item.loading) {
                    return item.text;
                }

                return item.label;
            },
            templateSelection: function(item){
                return item.label || item.text;
            }
        });

        @if($project->client()->count() > 0)
            $('#client_id').append($('<option></option>').attr('value', '{{ $project->client()->first()->id }}').text("{{ $project->client()->first()->full_name.' ('.$project->client()->first()->email.')' }}"));
        @endif

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true,
            todayBtn: true,
            startDate: new Date()
        });
    });
</script>
@endpush