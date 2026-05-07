@extends('app')

@section('title', ucwords($project->project_name))

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('project.dashboard') }}">Project Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('project.list') }}">List</a>
    </li>
    <li class="breadcrumb-item">{{ ucwords($project->project_name) }}</li>
</ol>
@endsection

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">{{ __('Project Summary') }}</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row py-4">
                    <div class="col-md-3 col-12 mb-3">
                        <a href="{{ route('project.task.list', $project->id) }}">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-primary rounded shadow-xs">
                                        <i class="mdi mdi-calendar-check"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0">{{ __('Total Tasks') }}</p>
                                    <h5 class="mb-0">{{ $project->tasks()->count() }}</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-12 mb-3">
                        <a href="{{ route('project.milestones.list', [$project->id]) }}">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-success rounded shadow-xs">
                                        <i class="mdi mdi-flag-triangle"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0">{{ __('Total Milestones') }}</p>
                                    <h5 class="mb-0">{{ $project->milestones()->count() }}</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-12 mb-3">
                        <a href="{{ route('project.bug.list', $project->id) }}">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-warning rounded shadow-xs">
                                        <i class="mdi mdi-bug-outline"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0">{{ __('Total Bugs') }}</p>
                                    <h5 class="mb-0">{{ $project->bugs()->count() }}</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-info rounded shadow-xs">
                                    <i class="mdi mdi-account-group-outline"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <p class="mb-0">{{ __('Allocated Resources') }}</p>
                                <h5 class="mb-0">{{ $project->assigns()->count() }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row my-5">
    <div class="col-md-12">
        <a href="{{ route('project.task.add', $project->id) }}" class="btn btn-primary">
            <span class="mdi mdi-calendar-check me-2"></span>
            <span>{{ __('Add New Task') }}</span>
        </a>
        @if(auth()->user()->role == 1 || auth()->user()->role == 7)
            <a href="{{ route('project.milestones.add', $project->id) }}" class="btn btn-success">
                <span class="mdi mdi-flag-triangle me-2"></span>
                <span>{{ __('Add New Milestone') }}</span>
            </a>
        @endif
        <a href="{{ route('project.bug.add', $project->id) }}" class="btn btn-warning">
            <span class="mdi mdi-bug me-2"></span>
            <span>{{ __('Raise A Bug') }}</span>
        </a>
        @if(auth()->user()->role == 1 || auth()->user()->role == 7)
            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalToggle">
                <span class="mdi mdi-account-group-outline me-2"></span>
                <span>{{ __('Assign New Resource') }}</span>
            </button>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card mb-5">
            @if(!in_array(auth()->user()->role, [8, 4]))
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">{{ ucwords($project->project_name) }}</h4>
                    <div class="text-end">
                        <form action="{{ route('project.delete', $project->id) }}" method="POST" id="delete_project_form">
                            @csrf
                            @method('DELETE')
                            <div class="btn-group" role="group">
                                <a href="{{ route('project.edit', $project->id) }}" class="btn btn-xs btn-outline-primary">
                                    <span class="mdi mdi-pencil-outline me-2"></span>
                                    <span>{{ __('Edit') }}</span>
                                </a>
                                <button type="submit" name="submit" class="btn btn-xs btn-outline-danger d-none">
                                    <span class="mdi mdi-trash-can-outline me-2"></span>
                                    <span>{{ __('Delete') }}</span>
                                </button>
                                <button type="button" id="project_delete_btn" class="btn btn-xs btn-outline-danger">
                                    <span class="mdi mdi-trash-can-outline me-2"></span>
                                    <span>{{ __('Delete') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="lead">{!! $project->description !!}</div>
                    </div>
                    <div class="col-md-12 mb-4">
                        <h6>{{ __('Project Information') }}</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th class="bg-primary text-white">{{ __('Estimated Hours') }}</th>
                                <td>{{ $project->project_hours.' Hours' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">{{ __('Expected Start Date') }}</th>
                                <td>{{ $project->start_date ? $project->start_date->format('l, jS F, Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">{{ __('Expected End Date') }}</th>
                                <td>{{ $project->end_date ? $project->end_date->format('l, jS F, Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">{{ __('Client') }}</th>
                                <td>
                                    @if($project->client)
                                        <span class="d-block">{{ $project->client()->first()->full_name }}</span>
                                        @if(auth()->user()->role == 1 || auth()->user()->role == 7)
                                            <span class="d-block">
                                                <a href="mailto:{{ $project->client()->first()->email }}">{{ $project->client()->first()->email }}</a>
                                            </span>
                                            @if($project->client()->first()->phone)
                                            <span class="d-block">
                                                <a href="tel:{{ $project->client()->first()->pnone }}">{{ $project->client()->first()->phone }}</a>
                                            </span>
                                            @endif
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    @if(!in_array(auth()->user()->role, [8, 4]))
                    <div class="col-md-12 mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="m-0">{{ __('Project Resources') }}</h6>
                            <div class="text-end">
                                <button class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalToggle">
                                    <span class="mdi mdi-plus me-2"></span>
                                    <span>{{ __('Assign') }}</span>
                                </button>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            @if($project->assigns()->count() > 0)
                                <tr class="bg-primary">
                                    <th class="text-white">{{ __('Name') }}</th>
                                    <th class="text-white">{{ __('Role') }}</th>
                                    <th class="text-white text-center">{{ __('Action') }}</th>
                                </tr>
                                @foreach($project->assigns()->get() as $assign)
                                    <tr data-id="{{ $assign->id }}">
                                        <th>{{ $assign->full_name }}</th>
                                        <td>{{ $assign->role_details()->first()->name }}</td>
                                        <td class="text-center">
                                            <a href="javascript:;" class="btn btn-xs btn-danger remove_assign" data-url="{{ route('project.remove_assign', [$project->id, $assign->id]) }}">
                                                <span class="mdi mdi-trash-can-outline"></span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                            <tr>
                                <td class="text-center">
                                    <span class="d-block mb-3">{{ __('No one has been assigned to this project yet') }}</span>
                                    <button type="button" class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalToggle">
                                        <span class="mdi mdi-plus me-2"></span>
                                        <span>{{ __('Assign Now') }}</span>
                                    </button>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
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
            @if($project->tasks()->count() > 0)
                @foreach($project->tasks()->take(10)->orderBy('created_at', 'desc')->get() as $task)
                    <a href="{{ route('project.task.detail', [$project->id, $task->id]) }}">
                        <div class="card mb-3">
                            <div class="card-body">
                                <strong class="text-primary text-decoration-underline">{{ $task->task_name }}</strong>{{ __(' has been added on ').$task->created_at->format('jS F, Y') }}
                            </div>
                        </div>
                    </a>
                @endforeach
                <div class="text-end">
                    <a href="{{ route('project.task.list', $project->id) }}" class="btn btn-xs btn-outline-primary">{{ __('View All') }}</a>
                </div>
            @else
            <p class="text-center">{{ __('No Task Added Yet') }}</p>
            <div class="text-center">
                <a href="{{ route('project.task.add', $project->id) }}" class="btn btn-xs btn-outline-primary">{{ __('Add New') }}</a>
            </div>
            @endif
        </div>
        <div class="divider">
            <div class="divider-text">{{ __('Bugs') }}</div>
        </div>
        <div class="d-block">
            @if($project->bugs()->count() > 0)
                @foreach($project->bugs()->take(10)->orderBy('created_at', 'desc')->get() as $bug)
                <a href="{{ route('project.bug.detail', [$project->id, $bug->id]) }}">
                    <div class="card mb-3">
                        <div class="card-body">
                            <strong class="text-primary text-decoration-underline">{{ $bug->bug_name }}</strong>{{ __(' has been added on ').$bug->created_at->format('jS F, Y') }}
                        </div>
                    </div>
                </a>
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
<div class="modal modal-top fade" id="modalToggle" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="{{ route('project.assign', $project->id) }}" class="modal-content" id="assignUpdate">
            @csrf
            <input type="hidden" name="project-id" value="{{ $project->id }}">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center">
                    <div class="spinner-grow" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<!-- Or for RTL support -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
    $('#project_delete_btn').click(function(){
        Swal.fire({
            title: "Are you sure?",
            text: "You will not be able to recover this project",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: 'Yes, I am sure!',
            cancelButtonText: "No, cancel it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete_project_form').find('button[type="submit"]').click();
            } else {
                Swal.fire("Cancelled", "Project is safe for now!", "success");
            }
        });
    });

    $(function(){
        $('.remove_assign').click(function(e){
            e.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: "You will not be able to revert back this action",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Yes, I am sure!',
                cancelButtonText: "No, cancel it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    var target = $(this).data('url');
                    $.ajax({
                        type: "DELETE",
                        url: target,
                        data: {
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(res){
                            if(res.success){
                                Swal.fire({
                                    title: res.message,
                                    icon: "success",
                                    allowOutsideClick: false,
                                    didClose: (toast) => {
                                        location.reload();
                                    }
                                });
                            }else{
                                Swal.fire({
                                    title: res.messages,
                                    icon: "error",
                                    allowOutsideClick: false,
                                    didClose: (toast) => {
                                        location.reload();
                                    }
                                });
                            }
                        },
                        error: function(request, status, error){
                            console.log("ajax call went wrong:" + request.responseText);
                        }
                    });
                }
            });
        });

        $('#modalToggle').modal({
            backdrop: 'static',
            keyboard: false
        })
        .on('show.bs.modal', function(e){
            $.ajax({
                type: "GET",
                url: "{{ route('project.get_users', $project->id) }}",
                data: {
                    project_id: '{{ $project->id }}',
                },
                success: function(res){
                    $(e.target).find('.modal-body').html(res);
                    $('.select2').select2({
                        theme: 'bootstrap-5',
                        multiple: true,
                        dropdownParent: $(e.target).find('.modal-body')
                    });
                },
                error: function(request, status, error){
                    console.log("ajax call went wrong:" + request.responseText);
                }
            });
        })
        .on('hide.bs.modal', function(e){
            $(e.target).find('.modal-body').html('<div class="d-flex justify-content-center"><div class="spinner-grow" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        });

        $('#assignUpdate').submit(function(e){
            e.preventDefault();
            var submitBtn = $(this).find(':submit');
            submitBtn.prop('disabled', true);

            $.ajax({
                type: "POST",
                url: "{{ route('project.assign', $project->id) }}",
                data: $('#assignUpdate').serialize(),
                success: function(res){
                    $('#modalToggle').modal('hide');
                    Swal.fire({
                        title: 'Project Details Updated',
                        icon: "success",
                        allowOutsideClick: false,
                        didClose: (toast) => {
                            location.reload();
                        }
                    });
                },
                error: function(request, status, error){
                    console.log("ajax call went wrong:" + request.responseText);
                    submitBtn.prop('disabled', false);
                }

            });
        });
    });
</script>
@endpush