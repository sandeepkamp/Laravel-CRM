<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Holiday;
use App\Constants\Enum;
use App\Models\Appraisal;
use App\Models\Applicant;
use App\Models\LeaveType;
use App\Models\Interview;
use App\Models\Department;
use App\Models\JobPosting;
use App\Models\Attendance;
use App\Models\OfferLetter;
use Illuminate\Support\Str;
use App\Models\Resignation;
use App\Models\LeaveBalance;
use App\Models\BankStatement;
use App\Models\SalaryPayment;
use App\Models\AssetHandover;
use App\Constants\UploadPaths;
use App\Models\EmployeeHistory;
use Illuminate\Validation\Rule;
use App\Models\EmployeeFeedback;
use App\Models\LeaveManagementModel;
use App\Models\Projects\ProjectModel;
use App\Services\EsslAttendanceService;
use App\Http\Middleware\RoleMiddleware;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Projects\ProjectTasksModel;
use App\Models\Projects\ProjectAssignModel;



class HrController extends Controller
{
    protected $attendanceService;

    public function __construct(EsslAttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        return view('hr.dashboard');
    }

    /*
     * Employee Availability, Allocation & Skillset
     */

    public function getEmployees(Request $request)
    {
        if ($request->ajax()) {
            $employees = User::with(['projectModel', 'assignedTasks', 'leaveRequests', 'userMeta']);

            if (!$request->has('order')) {
                $employees->orderByDesc('id');
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $employees->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
            }

            return DataTables::of($employees->limit(5))
                ->addColumn('name', function ($row) {
                    return ucwords($row->full_name);
                })
                ->orderColumn('name', function ($query, $order) {
                    $query->orderByRaw("CONCAT(first_name, ' ', last_name) {$order}");
                })
                ->addColumn('role', function ($row) {
                    $roleLabels = roleLabels();
                    $roleClass = $roleLabels[$row->role]['class'] ?? 'badge rounded-pill bg-label-default';
                    $roleName = optional($row->role_details)->name ?? 'Unknown';
                    return "<span class='{$roleClass}'>{$roleName}</span>";
                })
                ->orderColumn('role', function ($query, $order) {
                    $query->join('user_role as u', 'users.role', '=', 'u.id')
                        ->orderByRaw("CONCAT(u.name) {$order}");
                })
                ->addColumn('allocation', function ($row) {
                    $allocations = [];
                
                    if ($row->projectModel->isNotEmpty()) {
                        $projects = $row->projectModel->pluck('project_name')->toArray();
                        $allocations[] = 'Projects: ' . implode(', ', $projects);
                    }
                    
                    if ($row->assignedTasks->isNotEmpty()) {
                        $tasks = $row->assignedTasks->pluck('task_name')->toArray();
                        $allocations[] = 'Tasks: ' . implode(', ', $tasks);
                    }
                
                    return !empty($allocations) ? implode(' | ', $allocations) : 'No Project or Task Assigned';
                })
                ->editColumn('status', function ($row) {
                    $statusLabels = getUserStatus();
                    $status = $statusLabels[$row->status] ?? ['text' => 'Unknown', 'class' => 'badge rounded-pill bg-label-secondary'];
                    return "<span class='{$status['class']}'>{$status['text']}</span>";
                })
                ->addColumn('availability', function ($row) {
                    $onLeave = $row->leaveRequests->where('status', 1)->count() > 0;
                    return $onLeave ? "<span class='text-danger'>Unvailable</span>" : "<span class='text-success'>Available</span>";
                })
                ->addColumn('skillset', function ($row) {
                    $skillSets = $row->userMeta->where('meta_key', 'skills')->pluck('meta_value')->implode(', ');
                    return !empty($skillSets) ? $skillSets : 'No Skills Available';
                })
                ->rawColumns(['role', 'status', 'availability', 'skillset'])
                ->make(true);
        }
    }

    // Employee Availability Calendar.

    public function employeeAvailability()
    {
        $employees = User::with('leaveRequests')->get();
        $events = [];
    
        foreach ($employees as $employee) {
            if ($employee->leaveRequests->isNotEmpty()) {
                foreach ($employee->leaveRequests as $leave) {
                    $events[] = [
                        'title' => $employee->full_name,
                        'start' => Carbon::parse($leave->leave_from)->toDateString(),
                        'end' => Carbon::parse($leave->leave_to)->addDay()->toDateString(),
                        'status' => 'On Leave',
                        'description' => $leave->description ?? 'No description provided',
                        'color' => '#dc3545' // Red color for leave
                    ];
                }
            }
        }
    
        return response()->json($events);
    }

    /*
    * Resource Utilization and Overload Alerts.
    */

    public function resourceUtilization(Request $request)
    {
        if ($request->ajax()) {
            $dailyWorkingHours = 8; // working hours per day
            $employees = User::with(['projects', 'assignedTasks']);

            if(!$request->has('order')) {
                $employees->orderByDesc('id');
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $employees->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
            }
    
            return DataTables::of($employees->limit(5))
                ->addColumn('name', function ($row) {
                    return $row->full_name;
                })
                ->orderColumn('name', function ($query, $order) {
                    $query->orderByRaw("CONCAT(first_name, ' ', last_name) {$order}");
                })
                ->addColumn('workload_percentage', function ($row) use ($dailyWorkingHours) {
                    $totalAssignedHours = $row->assignedTasks->sum('estimated_time');
                    $workloadPercentage = ($totalAssignedHours / $dailyWorkingHours) * 100;
                    return round($workloadPercentage, 2) . '%';
                })
                ->addColumn('status', function ($row) use ($dailyWorkingHours) {
                    $totalAssignedHours = $row->assignedTasks->sum('estimated_time');
                    $workloadPercentage = ($totalAssignedHours / $dailyWorkingHours) * 100;
                    // Assign workload status
                    if ($workloadPercentage > 100) {
                        return '<span class="badge bg-danger">Overloaded (' . round($workloadPercentage, 2) . '%)</span>';
                    } elseif ($workloadPercentage >= 90) {
                        return '<span class="badge bg-warning">Near Overload (' . round($workloadPercentage, 2) . '%)</span>';
                    } else {
                        return '<span class="badge bg-success">Balanced (' . round($workloadPercentage, 2) . '%)</span>';
                    }
                })
                ->rawColumns(['status'])
                ->make(true);
        }
    }

    public function getLeaveStatistics()
    {
        $leaveData = LeaveManagementModel::select('status', \DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $statusMapping = [
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Rejected',
            3 => 'On Hold'
        ];

        $chartData = [];
        foreach ($leaveData as $leave) {
            $chartData[] = [
                'name' => $statusMapping[$leave->status] ?? 'Unknown',
                'y' => $leave->count
            ];
        }

        return response()->json($chartData);
    }

    public function monthlyLeavesChart()
    {
        $monthlyLeaves = LeaveManagementModel::selectRaw('MONTH(leave_from) as month, COUNT(*) as total')
            ->whereYear('leave_from', Carbon::now()->year) // Get leaves of the current year
            ->where('status', 1) // Only count approved leaves
            ->groupBy('month')
            ->pluck('total', 'month');

        // Convert month numbers to readable format
        $formattedData = [];
        for ($i = 1; $i <= 12; $i++) {
            $formattedData[] = $monthlyLeaves[$i] ?? 0; // Default to 0 if no data for that month
        }

        return response()->json($formattedData);
    }

    public function configuration(Request $request)
    {
        app(RoleMiddleware::class)->handle($request, function () {}, 1, 3);
        return view('hr.configuration');
    }

    public function employeeManagement(Request $request)
    {
        app(RoleMiddleware::class)->handle($request, function () {}, 1, 3, 4);
        return view('hr.employee-management');
    }

    public function employeeHistory(Request $request)
    {
        if ($request->ajax()) {
            $employeeHistory = EmployeeHistory::with(['employee']);

            if (!$request->has('order')) {
                $employeeHistory->orderByDesc('id');
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $employeeHistory->whereHas('employee', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
            }
    
            return DataTables::of($employeeHistory)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    $name = $row->employee->full_name ?? $row->employee->first_name . ' ' . $row->employee->last_name;
                    return ucwords($name);
                })
                ->orderColumn('employee_name', function ($query, $order) {
                    $query->join('users as u', 'employee_histories.employee_id', '=', 'u.id')
                        ->orderByRaw("CONCAT(u.first_name, ' ', u.last_name) {$order}");
                })
                ->editColumn('company_name', function ($row) {
                    return ucwords($row->company_name);
                })
                ->editColumn('designation', function ($row) {
                    return ucwords($row->designation);
                })
                ->editColumn('reason_for_leaving', function ($row) {
                    $reasonForLeaving = $row->reason_for_leaving;
                    $name = $row->employee->full_name ?? $row->employee->first_name . ' ' . $row->employee->last_name;
                    if (strlen($reasonForLeaving) > 50) {
                        return '<span>' . substr($reasonForLeaving, 0, 15) . '...</span>
                                <a href="javascript:void(0)" class="text-primary read-more" data-description="' . htmlspecialchars($reasonForLeaving) . '" data-description-label="'.ucwords($name).': Reason For Leaving" title="Read More">Read More</a>';
                    } else {
                        return $reasonForLeaving;
                    }
                })
                ->editColumn('from_date', function ($row) {
                    return optional($row->from_date)->format('jS M, Y');
                })
                ->editColumn('to_date', function ($row) {
                    return optional($row->to_date)->format('jS M, Y');
                })
                ->addColumn('action', function ($row) {
                    return '<a href="#"><span class="mdi mdi-square-edit-outline text-info"></span></a> <a href="#"><span class="mdi mdi-trash-can-outline text-danger"></span></a>';
                })
                ->rawColumns(['reason_for_leaving', 'action'])
                ->make(true);
        }
    }

