@extends('customer.app')

@section('title', 'Dashboard')

@push('style')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/buttons.dataTables.min.css') }}">
    <style>
        .card {
            border-radius: 10px;
        }
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
        }
        .rating input {
            display: none;
        }
        .rating label {
            font-size: 24px;
            color: #ccc;
            cursor: pointer;
        }
        .rating input:checked ~ label,
        .rating label:hover,
        .rating label:hover ~ label {
            color: gold;
        }
    </style>
@endpush

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('clients.dashboard') }}">Management Dashboard</a>
        </li>
    </ol>
@endsection

@section('content')
    <!-- statistics section -->
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Statistics</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <!-- Total Projects -->
                        <div class="col-sm-12 col-md-6">
                            <a href="{{ route('clients.projects') }}">
                                <div class="d-flex align-items-center">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-primary rounded shadow">
                                            <i class="tf-icons mdi mdi-collapse-all mdi-24px"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <div class="small mb-1">Total Projects</div>
                                        <h5 class="mb-0">{{ $projectsCount }}</h5>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Completed Projects -->
                        <div class="col-sm-12 col-md-6">
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

                        <!-- Completed Tasks -->
                        <div class="col-sm-12 col-md-3 d-none">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-info rounded shadow">
                                        <i class="tf-icons mdi mdi-checkbox-marked-circle-minus-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Completed Tasks</div>
                                    <h5 class="mb-0">{{ $completedTaskCount }}</h5>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Milestones -->
                        <div class="col-sm-12 col-md-3 d-none">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-warning rounded shadow">
                                        <i class="tf-icons mdi mdi-flag-triangle mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Total Milestones</div>
                                    <h5 class="mb-0">{{ $milestonesCount }}</h5>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <div class="col-sm-12 col-md-6">
            <!-- Deliverables Due This Week -->
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5>Deliverables Due This Week</h5>
                    <ul id="deliverablesList" class="list-group">
                        @if($deliverables->isEmpty())
                            <li class='list-group-item'>No deliverables due this week.</li>
                        @else
                            @foreach ($deliverables as $index => $deliverable)
                            <li class='list-group-item'>{{ ucfirst($deliverable->project_name) }} - Due: {{ $deliverable->end_date->format('jS M, Y') }}</li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-sm-12 col-md-4">

            <!-- Budget Graphical Statistics -->
            <div class="card mb-4">
                <div class="card-body">
                    <div id="budgetChart"></div>
                </div>
            </div>

        </div>
        <div class="col-sm-12 col-md-8">

            <!-- Project Evaluation -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Projects Evaluation</h5>
                        <div class=" text-end pt-0 my-n5">
                            <input type="search" id="searchProject" class="form-control form-control-sm" placeholder="Search Project">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive sm-device">
                        <table class="table table-striped table-bordered project-evaluation">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Rating</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via DataTables server-side processing -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Current Ongoing Projects -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Current Ongoing Projects</h5>
                        <div class=" text-end pt-0 my-n5">
                            <input type="search" id="searchOngoingProject" class="form-control form-control-sm" placeholder="Search Project">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive sm-device">
                        <table class="table table-striped table-bordered ongoing-projects">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Project Hours</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via DataTables server-side processing -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <x-modals.description-modal />
@endsection

@include('components.scripts.description-modal-script')

@push('script')
    <script src="{{ url('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/jszip.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/pdfmake.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/vfs_fonts.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/buttons.html5.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/buttons.print.min.js') }}"></script>
    
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
        $(document).ready(function () {
             // project-evaluation
             var projectEvaluation = $('.project-evaluation').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                dom: 'lrtp',
                info: false,
                ajax: {
                    url: "{{ route('clients.project-evaluation') }}"
                },
                columns: [
                    {data: 'project_name', name: 'project_name'},
                    {data: 'rating', name: 'rating'},
                    {data: 'comment', name: 'comment'}
                ]
            });  

            $('#searchProject').on('input', function() {
                projectEvaluation.search(this.value).draw();
            });
             
            // ongoing-projects
            var ongoingProjects = $('.ongoing-projects').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                dom: 'lrtp',
                info: false,
                ajax: {
                    url: "{{ route('clients.ongoing-projects') }}"
                },
                columns: [
                    { data: 'project_name', name: 'project_name' },
                    { data: "project_hours", name: 'project_hours' },
                    { data: "start_date", name: 'start_date' },
                    { data: "end_date", name: 'end_date' },
                    { data: 'status', name: 'status' }
                ]
            });  

            $('#searchOngoingProject').on('input', function() {
                ongoingProjects.search(this.value).draw();
            });

            // Budget Chart
            const budgets = @json($budgets);
            const projectNames = budgets.map(project => project.project_name);
            const budgetData = budgets.map(project => project.budget);
            const actualExpenseData = budgets.map(project => project.actual_expense);

            Highcharts.chart('budgetChart', {
                chart: { 
                    type: 'column',
                    // scrollablePlotArea: {
                    //     minWidth: 530,
                    //     scrollPositionX: 1
                    // }
                },
                title: { text: 'Budget Evaluation' },
                xAxis: {
                    categories: projectNames,
                    title: { text: 'Projects' }
                },
                yAxis: {
                    min: 0,
                    title: { text: 'Amount ()' }
                },
                series: [
                    { name: 'Planned Budget', data: budgetData, color: '#007bff' },
                    { name: 'Actual Expense', data: actualExpenseData, color: '#dc3545' }
                ],
                tooltip: {
                    shared: true,
                    valuePrefix: ''
                },
                credits: { enabled: false }
            });
        }); // document closed
    </script>
@endpush