@extends('app')

@section('title', 'Add New')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('client.list') }}">List</a>
    </li>
    <li class="breadcrumb-item">Add New</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title">Client Form</h5>
                    <div class=" text-end pt-0 my-n5">
                        <a href="{{ route('client.list') }}" class="btn btn-outline-primary btn-sm waves-effect">
                            <span class="mdi mdi-step-backward"></span> Back
                        </a>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('client.add') }}" autocomplete="off">
                @csrf
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
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="{{ __('First Name') }}" required>
                                    <label class="form-label" for="first_name">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                    @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="{{ __('Last Name') }}" required>
                                    <label class="form-label" for="last_name">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                    @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}" required>
                                    <label class="form-label" for="email">{{ __('Email') }} <span class="text-danger">*</span></label>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="{{ __('Phone') }}">
                                    <label class="form-label" for="phone">{{ __('Phone') }}</label>
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{ old('password') }}" placeholder="Password">
                                    <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password" name="confirm_password" value="{{ old('confirm_password') }}" placeholder="Confirm Password">
                                    <label class="form-label" for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                                    @error('confirm_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select @error('status') is-invalid @enderror" name="status" id="status" required>
                                        <option value="1">{{ __('Active') }}</option>
                                        <option value="2">{{ __('Inactive') }}</option>
                                        <option value="3">{{ __('Blocked') }}</option>
                                    </select>
                                    <label class="form-label" for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary" name="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection