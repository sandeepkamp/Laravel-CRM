@extends('app')

@section('title', 'Add Asset Handover')

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
        <a href="{{ route('hr.exit.process') }}">Exit Process</a>
    </li>
    <li class="breadcrumb-item">Create Asset Handover</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form method="POST" action="{{ route('hr.asset.handover.store') }}" class="prevent-multiple-submits" autocomplete="off">
                @csrf
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title">{{ __('Asset Handover Form') }}</h5>
                        <div class=" text-end pt-0 my-n5">
                            <a href="{{ route('hr.exit.process') }}" class="btn btn-outline-primary btn-sm waves-effect">
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
                                    <select class="form-select select2 @error('employee_id') is-invalid @enderror" name="employee_id" id="employee_id" required>
                                        <option value="">{{ __('Select Employee') }}</option>
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>
                                            {{ $employee->full_name ?? ($employee->first_name . ' ' . $employees->last_name) }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="employee_id">{{ __('Employee') }} <span class="text-danger">*</span></label>
                                    @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select select2 @error('handover_status') is-invalid @enderror" name="handover_status" id="handover_status" required>
                                        <option value="">{{ __('Select Status') }}</option>
                                        @foreach(Enum::ASSET_HANDOVER_STATUS as $key => $value)
                                        <option value="{{ $key }}" @selected(old('handover_status') == $key)>
                                            {{ $value['label'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="handover_status">{{ __('Handover Status') }} <span class="text-danger">*</span></label>
                                    @error('handover_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('asset_name') is-invalid @enderror" name="asset_name" id="asset_name" value="{{ old('asset_name') }}" placeholder="{{ __('Asset Name') }}" required>
                                    <label for="asset_name">{{ __('Asset Name') }} <span class="text-danger">*</span></label>
                                    @error('asset_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="date" class="form-control @error('handover_date') is-invalid @enderror" name="handover_date" id="handover_date" value="{{ old('handover_date') }}" placeholder="{{ __('Handover Date') }}" onclick="this.showPicker();" required>
                                    <label for="handover_date">{{ __('Handover Date') }} <span class="text-danger">*</span></label>
                                    @error('handover_date')
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
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
]"/> 
<script>
    $('document').ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Select department",
            allowClear: false
        });
    }); // document closed
</script>
@endpush
