@extends('app')

@section('title', $user->first_name)

@push('style')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    
    <style>
        .progress-circle {
            position: relative;
            width: 190px;
            height: 190px;
            border-radius: 50%;
            background: conic-gradient(#DF5353 var(--progress-degree), #e0e0e0 0deg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #3c4761;
        }

        .progress-circle::before {
            content: "";
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #fff;
        }

        .progress-text {
            position: relative;
            z-index: 1;
        }
    </style>
@endpush

@section('breadcrumb')

<ol class="breadcrumb">

    <li class="breadcrumb-item">

        <a href="#">Dashboard</a>

    </li>

    <li class="breadcrumb-item">

        <a href="{{ route('employee.list') }}">List</a>

    </li>

    <li class="breadcrumb-item">Profile</li>

</ol>

@endsection

@section('content')
@php
    $isSelf = auth()->id() == $user->id;
@endphp
<div class="container mt-5">

    <div class="card shadow-lg rounded-4 border-light">

        <div class="card-body p-4">

            <div class="d-flex align-items-center justify-content-between">

                <div class="d-flex align-items-center">

                    <img src="{{ url('assets/img/avatars/1.png') }}" alt="Profile Picture" class="rounded-circle border border-primary me-4" width="100" height="100">

                    <div>

                        <!--<h3 class="text-dark">{{ $user->first_name }} {{ $user->last_name }}</h3>-->
                        @php
                            $isSelf = auth()->id() == $user->id;
                            $isAdmin = auth()->user()->role == 1;
                        @endphp
                        
                        <h3 class="text-dark d-flex align-items-center">
                            <span id="user-name-display">
                                {{ $user->first_name }} {{ $user->last_name }}
                            </span>
                        
                            @if ($isSelf || $isAdmin)
                                <button class="btn btn-sm btn-link ms-2 p-0" onclick="toggleNameEdit()" title="Edit Name">
                                    <i class="bi bi-pencil-square text-primary"></i>
                                </button>
                            @endif
                        </h3>
                        
                        @if ($isSelf || $isAdmin)
                            <form method="POST" action="{{ route('employee.inline.update', $user->id) }}" id="name-edit-form" class="d-none">
                                @csrf
                                @method('PATCH')
                                <div class="d-flex align-items-center">
                                    <input type="text" name="first_name" class="form-control form-control-sm me-2" value="{{ $user->first_name }}" required style="max-width: 150px;">
                                    <input type="text" name="last_name" class="form-control form-control-sm me-2" value="{{ $user->last_name }}" required style="max-width: 150px;">
                                    <button type="submit" class="btn btn-sm btn-success me-1">Save</button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="toggleNameEdit()">Cancel</button>
                                </div>
                            </form>
                        @endif


                        <p class="text-muted">{{ $user->role_details->name }}</p>

                        <p class="text-muted">Employee ID: <strong>{{ $user->emp_id }}</strong></p>

                    </div>

                </div>

                <!-- Back Button (optional) -->

                <a href="{{ route('employee.list') }}" class="btn btn-sm btn-outline-primary d-none d-md-block">
                    <span class="mdi mdi-step-backward"></span>
                    Back to List
                </a>

            </div>

            <ul class="nav nav-pills mt-4" id="profileTabs" role="tablist">

                <li class="nav-item">

                    <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Profile</button>

                </li>

                <li class="nav-item">

                    <button class="nav-link" id="experiences-tab" data-bs-toggle="pill" data-bs-target="#experiences" type="button" role="tab" aria-controls="experiences" aria-selected="false">Experience</button>

                </li>

                <li class="nav-item">

                    <button class="nav-link" id="certifications-tab" data-bs-toggle="pill" data-bs-target="#certifications" type="button" role="tab" aria-controls="certifications" aria-selected="false">Certifications</button>

                </li>

                <li class="nav-item">

                    <button class="nav-link" id="workingdays-tab" data-bs-toggle="pill" data-bs-target="#workingdays" type="button" role="tab" aria-controls="workingdays" aria-selected="false">Working Days & Timing</button>

                </li>

                <li class="nav-item">

                    <button class="nav-link" id="orgchart-tab" data-bs-toggle="pill" data-bs-target="#orgchart" type="button" role="tab" aria-controls="orgchart" aria-selected="false">Organization Chart</button>

                </li>

            </ul>

            <div class="tab-content mt-4" id="profileTabsContent">

                <!-- About Tab -->

                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <h5 class="text-primary">Email:</h5>

                            <p>{{ $user->email }}</p>

                        </div>

                        <!--<div class="col-md-6 mb-3">-->

                        <!--    <h5 class="text-primary">Phone:</h5>-->

                        <!--    <p>{{ $user->phone }}</p>-->

                        <!--</div>-->
                        <div class="col-md-6 mb-3">
                            <h5 class="text-primary">Phone:</h5>
                        
                            @php
                                $isSelf = auth()->id() == $user->id;
                                $isAdmin = auth()->user()->role == 1;
                            @endphp
                        
                            <div class="d-flex align-items-center">
                                <span id="phone-display">
                                    {{ $user->phone }}
                                </span>
                        
                                @if ($isSelf || $isAdmin)
                                    <button class="btn btn-sm btn-link ms-2 p-0" onclick="togglePhoneEdit()" title="Edit Phone">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </button>
                                @endif
                            </div>
                        
                            @if ($isSelf || $isAdmin)
                                <form method="POST" action="{{ route('employee.inline.update', $user->id) }}" id="phone-edit-form" class="d-none mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <div class="d-flex align-items-center">
                                        <input type="text" name="phone" class="form-control form-control-sm me-2" value="{{ $user->phone }}" required style="max-width: 200px;">
                                        <button type="submit" class="btn btn-sm btn-success me-1">Save</button>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="togglePhoneEdit()">Cancel</button>
                                    </div>
                                </form>
                            @endif
                        </div>

                        <div class="col-md-6 mb-3">

                            <h5 class="text-primary">Role:</h5>

                            <p>{{ $user->role_details->name }}</p>

                        </div>

                        <div class="col-md-6 mb-3">

                            <h5 class="text-primary">Joined Date:</h5>

                            @if(isset($userMeta['doj']))

                            <p>{{ \Carbon\Carbon::parse(trim($userMeta['doj'], '"'))->format('d M Y') }}</p>

                            @endif

                        </div>

                    </div>

                </div>

                <!-- Experiences Tab -->

                <div class="tab-pane fade" id="experiences" role="tabpanel" aria-labelledby="experiences-tab">

                    <div class="row">

                        @php

                            $experiences = $userMeta['experience'] ?? []; 

                        @endphp

                        @if(!empty($experiences) && is_array($experiences))

                            @foreach($experiences as $experience)

                                <div class="col-md-4 mb-3">

                                    <div class="card shadow-sm border-success rounded-3">

                                        <div class="card-body">

                                            <h5 class="card-title text-primary">{{ $experience['designation'] ?? 'N/A' }}</h5>

                                            <p><strong>Employer:</strong> {{ $experience['employer'] ?? 'N/A' }}</p>

                                            <p><strong>Skills:</strong> {{ $experience['skills'] ?? 'N/A' }}</p>

                                            <p><strong>Duration:</strong> 

                                                {{ $experience['years'] ?? 0 }} year(s) 

                                                @if(!empty($experience['months'])) 

                                                    and {{ $experience['months'] }} month(s)

                                                @endif

                                            </p>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        @else

                            <p>No experiences available.</p>

                        @endif

                    </div>

                </div>

                <!-- Certifications Tab -->

                <div class="tab-pane fade" id="certifications" role="tabpanel" aria-labelledby="Certifications-tab">

                    <div class="row">

                        @php

                            // Ensure $userMeta['certifications'] is an array

                            $validCertifications = $userMeta['certifications'] ?? [];

                        @endphp

                                

                        @if(count($validCertifications) > 0)

                            @foreach($validCertifications as $certification)

                                <div class="col-md-4 mb-3">

                                    <div class="card shadow-sm">

                                        <div class="card-body text-center">

                                            <h5 class="card-title">{{ trim($certification['course_name'] ?? '') }}</h5>

                                            <p class="card-text">{{ trim($certification['course_from'] ?? '') }}</p>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        @else

                            <p>No certifications available.</p>

                        @endif

                    </div>

                </div>

                <!--Working Days & Timing-->

                @php

                    $workingDaysRaw = $userMeta['working_days'] ?? '';

                    // Check if it's a valid JSON string

                    if (is_string($workingDaysRaw)) {

                        $workingDaysRaw = stripslashes($workingDaysRaw);

                        $workingDays = json_decode($workingDaysRaw, true);

                        if (json_last_error() !== JSON_ERROR_NONE) {

                            $workingDays = [];

                        }

                    } elseif (is_array($workingDaysRaw)) {

                        $workingDays = $workingDaysRaw;

                    } else {

                        $workingDays = [];

                    }

                @endphp

                <div class="tab-pane fade" id="workingdays" role="tabpanel" aria-labelledby="Working Days & Timing-tab">

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead class="table-light">

                                <tr>

                                    <th>Day</th>

                                    <th>Start Time</th>

                                    <th>End Time</th>

                                </tr>

                            </thead>

                            <tbody>

                                @if(!empty($workingDays) && is_array($workingDays))

                                    @foreach($workingDays as $day => $details)

                                        <tr>

                                            <td>{{ $details['day'] ?? $day }}</td>

                                            <td>

                                                @if(!empty($details['start']))

                                                    {{ \Carbon\Carbon::parse($details['start'])->format('h:i A') }}

                                                @else

                                                    N/A

                                                @endif

                                            </td>

                                            <td>

                                                @if(!empty($details['end']))

                                                    {{ \Carbon\Carbon::parse($details['end'])->format('h:i A') }}

                                                @else

                                                    N/A

                                                @endif

                                            </td>

                                        </tr>

                                    @endforeach

                                @else

                                    <tr>

                                        <td colspan="3" class="text-center">No working days and timing available.</td>

                                    </tr>

                                @endif

                            </tbody>

                        </table>

                    </div>

                </div>

                <!-- Organization Chart Tab Content -->

                <div class="tab-pane fade" id="orgchart" role="tabpanel" aria-labelledby="orgchart-tab">

                    <div id="orgChartContainer" style="width: 100%; height: 600px;"></div>

                </div>

            </div>

        </div>

    </div>

    @if(in_array(auth()->user()->role, [1, 7, 8, 4, 3]))
    <div class="row mt-4">
        
        <div class="col-sm-12 col-md-4">
            
            <!-- Work Summary -->
            <div class="card mb-4">
                <div class="card-header pb-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">
                            Work Summary
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="px-5">
                        <li class="py-2">Tasks Completed: {{ $totalCompletedTask ?? '' }}/{{ $totalTask ?? '' }}</li>
                        <li>Upcoming Deadlines: {{ $upcomingDeadline ?? 0 }}</li>
                    </ul>
                </div>
            </div>

            {{-- Incentive --}}
            <div class="card mb-4">
                <div class="card-header pb-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">
                        Incentive
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <ul id="incentiveList" class="px-5">
                        <li class="list-group-item text-muted">Loading incentives...</li>
                    </ul>
                </div>
            </div>

            {{-- Employee Leaves --}}
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center border">
                    <h5 class="mb-0">Employee Leaves</h5>
                </div>
                <div class="card-body">    
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <select id="yearSelect" class="form-select">
                                @foreach(range(now()->year, 2000) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <select id="monthSelect" class="form-select">
                                @foreach(range(1, 12) as $month)
                                <option value="{{ $month }}" {{ $month == now()->month ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <div class="progress-circle" style="--progress-degree: 0deg;">
                            <span class="progress-text">0 Day</span>
                        </div>
                    </div>

                </div>
            </div>
            
            {{-- Task Completion Rate --}}
            <div class="card shadow">
                <div class="card-body">
                    <div id="taskCompletionChart"></div>
                </div>
            </div>

        </div>

        <div class="col-md-8">

            <!-- Assigned vs. Completed Tasks -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Assigned vs. Completed Tasks</h5>
                        <div class=" text-end pt-0 my-n5">
                            <input type="search" id="searchTask" class="form-control form-control-sm" placeholder="Search Task">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive sm-device">
                        <table class="table table-striped table-bordered assigned-completed-task" style="font-size: inherit;">
                            <thead>
                                <tr>
                                    <th>Task</th>
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

            @if(in_array(auth()->user()->role, [1, 7, 8, 3]) && $user->role != 4)
            <!-- completed projects in year -->
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

            @if(in_array(auth()->user()->role, [1, 7, 8, 4, 3]))
            <!-- completed tasks in year -->
            <div class="card mb-4">
                <div class="card-body">
                    <select id="yearSelectTasks" class="form-select w-25 mb-4">
                        @foreach(range(now()->year, 2021) as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                    <div id="tasksLineChart"></div>
                </div>
            </div>
            @endif

            <div class="card shadow d-none">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <select name="projectSelect" id="projectSelect" class="form-select" onchange="getProject()">
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" data-route="{{ route('projects.taskCompletion', [request()->route('id')]) }}">
                                        {{ ucwords($project->project_name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <figure class="highcharts-figure">
                        <div id="taskCompletionTime"></div>
                    </figure>
                </div>
            </div>

        </div>
    </div>

    @endif
</div>

@endsection

<script src="https://code.highcharts.com/highcharts.js"></script>

<script src="https://code.highcharts.com/modules/sankey.js"></script>

<script src="https://code.highcharts.com/modules/organization.js"></script>

<script src="https://code.highcharts.com/modules/exporting.js"></script>

<script src="https://code.highcharts.com/modules/accessibility.js"></script>


<script>
    @if(in_array(auth()->user()->role, [1, 7, 8, 4, 3]))
    function getProject() {
        var selectedOption = $('#projectSelect option:selected');
        var projectId = selectedOption.val();
        var route = selectedOption.data('route');
        
        $.ajax({
            url: route, 
            type: "GET", 
            data: { project_id: projectId },
            success: function(response) {
                if (response.success) {
                    updateChart(response.data);
                } else {
                    console.error("Error fetching data");
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    function updateChart(data) {
        console.log(data);
        Highcharts.chart('taskCompletionTime', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Employee Task Completion Time',
                align: 'left'
            },
            xAxis: {
                categories: data.allTaskName, 
                title: {
                    text: 'Tasks'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Time in Minutes'
                }
            },
            legend: {
                reversed: true
            },
            plotOptions: {
                series: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: true,
                        format: '{y} min'
                    }
                }
            },
            series: [{
                name: 'Extra Time (Overdue)',
                data: data.allExtraTime,
                color: 'red'
            }, {
                name: 'Estimated Time',
                data: data.allEstimatedTime, 
                color: 'green'
            }],
            credits: {enabled: false}
        });
    }
    @endif

    document.addEventListener('DOMContentLoaded', function () {

        $('#orgchart-tab').on('shown.bs.tab', function () {

            fetchOrgChart({{ $user->id }});

        });

        function fetchOrgChart(empId) {

            $.ajax({

                url: "{{ route('employee.orgchart', $user->id) }}",

                method: 'GET',

                success: function (response) {

                    renderOrgChart(response);

                },

                error: function () {

                    $('#orgChartContainer').html('<p class="text-danger">Failed to load organization chart.</p>');

                }

            });

        }

        function renderOrgChart(data) {

            Highcharts.chart('orgChartContainer', {

                chart: {

                    height: 600,

                    inverted: true,

                    spacing: [20, 20, 20, 20],

                    backgroundColor: '#f9fafb'

                },

                title: {

                    text: 'Organization Chart',

                    style: {

                        fontSize: '20px',

                        color: '#333',

                        fontWeight: 'bold'

                    }

                },

                series: [{

                    type: 'organization',

                    name: 'Organization',

                    keys: ['from', 'to'],

                    data: formatOrgData(data),

                    nodes: formatNodeData(data),

                    colorByPoint: false,

                    color: '#5E83FB',

                    linkLineWidth: 2,

                    linkLength: 30,

                    borderColor: '#ffffff',

                    borderRadius: 8,

                    hangingIndent: 20,

                    dataLabels: {

                        color: '#333',

                        style: {

                            fontWeight: 'bold',

                            fontSize: '14px'

                        }

                    },

                    tooltip: {

                        outside: true,

                        useHTML: true,

                        formatter: function () {

                            return `<b>${this.point.name}</b><br>${this.point.title}`;

                        }

                    }

                }],

                tooltip: {

                    outside: true,

                    useHTML: true,

                    formatter: function () {

                        return `<b>${this.point.name}</b><br>${this.point.title}`;

                    }

                }

            });

        }

        function formatOrgData(data) {

            let orgData = [];

            function traverse(node, parentId = null) {

                if (parentId !== null) {

                    orgData.push({

                        from: parentId.toString(),

                        to: node.id.toString()

                    });

                }

                if (node.children && node.children.length > 0) {

                    node.children.forEach(child => traverse(child, node.id));

                }

            }

            // Traverse from the parent node (top-most parent)

            traverse(data);

            return orgData;

        }

        function formatNodeData(data) {

            let nodes = [];

            function traverse(node, level = 0) {

                nodes.push({

                    id: node.id.toString(),

                    name: node.name,

                    title: node.title,

                    image: node.image || '',

                    width: 220,

                    height: 60,

                    color: getNodeColor(level),

                    borderColor: '#ccc',

                    borderWidth: 2,

                    dataLabels: {

                        style: {

                            fontSize: level === 0 ? '16px' : '14px',

                            fontWeight: level === 0 ? 'bold' : 'normal'

                        }

                    }

                });

                if (node.children && node.children.length > 0) {

                    node.children.forEach(child => traverse(child, level + 1));

                }

            }

            traverse(data);

            return nodes;

        }

        function getNodeColor(level) {

            const colors = ['#5E83FB', '#42b983', '#FFB74D', '#FF6F61', '#9C27B0'];

            return colors[level % colors.length];

        }

        var displayedProject = @json($displayedProject);
        updateChart(displayedProject);

    });

    @if(in_array(auth()->user()->role, [1, 7, 8, 4, 3]))
    function loadLeaveRecords() {
        var userId = "{{ request()->route('id') }}"; 
        var year = $('#yearSelect').val();
        var month = $('#monthSelect').val();

        $.ajax({
            url: "{{ route('employee.leaveRecords') }}",
            type: "GET",
            data: { user_id: userId, year: year, month: month },
            success: function(response) {
                if (response.success) {
                    $('.progress-circle').css('--progress-degree', response.progressDegree + 'deg');
                    $('.progress-text').text(response.leaveDays + ((response.leaveDays == 1 || response.leaveDays == 0) ? ' Day' : ' Days'));
                } else {
                    console.error("Error fetching leave records");
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    }
    
    function taskCompletionChart() {
        var route = "{{ route('employee.taskCompletionChart') }}";
        var empId = "{{ request()->route('id') }}"; 
        
        $.ajax({
            url: route, 
            type: "GET", 
            data: { emp_id: empId },
            success: function(response) {
                if (response.success) {
                    updateTaskCompletionChart(response);
                } else {
                    console.error("Error fetching data");
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    window.onload = function() {
        if (typeof jQuery === 'undefined') {
            console.error("jQuery is not loaded!");
            return;
        }

        loadLeaveRecords();
        taskCompletionChart();

        $('#yearSelect, #monthSelect').change(function() {
            loadLeaveRecords();
        });
    };

        // Task Completion Chart
        function updateTaskCompletionChart(data){ console.log(data)
            Highcharts.chart('taskCompletionChart', {
                chart: { type: 'bar' },
                title: { text: 'Task Completion Rate', align: 'left' },
                xAxis: { categories: ['Assigned Tasks', 'Completed Tasks'] },
                yAxis: { title: { text: 'Tasks' } },
                series: [{
                    name: data.name,colorByPoint: true, 
                    data: [
                        { y: data.assignedTasks, color: '#007bff' },
                        { y: data.completedTasks, color: 'green' }  
                    ]
                }],
                credits: { enabled: false }
            });
        }

    @endif
</script>

@push('script')
    <script src="{{ url('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/jszip.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/pdfmake.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/vfs_fonts.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/buttons.html5.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/buttons.print.min.js') }}"></script>

    <script type="text/javascript">
        $(function () {
            // Assigned vs. Completed Tasks
            var empId = "{{ request()->route('id') }}"; 

            var assignedCompletedTasks = $('.assigned-completed-task').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                dom: 'lrtp',
                info: false,
                pageLength: 10,
                ajax: {
                    url: "{{ route('employee.assignedCompletedTasks') }}",
                    type: "GET", 
                    data: function(d) {
                        d.emp_id = empId; 
                    }
                },
                data: { emp_id: empId },
                columns: [
                    {data: 'task_name', name: 'task_name'},
                    {data: 'status', name: 'status', searchable: false}
                ]
            });  

            $('#searchTask').on('input', function() {
                assignedCompletedTasks.search(this.value).draw();
            });

            // incentive program
            let employeeId = "{{ request()->route('id') }}"; 
            $.ajax({
                url: `/employee/${employeeId}/incentives`,
                type: 'GET',
                success: function(response) {
                    $('#incentiveList').empty();
                    let hasIncentive = false;
                    if (response.bonusTaskPercentage == 100) {
                        let bonusText = `Bonus: completed ${response.bonusTaskCount} out of ${response.totalTasks} task(s) on due date (${response.bonusTaskPercentage}%)`;
                        $('#incentiveList').append(`<li>${bonusText}</li>`);
                        hasIncentive = true;
                    }

                    if (response.promotionTaskPercentage == 100) {
                        let promoText = `Promotion: completed ${response.promotionTaskCount} out of ${response.totalTasks} task(s) before due date (${response.promotionTaskPercentage}%)`;
                        $('#incentiveList').append(`<li>${promoText}</li>`);
                        hasIncentive = true;
                    }

                    if (!hasIncentive) {
                        $('#incentiveList').append(`<li class="text-muted">No incentives available at this time.</li>`);
                    }
                },
                error: function() {
                    $('#incentiveList').html(`<li class="text-danger">Failed to load data</li>`);
                }
            });
        });
        
        @if(in_array(auth()->user()->role, [1, 7, 8, 3]) && $user->role != 4)
        let employeeId = "{{ request()->route('id') }}"; 
        function renderProjectLineChart(year) {
            $.ajax({
                url: '{{ route("employee.projects.monthly.completed") }}',
                type: 'GET',
                data: { year: year, empId: employeeId },
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
                            name: 'Complered Projects',
                            data: response.data,
                            color: 'green'
                        }],
                        credits: {
                            enabled: false
                        }
                    });
                }
            });
        }

        // Handle year change for projects
        $('#yearSelectProjects').on('change', function() {
            const selectedYear = $(this).val();
            renderProjectLineChart(selectedYear);
        });

        // Load current year project data on page load
        $(document).ready(function() {
            renderProjectLineChart($('#yearSelectProjects').val());
        });
        @endif

        @if(in_array(auth()->user()->role, [1, 7, 8, 4, 3]))
        let empId = "{{ request()->route('id') }}"; 
        function fetchChartData(year) {
            $.ajax({
                url: '{{ route("employee.tasks.monthly.completed") }}', 
                type: 'GET',
                data: { year: year, empId: empId },
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
                            data: response.data,
                            color: 'green'
                        }],
                        credits: {
                            enabled: false
                        }
                    });
                }
            });
        }

        $('#yearSelectTasks').on('change', function() {
            const selectedYear = $(this).val();
            fetchChartData(selectedYear);
        });

        // Load current year data by default
        $(document).ready(function() {
            fetchChartData($('#yearSelectTasks').val());
        });
        @endif
    </script>
    <script>
    function toggleNameEdit() {
        const display = document.getElementById('user-name-display');
        const form = document.getElementById('name-edit-form');
        if (form.classList.contains('d-none')) {
            form.classList.remove('d-none');
            display.style.display = 'none';
        } else {
            form.classList.add('d-none');
            display.style.display = '';
        }
    }
    </script>
    <script>
        function togglePhoneEdit() {
            const phoneDisplay = document.getElementById('phone-display');
            const phoneForm = document.getElementById('phone-edit-form');
            if (phoneForm.classList.contains('d-none')) {
                phoneForm.classList.remove('d-none');
                phoneDisplay.style.display = 'none';
            } else {
                phoneForm.classList.add('d-none');
                phoneDisplay.style.display = '';
            }
        }
    </script>

@endpush







