@extends('app')

@section('title', 'Hosting List')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('hosting_dashboard') }}">Hosting Dashboard</a>
    </li>
    <li class="breadcrumb-item active">List</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('hosting.new') }}" class="btn btn-outline-primary btn-sm waves-effect float-end">
                    <span class="tf-icons mdi mdi-plus me-1"></span>
                    Add Hosting
                </a>
            </div>
            <div class="card-body">
                @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong><i class="tf-icons mdi mdi-check-circle-outline"></i> Success -</strong>
                    {{ session()->get('success') }}
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table hosting-table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Provider</th>
                                <th>Package</th>
                                <th>Purchase Date</th>
                                <th>Activation Date</th>
                                <th>Expiry Date</th>
                                <th>Price</th>
                                <th>Activate Plan</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <!--<tbody>-->
                        <!--    @foreach ($hostingDetails as $hosting)-->
                        <!--    <tr class="@if ($hosting->expiry_date && $hosting->expiry_date < now()) table-danger @endif">-->
                        <!--        <td>{{ $hosting->client->first_name }} {{ $hosting->client->last_name }}</td>-->
                        <!--        <td>{{ $hosting->hostingProvider->provider_name }}</td>-->
                        <!--        <td>{{ $hosting->plan_details }}</td>-->
                        <!--        <td>{{ $hosting->date_of_purchase }}</td>-->
                        <!--        <td>{{ $hosting->activation_date ?? 'N/A' }}</td>-->
                        <!--        <td>{{ $hosting->expiry_date ?? 'N/A' }}</td>-->
                        <!--        <td>£{{ number_format($hosting->cost, 2) }}</td>-->
                        <!--        <td class="status-label">-->
                        <!--            @if ($hosting->status == 0)-->
                        <!--                <button class="btn btn-warning btn-sm activate-plan" data-id="{{ $hosting->id }}">-->
                        <!--                    Activate Plan-->
                        <!--                </button>-->
                        <!--            @elseif ($hosting->expiry_date && $hosting->expiry_date < now())-->
                        <!--                <button class="btn btn-primary btn-sm renew-plan" data-id="{{ $hosting->id }}">-->
                        <!--                    Renew Plan-->
                        <!--                </button>-->
                        <!--            @else-->
                        <!--                <span class="btn btn-success btn-sm">Plan Active</span>-->
                        <!--            @endif-->
                        <!--        </td>-->
                        <!--        <td class="text-center">-->
                        <!--            <form method="POST" action="{{ route('hosting.delete', $hosting->id) }}" id="delete-form-{{ $hosting->id }}" style="display: none;">-->
                        <!--                @csrf-->
                        <!--                @method('DELETE')-->
                        <!--            </form>-->
                        <!--            <div class="dropdown">-->
                        <!--                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">-->
                        <!--                    <i class="mdi mdi-dots-vertical"></i>-->
                        <!--                </button>-->
                        <!--                <div class="dropdown-menu">-->
                        <!--                    <a class="dropdown-item" href="{{ route('hosting.edit', $hosting->id) }}">-->
                        <!--                        <i class="mdi mdi-pencil-outline me-1"></i>-->
                        <!--                        Edit-->
                        <!--                    </a>-->
                        <!--                    <a class="dropdown-item" href="javascript:void(0);" onclick="confirmDelete({{ $hosting->id }})">-->
                        <!--                        <i class="mdi mdi-trash-can-outline me-1"></i>-->
                        <!--                        Delete-->
                        <!--                    </a>-->
                        <!--                </div>-->
                        <!--            </div>-->
                        <!--        </td>-->
                        <!--    </tr>-->
                        <!--    @endforeach-->
                        <!--</tbody>-->
                        <tbody>
                            @foreach ($hostingDetails as $hosting)
                            <tr class="@if ($hosting->expiry_date && $hosting->expiry_date < now()) table-danger @endif">
                                <td>{{ $hosting->client->first_name }} {{ $hosting->client->last_name }}</td>
                                <td>{{ $hosting->hostingProvider->provider_name }}</td>
                                <td>
                                    <a href="javascript:void(0);" class="text-primary" data-bs-toggle="modal" data-bs-target="#packageDetailsModal{{ $hosting->id }}">
                                        Show Details
                                    </a>
                                </td>
                                <td>{{ $hosting->date_of_purchase }}</td>
                                <td>{{ $hosting->activation_date ?? 'N/A' }}</td>
                                <td>{{ $hosting->expiry_date ?? 'N/A' }}</td>
                                <td>£{{ number_format($hosting->cost, 2) }}</td>
                                <td class="status-label">
                                    @if ($hosting->status == 0)
                                        <button class="btn btn-warning btn-sm activate-plan" data-id="{{ $hosting->id }}">
                                            Activate Plan
                                        </button>
                                    @elseif ($hosting->expiry_date && $hosting->expiry_date < now())
                                        <button class="btn btn-primary btn-sm renew-plan" data-id="{{ $hosting->id }}">
                                            Renew Plan
                                        </button>
                                    @else
                                        <span class="btn btn-success btn-sm">Plan Active</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('hosting.delete', $hosting->id) }}" id="delete-form-{{ $hosting->id }}" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('hosting.edit', $hosting->id) }}">
                                                <i class="mdi mdi-pencil-outline me-1"></i>
                                                Edit
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="confirmDelete({{ $hosting->id }})">
                                                <i class="mdi mdi-trash-can-outline me-1"></i>
                                                Delete
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <!-- Modal for Package Details -->
                            <div class="modal fade" id="packageDetailsModal{{ $hosting->id }}" tabindex="-1" aria-labelledby="packageDetailsLabel{{ $hosting->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="packageDetailsLabel{{ $hosting->id }}">Package Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>{{ $hosting->plan_details }}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal for plan activation -->