    public function employeeHistoryCreate(Request $request)
    {
        $viewData = [];
        $viewData['employees'] = User::all();

        return view('hr.employee-history-add', $viewData);
    }

    public function employeeHistoryStore(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id'              => 'required|exists:users,id',
            'company_name'             => 'required|array',
            'company_name.*'           => 'required|string|max:255',
            'designation'              => 'required|array',
            'designation.*'            => 'required|string|max:255',
            'from_date'                => 'required|array',
            'from_date.*'              => 'required|date',
            'to_date'                  => 'required|array',
            'to_date.*'                => 'required|date|after_or_equal:from_date.*',
            'reason_for_leaving'       => 'nullable|array',
            'reason_for_leaving.*'     => 'nullable|string|max:1000',
        ]);
        // dd($request->all());

        foreach ($request->company_name as $index => $company) {
            EmployeeHistory::create([
                'employee_id'        => $request->employee_id,
                'company_name'       => $company,
                'designation'        => $request->designation[$index],
                'from_date'          => $request->from_date[$index],
                'to_date'            => $request->to_date[$index],
                'reason_for_leaving' => $request->reason_for_leaving[$index] ?? null,
            ]);
        }

        return redirect()->route('hr.employee.management')->with('success', 'Employee history added successfully.');
    }

    public function employeeFeedback(Request $request)
    {
        if ($request->ajax()) {
            $roleId = auth()->user()->role;
            $employeeId = auth()->user()->id;
            
            if(in_array($roleId, [4])) {
                $employeeFeedback = EmployeeFeedback::with(['employee', 'submittedByUser'])->where('employee_id', $employeeId);
            } else {
                $employeeFeedback = EmployeeFeedback::with(['employee', 'submittedByUser']);
            }

            if (!$request->has('order')) {
                $employeeFeedback->orderByDesc('id');
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $employeeFeedback->whereHas('employee', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
            }
    
            return DataTables::of($employeeFeedback)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    $name = $row->employee->full_name ?? $row->employee->first_name . ' ' . $row->employee->last_name;
                    return ucwords($name);
                })
                ->orderColumn('employee_name', function ($query, $order) {
                    $query->join('users as u', 'employee_feedbacks.employee_id', '=', 'u.id')
                        ->orderByRaw("CONCAT(u.first_name, ' ', u.last_name) {$order}");
                })
                ->addColumn('reviewer', function ($row) {
                    $name = $row->submittedByUser->full_name ?? $row->submittedByUser->first_name . ' ' . $row->submittedByUser->last_name;
                    return ucwords($name);
                })
                ->editColumn('feedback_text', function ($row) {
                    $feedback_text = $row->feedback_text;
                    $name = $row->employee->full_name ?? $row->employee->first_name . ' ' . $row->employee->last_name;
                    if (strlen($feedback_text) > 50) {
                        return '<span>' . substr($feedback_text, 0, 15) . '...</span>
                                <a href="javascript:void(0)" class="text-primary read-more" data-description="' . htmlspecialchars($feedback_text) . '" data-description-label="'.ucwords($name).': Feedback" title="Read More">Read More</a>';
                    } else {
                        return $feedback_text;
                    }
                })
                ->editColumn('submitted_at', function ($row) {
                    return optional($row->submitted_at)->format('jS M, Y');
                })
                ->editColumn('quarter', function ($row) {
                    return \Enum::QUARTER[$row->quarter]['label'];
                })
                ->editColumn('rating', function ($row) {
                    return round($row->rating, 1) . '/10';
                })
                ->rawColumns(['feedback_text'])
                ->make(true);
        }
    }

    public function employeeFeedbackCreate(Request $request)
    {
        $viewData = [];
        $departmentId = auth()->user()->department_id;
        $roleId = auth()->user()->role;

        if(($departmentId == 1) && ($roleId == 1)) {
            $viewData['employees'] = User::all();
        } elseif(($departmentId == 2) && ($roleId == 7)) {
            $viewData['employees'] = User::whereIn('role', [8, 4])->get();
        }  elseif(($departmentId == 2) && ($roleId == 8)) {
            $viewData['employees'] = User::whereIn('role', [4])->get();
        } else {
            $viewData['employees'] = collect();
        }

        return view('hr.employee-feedback-add', $viewData);
    }

    public function employeeFeedbackStore(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id'    => 'required|exists:users,id',
            'rating'         => 'required|numeric|max:10',
            'quarter'        => ['required', 'string', Rule::in(array_keys(Enum::QUARTER))],
            'feedback_text'  => 'nullable|string|max:1000',
        ]);
    
        $validatedData['submitted_by']   = auth()->id();
        EmployeeFeedback::create($validatedData);

        return redirect()->route('hr.employee.management')->with('success', 'Employee feedback added successfully.');
    }

    public function holidayManagement(Request $request)
    { 
        if ($request->ajax()) {
            $holidayManagement = Holiday::query();

            if (!$request->has('order')) {
                $holidayManagement->orderByDesc('id');
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $holidayManagement->where('name', 'LIKE', "%{$search}%");
            }
    
            return DataTables::of($holidayManagement)
                ->addIndexColumn()
                ->editColumn('description', function ($row) {
                    $description = $row->description;
                    if (strlen($description) > 50) {
                        return '<span>' . substr($description, 0, 15) . '...</span>
                                <a href="javascript:void(0)" class="text-primary read-more" data-description="' . htmlspecialchars($description) . '" data-description-label="'.ucwords($row->name).': Description" title="Read More">Read More</a>';
                    } else {
                        return $description;
                    }
                })
                ->editColumn('date', function ($row) {
                    return $row->date->format('jS M, Y');
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('hr.holiday.management.edit', ['id' => $row->id]);
                    $deleteForm = '
                        <form action="' . route('hr.holiday.management.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to delete this holiday?\')">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </form>
                    ';
                    return '<a href="' . $editUrl . '" class="btn btn-info btn-xs"><i class="mdi mdi-square-edit-outline"></i></a> ' . $deleteForm;
                })
                ->rawColumns(['description', 'action'])
                ->make(true);
        }
    }

    public function holidayManagementCreate(Request $request)
    {
        return view('hr.holiday.holiday-management-add');
    }

    public function holidayManagementStore(Request $request)
    {
        $validatedData = $request->validate([
            'name'    => 'required|string|max:255',
            'date'    => 'required|date',
            'description'  => 'nullable|string|max:1000',
        ]);
        
        $validatedData['name'] = ucwords($request->name);
        $validatedData['description'] = ucfirst($request->description);
        $holiday = Holiday::create($validatedData);

        if ($holiday) {
            return redirect()->route('hr.configuration')->with('success', 'Holiday added successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
    }

    public function holidayManagementEdit($id)
    {
        $holiday = Holiday::find($id);

        if (!$holiday) {
            return redirect()->route('hr.configuration')->with('error', 'Holiday not found.');
        }

        return view('hr.holiday.holiday-management-edit', compact('holiday'));
    }

    public function holidayManagementUpdate(Request $request, $id)
    {
        $holiday = Holiday::find($id);

        if (!$holiday) {
            return redirect()->route('hr.configuration')->with('error', 'Holiday not found.');
        }

        $validatedData = $request->validate([
            'name'    => 'required|string|max:255',
            'date'    => 'required|date',
            'description'  => 'nullable|string|max:1000',
        ]);

        $validatedData['name'] = ucwords($request->name);
        $validatedData['description'] = ucfirst($request->description);
        $holiday->update($validatedData);

        return redirect()->route('hr.configuration')->with('success', 'Holiday updated successfully.');
    }

    public function holidayManagementDelete($id)
    {
        $holiday = Holiday::find($id);

        if (!$holiday) {
            return redirect()->route('hr.configuration')->with('error', 'Holiday not found.');
        }

        $holiday->delete();
        return redirect()->route('hr.configuration')->with('success', 'Holiday deleted successfully.');

    }

    public function leaveTypeIndex(Request $request)
    {
        if ($request->ajax()) {
            $leaveType = LeaveType::query();

            if (!$request->has('order')) {
                $leaveType->orderByDesc('id');
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $leaveType->where('name', 'LIKE', "%{$search}%");
            }
    
            return DataTables::of($leaveType->limit(5))
                ->addIndexColumn()
                ->editColumn('description', function ($row) {
                    $description = $row->description;
                    if (strlen($description) > 50) {
                        return '<span>' . substr($description, 0, 15) . '...</span>
                                <a href="javascript:void(0)" class="text-primary read-more" data-description="' . htmlspecialchars($description) . '" data-description-label="'.ucwords($row->name).': Description" title="Read More">Read More</a>';
                    } else {
                        return $description;
                    }
                })
                ->editColumn('bg_color', function ($row) {
                    return '<input type="color" value="' . $row->bg_color . '" style="display: block; margin: 0 auto;" disabled>';
                    
                })
                ->editColumn('font_color', function ($row) {
                    return '<input type="color" value="' . $row->font_color . '" style="display: block; margin: 0 auto;" disabled>';
                    
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('hr.leave.type.edit', ['id' => $row->id]);
                    $deleteForm = '
                        <form action="' . route('hr.leave.type.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to delete this Leave Type?\')">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </form>
                    ';
                    return '<a href="' . $editUrl . '" class="btn btn-info btn-xs"><i class="mdi mdi-square-edit-outline"></i></a> ' . $deleteForm;
                })
                ->rawColumns(['description', 'bg_color', 'font_color', 'action'])
                ->make(true);
        }
    }

    public function leaveTypeCreate(Request $request)
    {
        return view('hr.leave-type.leave-type-create');
    }

    public function leaveTypeStore(Request $request)
    {
        $validatedData = $request->validate([
            'name'    => 'required|string|max:255',
            'description'  => 'nullable|string|max:1000',
            'max_days_per_year'    => 'required|integer|min:0',
            'bg_color'  => 'nullable|string',
            'font_color'  => 'nullable|string',
        ]);
        
        $validatedData['name'] = ucwords($request->name);
        $validatedData['description'] = ucfirst($request->description ?? '');
        $leaveType = LeaveType::create($validatedData);

        if ($leaveType) {
            $users = User::all();
            $leaveBalances = [];
            foreach ($users as $user) {
                $leaveBalances[] = [
                    'employee_id'     => $user->id,
                    'leave_type_id'   => $leaveType->id,
                    'year'            => date('Y'),
                    'opening_balance' => $validatedData['max_days_per_year'],
                ];
            }
            LeaveBalance::insert($leaveBalances);
    
            return redirect()->route('hr.configuration')->with('success', 'Leave Type added successfully.');
        }
        return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
    }

    public function leaveTypeEdit($id)
    {
        $leaveType = LeaveType::find($id);

        if (!$leaveType) {
            return redirect()->route('hr.configuration')->with('error', 'Leave Type not found.');
        }

        return view('hr.leave-type.leave-type-edit', compact('leaveType'));
    }

    public function leaveTypeUpdate(Request $request, $id)
    {
        $leaveType = LeaveType::find($id);

        if (!$leaveType) {
            return redirect()->route('hr.configuration')->with('error', 'Leave Type not found.');
        }

        $validatedData = $request->validate([
            'name'    => 'required|string|max:255',
            'description'  => 'nullable|string|max:1000',
            'max_days_per_year'    => 'required|integer',
            'bg_color'  => 'nullable|string',
            'font_color'  => 'nullable|string',
        ]);

        $validatedData['name'] = ucwords($request->name);
        $validatedData['description'] = ucfirst($request->description);
        $leaveType->update($validatedData);

        return redirect()->route('hr.configuration')->with('success', 'Leave Type updated successfully.');
    }

    public function leaveTypeDelete($id)
    {
        $leaveType = LeaveType::find($id);

        if (!$leaveType) {
            return redirect()->route('hr.configuration')->with('error', 'Leave Type not found.');
        }

        $leaveType->delete();
        return redirect()->route('hr.configuration')->with('success', 'Leave Type deleted successfully.');
    }

    public function payrollManagement(Request $request)
    {
        app(RoleMiddleware::class)->handle($request, function () {}, 1, 3, 4);
        return view('hr.payroll-management');
    }

    public function bankStatementIndex(Request $request)
    {
        if ($request->ajax()) {
            $roleId = auth()->user()->role;
            $employeeId = auth()->user()->id;

            if(in_array($roleId, [4])) {
                $bankStatement = BankStatement::query()->where('employee_id', $employeeId);
            } else {
                $bankStatement = BankStatement::query();
            }

            if (!$request->has('order')) {
                $bankStatement->orderByDesc('id');
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $bankStatement->whereHas('employee', function($employee) use($search) {
                    $employee->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
            }
    
            return DataTables::of($bankStatement->limit(5))
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    $name = $row->employee->full_name ?? $row->employee->first_name . ' ' . $row->employee->last_name;
                    return ucwords($name);
                })
                ->orderColumn('employee_name', function ($query, $order) {
                    $query->join('users as u', 'bank_statements.employee_id', '=', 'u.id')
                        ->orderByRaw("CONCAT(u.first_name, ' ', u.last_name) {$order}");
                })
                ->editColumn('bank_name', function ($row) {
                    return ucwords($row->bank_name);
                })
                ->editColumn('branch_name', function ($row) {
                    return ucwords($row->branch_name);
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('hr.bank.statement.edit', ['id' => $row->id]);
                    $link = '<a href="' . $editUrl . '" class="btn btn-info btn-xs"><i class="mdi mdi-square-edit-outline"></i></a> ';
                    return in_array(auth()->user()->role, [1, 3]) ? $link : '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function bankStatementCreate(Request $request)
    {
        $viewData = [];
        $viewData['employees'] = User::all();

        return view('hr.bank-statement.bank-statement-create', $viewData);
    }

    public function bankStatementStore(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id'    => 'required|exists:users,id',
            'bank_name'    => 'required|string|max:255',
            'branch_name'    => 'nullable|string|max:255',
            'account_number'    => 'required|string',
            'ifsc_code'    => 'required|string|max:255',
        ]);
        
        $validatedData['bank_name'] = ucwords($request->bank_name);
        $validatedData['branch_name'] = ucwords($request->branch_name);
        $bankStatement = BankStatement::create($validatedData);

        if ($bankStatement) {
            return redirect()->route('hr.payroll.management')->with('success', 'Bank Statement added successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
    }

    public function bankStatementEdit($id)
    {
        $bankStatement = BankStatement::find($id);

        if (!$bankStatement) {
            return redirect()->route('hr.payroll.management')->with('error', 'Bank Statement not found.');
        }

        return view('hr.bank-statement.bank-statement-edit', compact('bankStatement'));
    }

    public function bankStatementUpdate(Request $request, $id)
    {
        $bankStatement = BankStatement::find($id);

        if (!$bankStatement) {
            return redirect()->route('hr.payroll.management')->with('error', 'Bank Statement not found.');
        }

        $validatedData = $request->validate([
            'bank_name'    => 'required|string|max:255',
            'branch_name'    => 'nullable|string|max:255',
            'account_number'    => 'required|string',
            'ifsc_code'    => 'required|string|max:255',
        ]);
        
        $validatedData['bank_name'] = ucwords($request->bank_name);
        $validatedData['branch_name'] = ucwords($request->branch_name);
        $bankStatement->update($validatedData);

        return redirect()->route('hr.payroll.management')->with('success', 'Bank Statement updated successfully.');
    }

    public function recruitment(Request $request)
    {
        app(RoleMiddleware::class)->handle($request, function () {}, 1, 3);
        return view('hr.recruitment');
    }

    // ------------job posting---------------
    public function jobPostingIndex(Request $request)
    {
        if ($request->ajax()) {
            $jobPosting = JobPosting::with(['department']);

            if (!$request->has('order')) {
                $jobPosting->orderByDesc('id');
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $jobPosting->where('title', 'LIKE', "%{$search}%");
            }

            return DataTables::of($jobPosting->limit(5))
                ->addIndexColumn()
                ->editColumn('description', function ($row) {
                    $description = $row->description;
                    if (strlen($description) > 50) {
                        return '<span>' . substr($description, 0, 15) . '...</span>
                                <a href="javascript:void(0)" class="text-primary read-more" data-description="' . htmlspecialchars($description) . '" data-description-label="'.ucwords($row->title).': Description" title="Read More">Read More</a>';
                    } else {
                        return $description;
                    }
                })
                ->editColumn('posted_date', function ($row) {
                    return $row->posted_date->format('jS M, Y');
                })
                ->addColumn('department', function ($row) {
                    return $row->department ? $row->department->name : '';
                })
                ->addColumn('status', function ($row) {
                    return Enum::JOB_POSTING_STATUS[$row->status]['label'];
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('hr.job.posting.edit', ['id' => $row->id]);
                    $deleteForm = '
                        <form action="' . route('hr.job.posting.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to delete this Job Posting?\')">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </form>
                    ';
                    return '<a href="' . $editUrl . '" class="btn btn-info btn-xs"><i class="mdi mdi-square-edit-outline"></i></a> ' . $deleteForm;
                })
                ->rawColumns(['description', 'department', 'action'])
                ->make(true);
        }
    }

    public function jobPostingCreate(Request $request)
    {
        $viewData = [];
        $viewData['department'] = Department::all();

        return view('hr.job-posting.job-posting-create', $viewData);
    }

    public function jobPostingStore(Request $request)
    {
        $validatedData = $request->validate([
            'title'  => 'required|string|max:255',
            'description'  => 'required|string|max:1000',
            'department_id'    => 'required|exists:departments,id',
            'location'    => 'required|string|max:255',
            'status'    => ['required', 'string', Rule::in(array_keys(Enum::JOB_POSTING_STATUS))],
        ]);
        
        $validatedData['title'] = ucfirst($request->title);
        $validatedData['description'] = ucfirst($request->description);
        $validatedData['posted_date'] = now();
        $jobPosting = JobPosting::create($validatedData);

        if ($jobPosting) {
            return redirect()->route('hr.recruitment')->with('success', 'Job Posting added successfully.');
        }
        return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
    }

    public function jobPostingEdit($id)
    {
        $jobPosting = JobPosting::find($id);
        if (!$jobPosting) {
            return redirect()->route('hr.recruitment')->with('error', 'Job Posting not found.');
        }
        $department = Department::all();

        return view('hr.job-posting.job-posting-edit', compact('department', 'jobPosting'));
    }

    public function jobPostingUpdate(Request $request, $id)
    {
        $jobPosting = JobPosting::find($id);
        if (!$jobPosting) {
            return redirect()->route('hr.recruitment')->with('error', 'Job Posting not found.');
        }

        $validatedData = $request->validate([
            'title'  => 'required|string|max:255',
            'description'  => 'required|string|max:1000',
            'department_id'    => 'required|exists:departments,id',
            'location'    => 'required|string|max:255',
            'posted_date'    => 'required|date|after_or_equal:today',
            'status'    => ['required', 'string', Rule::in(array_keys(Enum::JOB_POSTING_STATUS))],
        ]);
        
        $validatedData['title'] = ucfirst($request->title);
        $validatedData['description'] = ucfirst($request->description);
        $jobPosting->update($validatedData);
        return redirect()->route('hr.recruitment')->with('success', 'Job Posting updated successfully.');
    }

    public function jobPostingDelete($id)
    {
        $jobPosting = JobPosting::find($id);
        if (!$jobPosting) {
            return redirect()->route('hr.recruitment')->with('error', 'Job Posting not found.');
        }

        $jobPosting->delete();
        return redirect()->route('hr.recruitment')->with('success', 'Job Posting deleted successfully.');
    }

    // ------------applicant---------------
    public function applicantIndex(Request $request)
    {
        if ($request->ajax()) {
            $applicant = Applicant::with(['jobPosting']);

            if (!$request->has('order')) {
                $applicant->orderByDesc('id');
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $applicant->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            }

            return DataTables::of($applicant->limit(5))
                ->addIndexColumn()
                ->addColumn('post_title', function ($row) {
                    return $row->jobPosting->title;
                })
                ->addColumn('department', function ($row) {
                    return $row->jobPosting->department->name;
                })
                ->editColumn('address', function ($row) {
                    $address = $row->address;
                    if (strlen($address) > 50) {
                        return '<span>' . substr($address, 0, 15) . '...</span>
                                <a href="javascript:void(0)" class="text-primary read-more" data-description="' . htmlspecialchars($address) . '" data-description-label="'.ucwords($row->name).': Address" title="Read More">Read More</a>';
                    } else {
                        return $address;
                    }
                })
                ->editColumn('status', function ($row) {
                    $label = isset(\Enum::APPLICANT_STATUS[$row->status]) 
                    ? \Enum::APPLICANT_STATUS[$row->status]['label'] 
                    : '-';
                    $url = route('hr.applicant.update.status', [$row->id]);

                    return "
                        <div class='status-wrapper' data-id='{$row->id}' data-current-status='{$row->status}' data-module='applicant_status' data-url='{$url}' data-table-class='applicant-table' style='width:fit-content'>
                            <span class='status-text'>{$label}</span>
                            <a href='javascript:void(0);' class='edit-status' style='margin-left:8px;'>
                                <span class='mdi mdi-circle-edit-outline'></span>
                            </a>
                        </div>
                    ";
                })
                ->addColumn('action', function ($row) {
                    $deleteForm = '
                        <form action="' . route('hr.applicant.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to delete this applicant?\')">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </form>
                    ';
                    return $deleteForm;
                })
                ->rawColumns(['address', 'department', 'status', 'action'])
                ->make(true);
        }
    }

    public function applicantCreate(Request $request)
    {
        $viewData = [];
        $viewData['jobPostings'] = JobPosting::all();

        return view('hr.applicant.applicant-create', $viewData);
    }

    public function applicantStore(Request $request)
    {
        $validatedData = $request->validate([
            'job_posting_id'    => 'required|exists:job_postings,id',
            'name'    => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'phone'    => 'required|string|max:255',
            'resume_path'    => 'required|file|mimes:pdf,doc,docs|max:10240', // max 10MB
            'status'    => ['required', 'string', Rule::in(array_keys(Enum::APPLICANT_STATUS))],
            'address'    => ['nullable', 'string', 'max:1000'],
        ]);
        
        $validatedData['name'] = Str::title($request->name);
        $validatedData['address'] = $request->address ? ucfirst($request->address) : null;
        $fileData = [];
        if($request->hasFile('resume_path')) {
            $fileData = uploadFile($request->file('resume_path'), \UploadPaths::APPLICANT_RESUME);
        }
        $validatedData['resume_path'] = !empty($fileData) ? $fileData['path'] : null;

        $applicant = Applicant::create($validatedData);

        if ($applicant) {
            return redirect()->route('hr.recruitment')->with('success', 'Applicant added successfully.');
        }
        return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
    }

    public function applicantUpdateStatus(Request $request, $id)
    {
        $applicant = Applicant::find($id);
        if (!$applicant) {
            return redirect()->route('hr.recruitment')->with('error', 'Applicant not found.');
        }

        $validatedData = $request->validate([
            'status'    => ['required', 'string', Rule::in(array_keys(Enum::APPLICANT_STATUS))],
        ]);

        $applicant->update($validatedData);
        return response()->json([ 'status' => 'success', 'message' => 'Status updated successfully' ]);
    }

    public function applicantDelete($id)
    {
        $applicant = Applicant::find($id);
        if (!$applicant) {
            return redirect()->route('hr.recruitment')->with('error', 'Applicant not found.');
        }

        $applicant->delete();
        return redirect()->route('hr.recruitment')->with('success', 'Applicant deleted successfully.');
    }

    // ------------Interview---------------
    public function interviewIndex(Request $request)
    {
        if ($request->ajax()) {
            $interview = Interview::with(['applicant', 'interviewer']);

            if (!$request->has('order')) {
                $interview->orderByDesc('id');
            }

            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $interview->where(function ($query) use ($search) {
                    $query->whereHas('applicant', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('interviewer', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
                });
            }

            return DataTables::of($interview->limit(5))
                ->addIndexColumn()
                ->addColumn('applicant', function ($row) {
                    return $row->applicant->name;
                })
                ->addColumn('interviewer', function ($row) {
                    return $row->interviewer?->full_name;
                })
                ->editColumn('scheduled_date', function ($row) {
                    return $row->scheduled_date->format('jS M, Y');
                })
                ->editColumn('feedback', function ($row) {
                    $feedback = $row->feedback;
                    if (strlen($feedback) > 50) {
                        return '<span>' . substr($feedback, 0, 15) . '...</span>
                                <a href="javascript:void(0)" class="text-primary read-more" data-description="' . htmlspecialchars($feedback) . '" data-description-label="'.ucwords($row->applicant->name).': Feedback" title="Read More">Read More</a>';
                    } else {
                        return $feedback;
                    }
                })
                ->editColumn('status', function ($row) {
                    return Enum::INTERVIEW_STATUS[$row->status]['label'];
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('hr.interview.edit', ['id' => $row->id]);
                    $deleteForm = '
                        <form action="' . route('hr.interview.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to delete this Interview?\')">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </form>
                    ';
                    return '<a href="' . $editUrl . '" class="btn btn-info btn-xs"><i class="mdi mdi-square-edit-outline"></i></a> ' . $deleteForm;
                })
                ->rawColumns(['feedback', 'action'])
                ->make(true);
        }
    }

    public function interviewCreate(Request $request)
    {
        $viewData = [];
        $viewData['applicants'] = Applicant::all();
        $viewData['interviewers'] = User::whereIn('role', [1, 3, 7, 8])->get();

        return view('hr.interview.interview-create', $viewData);
    }

    public function interviewStore(Request $request)
    {
        $validatedData = $request->validate([
            'applicant_id'    => 'required|exists:applicants,id',
            'interviewer_id'    => 'required|exists:users,id',
            'scheduled_date'    => 'required|date|after_or_equal:today',
            'status'    => ['required', 'string', Rule::in(array_keys(Enum::INTERVIEW_STATUS))],
            'feedback'    => ['nullable', 'string', 'max:1000'],
        ]);
        
        $validatedData['feedback'] = ucfirst($request->feedback);
        $interview = Interview::create($validatedData);

        if ($interview) {
            return redirect()->route('hr.recruitment')->with('success', 'Interview added successfully.');
        }
        return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
    }

    public function interviewEdit($id)
    {
        $interview = Interview::find($id);
        if (!$interview) {
            return redirect()->route('hr.recruitment')->with('error', 'Interview not found.');
        }

        $applicants = Applicant::where('id', $interview->applicant_id)->get();
        $interviewers = User::whereIn('role', [1, 3, 7, 8])->get();

        return view('hr.interview.interview-edit', compact('interview', 'applicants', 'interviewers'));
    }

    public function interviewUpdate(Request $request, $id)
    {
        $interview = Interview::find($id);
        if (!$interview) {
            return redirect()->route('hr.recruitment')->with('error', 'Interview not found.');
        }

        $validatedData = $request->validate([
            'interviewer_id'    => 'required|exists:users,id',
            'scheduled_date'    => 'required|date|after_or_equal:today',
            'status'    => ['required', 'string', Rule::in(array_keys(Enum::INTERVIEW_STATUS))],
            'feedback'    => ['nullable', 'string', 'max:1000'],
        ]);
        
        $validatedData['feedback'] = ucfirst($request->feedback);
        if ($interview->update($validatedData)) {
            return redirect()->route('hr.recruitment')->with('success', 'Interview updated successfully.');
        }
    
        return redirect()->back()->withInput()->with('error', 'Update failed. Please try again.');
    }

    public function interviewDelete($id)
    {
        $interview = Interview::find($id);
        if (!$interview) {
            return redirect()->route('hr.recruitment')->with('error', 'interview not found.');
        }

        $interview->delete();
        return redirect()->route('hr.recruitment')->with('success', 'interview deleted successfully.');
    }

    // ------------offer-letter---------------
    public function offerLetterIndex(Request $request)
    {
        if ($request->ajax()) {
            $offerLetter = OfferLetter::with(['applicant']);

            if (!$request->has('order')) {
                $offerLetter->orderByDesc('id');
            }

            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $offerLetter->where(function ($query) use ($search) {
                    $query->whereHas('applicant', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
                });
            }

            return DataTables::of($offerLetter->limit(5))
                ->addIndexColumn()
                ->addColumn('applicant', function ($row) {
                    return $row->applicant->name;
                })
                ->editColumn('offer_date', function ($row) {
                    return $row->offer_date->format('jS M, Y');
                })
                ->editColumn('joining_date', function ($row) {
                    return $row->joining_date->format('jS M, Y');
                })
                ->editColumn('status', function ($row) {
                    return Enum::OFFER_LETTER_STATUS[$row->status]['label'];
                })
                ->editColumn('offer_file_path', function ($row) {
                    if($row->offer_file_path) {
                        $document = '<button class="btn btn-sm btn-primary view-offer-letter" data-url="' . asset(UploadPaths::OFFER_LETTERS . '/' . $row->offer_file_path) . '">View</button>';
                    } else {
                        $document = '<span class="text-secondary">No Document</span>';
                    }
                    return $document;
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('hr.offer.letter.edit', ['id' => $row->id]);
                    $deleteForm = '
                        <form action="' . route('hr.offer.letter.delete', ['id' => $row->id]) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to delete this Offer Letter?\')">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </form>
                    ';
                    return '<a href="' . $editUrl . '" class="btn btn-info btn-xs"><i class="mdi mdi-square-edit-outline"></i></a> ' . $deleteForm;
                })
                ->rawColumns(['action', 'offer_file_path'])
                ->make(true);
        }
    }

    public function offerLetterCreate(Request $request)
    {
        $viewData = [];
        $viewData['applicants'] = Applicant::all();

        return view('hr.offer-letter.offer-letter-create', $viewData);
    }

    public function offerLetterStore(Request $request)
    {
        $validatedData = $request->validate([
            'applicant_id'    => 'required|exists:applicants,id',
            'offer_date'    => 'required|date|after_or_equal:today',
            'joining_date'    => 'required|date|after_or_equal:today',
            'offer_file_path'    => 'nullable|file|mimes:pdf,doc,docs|max:5120', // max 5MB
            'status'    => ['required', 'string', Rule::in(array_keys(Enum::OFFER_LETTER_STATUS))],
        ]);
        
        $fileData = [];
        if($request->hasFile('offer_file_path')) {
            $fileData = uploadFile($request->file('offer_file_path'), \UploadPaths::OFFER_LETTERS);
        }
        $validatedData['offer_file_path'] = !empty($fileData) ? $fileData['modified_name'] : null;

        $offerLetter = OfferLetter::create($validatedData);

        if ($offerLetter) {
            return redirect()->route('hr.recruitment')->with('success', 'Offer Letter added successfully.');
        }
        return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
    }

    public function offerLetterEdit($id)
    {
        $offerLetter = OfferLetter::find($id);
        if (!$offerLetter) {
            return redirect()->route('hr.recruitment')->with('error', 'Offer Letter not found.');
        }
        $applicants = Applicant::where('id', $offerLetter->applicant_id)->get();

        return view('hr.offer-letter.offer-letter-edit', compact('offerLetter', 'applicants'));
    }

    public function offerLetterUpdate(Request $request, $id)
    {
        $offerLetter = OfferLetter::find($id);
        if (!$offerLetter) {
            return redirect()->route('hr.recruitment')->with('error', 'Offer Letter not found.');
        }

        $validatedData = $request->validate([
            'offer_date'    => 'required|date|after_or_equal:today',
            'joining_date'    => 'required|date|after_or_equal:today',
            'offer_file_path'    => 'nullable|file|mimes:pdf,doc,docs|max:5120', // max 5MB
            'status'    => ['required', 'string', Rule::in(array_keys(Enum::OFFER_LETTER_STATUS))],
        ]);
        
        $fileData = [];
        if($request->hasFile('offer_file_path')) {
            $fileData = uploadFile($request->file('offer_file_path'), \UploadPaths::OFFER_LETTERS);
        }
        $validatedData['offer_file_path'] = !empty($fileData) ? $fileData['modified_name'] : null;

        $offerLetter->update($validatedData);
        return redirect()->route('hr.recruitment')->with('success', 'Offer Letter updated successfully.');
    }

    public function offerLetterDelete($id)
    {
        $offerLetter = OfferLetter::find($id);
        if (!$offerLetter) {
            return redirect()->route('hr.recruitment')->with('error', 'Offer Letter not found.');
        }

        if($offerLetter->offer_file_path) {
            deleteFile(\UploadPaths::OFFER_LETTERS . '/' . $offerLetter->offer_file_path);
        }
        $offerLetter->delete();
        return redirect()->route('hr.recruitment')->with('success', 'Offer Letter deleted successfully.');
    }

    public function performanceAppraisalIndex(Request $request)
    {
        if ($request->ajax()) {
            $roleId = auth()->user()->role;
            $employeeId = auth()->user()->id;

            if(in_array($roleId, [4])) {
                $appraisal = Appraisal::with(['employee', 'reviewer'])->where('employee_id', $employeeId);
            } else {
                $appraisal = Appraisal::with(['employee', 'reviewer']);
            }

            if (!$request->has('order')) {
                $appraisal->orderByDesc('id');
            }

            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $appraisal->where(function ($query) use ($search) {
                    $query->whereHas('employee', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    })
                    ->orWhereHas('reviewer', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
                });            
            }
    
            return DataTables::of($appraisal)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    $name = $row->employee?->full_name ?? $row->employee->first_name . ' ' . $row->employee->last_name;
                    return ucwords($name);
                })
                ->orderColumn('employee_name', function ($query, $order) {
                    $query->join('users as u', 'appraisals.employee_id', '=', 'u.id')
                        ->orderByRaw("CONCAT(u.first_name, ' ', u.last_name) {$order}");
                })
                ->addColumn('evaluator', function ($row) {
                    $name = $row->reviewer?->full_name ?? $row->reviewer->first_name . ' ' . $row->reviewer->last_name;
                    return ucwords($name);
                })
                ->addColumn('remarks', function ($row) {
                    $comments = $row->comments;
                    $name = $row->employee?->full_name ?? $row->employee->first_name . ' ' . $row->employee->last_name;
                    if (strlen($comments) > 50) {
                        return '<span>' . substr($comments, 0, 15) . '...</span>
                                <a href="javascript:void(0)" class="text-primary read-more" data-description="' . htmlspecialchars($comments) . '" data-description-label="'.ucwords($name).': Comments" title="Read More">Read More</a>';
                    } else {
                        return $comments;
                    }
                })
                ->addColumn('appraisal_type', function ($row) {
                    return \Enum::PERIOD[$row->period]['label'];
                })
                ->addColumn('score', function ($row) {
                    return round($row->rating, 1) . '/10';
                })
                ->rawColumns(['remarks'])
                ->make(true);
        }
    }

    public function performanceAppraisalCreate(Request $request)
    {
        $viewData = [];
        $departmentId = auth()->user()->department_id;
        $roleId = auth()->user()->role;

        if(($departmentId == 1) && ($roleId == 1)) {
            $viewData['employees'] = User::all();
        } elseif(($departmentId == 2) && ($roleId == 7)) {
            $viewData['employees'] = User::whereIn('role', [8, 4])->get();
        }  elseif(($departmentId == 2) && ($roleId == 8)) {
            $viewData['employees'] = User::whereIn('role', [4])->get();
        } else {
            $viewData['employees'] = collect();
        }

        return view('hr.performance-appraisal-create', $viewData);
    }

    public function performanceAppraisalStore(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id'    => 'required|exists:users,id',
            'rating'         => 'required|numeric|max:10',
            'period'        => ['required', 'string', Rule::in(array_keys(Enum::PERIOD))],
            'comments'  => 'nullable|string|max:1000',
        ]);
    
        $validatedData['reviewer_id']   = auth()->id();
        Appraisal::create($validatedData);

        return redirect()->route('hr.employee.management')->with('success', 'Appraisal added successfully.');
    }

    public function exitProcess(Request $request)
    {
        app(RoleMiddleware::class)->handle($request, function () {}, 1, 3, 4);
         return view('hr.exit-process');
    }

    // ------------resignation---------------
    public function resignationIndex(Request $request)
    {
        if ($request->ajax()) {
            $roleId = auth()->user()->role;
            $employeeId = auth()->user()->id;

            if(in_array($roleId, [4])) {
                $resignation = Resignation::with(['employee', 'approver'])->where('employee_id', $employeeId);
            } else {
                $resignation = Resignation::with(['employee', 'approver']);
            }

            if (!$request->has('order')) {
                $resignation->orderByDesc('id');
            }

            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $resignation->where(function ($query) use ($search) {
                    $query->whereHas('employee', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    })
                    ->orWhereHas('approver', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
                });
            }

            return DataTables::of($resignation->limit(5))
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    $name = $row->employee?->full_name ?? $row->employee->first_name . ' ' . $row->employee->last_name;
                    return ucwords($name);
                })
                ->orderColumn('employee_name', function ($query, $order) {
                    $query->join('users as u', 'resignations.employee_id', '=', 'u.id')
                        ->orderByRaw("CONCAT(u.first_name, ' ', u.last_name) {$order}");
                })
                ->addColumn('approved_by', function ($row) {
                    if($row->approver) {
                        $name = $row->approver?->full_name ?? $row->approver->first_name . ' ' . $row->approver->last_name;
                        return ucwords($name);
                    }
                    return '';
                })
                ->orderColumn('approved_by', function ($query, $order) {
                    $query->join('users as u', 'resignations.employee_id', '=', 'u.id')
                        ->orderByRaw("CONCAT(u.first_name, ' ', u.last_name) {$order}");
                })
                ->editColumn('reason', function ($row) {
                    $reason = $row->reason;
                    $name = $row->employee?->full_name ?? $row->employee->first_name . ' ' . $row->employee->last_name;
                    if (strlen($reason) > 50) {
                        return '<span>' . substr($reason, 0, 15) . '...</span>
                                <a href="javascript:void(0)" class="text-primary read-more" data-description="' . htmlspecialchars($reason) . '" data-description-label="'.ucwords($name).': Reason For Leaving" title="Read More">Read More</a>';
                    } else {
                        return $reason;
                    }
                })
                ->editColumn('status', function ($row) {
                    $label = isset(\Enum::RESIGNATION_STATUS[$row->status]) 
                    ? \Enum::RESIGNATION_STATUS[$row->status]['label'] 
                    : '-';
                    $url = route('hr.resignation.update.status', [$row->id]);
                    $icon = "<a href='javascript:void(0);' class='edit-status' style='margin-left:8px;'>
                                <span class='mdi mdi-circle-edit-outline'></span>
                            </a>";
                    $addIcon = in_array(auth()->user()->role, [1, 3]) ? $icon : '';

                    return "
                        <div class='status-wrapper' data-id='{$row->id}' data-current-status='{$row->status}' data-module='resignation_status' data-url='{$url}' data-table-class='resignation-table' style='width:fit-content'>
                            <span class='status-text'>{$label}</span>
                            {$addIcon}
                        </div>
                    ";
                })
                ->editColumn('resignation_date', function ($row) {
                    return $row->resignation_date->format('jS M, Y');
                })
                ->editColumn('last_working_day', function ($row) {
                    return $row->last_working_day->format('jS M, Y');
                })
                ->rawColumns(['status', 'reason'])
                ->make(true);
        }
    }

    public function resignationCreate(Request $request)
    {
        $viewData = [];
        $viewData['employees'] = User::all();

        return view('hr.resignation.resignation-create', $viewData);
    }

    public function resignationStore(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id'    => 'required|exists:users,id',
            'rating'    => 'required|numeric|max:10',
            'resignation_date'    => 'required|date',
            'last_working_day'    => 'nullable|date',
            'reason'    => ['nullable', 'string', 'max:1000'],
        ]);
        
        $validatedData['reason'] = ucfirst($request->reason);
        $resignation = Resignation::create($validatedData);

        if ($resignation) {
            return redirect()->route('hr.exit.process')->with('success', 'Resignation added successfully.');
        }
        return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
    }

    public function resignationUpdateStatus(Request $request, $id)
    {
        $resignation = Resignation::find($id);
        if (!$resignation) {
            return redirect()->route('hr.exit.process')->with('error', 'Resignation not found.');
        }

        $validatedData = $request->validate([
            'status'    => ['required', 'string', Rule::in(array_keys(Enum::RESIGNATION_STATUS))],
        ]);
        $validatedData['approved_by'] = auth()->id();

        $resignation->update($validatedData);
        return response()->json([ 'status' => 'success', 'message' => 'Status updated successfully' ]);
    }

    // ------------asset-handover---------------
    public function assetHandoverIndex(Request $request)
    {
        if ($request->ajax()) {
            $roleId = auth()->user()->role;
            $employeeId = auth()->user()->id;

            if(in_array($roleId, [4])) {
                $assetHandover = AssetHandover::with(['employee'])->where('employee_id', $employeeId);
            } else {
                $assetHandover = AssetHandover::with(['employee']);
            }

            if (!$request->has('order')) {
                $assetHandover->orderByDesc('id');
            }

            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $assetHandover->where(function ($query) use ($search) {
                    $query->whereHas('employee', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    })
                    ->orWhere('asset_name', 'LIKE', "%{$search}%");
                });
            }

            return DataTables::of($assetHandover->limit(5))
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    $name = $row->employee?->full_name ?? $row->employee->first_name . ' ' . $row->employee->last_name;
                    return ucwords($name);
                })
                ->orderColumn('employee_name', function ($query, $order) {
                    $query->join('users as u', 'asset_handovers.employee_id', '=', 'u.id')
                        ->orderByRaw("CONCAT(u.first_name, ' ', u.last_name) {$order}");
                })
                ->editColumn('handover_status', function ($row) {
                    $label = isset(\Enum::ASSET_HANDOVER_STATUS[$row->handover_status]) 
                    ? \Enum::ASSET_HANDOVER_STATUS[$row->handover_status]['label'] 
                    : '-';
                    $url = route('hr.asset.handover.update.status', [$row->id]);
                    $icon = "<a href='javascript:void(0);' class='edit-status' style='margin-left:8px;'>
                                <span class='mdi mdi-circle-edit-outline'></span>
                            </a>";
                    $addIcon = in_array(auth()->user()->role, [1, 3]) ? $icon : '';

                    return "
                        <div class='status-wrapper' data-id='{$row->id}' data-current-status='{$row->handover_status}' data-module='asset_handover' data-url='{$url}' data-table-class='asset-handover-table' style='width:fit-content'>
                            <span class='status-text'>{$label}</span>
                            {$addIcon}
                        </div>
                    ";
                })
                ->editColumn('handover_date', function ($row) {
                    return $row->handover_date->format('jS M, Y');
                })
                ->rawColumns(['handover_status'])
                ->make(true);
        }
    }

    public function assetHandoverCreate(Request $request)
    {
        $viewData = [];
        $viewData['employees'] = User::all();

        return view('hr.asset-handover.asset-handover-create', $viewData);
    }

    public function assetHandoverStore(Request $request)
    { 
        $validatedData = $request->validate([
            'employee_id'    => 'required|exists:users,id',
            'handover_date'    => 'required|date',
            'handover_status'    => ['required', 'string', Rule::in(array_keys(Enum::ASSET_HANDOVER_STATUS))],
            'asset_name'    => ['required', 'string', 'max:255'],
        ]);
        
        $validatedData['asset_name'] = ucwords($request->asset_name);
        $assetHandover = AssetHandover::create($validatedData);

        if ($assetHandover) {
            return redirect()->route('hr.exit.process')->with('success', 'Asset Handover added successfully.');
        }
        return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
    }

    public function assetHandoverUpdateStatus(Request $request, $id)
    {
        $assetHandover = AssetHandover::find($id);
        if (!$assetHandover) {
            return redirect()->route('hr.exit.process')->with('error', 'Asset Handover not found.');
        }

        $validatedData = $request->validate([
            'status'    => ['required', 'string', Rule::in(array_keys(Enum::ASSET_HANDOVER_STATUS))],
        ]);
        $validatedData['handover_status'] = $request->status;

        $assetHandover->update($validatedData);
        return response()->json([ 'status' => 'success', 'message' => 'Status updated successfully' ]);
    }

    // ----------------salary-management-------------
    public function salaryManagementIndex(Request $request)
    {
        if ($request->ajax()) {
            $roleId = auth()->user()->role;
            $employeeId = auth()->user()->id;

            if(in_array($roleId, [4])) {
                $salaryPayment = SalaryPayment::with(['employee'])->where('employee_id', $employeeId);
            } else {
                $salaryPayment = SalaryPayment::with(['employee']);
            }

            if (!$request->has('order')) {
                $salaryPayment->orderByDesc('id');
            }

            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $salaryPayment->where(function ($query) use ($search) {
                    $query->whereHas('employee', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
                });            
            }
    
            return DataTables::of($salaryPayment->limit(5))
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    $name = $row->employee?->full_name ?? $row->employee->first_name . ' ' . $row->employee->last_name;
                    return ucwords($name);
                })
                ->orderColumn('employee_name', function ($query, $order) {
                    $query->join('users as u', 'salary_payments.employee_id', '=', 'u.id')
                        ->orderByRaw("CONCAT(u.first_name, ' ', u.last_name) {$order}");
                })
                ->editColumn('payment_mode', function ($row) {
                    $label = isset(\Enum::PAYMENT_MODE[$row->payment_mode])
                            ? \Enum::PAYMENT_MODE[$row->payment_mode]['label']
                            : '-';
                    return $label;
                })
                ->editColumn('month', function ($row) {
                    return Carbon::createFromFormat('m', $row->month)->format('F');
                })
                ->make(true);
        }
    }

    public function salaryManagementCreate(Request $request)
    {
        $viewData = [];
        $viewData['employees'] = User::all();

        return view('hr.salary-management.salary-management-create', $viewData);
    }

    public function salaryManagementStore(Request $request)
    { 
        $validatedData = $request->validate([
            'employee_id'    => 'required|exists:users,id',
            'year'    => 'required|digits:4|integer|max:' . date('Y'),
            'month'    => 'required|integer|between:1,12',
            'gross_salary'    => 'required|numeric|min:0',
            'payment_mode'    => ['required', 'string', Rule::in(array_keys(Enum::PAYMENT_MODE))],
        ]);
        
        $salaryPayment = SalaryPayment::create($validatedData);

        if ($salaryPayment) {
            return redirect()->route('hr.payroll.management')->with('success', 'Record added successfully.');
        }
        return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
    }

    // -----------------employee leave balance--------------------
    public function leaveBalanceIndex(Request $request)
    {
        if ($request->ajax()) {
            $roleId = auth()->user()->role;
            $employeeId = auth()->user()->id;

            if(in_array($roleId, [4])) {
                $leaveBalance = LeaveBalance::with(['employee', 'leaveType'])->where('employee_id', $employeeId);
            } else {
                $leaveBalance = LeaveBalance::with(['employee', 'leaveType']);
            }
            // $leaveBalance = User::with(['leaveBalances.leaveType']);

            if (!$request->has('order')) {
                $leaveBalance->orderByDesc('id');
            }

            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $leaveBalance->where(function ($query) use ($search) {
                    $query->whereHas('employee', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
                });            
            }
    
            return DataTables::of($leaveBalance)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    $name = $row->employee?->full_name ?? $row->employee->first_name . ' ' . $row->employee->last_name;
                    return ucwords($name);
                })
                ->orderColumn('employee_name', function ($query, $order) {
                    $query->join('users as u', 'leave_balances.employee_id', '=', 'u.id')
                        ->orderByRaw("CONCAT(u.first_name, ' ', u.last_name) {$order}");
                })
                ->addColumn('leave_type', function ($row) {
                    $leaveType = $row->leaveType ? $row->leaveType->name : 'NA';
                    return ucwords($leaveType);
                })
                // ->addColumn('leave_types', function ($user) {
                //     $badges = '';
                //     foreach ($user->leaveBalances as $balance) {
                //         $type = ucwords($balance->leaveType->name ?? '');
                //         $remaining = max(0, $balance->opening_balance - $balance->used_days);
                //         $badges .= "<span class='badge bg-primary me-1 mb-1'>{$type}: {$remaining}</span>";
                //     }
                //     return $badges;
                // })
                // ->rawColumns(['leave_types'])
                ->make(true);
        }
    }
    
    // -----------------Attendance--------------------
    public function attendance(Request $request)
    {
        app(RoleMiddleware::class)->handle($request, function () {}, 1, 3, 4);
        
        $roleId = auth()->user()->role;
        $employeeId = auth()->user()->id;

        if(in_array($roleId, [4])) {
            $viewData['employees'] = User::where('id', $employeeId)->get();
        } else {
            $viewData['employees'] = User::all();
        }
        $viewData['leaveTypes'] = LeaveType::all();

        return view('hr.attendance', $viewData);
    }

    public function attendanceAjax(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfMonth();

        $roleId = auth()->user()->role;
        $employeeId = auth()->user()->id;

        if(in_array($roleId, [4])) {
            $attendances = Attendance::with(['employee', 'employee.employeeBreaks'])->whereBetween('date', [
                $startDate->toDateString(),
                $endDate->toDateString()
            ])->where('employee_id', $employeeId);
        } else {
            $attendances = Attendance::with(['employee', 'employee.employeeBreaks'])->whereBetween('date', [
                $startDate->toDateString(),
                $endDate->toDateString()
            ]);
        }

        if (!$request->has('order')) {
            $attendances->orderByDesc('id');
        }

        return DataTables::of($attendances)
                ->filter(function ($query) use ($request) {
                    if ($search = $request->input('search.value')) {
                        $query->whereHas('employee', function ($q) use ($search) {
                            $q->where('first_name', 'LIKE', "%{$search}%")
                                ->orWhere('last_name', 'LIKE', "%{$search}%")
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                        });
                    }
                })
                ->addColumn('user_name', function ($row) {
                    $name = $row->employee ? $row->employee->first_name . ' ' . $row->employee->last_name : '';
                    return ucwords($name);
                })
                ->orderColumn('user_name', function ($query, $order) {
                    $query->join('users as u', 'attendances.employee_id', '=', 'u.id')
                        ->orderByRaw("CONCAT(u.first_name, ' ', u.last_name) {$order}");
                })
                ->editColumn('punch_in', function ($row) {
                    $punchInTime = Carbon::parse($row->punch_in);
                    $threshold = $punchInTime->copy()->setTime(11, 0, 0);

                    $class = $punchInTime->greaterThan($threshold) ? 'badge rounded-pill bg-label-danger' : 'badge rounded-pill bg-label-secondary';
                    return '<span class="' . $class . '">' . $punchInTime->format('h:i:s A') . '</span>';
                })
                ->editColumn('punch_out', function ($row) {
                    $punchOutTime = Carbon::parse($row->punch_out);
                    $threshold = $punchOutTime->copy()->setTime(20, 0, 0);

                    $class = $punchOutTime->greaterThan($threshold) ? 'badge rounded-pill bg-label-success' : 'badge rounded-pill bg-label-secondary';
                    return '<span class="' . $class . '">' . $punchOutTime->format('h:i:s A') . '</span>';
                })
                ->addColumn('break', function ($row) {
                    // $totalBreakMinutes = $row->employee->employeeBreaks->where('date', $row->date)->sum('duration');
                    // return $totalBreakMinutes . ' min';
                    $breaks = $row->employee->employeeBreaks->where('date', $row->date);
                    $totalBreakMinutes = $breaks->sum('duration');

                    $breakDetails = $breaks->map(function ($break) {
                        return Carbon::parse($break->start_time)->format('h:i:s A') . ' - ' . Carbon::parse($break->end_time)->format('h:i:s A');
                    })->implode('<br>');

                    $escapedBreaks = htmlspecialchars($breakDetails, ENT_QUOTES, 'UTF-8');

                    return '<button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="popover" data-bs-html="true" data-bs-title="Total Breaks" data-bs-placement="left" data-bs-content="' . $escapedBreaks . '">' . $totalBreakMinutes . ' min</button>';
                })
                ->rawColumns(['punch_in', 'punch_out', 'break'])
                ->make(true);
    }

    public function attendanceRecords(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfDay()->format('m/d/Y H:i'));
        $endDate = $request->input('end_date', now()->endOfDay()->format('m/d/Y H:i'));

        $attendanceRecords = $this->attendanceService->getAttendanceBetweenDates($startDate, $endDate);

        return view('attendance.index', compact('attendanceRecords'));
    }

    public function getCalendarEvents(Request $request)
    {
        $employeeId = $request->employee_id;
        $year = $request->year;

        $attendances = Attendance::where('id', $employeeId)
            ->whereYear('date', $year)
            ->get();

        $events = $attendances->map(function ($att) {
            $statusColors = [
                'Present' => '#28a745',
                'Absent' => '#dc3545',
                'Leave' => '#ffc107',
            ];

            return [
                'title' => $att->status,
                'start' => $att->date,
                'color' => $statusColors[$att->status] ?? '#6c757d',
            ];
        });

        return response()->json($events);
    }

    public function attendanceCalendarGrid(Request $request)
    {
        $employeeId = $request->employee_id;
        $year = $request->year;

        $status = Enum::LEAVE_STATUS['approved']['index'];
        $leaves = LeaveManagementModel::where(['user_id' => $employeeId, 'status' =>  $status])->whereYear('updated_at', $year)->get();

        $data = [];
        foreach ($leaves as $leave) {
            $start = Carbon::parse($leave->leave_from);
            $end = Carbon::parse($leave->leave_to);

            // Loop through each day in the leave range
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $data[$date->toDateString()] = ['bg_color' => $leave->leaveTypes?->bg_color, 'font_color' => $leave->leaveTypes?->font_color];
            }
        }

        $holidays = Holiday::whereYear('date', $year)->get();
        foreach($holidays as $holiday) {
            $date = Carbon::parse($holiday->date);
            $data[$date->toDateString()] = ['holiday_name' => $holiday->name, 'bg_color' => '#dc3545', 'font_color' => '#ffffff'];
        }
        // dd(2, $data);

        return response()->json($data);
    }

    public function fetchAttendance(Request $request)
    {
        $employeeId = $request->employee_id;
        $year = $request->year;

        $attendance = Attendance::where('id', $employeeId)
            ->whereYear('date', $year)
            ->get();

        $attendanceMap = [];
        foreach ($attendance as $record) {
            $status = $record->punch_in ? 'Present' : 'Absent';
            $attendanceMap[Carbon::parse($record->date)->format('Y-m-d')] = $status;
        }
    
        return response()->json([
            'attendance' => $attendanceMap,
        ]);
    }


}