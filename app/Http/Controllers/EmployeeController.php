<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use App\Models\ReportingPerson;
use App\Models\UserMeta;
use Illuminate\Support\Facades\Mail;
use App\Models\UserRoleModel;
use App\Models\Department;
use App\Models\LeaveManagementModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Projects\ProjectModel;
use App\DataTables\EmployeeDataTable;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Projects\ProjectBugsModel;
use App\Models\Projects\ProjectTasksModel;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(EmployeeDataTable $dataTable)
    {
        $user = auth()->user();
        
        if ($user->role == 1 ) {
            // Role Admin Show all employees
            $users = User::all();
        }
        elseif ($user->role == 3) {
            // Role HR Show all employees except Admin 
            $users = User::whereNotIn('role', [1])->get();
        }
        else {
            // For other roles, only show the logged-in user
            $users = User::where('id', $user->id)->get();
        }
        
        return $dataTable->render('employee.list', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $departments = Department::all();
        return view('employee.add', ['roles' => UserRoleModel::all(),'users' => $users,'departments' => $departments,]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email:rfc,dns|max:255|unique:users,email',
            'role' => 'required',
            'status' => 'required',
            'password' => 'required|string|min:8|same:confirm_password',
            'confirm_password' => 'required',
            'doj' => 'required|date', 
            'skills' => 'nullable|string', 
            'department_id' => 'required',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Generate unique employee ID
        do {
            $employeeId = 'digitex_' . str_pad(mt_rand(1, 999999), 4, '0', STR_PAD_LEFT);
        } while (User::where('emp_id', $employeeId)->exists());
    
        // Create new user
        $user = new User();
        $user->emp_id = $employeeId;
        $user->first_name = $request->input('first_name', 'Digitex');
        $user->last_name = $request->input('last_name', 'Technologies');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->role = $request->input('role');
        $user->status = $request->input('status');
        $user->password = Hash::make($request->input('password'));
        $user->date_registered = now();
        $user->department_id = $request->input('department_id');
        $user->save();
    
        // Assign reporting person if selected
        if ($request->filled('reporting_user_id')) {
            ReportingPerson::create([
                'emp_id' => $user->id,
                'reporting_person_id' => $request->input('reporting_user_id'),
            ]);
        }
    
        // Use MetaHelper to prepare metadata
        $metaData = [];
        \App\Helpers\MetaHelper::addMetaIfFilled($request, $metaData, 'skills');
        \App\Helpers\MetaHelper::addMetaIfFilled($request, $metaData, 'doj');
        \App\Helpers\MetaHelper::addMetaIfFilled($request, $metaData, 'experience');
        \App\Helpers\MetaHelper::addMetaIfFilled($request, $metaData, 'certifications');
        \App\Helpers\MetaHelper::addMetaIfFilled($request, $metaData, 'working_days');
    
        // Save metadata
        foreach ($metaData as $key => $value) {
            UserMeta::create([
                'user_id' => $user->id,
                'meta_key' => $key,
                'meta_value' => $value,
            ]);
        }
    
        return redirect(route('employee.list'))->withSuccess('Employee added successfully');
    }

    public function getReportingPersons($department_id)
    {
        $users = User::where('department_id', $department_id)->select('id', 'first_name', 'last_name')->get();
        return response()->json($users);
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $departments = Department::all();
        // dd($user);
        $roles = UserRoleModel::all();
        $userMeta = UserMeta::where('user_id', $id)->get()->pluck('meta_value', 'meta_key');
        // $reportingPersons = User::where('id', '!=', $id)->get(); 
        $reportingPersons = User::where('id', '!=', $id)->where('department_id', $user->department_id)->get();
        $assignedReportingPersonId = ReportingPerson::where('emp_id', $user->id)->first()->reporting_person_id ?? null;
        // dd($userMeta);
        if (isset($userMeta['certifications'])) {
            $userMeta['certifications'] = json_decode($userMeta['certifications'], true);
        }
        if (isset($userMeta['working_days'])) {
            $userMeta['working_days'] = json_decode($userMeta['working_days'], true);
        }
        if (isset($userMeta['experience'])) {
            $userMeta['experience'] = json_decode($userMeta['experience'], true);
        }
        if (isset($userMeta['doj'])) {
            $userMeta['doj'] = json_decode($userMeta['doj'], true);
        }    
        $skills = $userMeta['skills'] ?? '';
        if (is_string($skills)) {
            $skillsArray = explode(',', $skills);
        } else {
            $skillsArray = json_decode($skills, true) ?? [];
        }
        return view('employee.edit', compact('user', 'roles', 'userMeta','reportingPersons','assignedReportingPersonId','departments'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required',
            'status' => 'required',
            'password' => 'nullable|string|min:8|same:confirm_password',
            'confirm_password' => 'nullable',
            'reporting_to' => 'required|exists:users,id',
            'department_id' => 'required',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Update basic user fields
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->role = $request->input('role');
        $user->status = $request->input('status');
        $user->department_id = $request->input('department_id');
    
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
    
        $user->save();
    
        // Update or create reporting person
        ReportingPerson::updateOrCreate(
            ['emp_id' => $user->id],
            ['reporting_person_id' => $request->input('reporting_to')]
        );
    
        // Handle meta fields
        $metaFields = ['skills', 'experience', 'certifications', 'working_days', 'doj'];
        foreach ($metaFields as $key) {
            if ($request->has($key)) {
                $value = $request->input($key);
                if (!empty($value)) {
                    // Update or create the meta entry
                    UserMeta::updateOrCreate(
                        ['user_id' => $user->id, 'meta_key' => $key],
                        ['meta_value' => $value]
                    );
                } else {
                    // Delete the meta entry if the field is empty
                    UserMeta::where('user_id', $user->id)->where('meta_key', $key)->delete();
                }
            }
        }
    
        return redirect(route('employee.list'))->withSuccess('Employee updated successfully');
    }

    // For profile name and phone update
    public function inlineUpdate(Request $request, $id)
    {
        if (auth()->id() != $id && auth()->user()->role != 1 && auth()->user()->role != 3) {
            abort(403); // Forbidden
        }
    
        $user = User::findOrFail($id);
    
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);
    
        if ($request->has('first_name') && $request->has('last_name')) {
            $user->first_name = $validated['first_name'];
            $user->last_name = $validated['last_name'];
        }
    
        if ($request->has('phone')) {
            $user->phone = $validated['phone'];
        }
    
        $user->save();
    
        return back()->withSuccess('Profile updated successfully');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        // Retrieve user meta information
        $userMeta = UserMeta::where('user_id', $id)->get()->pluck('meta_value', 'meta_key');
        if (isset($userMeta['experience'])) {
            $userMeta['experience'] = json_decode($userMeta['experience'], true);
        }
        if (isset($userMeta['certifications'])) {
            $userMeta['certifications'] = json_decode($userMeta['certifications'], true); 
        }
        if (isset($userMeta['working_days'])) {
            $userMeta['working_days'] = json_decode($userMeta['working_days'], true);
        }
        if (isset($userMeta['skills'])) {
            $userMeta['skills'] = json_decode($userMeta['skills'], true);
        }
        if (isset($userMeta['doj'])) {
            $userMeta['doj'] = json_decode($userMeta['doj'], true);
        }

        // measure quality of employee
        $viewData = [];
        $viewData['totalTask'] = ProjectTasksModel::whereHas('taskAssigns', function($q) use($id) {
                                    $q->where('user_id', $id);
                                })->count();
        $viewData['totalCompletedTask'] = ProjectTasksModel::whereHas('taskAssigns', function($q) use($id) {
                                            $q->where('user_id', $id);
                                        })->where('status', 9)->count();
        $viewData['upcomingDeadline'] = ProjectTasksModel::whereHas('taskAssigns', function($q) use($id) {
                                            $q->where('user_id', $id);
                                        })
                                        ->whereBetween('due_date', [
                                            Carbon::now(),
                                            Carbon::now()->addMonth()
                                        ])
                                        ->count();
    
        // project's task completion chart
        $viewData = array_merge($viewData, $this->projectsTaskCompletion(request(), $id));

        // dd($viewData);
        return view('employee.profile', compact('user', 'userMeta'), $viewData);
    }

    public function getMonthlyCompletedProjects(Request $request)
    {
        $year = $request->year ?? now()->year;
        $empId = $request->empId;

        $monthlyCounts = ProjectModel::whereHas('projectAssignes', function($q) use ($empId) {
                                $q->where('user_id', $empId);
                            })
                            ->selectRaw('MONTH(updated_at) as month, COUNT(*) as count')
                            ->whereYear('updated_at', $year)
                            ->where('status', 7) 
                            ->groupByRaw('MONTH(updated_at)')
                            ->pluck('count', 'month');

        $monthlyData = array_fill(1, 12, 0);
        foreach ($monthlyCounts as $month => $count) {
            $monthlyData[$month] = $count;
        }

        return response()->json([
            'labels' => [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ],
            'data' => array_values($monthlyData)
        ]);
    }

    public function getMonthlyCompletedTasks(Request $request)
    {
        $year = $request->year ?? now()->year;
        $empId = $request->empId;

        $monthlyCounts = ProjectTasksModel::whereHas('taskAssigns', function($q) use ($empId) {
                                $q->where('user_id', $empId);
                            })
                            ->selectRaw('MONTH(updated_at) as month, COUNT(*) as count')
                            ->whereYear('updated_at', $year)
                            ->where('status', 9) 
                            ->groupByRaw('MONTH(updated_at)')
                            ->pluck('count', 'month');

        $monthlyData = array_fill(1, 12, 0);
        foreach ($monthlyCounts as $month => $count) {
            $monthlyData[$month] = $count;
        }

        return response()->json([
            'labels' => [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ],
            'data' => array_values($monthlyData)
        ]);
    }

    public function projectsTaskCompletion(Request $request, $empId) 
    {
        $viewData = [];
        $viewData['projects'] = ProjectModel::whereHas('projectAssignes', function($q) use ($empId) {
                                    $q->where('user_id', $empId);
                                })->with('tasks', function($q) use ($empId) {
                                    $q->whereHas('taskAssigns', function($query) use ($empId) {
                                        $query->where('user_id', $empId);
                                    });
                                })->latest()->get();
        $firstProject = $request->ajax() 
                        ? $viewData['projects']->where('id', $request->get('project_id', null))->first() 
                        : $viewData['projects']->first();
        $allTaskName = $firstProject ? $firstProject->tasks->pluck('task_name')->toArray() : [];
        $EstimatedTime = $firstProject ? $firstProject->tasks->pluck('estimated_time')->toArray() : [];
        // $allExtraTime = $firstProject ? $firstProject->tasks->pluck('name')->toArray() : [];
        $allExtraTime = $firstProject ? $firstProject->tasks->map(fn($task) => rand(10, 60))->toArray() : [];
        $viewData['displayedProject'] = [
                        'allTaskName' => $allTaskName,                                
                        'allEstimatedTime' => $EstimatedTime,                              
                        'allExtraTime' => $allExtraTime                               
                    ];
    
        if($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $viewData['displayedProject']
            ]);
        }
        return $viewData;
    }

    public function getLeaveRecords(Request $request)
    {
        $userId = $request->input('user_id'); 
        $year = $request->input('year', now()->year); 
        $month = $request->input('month', now()->month); 

        $leaveRecords = LeaveManagementModel::where('user_id', $userId)
            ->whereYear('leave_from', $year)
            ->whereMonth('leave_from', $month)
            ->get();

        $leaveRecord = $leaveRecords->first();
        $leaveDays = $leaveRecord ? round($leaveRecord->leave_from->diffInDays($leaveRecord->leave_to) + 1) : 0;
        $totalDaysInMonth = Carbon::create($year, $month)->daysInMonth;
        $progressDegree = ($leaveDays / $totalDaysInMonth) * 360;

        return response()->json([
            'success' => true,
            'leaveDays' => $leaveDays,
            'progressDegree' => $progressDegree
        ]);
    }

    public function getIncentives($empId)
    {
        $taskQuery = ProjectTasksModel::whereHas('taskAssigns', function ($q) use ($empId) {
            $q->where('user_id', $empId);
        });
    
        $totalTasks = (clone $taskQuery)->count();
    
        $completedTasks = (clone $taskQuery)
            ->where('status', 9) 
            ->whereNotNull('updated_at')
            ->get();
    
        $bonusTasks = [];
        $promotionTasks = [];
    
        foreach ($completedTasks as $task) {
            $dueDate = Carbon::parse($task->due_date)->toDateString();
            $completedAt = Carbon::parse($task->updated_at)->toDateString();
    
            if ($completedAt === $dueDate) {
                $bonusTasks[] = $task;
            } elseif ($completedAt < $dueDate) {
                $promotionTasks[] = $task;
            }
        }
    
        $bonusPercentage = $totalTasks > 0 ? (count($bonusTasks) / $totalTasks) * 100 : 0;
        $promotionPercentage = $totalTasks > 0 ? (count($promotionTasks) / $totalTasks) * 100 : 0;
    
        return response()->json([
            'bonusTaskCount' => count($bonusTasks),
            'promotionTaskCount' => count($promotionTasks),
            'totalTasks' => $totalTasks,
            'bonusTaskPercentage' => round($bonusPercentage, 2),
            'promotionTaskPercentage' => round($promotionPercentage, 2),
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Delete all associated user_meta records
        UserMeta::where('user_id', $id)->delete();
        // Delete the user
        $user->delete();
        return redirect(route('employee.list'))->withSuccess('Employee deleted successfully');
    }
    
    public function getOrgChart($id)
    {
        $employee = User::findOrFail($id);
        $currentUser = auth()->user();
        // dd($currentUser);
        if ($currentUser->role == 1 || $currentUser->role == 3 ) {
            $parentData = $this->getParent($employee);
        }
        else{
             $parentData = $this->getParent($employee);
        }

        $orgData = $this->buildOrgChartData($employee);
    
        if (!empty($parentData['children'])) {
            $lastParent = &$parentData;
            while (!empty($lastParent['children'])) {
                $lastParent = &$lastParent['children'][0];
            }
            $lastParent['children'][] = $orgData;
        } else {
            $parentData['children'][] = $orgData;
        }
    
        return response()->json($parentData);
    }
    // Get Immediate Parents
    private function getImmediateParent($employee)
    {
        $parentRelation = ReportingPerson::where('emp_id', $employee->id)->first();
        if ($parentRelation) {
            $parent = User::find($parentRelation->reporting_person_id);
            if ($parent) {
                return [
                    'id' => (string) $parent->id,
                    'name' => $parent->first_name . ' ' . $parent->last_name,
                    'title' => $parent->role_details->name ?? 'Supervisor',
                    'children' => [] 
                ];
            }
        }
    
        // Return employee if no parent is found
        return [
            'id' => (string) $employee->id,
            'name' => $employee->first_name . ' ' . $employee->last_name,
            'title' => $employee->role_details->name ?? 'Employee',
            'children' => []
        ];
    }

    private function getParent($employee)
    {
        $parents = [];
        $this->buildParentHierarchy($employee, $parents);
        
        // If parent(s) found, Get from top level
        if (!empty($parents)) {
            $topParent = array_shift($parents);
            $currentParent = &$topParent;
            
            foreach ($parents as $parent) {
                $currentParent['children'] = [$parent];
                $currentParent = &$currentParent['children'][0];
            }
            
            return $topParent;
        }
    
        // Return employee if no parent is found
        return [
            'id' => (string) $employee->id,
            'name' => $employee->first_name . ' ' . $employee->last_name,
            'title' => $employee->role_details->name ?? 'Employee',
            'children' => []
        ];
    }

    private function buildParentHierarchy($employee, &$parents)
    {
        $parentRelation = ReportingPerson::where('emp_id', $employee->id)->first();
        if ($parentRelation) {
            $parent = User::find($parentRelation->reporting_person_id);
            if ($parent) {
                $parentData = [
                    'id' => (string) $parent->id,
                    'name' => $parent->first_name . ' ' . $parent->last_name,
                    'title' => $parent->role_details->name ?? 'Supervisor',
                    'children' => []
                ];
                array_unshift($parents, $parentData);
                $this->buildParentHierarchy($parent, $parents);
            }
        }
    }

    private function buildOrgChartData($employee)
    {
        $children = ReportingPerson::where('reporting_person_id', $employee->id)->get();
        // dd($children);
        $childData = [];
        foreach ($children as $child) {
            $childUser = User::find($child->emp_id);
            if ($childUser) {
                $childData[] = $this->buildOrgChartData($childUser);
            }
        }
    
        return [
            'id' => (string) $employee->id,
            'name' => $employee->first_name . ' ' . $employee->last_name,
            'title' => $employee->role_details->name ?? 'Employee',
            'children' => $childData
        ];
    }
    
    public function taskCompletionChart(Request $request)
    {
        $empId = $request->emp_id;
        if(!$empId) {
            return response()->json([
                'success' => false,
                'message' => 'Employee ID is required.'
            ], 400);
        }

        $employee = User::find($empId);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        $tasksQuery = ProjectTasksModel::whereHas('taskAssigns', function ($q) use ($empId) {
                            $q->where('user_id', $empId);
                        });
    
        $assignedTaskCount = (clone $tasksQuery)->count();
        $completedTaskCount = (clone $tasksQuery)->where('status', 9)->count();
        
        return response()->json([
            'success' => true,
            'name' => $employee->full_name,
            'assignedTasks' => $assignedTaskCount,
            'completedTasks' => $completedTaskCount
        ]);
    }

    public function assignedCompletedTasks(Request $request)
    {
        $empId = $request->emp_id;
        if(!$empId) {
            return response()->json([
                'success' => false,
                'message' => 'Employee ID is required.'
            ], 400);
        }

        if ($request->ajax()) {
            $taskLists = ProjectTasksModel::whereHas('assigns', function ($query) use ($empId) {
                                $query->where('user_id', $empId);
                            })->with(['assigns', 'statusDetail']);

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $taskLists->where(function ($q) use ($search) {
                    $q->where('task_name', 'LIKE', "%{$search}%");
                });
            }

            return Datatables::of($taskLists->limit(5))
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->statusDetail) {
                        return '<span class="badge" style="background-color:' . $row->statusDetail->bg_color . '; color:' . $row->statusDetail->font_color . ';">' . $row->statusDetail->status_name . '</span>';
                    }
                    return '<span class="badge bg-secondary">Unknown</span>';
                })
                ->rawColumns(['status'])
                ->make(true);
        }
    }
    
}
