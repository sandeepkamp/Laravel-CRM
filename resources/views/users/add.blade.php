@extends('app')



@section('title', 'Add New User')



@section('breadcrumb')

<ol class="breadcrumb">

    <li class="breadcrumb-item">

        <a href="{{ route('dashboard') }}">Dashboard</a>

    </li>

    <li class="breadcrumb-item">

        <a href="{{ route('user.list') }}">List</a>

    </li>

    <li class="breadcrumb-item">Add New User</li>

</ol>

@endsection



@section('content')

<div class="row">

    <div class="col-12">

        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title">User Form</h5>
                    <div class=" text-end pt-0 my-n5">
                        <a href="{{ route('user.list') }}" class="btn btn-outline-primary btn-sm waves-effect">
                            <span class="mdi mdi-step-backward"></span> Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">

                @if(session()->has('success'))

                <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show">

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                    <strong>Success -</strong>

                    {{ session()->get('success') }}

                </div>

                @endif

                <form method="POST" action="{{ route('user.add') }}" autocomplete="off" enctype="multipart/form-data">

                    @csrf

                    <div class="form-body">

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="First Name" required autocomplete="off">

                                    <label class="form-label" for="first_name">First Name <span class="text-danger">*</span></label>

                                    @error('first_name')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" required autocomplete="off">

                                    <label class="form-label" for="last_name">Last Name <span class="text-danger">*</span></label>

                                    @error('last_name')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">
            
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="off">

                                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>

                                    @error('email')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Phone" autocomplete="off" required pattern="^[\d\+\-]*$" 

               title="Only numbers, plus (+), and minus (-) are allowed">

                                    <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>

                                    @error('phone')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <select class="form-control @error('department_id') is-invalid @enderror" name="department_id" id="department_id" required>

                                        <option value=""> -- select -- </option>

                                        @foreach($departments as $department)

                                            <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>

                                                {{ $department->name }}

                                            </option>

                                        @endforeach

                                    </select>

                                    <label class="form-label" for="department_id">Department <span class="text-danger">*</span></label>
                                    
                                </div>
                                
                                @error('department_id')

                                <div class="invalid-feedback">{{ $message }}</div>

                                @enderror
                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <select class="form-control @error('role') is-invalid @enderror" name="role" id="role" required>

                                        <option value=""> -- select -- </option>

                                        <!-- @if($roles)

                                            @foreach($roles as $role)

                                            <option value="{{ $role->id }}" @if(old('role') == $role->id) selected="selected" @endif>{{ $role->name }}</option>

                                            @endforeach

                                        @endif -->

                                    </select>

                                    <label class="form-label" for="role">Role <span class="text-danger">*</span></label>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{ old('password') }}" placeholder="Password" autocomplete="off">

                                    <label class="form-label" for="password">Password <span class="text-danger">*</span></label>

                                    @error('password')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password" name="confirm_password" value="{{ old('confirm_password') }}" placeholder="Confirm Password" autocomplete="off">

                                    <label class="form-label" for="confirm_password">Confirm Password <span class="text-danger">*</span></label>

                                    @error('confirm_password')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3 mt-3">

                                    <select class="form-control @error('status') is-invalid @enderror" name="status" id="status" required>

                                        <option value=""> -- select -- </option>

                                        <option value="1" @if(old('status') == 1) selected="selected" @endif>Active</option>

                                        <option value="2" @if(old('status') == 2) selected="selected" @endif>Inactive</option>

                                        <option value="3" @if(old('status') == 3) selected="selected" @endif>Blocked</option>

                                    </select>

                                    <label class="form-label" for="status">Status <span class="text-danger">*</span></label>

                                </div>

                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-0" for="documents">{{ __('Upload Files') }}</label>
                                <input type="file" class="form-control" name="documents[]" id="documents" multiple accept=".jpg, .jpeg, .png, .pdf, .doc, .docs" aria-describedby="documentsHelp" style="padding: 10px;">
                                <span id="documentsHelp" class="form-text">{{ __('Supported format jpg, jpeg, png, pdf, doc, docs — Maximum file size: 10 MB each') }}</span>
                                @error('documents')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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

        // get roles by selecting department
        $('#department_id').on('change', function() {
            var departmentId = $(this).val();

            if (departmentId) {
                $.ajax({
                    url: '/roles-by-department/' + departmentId,
                    type: 'GET',
                    success: function(data) {
                        var roleSelect = $('#role'); 
                        roleSelect.empty();
                        roleSelect.append('<option value="">--Select Role--</option>');

                        $.each(data, function(index, role) {
                            roleSelect.append('<option value="' + role.id + '">' + role.name + '</option>');
                        });
                    },
                    error: function() {
                        swal.fire({
                            icon: 'warning',
                            title: 'Error',
                            text: 'Unable to fetch roles.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });

    }); // document closed

</script>

@endsection