@extends('app')

@section('title', 'Edit Leave Type')

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
        <a href="{{ route('hr.configuration') }}">Configuration</a>
    </li>
    <li class="breadcrumb-item">Update Leave Type</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form method="POST" action="{{ route('hr.leave.type.update', [$leaveType->id]) }}" class="prevent-multiple-submits" autocomplete="off">
                @csrf
                @method('PUT')
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title">{{ __('Leave Type Form') }}</h5>
                        <div class=" text-end pt-0 my-n5">
                            <a href="{{ route('hr.configuration') }}" class="btn btn-outline-primary btn-sm waves-effect">
                                <span class="mdi mdi-step-backward"></span> {{ __('Back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <div class="form-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name', $leaveType->name) }}" placeholder="{{ __('Leave Type Name') }}" required>
                                    <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-4">
                                    <input type="number" class="form-control @error('max_days_per_year') is-invalid @enderror" name="max_days_per_year" id="max_days_per_year" value="{{ old('max_days_per_year', $leaveType->max_days_per_year) }}" placeholder="{{ __('Number of days') }}" required>
                                    <label for="max_days_per_year">{{ __('Max Days Per Year') }} <span class="text-danger">*</span></label>
                                    @error('max_days_per_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-4">
                                    <input type="color" class="form-control @error('bg_color') is-invalid @enderror" name="bg_color" id="bg_color" value="{{ old('bg_color', $leaveType->bg_color ?? '#41d8ba') }}">
                                    <label for="bg_color">{{ __('Background Color') }}</label>
                                </div>
                                @error('bg_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-4">
                                    <input type="color" class="form-control @error('font_color') is-invalid @enderror" name="font_color" id="font_color" value="{{ old('font_color', $leaveType->font_color ?? '#4d4d4d') }}">
                                    <label for="font_color">{{ __('Font Color') }}</label>
                                </div>
                                @error('font_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline mb-3">
                                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" style="height: 100px;">{{ old('description', $leaveType->description) }}</textarea>
                                    <label for="description">{{ __('Description') }}</label>
                                </div>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
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
        initCustomTinyMCE('textarea#description', 280);
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Select an employee",
            allowClear: false
        });
    }); // document closed
</script>
@endpush