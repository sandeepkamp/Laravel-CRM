@extends('app')

@section('title', 'Add Applicant')

<!-- page css -->
@push('style')
<x-styles :files="[
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
    'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css',
    'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css',
]"/>
@endpush

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('hr.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('hr.recruitment') }}">Recruitment</a>
    </li>
    <li class="breadcrumb-item">Create Applicant</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form method="POST" action="{{ route('hr.applicant.store') }}" class="prevent-multiple-submits" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title">{{ __('Applicant Form') }}</h5>
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
                            <div class="col-6">
                                <div class="row">
                                    
                                    <!-- select2 -->
                                    <div class="col-md-12 mb-2">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <select class="form-select select2 @error('job_posting_id') is-invalid @enderror" name="job_posting_id" id="job_posting_id" required>
                                                <option value="">{{ __('Select Job Posting') }}</option>
                                                @foreach($jobPostings as $jobPosting)
                                                <option value="{{ $jobPosting->id }}" @selected(old('job_posting_id') == $jobPosting->id)>
                                                    {{ $jobPosting->title }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <label for="job_posting_id">{{ __('Job Posting') }} <span class="text-danger">*</span></label>
                                            @error('job_posting_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- type="text" -->
                                    <div class="col-md-12 mb-2">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name') }}" placeholder="{{ __('Enter Name') }}" required>
                                            <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                                            @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- type="email" -->
                                    <div class="col-md-12 mb-2">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email') }}" placeholder="{{ __('Enter Email') }}" required>
                                            <label for="email">{{ __('Email') }} <span class="text-danger">*</span></label>
                                            @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- type="number" -->
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="number" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" value="{{ old('phone') }}" placeholder="{{ __('Enter Phone') }}">
                                            <label for="phone">{{ __('Phone') }}</label>
                                            @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- type="file" -->
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label mb-0" for="resume_path">{{ __('Upload Resume') }}</label>
                                        <input type="file" class="form-control" name="resume_path" id="resume_path" accept=".pdf, .doc, .docs" aria-describedby="resume_path" style="padding: 10px;">
                                        <span class="form-text">{{ __('Supported format pdf, doc, docs - Maximum file size: 10 MB') }}</span>
                                        @error('resume_path')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- select2 -->
                                    <div class="col-md-12 mb-2">
                                        <div class="form-floating form-floating-outline mb-3">
                                            <select class="form-select @error('status') is-invalid @enderror" name="status" id="status" required>
                                                <option value="">{{ __('Select Status') }}</option>
                                                @foreach(Enum::APPLICANT_STATUS as $key => $value)
                                                <option value="{{ $key }}" @selected(old('status') == $key)>
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

                                </div>
                            </div>

                            <div class="col-6">
                                <!-- textarea -->
                                <div class="form-floating form-floating-outline mb-3">
                                    <textarea class="form-control @error('address') is-invalid @enderror" name="address" id="address">{{ old('address') }}</textarea>
                                    <label class="form-label" for="address">{{ __('Address') }} </label>
                                    @error('address')
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
<x-scripts :files="[
    'vendor/tinymce/tinymce.min.js',
    'assets/js/tinymce-helper.js',
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
]"/>

<script type="text/javascript">
    $('document').ready(function() {
        initCustomTinyMCE('textarea#address', 380);
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Select Job Posting",
            allowClear: false
        });
    }); // document closed
</script>
@endpush
