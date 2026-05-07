@extends('app')

@section('title', 'Add Salary Management')

@push('style')
<!-- page css -->
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
        <a href="{{ route('hr.payroll.management') }}">Payroll</a>
    </li>
    <li class="breadcrumb-item">Create Salary Management</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form method="POST" action="{{ route('hr.salary.management.store') }}" enctype="multipart/form-data" class="prevent-multiple-submits" autocomplete="off">
                @csrf
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title">{{ __('Salary Management Form') }}</h5>
                        <div class=" text-end pt-0 my-n5">
                            <a href="{{ route('hr.payroll.management') }}" class="btn btn-outline-primary btn-sm waves-effect">
                                <span class="mdi mdi-step-backward"></span> {{ __('Back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-body">
                        <div class="row">

                            <!-- employee_id -->
                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select select2 @error('employee_id') is-invalid @enderror" name="employee_id" id="employee_id" required>
                                        <option value="">{{ __('Select Employee') }}</option>
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>
                                            {{ $employee->full_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="employee_id">{{ __('Employee') }} <span class="text-danger">*</span></label>
                                    @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- year -->
                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select select2 @error('year') is-invalid @enderror" name="year" id="year" required>
                                        <option value="">{{ __('Select Year') }}</option>
                                        @foreach(range(now()->year + 1, now()->year - 1) as $year)
                                            <option value="{{ $year }}" @selected(old('year', date('Y')) == $year)>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="year">{{ __('Year') }} <span class="text-danger">*</span></label>
                                    @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- month -->
                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select select2 @error('month') is-invalid @enderror" name="month" id="month" required>
                                        <option value="">{{ __('Select Month') }}</option>
                                        @foreach(range(1, 12) as $month)
                                            <option value="{{ $month }}" @selected(old('month', date('m')) == $month)>
                                                {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                            </option>
                                        @endforeach 
                                    </select>
                                    <label for="month">{{ __('Month') }} <span class="text-danger">*</span></label>
                                    @error('month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- gross_salary -->
                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" class="form-control @error('gross_salary') is-invalid @enderror" name="gross_salary" id="gross_salary" value="{{ old('gross_salary') }}" placeholder="{{ __('Enter Gross Salary') }}">
                                    <label for="gross_salary">{{ __('Gross Salary') }}</label>
                                    @error('gross_salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- payment_mode -->
                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select select2 @error('payment_mode') is-invalid @enderror" name="payment_mode" id="payment_mode" required>
                                        <option value="">{{ __('Select Payment Mode') }}</option>
                                        @foreach(Enum::PAYMENT_MODE as $key => $value)
                                        <option value="{{ $key }}" @selected(old('payment_mode') == $key)>
                                            {{ $value['label'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="payment_mode">{{ __('Payment Mode') }} <span class="text-danger">*</span></label>
                                    @error('payment_mode')
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
<!-- page js -->
<x-scripts :files="[
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
]"/>
<script>
    $('document').ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            allowClear: false
        });
    }); // document closed
</script>
@endpush