@extends('app')

@section('title', 'Payroll Management')

@push('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/libs/DataTables/datatables.min.css') }}">
@endpush

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('hr.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">Payroll Management</li>
</ol>
@endsection

@section('content')
<div class="row">

    @php $userRole = auth()->user()->role; @endphp

    <!-- Employee Bank Statement -->
    @php $canAddStatement = in_array($userRole, [1, 3, 7, 8]); @endphp
    @if(in_array(auth()->user()->role, [1, 3, 4]))
    <x-data-table-card title="Employee Bank Statement" table-id="bankStatementTable" table-class="bank-statement-table" :search="false" :button="$canAddStatement" :route="$canAddStatement ? route('hr.bank.statement.create') : null" :button-name="$canAddStatement ? 'Add New' : null">
        <x-slot name="thead">
            <tr><th>Employee Name</th><th>Bank Name</th><th>Branch Name</th><th>Account Number</th><th>IFSC Code</th><th>Action</th></tr>
        </x-slot>
    </x-data-table-card>
    @endif

    <!-- Salary Management -->
    @php $canAddSalary = in_array($userRole, [1, 3, 7, 8]); @endphp
    @if(in_array(auth()->user()->role, [1, 3, 4]))
    <x-data-table-card title="Employee Salary Management" table-id="salaryManagementTable" table-class="salary-management-table" :search="false" :button="$canAddSalary" :route="$canAddSalary ? route('hr.salary.management.create') : null" :button-name="$canAddSalary ? 'Add New' : null">
        <x-slot name="thead">
            <tr><th>Employee Name</th><th>Year</th><th>Month</th><th>Gross Salary</th><th>Payment Mode</th></tr>
        </x-slot>
    </x-data-table-card>
    @endif

</div>
@endsection

@push('script')
<script src="{{ asset('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/common-helpers.js') }}"></script>
<script>
    $('document').ready(function() {
        // bank-statement
        @if(in_array(auth()->user()->role, [1, 3, 4]))
        initDataTable('.bank-statement-table', '{{ route("hr.bank.statement.index") }}', ['employee_name', 'bank_name.sf', 'branch_name.sf', 'account_number.sf', 'ifsc_code.sf', 'action.sfof'], {language: { searchPlaceholder: 'Employee Name' }});
        @endif
        // salary-management
        @if(in_array(auth()->user()->role, [1, 3, 4]))
        initDataTable('.salary-management-table', '{{ route("hr.salary.management.index") }}', ['employee_name', 'year.sf', 'month.sf', 'gross_salary.sf', 'payment_mode.sf'], {language: { searchPlaceholder: 'Employee Name' }});
        @endif
    }); // document closed
</script>
@endpush