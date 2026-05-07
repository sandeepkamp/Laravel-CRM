@extends('app')

@section('title', 'Edit Hosting')

@section('breadcrumb')

<ol class="breadcrumb">

    <li class="breadcrumb-item">

        <a href="{{ route('hosting_dashboard') }}">Hosting Dashboard</a>

    </li>

    <li class="breadcrumb-item">

        <a href="{{ route('hosting.list') }}">List</a>

    </li>

    <li class="breadcrumb-item active">Edit Hosting</li>

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

                <form method="POST" action="{{ route('hosting.update', $hostingDetail->id) }}" id="hostingform">

                    @csrf

                    @method('PUT')

                    <div class="form-body">

                        <div class="row">

                            <!-- Client Selection -->

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <select class="form-control select2 @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>

                                        <option value="">Select a Client</option>

                                        @foreach ($clients as $client)

                                            <option value="{{ $client->id }}" @if(old('client_id', $hostingDetail->client_id) == $client->id) selected @endif>

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



                            <!-- Hosting Provider -->

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <select class="form-control @error('hosting_provider_id') is-invalid @enderror" id="hosting_provider_id" name="hosting_provider_id" required>

                                        <option value="">Select Hosting Provider</option>

                                        @foreach ($providers as $provider)

                                            <option value="{{ $provider->provider_id }}" @if(old('hosting_provider_id', $hostingDetail->hosting_provider_id) == $provider->provider_id) selected @endif>{{ $provider->provider_name }}</option>

                                        @endforeach

                                    </select>

                                    <label class="form-label" for="hosting_provider_id">Provider</label>

                                    @error('hosting_provider_id')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>



                        <!-- Purchase Date -->

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="date" class="form-control @error('date_of_purchase') is-invalid @enderror" id="date_of_purchase" name="date_of_purchase" value="{{ old('date_of_purchase', $hostingDetail->date_of_purchase) }}" required>

                                    <label for="date_of_purchase">Purchase Date</label>

                                    @error('date_of_purchase')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>



                            <!-- Plan Details -->

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <textarea class="form-control @error('plan_details') is-invalid @enderror" id="plan_details" name="plan_details" rows="6" required>{{ old('plan_details', $hostingDetail->plan_details) }}</textarea>

                                    <label for="plan_details">Package Details</label>

                                    @error('plan_details')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>



                        <!-- Cost -->

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-group mb-3">

                                    <div class="input-group">

                                        <span class="input-group-text">£</span>

                                        <input type="number" min="0" class="form-control @error('cost') is-invalid @enderror" aria-label="Amount" name="cost" id="cost" value="{{ old('cost', $hostingDetail->cost) }}" placeholder="0.00">

                                    </div>

                                    @error('cost')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>

                        <!-- Activation and Expiry Dates (Readonly) -->

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control" id="activation_date" value="{{ $hostingDetail->activation_date ?? 'N/A' }}" readonly>

                                    <label for="activation_date">Activation Date</label>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control" id="expiry_date" value="{{ $hostingDetail->expiry_date ?? 'N/A' }}" readonly>

                                    <label for="expiry_date">Expiry Date</label>

                                </div>

                            </div>

                        </div>



                        <div class="form-actions">

                            <div class="text-end">

                                <button type="submit" class="btn btn-primary">Update</button>

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

</script>

@endsection

