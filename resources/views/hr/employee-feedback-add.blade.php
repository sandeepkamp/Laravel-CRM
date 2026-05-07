@extends('app')

@section('title', 'Add Employee Feedback')

@push('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('hr.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('hr.employee.management') }}">Employee</a>
    </li>
    <li class="breadcrumb-item">Create Employee Feedback</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form method="POST" action="{{ route('hr.employee.feedback.store') }}" class="prevent-multiple-submits" autocomplete="off">
                @csrf
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title">{{ __('Employee Feedback Form') }}</h5>
                        <div class=" text-end pt-0 my-n5">
                            <a href="{{ route('hr.employee.management') }}" class="btn btn-outline-primary btn-sm waves-effect">
                                <span class="mdi mdi-step-backward"></span> {{ __('Back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select select2 @error('employee_id') is-invalid @enderror" name="employee_id" id="employee_id" required>
                                        <option value="">{{ __('Select Employee') }}</option>
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>
                                            {{ $employee->first_name . ' ' . $employee->last_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="employee_id">{{ __('Employee') }} <span class="text-danger">*</span></label>
                                    @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select select2 @error('quarter') is-invalid @enderror" name="quarter" id="quarter" required>
                                        @foreach(Enum::QUARTER as $key => $value)
                                        <option value="{{ $key }}" @selected(old('quarter') == $key)>
                                            {{ $value['label'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="quarter">{{ __('Quarter') }} <span class="text-danger">*</span></label>
                                    @error('quarter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" class="form-control @error('rating') is-invalid @enderror" name="rating" id="rating" value="{{ old('rating') }}" placeholder="{{ __('Company Name') }}" max="10" step="0.1" required aria-describedby="ratingHelp">
                                    <label for="rating">{{ __('Rating') }} <span class="text-danger">*</span></label>
                                    <span id="ratingHelp" class="form-text">Provide a rating out of 10.</span>
                                    @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline mb-3">
                                    <textarea class="form-control @error('feedback_text') is-invalid @enderror" name="feedback_text" id="feedback_text" style="height: 100px;">{{ old('feedback_text') }}</textarea>
                                    <label for="feedback_text">{{ __('Feedback') }}</label>
                                    @error('feedback_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script src="{{ asset('assets/js/tinymce-helper.js') }}"></script>
<script>
    $('document').ready(function() {
        initCustomTinyMCE('textarea#feedback_text', 280);
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Select an employee",
            allowClear: false
        });
    }); // document closed
</script>
@endpush