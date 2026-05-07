@extends('app')



@section('title', 'Add New Team Member')



@section('breadcrumb')

<ol class="breadcrumb">

    <li class="breadcrumb-item">

        <a href="#">Dashboard</a>

    </li>

    <li class="breadcrumb-item">

        <a href="{{ route('employee.list') }}">List</a>

    </li>

    <li class="breadcrumb-item">Add New Team Member</li>

</ol>

@endsection



@section('content')

<div class="row">

    <div class="col-12">

        <div class="card">

            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title">Team Member Form</h5>
                    <div class=" text-end pt-0 my-n5">
                        <a href="{{ route('employee.list') }}" class="btn btn-outline-primary btn-sm waves-effect">
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

                <form method="POST" action="{{ route('employee.add') }}" class="prevent-multiple-submits">

                    @csrf

                    <div class="form-body">

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="First Name" required>

                                    <label class="form-label" for="first_name">First Name <span class="text-danger">*</span></label>

                                    @error('first_name')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" required>

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

                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Email" required>

                                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>

                                    @error('email')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Phone" required pattern="^[\d\+\-]*$" 

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

                                    <select class="form-control @error('role') is-invalid @enderror" name="role" id="role" required>

                                        <option value=""> -- select -- </option>

                                        @if($roles)

                                            @foreach($roles as $role)

                                            <option value="{{ $role->id }}" @if(old('role') == $role->id) selected="selected" @endif>{{ $role->name }}</option>

                                            @endforeach

                                        @endif

                                    </select>

                                    <label class="form-label" for="role">Role <span class="text-danger">*</span></label>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <select class="form-control @error('status') is-invalid @enderror" name="status" id="status" required>

                                        <option value=""> -- select -- </option>

                                        <option value="1" @if(old('status') == 1) selected="selected" @endif>Active</option>

                                        <option value="2" @if(old('status') == 2) selected="selected" @endif>Inactive</option>

                                        <option value="3" @if(old('status') == 3) selected="selected" @endif>Blocked</option>

                                    </select>

                                    <label class="form-label" for="status">Status <span class="text-danger">*</span></label>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{ old('password') }}" placeholder="Password">

                                    <label class="form-label" for="password">Password <span class="text-danger">*</span></label>

                                    @error('password')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password" name="confirm_password" value="{{ old('confirm_password') }}" placeholder="Confirm Password">

                                    <label class="form-label" for="confirm_password">Confirm Password <span class="text-danger">*</span></label>

                                    @error('confirm_password')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="date" class="form-control @error('doj') is-invalid @enderror" id="doj" name="doj" value="{{ old('doj') }}" required onclick="this.showPicker();">

                                    <label class="form-label" for="doj">Date of Joining (DOJ) <span class="text-danger">*</span></label>

                                    @error('doj')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                        <input type="text" class="form-control" id="skills" placeholder="Add skills and press enter">

                                          <label class="form-label" for="skills">skills</label>

                                        <div id="skills-tags"></div>

                                    <input type="hidden" name="skills" id="skills-hidden">

                                </div>

                            </div>

                        </div>

                        <fieldset class="mb-4">

                            <legend class="h6">Working Days & Timing</legend>

                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)

                            <div class="row mb-2">

                                <div class="col-md-4">

                                    <input type="text" name="working_days[{{ $day }}][day]" class="form-control" value="{{ $day }}" readonly>

                                </div>

                                <div class="col-md-4">

                                    <input type="time" name="working_days[{{ $day }}][start]" class="form-control" placeholder="Start Time" onclick="this.showPicker();">

                                </div>

                                <div class="col-md-4">

                                    <input type="time" name="working_days[{{ $day }}][end]" class="form-control" placeholder="End Time" onclick="this.showPicker();">

                                </div>

                            </div>

                            @endforeach

                        </fieldset>

                        <fieldset class="mb-4">

                            <!--<legend class="h6">Experience</legend>-->

                            <div id="experience-wrapper">

                                <div class="experience-group"></div>

                            </div>

                            <button type="button" class="btn btn-outline-primary mt-2" id="add-experience">Add Experience</button>

                        </fieldset>

                        <fieldset class="mb-4">

                            <!--<legend class="h6">Certifications</legend>-->

                            <div id="certifications-wrapper"></div>

                            <button type="button" class="btn btn-outline-primary mt-2" id="add-certification">Add Certifications</button>

                        </fieldset>

                    </div>

                     <div class="row">

                        <div class="col-md-6">

                            <div class="form-floating form-floating-outline mb-3">

                                <select id="department" name="department_id" class="form-control select2 @error('reporting_to') is-invalid @enderror">

                                    <option value="">Select Department</option>

                                    @foreach($departments as $department)

                                        <option value="{{ $department->id }}">{{ $department->name }}</option>

                                    @endforeach

                                </select>

                                <label class="form-label" for="department_id">Department</label>

                                @error('department_id')

                                <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-floating form-floating-outline mb-3">

                                <select id="reporting_person" name="reporting_user_id" class="form-control select2 @error('reporting_to') is-invalid @enderror">

                                    <option value="">Select Reporting Person</option>

                                </select>

                                <label class="form-label" for="reporting_user_id">Reporting Person</label>

                                @error('reporting_user_id')

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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">

    $(document).ready(function() {

        $('.select2').select2({

            theme: 'bootstrap-5',

        });

    });

    document.addEventListener("DOMContentLoaded", function() {

        // Initially hide the experience and certification wrappers

        document.getElementById('experience-wrapper').style.display = 'none';

        document.getElementById('certifications-wrapper').style.display = 'none';

        // Show experience section when Add More is clicked

        document.getElementById('add-experience').addEventListener('click', function() {

            let wrapper = document.getElementById('experience-wrapper');

            wrapper.style.display = 'block'; // Show experience section

            let index = document.querySelectorAll('.experience-group').length;

            let newField = `<div class="experience-group">

                <input type="text" name="experience[${index}][designation]" class="form-control mb-2" placeholder="Designation">

                <input type="text" name="experience[${index}][employer]" class="form-control mb-2" placeholder="Employer">

                <input type="text" name="experience[${index}][skills]" class="form-control mb-2" placeholder="Skills (comma separated)">

                <input type="number" name="experience[${index}][years]" class="form-control mb-2" placeholder="Years of Experience">

                <input type="number" name="experience[${index}][months]" class="form-control mb-2" placeholder="Months of Experience">

                <button type="button" class="btn btn-outline-primary mt-2 waves-effect remove-experience">Remove</button>

            </div>`;

            wrapper.insertAdjacentHTML('beforeend', newField);

        });

        // Remove experience fields

        document.addEventListener('click', function(event) {

            if (event.target.classList.contains('remove-experience')) {

                event.target.closest('.experience-group').remove();

                if (document.querySelectorAll('.experience-group').length === 0) {

                    document.getElementById('experience-wrapper').style.display = 'none';

                }

            }

        });

        // Depatment wise user select

        $(document).ready(function () {

            $('#department').on('change', function () {

                var departmentId = $(this).val();

                if (departmentId) {

                    $.ajax({

                        url: '/get-reporting-persons/' + departmentId,

                        type: 'GET',

                        dataType: 'json',

                        success: function (data) {

                            $('#reporting_person').empty().append('<option value="">Select Reporting Person</option>');

                            $.each(data, function (key, user) {

                                var fullName = user.first_name + (user.last_name ? ' ' + user.last_name : '');

                                $('#reporting_person').append('<option value="' + user.id + '">' + fullName + '</option>');

                            });

                        },

                        error: function (xhr) {

                            console.error('Error fetching reporting persons:', xhr.responseText);

                            alert('Failed to load reporting persons.');

                        }

                    });

                } else {

                    var fullName = user.first_name + (user.last_name ? ' ' + user.last_name : '');

                    $('#reporting_person').append('<option value="' + user.id + '">' + fullName + '</option>');

                }

            });

        });

        // Show certification section when Add More is clicked

        document.getElementById('add-certification').addEventListener('click', function() {

            let wrapper = document.getElementById('certifications-wrapper');

            wrapper.style.display = 'block'; // Show certification section

            let index = document.querySelectorAll('.certification-group').length;

            let newField = `<div class="certification-group">

                <input type="text" name="certifications[${index}][course_name]" class="form-control mb-2" placeholder="Course Name">

                <input type="text" name="certifications[${index}][course_from]" class="form-control mb-2" placeholder="Course From">

                <button type="button" class="btn btn-outline-primary mt-2 waves-effect remove-certification">Remove</button>

            </div>`;

            wrapper.insertAdjacentHTML('beforeend', newField);

        });

        // Remove certification fields

        document.addEventListener('click', function(event) {

            if (event.target.classList.contains('remove-certification')) {

                event.target.closest('.certification-group').remove();

                if (document.querySelectorAll('.certification-group').length === 0) {

                    document.getElementById('certifications-wrapper').style.display = 'none';

                }

            }

        });

    });

    document.addEventListener("DOMContentLoaded", function() {

        const skillsInput = document.getElementById("skills");

        const skillsTags = document.getElementById("skills-tags");

        const skillsHiddenInput = document.getElementById("skills-hidden");



        skillsInput.addEventListener("keydown", function(event) {

            if (event.key === "Enter" && skillsInput.value.trim() !== "") {

                event.preventDefault();

                

                const skillValue = skillsInput.value.trim();

                const skillTag = document.createElement("span");

                skillTag.classList.add("badge", "bg-primary", "me-1", "tag");

                skillTag.textContent = skillValue;



                const removeButton = document.createElement("button");

                removeButton.classList.add("btn-close", "btn-sm", "ms-2");

                removeButton.addEventListener("click", function() {

                    skillsTags.removeChild(skillTag);

                    updateHiddenInput();

                });



                skillTag.appendChild(removeButton);

                skillsTags.appendChild(skillTag);



                skillsInput.value = ""; // Clear input field

                updateHiddenInput(); // Update hidden input with tag values

            }

        });

        function updateHiddenInput() {

            const tags = [...skillsTags.getElementsByClassName("tag")];

            const skillsArray = tags.map(tag => tag.textContent.trim());

            skillsHiddenInput.value = skillsArray.join(",");

        }

    });

</script>

@endsection

