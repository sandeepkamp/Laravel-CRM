@extends('app')

@section('title', 'Add Job Posting')

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
    <li class="breadcrumb-item">Create Job Posting</li>
</ol>
@endsection

@section('content')
<div class="row">
    
    <div class="col-12">
        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong><i class="tf-icons mdi mdi-alert-outline"></i> Error -</strong>{{ session()->get('error') }}
            </div>
        @endif
    </div>

    <div class="col-12">
        <div class="card">
            <form method="POST" action="{{ route('hr.job.posting.store') }}" class="prevent-multiple-submits" autocomplete="off">
                @csrf
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title">{{ __('Job Posting Form') }}</h5>
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

                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" id="title" value="{{ old('title') }}" placeholder="{{ __('Title') }}" required>
                                    <label for="title">{{ __('Title') }} <span class="text-danger">*</span></label>
                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select @error('department_id') is-invalid @enderror" name="department_id" id="department_id" required>
                                        <option value="">{{ __('Select Department') }}</option>
                                        @foreach($department as $dep)
                                        <option value="{{ $dep->id }}" @selected(old('department_id') == $dep->id)>
                                            {{ $dep->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="department_id">{{ __('Department') }} <span class="text-danger">*</span></label>
                                    @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select @error('status') is-invalid @enderror" name="status" id="status" required>
                                        <option value="">{{ __('Select Status') }}</option>
                                        @foreach(Enum::JOB_POSTING_STATUS as $key => $value)
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

                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('location') is-invalid @enderror" name="location" id="location" value="{{ old('location') }}" placeholder="{{ __('Location') }}" required>
                                    <label for="location">{{ __('Location') }} <span class="text-danger">*</span></label>
                                    @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline mb-3">
                                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description">{{ old('description') }}</textarea>
                                    <label for="description">{{ __('Description') }}</label>
                                    @error('description')
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
        initCustomTinyMCE('textarea#description', 380);
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Select department",
            allowClear: false
        });
    }); // document closed
</script>
@endpush