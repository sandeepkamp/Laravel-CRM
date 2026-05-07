@extends('app')

@section('title', 'HR Dasboard')

@push('style')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/buttons.dataTables.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet">
@endpush

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('hr.dashboard') }}">HR Dashboard</a>
        </li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12 col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div id="leaveChart"></div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <div id="monthlyLeavesChart" style="width: 100%; height: 400px;"></div>
                </div>
            </div>
        </div>
    
        <div class="col-sm-12 col-md-8">
            <!-- Employee database and availability tracker. -->
            <div class="card mb-4">
                <div class="card-body">
                    <div id="employeeAvailabilityCalendar"></div>
                </div>
            </div>
            <!-- Employee database and availability tracker. -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Employee Availability, Allocation & Skillset</h5>
                        <div class=" text-end pt-0 my-n5">
                            <input type="search" id="employeeAvailability" class="form-control form-control-sm" placeholder="Search Employee">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive sm-device">
                        <table class="table table-striped table-bordered employee-availability">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Allocation</th>
                                    <th>Status</th>
                                    <th>Availability</th>
                                    <th>Skillset</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via DataTables server-side processing -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Resource Utilization & Overload Alerts. -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Resource Overload Alert</h5>
                        <div class=" text-end pt-0 my-n5">
                            <input type="search" id="searchResource" class="form-control form-control-sm" placeholder="Search Resource">
                        </div>
                    </div>
                </div>
                <div class="card-body">  
                    <div class="table-responsive sm-device">
                        <table id="resourceTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                <th>Name</th>
                                <th>Workload (%)</th>
                                <th>Status</th>
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

@push('script')
    <script src="{{ url('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/jszip.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/pdfmake.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/vfs_fonts.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/buttons.html5.min.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/DataTables/buttons.print.min.js') }}"></script>
    
    <!-- FullCalendar JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/web-component@6.1.15/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.15/index.global.min.js'></script>
    
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>


    <script>
        $(document).ready(function() {
            // Employee database and availability tracker.
            var availability = $('.employee-availability').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                dom: 'lrtp',
                info: false,
                pageLength: 10,
                ajax: "{{ route('hr.employees') }}",
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'role', name: 'role', searchable: false },
                    { data: 'allocation', name: 'allocation', orderable: false, searchable: false },
                    { data: 'status', name: 'status', searchable: false },
                    { data: 'availability', name: 'availability', orderable: false, searchable: false },
                    { data: 'skillset', name: 'skillset', orderable: false, searchable: false }
                ]
            });

            $('#employeeAvailability').on('input', function() {
                availability.search(this.value).draw();
            });

            // Resource Utilization and Overload Alerts.
            var resource = $('#resourceTable').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                dom: 'lrtp',
                info: false,
                pageLength: 10,
                ajax: "{{ route('hr.resource-utilization') }}",
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'workload_percentage', name: 'workload_percentage' },
                    { data: 'status', name: 'status', orderable: false, searchable: false }
                ]
            });

            $('#searchResource').on('input', function() {
                resource.search(this.value).draw();
            });

            $.ajax({
                url: "{{ route('hr.leave-statistics') }}",
                type: "GET",
                success: function (data) {
                    Highcharts.chart('leaveChart', {
                        chart: { type: 'pie' },
                        title: { text: 'Employee Leave Status' },
                        tooltip: { pointFormat: '{series.name}: <b>{point.y}</b>' },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.y}'
                                }
                            }
                        },
                        series: [{
                            name: 'Leaves',
                            colorByPoint: true,
                            data: data // Dynamic Data from API
                        }],
                        credits: { enabled: false }
                    });
                },
                error: function () {
                    alert('Error loading leave statistics.');
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () { 
            // Employee Availability Calendar.
            var calendarEl = document.getElementById('employeeAvailabilityCalendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth', // Default view
                events: "{{ route('hr.employee-availability') }}",
                customButtons: {
                    customTitle: {
                        text: "Employee Leave Calendar",
                        click: function() {} // Empty function since we don't want it clickable
                    }
                },
                headerToolbar: {
                    left: 'customTitle', // Custom static title
                    center: 'title',  // Default FullCalendar title (March 2025)
                    right: 'prev,next today'
                },
                titleFormat: { year: 'numeric', month: 'long' },
                height: 600,
                eventClick: function (info) {
                    function formatDate(date) {
                        return moment(date).format('Do MMM, YYYY');
                    }
                    let plainDesc = (info.event.extendedProps.description).replace(/<[^>]*>/g, "");
                    Swal.fire({
                        title: '<b>Employee:</b> ' + info.event.title,
                        html: '<b>Status:</b> ' + info.event.extendedProps.status +
                            '<br><b>Description:</b> ' + plainDesc +
                            '<br><br><b>Date:</b> ' + formatDate(info.event.start) + ' - ' + formatDate(info.event.end), 
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                }
            });

            calendar.render();

            //-------------
            fetch("{{ route('hr.monthly-leaves') }}") // Call API route
            .then(response => response.json())
            .then(data => {
                Highcharts.chart('monthlyLeavesChart', {
                    chart: { 
                        type: 'column',
                        scrollablePlotArea: {
                            minWidth: 530,
                            scrollPositionX: 1
                        }
                    },
                    title: { text: 'Monthly Leaves Taken by Employees' },
                    xAxis: {
                        categories: [
                            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                        ],
                        title: { text: 'Months' }
                    },
                    yAxis: {
                        min: 0,
                        title: { text: 'Number of Leaves' }
                    },
                    series: [{
                        name: 'Leaves Taken',
                        data: data, // Data from API
                        color: '#FAA0A0'
                    }],
                    credits: { enabled: false }
                });
            })
            .catch(error => console.error('Error fetching leave data:', error));
        });

    </script>
@endpush