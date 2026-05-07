@extends('app')

@section('title', 'Create eSignature')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('sales_dashboard') }}">Sales Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('deal.list') }}">List</a>
    </li>
    <li class="breadcrumb-item">Create eSignature</li>
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
                    <strong><i class="tf-icons mdi mdi-check-circle-outline"></i> Success -</strong>
                    {{ session()->get('success') }}
                </div>
                @endif
                @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show">
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                    <strong><i class="tf-icons mdi mdi-alert-outline"></i> Error -</strong>
                    {{ session()->get('error') }}
                </div>
                @endif
                <form method="POST" enctype="multipart/form-data" id="upload-file" action="{{ route('contract.send', [$deal_id]) }}">
                    @csrf
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" name="name" id="inputName" class="form-control @error('name') is-invalid @enderror" placeholder="Name.." value="{{ old('name') }}">
                                    <label class="form-label" for="inputName">Sender Name</label>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="email" id="inputEmail" class="form-control @error('email') is-invalid @enderror" placeholder="Email.." value="{{ old('email') }}">
                                    <label class="form-label" for="inputEmail">Sender Email</label> 
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input class="form-control" type="file" id="formFile" name="formFile">
                                    <div class="form-text">Make sure to place **signature** anywhere in your document where you need your recipient to sign</div>
                                    <label for="formFile" class="form-label">Upload contract doc, docx or pdf</label>
                                    @error('formFile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="submit" class="btn btn-outline-primary wave-effect float-end">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection