@extends('app')

@section('title', 'Dasboard')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('hosting_dashboard') }}">Hosting Management Dashboard</a>
    </li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-8 mb-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Statistics</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Total Users -->
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-primary rounded shadow">
                                    <i class="tf-icons mdi mdi-account mdi-24px"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="small mb-1">Total Users</div>
                                <h5 class="mb-0">{{ $totalUsersCount }}</h5>
                            </div>
                        </div>
                    </div>
                    <!-- Active Users -->
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-success rounded shadow">
                                    <i class="tf-icons mdi mdi-check-circle mdi-24px"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="small mb-1">Active Plans</div>
                                <h5 class="mb-0">{{ $activePlanCount }}</h5>
                            </div>
                        </div>
                    </div>
                    <!-- Purchased Plan Users -->
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-warning rounded shadow">
                                    <i class="tf-icons mdi mdi-close-circle-outline mdi-24px"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="small mb-1">Inactive Plans</div>
                                <h5 class="mb-0">{{ $inactivePlanCount }}</h5>
                            </div>
                        </div>
                    </div>
                    <!-- Renew Plans -->
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-info rounded shadow">
                                    <i class="tf-icons mdi mdi-refresh mdi-24px"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="small mb-1">Renew Plans</div>
                                <h5 class="mb-0">{{ $renewPlanCount }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Show Inactive Purchased Plans -->
@if($inactivePlans->count() > 0)
<div class="row mt-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Inactive Purchased Plans</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Provider</th>
                                <th>Package Details</th>
                                <th>Purchase Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inactivePlans as $plan)
                            <tr>
                                <td>{{ $plan->client->first_name }} {{ $plan->client->last_name }}</td>
                                <td>{{ $plan->hostingProvider->provider_name }}</td>
                                <td>{{ $plan->plan_details }}</td>
                                <td>{{ $plan->date_of_purchase }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm activate-plan" data-id="{{ $plan->id }}">
                                        Activate Plan
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
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
<style type="text/css">
    .highcharts-container {
        font-family: var(--bs-body-font-family);
    }
    .highcharts-title,
    .highcharts-subtitle,
    .highcharts-yaxis .highcharts-axis-title,
    .highcharts-caption,
    .highcharts-label,
    .highcharts-label text {
        font-family: var(--bs-body-font-family);
        font-weight: normal !important;
    }
</style>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        // Handle Activate Plan Button click for Dashboard
        $('.activate-plan').on('click', function() {
            var hostingId = $(this).data('id');
            console.log('Activating Hosting ID: ' + hostingId);  // Debugging line
            $('#activationModal').data('hostingId', hostingId).modal('show');  // Show the modal and store the hosting ID
        });

        // When the user confirms activation
        $('#confirm-activation').on('click', function() {
            var hostingId = $('#activationModal').data('hostingId');
            var months = $('#plan-duration').val();
            
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
</script>
@endsection