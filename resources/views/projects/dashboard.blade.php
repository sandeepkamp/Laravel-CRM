@extends('app')

@section('title', 'Dasboard')

@push('style')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/buttons.dataTables.min.css') }}">
@endpush

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('project.dashboard') }}">Projects Dashboard</a>
        </li>
    </ol>
@endsection

@section('content')
    <!-- Task Progress Overview section start -->
    @if(in_array(auth()->user()->role, [1, 7]))
        <div class="row mb-4">
            <div class="col-sm-12 col-md-12">
                <div class="card p-2">

                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">Task Progress Overview</h5>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-12 col-md-3">
                                <div class="d-flex align-items-center">
                                    <a href="javascript::void()">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-primary rounded shadow">
                                            <i class="tf-icons mdi mdi-file-tree mdi-24px"></i>
                                        </div>
                                    </div>
                                    </a>
                                    <div class="ms-3">
                                        <div class="small mb-1">Total</div>
                                        <h5 class="mb-0">{{ $totalTaskCount }}</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-3">
                                <div class="d-flex align-items-center">
                                    <a href="javascript::void()">
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
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-sm-12 col-md-4">
            <!-- Budget vs. Actual Expense start -->
            @if(in_array(auth()->user()->role, [1, 7]))
                <div class="card mb-4">
                    <div class="card-body">
                        <div id="budgetChart"></div>
                    </div>
                </div>
            @endif

            <!-- Team Performance Metrics & Resource Utilization start -->
            @if(in_array(auth()->user()->role, [1, 7]))
                <div class="card mb-4">
                    <div class="card-body">
                        <div id="teamPerformanceChart"></div>
                    </div>
                </div>
            @endif
        
            <!-- team performance matrics -->
            @if(in_array(auth()->user()->role, [1, 7]))
                <div class="card mb-4">
                    <div class="card-body">
                        <div id="teamPerformanceMetrics"></div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-sm-12 col-md-8">
            @if(in_array(auth()->user()->role, [1, 7]))
            <!-- Projects Assigned to The Manager list -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Assigned Projects</h5>
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
                                    <th>Name</th>
                                    <th>Assigns</th>
                                    <th>Project Hours</th>
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

            @if(in_array(auth()->user()->role, [1, 7]))
            <!-- tasks assigned to team members list -->
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
                    <div class="table-responsive sm-device">
                        <table class="table table-striped table-bordered member-task-list">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Assigns</th>
                                    <th>Task Hours</th>
                                    <!-- <th>Task Created</th> -->
                                    <th>Status</th>
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

            @if(in_array(auth()->user()->role, [1, 7]))
            <!-- Milestone and Task Tracking start -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Milestones</h5>
                        <div class=" text-end pt-0 my-n5">
                            <input type="search" id="searchMilestones" class="form-control form-control-sm" placeholder="Search Milestones">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive sm-device">
                        <table class="table table-striped table-bordered milestones-list">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Project Name</th>
                                    <th>Status</th>
                                    <th>Deadline</th>
                                    <th>Alert</th>
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

    <!-- Upcoming Deliverables & Critical Issues start -->
    <div class="row mb-5 d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Upcoming Deliverables & Critical Issues</h5>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">Deliverable 1 - Due in 3 days</li>
                        <li class="list-group-item list-group-item-danger">Critical Issue: Server downtime</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role == 1 || auth()->user()->role == 7)
    <!-- Overdue Tasks & Performance Issues start -->
    <div class="row mb-5 d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Overdue Tasks & Performance Issues</h5>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Overdue Tasks Alert -->
                    <div class="alert alert-danger">
                        <strong>Overdue Tasks!</strong> The following tasks are overdue:
                        <ul>
                            <li><strong>Fix Login Bug</strong> (Due: 10 Mar, 2025) - Assigned to John Doe</li>
                            <li><strong>Database Optimization</strong> (Due: 8 Mar, 2025) - Assigned to Jane Smith</li>
                        </ul>
                    </div>

                    <!-- Performance Issues Alert -->
                    <div class="alert alert-warning">
                        <strong>Performance Issues!</strong> The following tasks are pending for too long:
                        <ul>
                            <li><strong>Create API Endpoints</strong> (Created: 1 Mar, 2025) - Assigned to Mike Johnson</li>
                            <li><strong>Design UI for Dashboard</strong> (Created: 5 Mar, 2025) - Assigned to Alice Brown</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(auth()->user()->role == 1 || auth()->user()->role == 7)
    <!-- Daily Task Updates Widget -->
    <div class="row mb-5 d-none">
        <div class="col-sm-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Daily Task Updates</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered daily-task-update">
                            <thead>
                                <tr>
                                    <th>Task Name</th>
                                    <th>Description</th>
                                    <th>Assigned To</th>
                                    <th>Status</th>
                                    <th>Created At</th>
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
    @endif

    <!-- Notifications Widget -->
    <div class="row mb-5 d-none">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header bg-warning text-dark">🔔 Team Notifications</div>
                <div class="card-body">
                    <table class="table table-striped table-bordered notification-list">
                        <thead>
                            <tr>
                                <th>Message</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--View of all assigned tasks with deadlines and priorities. -->
    <div class="row mb-5 d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Tasks with Deadlines & Priorities</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered task-deadline-priority">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Assigns</th>
                                    <th>Priorities</th>
                                    <th>Deadlines</th>
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
    
    <!--Today’s Tasks and Deadlines. -->
    <div class="row mb-5 d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Today’s Tasks and Deadlines</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered today-task-deadlines">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Assigns</th>
                                    <th>Deadlines</th>
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

    <!-- Time Log Submission Summary Widget -->
    <div class="row mb-5 d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">
                            <!-- <span class="mdi mdi-clock-time-four-outline text-info"></span> -->
                            Time Log Summary
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex" style="justify-content: space-around;gap: 564px;">
                        <p><strong>Today’s Logged Hours:</strong> <span id="todayHours">5</span></p>
                        <p><strong>Weekly Logged Hours:</strong> <span id="weeklyHours">22</span></p>
                    </div>
                    <h6>Recent Time Logs:</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Hours</th>
                                    <th>Task</th>
                                </tr>
                            </thead>
                            <tbody id="recentLogs">
                                <tr>
                                    <td>3</td>
                                    <td>Task A</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Task B</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Task C</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Task D</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Task E</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Time tracking and task progress update interface. -->
    <div class="row mb-5 d-none">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">
                            Time Log Submission
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <form id="timeLogForm" method="POST" action="">
                        @csrf
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-floating form-floating-outline mb-3">
                                        <input type="number" class="form-control" id="work_hours" name="work_hours" value="" required>
                                        <label class="form-label" for="work_hours">Work Hours <span class="text-danger">*</span></label>
                                        @if($errors->has('work_hours'))
                                            <div class="text-danger">{{ $errors->first('work_hours') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-12">
                                    <div class="form-floating form-floating-outline mb-3">
                                        <textarea class="form-control" id="work_description" name="work_description" rows="3"></textarea>
                                        <label class="form-label" for="work_description">Work Description <span class="text-danger">*</span></label>
                                    </div>
                                    @if($errors->has('work_description'))
                                        <div class="text-danger">{{ $errors->first('work_description') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

    <script src="{{ url('vendor/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>

    <script type="text/javascript">

        $(function () {
            // Projects Assigned to The Manager list
            @if(in_array(auth()->user()->role, [1, 7]))
                var projectList = $('.project-list').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: false,
                    dom: 'lrtp',
                    info: false,
                    ajax: {
                        url: "{{ route('project.assigned-project-list') }}",
                        data: { type: 'projects' }
                    },
                    columns: [
                        {data: 'project_name', name: 'project_name'},
                        {data: 'assigns', name: 'assigns'},
                        {data: 'project_hours', name: 'project_hours'},
                        {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
                        {data: 'status', name: 'status', orderable: false, searchable: false}
                    ],
                    drawCallback: function () {
                        $('[data-bs-toggle="tooltip"]').tooltip({
                            html: true,
                            placement: 'right'
                        });
                    }
                });  

                $('#searchProject').on('input', function() {
                    projectList.search(this.value).draw();
                });
            @endif

            // Milestones list
            @if(in_array(auth()->user()->role, [1, 7]))
                var milestones = $('.milestones-list').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: false,
                    dom: 'lrtp',
                    info: false,
                    ajax: {
                        url: "{{ route('project.milestones-list') }}",
                        data: { type: 'milestones' } 
                    },
                    columns: [
                        {data: 'milestone_name', name: 'milestone_name'},
                        {data: 'project_name', name: 'project_name'},
                        {data: 'status', name: 'status', orderable: false, searchable: false},
                        {data: 'Deadline', name: 'Deadline', orderable: false, searchable: false},
                        {data: 'Alert', name: 'Alert', orderable: false, searchable: false}
                    ],
                    columnDefs: [
                        { 
                            targets: [/* Column index of Alert */], 
                            orderable: false,
                            render: function(data, type, row) {
                                return data ? data : ''; // Ensure there's always valid content
                            }
                        }
                    ]
                });

                $('#searchMilestones').on('input', function() {
                    milestones.search(this.value).draw();
                });
            @endif

            // tasks assigned to team members list
            @if(in_array(auth()->user()->role, [1, 7]))
                var assignedTask = $('.member-task-list').DataTable({
                    processing: true,
                    serverSide: true,
                    paging: false,
                    dom: 'lrtp',
                    info: false,
                    ajax: {
                        url: "{{ route('project.member-task-list') }}",
                        data: { type: 'tasks' }
                    },
                    columns: [
                        {data: 'task_name', name: 'task_name'},
                        {data: 'assigns', name: 'assigns'},
                        {data: 'estimated_time', name: 'estimated_time'},
                        // {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
                        {data: 'status', name: 'status', orderable: false, searchable: false},
                        {data: 'priority', name: 'priority'},
                        {data: 'deadline', name: 'deadline'}
                    ],
                    drawCallback: function () {
                        $('[data-bs-toggle="tooltip"]').tooltip({
                            placement: 'right',
                            html: true
                        });
                    }
                });

                $('#searchTasks').on('input', function() {
                    assignedTask.search(this.value).draw();
                });
            @endif

            
            // View of all assigned tasks with deadlines and priorities.
            // var task = $('.task-deadline-priority').DataTable({
            //     processing: true,
            //     serverSide: true,
            //     ajax: {
            //         url: "{{ route('project.task-deadline-priority') }}"
            //     },
            //     columns: [
            //         {data: 'task_name', name: 'task_name'},
            //         {data: 'assigns', name: 'assigns'},
            //         {data: 'priority', name: 'priority'},
            //         {data: 'deadline', name: 'deadline', orderable: false, searchable: false},
            //     ]
            // });
            
            // Today’s Tasks and Deadlines.
            var task = $('.today-task-deadlines').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('project.today-task-deadlines') }}"
                },
                columns: [
                    {data: 'task_name', name: 'task_name'},
                    {data: 'assigns', name: 'assigns'},
                    {data: 'deadline', name: 'deadline', orderable: false, searchable: false},
                ]
            });
            
            function stripHtml(html) {
                let doc = new DOMParser().parseFromString(html, 'text/html');
                return doc.body.textContent || "";
            }

            // Daily Task Updates 
            $('.daily-task-update').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('project.daily-task-update') }}",
                    data: { type: 'tasksupdate' }
                },
                columns: [
                    { data: 'task_name', name: 'task_name' },
                    { 
                        data: 'description', 
                        name: 'description',
                        render: function(data, type, row) {
                            let plainText = stripHtml(data); // Remove HTML tags
                            if (plainText.length > 50) {
                                return plainText.substring(0, 50) + 
                                    '... <a href="javascript:void(0);" class="read-more" data-fulltext="' + plainText + '">Read More</a>';
                            }
                            return plainText;
                        }
                    },
                    { 
                        data: 'assigns', 
                        name: 'assigns',
                        render: function(data) {
                            return data ? data.map(a => `<span class="badge bg-label-primary">${a.first_name} ${a.last_name}</span>`).join('<br>') : 'N/A';
                        }
                    },
                    { 
                        data: 'status', 
                        name: 'status',
                        render: function(data, type, row) {
                            return `<span class="badge" style="background-color:${row.statusDetail?.bg_color || '#ccc'}; color:${row.statusDetail?.font_color || '#000'};">${row.statusDetail?.status_name || 'Unknown'}</span>`;
                        }
                    },
                    { data: 'created_at', name: 'created_at' }
                ]
            });

            $(document).on('click', '.read-more', function() {
                alert(stripHtml($(this).data('fulltext')));  // Show full description in an alert or modal
            });

             // Load Notifications Table
            // $('.notification-list').DataTable({
            //     processing: true,
            //     serverSide: true,
            //     ajax: {
            //         url: "{{ route('project.dashboard') }}",
            //         data: { type: 'notifications' }
            //     },
            //     columns: [
            //         { data: 'message', name: 'message' },
            //         { data: 'created_at', name: 'created_at' }
            //     ]
            // });
            
        });
        
        $(document).on('ajaxComplete', function() {
            $('table').removeAttr('style');
        });

        document.addEventListener("DOMContentLoaded", function () {
            @if(in_array(auth()->user()->role, [1, 7]))
            // Budget vs. Actual Expense Chart
            const budgets = @json($budgets);
            const projectNames = budgets.map(project => project.project_name);
            const budgetData = budgets.map(project => project.budget);
            const actualExpenseData = budgets.map(project => project.actual_expense);

            Highcharts.chart('budgetChart', {
                chart: { 
                    type: 'column',
                    scrollablePlotArea: {
                        minWidth: 530,
                        scrollPositionX: 1
                    }
                },
                title: { text: 'Budget vs. Actual Expense' },
                xAxis: {
                    categories: projectNames,
                    title: { text: 'Projects' }
                },
                yAxis: {
                    min: 0,
                    title: { text: 'Amount' }
                },
                series: [
                    { name: 'Budget', data: budgetData, color: '#007bff' },
                    { name: 'Actual Expense', data: actualExpenseData, color: '#dc3545' }
                ],
                tooltip: {
                    shared: true,
                    valuePrefix: ''
                },
                credits: { enabled: false }
            });
            @endif

            @if(in_array(auth()->user()->role, [1, 7]))
            // Team Performance Chart
            const users = @json($users);
            const userNames = users.map(user => user.user_name);
            const completedTasks = users.map(user => user.completed_task);
            const colors = users.map(user => user.color || getRandomColor());

            Highcharts.chart('teamPerformanceChart', {
                chart: { type: 'pie' },
                title: { text: 'Team Task Completion Overview' },
                series: [{
                    name: 'Completed Tasks',
                    data: userNames.map((name, index) => ({
                        name,
                        y: completedTasks[index],
                        color: colors[index]
                    }))
                }],
                tooltip: { pointFormat: '{series.name}: <b>{point.y}</b>' },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.y}' }
                    }
                },
                credits: { enabled: false }
            });
            @endif

            @if(in_array(auth()->user()->role, [1, 7]))
                function getRandomColor() {
                    return `#${Math.floor(Math.random()*16777215).toString(16)}`;
                }

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
        
        tinymce.init({
            selector: 'textarea#work_description',
            license_key: 'gpl',
            branding: false,
            promotion: false,
            menubar: false,
            theme: 'silver',
            skin: 'tinymce-5'
        });
    </script>
@endpush