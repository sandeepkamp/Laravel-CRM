@extends('app')

@section('title', 'Exit Process')

@push('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/libs/DataTables/datatables.min.css') }}">
@endpush

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('hr.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">Exit Process</li>
</ol>
@endsection

@section('content')
<div class="row">

    @php $userRole = auth()->user()->role; @endphp

    <!-- Resignation -->
    @php $canAddResignation = in_array($userRole, [1, 3, 7, 8]); @endphp
    @if(in_array(auth()->user()->role, [1, 3, 4]))
    <x-data-table-card title="Resignation" table-id="resignationTable" table-class="resignation-table" :search="false" :button="$canAddResignation" :route="$canAddResignation ? route('hr.resignation.create') : null" :button-name="$canAddResignation ? 'Add New' : null">
        <x-slot name="thead">
            <tr><th>Employee</th><th>Leaving Reason</th><th>Resignation Date</th><th>Last Working Day</th><th>Status</th><th>Approved By</th></tr>
        </x-slot>
    </x-data-table-card>
    @endif

    <!-- Asset Handover -->
    @php $canAddAsset = in_array($userRole, [1, 3, 7, 8]); @endphp
    @if(in_array(auth()->user()->role, [1, 3, 4]))
    <x-data-table-card title="Asset Handover" table-id="assetHandoverTable" table-class="asset-handover-table" :search="false" :button="$canAddAsset" :route="$canAddAsset ? route('hr.asset.handover.create') : null" :button-name="$canAddAsset ? 'Add New' : null">
        <x-slot name="thead">
            <tr><th>Employee</th><th>Asset Name</th><th>Handover Date</th><th>Handover Status</th></tr>
        </x-slot>
    </x-data-table-card>
    @endif

</div>

<x-modals.description-modal />
@endsection

@include('components.scripts.description-modal-script')

@push('script')
<script src="{{ asset('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
<script>
    window.STATUS_ENUMS = {
        resignation_status: @json(\Enum::RESIGNATION_STATUS),
        asset_handover: @json(\Enum::ASSET_HANDOVER_STATUS),
    };
    window.CSRF_TOKEN = '{{ csrf_token() }}';
</script>
<script src="{{ asset('assets/js/common-helpers.js') }}"></script>
<script>
    $('document').ready(function() {
        // ---resignation---
        @if(in_array(auth()->user()->role, [1, 3, 4]))
        initDataTable('.resignation-table', '{{ route("hr.resignation.index") }}', ['employee_name', 'reason.sf', 'resignation_date.sf', 'last_working_day.sf', 'status.sf', 'approved_by.sf'], {language: { searchPlaceholder: 'Employee Name' }});
        @endif
        // ---asset handover---
        @if(in_array(auth()->user()->role, [1, 3, 4]))
        initDataTable('.asset-handover-table', '{{ route("hr.asset.handover.index") }}', ['employee_name', 'asset_name.sf', 'handover_date.sf', 'handover_status.sf'], {language: { searchPlaceholder: 'Employee/Asset Name' }});
        @endif

    }); // document closed
</script>
@endpush