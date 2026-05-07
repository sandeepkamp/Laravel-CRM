@extends('customer.app')

@section('title', ucwords($milestone->milestone_name))

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('clients.projects-details', [$project->id]) }}">{{ ucwords($project->project_name) }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('clients.project-milestone-list', [$project->id]) }}">Milestone List</a>
        </li>
        <li class="breadcrumb-item">{{ ucwords($milestone->milestone_name) }}</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header header-elements">
                    <h4 class="card-title">{{ ucfirst($milestone->milestone_name) }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-5">
                            <div class="lead">{!! $milestone->description !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')

@endpush