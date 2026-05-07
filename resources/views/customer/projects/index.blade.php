@extends('customer.app')

@section('title', 'Projects')

@push('style')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/datatables.min.css') }}">
@endpush

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('clients.projects') }}">Projects Management</a>
        </li>
    </ol>
@endsection

@section('content')
    <!-- projects overview -->
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Projects Overview</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <!-- Total Projects -->
                        <div class="col-sm-12 col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-primary rounded shadow">
                                        <i class="tf-icons mdi mdi-collapse-all mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Total Projects</div>
                                    <h5 class="mb-0">{{ $totalProjectsCount }}</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Ongoing Projects -->
                        <div class="col-sm-12 col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-warning rounded shadow">
                                        <i class="tf-icons mdi mdi-progress-clock mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Ongoing Projects</div>
                                    <h5 class="mb-0">{{ $ongoingProjectsCount }}</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Completed Projects -->
                        <div class="col-sm-12 col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-success rounded shadow">
                                        <i class="tf-icons mdi mdi-check-circle mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Completed Projects</div>
                                    <h5 class="mb-0">{{ $completedProjectsCount }}</h5>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Handed Over Projects -->
                        <div class="col-sm-12 col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-info rounded shadow">
                                        <i class="tf-icons mdi mdi-clipboard-check mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Handed Over Projects</div>
                                    <h5 class="mb-0">{{ $handedOverProjectsCount }}</h5>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Projects List -->
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Projects List</h5>
                        <div class=" text-end pt-0 my-n5">
                            <input type="search" id="searchProject" class="form-control form-control-sm" placeholder="Search Project">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive sm-device">
                        <table id="projects-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Project Hours</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ url('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            var projectsTable = $('#projects-table').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                dom: 'lrtp',
                info: false,
                ajax: "{{ route('clients.projects-list') }}",
                columns: [
                    { data: 'project_name', name: 'project_name' },
                    { data: 'project_hours', name: 'project_hours' },
                    { data: 'start_date', name: 'start_date' },
                    { data: 'end_date', name: 'end_date' },
                    { data: 'status', name: 'status' }
                ]
            });

            $('#searchProject').on('input', function() {
                projectsTable.search(this.value).draw();
            });
        });
    </script>
@endpush
