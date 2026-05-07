@extends('app')

@section('title', 'Clients')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">List</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
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
        <div class="card">
            <div class="card-header">
                <a href="{{ route('client.add') }}" class="btn btn-outline-primary btn-sm waves-effect float-end">
                    <span class="tf-icons mdi mdi-plus me-1"></span>
                    Client
                </a>
            </div>
            <div class="card-body">
                {{ $dataTable->table(['class' => 'table leads-table table-striped table-border-bottom-0'], true) }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/datatables.min.css') }}">
@endsection

@section('script')
<script src="{{ url('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
@endsection
@push('script')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush