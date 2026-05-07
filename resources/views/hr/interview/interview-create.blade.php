@extends('app')

@section('title', 'Add Interview')

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
        <a href="{{ route('hr.recruitment') }}">Recruitment</a>
    </li>
    <li class="breadcrumb-item">Create Interview</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form method="POST" action="{{ route('hr.interview.store') }}" class="prevent-multiple-submits" autocomplete="off">
                @csrf
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title">{{ __('Interview Form') }}</h5>
                        <div class=" text-end pt-0 my-n5">
                            <a href="{{ route('hr.recruitment') }}" class="btn btn-outline-primary btn-sm waves-effect">
                                <span class="mdi mdi-step-backward"></span> {{ __('Back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-body">
                        <div class="row">

                            <!-- Applicant -->
                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select select2 @error('applicant_id') is-invalid @enderror" name="applicant_id" id="applicant_id" required>
                                        <option value="">{{ __('Select Applicant') }}</option>
                                        @foreach($applicants as $applicant)
                                        <option value="{{ $applicant->id }}" @selected(old('applicant_id') == $applicant->id)>
                                            {{ $applicant->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="applicant_id">{{ __('Applicant') }} <span class="text-danger">*</span></label>
                                    @error('applicant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Interviewer -->
                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select select2 @error('interviewer_id') is-invalid @enderror" name="interviewer_id" id="interviewer_id" required>
                                        <option value="">{{ __('Select Interviewer') }}</option>
                                        @foreach($interviewers as $interviewer)
                                        <option value="{{ $interviewer->id }}" @selected(old('interviewer_id') == $interviewer->id)>
                                            {{ $interviewer->full_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="interviewer_id">{{ __('Interviewer') }} <span class="text-danger">*</span></label>
                                    @error('interviewer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- scheduled_date -->
                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date') }}" placeholder="{{ __('Scheduled Date') }}" onclick="this.showPicker();" required>
                                    <label for="scheduledDate">{{ __('Scheduled Date') }} <span class="text-danger">*</span></label>
                                    @error('scheduled_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- INTERVIEW_STATUS -->
                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select @error('status') is-invalid @enderror" name="status" id="status" required>
                                        <option value="">{{ __('Select Status') }}</option>
                                        @foreach(Enum::INTERVIEW_STATUS as $key => $value)
                                        <option value="{{ $key }}"  @selected(old('status') == $key)>
                                            {{ $value['label'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Feedback -->
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline mb-3">
                                    <textarea class="form-control @error('feedback') is-invalid @enderror" name="feedback" id="feedback">{{ old('feedback') }}</textarea>
                                    <label for="feedback">{{ __('Feedback') }}</label>
                                    @error('feedback')
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
        initCustomTinyMCE('textarea#feedback', 380);
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Select department",
            allowClear: false
        });
    }); // document closed
</script>
@endpush