@extends('app')

@section('title', $dashboardTitle ?? 'Coming Soon')

@push('style')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.standalone.min.css" />
    <style>
        .swal2-container {
            z-index: 11000 !important;
        }
    </style>
@endpush

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">Dashboard</a>
        </li>
    </ol>
@endsection

@section('content')

    <!-- statistics section -->
    @if(in_array(auth()->user()->role, [1, 7, 8, 4]))
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">{{ $statisticsTitle ?? 'Statistics'}}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <!-- Total Projects -->
                            @if(in_array(auth()->user()->role, [1, 7]))
                                <div class="col-sm-12 col-md-3">
                                    <a href="{{ route('project.list') }}">
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
                                    </a>
                                </div>
                            @endif

                            <!-- Total Users -->
                            @if(in_array(auth()->user()->role, [1, 7]))
                                <div class="col-sm-12 col-md-3">
                                    <a href="{{ route('user.list') }}">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-warning rounded shadow">
                                                    <i class="tf-icons mdi mdi-account-multiple-check-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <div class="small mb-1">Total Users</div>
                                                <h5 class="mb-0">{{ $totalUsersCount }}</h5>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            <!-- Active Users -->
                            @if(in_array(auth()->user()->role, [1, 7]))
                                <div class="col-sm-12 col-md-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-success rounded shadow">
                                                <i class="tf-icons mdi mdi-account-multiple-check-outline mdi-24px"></i>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <div class="small mb-1">Active Users</div>
                                            <h5 class="mb-0">{{ $activeUsersCount }}</h5>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Total Clients -->
                            @if(in_array(auth()->user()->role, [1]))
                                <div class="col-sm-12 col-md-3">
                                    <a href="{{ route('client.list') }}">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-info rounded shadow">
                                                    <i class="tf-icons mdi mdi-account-multiple-check-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <div class="small mb-1">Total Clients</div>
                                                <h5 class="mb-0">{{ $totalClientsCount }}</h5>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            @if(in_array(auth()->user()->role, [8, 4]))
                                <!-- Completed tasks  -->
                                <div class="col-sm-12 col-md-3">
                                    <div class="d-flex align-items-center">
                                        <a href="">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-success rounded shadow">
                                                <i class="tf-icons mdi mdi-check-circle mdi-24px"></i>
                                            </div>
                                        </div>
                                        </a>
                                        <div class="ms-3">
                                            <div class="small mb-1">Completed</div>
                                            <h5 class="mb-0">{{ $completedTaskCount }}</h5>
                                        </div>
                                    </div>
                                </div>

                                <!-- In Progress tasks  -->
                                <div class="col-sm-12 col-md-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-info rounded shadow">
                                                <i class="tf-icons mdi mdi-progress-clock mdi-24px"></i>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <div class="small mb-1">In Progress</div>
                                            <h5 class="mb-0">{{ $inProgressTaskCount }}</h5>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pending tasks -->
                                <div class="col-sm-12 col-md-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-warning rounded shadow">
                                                <i class="tf-icons mdi mdi-alarm-off mdi-24px"></i>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <div class="small mb-1">Pending</div>
                                            <h5 class="mb-0">{{ $pendingTaskCount }}</h5>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Leave Status -->
                            @if(in_array(auth()->user()->role, [7, 8, 4]))
                                <div class="col-sm-12 col-md-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-warning rounded shadow">
                                                <i class="tf-icons mdi mdi-lan-pending mdi-24px"></i>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <div class="small">Leave Status</div>
                                            <div class="dropdown">
                                                <button class="btn btn-white w-100 dropdown-toggle p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none;font-size: 10px;">
                                                    View Status
                                                </button>
                                                <ul class="dropdown-menu w-100">
                                                    @foreach($leaveStatusCounts as $status => $count)
                                                        <li class="dropdown-item d-flex justify-content-between">
                                                            <span>{{ $status }}</span>
                                                            <span class="badge bg-secondary">{{ $count }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    @if(in_array(auth()->user()->role, [1, 7, 8, 4]))
        <div class="row mb-5">
            <div class="col-sm-12 col-md-4">

                <!-- completed projects in year -->
                @if(in_array(auth()->user()->role, [1]))
                    <div class="card mb-4">
                        <div class="card-body">
                            <select id="yearSelectProjects" class="form-select w-25 mb-4">
                                @foreach(range(now()->year, 2021) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            <div id="projectsLineChart"></div>
                        </div>
                    </div>
                @endif

                <!-- completed tasks in year -->
                @if(in_array(auth()->user()->role, [1, 7, 8, 4]))
                    <div class="card mb-4">
                        <div class="card-body">
                            <select id="yearSelect" class="form-select w-25 mb-4">
                                @foreach(range(now()->year, 2021) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            <div id="tasksLineChart"></div>
                        </div>
                    </div>
                @endif

                <!-- Project Completion Rate Widget -->
                @if(in_array(auth()->user()->role, [1, 7]))
                    <div class="card mb-4">
                        <div class="card-body">
                            <div id="projectChart"></div>
                        </div>
                    </div>
                @endif

                <!-- Resource Utilization Widget -->
                @if(in_array(auth()->user()->role, [1, 7]))
                    <div class="card mb-4">
                        <div class="card-body">
                            <div id="resourceChart"></div>
                        </div>
                    </div>
                @endif
        
                <!-- team performance matrics -->
                @if(in_array(auth()->user()->role, [8]))
                    <div class="card mb-4">
                        <div class="card-body">
                            <div id="teamPerformanceMetrics"></div>
                        </div>
                    </div>
                @endif

            </div>
            <div class="col-sm-12 col-md-8">

                <!-- Idle Time Analysis Table -->
                @if(in_array(auth()->user()->role, [1, 7]))
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title m-0 me-2">Weekly Idle Time Report</h5>
                                <div class=" text-end pt-0 my-n5">
                                    <input type="search" id="searchEmployees" class="form-control form-control-sm" placeholder="Search Employee">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive sm-device">
                                <table class="display table table-striped table-bordered idle-time-report">
                                    <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Weekly Hours</th>
                                        <th>Estimated Task Time</th>
                                        <th>Idle Time</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <!-- AJAX Data Will Be Loaded Here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- employee-productivity-list -->
                @if(in_array(auth()->user()->role, [1, 7]))
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title m-0 me-2">Employee Productivity</h5>
                                <div class=" text-end pt-0 my-n5">
                                    <input type="search" id="searchEmployee" class="form-control form-control-sm" placeholder="Search Employee">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive sm-device">
                            <table class="table table-striped table-bordered employee-productivity-list">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Tasks Assigned</th>
                                        <th>Tasks Completed</th>
                                        <th>On Time</th>
                                        {{-- <th>Quality Score</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via DataTables server-side processing -->
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                @endif

                <!-- billable vs. non-billable tasks -->
                @if(in_array(auth()->user()->role, [1]))
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title m-0 me-2">Billable vs Non-Billable Tasks</h5>
                                <select name="project_id" class="form-select w-25" style="padding: 4px 10px;">
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ ucwords($project->project_name) }}</option>
                                    @endforeach
                                </select>
                                <div class=" text-end pt-0 my-n5">
                                    <input type="search" id="searchBillableTasks" class="form-control form-control-sm" placeholder="Search Task">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive sm-device">
                            <table class="table table-striped table-bordered billable-non-billable-tasks">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Task</th>
                                        <th>Estimate Time</th>
                                        <th>Billable Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via DataTables server-side processing -->
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                @endif

                <!-- active and completed project section -->
                @if(in_array(auth()->user()->role, [1, 7, 8, 4]))
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title m-0 me-2">Active & Completed Projects</h5>
                                <div class=" text-end pt-0 my-n5">
                                    <input type="search" id="searchProject" class="form-control form-control-sm" placeholder="Search Project">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive sm-device">
                            <table class="table table-striped table-bordered project-list">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Client</th>
                                        <th>Assigns</th>
                                        <th>Project Created</th>
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
                @endif

                <!-- tasks assigned to team leader & members list -->
                @if(in_array(auth()->user()->role, [1, 7, 8, 4]))
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title m-0 me-2">Assigned Tasks</h5>
                                <div class=" text-end pt-0 my-n5">
                                    <input type="search" id="searchTasks" class="form-control form-control-sm" placeholder="Search Task">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered assigned-task-list">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Project</th>
                                            <th>Assigns</th>
                                            <th>Task Hours</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Deadline</th>
                                            <th>Overdue</th>
                                            @if(!in_array(auth()->user()->role, [1, 7]))
                                            <th>Add Time</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via DataTables server-side processing -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- upcoming deadline tasks list -->
                @if(in_array(auth()->user()->role, []))
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title m-0 me-2">Upcoming Deadline Tasks</h5>
                                <div class=" text-end pt-0 my-n5">
                                    <input type="search" id="searchDeadlineTasks" class="form-control form-control-sm" placeholder="Search Task">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive sm-device">
                                <table class="table table-striped table-bordered upcoming-deadline-tasks">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Task Hours</th>
                                            <th>Priority</th>
                                            <th>Deadline</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via DataTables server-side processing -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Overdue Tasks -->
                @if(in_array(auth()->user()->role, []))
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title m-0 me-2">Overdue Tasks</h5>
                                <div class=" text-end pt-0 my-n5">
                                    <input type="search" id="searchTasks" class="form-control form-control-sm" placeholder="Search Task">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive sm-device">
                            <table class="table table-striped table-bordered overdue-tasks">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Created Date</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via DataTables server-side processing -->
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                @endif

                <!-- resource allocation -->
                @if(in_array(auth()->user()->role, [1]))
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title m-0 me-2">{{ (auth()->user()->role == 1) ? 'Resource Allocation' : 'Team Member Assigned Task'}}</h5>
                                <div class=" text-end pt-0 my-n5">
                                    <input type="search" id="searchResourceAllocation" class="form-control form-control-sm" placeholder="Search Resource">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive sm-device">
                                <table class="table table-striped table-bordered resource-allocation">
                                    <thead>
                                        <tr>
                                            <th>{{ (auth()->user()->role == 1) ? 'Employee' : 'Team Member'}}</th>
                                            <th>Role</th>
                                            <th>Allocation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via DataTables server-side processing -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Unassigned Resource -->
                @if(in_array(auth()->user()->role, [1, 7]))
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="card-title m-0 me-2">Unassigned Resources</h5>
                                <div class=" text-end pt-0 my-n5">
                                    <input type="search" id="searchResource" class="form-control form-control-sm" placeholder="Search Resource">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive sm-device">
                                <table class="table table-striped table-bordered unassigned-resource">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Role</th>
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
                @endif
            </div>
        </div>
    @endif

    <!-- Modal for update task start time & end time -->
    @if(in_array(auth()->user()->role, [8, 4]))
        <div class="modal" id="taskUpdateTimeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Update Task Start & End Time</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form method="POST" action="" class="mt-3" id="updateTaskTimeForm">
                        @csrf
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-floating form-floating-outline mb-3">
                                        <input type="datetime-local" class="form-control" name="start_time" id="start_time" value="" placeholder="{{ __('Start Time') }}" autocomplete="off" onclick="this.showPicker();">
                                        <label class="form-label" for="start_time">{{ __('Start Time') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating form-floating-outline mb-3">
                                        <input type="datetime-local" class="form-control" name="end_time" id="end_time" value="" placeholder="{{ __('End Time') }}" autocomplete="off" onclick="this.showPicker();">
                                        <label class="form-label" for="end_time">{{ __('End Time') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" id="updateTaskTime">Update</button>
                </div>

                </div>
            </div>
        </div>
    @endif

@endsection

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
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"> </script>

    <script type="text/javascript">
        $(function () {
            
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                autoclose: true,
                todayBtn: true,
                startDate: new Date()
            });

            // resource-allocation
            @if(in_array(auth()->user()->role, [1]))
                var resourceAllocation = $('.resource-allocation').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: false,
                    dom: 'lrtp',
                    info: false,
                    pageLength: 10,
                    ajax: "{{ route('dashboard.resource.allocation') }}",
                    columns: [
                        { data: 'name', name: 'name' },
                        { data: 'role', name: 'role', searchable: false },
                        { data: 'allocation', name: 'allocation', searchable: false, orderable: false },
                    ]
                });

                $('#searchResourceAllocation').on('input', function() {
                    resourceAllocation.search(this.value).draw();
                });
            @endif

            // employee-productivity-list
            @if(in_array(auth()->user()->role, [1, 7]))
                var employeeList = $('.employee-productivity-list').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: false,
                    dom: 'lrtp',
                    info: false,
                    ajax: "{{ route('dashboard.employee-productivity-list') }}",
                    columns: [
                        {data: 'employee_name', name: 'employee_name'},
                        {data: 'task_assigned_count', name: 'task_assigned_count', searchable: false},
                        {data: 'task_completed_count', name: 'task_completed_count', searchable: false},
                        {data: 'task_completed_on_time', name: 'task_completed_on_time', searchable: false}
                    ]
                });  

                $('#searchEmployee').on('input', function() {
                    employeeList.search(this.value).draw();
                });
            @endif

            // billable-non-billable-tasks
            @if(in_array(auth()->user()->role, [1]))
                var searchBillableTasks;
                function loadBillableTasks(projectId = null) {
                    if ($.fn.DataTable.isDataTable('.billable-non-billable-tasks')) {
                        $('.billable-non-billable-tasks').DataTable().clear().destroy();
                    }

                    searchBillableTasks = $('.billable-non-billable-tasks').DataTable({
                        processing: true,
                        serverSide: true,
                        paging: false,
                        dom: 'lrtp',
                        info: false,
                        ajax: {
                            url: "{{ route('dashboard.billable-non-billable-tasks') }}",
                            data: function (d) {
                                d.project_id = $('select[name="project_id"]').val();
                            }
                        },
                        columns: [
                            {data: 'employee_name', name: 'employee_name', searchable: false },
                            {data: 'task_name', name: 'task_name'},
                            {data: 'estimated_time', name: 'estimated_time', searchable: false},
                            {data: 'billable_type', name: 'billable_type', searchable: false}
                        ],
                        drawCallback: function () {
                            $('[data-bs-toggle="tooltip"]').tooltip({
                                placement: 'right'
                            });
                        }
                    });
                }

                loadBillableTasks();
                $('select[name="project_id"]').on('change', function () {
                    loadBillableTasks($(this).val());
                });

                $('#searchBillableTasks').on('input', function () {
                    if (searchBillableTasks) {
                        searchBillableTasks.search(this.value).draw();
                    }
                });
            @endif

            // active and completed project
            @if(in_array(auth()->user()->role, [1, 7, 8, 4]))
                var projectList = $('.project-list').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: false,
                    dom: 'lrtp',
                    info: false,
                    ajax: "{{ route('dashboard.project-list') }}",
                    columns: [
                        {data: 'project_name', name: 'project_name'},
                        {data: 'client_name', name: 'client_name', searchable: false},
                        {data: 'assigns', name: 'assigns', searchable: false, orderable: false},
                        {data: 'created_at', name: 'created_at', searchable: false},
                        {data: 'status', name: 'status', searchable: false}
                    ],
                    drawCallback: function () {
                        $('[data-bs-toggle="tooltip"]').tooltip({
                            placement: 'right'
                        });
                    }
                });  

                $('#searchProject').on('input', function() {
                    projectList.search(this.value).draw();
                });
            @endif

            // overdue-tasks
            @if(in_array(auth()->user()->role, []))
                var overdueTasks = $('.overdue-tasks').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: false,
                    dom: 'lrtp',
                    info: false,
                    ajax: "{{ route('dashboard.overdue-tasks') }}",
                    columns: [
                        {data: 'task_name', name: 'task_name'},
                        {data: 'created_at', name: 'created_at', searchable: false},
                        {data: 'due_date', name: 'due_date', searchable: false}
                    ]
                });  

                $('#searchTasks').on('input', function() {
                    overdueTasks.search(this.value).draw();
                });
            @endif

            // Unassigned Resource
            @if(auth()->user()->role == 1 || auth()->user()->role == 7)
                var unassignedResource = $('.unassigned-resource').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: false,
                    dom: 'lrtp',
                    info: false,
                    pageLength: 10,
                    ajax: "{{ route('dashboard.unassigned-resource') }}",
                    columns: [
                        {data: 'full_name', name: 'full_name'},
                        {data: 'department', name: 'department', searchable: false},
                        {data: 'status', name: 'status', searchable: false}
                    ]
                });  

                $('#searchResource').on('input', function() {
                    unassignedResource.search(this.value).draw();
                });
            @endif

            // assigned tasks to team leader & members list
            @if(in_array(auth()->user()->role, [1, 7, 8, 4]))
                var assignedTasks = $('.assigned-task-list').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: false,
                    dom: 'lrtp',
                    info: false,
                    ajax: {
                        url: "{{ route('dashboard.assigned-task-list') }}"
                    },
                    columns: [
                        {data: 'task_name', name: 'task_name'},
                        {data: 'project_name', name: 'project_name'},
                        {data: 'assigns', name: 'assigns', searchable: false, orderable: false}, 
                        {data: 'estimated_time', name: 'estimated_time', searchable: false},
                        {data: 'status', name: 'status', searchable: false},
                        {data: 'priority', name: 'priority', searchable: false},
                        {data: 'deadline', name: 'deadline', searchable: false},
                        {data: 'overdue', name: 'overdue', searchable: false},
                        @if(!in_array(auth()->user()->role, [1, 7]))
                        {data: 'add_time', name: 'add_time', searchable: false}
                        @endif
                    ],
                    drawCallback: function () {
                        $('[data-bs-toggle="tooltip"]').tooltip({
                            placement: 'right'
                        });
                    }
                });

                $('#searchTasks').on('input', function() {
                    assignedTasks.search(this.value).draw();
                });
            @endif

            // upcoming-deadline-tasks list
            @if(in_array(auth()->user()->role, []))
                var searchDeadlineTasks = $('.upcoming-deadline-tasks').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: false,
                    dom: 'lrtp',
                    info: false,
                    ajax: {
                        url: "{{ route('dashboard.upcoming-deadline-tasks') }}"
                    },
                    columns: [
                        {data: 'task_name', name: 'task_name'},
                        {data: 'estimated_time', name: 'estimated_time', searchable: false},
                        {data: 'priority', name: 'priority', searchable: false},
                        {data: 'deadline', name: 'deadline', searchable: false}
                    ]
                });

                $('#searchDeadlineTasks').on('input', function() {
                    searchDeadlineTasks.search(this.value).draw();
                });
            @endif
            
            // projects-monthly-completed
            @if(in_array(auth()->user()->role, [1]))
                function renderProjectLineChart(year) {
                    $.ajax({
                        url: "{{ url('/projects-monthly-completed') }}",
                        type: 'GET',
                        data: { year: year },
                        success: function(response) {
                            Highcharts.chart('projectsLineChart', {
                                chart: {
                                    type: 'line'
                                },
                                title: {
                                    text: `Completed Projects in ${year}`
                                },
                                xAxis: {
                                    categories: response.labels
                                },
                                yAxis: {
                                    title: {
                                        text: 'Number of Projects'
                                    },
                                    allowDecimals: false,
                                    min: 0
                                },
                                series: [{
                                    name: 'Completed Projects',
                                    data: response.data
                                }],
                                credits: {
                                    enabled: false
                                }
                            });
                        }
                    });
                }

                $('#yearSelectProjects').on('change', function() {
                    const selectedYear = $(this).val();
                    renderProjectLineChart(selectedYear);
                });

                renderProjectLineChart($('#yearSelectProjects').val());
            @endif

            // tasks-monthly-completed
            @if(in_array(auth()->user()->role, [1, 7, 8, 4]))
                function fetchChartData(year) {
                    $.ajax({
                        url: '{{ url("/tasks-monthly-completed") }}', 
                        type: 'GET',
                        data: { year: year },
                        success: function(response) {
                            Highcharts.chart('tasksLineChart', {
                                chart: {
                                    type: 'line'
                                },
                                title: {
                                    text: `Completed Tasks in ${year}`
                                },
                                xAxis: {
                                    categories: response.labels
                                },
                                yAxis: {
                                    title: {
                                        text: 'Number of Completed Tasks'
                                    },
                                    allowDecimals: false,
                                    min: 0
                                },
                                series: [{
                                    name: 'Completed Tasks',
                                    data: response.data
                                }],
                                credits: {
                                    enabled: false
                                }
                            });
                        }
                    });
                }

                $('#yearSelect').on('change', function() {
                    const selectedYear = $(this).val();
                    fetchChartData(selectedYear);
                });

                // Load current year data by default
                $(document).ready(function() {
                    fetchChartData($('#yearSelect').val());
                });
            @endif

            // Load idle time data via AJAX
            @if(in_array(auth()->user()->role, [1, 7]))
                var employeesList = $('.idle-time-report').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: false,
                    dom: 'lrtp',
                    info: false,
                    ajax: "{{ route('dashboard.idle.time.report') }}",
                    columns: [
                        { data: 'employee', name: 'employee' },
                        { data: 'weekly_hours', name: 'weekly_hours', searchable: false, orderable: false },
                        { data: 'estimated_hours', name: 'estimated_hours', searchable: false },
                        { data: 'idle_time', name: 'idle_time', searchable: false }
                    ]
                });

                $('#searchEmployees').on('input', function() {
                    employeesList.search(this.value).draw();
                });
            @endif

            // update-task-time
            @if(in_array(auth()->user()->role, [8, 4]))
                $(document).on('click', '.update-task-time', function () {
                    let taskId = $(this).data('taskid'); 
                    console.log(taskId);
                    let baseUrl = "{{ url('/') }}";
                    let taskTimeUpdateRoute = baseUrl + '/update-task-time/' + taskId;
                    $('#updateTaskTimeForm').attr('action', taskTimeUpdateRoute);

                    $.ajax({
                        url: baseUrl + '/get-task-time/' + taskId,
                        method: 'GET',
                        success: function(response) {
                            $('#start_time').val(response.start_time);
                            $('#end_time').val(response.end_time);
                        },
                        error: function(xhr) {
                            // Task not found
                            Swal.fire({
                                icon: 'error',
                                title: 'Not Found',
                                text: xhr.responseJSON.message,
                                confirmButtonColor: '#d33',
                            });
                            $('#start_time').val('');
                            $('#end_time').val('');
                        }
                    });
                });

                $(document).on('click', '#updateTaskTime', function (e) {
                    e.preventDefault();

                    let form = $('#updateTaskTimeForm');
                    let actionUrl = form.attr('action');

                    $.ajax({
                        url: actionUrl,
                        method: 'POST',
                        data: form.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            $('#taskUpdateTimeModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                allowOutsideClick: false,
                                didClose: (toast) => {
                                    location.reload();
                                }
                            });

                            $('#taskUpdateTimeModal').modal('hide');
                            form[0].reset();
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                // Validation error
                                let errors = xhr.responseJSON.errors;
                                let errorList = '';
                                $.each(errors, function (key, value) {
                                    errorList += value[0] + '\n';
                                });

                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Validation Error',
                                    text: errorList,
                                    confirmButtonColor: '#d33',
                                });
                            } else if (xhr.status === 404) {
                                // Task not found
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Not Found',
                                    text: xhr.responseJSON.message,
                                    confirmButtonColor: '#d33',
                                });
                            } else if (xhr.status === 500) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Server Error',
                                    text: xhr.responseJSON.message || 'Failed to update task.',
                                    confirmButtonColor: '#d33',
                                });
                            } else {
                                // General error
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An unexpected error occurred.',
                                    confirmButtonColor: '#d33',
                                });
                            }
                        }
                    });
                });
            @endif
        }); // function closed
        
        document.addEventListener("DOMContentLoaded", function () {
            @if(in_array(auth()->user()->role, [1, 7]))
                const projectData = @json($projectLabelValues);
                const resourceData = @json($resourcesLabelValues);

                if (!projectData || !resourceData) {
                    console.error("Data not loaded!");
                    return;
                }

                // Function to render a chart
                function renderChart(containerId, title, categories, data, colors) {
                    Highcharts.chart(containerId, {
                        chart: { 
                            type: 'column',
                            options3d: {
                                enabled: true,
                                alpha: 10,
                                beta: 15,
                                depth: 50,
                                viewDistance: 25
                            }
                        },
                        title: { text: title },
                        xAxis: { categories: categories },
                        yAxis: { allowDecimals: false },
                        series: [{ name: title, data: data, colorByPoint: true }],
                        colors: colors,
                        plotOptions: { column: { depth: 25 } },
                        credits: { enabled: false }
                    });
                }

                // Render Project Completion Rate Chart
                renderChart("projectChart", "Project Completion Rate", 
                    ["Completed", "In Progress", "Pending"], projectData, 
                    ["green", "blue", "orange"]);

                // Render Resource Utilization Chart
                renderChart("resourceChart", "Resource Utilization", 
                    ["Active Users", "Inactive Users", "Utilization Rate"], resourceData, 
                    ["green", "blue", "cyan"]);
            @endif

            // team performance matrics 
            @if(in_array(auth()->user()->role, [8]))
                const teamPerformance = @json($teamPerformance);
                const teamNames = teamPerformance.map(member => member.name);
                const completionRates = teamPerformance.map(member => member.completionRate);
                const timeLogged = teamPerformance.map(member => member.timeLogged);

                Highcharts.chart('teamPerformanceMetrics', {
                    chart: { 
                        type: 'column',
                        scrollablePlotArea: {
                            minWidth: 1200,
                            scrollPositionX: 1
                        }
                    },
                    title: { text: 'Team Performance Metrics' },
                    xAxis: { categories: teamNames, title: { text: 'Team Members' } },
                    yAxis: [
                        { title: { text: 'Completion Rate (%)' } },
                        { title: { text: 'Time Logged (hrs)' }, opposite: true }
                    ],
                    tooltip: { shared: true },
                    series: [
                        { name: 'Completion Rate (%)', data: completionRates, color: '#28a745' },
                        { name: 'Time Logged (hrs)', data: timeLogged, color: '#007bff', yAxis: 1 }
                    ],
                    credits: { enabled: false }
                });
            @endif
        });

    </script>

@endpush