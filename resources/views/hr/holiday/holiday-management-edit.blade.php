@extends('app')

@section('title', 'Edit Holiday Management')

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
    <li class="breadcrumb-item">Update Holiday Management</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form method="POST" action="{{ route('hr.holiday.management.update', [$holiday->id]) }}" class="prevent-multiple-submits" autocomplete="off">
                @csrf
                @method('PUT')
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title">{{ __('Holiday Management Form') }}</h5>
                        <div class=" text-end pt-0 my-n5">
                            <a href="{{ route('hr.configuration') }}" class="btn btn-outline-primary btn-sm waves-effect">
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
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name', $holiday->name) }}" placeholder="{{ __('Holiday Name') }}" required>
                                    <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" name="date" id="date" value="{{ \Carbon\Carbon::parse($holiday->date)->format('Y-m-d') }}" required>
                                    <label for="date">{{ __('Date') }} <span class="text-danger">*</span></label>
                                    @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline mb-3">
                                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" style="height: 100px;">{{ old('description', $holiday->description) }}</textarea>
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