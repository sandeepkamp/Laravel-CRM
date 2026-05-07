@extends('app')

@section('title', 'Edit Team Member')

@section('breadcrumb')

<ol class="breadcrumb">

    <li class="breadcrumb-item">

        <a href="{{ route('dashboard') }}">Dashboard</a>

    </li>

    <li class="breadcrumb-item">

        <a href="{{ route('employee.list') }}">List</a>

    </li>

    <li class="breadcrumb-item">Edit Team Member</li>

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

                <form method="POST" action="{{ route('employee.update', $user->id) }}">

                    @csrf

                    @method('PUT')

                    <div class="form-body">

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $user->first_name) }}" placeholder="First Name" required>

                                    <label>First Name <span class="text-danger">*</span></label>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $user->last_name) }}" placeholder="Last Name" required>

                                    <label>Last Name <span class="text-danger">*</span></label>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" placeholder="Email" required>

                                    <label>Email <span class="text-danger">*</span></label>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Phone" required>

                                    <label>Phone <span class="text-danger">*</span></label>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    @if(auth()->user()->role == 1 || auth()->user()->role == 3)

                                        <select class="form-control" name="role" required>

                                            <option value=""> -- select -- </option>

                                            @foreach($roles as $role)

                                                <option value="{{ $role->id }}" @if(old('role', $user->role) == $role->id) selected @endif>

                                                    {{ $role->name }}

                                                </option>

                                            @endforeach

                                        </select>

                                    @else

                                        <input type="text" class="form-control" value="{{ $roles->firstWhere('id', $user->role)->name ?? 'Unknown' }}" readonly>

                                        <input type="hidden" name="role" value="{{ $user->role }}">

                                    @endif

                                    <label>Role <span class="text-danger">*</span></label>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    @if(auth()->user()->role == 1 || auth()->user()->role == 3)

                                        <select class="form-control" name="status" required>

                                            <option value="1" @if(old('status', $user->status) == 1) selected @endif>Active</option>

                                            <option value="2" @if(old('status', $user->status) == 2) selected @endif>Inactive</option>

                                            <option value="3" @if(old('status', $user->status) == 3) selected @endif>Blocked</option>

                                        </select>

                                    @else

                                        @php

                                            $statuses = [1 => 'Active', 2 => 'Inactive', 3 => 'Blocked'];

                                        @endphp

                                        <input type="text" class="form-control" value="{{ $statuses[$user->status] ?? 'Unknown' }}" readonly>

                                        <input type="hidden" name="status" value="{{ $user->status }}">

                                    @endif

                                    <label>Status <span class="text-danger">*</span></label>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="password" class="form-control" name="password" placeholder="Password">

                                    <label>Password</label>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password">

                                    <label>Confirm Password</label>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    @if(auth()->user()->role == 1 || auth()->user()->role == 3)

                                        <input type="date" class="form-control @error('doj') is-invalid @enderror" id="doj" name="doj" value="{{ old('doj', $userMeta['doj'] ?? '') }}" required onclick="this.showPicker();">

                                    @else

                                        <input type="date" class="form-control" value="{{ old('doj', $userMeta['doj'] ?? '') }}" readonly >

                                        <input type="hidden" name="doj" value="{{ $userMeta['doj'] ?? '' }}">

                                    @endif

                                    <label class="form-label" for="doj">Date of Joining (DOJ) <span class="text-danger">*</span></label>

                                    @error('doj')

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                </div>

                            </div>

                            @php

                                $skills = $userMeta['skills'] ?? '';

                                if (is_string($skills)) {

                                    $skillsArray = explode(',', $skills); // Split string into array

                                } else {

                                    $skillsArray = json_decode($skills, true) ?? [];

                                }

                            @endphp

                            <div class="col-md-6">

                                <div class="form-floating form-floating-outline mb-3">

                                    <input type="text" class="form-control" id="skills" placeholder="Add skills and press enter">

                                    <label class="form-label" for="skills">Skills</label>

                                    <div id="skills-tags">

                                        @if(!empty($skillsArray))

                                            @foreach($skillsArray as $skill)

                                                <span class="badge badge-primary">{{ trim($skill) }}</span>

                                            @endforeach

                                        @endif

                                    </div>

                                    <input type="hidden" name="skills" id="skills-hidden" value="{{ old('skills', implode(',', $skillsArray)) }}">

                                </div>

                            </div>

                        </div>

                        <fieldset class="mb-4">

                            <legend class="h6">Experience</legend>

                            <div id="experience-wrapper">

                                @php

                                    $experienceData = $userMeta['experience'] ?? [];

                                    if (is_string($experienceData)) {

                                        $experienceData = json_decode($experienceData, true) ?? [];

                                    }

                                @endphp
                                @foreach($experienceData as $index => $exp)
                                    <div class="experience-group">

                                        <input type="text" name="experience[{{ $index }}][designation]" class="form-control mb-2" value="{{ $exp['designation'] ?? '' }}" placeholder="Designation">

                                        <input type="text" name="experience[{{ $index }}][employer]" class="form-control mb-2" value="{{ $exp['employer'] ?? '' }}" placeholder="Employer">

                                        <input type="text" name="experience[{{ $index }}][skills]" class="form-control mb-2" value="{{ $exp['skills'] ?? '' }}" placeholder="Skills (comma separated)">

                                        <input type="number" name="experience[{{ $index }}][years]" class="form-control mb-2" value="{{ $exp['years'] ?? '' }}" placeholder="Years of Experience">

                                        <input type="number" name="experience[{{ $index }}][months]" class="form-control mb-2" value="{{ $exp['months'] ?? '' }}" placeholder="Months of Experience">

                                        <button type="button" class="btn btn-outline-primary mt-2 remove-experience">Remove</button>

                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-outline-primary mt-2" id="add-experience">Add</button>

                        </fieldset>

                        <fieldset class="mb-4">

                            <legend class="h6">Certifications</legend>

                            <div id="certifications-wrapper">

                                @if(!empty($userMeta['certifications']))

                                    @foreach($userMeta['certifications'] as $index => $cert)

                                        <div class="certification-group">

                                            <input type="text" name="certifications[{{ $index }}][course_name]" class="form-control mb-2" value="{{ $cert['course_name'] }}" placeholder="Course Name">

                                            <input type="text" name="certifications[{{ $index }}][course_from]" class="form-control mb-2" value="{{ $cert['course_from'] }}" placeholder="Course From">

                                            <button type="button" class="btn btn-outline-primary mt-2 remove-certification">Remove</button>

                                        </div>

                                    @endforeach

                                @endif

                            </div>

                            <button type="button" class="btn btn-outline-primary mt-2" id="add-certification">Add</button>

                        </fieldset>

                        <fieldset class="mb-4">

                            <legend class="h6">Working Days & Timing</legend>

                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)

                            <div class="row mb-2">

                                <div class="col-md-4">

                                    <input type="text" name="working_days[{{ $day }}][day]" class="form-control" value="{{ $day }}" readonly>

                                </div>

                                <div class="col-md-4">

                                    <input type="time" name="working_days[{{ $day }}][start]" class="form-control" value="{{ $userMeta['working_days'][$day]['start'] ?? '' }}" onclick="this.showPicker();">

                                </div>

                                <div class="col-md-4">

                                    <input type="time" name="working_days[{{ $day }}][end]" class="form-control" value="{{ $userMeta['working_days'][$day]['end'] ?? '' }}" onclick="this.showPicker();">

                                </div>

                            </div>

                            @endforeach

                        </fieldset>

                    </div>

                    @php

                        $canEdit = auth()->user()->role == 1 || auth()->user()->role == 3;

                    @endphp

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-floating form-floating-outline mb-3">

                                <select id="department" name="department_id" class="form-control select2" {{ !$canEdit ? 'disabled' : '' }}>

                                    <option value="">Select Department</option>

                                    @foreach($departments as $department)

                                        <option value="{{ $department->id }}" {{ $user->department_id == $department->id ? 'selected' : '' }}>

                                            {{ $department->name }}

                                        </option>

                                    @endforeach

                                </select>

                                <label class="form-label" for="department">Department</label>

                                @error('department_id')

                                <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                            </div>

                        </div>
                        
                        <div class="col-md-6">

                            <div class="form-floating form-floating-outline mb-3">

                                <select id="reporting_person" name="reporting_to" class="form-control select2" {{ !$canEdit ? 'disabled' : '' }}>

                                    <option value="">Select Reporting Person</option>

                                    @foreach($reportingPersons as $person)

                                        <option value="{{ $person->id }}" {{ $assignedReportingPersonId == $person->id ? 'selected' : '' }}>

                                            {{ $person->first_name }}{{ $person->last_name ? ' ' . $person->last_name : '' }}

                                        </option>

                                    @endforeach

                                </select>

                                <label class="form-label" for="reporting_person">Reporting Person</label>

                                @error('reporting_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                            </div>

                        </div>

                    </div>

                    <div class="form-actions">

                        <div class="text-end">

                            <button type="submit" class="btn btn-primary">UPDATE</button>

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

<script>

    $(document).ready(function() {

        $('.select2').select2({

            theme: 'bootstrap-5',

        });

    });

    document.addEventListener("DOMContentLoaded", function() {

        document.getElementById('add-experience').addEventListener('click', function() {

            let wrapper = document.getElementById('experience-wrapper');

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

        document.getElementById('add-certification').addEventListener('click', function() {

            let wrapper = document.getElementById('certifications-wrapper');

            let index = document.querySelectorAll('.certification-group').length;

            let newField = `<div class="certification-group">

                <input type="text" name="certifications[${index}][course_name]" class="form-control mb-2" placeholder="Course Name">

                <input type="text" name="certifications[${index}][course_from]" class="form-control mb-2" placeholder="Course From">

                <button type="button" class="btn btn-outline-primary mt-2 remove-certification">Remove</button>

            </div>`;

            wrapper.insertAdjacentHTML('beforeend', newField);

        });

        document.addEventListener('click', function(event) {

            if (event.target.classList.contains('remove-experience')) {

                event.target.closest('.experience-group').remove();

            }

            if (event.target.classList.contains('remove-certification')) {

                event.target.closest('.certification-group').remove();

            }

        });

    });

    // Depatment wise user select

    $(document).ready(function () {

        let departmentId = $('#department').val();

        let selectedReportingId = '{{ $assignedReportingPersonId }}';

        if (departmentId) {

            $.ajax({

                url: '/get-reporting-persons/' + departmentId,

                type: 'GET',

                dataType: 'json',

                success: function (data) {

                    $('#reporting_person').empty().append('<option value="">Select Reporting Person</option>');

                    $.each(data, function (key, user) {

                        let selected = (user.id == selectedReportingId) ? 'selected' : '';

                        let fullName = user.first_name + (user.last_name ? ' ' + user.last_name : '');

                        $('#reporting_person').append('<option value="' + user.id + '" ' + selected + '>' + fullName + '</option>');

                    });

                },

                error: function (xhr) {

                    console.error('Error fetching reporting persons:', xhr.responseText);

                }

            });

        }

        // On department change

        $('#department').on('change', function () {

            var newDeptId = $(this).val();

            $('#reporting_person').empty().append('<option value="">Select Reporting Person</option>');

            if (newDeptId) {

                $.ajax({

                    url: '/get-reporting-persons/' + newDeptId,

                    type: 'GET',

                    dataType: 'json',

                    success: function (data) {

                        $.each(data, function (key, user) {

                            let fullName = user.first_name + (user.last_name ? ' ' + user.last_name : '');

                            $('#reporting_person').append('<option value="' + user.id + '">' + fullName + '</option>');

                        });

                    },

                    error: function (xhr) {

                        console.error('Error:', xhr.responseText);

                    }

                });

            }

        });

    });

    document.addEventListener("DOMContentLoaded", function() {

        const skillsInput = document.getElementById("skills");

        const skillsTags = document.getElementById("skills-tags");

        const skillsHiddenInput = document.getElementById("skills-hidden");

        // Function to update the hidden input

        function updateHiddenInput() {

            const tags = [...skillsTags.getElementsByClassName("tag")];

            const skillsArray = tags.map(tag => tag.textContent.trim());

            skillsHiddenInput.value = skillsArray.join(",");

        }

        const preExistingSkills = skillsHiddenInput.value

            .replace(/^"|"$/g, '')

            .split(",") 

            .map(skill => skill.trim()); 

        preExistingSkills.forEach(skill => {

            if (skill !== "") {

                const skillTag = document.createElement("span");

                skillTag.classList.add("badge", "bg-primary", "me-1", "tag");

                skillTag.textContent = skill;

    

                const removeButton = document.createElement("button");

                removeButton.classList.add("btn-close", "btn-sm", "ms-2");

                removeButton.addEventListener("click", function() {

                    skillsTags.removeChild(skillTag);

                    updateHiddenInput();

                });

                skillTag.appendChild(removeButton);

                skillsTags.appendChild(skillTag);

            }

        });

        // Handle adding new skills

        skillsInput.addEventListener("keydown", function(event) {

            if (event.key === "Enter" && skillsInput.value.trim() !== "") {

                event.preventDefault();

                const skillValue = skillsInput.value.trim();

                // Ensure no quotes are added

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

                skillsInput.value = ""; // Clear the input field

                updateHiddenInput(); // Update the hidden input with tag values

            }

        });

    });

</script>

@endsection