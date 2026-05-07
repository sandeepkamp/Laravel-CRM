@extends('app')



@section('title', 'Request Leave')



@section('breadcrumb')

<ol class="breadcrumb">

    <li class="breadcrumb-item">

        <a href="{{ route('leave.list') }}">Leave Dashboard</a>

    </li>

    <li class="breadcrumb-item">Submit Leave Request</li>

</ol>

@endsection



@section('content')

<div class="row">

    <div class="col-12">

        <div class="card">

            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title">Request Leave Form</h5>
                    <div class=" text-end pt-0 my-n5">
                        <a href="{{ route('leave.list') }}" class="btn btn-outline-primary btn-sm waves-effect">
                            <span class="mdi mdi-step-backward"></span> Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body pt-4">

                @if(session()->has('success'))

                <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show">

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                    <strong><i class="tf-icons mdi mdi-check-circle-outline"></i> Success -</strong>

                    {{ session()->get('success') }}

                </div>

                @endif

                @if(session()->has('error'))

                <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show">

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                    <strong><i class="tf-icons mdi mdi-alert-outline"></i> Error -</strong>

                    {{ session()->get('error') }}

                </div>

                @endif

                <form method="POST" action="{{ route('leave.submit_request') }}" enctype="multipart/form-data" class="prevent-multiple-submits">

                    @csrf

                    <div class="form-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="row">
                                    <!-- employee -->
                                    <div class="col-md-12">

                                        <div class="form-floating form-floating-outline mb-4">

                                            <select class="form-control" id="user_id" name="user_id" required {{ auth()->user()->role != 1 && auth()->user()->role != 3  ? 'readonly' : '' }}>

                                                @foreach($employees as $employee)

                                                    <option value="{{ $employee->id }}" {{ old('user_id', auth()->id()) == $employee->id ? 'selected' : '' }}>

                                                        {{ $employee->first_name . ' ' . $employee->last_name }}

                                                    </option>

                                                @endforeach

                                            </select>

                                            <label class="form-label" for="user_id">Employee <span class="text-danger">*</span></label>

                                        </div>

                                    </div>

                                    <!-- leave type -->
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline mb-4">
                                            <select class="form-control @error('leave_type_id') is-invalid @enderror" id="leave_type_id" name="leave_type_id" required>
                                                <option value="">---Select Leave Type---</option>
                                                @foreach($leaveTypes as $leaveType)
                                                    <option value="{{ $leaveType->id }}" @selected(old('leave_type_id') == $leaveType->id) >
                                                        {{ $leaveType->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label class="form-label" for="leave_type_id">Leave Type <span class="text-danger">*</span></label>
                                        </div>
                                        @error('leave_type_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- leave from -->
                                    <div class="col-md-12">

                                        <div class="form-floating form-floating-outline mb-4">

                                            <input type="datetime-local" class="form-control" id="leave_from" name="leave_from" value="{{ old('leave_from') }}" onclick="this.showPicker();" required>

                                            <label class="form-label" for="leave_from">Leave From <span class="text-danger">*</span></label>

                                            @if($errors->has('leave_from'))

                                                <div class="text-danger">{{ $errors->first('leave_from') }}</div>

                                            @endif

                                        </div>

                                    </div>

                                    <!-- leave to -->
                                    <div class="col-md-12">

                                        <div class="form-floating form-floating-outline mb-4">

                                            <input type="datetime-local" class="form-control" id="leave_to" name="leave_to" value="{{ old('leave_to') }}" onclick="this.showPicker();" required>

                                            <label class="form-label" for="leave_to">Leave To <span class="text-danger">*</span></label>

                                            @if($errors->has('leave_to'))

                                                <div class="text-danger">{{ $errors->first('leave_to') }}</div>

                                            @endif

                                        </div>

                                    </div>

                                    <!-- document -->
                                    <div class="col-md-12">

                                        <div class="form-group mb-4">

                                            <input type="file" class="form-control" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png" style="padding: 10px;">

                                            <label class="form-label" for="attachment">Upload Document (PDF/Images)</label>

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row">

                                    <!-- description -->
                                    <div class="col-md-12">

                                        <div class="form-floating form-floating-outline mb-3">

                                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>

                                            <label class="form-label" for="description">Description <span class="text-danger">*</span></label>

                                        </div>

                                        @if($errors->has('description'))

                                            <div class="text-danger">{{ $errors->first('description') }}</div>

                                        @endif

                                    </div>
                                    
                                </div>
                            </div>

                        </div>

                        <!-- <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <select class="form-control" id="user_id" name="user_id" required {{ auth()->user()->role != 1 && auth()->user()->role != 3  ? 'readonly' : '' }}>

                                        @foreach($employees as $employee)

                                            <option value="{{ $employee->id }}" {{ old('user_id', auth()->id()) == $employee->id ? 'selected' : '' }}>

                                                {{ $employee->first_name . ' ' . $employee->last_name }}

                                            </option>

                                        @endforeach

                                    </select>

                                    <label class="form-label" for="user_id">Employee <span class="text-danger">*</span></label>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="datetime-local" class="form-control" id="leave_from" name="leave_from" value="{{ old('leave_from') }}" onclick="this.showPicker();" required>

                                    <label class="form-label" for="leave_from">Leave From <span class="text-danger">*</span></label>

                                     @if($errors->has('leave_from'))

                                        <div class="text-danger">{{ $errors->first('leave_from') }}</div>

                                    @endif

                                </div>

                            </div>

                        </div> -->

                        <!-- <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="datetime-local" class="form-control" id="leave_to" name="leave_to" value="{{ old('leave_to') }}" onclick="this.showPicker();" required>

                                    <label class="form-label" for="leave_to">Leave To <span class="text-danger">*</span></label>

                                    @if($errors->has('leave_to'))

                                        <div class="text-danger">{{ $errors->first('leave_to') }}</div>

                                    @endif

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group mb-3">

                                    <input type="file" class="form-control" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png">

                                    <label class="form-label" for="attachment">Upload Document (PDF/Images)</label>

                                </div>

                            </div>

                        </div> -->

                        <!-- <div class="row">

                           <div class="col-md-12">

                                <div class="form-floating form-floating-outline mb-3">

                                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>

                                    <label class="form-label" for="description">Description <span class="text-danger">*</span></label>

                                </div>

                                @if($errors->has('description'))

                                    <div class="text-danger">{{ $errors->first('description') }}</div>

                                @endif

                            </div>

                        </div> -->

                    </div>

                    <div class="form-actions">

                        <div class="text-end">

                            <button type="submit" class="btn btn-primary">Submit</button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection



@push('script')

<script src="{{ url('vendor/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">

    tinymce.init({

        selector: 'textarea#description',

        license_key: 'gpl',

        branding: false,

        promotion: false,
        menubar: false,

        theme: 'silver',

        skin: 'tinymce-5',
        height: 300

    });



    document.addEventListener('DOMContentLoaded', function() {

        // Get the current date

        let currentDate = new Date().toISOString().split("T")[0];

        // Set the min attribute for both date fields to the current date

        document.getElementById('leave_from').setAttribute('min', currentDate);

        document.getElementById('leave_to').setAttribute('min', currentDate);

        

        // Initialize Select2 for the Employee Select Field

        $('#user_id').select2({

            theme: 'bootstrap-5', // Optional: use Bootstrap 5 theme for styling

            placeholder: "Select an employee", // Placeholder text

            allowClear: false // Allow clearing of selection

        });

    });

</script>

@endpush

