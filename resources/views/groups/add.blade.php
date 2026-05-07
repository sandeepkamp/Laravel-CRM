@extends('app')

@section('title', 'Add New Group')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('user.list') }}">List</a>
    </li>
    <li class="breadcrumb-item">Add New Group</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show">
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                    <strong>Success -</strong>
                    {{ session()->get('success') }}
                </div>
                @endif
                <form method="POST" action="{{ route('group.add') }}">
                    @csrf
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Name" required>
                                    <label class="form-label" for="name">Name</label>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-control @error('owner') is-invalid @enderror select2" name="owner" id="owner">
                                        <option value=""> -- select -- </option>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}" @if(old('owner')==$user->id) selected="selected" @endif>{{ $user->full_name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="form-label" for="owner">Owner</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-control @error('members') is-invalid @enderror select2" name="members[]" multiple id="members">

                                        @foreach($members as $user)
                                        <option value="{{ $user->id }}" @if(old('owner')==$user->id) selected="selected" @endif>{{ $user->full_name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="form-label" for="members">Members</label>
                                </div>
                            </div>
                            <div class="col-md-6">

                            </div>
                        </div>

                    </div>
                    <div class="form-actions">
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" name="submit">SAVE</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<!-- Styles -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<!-- Or for RTL support -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
@endsection

@section('script')
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
        });
        // Store original members dropdown HTML for resetting
        var originalMembersOptions = $('#members').html();
        $('#owner').on('change', function() {
            var selectedOwnerId = $(this).val();
            // Enable all options in the members dropdown
            $('#members option').prop('disabled', false);

            // Disable the selected owner in the members dropdown
            $('#members option[value="' + selectedOwnerId + '"]').prop('disabled', true);
        });
        $('#members').on('change', function() {
            var selectedOwnerId = $(this).val();
            // console.log( selectedOwnerId);

            // Enable all options in the members dropdown
            $('#owner option').prop('disabled', false);

            // // Disable the selected owner in the members dropdown
            for (key of selectedOwnerId) {
                console.log(key);
                $('#owner option[value="' + key + '"]').prop('disabled', true);
            }
            // $('#members option[value="' + selectedOwnerId + '"]').prop('disabled', true);
        });

    });
</script>
@endsection