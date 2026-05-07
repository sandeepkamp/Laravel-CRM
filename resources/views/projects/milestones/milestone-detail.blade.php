@extends('app')

@section('title', ucwords($milestone->milestone_name))

@push('style')
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
            <a href="{{ route('project.details', [$project->id]) }}">{{ ucwords($project->project_name) }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('project.milestones.list', [$project->id]) }}">Milestone List</a>
        </li>
        <li class="breadcrumb-item">{{ ucwords($milestone->milestone_name) }}</li>
    </ol>
@endsection

@section('content')
        <!-- Milestone Bug Progress Overview section start -->
        <div class="row mb-4">
        <div class="col-sm-12 col-md-8">
            <div class="card p-2">

                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Milestone Bugs Overview</h5>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-12 col-md-4">
                            <div class="d-flex align-items-center">
                                <a href="javascript::void()">
                                <div class="avatar">
                                    <div class="avatar-initial bg-primary rounded shadow">
                                        <i class="tf-icons mdi mdi-bug-outline mdi-24px"></i>
                                    </div>
                                </div>
                                </a>
                                <div class="ms-3">
                                    <div class="small mb-1">Total</div>
                                    <h5 class="mb-0">{{ $totalBugCount }}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-4">
                            <div class="d-flex align-items-center">
                                <a href="javascript::void()">
                                <div class="avatar">
                                    <div class="avatar-initial bg-success rounded shadow">
                                        <i class="tf-icons mdi mdi-bug-check-outline mdi-24px"></i>
                                    </div>
                                </div>
                                </a>
                                <div class="ms-3">
                                    <div class="small mb-1">Resolved</div>
                                    <h5 class="mb-0">{{ $resolvedBugCount }}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-info rounded shadow">
                                        <i class="tf-icons mdi mdi-bug-play-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">In Progress</div>
                                    <h5 class="mb-0">{{ $inProgressBugCount }}</h5>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <!-- Task Completion Status -->
        <div class="col-sm-12 col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div id="milestoneContributionChart"></div>
                </div>
            </div>
        </div>
        <!-- Task VS Bug Completion Rate -->
        <div class="col-sm-12 col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div id="taskBugCompletionRate"></div>
                </div>
            </div>
        </div>
        <!-- Task Completion Status -->
        <div class="col-sm-12 col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div id="taskCompletionStatus"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="card mb-4 p-3">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">{{ ucfirst($milestone->milestone_name) }}</h4>
                    <div class="text-end">
                        <form action="{{ route('project.milestones.delete', [$project->id, $milestone->id]) }}" method="POST" id="delete_milestone_form">
                            @csrf
                            <div class="btn-group" role="group">
                                <a href="{{ route('project.milestone.edit', [$project->id, $milestone->id]) }}" class="btn btn-xs btn-outline-primary">
                                    <span class="mdi mdi-pencil-outline me-2"></span>
                                    <span>{{ __('Edit') }}</span>
                                </a>
                                <button type="button" onclick="confirmDelete({{$milestone->id}})" class="btn btn-xs btn-outline-danger">
                                    <span class="mdi mdi-trash-can-outline me-2"></span>
                                    <span>{{ __('Delete') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
                <div class="card-body">
                    <div class="lead">{!! $milestone->description !!}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="card-title">{{ __('Latest Updates') }}</h4>
                <div class="text-end">
                    <a href="{{ route('project.task.list', $project->id) }}" class="btn btn-xs btn-outline-primary">
                        <span class="mdi mdi-calendar-check me-2"></span>
                        <span>{{ __('Task List') }}</span>
                    </a>
                    <a href="{{ route('project.bug.list', $project->id) }}" class="btn btn-xs btn-outline-primary">
                        <span class="mdi mdi-bug me-2"></span>
                        <span>{{ __('Bug List') }}</span>
                    </a>
                </div>
            </div>
            <div class="divider">
                <div class="divider-text">{{ __('Tasks') }}</div>
            </div>
            <div class="d-block">
                @if($milestone->milestoneTasks()->count() > 0)
                    @foreach($milestone->milestoneTasks()->take(3)->orderBy('created_at', 'desc')->get() as $task)
                        <a href="{{ route('project.task.detail', [$milestone->project_id, $task->id]) }}">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <strong>{{ ucfirst($task->task_name) }}</strong>{{ __(' has been added on ').$task->created_at->format('jS F, Y') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                    <div class="text-end">
                        <a href="{{ route('project.task.list', $milestone->project_id) }}" class="btn btn-xs btn-outline-primary">{{ __('View All') }}</a>
                    </div>
                @else
                <p class="text-center">{{ __('No Task Added Yet') }}</p>
                <div class="text-center">
                    <a href="{{ route('project.task.add', $milestone->project_id) }}" class="btn btn-xs btn-outline-primary">{{ __('Add New') }}</a>
                </div>
                @endif
            </div>
            <div class="divider">
                <div class="divider-text">{{ __('Bugs') }}</div>
            </div>
            <div class="d-block">
                @if($milestone->milestoneBugs()->count() > 0)
                    @foreach($milestone->milestoneBugs()->take(3)->orderBy('created_at', 'desc')->get() as $bug)
                        <div class="card mb-3">
                            <div class="card-body">
                                <strong>{{ ucfirst($bug->bug_name) }}</strong>{{ __(' has been added on ').$bug->created_at->format('jS F, Y') }}
                            </div>
                        </div>
                    @endforeach
                    <div class="text-end">
                        <a href="{{ route('project.bug.list', $project->id) }}" class="btn btn-xs btn-outline-primary">{{ __('View All') }}</a>
                    </div>
                @else
                <p class="text-center">{{ __('No Bugs Raised So Far') }}</p>
                <div class="text-center">
                    <a href="{{ route('project.bug.add', $project->id) }}" class="btn btn-xs btn-outline-primary">{{ __('Raise Now') }}</a>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Milestone Completion Rate
            const completedTasksRate = @json($completedTasksRate);
            const completedBugsRate = @json($completedBugsRate);
            const taskBugCompletedRate = completedTasksRate + completedBugsRate;

            // Calculate overall milestone completion & uncompletion rate
            const taskBugUnompletedRate = 200 - taskBugCompletedRate;

            const completedMilestonePercentage = parseFloat(((taskBugCompletedRate / (taskBugCompletedRate + taskBugUnompletedRate)) * 100).toFixed(2));
            const remainingMilestonePercentage = parseFloat(((taskBugUnompletedRate / (taskBugCompletedRate + taskBugUnompletedRate)) * 100).toFixed(2));

            // Contribution to Milestone Completion
            Highcharts.chart('milestoneContributionChart', {
                chart: { type: 'pie' },
                title: { 
                    text: "Milestone Completion Rate", 
                    useHTML: true,
                    align: 'left'
                },
                tooltip: { pointFormat: '{series.name}: <b>{point.y}%</b>' },
                series: [{
                    name: 'Milestone Completion',
                    data: [
                        { name: 'Completed Milestone Rate', y: completedMilestonePercentage, color: '#17a2b8' }, // Cyan
                        { name: 'Remaining Milestone Rate', y: remainingMilestonePercentage, color: '#dc3545' } // Red
                    ]
                }],
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: { 
                            enabled: true, 
                            format: '<b>{point.name}</b>: {point.y}%' 
                        }
                    }
                },
                credits: { enabled: false }
            });

            // Task vs Bug Completion Rate
            const tasksCompletedPercentage = (completedTasksRate > 0) ? parseFloat(((completedTasksRate / taskBugCompletedRate) * 100).toFixed(2)) : 0;
            const bugsCompletedPercentage = (completedBugsRate > 0) ? parseFloat(((completedBugsRate / taskBugCompletedRate) * 100).toFixed(2)) : 0;
            
            Highcharts.chart('taskBugCompletionRate', {
                chart: { type: 'pie' },
                title: { 
                    text: "Task VS Bug Completion Rate", 
                    useHTML: true,
                    align: 'left'
                },
                tooltip: { pointFormat: '{series.name}: <b>{point.y}%</b>' },
                series: [{
                    name: 'Completion Rate',
                    data: [
                        { name: 'Task Completion Rate', y: tasksCompletedPercentage, color: '#28a745' }, // Green
                        { name: 'Bug Resolution Rate', y: bugsCompletedPercentage, color: '#007bff' } // Blue
                    ]
                }],
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: { 
                            enabled: true, 
                            format: '<b>{point.name}</b>: {point.y}%' 
                        }
                    }
                },
                credits: { enabled: false }
            });
            
            // Task Completion Status
            const completedTasks = @json($completedTaskCount);
            const inProgressTasks = @json($inProgressTaskCount);
            const pendingTasks = @json($pendingTaskCount);

            Highcharts.chart('taskCompletionStatus', {
                chart: { type: 'pie' },
                title: { text: "Milestone Task Progress Overview", useHTML: true, align: 'left' },
                series: [{
                    name: 'Tasks',
                    colorByPoint: true,
                    data: [
                        { name: 'Completed', y: completedTasks, color: '#28a745' }, 
                        { name: 'In Progress', y: inProgressTasks, color: '#fe6a35' }, 
                        { name: 'Pending', y: pendingTasks, color: '#ffc107' } 
                    ]
                }],
                tooltip: { pointFormat: '{series.name}: <b>{point.y}</b>' },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: { 
                            enabled: true, 
                            format: '<b>{point.name}</b>: {point.y}' 
                        }
                    }
                },
                credits: { enabled: false }
            });
        }); // document closed

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
                    document.getElementById('delete_milestone_form').submit();
                }
            });
        }
    </script>
@endpush