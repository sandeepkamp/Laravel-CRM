@extends('app')

@section('title', 'Upcoming SEO Renewals')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('seo.list') }}">SEO Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Upcoming SEO Renewals</li>
</ol>
@endsection

@section('content')
@if($upcomingPlans->count() > 0)
<div class="row mt-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Upcoming Renewals</h5>
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
                            @foreach($upcomingPlans as $plan)
                            <tr>
                                <td>{{ $plan->client->first_name }} {{ $plan->client->last_name }}</td>
                                <td><a href="{{ $plan->website_url }}" target="_blank">{{ $plan->website_url }}</a></td>
                                <td>{{ ucfirst($plan->seo_package) }}</td>
                                <td>{{ \Carbon\Carbon::parse($plan->start_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($plan->renewal_date)->format('d M Y') }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#renewModal{{ $plan->id }}">
                                        Renew
                                    </button>
                                </td>
                            </tr>

                            <!-- Renew Modal -->
                            <div class="modal fade" id="renewModal{{ $plan->id }}" tabindex="-1" aria-labelledby="renewModalLabel{{ $plan->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('seo.renew', $plan->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="renewModalLabel{{ $plan->id }}">Renew SEO Plan</h5>
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
@endsection
