@extends('customer.app')

@section('title', ucfirst($task->task_name))

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('clients.projects-details', $task->project()->first()->id) }}">{{ ucwords($task->project()->first()->project_name) }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('clients.projects-task-list', $task->project()->first()->id) }}">Tasks List</a>
    </li>
    <li class="breadcrumb-item">{{ ucfirst($task->task_name) }}</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header header-elements">
                <h4 class="card-title">{{ ucfirst($task->task_name) }}</h4>
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
                                            <a href="{{ route('clients.projects-details', $task->project()->first()->id) }}">{{ ucwords($task->project()->first()->project_name) }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-primary text-white">{{ __('Milestone') }}</th>
                                        <td>
                                            <a href="{{ route('clients.project-milestone-detail', [$task->project()->first()->id, $task->milestone()->first()->id]) }}">{{ $task->milestone()->first()->milestone_name }}</a>
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
                        </div>
                    </div>
                    @php $showFilesSection = false; @endphp
                    <div class="col-md-6 mb-5">
                        <div class="divider">
                            <div class="divider-text">{{ __('Files') }}</div>
                        </div>
                        @if($showFilesSection)
                        @if($task->files()->count() > 0)
                            <ul class="list-group">
                                @foreach($task->files()->orderBy('created_at', 'desc')->get() as $file)
                                <li class="list-group-item">
                                    <div class="mb-3">{{ $file->file_name }}</div>
                                    <div class="text-muted small d-block mb-4">{{ __('Uploaded on ').$file->created_at->format('l, jS F, Y').' at '.$file->created_at->format('h:i A') }}</div>
                                    <div class="text-end">
                                        <a href="{{ route('clients.task-file-download', [$task->project_id, $task->id, $file->id]) }}" class="btn btn-xs btn-outline-primary">
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')

@endpush