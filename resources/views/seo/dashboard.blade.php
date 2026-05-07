@extends('app')

@section('title', 'SEO Dashboard')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('seo.dashboard') }}">SEO Management Dashboard</a>
    </li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12 mb-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">SEO Statistics</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Total Clients -->
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-primary rounded shadow">
                                    <i class="tf-icons mdi mdi-account mdi-24px"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="small mb-1">Total Customer</div>
                                <h5 class="mb-0">{{ $totalClients }}</h5>
                            </div>
                        </div>
                    </div>
                    <!-- Active Plans -->
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-success rounded shadow">
                                    <i class="tf-icons mdi mdi-check-circle mdi-24px"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="small mb-1">Active Plans</div>
                                <h5 class="mb-0">{{ $activePlans }}</h5>
                            </div>
                        </div>
                    </div>
                    <!-- Inactive Plans -->
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-warning rounded shadow">
                                    <i class="tf-icons mdi mdi-close-circle-outline mdi-24px"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="small mb-1">Inactive Plans</div>
                                <h5 class="mb-0">{{ $inactivePlans }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <!-- Renewals Soon -->
                        <a href="{{ route('seo.upcoming-renewals') }}" class="col-md-3 col-6 text-decoration-none text-dark">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-info rounded shadow">
                                        <i class="tf-icons mdi mdi-calendar-clock mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Renewing Soon</div>
                                    <h5 class="mb-0">{{ $renewSoon }}</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($overduePlans->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Overdue SEO Plans</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Website</th>
                                    <th>Package</th>
                                    <th>Start Date</th>
                                    <th>Renewal Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overduePlans as $plan)
                                <tr>
                                    <td>{{ $plan->client->first_name }} {{ $plan->client->last_name }}</td>
                                    <td><a href="{{ $plan->website_url }}" target="_blank">{{ $plan->website_url }}</a></td>
                                    <td>{{ ucfirst($plan->seo_package) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($plan->start_date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($plan->renewal_date)->format('d M Y') }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#reactivateModal{{ $plan->id }}">
                                            Renew
                                        </button>
                                    </td>
                                </tr>
    
                               <!-- Reactivate Modal -->
                                <div class="modal fade" id="reactivateModal{{ $plan->id }}" tabindex="-1" aria-labelledby="reactivateModalLabel{{ $plan->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('seo.reactivate', $plan->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="reactivateModalLabel{{ $plan->id }}">Renew SEO Plan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                                            <input type="date" name="start_date" class="form-control" onclick="this.showPicker();" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="renewal_date" class="form-label">Renewal Date <span class="text-danger">*</span></label>
                                                            <input type="date" name="renewal_date" class="form-control" onclick="this.showPicker();" required>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label for="seo_package" class="form-label">SEO Package <span class="text-danger">*</span></label>
                                                            <select name="seo_package" class="form-control" required>
                                                                @foreach(Enum::SEO_PACKAGES as $key=> $value)
                                                                    <option value="{{$key}}" {{ old('seo_package', $plan->seo_package) == $key ? 'selected' : '' }}>{{$value['label']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Confirm Renew</button>
                                                </div>
                                            </div>
                                        </form>
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
    @endif
</div>
@endsection



