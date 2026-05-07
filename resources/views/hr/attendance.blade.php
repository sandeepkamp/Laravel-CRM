@extends('app')

@section('title', 'Attendance')

@push('style')
<!-- page cdn -->
<x-styles :files="[ 'assets/vendor/libs/DataTables/datatables.min.css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', 'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css', 'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css', ]" />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<style>
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    #calendarGrid .month-box {
        border: 1px solid #ccc;
        padding: 10px;
        font-size: 12px;
        height: 279.17px;
    }

    #calendarGrid .month-box h5 {
        text-align: center;
    }

    .days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        font-size: 12px;
    }

    .day {
        aspect-ratio: 1 / 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
    }

    .present {
        background-color: #28a745;
        color: white;
    }

    .absent {
        background-color: #dc3545;
        color: white;
    }

    .leave {
        background-color: #ffc107;
        color: black;
    }

    .legend.holiday {
        background-color: #dc3545;
        color: white;
    }

    .legend.sick-leave {
        background-color: #ffc107;
        color: black;
    }

    .legend.paid-leave {
        background-color: #31d2f2;
        color: black;
    }

    .legend.weekend {
        background-color: #adadad;
        color: white;
    }

    .weekend {
        background-color: #adadad;
        color: white;
    }
    h3.popover-header{background: #e9ecef; padding: 12px!important;}
    div.bs-popover-auto{border: 1px solid #b9b6b6; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);}
</style>
@endpush

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('hr.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Attendance</li>
</ol>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">

        <!-- Employee Attendance Report -->
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="card-title m-0 me-2 mb-3">Employee Attendance Report</h3>
                    <form id="filterForm" class="row" method="GET">
                        <div class="col-md-5 mb-1">
                            <input type="datetime-local" name="start_date" class="form-control" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-md-5 mb-1">
                            <input type="datetime-local" name="end_date" class="form-control" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive sm-device">
                    <table class="table table-bordered table-striped attendance-list" id="attendanceList">
                        <thead>
                            <tr>
                                <!-- <th>Employee Code</th> -->
                                <th>Employee Name</th>
                                <th>Date</th>
                                <th>Punch In</th>
                                <th>Punch Out</th>
                                <!-- <th>Lunch</th> -->
                                <th>Break</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Employee Leave Calendar -->
@php
$selectedEmployeeId = $employees[0]->id ?? null;
$selectedYear = now()->year;
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3>Employee Leave Calendar</h3>
                </div>
                <div class="row">
                    <!-- employee list -->
                    <div class="col-md-6 mb-3">
                        <div class="input-group">
                            <span class="input-group-text w-100 w-md-25 justify-content-center" id="basic-addon1" style="background: #e9ecef;">Select Employee:</span>
                            <select class="form-select select2 rounded" name="employeeSelect" id="employeeSelect" aria-label="Select Employee">
                                <option value="">-- Select Employee --</option>
                                @foreach ($employees as $key => $employee)
                                    <option value="{{ $employee->id }}" @selected($employee->id == 0)>
                                        {{ $employee?->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- year -->
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <span class="input-group-text w-100 w-md-25 justify-content-center" id="basic-addon1" style="background: #e9ecef;">Select Year:</span>
                            <select id="yearSelect" class="form-select select2 rounded">
                                @for ($y = now()->year; $y >= now()->year - 4; $y--)
                                <option value="{{ $y }}" @selected($y==$selectedYear)>
                                    {{ $y }}
                                </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    @foreach($leaveTypes as $leaveType)
                    <div class="col-md-2">
                        <div class="legend sick-leave w-100 p-2 text-center rounded mb-3" style="background: {{ $leaveType->bg_color}}; color: {{ $leaveType->font_color }};">{{ ucwords($leaveType->name) }}</div>
                    </div>
                    @endforeach
                    <div class="col-md-2">
                        <div class="legend weekend w-100 p-2 text-center rounded mb-3">Weekend</div>
                    </div>
                    <div class="col-md-2">
                        <div class="legend holiday w-100 p-2 text-center rounded mb-3">Holiday</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="calendarGrid" class="row"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<!-- page cdn -->
<x-scripts :files="['assets/vendor/libs/DataTables/datatables.min.js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',]" />

<script>
    const ajaxUrl = "{{ route('hr.attendance.ajax') }}";

    function loadTable(startDate, endDate) {
        $('.attendance-list').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: ajaxUrl,
                data: {
                    start_date: startDate,
                    end_date: endDate
                }
            },
            lengthMenu: [7, 10, 50, 100],
            language: { searchPlaceholder: 'Employee Name' },
            columns: [
                // {
                //     data: 'employee_code',
                //     name: 'employee_code'
                // },
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'punch_in',
                    name: 'punch_in'
                },
                {
                    data: 'punch_out',
                    name: 'punch_out'
                },
                // {
                //     data: 'lunch',
                //     name: 'lunch'
                // },
                {
                    data: 'break',
                    name: 'break'
                },
            ],
            drawCallback: function () {
                // const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                // popoverTriggerList.forEach(function (popoverTriggerEl) {
                //     const existingPopover = bootstrap.Popover.getInstance(popoverTriggerEl);
                //     if (existingPopover) {
                //         existingPopover.dispose(); // Prevent duplicate popovers
                //     }
                //     new bootstrap.Popover(popoverTriggerEl);
                // });
                initPopovers();
            }
        });
    }


    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            allowClear: false
        });

        let start = $('input[name="start_date"]').val();
        let end = $('input[name="end_date"]').val();
        loadTable(start, end);

        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            start = $('input[name="start_date"]').val();
            end = $('input[name="end_date"]').val();
            loadTable(start, end);
        });

    }); // document closed

    $('#employeeSelect, #yearSelect').on('change', function() {
        const empId = $('#employeeSelect').val();
        const year = $('#yearSelect').val();
        if (empId && year) {
            $.ajax({
                url: '/hr/employee/attendance-calendar-grid',
                data: {
                    employee_id: empId,
                    year: year
                },
                success: function(calendarResponse) {
                    renderYearCalendar(calendarResponse, year);
                }
            });
        }
    });

    function renderYearCalendar(calendarResponse, year) {
        const row = $('#calendarGrid');
        row.empty();

        for (let month = 0; month < 12; month++) {
            const column = $('<div>').addClass('col-md-3 mb-4');
            const monthBox = $('<div>').addClass('month-box');
            const title = $('<h5>').text(moment().month(month).format('MMMM'));
            const daysGrid = $('<div>').addClass('days-grid');

            // Add weekday headers
            ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].forEach(d => {
                daysGrid.append(`<div class="day"><strong>${d}</strong></div>`);
            });

            const start = moment(`${year}-${month + 1}-01`);
            const end = start.clone().endOf('month');
            const firstDayIndex = (start.isoWeekday() + 6) % 7; // 0-based index

            // Blank days before month starts
            for (let i = 0; i < firstDayIndex; i++) {
                daysGrid.append('<div class="day"></div>');
            }

            // Days of the month
            for (let day = 1; day <= end.date(); day++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const momentDate = moment(dateStr);
                const dayOfWeek = momentDate.isoWeekday(); // 6 = Saturday, 7 = Sunday

                const dayDiv = $('<div>').addClass('day').text(day);
                if (calendarResponse[dateStr]) {
                    const bgColor = calendarResponse[dateStr]['bg_color'];
                    const fontColor = calendarResponse[dateStr]['font_color'];
                    const holidayName = calendarResponse[dateStr]['holiday_name'];
                    dayDiv.css({ 'background': bgColor, 'color': fontColor });
                    dayDiv.attr({ 'title': holidayName });
                }

                // Highlight weekends
                if (dayOfWeek === 6 || dayOfWeek === 7) {
                    dayDiv.addClass('weekend');
                }

                daysGrid.append(dayDiv);
            }


            monthBox.append(title, daysGrid);
            column.append(monthBox);
            row.append(column);
        }
    }

    // ----------------close privious or opened popover start-------------------
    let currentPopover = null;

    function initPopovers() {
        const triggers = document.querySelectorAll('[data-bs-toggle="popover"]');

        triggers.forEach(trigger => {
            const existing = bootstrap.Popover.getInstance(trigger);
            if (existing) existing.dispose();

            const popover = new bootstrap.Popover(trigger, {
                trigger: 'manual',
                html: true
            });

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();

                // Close current popover if open
                if (currentPopover && currentPopover !== popover) {
                    currentPopover.hide();
                }

                // Toggle this one
                if (popover._isShown()) {
                    popover.hide();
                    currentPopover = null;
                } else {
                    popover.show();
                    currentPopover = popover;
                }
            });
        });

        // Hide popover on clicking outside
        document.addEventListener('click', function () {
            if (currentPopover) {
                currentPopover.hide();
                currentPopover = null;
            }
        });
    }
    // ----------------close privious or opened popover end------------------- 
</script>
@endpush