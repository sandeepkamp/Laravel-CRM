<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use DB;

use DateTime;

use DateInterval;

use DatePeriod;

use Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Constants\Enum;
use App\Models\Projects\ClientModel;
use App\Models\Projects\ProjectModel;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Projects\ProjectTasksModel;
use App\Models\Projects\ProjectAssignModel;
use App\Models\Projects\ProjectTaskAssignModel;
use Illuminate\Support\Facades\Validator;



class DashboardController extends Controller

{

    public function index(Request $request)
    {
        $role = auth()->user()->role;
        $viewData = [];
        $today = Carbon::today();

        $pendingLeavesCount = auth()->user()->leaveRequests()
                                ->where('status', 0)
                                // ->whereDate('leave_from', '<=', $today)
                                ->whereDate('leave_to', '>=', $today)
                                ->count();

        $approvedLeavesCount = auth()->user()->leaveRequests()
                                ->where('status', 1)
                                // ->whereDate('leave_from', '<=', $today)
                                ->whereDate('leave_to', '>=', $today)
                                ->count();

        $rejectedLeavesCount = auth()->user()->leaveRequests()
                                ->where('status', 2)
                                // ->whereDate('leave_from', '<=', $today)
                                ->whereDate('leave_to', '>=', $today)
                                ->count();

        $viewData['leaveStatusCounts'] = [
            'Pending' => $pendingLeavesCount,
            'Approved' => $approvedLeavesCount,
            'Rejected' => $rejectedLeavesCount,
        ];

        $viewData['projects'] = ProjectModel::select('id', 'project_name')->orderBy('id', 'DESC')->get();

        switch ($role) {
            case 1:
                $viewData['dashboardTitle'] = 'Admin Dashboard';
                $userQuery = User::query()->where('status', 1)->distinct('id');
                $projectQuery = ProjectModel::query();

                // statistics
                $viewData['statisticsTitle'] = 'Project Overview';
                $viewData['totalProjectsCount'] = (clone $projectQuery)->count();
                $viewData['activeUsersCount'] = (clone $userQuery)->whereHas('projectAssigned')->count('id');
                $viewData['totalUsersCount'] = (clone $userQuery)->count();
                $viewData['totalClientsCount'] = ClientModel::count();

                // projects completion rate
                $completedProjects = (clone $projectQuery)->whereIn('status', [7, 8])->count(); // 7=>Completed, 8=>Hannded Over to Client
                $inProgressProjects = (clone $projectQuery)->whereIn('status', [1, 2, 3, 4, 5, 6])->count(); // In Progress
                $pendingProjects = (clone $projectQuery)->whereIn('status', [0])->count(); // 0=>Not Started
                $viewData['projectLabelValues'] = [$completedProjects, $inProgressProjects, $pendingProjects];
                
                // resource utilization
                $activeUsers = (clone $userQuery)->whereHas('projectAssigned')->count('id');
                $inActiveUsers = (clone $userQuery)->doesntHave('projectAssigned')->count('id');
                $utilizationRate = round(($activeUsers / ($activeUsers + $inActiveUsers)) * 100, 2);
                $viewData['resourcesLabelValues'] = [$activeUsers, $inActiveUsers, $utilizationRate];

                return view('dashboard', $viewData); // admin dashboard route
                break;

            case 2:

                return redirect(route('sales_dashboard')); // change accroding to the role specific dashboard route

                break;

            case 3:

                return view('dashboard'); // change accroding to the role specific dashboard route

                break;

            case 4:
                $viewData['dashboardTitle'] = 'Team Member Dashboard';
                // statistics
                $mergedArr = array_merge($viewData, $this->getStatistics());
                
                return view('dashboard', $mergedArr); // team member dashboard route
                break;

            case 5:

                return view('dashboard'); // change accroding to the role specific dashboard route

                break;

            case 6:

                return view('dashboard'); // change accroding to the role specific dashboard route

                break;

            case 7:
                $viewData['dashboardTitle'] = 'Project Manager Dashboard';
                // statistics
                $viewData['statisticsTitle'] = 'Project Overview';
                $projectIds = ProjectAssignModel::where('user_id', auth()->id())->pluck('project_id')->unique()->toArray();
                $viewData['totalProjectsCount'] = ProjectModel::whereHas('projectAssignes', function ($query) {
                        $query->where('user_id', auth()->id());
                    })->count();
                $viewData['activeUsersCount'] = User::whereHas('projectAssigned', function ($query) {
                        $query->whereHas('project', function ($subQuery) {
                            $subQuery->whereHas('projectAssignes', function ($innerQuery) {
                                $innerQuery->where('user_id', auth()->id());
                            });
                        });
                    })->where('status', 1)->distinct()->count('id');
                $viewData['totalUsersCount'] = User::count();

                // customizable analytics widgets for projects
                $completedProjects = ProjectModel::whereHas('projectAssignes', function ($query) {
                        $query->where('user_id', auth()->id());
                    })->whereIn('status', [7, 8])->count(); // 7=>Completed, 8=>Hannded Over to Client
                $inProgressProjects = ProjectModel::whereHas('projectAssignes', function ($query) {
                        $query->where('user_id', auth()->id());
                    })->whereIn('status', [1, 2, 3, 4, 5, 6])->count(); // In Progress
                $pendingProjects = ProjectModel::whereHas('projectAssignes', function ($query) {
                        $query->where('user_id', auth()->id());
                    })->whereIn('status', [0])->count(); // 0=>Not Started
                $viewData['projectLabelValues'] = [$completedProjects, $inProgressProjects, $pendingProjects];
                
                // customizable analytics widgets for resource allocation
                $activeUsers = User::whereHas('projectAssigned', function($q) use($projectIds) {
                        $q->whereIn('project_id', $projectIds);
                    })->where('status', 1)->distinct()->count('id');
                $inActiveUsers = User::doesntHave('projectAssigned')->where('status', 1)->distinct()->count('id');
                $utilizationRate = round(($activeUsers / ($activeUsers + $inActiveUsers)) * 100, 2);
                $viewData['resourcesLabelValues'] = [$activeUsers, $inActiveUsers, $utilizationRate];

                return view('dashboard', $viewData); // project manager dashboard route
                break;

            case 8:
                $viewData['dashboardTitle'] = 'Team Leader Dashboard';

                // Team performance metrics
                $projectIds = ProjectAssignModel::where('user_id', auth()->id())->pluck('project_id')->unique()->toArray();
                $viewData['teamPerformance'] = User::whereHas('role_details.department', function($department) {
                                                    $department->where('id', 2);                
                                                })
                                                ->whereHas('projectAssigned', function($q) use($projectIds) {
                                                    $q->whereIn('project_id', $projectIds);
                                                })
                                                ->where('status', 1)->with(['assignedTasks'])
                                                ->get()
                                                ->map(function ($member) {
                                                    $totalTasks = $member->assignedTasks->count();
                                                    $completedTasks = $member->assignedTasks->where('status', 9)->count();
                                                    $timeLogged = $member->assignedTasks->sum('estimated_time');
                                
                                                    return [
                                                        'name' => $member->first_name . ' ' . $member->last_name,
                                                        'completionRate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
                                                        'timeLogged' => round(($timeLogged / 60), 2)
                                                    ];
                                                });

                $mergedArr = array_merge($viewData, $this->getStatistics());

                return view('dashboard', $mergedArr); // team leader dashboard route
                break;

            default:

                return view('dashboard');

                break;

        }
    }

