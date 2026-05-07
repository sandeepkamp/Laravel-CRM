@extends('app')

@section('title', 'Configuration')

@push('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/libs/DataTables/datatables.min.css') }}">
@endpush

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('hr.dashboard') }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">Configuration</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        
        <!-- Holiday Management -->
        @if(in_array(auth()->user()->role, [1, 3]))
        <x-data-table-card title="Holiday Management" table-id="holidayManagementTable" table-class="holiday-management-table" :search="false" :button="true" route="{{ route('hr.holiday.management.create') }}" button-name="Add New">
            <x-slot name="thead">
                <tr><th>Name</th><th>Date</th><th>Description</th><th>Action</th></tr>
            </x-slot>
        </x-data-table-card>
        @endif
        
        <!-- Leave Type -->
        @if(in_array(auth()->user()->role, [1, 3]))
        <x-data-table-card title="Leave Type" table-id="leaveTypeTable" table-class="leave-type-table" :search="false" :button="true" route="{{ route('hr.leave.type.create') }}" button-name="Add New">
            <x-slot name="thead">
                <tr><th>Leave Name</th><th>Description</th><th>Max Days Per Year</th><th>Background Color</th><th>Font Color</th><th>Action</th></tr>
            </x-slot>
        </x-data-table-card>
        @endif

    </div>

    <x-modals.description-modal />
@endsection

@include('components.scripts.description-modal-script')

@push('script')
    <!-- page js -->
    <script src="{{ asset('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/common-helpers.js') }}"></script>
    <script>
        $('document').ready(function() {
            @if(in_array(auth()->user()->role, [1, 3]))
            initDataTable('.holiday-management-table', '{{ route("hr.holiday.management") }}', ['name', 'date.sf', 'description.sf', 'action.sfof'], {language: { searchPlaceholder: 'Holiday Name' }});
            @endif
            @if(in_array(auth()->user()->role, [1, 3]))
            initDataTable('.leave-type-table', '{{ route("hr.leave.type.index") }}', ['name', 'description.sf', 'max_days_per_year.sf', 'bg_color.sf', 'font_color.sf', 'action.sfof'], {language: { searchPlaceholder: 'Leave Name' }});
            @endif
        }); // document closed

        </script>
@endpush