<div class="modal fade" id="activationModal" tabindex="-1" aria-labelledby="activationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activationModalLabel">Activate Hosting Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="plan-duration" class="form-label">Enter Number of Months:</label>
                    <input type="number" class="form-control" id="plan-duration" min="1" placeholder="Months">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-activation">Activate Plan</button>
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
<script type="text/javascript">
    $(document).ready(function() {
        // Handle Activate Plan Button click
        $('.activate-plan').on('click', function() {
            var hostingId = $(this).data('id');
            console.log('Activating Hosting ID: ' + hostingId);  // Debugging line
            $('#activationModal').data('hostingId', hostingId).modal('show');  // Show the modal and store the hosting ID
        });
    
        // When the user confirms activation
        $('#confirm-activation').on('click', function() {
            var hostingId = $('#activationModal').data('hostingId');
            var months = $('#plan-duration').val();
            // alert(1); return false;
            // Validate the input
            if (months <= 0) {
                alert("Please enter a valid number of months.");
                return;
            }
    
            console.log('Months: ' + months);  // Debugging line
    
            // Send AJAX request to activate the plan
            $.ajax({
                url: '/hostingmanagement/hosting/activate/' + hostingId,  // Include the prefix 'hostingmanagement'
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    hosting_id: hostingId,
                    months: months  // Send the months as part of the request
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $('button[data-id="' + hostingId + '"]').replaceWith('<span class="btn btn-success btn-sm">Plan Active</span>');
                    }
                    $('#activationModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    console.log("Error:", error);
                }
            });
        });
    });
    $(document).ready(function () {
        // Handle Renew Plan Button click
        $('.renew-plan').on('click', function () {
            var hostingId = $(this).data('id');
            $('#activationModal').data('hostingId', hostingId).modal('show'); // Reuse the activation modal
        });
    
        // Handle Activate or Renew submission
        $('#confirm-activation').on('click', function () {
            var hostingId = $('#activationModal').data('hostingId');
            var months = $('#plan-duration').val();
    
            if (months <= 0) {
                alert("Please enter a valid number of months.");
                return;
            }
    
            $.ajax({
                url: '/hostingmanagement/hosting/activate/' + hostingId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    months: months
                },
                success: function (response) {
                    if (response.status === 'success') {
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.log("Error:", error);
                }
            });
        });
    });
    
    $(document).ready(function () {
        $('.hosting-table').DataTable({
            dom: "<'row mb-5'<'col-md-12'B>>" +
                 "<'row'<'col-md-6'l><'col-md-6'f>>" +
                 "<'row'<'col-md-12'tr>>" +
                 "<'row'<'col-md-6'i><'col-md-6'p>>",
            buttons: [
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }
            ],
            language: {
                paginate: {
                    next: '&#8594;',
                    previous: '&#8592;'
                }
            },
            columnDefs: [
                { targets: 8, orderable: false } // Disables sorting on the "Actions" column (index 8)
            ]
        });
    });
</script>
<script>
    function confirmDelete(hostingId) {
        // Show confirmation dialog
        if (confirm("Are you sure you want to delete this hosting detail?")) {
            // If confirmed, submit the form
            document.getElementById('delete-form-' + hostingId).submit();
        }
    }
</script>

@endsection
