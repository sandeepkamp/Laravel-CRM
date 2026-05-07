@extends('app')



@section('title', 'Leave Management')

@push('style')
    <style>
        #fullDescription{word-wrap: break-word;}
    </style>
@endpush

@section('breadcrumb')

<ol class="breadcrumb">

    <li class="breadcrumb-item">

        <a href="#">Leave Dashboard</a>

    </li>

    <li class="breadcrumb-item">Leave List</li>

</ol>

@endsection

@section('content')

<div class="row">

    <div class="col-12">

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

        <div class="card">

            <div class="card-header">

                <a href="{{ route('leave.request') }}" class="btn btn-outline-primary btn-sm waves-effect float-end">

                    <span class="tf-icons mdi mdi-plus me-1"></span>

                    Add Leave Request

                </a>

            </div>

            <div class="card-body">

                {{ $dataTable->table(['class' => 'table leaves-table table-striped'], true) }}

            </div>

        </div>

    </div>

</div>

<!-- Upload Document Modal -->

<div class="modal fade" id="uploadDocModal" tabindex="-1" aria-labelledby="uploadDocModalLabel" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="uploadDocModalLabel">Upload Document</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <form id="uploadDocForm" enctype="multipart/form-data">

                    @csrf

                    <input type="hidden" name="leave_id" id="leaveId">

                    <div class="mb-3">

                        <label for="documentFile" class="form-label">Select File</label>

                        <input type="file" class="form-control" name="attachment" id="documentFile" required>

                    </div>

                    <button type="submit" class="btn btn-primary">Upload</button>

                </form>

            </div>

        </div>

    </div>

</div>

<!-- Full Description Modal -->

<div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="descriptionModalLabel">Reason For Leave</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <p id="fullDescription"></p>

            </div>

        </div>

    </div>

</div>

<!-- Document View Modal -->

<div class="modal fade" id="viewDocModal" tabindex="-1" aria-labelledby="viewDocModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="viewDocModalLabel">Document View</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <iframe id="docViewer" src="" width="100%" height="600px" style="border:none;"></iframe>

            </div>

        </div>

    </div>

</div>

@endsection

@section('style')

<link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/datatables.min.css') }}">

@endsection

@section('script')

<script src="{{ url('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>

<script>

    $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        }

    });

    $(document).ready(function () {

        function handleLeaveAction(action, leaveId) {

            $.ajax({

                url: "/leave/request/" + action + "/" + leaveId,

                type: "POST",

                success: function (response) {

                    if (response.status === 'success') {

                        // Show SweetAlert popup

                        Swal.fire({

                            icon: 'success',

                            title: 'Success!',

                            text: response.message,

                            confirmButtonColor: '#3085d6',

                            confirmButtonText: 'OK'

                        }).then(() => {

                            location.reload(); // Reload page after confirmation

                        });

                    } else {

                        Swal.fire({

                            icon: 'error',

                            title: 'Error!',

                            text: response.message

                        });

                    }

                },

                error: function (xhr) {

                    Swal.fire({

                        icon: 'error',

                        title: 'Oops!',

                        text: 'Something went wrong. Please try again later.'

                    });

                }

            });

        }

        // Approve Leave , Reject , On Hold

        $('body').on('click', '.approve-btn, .reject-btn, .hold-btn', function () {

            let leaveId = $(this).data('id');

            let action = $(this).hasClass('approve-btn') ? "approve" :

                         $(this).hasClass('reject-btn') ? "reject" : "hold";

            let actionText = action.charAt(0).toUpperCase() + action.slice(1);

            

            Swal.fire({

                title: `Are you sure?`,

                text: `You are about to ${actionText.toLowerCase()} this leave request.`,

                icon: 'warning',

                showCancelButton: true,

                confirmButtonColor: '#3085d6',

                cancelButtonColor: '#d33',

                confirmButtonText: `Yes, ${actionText.toLowerCase()} it!`

            }).then((result) => {

                if (result.isConfirmed) {

                    handleLeaveAction(action, leaveId);

                }

            });

        });



    });

    

    $(document).ready(function () {

        // Upload Document

        $('body').on('click', '.upload-doc-btn', function () {

            let leaveId = $(this).data('id');

            $('#leaveId').val(leaveId);

            $('#uploadDocModal').modal('show');

        });



        // Handle Upload Form Submission

        $('#uploadDocForm').submit(function (e) {

            e.preventDefault();

            let formData = new FormData(this);



            $.ajax({

                url: "{{ route('leave.upload.attachment') }}",

                type: "POST",

                data: formData,

                contentType: false,

                processData: false,

                success: function (response) {

                    if (response.status === 'success') {

                        $('#uploadDocModal').modal('hide');

                        Swal.fire('Success!', response.message, 'success').then(() => {

                            location.reload();

                        });

                    } else {

                        Swal.fire('Error!', response.message, 'error');

                    }

                },

                error: function () {

                    Swal.fire('Error!', 'Something went wrong.', 'error');

                }

            });

        });

    });

    // Description Modal Pop Up

    $(document).ready(function () {

        $('body').on('click', '.read-more', function () {

            var fullDescription = $(this).data('description');

            $('#fullDescription').html(fullDescription);  // Set the full description in the modal

            $('#descriptionModal').modal('show');  // Show the modal

        });

    });

    $(document).on('click', '.delete-btn', function() {

        var leaveId = $(this).data('id');

        Swal.fire({

            title: 'Are you sure?',

            text: 'You won\'t be able to revert this!',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#3085d6',

            cancelButtonColor: '#d33',

            confirmButtonText: 'Yes, delete it!'

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({

                    url: '{{ route("leave.destroy", ":id") }}'.replace(':id', leaveId),

                    type: 'DELETE',

                    data: {

                        _token: '{{ csrf_token() }}'

                    },

                    success: function(response) {

                        if (response.status === 'success') {

                            Swal.fire(

                                'Deleted!',

                                response.message,

                                'success'

                            );

                            window.LaravelDataTables['leaves-table'].ajax.reload();

                        } else {

                            Swal.fire(

                                'Error!',

                                response.message,

                                'error'

                            );

                        }

                    },

                    error: function(xhr) {

                        Swal.fire(

                            'Oops!',

                            'Something went wrong. Please try again.',

                            'error'

                        );

                    }

                });

            }

        });

    });

    $(document).ready(function () {
    $('body').on('click', '.view-doc-btn', function () {

        let docUrl = $(this).data('url');

        $('#docViewer').attr('src', docUrl);

        $('#viewDocModal').modal('show');

    });

});



</script>

@endsection

@push('script')

    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

@endpush

