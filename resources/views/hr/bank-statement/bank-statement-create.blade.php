@extends('app')

@section('title', 'Add Bank Statement')

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
        <a href="{{ route('hr.payroll.management') }}">Payroll</a>
    </li>
    <li class="breadcrumb-item">Create Bank Statement</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form method="POST" action="{{ route('hr.bank.statement.store') }}" class="prevent-multiple-submits" autocomplete="off">
                @csrf
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title">{{ __('Bank Statement Form') }}</h5>
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
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" name="bank_name" id="bank_name" value="{{ old('bank_name') }}" placeholder="{{ __('Enter Bank Name') }}" required>
                                    <label for="bank_name">{{ __('Bank Name') }} <span class="text-danger">*</span></label>
                                    @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('branch_name') is-invalid @enderror" name="branch_name" id="branch_name" value="{{ old('branch_name') }}" placeholder="{{ __('Enter Branch Name') }}">
                                    <label for="branch_name">{{ __('Branch Name') }} </label>
                                    @error('branch_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('account_number') is-invalid @enderror" name="account_number" id="account_number" value="{{ old('account_number') }}" placeholder="{{ __('Account Number') }}" required>
                                    <label for="account_number">{{ __('Account Number') }} <span class="text-danger">*</span></label>
                                    @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror" name="ifsc_code" id="ifsc_code" value="{{ old('ifsc_code') }}" placeholder="{{ __('Enter IFSC Code') }}" required>
                                    <label for="ifsc_code">{{ __('IFSC Code') }} <span class="text-danger">*</span></label>
                                    @error('ifsc_code')
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
        initCustomTinyMCE('textarea#description', 280);
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Select an employee",
            allowClear: false
        });
    }); // document closed
</script>
@endpush