@extends('app')



@section('title', 'Edit Leave')



@section('breadcrumb')

<ol class="breadcrumb">

    <!--<li class="breadcrumb-item">-->

    <!--    <a href="{{ route('dashboard') }}">Dashboard</a>-->

    <!--</li>-->

    <li class="breadcrumb-item">

        <a href="{{ route('leave.list') }}">Leave Dashboard</a>

    </li>

    <li class="breadcrumb-item">Edit Leave</li>

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

            <div class="card-body">

                <form method="POST" action="{{ route('leave.update', $leave->id) }}">

                    @csrf

                    <div class="form-body">

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="datetime-local" class="form-control" id="leave_from" name="leave_from" value="{{ \Carbon\Carbon::parse($leave->leave_from)->format('Y-m-d\TH:i') }}" required>

                                    <label class="form-label" for="leave_from">Leave From <span class="text-danger">*</span></label>

                                    @if($errors->has('leave_from'))

                                        <div class="text-danger">{{ $errors->first('leave_from') }}</div>

                                    @endif

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="datetime-local" class="form-control" id="leave_to" name="leave_to" value="{{ \Carbon\Carbon::parse($leave->leave_to)->format('Y-m-d\TH:i') }}" required>

                                    <label class="form-label" for="leave_to">Leave To <span class="text-danger">*</span></label>

                                    @if($errors->has('leave_to'))

                                        <div class="text-danger">{{ $errors->first('leave_to') }}</div>

                                    @endif

                                </div>

                            </div>

                        </div>

                        <div class="row">

                           <div class="col-md-12">

                                <div class="form-floating form-floating-outline mb-3">

                                    <textarea class="form-control" id="description" name="description" rows="3">{{ $leave->description }}</textarea>

                                    <label class="form-label" for="description">Description <span class="text-danger">*</span></label>

                                </div>

                                @if($errors->has('description'))

                                    <div class="text-danger">{{ $errors->first('description') }}</div>

                                @endif

                            </div>

                        </div>

                    </div>

                    <div class="form-actions">

                        <div class="text-end">

                            <button type="submit" class="btn btn-primary">Update</button>

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

        theme: 'silver',

        skin: 'tinymce-5'

    });



    document.addEventListener('DOMContentLoaded', function() {

        // Get the current date

        let currentDate = new Date().toISOString().split("T")[0];

        // Set the min attribute for both date fields to the current date

        document.getElementById('leave_from').setAttribute('min', currentDate);

        document.getElementById('leave_to').setAttribute('min', currentDate);

    });

</script>

@endpush



