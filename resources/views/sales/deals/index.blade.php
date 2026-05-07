@extends('app')
@section('title', 'Deals')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('sales_dashboard') }}">Sales Dashboard</a>
    </li>
    <li class="breadcrumb-item">List</li>
</ol>
@endsection
@section('content')
<div class="row">
    <!-- First Card for Total Deals -->
    <div class="col-md-12 col-lg-4 mb-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Total Deals</h4>
                <div class="d-flex align-items-center justify-content-between">
                    <i class="mdi mdi-briefcase-check-outline mdi-24px text-primary"></i>
                    <h5 class="mb-0 fw-bold text-primary">{{ $totalDeals }}</h5>
                </div>
            </div>
        </div>
    </div>
    <!-- Second Card for Total Value -->
    <div class="col-md-12 col-lg-4 mb-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Total Value</h4>
                <div class="d-flex align-items-center justify-content-between">
                    <i class="mdi mdi-cash-multiple mdi-24px text-success"></i>
                    <!--<h5 class="mb-0 fw-bold text-success">£{{ number_format($totalDealValue, 2) }}</h5>-->
                    <h5 class="mb-0 fw-bold text-success">{{ $currencySymbol }}{{ number_format($totalDealValue, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('deal.new') }}" class="btn btn-outline-primary btn-sm waves-effect float-end">
                    <span class="tf-icons mdi mdi-plus me-1"></span>
                    DEAL
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
                @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong><i class="tf-icons mdi mdi-alert-outline"></i> Error -</strong>
                    {{ session()->get('error') }}
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table leads-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Next Activity</th>
                                <th>Stage</th>
                                <th>Value</th>
                                <th>Deal Created</th>
                                <th>Owner</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTables server-side processing -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/datatables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/buttons.dataTables.min.css') }}">
@endsection

@section('script')
<script src="{{ url('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
<script src="{{ url('assets/vendor/libs/DataTables/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('assets/vendor/libs/DataTables/jszip.min.js') }}"></script>
<script src="{{ url('assets/vendor/libs/DataTables/pdfmake.min.js') }}"></script>
<script src="{{ url('assets/vendor/libs/DataTables/vfs_fonts.js') }}"></script>
<script src="{{ url('assets/vendor/libs/DataTables/buttons.html5.min.js') }}"></script>
<script src="{{ url('assets/vendor/libs/DataTables/buttons.print.min.js') }}"></script>
<script type="text/javascript">
    $(function(){
        const username = "{{ Auth::user()->full_name }}"; // Fetch the authenticated user's full name
        const appName = "{{ config('app.name') }}"; // Get the application name from config
        const timestamp = new Date().toISOString().replace(/T/, ' ').replace(/\..+/, ''); // Current timestamp

        $('.leads-table').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: "{{ route('deal.list') }}",
                type: "GET"
            },
            columns: [
                { data: 'title', name: 'title', orderable: true },
                { data: 'latest_activity', name: 'latest_activity', orderable: true, searchable: false },
                { data: 'stage', name: 'stage', orderable: false },
                { data: 'deal_value', name: 'deal_value', orderable: true}, 
                {
                    data: 'created_at', 
                    name: 'created_at', 
                    render: function(data, type, row) {
                        if (data) {
                            return formatDate(data);
                        }
                        return data;
                    }
                },
                { data: 'owner', name: 'owner', searchable: true},
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            dom: "<'row mb-5'<'col-md-12'B>>" + 
                 "<'row'<'col-md-6 mb-3'l><'col-md-6 mb-3'f>>" +
                 "<'row'<'col-md-12 mb-3'tr>>" +
                 "<'row'<'col-md-6 mb-3'i><'col-md-6 mb-3'p>>",
            buttons: [
                {
                    extend: 'copyHtml5',
                    title: `Deals by ${username} - ${timestamp} - ${appName}`,
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'excelHtml5',
                    title: `Deals by ${username} - ${timestamp} - ${appName}`,
                    filename: `Deals by ${username} - ${timestamp} - ${appName}`,
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'csvHtml5',
                    title: `Deals by ${username} - ${timestamp} - ${appName}`,
                    filename: `Deals by ${username} - ${timestamp} - ${appName}`,
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: `Deals by ${username} - ${timestamp} - ${appName}`,
                    filename: `Deals by ${username} - ${timestamp} - ${appName}`,
                    pageSize: 'A4', // Adjust the page size
                    orientation: 'landscape', // Use landscape for wider tables
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    },
                    customize: function(doc) {
                        // Customize margins and font sizes for better content fit
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        doc.styles.tableHeader.fontSize = 10; // Table header font size
                        doc.styles.tableBodyEven.alignment = 'center'; // Center align body text
                        doc.styles.tableBodyOdd.alignment = 'center'; // Center align body text
                        doc.content[1].margin = [10, 10, 10, 10]; // Adjust margins
                    }
                }
            ],
            language: {
                paginate: {
                    next: '&#8594;',
                    previous: '&#8592;'
                }
            },
            lengthMenu: [
                [30, 90, 270, -1], 
                [30, 90, 270, 'All'] 
            ],
            initComplete: function () {
                $('.dataTables_filter input[type="search"]').attr('placeholder', 'Search By Name/Owner');
            }
        });
    });
    // Function to format date as "31st Dec, 2024"
    function formatDate(date) {
        const d = new Date(date);
        const options = { day: 'numeric', month: 'short', year: 'numeric' };
        let formattedDate = d.toLocaleDateString('en-GB', options);
        const day = d.getDate();
        const suffix = (day >= 10 && day <= 20) ? 'th' : ['st', 'nd', 'rd', 'th'][Math.min(day % 10, 4)];
        formattedDate = formattedDate.replace(/(\d+)([a-z]{2})$/, '$1' + suffix);
        return formattedDate;
    }
</script>
<script type="text/javascript">
    function confirmDelete() {
        return confirm("Are you sure you want to delete this deal?");
    }
</script>
@endsection
