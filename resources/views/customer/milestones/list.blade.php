@extends('customer.app')

@section('title', 'Milestones')

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('clients.projects-details', [$project->id]) }}">{{ ucwords($project->project_name) }}</a>
        </li>
        <li class="breadcrumb-item">
            Milestone List
        </li>
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

    <!-- model for display description -->
    <div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionModalLabel">Milestone Description</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="fullDescription"></p>
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
    <script>
        $(document).ready(function () {
            $('body').on('click', '.read-more', function () {
                var fullDescription = $(this).data('description');
                $('#fullDescription').text(fullDescription);  
                $('#descriptionModal').modal('show');
            });
        });
    </script>
@endpush