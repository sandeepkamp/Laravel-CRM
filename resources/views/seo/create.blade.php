@extends('app')



@section('title', 'Add SEO Service')



@section('style')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.standalone.min.css" />

@endsection


@section('breadcrumb')

<ol class="breadcrumb">

    <li class="breadcrumb-item">

        <a href="">SEO Dashboard</a>

    </li>

    <li class="breadcrumb-item">Add SEO Service</li>

</ol>

@endsection



@section('content')

<div class="row">

    <div class="col-12">

        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title">SEO Service Form</h5>
                    <div class=" text-end pt-0 my-n5">
                        <a href="{{ route('seo.list') }}" class="btn btn-outline-primary btn-sm waves-effect">
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

                <form method="POST" action="{{ route('seo.add') }}" class="prevent-multiple-submits">

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

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="url" class="form-control" id="website_url" name="website_url" value="{{ old('website_url') }}" required>

                                    <label for="website_url">Website URL <span class="text-danger">*</span></label>

                                </div>

                                @error('website_url') <div class="text-danger">{{ $message }}</div> @enderror

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control" id="industry" name="industry" value="{{ old('industry') }}" required>

                                    <label for="industry">Industry <span class="text-danger">*</span></label>

                                </div>

                                @error('industry') <div class="text-danger">{{ $message }}</div> @enderror

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <select class="form-control" id="seo_package" name="seo_package" required>
                                        @foreach(Enum::SEO_PACKAGES as $key=> $value)
                                            <option value="{{$key}}">{{$value['label']}}</option>
                                        @endforeach

                                        <!--<option value="basic" {{ old('seo_package') == 'basic' ? 'selected' : '' }}>Basic</option>-->

                                        <!--<option value="advanced" {{ old('seo_package') == 'advanced' ? 'selected' : '' }}>Advanced</option>-->
                                        
                                        <!--<option value="premium" {{ old('seo_package') == 'premium' ? 'selected' : '' }}>Premium</option>-->

                                    </select>

                                    <label for="seo_package">Seo Package <span class="text-danger">*</span></label>

                                </div>

                                @error('seo_package') <div class="text-danger">{{ $message }}</div> @enderror

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}" onclick="this.showPicker();" required>

                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>

                                </div>

                                @error('start_date') <div class="text-danger">{{ $message }}</div> @enderror

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="date" class="form-control" id="renewal_date" name="renewal_date" value="{{ old('renewal_date') }}" onclick="this.showPicker();" required>

                                    <label for="renewal_date">Renewal Date <span class="text-danger">*</span></label>

                                </div>

                                @error('renewal_date') <div class="text-danger">{{ $message }}</div> @enderror

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <select class="form-control" id="status" name="status" required>
                                        
                                        @foreach(Enum::SEO_STATUSES as $key=> $value)
                                            <option value="{{$key}}">{{$value['label']}}</option>
                                        @endforeach

                                        <!--<option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>-->

                                        <!--<option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>-->

                                        <!--<option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>-->

                                    </select>

                                    <label for="status">Status <span class="text-danger">*</span></label>

                                </div>

                                @error('status') <div class="text-danger">{{ $message }}</div> @enderror

                            </div>

                        </div>

                    </div>

                    <div class="form-actions text-end">

                        <button type="submit" class="btn btn-primary">Save SEO Service</button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

    $('.select2').select2({

    theme: 'bootstrap-5',

    });

    document.addEventListener("DOMContentLoaded", function () {

        const websiteInput = document.getElementById("website_url");



        websiteInput.addEventListener("blur", function () {

            let val = websiteInput.value.trim();

            if (val && !/^https?:\/\//i.test(val)) {

                websiteInput.value = 'https://' + val;

            }

        });

    });

</script>

@endsection

