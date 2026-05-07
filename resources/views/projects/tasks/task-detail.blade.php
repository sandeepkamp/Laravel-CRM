@extends('app')

@section('title', $task->task_name)

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('project.details', $task->project()->first()->id) }}">{{ $task->project()->first()->project_name }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('project.task.list', $task->project()->first()->id) }}">Tasks List</a>
    </li>
    <li class="breadcrumb-item">{{ $task->task_name }}</li>
</ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header header-elements">
                    <h4 class="card-title">{{ $task->task_name }}</h4>
                    <div class="card-header-elements ms-auto">
                        <form action="{{ route('project.task.delete', [$task->project_id, $task->id]) }}" method="POST" id="delete_task_form" class="row g-3 align-items-center">
                            @csrf
                            <div class="col-auto">
                                <select class="form-select" id="change_status">
                                    @if($task_statuses)
                                        @foreach($task_statuses as $task_status)
                                        <option value="{{ $task_status->id }}" @if($task_status->id == $task->status()->first()->id) selected="selected" @endif data-url="{{ route('project.task.status.change', [$task->project_id, $task->id, $task_status->id]) }}">{{ $task_status->status_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            @if(!in_array(auth()->user()->role, [8, 4]))
                            <div class="col-auto">
                                <button type="submit" name="submit" class="btn btn-xs btn-outline-danger d-none">
                                    <span class="mdi mdi-trash-can-outline me-2"></span>
                                    <span>{{ __('Delete') }}</span>
                                </button>
                                <button type="button" id="task_delete_btn" class="btn btn-xs btn-outline-danger">
                                    <span class="mdi mdi-trash-can-outline me-2"></span>
                                    <span>{{ __('Delete') }}</span>
                                </button>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-5">
                            <div class="lead">{!! $task->description !!}</div>
                        </div>
                        <div class="col-md-6 mb-5">
                            <div class="row">
                                <div class="col-md-12 mb-5">
                                    <h6>{{ __('Task Information') }}</h6>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th class="bg-primary text-white">{{ __('Estimated Time') }}</th>
                                            <td>{{ intdiv($task->estimated_time, 60).'h '.($task->estimated_time % 60).'m'; }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-primary text-white">{{ __('Project') }}</th>
                                            <td>
                                                <a href="{{ route('project.details', $task->project()->first()->id) }}">{{ $task->project()->first()->project_name }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-primary text-white">{{ __('Milestone') }}</th>
                                            <td>
                                                <a href="{{ route('project.milestones.detail', [$task->project()->first()->id, $task->milestone()->first()->id]) }}">{{ $task->milestone()->first()->milestone_name }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-primary text-white">{{ __('Status') }}</th>
                                            <td style="color: {{ $task->status()->first()->font_color }};background-color: {{ $task->status()->first()->bg_color }}">{{ $task->status()->first()->status_name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-primary text-white">{{ __('Created At') }}</th>
                                            <td>{{ $task->created_at->format('l, jS F, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                @if(!in_array(auth()->user()->role, [8, 4]))
                                <div class="col-md-12 mb-5">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="m-0">{{ __('Task Resources') }}</h6>
                                        <div class="text-end">
                                            <button class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalToggle">
                                                <span class="mdi mdi-plus me-2"></span>
                                                <span>{{ __('Assign') }}</span>
                                            </button>
                                        </div>
                                    </div>
                                    <table class="table table-bordered">
                                        @if($task->assigns()->count() > 0)
                                            <tr class="bg-primary">
                                                <th class="text-white">{{ __('Name') }}</th>
                                                <th class="text-white">{{ __('Role') }}</th>
                                                <th class="text-white text-center">{{ __('Action') }}</th>
                                            </tr>
                                            @foreach($task->assigns()->get() as $assign)
                                                <tr data-id="{{ $assign->id }}">
                                                    <th>{{ $assign->full_name }}</th>
                                                    <td>{{ $assign->role_details()->first()->name }}</td>
                                                    <td class="text-center">
                                                        <a href="javascript:;" class="btn btn-xs btn-danger remove_assign" data-url="{{ route('project.task.remove_assign', [$task->project_id, $task->id, $assign->id]) }}">
                                                            <span class="mdi mdi-trash-can-outline"></span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                        <tr>
                                            <td class="text-center">
                                                <span class="d-block mb-3">{{ __('No one has been assigned to this task yet') }}</span>
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
                        <div class="col-md-6 mb-5">
                            <div class="divider">
                                <div class="divider-text">{{ __('Files') }}</div>
                            </div>
                            @if($task->files()->count() > 0)
                                <ul class="list-group">
                                    @foreach($task->files()->orderBy('created_at', 'desc')->get() as $file)
                                    <li class="list-group-item">
                                        <div class="mb-3">{{ $file->file_name }}</div>
                                        <div class="text-muted small d-block mb-4">{{ __('Uploaded on ').$file->created_at->format('l, jS F, Y').' at '.$file->created_at->format('h:i A') }}</div>
                                        <div class="text-end">
                                            <a href="{{ route('project.task.download', [$task->project_id, $task->id, $file->id]) }}" class="btn btn-xs btn-outline-primary">
                                                <span class="mdi mdi-download me-2"></span>
                                                <span>{{ __('Download') }}</span>
                                            </a>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            @else
                            <p>{{ __('There is no attachments available yet') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- assign modal -->
    <div class="modal modal-top fade" id="modalToggle" tabindex="-1">
        <div class="modal-dialog">
            <form method="post" action="{{ route('project.task.assign', [$task->project_id, $task->id]) }}" class="modal-content" id="assignUpdate">
                @csrf
                <input type="hidden" name="task-id" value="{{ $task->id }}">
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

    <!-- comment section -->
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
                <div class="card shadow bg-transparent">
                    <div class="card-header d-flex justify-content-between align-items-center border">
                        <h5 class="mb-0">Task Comments</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCommentModal">
                            <i class="fas fa-plus"></i> Add Comment
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="commentsList">
                            <!-- Display Comments -->
                            @foreach($taskComments as $comment)
                                <div class="card mb-3 shadow-lg">
                                    <div class="card-body">
                                        <p>{!! $comment->comment !!}</p>
                                        <div class="text-end">
                                            <small class="fw-bold">
                                                By {{ ucwords($comment?->users?->first_name . ' ' . $comment?->users?->last_name) }} 
                                                on {{ \Carbon\Carbon::parse($comment->created_at)->format('jS F, Y h:i A') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($taskComments->hasPages())
                            <nav aria-label="Page navigation example">
                                <ul class="pagination justify-content-center" id="pagination">
                                    {{ $taskComments->links() }}
                                </ul>
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Comment Modal -->
    <div class="modal fade" id="addCommentModal" tabindex="-1" aria-labelledby="addCommentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCommentModalLabel">Add Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCommentForm">
                        @csrf
                        <input type="hidden" name="task_id" value="{{ $task->id }}">
                        <div class="mb-3">
                            <label for="comment" class="form-label">Comment</label>
                            <textarea class="form-control" id="comment" name="comment"></textarea>
                        </div>
                        <button type="submit" id="saveCommentButton" class="btn btn-primary">Save Comment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<!-- Or for RTL support -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://benedict2010.com/vendor/tinymce/tinymce.min.js" referrerpolicy="origin"></script>

<script type="text/javascript">
    $('#task_delete_btn').click(function(){
        Swal.fire({
            title: "Are you sure?",
            text: "You will not be able to recover this task",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: 'Yes, I am sure!',
            cancelButtonText: "No, cancel it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete_task_form').find('button[type="submit"]').click();
            } else {
                Swal.fire("Cancelled", "Task is safe for now!", "success");
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
                        type: "POST",
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
                url: "{{ route('project.get_project_users') }}",
                data: {
                    project_id: '{{ $task->project_id }}',
                    task_id: '{{ $task->id }}'
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

        $('#assignUpdate').one('submit', function(e){
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "{{ route('project.task.assign', [$task->project_id, $task->id]) }}",
                data: $('#assignUpdate').serialize(),
                success: function(res){
                    $('#modalToggle').modal('hide');
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
        });

        $('#change_status').on('change', function(e){
            window.location.href = $(this).find(":selected").data('url');
        });
    });

    // Task Comment Form Submission
    $(document).ready(function() {
        $('#addCommentForm').on('submit', function(e) {
            e.preventDefault();
            let submitBtn = $('#saveCommentButton');
            submitBtn.prop('disabled', true);
            let newComment = tinymce.get("comment").getContent();
            
            if (newComment.trim() === "") {
                submitBtn.prop('disabled', false);
                Swal.fire({
                    icon: "warning",
                    title: "Oops...",
                    text: "Please enter a comment!",
                    didOpen: () => {
                        $(".swal2-container").css("z-index", "99999");
                    }
                });
                return;
            }

            $.ajax({
                url: "{{ route('project.task.comment.submit', [$task->project_id, $task->id]) }}",
                type: "POST",
                data: {
                    comment: newComment,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        didClose: () => {
                            location.reload(); 
                        }
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON.message || 'Something went wrong!',
                        didClose: () => {
                            location.reload(); 
                        }
                    });
                }
            });
            $('#addCommentModal').modal('hide');
        });

        tinymce.init({
            selector: 'textarea#comment',
            license_key: 'gpl',
            branding: false,
            promotion: false,
            menubar: false,
            theme: 'silver',
            skin: 'tinymce-5'
        });
    }); // document closed
</script>
@endpush