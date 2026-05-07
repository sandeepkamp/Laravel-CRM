@extends('customer.app')

@section('title', ucwords($project->project_name))

@push('style')
    <link rel="stylesheet" type="text/css" id="mce-u0" href="https://benedict2010.com/vendor/tinymce/skins/ui/tinymce-5/skin.min.css">
    <style>
        #feedbackForm .card {
            border-radius: 10px;
        }
        #feedbackForm .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
        }
        #feedbackForm .rating input {
            display: none;
        }
        #feedbackForm .rating label {
            font-size: 24px;
            color: #ccc;
            cursor: pointer;
        }
        #feedbackForm .rating input:checked ~ label,
        #feedbackForm .rating label:hover,
        #feedbackForm .rating label:hover ~ label {
            color: gold;
        }
    </style>
@endpush

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('clients.dashboard') }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('clients.projects') }}">Project List</a>
        </li>
        <li class="breadcrumb-item">{{ ucwords($project->project_name) }}</li>
    </ol>
@endsection

@section('content')
    <!-- Project Summary -->
    <div class="row">
        <div class="col-sm-12 col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title">{{ __('Project Summary') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-12 mb-4">
                            <a href="{{ route('clients.projects-task-list', $project->id) }}">
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
                        <div class="col-md-4 col-sm-12 mb-4">
                            <a href="{{ route('clients.project-milestone-list', [$project->id]) }}">
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
                        
                        <div class="col-md-4 col-sm-12">
                            <a href="{{ route('clients.project-bug-list', $project->id) }}">
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

                        <div class="col-md-3 col-6 d-none">
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
                        <div class="col-md-3 col-6 d-none">
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

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-5">
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title">{{ ucwords($project->project_name) }}</h4>
                        <!-- display after project handed over -->
                        @if($project->status == 8 && $project?->projectFeedback?->count() < 1)
                        <div class="text-end feedback-submission-button">
                            <button type="button" class="btn btn-xs btn-outline-primary waves-effect p-2 px-3" data-bs-toggle="modal" data-bs-target="#feedbackSubmission">
                                Feedback
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body mt-2">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="lead">{!! $project->description !!}</div>
                        </div>
                        <div class="col-md-12 mb-4">
                            <h6>{{ __('Project Information') }}</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th class="bg-primary text-white">{{ __('Estimated Hours') }}</th>
                                    <td>{{ intdiv($project->project_hours, 60) . 'h ' . ($project->project_hours % 60) . 'm' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-primary text-white">{{ __('Expected Start Date') }}</th>
                                    <td>{{ $project->start_date ? $project->start_date->format('l, jS F, Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-primary text-white">{{ __('Expected End Date') }}</th>
                                    <td>{{ $project->end_date ? $project->end_date->format('l, jS F, Y') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="card-title">{{ __('Latest Updates') }}</h4>
                <div class="text-end">
                    <a href="{{ route('clients.project-milestone-list', [$project->id]) }}" class="btn btn-xs btn-outline-primary">
                        <span class="mdi mdi-flag-triangle me-2"></span>
                        <span>{{ __('Milestones List') }}</span>
                    </a>
                    <a href="{{ route('clients.projects-task-list', $project->id) }}" class="btn btn-xs btn-outline-primary">
                        <span class="mdi mdi-calendar-check me-2"></span>
                        <span>{{ __('Task List') }}</span>
                    </a>
                </div>
            </div>
            <div class="divider">
                <div class="divider-text">{{ __('Milestones') }}</div>
            </div>
            <div class="d-block">
                @if($project->milestones()->count() > 0)
                    @foreach($project->milestones()->take(3)->orderBy('created_at', 'desc')->get() as $milestones)
                        <a href="{{ route('clients.project-milestone-detail', [$project->id, $milestones->id]) }}">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <strong>{{ ucfirst($milestones->milestone_name) }}</strong>{{ __(' has been added on ').$milestones->created_at->format('jS F, Y') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                    <div class="text-end">
                        <a href="{{ route('clients.project-milestone-list', [$project->id]) }}" class="btn btn-xs btn-outline-primary">{{ __('View All') }}</a>
                    </div>
                @else
                    <p class="text-center">{{ __('No Task Added Yet') }}</p>
                @endif
            </div>
            <div class="divider">
                <div class="divider-text">{{ __('Tasks') }}</div>
            </div>
            <div class="d-block">
                @if($project->tasks()->count() > 0)
                    @foreach($project->tasks()->take(3)->orderBy('created_at', 'desc')->get() as $task)
                        <a href="{{ route('clients.project-task-detail', [$project->id, $task->id]) }}">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <strong>{{ ucfirst($task->task_name) }}</strong>{{ __(' has been added on ').$task->created_at->format('jS F, Y') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                    <div class="text-end">
                        <a href="{{ route('clients.projects-task-list', [$project->id]) }}" class="btn btn-xs btn-outline-primary">{{ __('View All') }}</a>
                    </div>
                @else
                    <p class="text-center">{{ __('No Task Added Yet') }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Full feedback submission Modal -->
    <div class="modal fade" id="feedbackSubmission" tabindex="-1" aria-labelledby="feedbackSubmissionLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackSubmissionLabel">Feedback Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="card p-3">
                            <form id="feedbackForm">
                                @csrf
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                <input type="hidden" name="client_id" value="{{ auth()->guard('clients')->id() }}">
                                <textarea name="comment" id="comment" class="form-control mb-2 comment" rows="3" placeholder="Enter your feedback"></textarea>
                                <!-- Rating -->
                                <div class="rating mb-2">
                                    <input type="radio" id="star5" name="rating" value="5"><label for="star5">★</label>
                                    <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
                                    <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
                                    <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
                                    <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="https://benedict2010.com/vendor/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        $(document).ready(function() {
            $('#feedbackForm').on('submit', function(e) {
                e.preventDefault(); // Prevent form from reloading

                let formData = {
                    project_id: $('input[name="project_id"]').val(),
                    client_id: $('input[name="client_id"]').val(),
                    comment: $('textarea[name="comment"]').val(),
                    rating: $('input[name="rating"]:checked').val()
                };

                if (!formData.rating) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Please select a rating!'
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('clients.feedback-submission') }}",
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#feedbackSubmission').modal('hide');
                            $('.feedback-submission-button').remove();
                            Swal.fire({
                                icon: 'success',
                                title: 'Thank You!',
                                text: response.message
                            }).then(() => {
                                $('#feedbackForm')[0].reset(); // Reset form after success
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong!'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Submission Failed',
                            text: 'An error occurred while submitting feedback.'
                        });
                        console.log(xhr.responseText);
                    }
                });
            });
        });

        tinymce.init({
            selector: 'textarea#comment',
            license_key: 'gpl',
            branding: false,
            promotion: false,
            menubar: false,
            theme: 'silver',
            skin: 'tinymce-5',
            height: 300
        });
    </script>
@endpush