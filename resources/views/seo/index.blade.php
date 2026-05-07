@extends('app')

@section('title', 'SEO Services List')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('seo.dashboard') }}">SEO Dashboard</a></li>
    <li class="breadcrumb-item active">SEO List</li>
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
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('seo.create') }}" class="btn btn-outline-primary btn-sm waves-effect float-end">
                    <span class="tf-icons mdi mdi-plus me-1"></span>
                    Add SEO Service
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table seo-table">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Website URL</th>
                                <th>Industry</th>
                                <th>Package</th>
                                <th>Start Date</th>
                                <th>Renewal Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($seoServices as $seo)
                            <tr class="@if (\Carbon\Carbon::parse($seo->renewal_date)->isPast()) table-danger @endif">
                                <td>{{ $seo->client->first_name }} {{ $seo->client->last_name }}</td>
                                <td><a href="{{ $seo->website_url }}" target="_blank">{{ $seo->website_url }}</a></td>
                                <td>{{ $seo->industry }}</td>
                                <td>{{ ucfirst($seo->seo_package) }}</td>
                                <td>{{ $seo->start_date }}</td>
                                <td>{{ $seo->renewal_date }}</td>
                                <td>
                                    @php
                                        $renewalDate = \Carbon\Carbon::parse($seo->renewal_date);
                                        $now = \Carbon\Carbon::now();
                                        $daysUntilRenewal = $now->diffInDays($renewalDate, false);
                                        
                                        if ($renewalDate->isPast()) {
                                            $statusLabel = 'Inactive';
                                            $badgeClass = 'bg-warning';
                                        } elseif ($daysUntilRenewal >= 0 && $daysUntilRenewal <= 7) {
                                            $statusLabel = 'Upcoming Renew';
                                            $badgeClass = 'bg-secondary';
                                        } else {
                                            $statusLabel = 'Active';
                                            $badgeClass = 'bg-success';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('seo.edit', $seo->id) }}">
                                                <i class="mdi mdi-pencil-outline me-1"></i> Edit
                                            </a>
                                            <form action="{{ route('seo.delete', $seo->id) }}" method="POST" class="delete-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="dropdown-item text-danger btn-delete" data-id="{{ $seo->id }}">
                                                    <i class="mdi mdi-delete-outline me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $seoServices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/datatables.min.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('script')
<script src="{{ url('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('.seo-table').DataTable({
            dom: "<'row mb-5'<'col-md-12'B>>" +
                "<'row'<'col-md-6'l><'col-md-6'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'i><'col-md-6'p>>",
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            order: [[5, 'asc']]
        });

        // SweetAlert2 delete confirmation
        $('.btn-delete').click(function (e) {
            e.preventDefault();

            const form = $(this).closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
