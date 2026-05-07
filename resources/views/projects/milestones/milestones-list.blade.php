@extends('app')

@section('title', 'Milestone - '.ucwords($project->project_name))

@push('style')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/datatables.min.css') }}">
    <style>
        .swal-confirm-button {
            background-color: #d33 !important; /* Red color */
            color: white !important;
        }
        .swal-cancel-button {
            background-color: #6c757d !important; /* Gray color */
            color: white !important;
        }
    </style>
@endpush

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('project.details', $project->id) }}">{{ ucwords($project->project_name) }}</a>
        </li>
        <li class="breadcrumb-item">{{ __('Milestone List') }}</li>
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
                    <a href="{{ route('project.milestones.add', [$project->id]) }}" class="btn btn-outline-primary btn-sm waves-effect float-end">
                        <span class="tf-icons mdi mdi-plus me-1"></span>
                        Milestones
                    </a>
                </div>
                <div class="card-body">
                    {{ $dataTable->table(['class' => 'table project-milestones-table table-striped table-border-bottom-0'], true) }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        $(document).ready(function () {
            setTimeout(function () {
        $('.dataTables_filter input').attr('placeholder', 'Search Milestone');
    }, 500);
        });
        function confirmDelete(milestoneId) {
            Swal.fire({
                title: 'Are you sure?',
                html: "Deleting this milestone will also <span style='color:#ff7979;'>remove all associated tasks and bugs</span> linked to it. This action cannot be undone.", 
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it',
                customClass: {
                    confirmButton: 'swal-confirm-button',
                    cancelButton: 'swal-cancel-button'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + milestoneId).submit();
                }
            });
        }
    </script>
@endpush