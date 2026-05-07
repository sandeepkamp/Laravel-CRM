@extends('app')

@section('title', 'Request Leave')

@section('breadcrumb')
<ol class="breadcrumb">
    <!--<li class="breadcrumb-item">-->
    <!--    <a href="{{ route('dashboard') }}">Dashboard</a>-->
    <!--</li>-->
    <li class="breadcrumb-item">
        <a href="{{ route('leave.list') }}">Leave Dashboard</a>
    </li>
    <li class="breadcrumb-item">Submit Leave Request</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
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
                <form method="POST" action="{{ route('leave.submit_request') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-control" id="user_id" name="user_id" required {{ auth()->user()->role != 1 && auth()->user()->role != 3  ? 'readonly' : '' }}>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ auth()->id() == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->first_name . ' ' . $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label class="form-label" for="user_id">Employee <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="datetime-local" class="form-control" id="leave_from" name="leave_from" required>
                                    <label class="form-label" for="leave_from">Leave From <span class="text-danger">*</span></label>
                                     @if($errors->has('leave_from'))
                                        <div class="text-danger">{{ $errors->first('leave_from') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="datetime-local" class="form-control" id="leave_to" name="leave_to" required>
                                    <label class="form-label" for="leave_to">Leave To <span class="text-danger">*</span></label>
                                    @if($errors->has('leave_to'))
                                        <div class="text-danger">{{ $errors->first('leave_to') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                    <label class="form-label" for="description">Description <span class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <!--<div class="form-group mb-3">-->
                                <!--    <label class="form-label" for="attachment">Upload Document (PDF/Images)</label>-->
                                <!--    <input type="file" class="form-control" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png">-->
                                <!--</div>-->
                                <div class="form-group mb-3">
    <label class="form-label" for="attachment">Upload Documents (PDF/Images)</label>
    <input type="file" class="form-control" id="attachment" name="attachment[]" accept=".pdf,.jpg,.jpeg,.png" multiple>
    <div id="file-preview"></div>
</div>

                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the current date
        let currentDate = new Date().toISOString().split("T")[0];

        // Set the min attribute for both date fields to the current date
        document.getElementById('leave_from').setAttribute('min', currentDate);
        document.getElementById('leave_to').setAttribute('min', currentDate);
    });
    // File upload Preview js
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('attachment');
        const filePreview = document.getElementById('file-preview');
        let allFiles = []; // Store all selected files here
    
        fileInput.addEventListener('change', function(event) {
            const newFiles = Array.from(event.target.files);
    
            // Add new files to the allFiles array
            allFiles = [...allFiles, ...newFiles];
            
            // Remove duplicate files (if the user selects the same file again)
            allFiles = allFiles.filter((file, index, self) =>
                index === self.findIndex((f) => f.name === file.name)
            );
    
            // Clear previous previews
            filePreview.innerHTML = '';
    
            // Display all selected files
            allFiles.forEach((file, index) => {
                const fileDiv = document.createElement('div');
                fileDiv.classList.add('alert', 'alert-secondary', 'd-flex', 'justify-content-between', 'align-items-center', 'mb-2');
                fileDiv.innerHTML = `
                    <span>${file.name}</span>
                    <button type="button" class="btn-close" data-index="${index}" aria-label="Remove"></button>
                `;
                filePreview.appendChild(fileDiv);
            });
    
            // Add event listeners for removal
            document.querySelectorAll('#file-preview .btn-close').forEach(button => {
                button.addEventListener('click', function() {
                    const indexToRemove = parseInt(this.getAttribute('data-index'));
                    
                    // Remove the file from the allFiles array
                    allFiles.splice(indexToRemove, 1);
    
                    // Update the input files
                    const dt = new DataTransfer();
                    allFiles.forEach(file => dt.items.add(file));
                    fileInput.files = dt.files;
    
                    // Refresh the preview
                    this.parentElement.remove();
                });
            });
        });
    });
</script>