    private function getStatistics() : Array
    {
        $viewData = [];
        $viewData['statisticsTitle'] = 'Task Progress Overview';
        $viewData['completedTaskCount'] = ProjectTasksModel::whereHas('taskAssigns', function($q){
                                            $q->where('user_id', auth()->id());
                                        })->where('status', 9)->count();
        $viewData['inProgressTaskCount'] = ProjectTasksModel::whereHas('taskAssigns', function($q){
                                            $q->where('user_id', auth()->id());
                                        })->where('status', 4)->count();
        $viewData['pendingTaskCount'] = ProjectTasksModel::whereHas('taskAssigns', function($q){
                                            $q->where('user_id', auth()->id());
                                        })->where('status', 1)->count();
        return $viewData;
    }

    public function getMonthlyCompletedProjects(Request $request)
    {
        $year = $request->year ?? now()->year;

        $monthlyCounts = ProjectModel::selectRaw('MONTH(updated_at) as month, COUNT(*) as count')
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
        $role = auth()->user()->role;
        $departmentId = getDepartmentIdByRoleId($role);

        if(in_array($role, [1])) {
            $monthlyCounts = ProjectTasksModel::selectRaw('MONTH(updated_at) as month, COUNT(*) as count')
                            ->whereYear('updated_at', $year)
                            ->where('status', 9) 
                            ->groupByRaw('MONTH(updated_at)')
                            ->pluck('count', 'month');
        } elseif(in_array($role, [7, 8])) {
            $projectIds = ProjectAssignModel::where('user_id', auth()->id())->pluck('project_id')->unique()->toArray();
            $monthlyCounts = ProjectTasksModel::whereHas('taskAssigns', function($assigns) {
                                $assigns->where('user_id', auth()->id());                
                            })
                            ->selectRaw('MONTH(updated_at) as month, COUNT(*) as count')
                            ->whereIn('project_id', $projectIds)
                            ->whereYear('updated_at', $year)
                            ->where('status', 9) 
                            ->groupByRaw('MONTH(updated_at)')
                            ->pluck('count', 'month');
        } elseif(in_array($role, [4])) {
            $monthlyCounts = ProjectTasksModel::whereHas('taskAssigns', function($assigns) {
                                $assigns->where('user_id', auth()->id());                
                            })
                            ->selectRaw('MONTH(updated_at) as month, COUNT(*) as count')
                            ->whereYear('updated_at', $year)
                            ->where('status', 9) 
                            ->groupByRaw('MONTH(updated_at)')
                            ->pluck('count', 'month');
        } else {
            $monthlyCounts = ProjectTasksModel::where('id', 0)->get();
        }

        // Create 12-month array with default 0 values
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

    public function employeeProductivityList(Request $request)
    {
        if ($request->ajax()) {

            $role = auth()->user()->role;
            $query = User::query();

            if (in_array($role, [1, 7])) {
                $query->whereHas('role_details.department', function ($q) {
                    $q->where('id', 2);
                });
    
                if ($request->has('search') && !empty($request->input('search.value'))) {
                    $search = $request->input('search.value');
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
                }
    
                $query->with(['assignedTasks']);
            } else {
                return Datatables::of(collect())->make(true);
            }

            if (!$request->has('order')) {
                $query->orderByDesc('id');
            }

            return Datatables::of($query->take(5))
                ->addIndexColumn()
                ->addColumn('employee_name', function($row) {
                    $name = $row->full_name ?? ($row->first_name . ' ' . $row->last_name);
                    return ucwords($name);
                })
                ->orderColumn('employee_name', function ($query, $order) {
                    $query->orderByRaw("CONCAT(first_name, ' ', last_name) {$order}");
                })
                ->addColumn('task_assigned_count', function($row) {
                    return $row->assignedTasks->count();
                })
                ->addColumn('task_completed_count', function($row) {
                    return $row->assignedTasks->where('status', 9)->count();
                })
                ->addColumn('task_completed_on_time', function($row) {
                    return $row->assignedTasks->filter(function ($task) {
                        return $task->status == 9 &&
                               !empty($task->due_date) &&
                               !empty($task->updated_at) &&
                               Carbon::parse($task->updated_at)->lte(Carbon::parse($task->due_date));
                    })->count();
                })
                ->rawColumns(['employee_name', 'task_assigned_count', 'task_completed_count', 'task_completed_on_time'])
                ->make(true);
        }
    }

    /*
     * team leader & members assigned tasks list
     */

    public function assignedTaskList(Request $request)
    {
        if ($request->ajax()) {
            $role = auth()->user()->role;
            
            if(in_array($role, [1, 7])) {
                $taskLists = ProjectTasksModel::with(['assigns', 'statusDetail', 'project']);
                if (!$request->has('order')) {
                    $taskLists->orderByDesc('id');
                }
            } elseif(in_array($role, [8, 4])) {
                $taskLists = ProjectTasksModel::whereHas('assigns', function ($query) {
                                    $query->where('user_id', auth()->id());
                                })
                                ->with(['assigns', 'statusDetail', 'project']);

                if (!$request->has('order')) {
                    $taskLists->orderByDesc('id');
                }
            } else {
                $taskLists = collect();
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $taskLists->where(function ($q) use ($search) {
                    $q->where('task_name', 'LIKE', "%{$search}%");
                });
            }

            return Datatables::of($taskLists->limit(5))
                ->addIndexColumn()
                ->editColumn('task_name', function($row) {
                    return ucwords($row->task_name);
                })
                ->addColumn('project_name', function($row) {
                    return ucwords($row->project->project_name);
                })
                ->addColumn('assigns', function($row) {
                    $assigns = in_array(auth()->user()->role, [4])
                                ? $row->assigns()->where('user_id', auth()->id())->get()
                                : $row->assigns()->get();

                    return renderAssignBadge($assigns);
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('jS M, Y') : '';
                })
                ->editColumn('estimated_time', function($row) {
                    return intdiv($row->estimated_time, 60) . 'h ' . ($row->estimated_time % 60) . 'm';
                })
                ->addColumn('status', function ($row) {
                    if ($row->statusDetail) {
                        return '<span class="badge rounded-pill" style="background-color:' . $row->statusDetail->bg_color . '; color:' . $row->statusDetail->font_color . ';">' . $row->statusDetail->status_name . '</span>';
                    }
                    return '<span class="badge rounded-pill bg-label-secondary">Unknown</span>';
                })
                ->addColumn('deadline', function($row) {
                    if ($row->due_date) {
                        $now = Carbon::now();
                        $nextMonth = $now->copy()->addMonth();
                        if ($row->due_date->between($now, $nextMonth)) {
                            return '<span class="badge rounded-pill bg-label-warning">' . $row->due_date->format('jS M, Y') . '</span>';
                        }
                    }
                    return '';
                })
                ->addColumn('overdue', function($row) {
                    $deadline = config('constants.deadline');
                    $style = 'style="color:' . $deadline['color'] . '; background:' . $deadline['bg_color'] . ';"';
                    if ($row->due_date && ($row->due_date < Carbon::now())) {
                        return '<span class="badge rounded-pill"' . $style . '>' . $row->due_date->format('jS M, Y') . '</span>';
                    }
                    return '';
                })
                ->editColumn('priority', function($row) {
                    $priorityData = Enum::PRIORITIES[$row->priority] ?? null;
                    if ($priorityData) {
                        return '<span class="badge rounded-pill" style="background-color:' . $priorityData['background_color'] . ';
                                                        color:' . $priorityData['font_color'] . ';">
                                    ' . $priorityData['label'] . '
                                </span>';
                    }
                    return '<span class="badge rounded-pill bg-label-secondary">Unknown</span>';
                })
                ->addColumn('add_time', function($row) {
                    if(in_array($row->status, [3, 9, 10, 12])) { // blocked, completed, closed, canceled, 
                        $button = '';
                    } else {
                        $button = '<button type="button" class="btn update-task-time" data-bs-toggle="modal" data-bs-target="#taskUpdateTimeModal" style="cursor: default;"  data-taskid="' . $row->id . '"><span class="mdi mdi-square-edit-outline text-info cursor-pointer"></span></button>';
                    }
                    return $button;
                })
                ->rawColumns(['assigns', 'status', 'deadline', 'overdue', 'priority', 'estimated_time', 'add_time'])
                ->make(true);
        }
    }

    /*
     * upcoming-deadline-tasks list
     */

    public function upcomingDeadlineTasks(Request $request)
    {
        if ($request->ajax()) {
            $role = auth()->user()->role;
            
            if(in_array($role, [1, 7])) {
                $taskLists = ProjectTasksModel::whereBetween('due_date', [
                                    Carbon::now(),
                                    Carbon::now()->addMonth()
                                ])
                                ->with(['assigns', 'statusDetail'])
                                ->orderBy('id', 'ASC');
            } else {
                $taskLists = collect();
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $taskLists->where(function ($q) use ($search) {
                    $q->where('task_name', 'LIKE', "%{$search}%");
                });
            }

            return Datatables::of($taskLists->limit(5))
                ->addIndexColumn()
                ->addColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('jS M, Y') : '';
                })
                ->addColumn('estimated_time', function($row) {
                    return intdiv($row->estimated_time, 60) . 'h ' . ($row->estimated_time % 60) . 'm';
                })
                ->addColumn('deadline', function($row) {
                    $deadline = config('constants.deadline');
                    $style = 'style="color:' . $deadline['color'] . '; background:' . $deadline['bg_color'] . ';"';
                    if ($row->due_date) {
                        return '<span class="badge rounded-pill"' . $style . '>' . $row->due_date->format('jS M, Y') . '</span>';
                    }
                    return '<span class="badge rounded-pill"' . $style . '>&emsp;&emsp;&emsp;NA&emsp;&emsp;&emsp;</span>';
                })
                ->addColumn('priority', function($row) {
                    $priorityData = Enum::PRIORITIES[$row->priority] ?? null;
                    if ($priorityData) {
                        return '<span class="badge rounded-pill" style="background-color:' . $priorityData['background_color'] . ';
                                                        color:' . $priorityData['font_color'] . ';">
                                    ' . $priorityData['label'] . '
                                </span>';
                    }
                    return '<span class="badge rounded-pill bg-label-secondary">Unknown</span>';
                })
                ->rawColumns(['deadline', 'priority', 'estimated_time'])
                ->make(true);
        }
    }

    /*
    * active and completed projets
    */

    public function activeCompletedProjects(Request $request)
    {
        if ($request->ajax()) {
            $role = auth()->user()->role;
            
            if($role == 1) {
                $query = ProjectModel::whereIn('status', [1, 2, 3, 4, 5, 6, 7, 8])->with(['client', 'assigns']);

            } elseif(in_array($role, [7, 8, 4])) {
                $query = ProjectModel::whereHas('projectAssignes', function ($query) {
                            $query->where('user_id', auth()->id());
                        })->whereIn('status', [1, 2, 3, 4, 5, 6, 7, 8])->with(['client', 'assigns']);
            } else {
                $query = collect();
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('project_name', 'LIKE', "%{$search}%");
                });
            }

            return Datatables::of($query->limit(5))
                ->addIndexColumn()
                ->editColumn('project_name', function($row) {
                    return ucwords($row->project_name);
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('jS M, Y') : '';
                })
                ->editColumn('status', function($row) {
                    $statusDescriptions = getProjectStatusList();
                    $status = $statusDescriptions[$row->status] ?? ['text' => 'Unknown Status', 'color' => 'bg-secondary'];
                    return '<span class="badge rounded-pill ' . $status['color'] . '">' . $status['text'] . '</span>';
                })
                ->addColumn('client_name', function($row){
                    return $row->client()->count() > 0 ? ucwords($row->client()->first()->full_name) : '';
                })
                ->addColumn('assigns', function($row) {
                    $assigns = in_array(auth()->user()->role, [8, 4])
                                ? $row->assigns()->where('user_id', auth()->id())->get()
                                : $row->assigns()->get();

                    return renderAssignBadge($assigns);
                })
                ->rawColumns(['status', 'assigns'])
                ->make(true);
        }
    }

    /*
    * overdue-tasks
    */

    public function overdueTasks(Request $request)
    {
        $role = auth()->user()->role;

        if ($request->ajax()) {
            if($role == 1) {
                $query = ProjectTasksModel::whereDate('due_date', '<', Carbon::now());
            } elseif(in_array($role, [7])){
                $projectIds = ProjectAssignModel::where('user_id', auth()->id())->pluck('project_id')->unique()->toArray();
                $query = ProjectTasksModel::whereIn('project_id', $projectIds)->whereDate('due_date', '<', Carbon::now());
            } elseif(in_array($role, [8, 4])){
                $projectIds = ProjectAssignModel::where('user_id', auth()->id())->pluck('project_id')->unique()->toArray();
                $query = ProjectTasksModel::whereHas('taskAssigns', function($assigns) {
                                $assigns->where('user_id', auth()->id());
                            })
                            ->whereIn('project_id', $projectIds)
                            ->whereDate('due_date', '<', Carbon::now())
                            ->orderBy('id', 'ASC');
            } else {
                $query = collect();
            }

                // Apply DataTables search filter
                if ($request->has('search') && !empty($request->input('search.value'))) {
                    $search = $request->input('search.value');
                    $query->where(function ($q) use ($search) {
                        $q->where('task_name', 'LIKE', "%{$search}%");
                    });
                }
    
            return Datatables::of($query->limit(5))
                ->addIndexColumn()
                ->addColumn('task_name', function($row) {
                    return ucwords($row->task_name);
                })
                ->addColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('jS M, Y') : '';
                })
                ->addColumn('due_date', function($row) {
                    $deadline = config('constants.deadline');
                    $style = 'style="color:' . $deadline['color'] . '; background:' . $deadline['bg_color'] . ';"';
                    if ($row->due_date) {
                        return '<span class="badge rounded-pill"' . $style . '>' . $row->due_date->format('jS M, Y') . '</span>';
                    }
                    return '<span class="badge rounded-pill"' . $style . '>&emsp;&emsp;&emsp;NA&emsp;&emsp;&emsp;</span>';
                })
                ->rawColumns(['due_date'])
                ->make(true);
        }
    }

    /*
     * resource allocation
     */

    public function resourceAllocation(Request $request)
    {
        if ($request->ajax()) {
            $role = auth()->user()->role;
            $departmentId = getDepartmentIdByRoleId($role);

            $userQuery = User::whereHas('role_details.department', function($department) {
                    $department->where('id', 2);                
                })
                ->with(['projectModel', 'assignedTasks'])
                ->where('users.status', 1);

            if (!$request->has('order')) {
                $userQuery->orderByDesc('id');
            }

            if($role == 1 && $departmentId == 1) {
                $employees = $userQuery;
            } elseif($role == 8 && $departmentId == 2) {
                $projectIds = ProjectAssignModel::where('user_id', auth()->id())->pluck('project_id')->unique()->toArray();
                if (!empty($projectIds)) {
                    $employees = $userQuery
                                    ->whereHas('role_details', function($role) {
                                        $role->where('id', 4);                
                                    })
                                    ->whereHas('projectAssigned', function($q) use($projectIds) {
                                        $q->whereIn('project_id', $projectIds);
                                    });
                } else {
                    $employees = collect();
                }
            } else {
                $employees = collect();
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

            return DataTables::of($employees->take(5))
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
                ->rawColumns(['role'])
                ->make(true);
        }
    }

    /*
    * unassigned-resource
    */

    public function unassignedResource(Request $request)
    {
        if ($request->ajax()) {
            $query = User::whereHas('role_details.department', function($department) {
                            $department->where('id', 2);                
                        })
                        ->with('role_details')
                        ->whereIn('users.status', [1, 2])
                        ->doesntHave('projectAssigned');

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
            }

            return Datatables::of($query->limit(5))
                ->addIndexColumn()
                ->addColumn('full_name', function($row) {
                    return ucwords($row->full_name);
                })
                ->orderColumn('full_name', function ($query, $order) {
                    $query->orderByRaw("CONCAT(first_name, ' ', last_name) {$order}");
                })
                ->addColumn('department', function($row) {
                    return $row->role_details->name;
                })
                ->orderColumn('department', function ($query, $order) {
                    $query->join('user_role as u', 'users.role', '=', 'u.id')
                        ->orderByRaw("CONCAT(u.name) {$order}");
                })
                ->editColumn('status', function ($row) {
                    $statusLabels = getUserStatus();
                    $status = $statusLabels[$row->status] ?? ['text' => 'Unknown', 'class' => 'badge rounded-pill bg-label-secondary'];
                    return "<span class='{$status['class']}'>{$status['text']}</span>";
                })
                ->rawColumns(['status'])
                ->make(true);
        }
    }

    public function idleTimeReport(Request $request)
    {
        if ($request->ajax()) {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $weeklyWorkingHours = 40;
            $role = auth()->user()->role;
            $departmentId = getDepartmentIdByRoleId($role);

            $query = User::query()
                        ->whereHas('role_details.department', function ($department) {
                            $department->where('id', 2);
                        });

            if ($role == 7 && $departmentId == 2) { 
                $projectIds = ProjectModel::whereHas('projectAssignes', function($assigns) {
                                    $assigns->where('user_id', auth()->id());
                                })->pluck('id')->toArray();
                $taskIds = ProjectTasksModel::whereIn('project_id', $projectIds)->pluck('id')->toArray();
                $userIds = ProjectTaskAssignModel::whereIn('task_id', $taskIds)->pluck('user_id')->unique()->toArray();
                $query->whereIn('id', $userIds)->orderBy('id', 'DESC');
            } elseif($role == 1) {
                $query->orderBy('id', 'DESC');
            }

            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
            }
            $users = $query->get();

            $reportData = $users->map(function ($user) use ($startOfWeek, $endOfWeek, $weeklyWorkingHours) {
                        $userId = $user->id;
                        $estimated = ProjectTasksModel::whereHas('taskAssigns', function($assigns) use($userId) {
                                            $assigns->where('user_id', $userId);
                                        })
                                        ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                                        ->sum('estimated_time');
                        $estimatedHours = round($estimated / 60, 2);                
                        return (object)[
                            'employee' => $user->first_name . ' ' . $user->last_name,
                            'weekly_hours' => $weeklyWorkingHours . ' hrs',
                            'estimated_hours' => $estimatedHours . ' hrs',
                            'idle_time' => round(max($weeklyWorkingHours - $estimatedHours, 0), 2) . ' hrs',
                        ];
                    });


            return DataTables::of($reportData->take(5))
                ->addIndexColumn()
                ->addColumn('employee', fn($row) => ucwords($row->employee))
                ->addColumn('weekly_hours', fn($row) => $row->weekly_hours)
                ->addColumn('estimated_hours', fn($row) => $row->estimated_hours)
                ->addColumn('idle_time', fn($row) => $row->idle_time)
                ->rawColumns(['employee', 'weekly_hours', 'estimated_hours', 'idle_time'])
                ->make(true);
        }
    }

    public function billableNonBillableTask(Request $request)
    {
        if ($request->ajax()) {
            $projectId = $request->input('project_id');

            if (!$projectId) {
                return response()->json(['data' => []]);
            }

            $project = ProjectModel::find($projectId);
            if (!$project) {
                return response()->json(['data' => []]);
            }

            $tasks = ProjectTasksModel::with('users')->where('project_id', $projectId)->orderBy('id', 'ASC')->get();

            $projectMinutes = $project->project_hours * 60;
            $runningTotal = 0;
            $taskUtilization = [];

            foreach ($tasks as $task) {
                $usersName = [];
                foreach($task->users as $user) {
                    $usersName[] = $user->first_name . ' ' . $user->last_name;
                }

                $runningTotal += $task->estimated_time;

                $formattedEmployeeName = '<span class="badge rounded-pill bg-label-secondary">Task not yet assigned</span>';
                if (!empty($usersName)) {
                    $firstUser = array_shift($usersName);
                    $tooltipIcon = '';
                    $padding = 'style="padding: 4px 6px;"';
                
                    if (count($usersName) > 0) {
                        $escapedUsers = array_map(function ($name) {
                            return htmlspecialchars(ucwords($name), ENT_QUOTES, 'UTF-8');
                        }, $usersName);
                
                        $tooltipData = implode('<br>', $escapedUsers);
                
                        $tooltipIcon = ' <i class="mdi mdi-dots-horizontal-circle text-primary"
                                            style="cursor:pointer;"
                                            data-bs-toggle="tooltip"
                                            data-bs-html="true"
                                            aria-label="' . $tooltipData . '"
                                            data-bs-original-title="' . $tooltipData . '"></i>';
                        $padding = 'style="padding: 0px 6px;"';
                    }
                    
                    $formattedEmployeeName = '<span class="badge rounded-pill bg-label-primary" ' . $padding . '>'
                                           . htmlspecialchars(ucwords($firstUser), ENT_QUOTES, 'UTF-8') . $tooltipIcon .
                                           '</span>';
                }

                $taskUtilization[] = (object)[
                    'employee_name'       => $formattedEmployeeName,
                    'task_name'       => ucwords($task->task_name),
                    'estimated_time'  => $task->estimated_time . ' mins',
                    'billable_type'   => ($runningTotal <= $projectMinutes) 
                                            ? '<span class="badge rounded-pill bg-label-success">Billable</span>'
                                            : '<span class="badge rounded-pill bg-label-warning">Non-Billable</span>',
                ];
            }

            return DataTables::of(collect($taskUtilization)->take(5))
                ->addIndexColumn()
                ->addColumn('employee_name', function($row) {
                    return $row->employee_name;
                })
                ->rawColumns(['employee_name', 'billable_type'])
                ->make(true);
        }
    }

    public function bar(Request $request){



        $year = $request->year;

        

         $condition_sql_lead = "";

        $condition_sql_deal = "";



        if(Auth::user()->role == 1){

            $condition_sql_lead .= "";

            $condition_sql_deal .= "";

        }else if(Auth::user()->role == 2){

            $user_id = Auth::user()->id;

            $condition_sql_lead .= " AND leads.owner = $user_id";

            $condition_sql_deal .= " AND deals.owner = $user_id";

        }



        $deals = DB::select("SELECT MONTH(deals.created_at) as months,COUNT(deals.id) as deals_count FROM deals WHERE YEAR(deals.created_at) = $year AND deals.converted = 1 $condition_sql_deal GROUP BY MONTH(deals.created_at)");



        $leads = DB::select("SELECT MONTH(leads.created_at) as months,COUNT(leads.id) as leads_count FROM leads WHERE YEAR(leads.created_at) = $year AND leads.converted = 1 $condition_sql_lead GROUP BY MONTH(leads.created_at)");



        $project = DB::select("SELECT MONTH(deals_status.created_at) as months,COUNT(deals_status.id) as project_count FROM deals_status WHERE YEAR(deals_status.created_at) = $year AND deals_status.won = 1 GROUP BY MONTH(deals_status.created_at)");



        $deals_array = [];

        $lead_array = [];

        $project_array = [];



        for($i=1;$i<13;$i++){

            $key = array_search($i,array_column($deals, 'months'));

            if($key != ''){

                array_push($deals_array,$deals[$key]->deals_count);

            }else{

                array_push($deals_array,0);

            }

            $key1 = array_search($i,array_column($leads, 'months'));

            if($key1 != ''){

                array_push($lead_array,$leads[$key1]->leads_count);

            }else{

                array_push($lead_array,0);

            }

            $key2 = array_search($i,array_column($project, 'months'));

            if($key2 != ''){

                array_push($project_array,$project[$key2]->project_count);

            }else{

                array_push($project_array,0);

            }

        }



        return response()->json([

                'project_array' => $project_array,

                'lead_array' => $lead_array,

                'deals_array' => $deals_array,

            ]);

    }



    public function pie(Request $request){



        $user_sql = '';



        if($request->user){

            $user_sql = "WHERE deals.owner = $request->user";

        }else{

            $user_sql = '';

        }

        

        if(Auth::user()->role == 2){

            $user_id = Auth::user()->id;

            $condition_sql = "WHERE deals.owner = $user_id";

        }else{

            $condition_sql = '';

        }



        $deal_stage = DB::select("select deals_stages_list.name as name,(CASE

         WHEN COUNT(deals.id) THEN COUNT(deals.id)

         ELSE 0 END) as deals_count from deals inner join deals_stages ON deals.id = deals_stages.deal_id right join deals_stages_list ON deals_stages_list.id = deals_stages.stage $user_sql $condition_sql GROUP BY deals_stages_list.id");





        $deal_stage_array = [];



        foreach ($deal_stage as $key => $value) {

            

            if($key == 2){

                $data = new \stdClass();

                $data->name = $value->name;

                $data->y = $value->deals_count;

                $data->sliced = $value->deals_count;

                $data->selected = $value->deals_count;

            }else{

                 $data = [$value->name,$value->deals_count];

            }

            

            array_push($deal_stage_array,$data);

        }



        return response()->json([

                'deal_stage_array' => $deal_stage_array,

            ]);

    }



    public function line(){



        $year = date('Y');

        $condition_sql_lead = "";

        $condition_sql_deal = "";

        if(Auth::user()->role == 1){

            $condition_sql_lead .= "";

            $condition_sql_deal .= "";

        }else if(Auth::user()->role == 2){

            $user_id = Auth::user()->id;

            $condition_sql_lead .= " AND leads.owner = $user_id";

            $condition_sql_deal .= " AND deals.owner = $user_id";

        }





        $deals = DB::select("SELECT MONTH(deals.created_at) as months,COUNT(deals.id) as deals_count FROM deals WHERE YEAR(deals.created_at) = $year AND deals.converted = 1 $condition_sql_deal GROUP BY MONTH(deals.created_at)");



        $leads = DB::select("SELECT MONTH(leads.created_at) as months,COUNT(leads.id) as leads_count FROM leads WHERE YEAR(leads.created_at) = $year AND leads.converted = 1 $condition_sql_lead GROUP BY MONTH(leads.created_at)");



        $total_lead = DB::select("SELECT MONTH(leads.created_at) as months,COUNT(leads.id) as leads_count FROM leads WHERE YEAR(leads.created_at) = $year $condition_sql_lead GROUP BY MONTH(leads.created_at)");



        $total_deal = DB::select("SELECT MONTH(deals.created_at) as months,COUNT(deals.id) as deals_count FROM deals WHERE YEAR(deals.created_at) = $year $condition_sql_deal GROUP BY MONTH(deals.created_at)");



        $project = DB::select("SELECT MONTH(deals_status.created_at) as months,COUNT(deals_status.id) as project_count FROM deals_status WHERE YEAR(deals_status.created_at) = $year AND deals_status.won = 1 GROUP BY MONTH(deals_status.created_at)");





        $deals_array = [];

        $total_lead_array = [];

        $total_deal_array = [];

        $lead_array = [];

        $project_array = [];



        for($i=1;$i<13;$i++){

            $key = array_search($i,array_column($deals, 'months'));

            if($key != ''){

                array_push($deals_array,$deals[$key]->deals_count);

            }else{

                array_push($deals_array,0);

            }

            $key1 = array_search($i,array_column($leads, 'months'));

            if($key1 != ''){

                array_push($lead_array,$leads[$key1]->leads_count);

            }else{

                array_push($lead_array,0);

            }

            $key2 = array_search($i,array_column($project, 'months'));

            if($key2 != ''){

                array_push($project_array,$project[$key2]->project_count);

            }else{

                array_push($project_array,0);

            }

            $key3 = array_search($i,array_column($total_lead, 'months'));

            if($key3 != ''){

                array_push($total_lead_array,$total_lead[$key3]->leads_count);

            }else{

                array_push($total_lead_array,0);

            }

            $key4 = array_search($i,array_column($total_deal, 'months'));

            if($key4 != ''){

                array_push($total_deal_array,$total_deal[$key4]->deals_count);

            }else{

                array_push($total_deal_array,0);

            }

        }



        return response()->json([

                'project_array' => $project_array,

                'lead_array' => $lead_array,

                'deals_array' => $deals_array,

                'total_deal_array' => $total_deal_array,

                'total_lead_array' => $total_lead_array,

            ]);

    }



    public function area(){

        

        $month = date('m');



        $total_leads = DB::select("SELECT SUM(leads.value) as amount,users.first_name,users.last_name,DATE(leads.created_at) as Date,leads.owner as user_id FROM `leads` inner join users ON leads.owner = users.id WHERE Month(leads.created_at) = $month GROUP BY leads.owner,DATE(leads.created_at)");



        $user_id_array = [];

        $final_array = [];



        $begin  = new DateTime(date('Y-m-01'));

        $end = new DateTime(date('Y-m-d',time() + 86400));

        $interval = DateInterval::createFromDateString('1 day');

        $period = new DatePeriod($begin, $interval, $end);

        $x_axis = [];



        foreach ($total_leads as $key => $value) {



            if(!array_key_exists($value->user_id,$user_id_array)){

                $user_id_array[$value->user_id] = [];

                $user_id_array[$value->user_id] = [];

            }

            array_push($user_id_array[$value->user_id],

                ['amount'=>$value->amount,'date'=>$value->Date,'name'=>$value->first_name]

             );

        }



        // return $user_id_array;



        foreach ($user_id_array as $value){

            $data = new \stdClass();

            $data->name = $value[0]['name'];

            $data->data = [];

            $amount_array = [];

            foreach($period as $dt){

                array_push($x_axis,$dt->format('Y-m-d'));

                $key = array_search($dt->format('Y-m-d'),array_column($value, 'date'));

                if($key != ''){

                array_push($amount_array,(int)$value[$key]['amount']);

                }else{

                    array_push($amount_array,0);

                }

            }

            $data->data = $amount_array;

            array_push($final_array,$data);

        }



         return response()->json([

                'final_array' => $final_array,

                'x_axis' => $x_axis,

            ]);

        

    }

    public function getTaskTime($taskId)
    {
        $task = ProjectTasksModel::find($taskId);

        if ($task) {
            return response()->json([
                'start_time' => ($task->start_time != null) ? Carbon::parse($task->start_time)->format('Y-m-d\TH:i') : '',
                'end_time'   => ($task->end_time != null) ? Carbon::parse($task->end_time)->format('Y-m-d\TH:i') : '',
            ]);
        }

        return response()->json(['message' => 'Task not found.'], 404);
    }

    public function updateTaskTime(Request $request, $taskId)
    {
        $validator = Validator::make($request->all(), [
            'start_time' => 'nullable|date',
            'end_time'   => 'nullable|date'
        ]);
    
        $validator->after(function ($validator) use ($request) {
            if (empty($request->start_time) && empty($request->end_time)) {
                $validator->errors()->add('start_time', 'Either start date or end date must be provided.');
            }
    
            if ($request->start_time && $request->end_time && $request->end_time < $request->start_time) {
                $validator->errors()->add('end_time', 'End date must be after to start date.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $task = ProjectTasksModel::find($taskId);
        if (!$task) {
            return response()->json(['status' => 'error','message' => 'Task not found.'], 404);
        }

        
        if ($request->start_time) {
            $task->start_time = $request->start_time;
        }
        if ($request->end_time) {
            $task->end_time = $request->end_time;
        }
        $result = $task->save();

        if ($result) {
            return response()->json(['status' => 'success','message' => 'Task time updated successfully.']);
        } else {
            return response()->json(['status' => 'error','message' => 'Failed to update task. Please try again.'], 500); // Internal Server Error
        }
    }

}

