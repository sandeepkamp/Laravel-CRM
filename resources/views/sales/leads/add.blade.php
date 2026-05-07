@extends('app')



@section('title', 'Add New Lead')



@section('breadcrumb')

<ol class="breadcrumb">

    <li class="breadcrumb-item">

        <a href="{{ route('sales_dashboard') }}">Sales Dashboard</a>

    </li>

    <li class="breadcrumb-item">

        <a href="{{ route('lead.list') }}">List</a>

    </li>

    <li class="breadcrumb-item">Add New Lead</li>

</ol>

@endsection



@section('content')

<div class="row">

    <div class="col-12">

        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title">Lead Form</h5>
                    <div class=" text-end pt-0 my-n5">
                        <a href="{{ route('lead.list') }}" class="btn btn-outline-primary btn-sm waves-effect">
                            <span class="mdi mdi-step-backward"></span> Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">

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

                <form method="POST" action="{{ route('lead.add') }}">

                    @csrf

                    <div class="form-body">

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="First Name" autocomplete="off" required>

                                    <label class="form-label" for="first_name">First Name <span class="text-danger">*</span></label>

                                    @error('first_name')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" autocomplete="off" >

                                    <label class="form-label" for="last_name">Last Name </label>

                                    @error('last_name')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Email" autocomplete="off" required>

                                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>

                                    @error('email')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" placeholder="Phone Number"  title="Only numbers, plus (+), and minus (-) are allowed" autocomplete="off">

                                    <label class="form-label" for="phone_number">Phone Number</label>

                                    @error('phone_number')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Company Name" autocomplete="off">

                                    <label class="form-label" for="company_name">Company Name <span class="text-danger">*</span></label>

                                    @error('company_name')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website') }}" placeholder="Website" autocomplete="off">

                                    <label class="form-label" for="website">Website</label>

                                    @error('website')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation" value="{{ old('designation') }}" placeholder="Job Title" autocomplete="off">

                                    <label class="form-label" for="designation">Job Title</label>

                                    @error('designation')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="datepicker form-control @error('close_date') is-invalid @enderror" id="close_date" name="close_date" value="{{ old('close_date') ? old('close_date') : date('Y-m-d') }}" placeholder="yyyy-mm-dd" autocomplete="off">

                                    <label class="form-label" for="close_date">Expected Close Date (Optional)</label>

                                    @error('close_date')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-group mb-3">

                                    <label class="form-label" for="lead_value">Value</label>

                                    <div class="input-group mb-3">

                                        <span class="input-group-text">{{ $global_currency->symbol }}</span>

                                        <input type="number" class="form-control @error('lead_value') is-invalid @enderror" aria-label="Amount (to the nearest pound)" name="lead_value" id="lead_value" value="{{ old('value') ? old('value') : '0.00' }}" placeholder="0.00" step="0.01" min="0" onkeypress="return isNumberKey(event);" autocomplete="off">

                                    </div>

                                    <input type="hidden" name="currency" value="{{ old('currency') ? old('currency') : $global_currency->id }}">

                                    @error('lead_value')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group mb-3">

                                    <label class="form-label" for="labels">Labels</label>

                                    <select class="form-control select2 @error('labels') is-invalid @enderror" name="labels[]" id="labels" multiple>

                                        <option value="">-- select --</option>

                                        @foreach ($labels as $label)

                                        <option value="{{ $label->id }}">{{ $label->name }}</option>

                                        @endforeach

                                    </select>

                                    @error('labels')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="form-actions">

                        <div class="text-end">

                            <button type="submit" class="btn btn-primary" name="submit">ADD</button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection



@section('style')

<!-- Styles -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<!-- Or for RTL support -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.standalone.min.css" integrity="sha512-D5/oUZrMTZE/y4ldsD6UOeuPR4lwjLnfNMWkjC0pffPTCVlqzcHTNvkn3dhL7C0gYifHQJAIrRTASbMvLmpEug==" crossorigin="anonymous" referrerpolicy="no-referrer" />

@endsection



@section('script')

<!-- Scripts -->

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script type="text/javascript">

    function isNumberKey(evt) {

        var charCode = (evt.which) ? evt.which : evt.keyCode

        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)

            return false;

        return true;

    }

    $(document).ready(function() {

        $('.select2').select2({

            theme: 'bootstrap-5',

        });

        $('.datepicker').datepicker({

            format: 'yyyy-mm-dd',

            todayHighlight: true,

            autoclose: true,

            todayBtn: true,

            startDate: new Date()

        });

    });

    $(function(){

        $("#website").blur(function() {

            var tarea = $(this).val();

            if(tarea == ''){

                return false;

            }

            if (tarea.indexOf("http://") == 0 || tarea.indexOf("https://") == 0) {

            } else {

                var url = 'http://' + $(this).val();

                $('#website').val(url);

            }

        });

    });

</script>

@endsection