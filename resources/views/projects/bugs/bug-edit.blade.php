@extends('app')

@section('title', 'Edit Bug - '.ucwords($project->project_name))

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('project.details', [$project->id]) }}">{{ ucwords($project->project_name) }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('project.bug.list', [$project->id]) }}">{{ __('Bug List') }}</a>
        </li>
        <li class="breadcrumb-item">{{ __('Edit Bug') }}</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('project.bug.update', [$project->id, $bug->id]) }}" enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="card">

                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title">{{ __('Edit Bug for '. ucwords($project->project_name)) }}</h5>
                            <div class=" text-end pt-0 my-n5">
                                <a href="{{ route('project.bug.list', [$project->id]) }}" class="btn btn-outline-primary btn-sm waves-effect">
                                    <span class="mdi mdi-step-backward"></span> Back
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-4">
                                        <input type="text" class="form-control @error('bug_name') is-invalid @enderror" id="bug_name" name="bug_name" value="{{ $bug->bug_name }}" placeholder="{{ __('Bug Name') }}" maxlength="200" required>
                                        <span class="form-text">{{ __('Maximum 200 chars') }}</span>
                                        <label class="form-label" for="bug_name">{{ __('Bug Name') }} <span class="text-danger">*</span></label>
                                        @error('bug_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-4">
                                        <input type="number" class="form-control @error('estimated_time') is-invalid @enderror" id="estimated_time" name="estimated_time" value="{{ $bug->estimated_time }}" placeholder="{{ __('Estimated Time') }}" maxlength="200" required>
                                        <span class="form-text">{{ __('In Minutes') }}</span>
                                        <label class="form-label" for="estimated_time">{{ __('Estimated Time') }} <span class="text-danger">*</span></label>
                                        @error('estimated_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-3">
                                        <select class="form-select @error('milestone') is-invalid @enderror select2" name="milestone" id="milestone" required>
                                            @if($project->milestones()->count() > 0)
                                                @foreach($project->milestones()->get() as $milestone)
                                                    <option value="{{ $milestone->id }}" {{ ($bug->milestone_id == $milestone->id) ? 'selected' : '' }}>
                                                        {{ $milestone->milestone_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <label class="form-label" for="milestone">{{ __('Milestone') }} <span class="text-danger">*</span></label>
                                        @error('milestone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline mb-3">
                                       <select class="form-select @error('status') is-invalid @enderror select2" name="status" id="status" required>
                                           @if($bugStatus->count() > 0)
                                               @foreach($bugStatus as $status)
                                                    <option value="{{ $status->id }}" {{ ($status->id == $bug->status) ? 'selected' : '' }}>
                                                        {{ $status->status_name }}
                                                    </option>
                                               @endforeach
                                           @endif
                                       </select>
                                       <label class="form-label" for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                                       @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                       @enderror
                                   </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="form-label mb-0" for="bug_files">{{ __('Upload Files') }}</label>
                                    <input type="file" class="form-control" name="bug_files[]" id="bug_files" multiple accept=".mp4, .webp, .pdf, .jpg, .jpeg, .png, .gif, .doc, .docx, .pdf">
                                    <span class="form-text">{{ __('Supported format mp4, webp, pdf, jpg, jpeg, png, gif, doc, docx at max size 10mb') }}</span>
                                    @error('bug_files')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-floating form-floating-outline mb-3">
                                        <textarea class="form-control @error('bug_description') is-invalid @enderror" name="bug_description" id="bug_description" maxlength="1000">{{ $bug->description }}</textarea>
                                        <span class="form-text">{{ __('Maximum 1000 chars') }}</span>
                                        <label class="form-label" for="bug_description">{{ __('Bug Description') }}</label>
                                        @error('bug_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary" name="submit">{{ __('Update') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ url('vendor/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="text/javascript">
        tinymce.init({
            selector: 'textarea#bug_description',
            license_key: 'gpl',
            branding: false,
            promotion: false,
            menubar: false,
            theme: 'silver',
            skin: 'tinymce-5'
        });

        $(function(){
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: 'resolve',
                placeholder: "{{ __('Select milestone') }}",
            });
        });
    </script>
@endpush