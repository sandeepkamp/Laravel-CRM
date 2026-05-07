@extends('app')

@section('title', 'Add Employee History')

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
        <li class="breadcrumb-item">Create Employee History</li>
    </ol>
@endsection

@section('content')
    <!-- Hidden clean history block template -->
    <div id="history-template" style="display: none;">
        <div class="history-block row px-3 py-4 mb-3" style="border: 1px solid #ddd; border-radius: 3px;">
            <div class="col-md-6">
                <div class="form-floating form-floating-outline mb-3">
                    <input type="text" class="form-control" name="company_name[]" placeholder="{{ __('Company Name') }}" required>
                    <label>{{ __('Company Name') }} <span class="text-danger">*</span></label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating form-floating-outline mb-3">
                    <input type="text" class="form-control" name="designation[]" placeholder="{{ __('Designation') }}" required>
                    <label>{{ __('Designation') }} <span class="text-danger">*</span></label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating form-floating-outline mb-3">
                    <input type="date" class="form-control" name="from_date[]" required>
                    <label>{{ __('From Date') }} <span class="text-danger">*</span></label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating form-floating-outline mb-3">
                    <input type="date" class="form-control" name="to_date[]" required>
                    <label>{{ __('To Date') }} <span class="text-danger">*</span></label>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-floating form-floating-outline">
                    <textarea class="form-control" name="reason_for_leaving[]" style="height: 100px;"></textarea>
                    <label>{{ __('Reason for Leaving') }}</label>
                </div>
            </div>

            <div class="col-12 text-end">
                <button type="button" class="btn btn-danger remove-history-block">
                    <i class="mdi mdi-delete"></i> Remove
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <form method="POST" action="{{ route('hr.employee.history.store') }}" class="prevent-multiple-submits" autocomplete="off">
                    @csrf
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title">{{ __('Employee History Form') }}</h5>
                        <div class=" text-end pt-0 my-n5">
                            <a href="{{ route('hr.employee.management') }}" class="btn btn-outline-primary btn-sm waves-effect">
                                <span class="mdi mdi-step-backward"></span> {{ __('Back') }}
                            </a>
                        </div>
                    </div>
                    </div>
                    <div class="card-body pb-0">
                        <div class="form-body">
                            <div class="row">

                                <div class="col-md-12">
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

                                <div class="col-md-12 px-4">
                                <div id="history-container">
                                    <div class="history-block row px-3 py-4 mb-3" style="border: 1px solid #ddd; border-radius: 3px;">
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline mb-3">
                                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name[]" id="company_name" value="{{ old('company_name') }}" placeholder="{{ __('Company Name') }}" required>
                                                <label for="company_name">{{ __('Company Name') }} <span class="text-danger">*</span></label>
                                                @error('company_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                            
                                        <div class="col-md-6 mb-2">
                                            <div class="form-floating form-floating-outline mb-3">
                                                <input type="text" class="form-control @error('designation') is-invalid @enderror" name="designation[]" id="designation" value="{{ old('designation') }}" placeholder="{{ __('Designation') }}" required>
                                                <label for="designation">{{ __('Designation') }} <span class="text-danger">*</span></label>
                                                @error('designation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                            
                                        <div class="col-md-6 mb-2">
                                            <div class="form-floating form-floating-outline mb-3">
                                                <input type="date" class="form-control @error('from_date') is-invalid @enderror" name="from_date[]" id="from_date" value="{{ old('from_date') }}" required>
                                                <label for="from_date">{{ __('From Date') }} <span class="text-danger">*</span></label>
                                                @error('from_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                            
                                        <div class="col-md-6 mb-2">
                                            <div class="form-floating form-floating-outline mb-3">
                                                <input type="date" class="form-control @error('to_date') is-invalid @enderror" name="to_date[]" id="to_date" value="{{ old('to_date') }}" required>
                                                <label for="to_date">{{ __('To Date') }} <span class="text-danger">*</span></label>
                                                @error('to_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                            
                                        <div class="col-md-12">
                                            <div class="form-floating form-floating-outline">
                                                <textarea class="form-control @error('reason_for_leaving') is-invalid @enderror" name="reason_for_leaving[]" id="reason_for_leaving_0" style="height: 100px;">{{ old('reason_for_leaving') }}</textarea>
                                                <label for="reason_for_leaving">{{ __('Reason for Leaving') }}</label>
                                                @error('reason_for_leaving')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                </div>
                            
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary" id="add-more-history">+ Add More</button>
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
            
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: "Select an employee",
                allowClear: false
            });
            
            initCustomTinyMCE('textarea#reason_for_leaving_0', 280);
            let historyIndex = 1;

            $('#add-more-history').click(function () {
                let $clone = $('#history-template .history-block').clone();

                let newId = 'reason_for_leaving_' + historyIndex;
                $clone.find('textarea[name="reason_for_leaving[]"]').attr('id', newId);

                $('#history-container').append($clone);
                initCustomTinyMCE('#' + newId, 280);
                historyIndex++;
            });

            $(document).on('click', '.remove-history-block', function () {
                if ($('.history-block').length > 1) {
                    // Remove TinyMCE instance before removing the block
                    let textarea = $(this).closest('.history-block').find('textarea');
                    if (textarea.length && tinymce.get(textarea.attr('id'))) {
                        tinymce.remove('#' + textarea.attr('id'));
                    }

                    $(this).closest('.history-block').remove();
                } else {
                    alert('At least one history block is required.');
                }
            });

        }); // document closed
    </script>
@endpush
