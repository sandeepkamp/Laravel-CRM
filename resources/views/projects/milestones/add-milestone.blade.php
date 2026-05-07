@extends('app')

@section('title', 'Add Milestone - '.ucwords($project->project_name))

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('project.details', [$project->id]) }}">{{ ucwords($project->project_name) }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('project.milestones.list', [$project->id]) }}">{{ __('Milestone List') }}</a>
        </li>
        <li class="breadcrumb-item">{{ __('Add Milestone') }}</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <form id="milestoneForm" method="POST" action="{{ route('project.milestones.add.submit', [$project->id]) }}" enctype="multipart/form-data">
                @csrf
                <div class="card">

                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title">{{ __('Add Milestone for '. ucwords($project->project_name)) }}</h5>
                            <div class=" text-end pt-0 my-n5">
                                <a href="{{ route('project.milestones.list', [$project->id]) }}" class="btn btn-outline-primary btn-sm waves-effect">
                                    <span class="mdi mdi-step-backward"></span> Back
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <strong><i class="tf-icons mdi mdi-check-circle-outline"></i> Success -</strong>
                            {{ session()->get('success') }}
                        </div>
                        @endif
                        @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <strong><i class="tf-icons mdi mdi-alert-outline"></i> Error -</strong>
                            {{ session()->get('error') }}
                        </div>
                        @endif
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-floating form-floating-outline mb-3">
                                        <input type="text" class="form-control @error('milestone_name') is-invalid @enderror" id="milestone_name" name="milestone_name" value="{{ old('milestone_name') }}" placeholder="{{ __('Milestones Name') }}" maxlength="200" required>
                                        <span class="form-text">{{ __('Maximum 200 chars') }}</span>
                                        <label class="form-label" for="milestone_name">{{ __('Milestones Name') }} <span class="text-danger">*</span></label>
                                        @error('milestone_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-floating form-floating-outline mb-3">
                                        <textarea class="form-control @error('milestone_description') is-invalid @enderror" name="milestone_description" id="milestone_description" maxlength="1000">{{ old('milestone_description') }}</textarea>
                                        <span class="form-text">{{ __('Maximum 1000 chars') }}</span>
                                        <label class="form-label" for="milestone_description">{{ __('Milestone Description') }}</label>
                                        @error('milestone_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary" name="submit" id="saveMilestoneBtn">{{ __('Save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ url('vendor/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            let form = $('#milestoneForm');
            let submitBtn = $('#saveMilestoneBtn');

            form.on('submit', function () {
                submitBtn.prop('disabled', true).text('Saving...');
            });

            let hasError = @json(session()->has('error') || $errors->any());
            if (hasError) {
                submitBtn.prop('disabled', false).text('Save');
            }
        });

        tinymce.init({
            selector: 'textarea#milestone_description',
            license_key: 'gpl',
            branding: false,
            promotion: false,
            menubar: false,
            theme: 'silver',
            skin: 'tinymce-5'
        });
    </script>
@endpush