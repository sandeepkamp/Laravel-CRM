@extends('app')

@section('title', 'Employee Management')

@push('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/libs/DataTables/datatables.min.css') }}">
@endpush

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('hr.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">Employee Management</li>
</ol>
@endsection

@section('content')
<div class="row">

    @php $userRole = auth()->user()->role; @endphp

    <!-- Leave Balance Tracking -->
    @if(in_array(auth()->user()->role, [1, 3, 4, 7, 8]))
    <x-data-table-card title="Leave Balance Tracking" table-id="leaveBalanceTable" table-class="leave-balance-table">
        <x-slot name="thead">
            <tr><th>Employee</th><th>Leave Type</th><th>Year</th><th>Total Days</th><th>Used Days</th></tr>
        </x-slot>
    </x-data-table-card>
    @endif

    <!-- Performance Appraisal -->
    @php $canAddAppraisal = in_array($userRole, [1, 3, 7, 8]); @endphp
    @if(in_array(auth()->user()->role, [1, 3, 4, 7, 8]))
    <x-data-table-card title="Performance Appraisal" table-id="performanceAppraisalTable" table-class="performance-appraisal-table" :search="false" :button="$canAddAppraisal" :route="$canAddAppraisal ? route('hr.performance.appraisal.create') : null" :button-name="$canAddAppraisal ? 'Add New' : null">
        <x-slot name="thead">
            <tr><th>Employee</th><th>Evaluator</th><th>Remarks</th><th>Score</th><th>Appraisal Type</th></tr>
        </x-slot>
    </x-data-table-card>
    @endif

    <!-- Employee Feedback -->
    @php $canAddFeedback = in_array($userRole, [1, 3, 7, 8]); @endphp
    @if(in_array(auth()->user()->role, [1, 3, 4, 7, 8]))
    <x-data-table-card title="Employee Feedback" table-id="employeeFeedbackTable" table-class="employee-feedback-table" :search="false" :button="$canAddFeedback" :route="$canAddFeedback ? route('hr.employee.feedback.create') : null" :button-name="$canAddFeedback ? 'Add New' : null">
        <x-slot name="thead">
            <tr><th>Employee</th><th>Reviewer</th><th>Feedback</th><th>Rating</th><th>Review Type</th><th>Submitted At</th></tr>
        </x-slot>
    </x-data-table-card>
    @endif

    <!-- Employee Organisation History -->
    @if(in_array(auth()->user()->role, [1, 3]))
    <x-data-table-card title="Employee Organisation History" table-id="employeeHistoryTable" table-class="employee-history-table" :search="false" :button="true" route="{{ route('hr.employee.history.create') }}" button-name="Add New">
        <x-slot name="thead">
            <tr><th>Employee</th><th>Company</th><th>Designation</th><th>Leaving Reason</th><th>From</th><th>To</th></tr>
        </x-slot>
    </x-data-table-card>
    @endif

</div>

<x-modals.description-modal />
@endsection

@include('components.scripts.description-modal-script')

@push('script')
<script src="{{ asset('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/common-helpers.js') }}"></script>
<script>
    $('document').ready(function() {
        // ---employee-history---
        @if(in_array(auth()->user()->role, [1, 3]))
        initDataTable('.employee-history-table', '{{ route("hr.employee.history") }}', ['employee_name', 'company_name.sf', 'designation.sf', 'reason_for_leaving.sf', 'from_date.sf', 'to_date.sf'], {language: { searchPlaceholder: 'Employee Name' }});
        @endif
        // ---employee-feedback---
        @if(in_array(auth()->user()->role, [1, 3, 4, 7, 8]))
        initDataTable('.employee-feedback-table', '{{ route("hr.employee.feedback") }}', ['employee_name', 'reviewer', 'feedback_text.sf', 'rating.sf', 'quarter.sf', 'submitted_at.sf'], {language: { searchPlaceholder: 'Employee Name' }});
        @endif
        // ---performance-appraisal--- 
        @if(in_array(auth()->user()->role, [1, 3, 4]))
        initDataTable('.performance-appraisal-table', '{{ route("hr.performance.appraisal.index") }}', ['employee_name', 'evaluator', 'remarks.sf', 'score.sf', 'appraisal_type.sf'], {language: { searchPlaceholder: 'Employee Name' }});
        @endif
        // ---leave-balance---
        @if(in_array(auth()->user()->role, [1, 3, 4]))
        initDataTable('.leave-balance-table', '{{ route("hr.leave.balance.index") }}', ['employee_name', 'leave_type.sf', 'year.sf', 'opening_balance.sf', 'used_days.sf'], {language: { searchPlaceholder: 'Employee Name' }});
        @endif
    }); // document closed
</script>
@endpush