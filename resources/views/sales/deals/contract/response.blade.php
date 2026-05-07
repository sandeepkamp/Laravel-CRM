@extends('app')

@section('title', 'Creating DocuSign contract')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('sales_dashboard') }}">Sales Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('deal.list') }}">List</a>
    </li>
    <li class="breadcrumb-item">Creating DocuSign contract</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show">
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                    <strong><i class="tf-icons mdi mdi-check-circle-outline"></i> Success -</strong>
                    {{ $result }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection