@extends('customer.app')

@section('title', 'Tasks - '.ucwords($project->project_name))

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('clients.projects-details', $project->id) }}">{{ ucwords($project->project_name) }}</a>
        </li>
        <li class="breadcrumb-item">{{ __('Tasks List') }}</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
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