@extends('app')

@section('title', 'Add Hosting')

@section('breadcrumb')

<ol class="breadcrumb">

    <li class="breadcrumb-item">

        <a href="{{ route('hosting_dashboard') }}">Hosting Dashboard</a>

    </li>

    <li class="breadcrumb-item">

        <a href="{{ route('hosting.list') }}">List</a>

    </li>

    <li class="breadcrumb-item">Add New Hosting</li>

</ol>

@endsection



@section('content')

<div class="row">

    <div class="col-12">

        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title">Hosting Form</h5>
                    <div class=" text-end pt-0 my-n5">
                        <a href="{{ route('hosting.list') }}" class="btn btn-outline-primary btn-sm waves-effect">
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

                @if($errors->any())

                    @foreach($errors->all() as $error)

                    <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show">

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                        <strong><i class="tf-icons mdi mdi-alert-outline"></i> Error -</strong>

                        {{ $error }}

                    </div>

                    @endforeach

                @endif

                <form method="POST" action="{{ route('hosting.add') }}" id="hostingform">

                    @csrf

                    <div class="form-body">

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <select class="form-control select2 @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>

                                        <option value="">Select a Client</option>

                                        @foreach ($clients as $client)

                                            <option value="{{ $client->id }}" @if(old('client_id') == $client->id) selected @endif>

                                                {{ $client->first_name }} {{ $client->last_name }}

                                            </option>

                                        @endforeach

                                    </select>

                                    <label class="form-label" for="client_id">Clients</label>

                                    @error('client_id')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group mb-3">

                                    <!--<label class="form-label" for="cost">Price</label>-->

                                    <div class="input-group">

                                        <span class="input-group-text">£</span>

                                        <input type="number" min="0" class="form-control @error('cost') is-invalid @enderror" aria-label="Amount" name="cost" id="cost" value="{{ old('cost') }}" placeholder="0.00"  oninput="limitCostInput(this)" 

                step="0.01" >

                                    </div>

                                    @error('cost')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <textarea class="form-control @error('plan_details') is-invalid @enderror" id="plan_details" name="plan_details" rows="3" required>{{ old('plan_details') }}</textarea>

                                    <label class="form-label" for="plan_details">Package Details</label>

                                    @error('plan_details')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <select class="form-control @error('hosting_provider_id') is-invalid @enderror" id="hosting_provider_id" name="hosting_provider_id" required>

                                        <option value="">Select Hosting Provider</option>

                                        @foreach ($providers as $provider)

                                            <option value="{{ $provider->provider_id }}" @if(old('hosting_provider_id') == $provider->provider_id) selected @endif>{{ $provider->provider_name }}</option>

                                        @endforeach

                                    </select>

                                    <label class="form-label" for="hosting_provider_id">Provider</label>

                                    @error('hosting_provider_id')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>

                        <div class="form-actions">

                            <div class="text-end">

                                <button type="submit" class="btn btn-primary" name="submit">ADD</button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection



@section('style')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.standalone.min.css" />

@endsection



@section('script')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>

<script type="text/javascript">

    $(document).ready(function() {

        $('.select2').select2({

            theme: 'bootstrap-5',

        });

        $('.datepicker').datepicker({

            format: 'yyyy-mm-dd',

            todayHighlight: true,

            autoclose: true,

            todayBtn: true,

        });

    });

    

    function limitCostInput(input) {

        let value = input.value;

        

        // Remove non-numeric characters (except for decimal point)

        value = value.replace(/[^0-9.]/g, '');

        

        // Ensure only one decimal point

        let parts = value.split('.');

        if (parts.length > 2) {

            value = parts[0] + '.' + parts[1];

        }



        // Limit to 10 characters in total

        if (value.length > 10) {

            value = value.slice(0, 10);

        }



        input.value = value;

    }

</script>

@endsection

