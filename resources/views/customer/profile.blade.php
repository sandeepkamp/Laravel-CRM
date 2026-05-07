@extends('customer.app')

@section('title', 'My Profile')

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('clients.dashboard') }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">{{ $client->full_name }}</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show">
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                    <strong>Success -</strong>
                    {{ session()->get('success') }}
                </div>
                @endif

                <form method="POST" action="{{ route('clients.update-profile') }}" autocomplete="off">
                    @csrf
                    @method('POST')
                    <div class="form-body">
                    	<div class="row">

                    		<div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') ? old('first_name') : $client->first_name }}" placeholder="First Name" required>
                                    <label class="form-label" for="first_name">First Name <span class="text-danger">*</span></label>
                                    @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') ? old('last_name') : $client->last_name }}" placeholder="Last Name" required>
                                    <label class="form-label" for="last_name">Last Name <span class="text-danger">*</span></label>
                                    @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                    	</div>
                    	<div class="row">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="email" name="fake_email" style="display: none;">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') ? old('email') : $client->email }}" placeholder="Email" required>
                                    <span class="form-text">This is your login id. If you change this one it'll change your login id as well</span>
                                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') ? old('phone') : $client->phone }}" placeholder="Phone" required pattern="^[\d\+\-]*$" title="Only numbers, plus (+), and minus (-) are allowed">
                                    <span class="form-text">Only numbers, plus (+), and minus (-) are allowed</span>
                                    <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="password" name="fake_password" style="display: none;">
                                <div class="form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input type="password" id="password" class="form-control @error('confirm_password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                                            <label for="password">Password</label>
                                            @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password" name="confirm_password" placeholder="Confirm Password" autocomplete="cc-password" data-type="password">
                                    <label class="form-label" for="confirm_password">Confirm Password</label>
                                    @error('confirm_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" name="submit">SAVE</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection