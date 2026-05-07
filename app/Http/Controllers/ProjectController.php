<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Models\User;
use App\Constants\Enum;
use Illuminate\Support\Facades\Mail;
use App\Models\Projects\ClientModel;
use App\Models\Projects\ProjectModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Projects\ProjectBugsModel;
use App\Models\Projects\ProjectTasksModel;
use App\Models\Projects\ProjectAssignModel;
use App\Models\Projects\ProjectBugAssignModel;
use App\Models\Projects\ProjectMilestoneModel;
use App\Models\Projects\ProjectBugStatusModel;
use App\DataTables\Projects\ProjectsDataTable;
use App\Models\Projects\ProjectTaskAssignModel;
use App\Models\Projects\ProjectBugCommentModel;
use App\Models\Projects\ProjectTaskCommentModel;
use App\Models\Projects\ProjectTaskStatusModel;
use App\DataTables\Projects\ProjectBugsDataTable;
use App\DataTables\Projects\ProjectTasksDataTable;
use App\Models\Projects\ProjectBugAttachmentModel;
use App\DataTables\Projects\ProjectMilestonesDataTable;

class ProjectController extends Controller
{
    public function dashboard(Request $request)
    {
        $role = auth()->user()->role;
        $viewData = [];

        if($role == 1) {
            // Task Progress Overview
            $viewData['totalTaskCount'] = ProjectTasksModel::count();
            $viewData['completedTaskCount'] = ProjectTasksModel::where('status', 9)->count();
            $viewData['inProgressTaskCount'] = ProjectTasksModel::where('status', 4)->count();
            $viewData['pendingTaskCount'] = ProjectTasksModel::where('status', 1)->count();

            // Budget vs. Actual Expense
            $viewData['budgets'] = ProjectModel::with(['tasks' => function($q) {
                    $q->select('project_id', 'estimated_time');
                }])
                ->select('id', 'project_name', 'project_hours')
                ->get()
                ->map(function($project) {
                    $totalEstimatedTime = $project->tasks->sum('estimated_time');
                    return [
                        'project_name' => $project->project_name,
                        'budget' => $project->project_hours,
                        'actual_expense' => $totalEstimatedTime
                    ];
                });
        
            // Team task completion overview
            $viewData['users'] = User::whereHas('role_details.department', function($department) {
                                        $department->where('id', 2);                
                                    })
                                    ->with(['projectTaskAssignModel.projectTasks' => function ($q) {
                                        $q->where('status', 9); // 9 for completed task
                                    }])
                                    ->get()
                                    ->map(function ($user) {
                                        return [
                                            'user_name' => $user->first_name . ' ' . $user->last_name,
                                            'completed_task' => $user->projectTaskAssignModel->filter(function ($assign) {
                                                    return $assign->projectTasks !== null;
                                                })->count(),
                                            'color' => '#' . substr(md5($user->id), 0, 6)
                                        ];
                                    });

            // Team performance metrics
            $viewData['teamPerformance'] = User::whereHas('role_details.department', function($department) {
                                                $department->where('id', 2);                
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
                                                    'timeLogged' => $timeLogged / 60
                                                ];
                                            });

        } elseif($role == 7) {
            // Task Progress Overview 
            $projectIds = ProjectAssignModel::where('user_id', auth()->id())->pluck('project_id')->unique()->toArray();
            $viewData['totalTaskCount'] = ProjectTasksModel::whereIn('project_id', $projectIds)->count();
            $viewData['completedTaskCount'] = ProjectTasksModel::whereIn('project_id', $projectIds)->where('status', 9)->count();
            $viewData['inProgressTaskCount'] = ProjectTasksModel::whereIn('project_id', $projectIds)->where('status', 4)->count();
            $viewData['pendingTaskCount'] = ProjectTasksModel::whereIn('project_id', $projectIds)->where('status', 1)->count();

            // Budget vs. Actual Expense
            $viewData['budgets'] = ProjectModel::whereIn('id', $projectIds)->with(['tasks' => function($q) {
                    $q->select('project_id', 'estimated_time');
                }])
                ->select('id', 'project_name', 'project_hours')
                ->get()
                ->map(function($project) {
                    $totalEstimatedTime = $project->tasks->sum('estimated_time');
                    return [
                        'project_name' => $project->project_name,
                        'budget' => $project->project_hours,
                        'actual_expense' => $totalEstimatedTime
                    ];
                });
        
            // Team Performance & Resource Utilization
            $userIds = ProjectTaskAssignModel::whereHas('projectTasks', function ($query) use ($projectIds) {
                    $query->whereIn('project_id', $projectIds);
                })->pluck('user_id')->unique()->toArray(); 
            $viewData['users'] = User::whereIn('id', $userIds)->with(['projectTaskAssignModel.projectTasks' => function ($q) {
                    $q->where('status', 9); // 9 for completed task
                }])
                ->get()
                ->map(function ($user) {
                    return [
                        'user_name' => $user->first_name . ' ' . $user->last_name,
                        'completed_task' => $user->projectTaskAssignModel->filter(function ($assign) {
                                return $assign->projectTasks !== null;
                            })->count(),
                        'color' => '#' . substr(md5($user->id), 0, 6)
                    ];
                });

            // Team performance metrics such as task completion rates and time logs.
            $viewData['teamPerformance'] = User::whereIn('id', $userIds)->with(['assignedTasks'])
                    ->get()
                    ->map(function ($member) {
                        $totalTasks = $member->assignedTasks->count();
                        $completedTasks = $member->assignedTasks->where('status', 9)->count();
                        $timeLogged = $member->assignedTasks->sum('estimated_time');
    
                        return [
                            'name' => $member->first_name . ' ' . $member->last_name,
                            'completionRate' => ($totalTasks > 0) ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
                            'timeLogged' => $timeLogged
                        ];
                    });

        } else {
            // Task Progress Overview
        }

        // tasks assigned to team members
        $viewData['tasks'] = ProjectTasksModel::with(['assigns' => function($q) {
                $q->select('first_name', 'last_name');
            }])->get();

        return view('projects.dashboard', $viewData);
    }

    /*
     * Fetch only projects assigned to the logged-in user
     */

    public function assignedProjectList(Request $request)
    {
        if ($request->ajax()) {
            $role = auth()->user()->role;
            
            if($role == 1) {  // admin
                $projects = ProjectModel::whereHas('assigns')->with(['assigns']);

            } elseif($role == 7) { // project manager
                $projects = ProjectModel::whereHas('assigns', function ($query) {
                        $query->where('user_id', auth()->id());
                    })->with(['assigns']);

            } else {
                $projects = collect();
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $projects->where(function ($q) use ($search) {
                    $q->where('project_name', 'LIKE', "%{$search}%");
                });
            }

            return Datatables::of($projects->limit(5))
                ->addIndexColumn()
                ->addColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('jS M, Y') : '';
                })
                ->addColumn('project_name', function($row) {
                    return ucwords($row->project_name);
                })
                ->addColumn('project_hours', function($row) {
                    return $row->project_hours ? $row->project_hours . ' hrs' : '';
                })
                ->addColumn('status', function($row) {
                    $statusDescriptions = getProjectStatusList();
                    $status = $statusDescriptions[$row->status] ?? ['text' => 'Unknown Status', 'color' => 'bg-secondary'];
                    return '<span class="badge rounded-pill ' . $status['color'] . '">' . $status['text'] . '</span>';
                })
                ->addColumn('assigns', function($row) {
                    $assigns = in_array(auth()->user()->role, [7])
                                ? $row->assigns()->where('user_id', auth()->id())->get()
                                : $row->assigns()->get();

                    return renderAssignBadge($assigns);
                })
                ->rawColumns(['status', 'assigns'])
                ->make(true);
        }
    }

    public function milestonesList(Request $request)
    {
        // Fetch milestones list
        if ($request->ajax()) {
            $role = auth()->user()->role;
            
            if($role == 1) {
                $milestones = ProjectMilestoneModel::with(['projectModel']);

            } elseif($role == 7) {
                $projectIds = ProjectAssignModel::where('user_id', auth()->id())->pluck('project_id')->unique()->toArray();
                $milestones = ProjectMilestoneModel::whereIn('project_id', $projectIds)->with(['projectModel']);

            } else {
                $milestones = collect();
            }

            // Apply DataTables search filter
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $milestones->where(function ($q) use ($search) {
                    $q->where('milestone_name', 'LIKE', "%{$search}%");
                });
            }
            
            return Datatables::of($milestones->limit(5))
                ->addIndexColumn()
                ->addColumn('project_name', function($row) {
                    return $row->projectModel ? ucwords($row->projectModel->project_name) : 'N/A';
                })
                ->addColumn('milestone_name', function($row) {
                    return $row->milestone_name ? ucwords($row->milestone_name) : 'N/A';
                })
                ->addColumn('status', function($row) {
                    if ($row->projectModel) {
                        $statusDescriptions = getProjectStatusList();
                        $status = $statusDescriptions[$row->projectModel->status] ?? ['text' => 'Unknown Status', 'color' => 'bg-secondary'];
                        return '<span class="badge rounded-pill ' . $status['color'] . '">' . $status['text'] . '</span>';
                    }
                    return '<span class="badge rounded-pill bg-secondary">N/A</span>';
                })
                ->addColumn('Deadline', function($row) {
                    $deadline = config('constants.deadline');
                    $style = 'style="color:' . $deadline['color'] . '; background:' . $deadline['bg_color'] . ';"';
                    return ($row->projectModel && $row->projectModel->end_date) ? '<span class="badge rounded-pill"' . $style . '>' . $row->projectModel->end_date->format('jS M, Y') . '</span>' : 'N/A';
                })
                ->addColumn('Alert', function($row) {
                    $now = Carbon::now();
                    $endDate = optional($row->projectModel)->end_date; // Prevent null errors
                
                    if (!$endDate) {
                        return '<span class="text-secondary">N/A</span>'; // Handle missing date
                    }
                
                    $endDate = Carbon::parse($endDate);
                    
                    if ($endDate->isPast()) {
                        return '<span class="text-danger">⚠ Overdue!</span>';
                    } elseif ($now->diffInDays($endDate) <= 30) {
                        return '<span class="text-warning">⏳ Due Soon</span>';
                    } else {
                        return '<span class="text-success">✔ On Track</span>';
                    }
                })
                ->rawColumns(['status', 'Deadline', 'Alert'])
                ->make(true);
        }
    }

    /*
     * tasks assigned to team members list
     */

    public function memberTaskList(Request $request)
    {
        if ($request->ajax()) {
            $role = auth()->user()->role;
            
            if($role == 1) {
                $taskLists = ProjectTasksModel::whereHas('assigns')->with(['assigns', 'statusDetail']);

            } elseif(in_array($role, [7, 8, 4])) {
                $taskLists = ProjectTasksModel::whereHas('assigns', function ($query) {
                                $query->where('user_id', auth()->id());
                            })->with(['assigns', 'statusDetail']);
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
                ->addColumn('task_name', function($row) {
                    return ucwords($row->task_name);
                })
                ->addColumn('estimated_time', function($row) {
                    return intdiv($row->estimated_time, 60) . 'h ' . ($row->estimated_time % 60) . 'm';
                })
                ->addColumn('assigns', function($row) {
                    $assigns = $row->assigns()->get();
                    return renderAssignBadge($assigns);
                })
                ->addColumn('status', function ($row) {
                    if ($row->statusDetail) {
                        return '<span class="badge rounded-pill" style="background-color:' . $row->statusDetail->bg_color . '; color:' . $row->statusDetail->font_color . ';">' . $row->statusDetail->status_name . '</span>';
                    }
                    return '<span class="badge rounded-pill bg-secondary">Unknown</span>';
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
                    return '<span class="badge rounded-pill bg-secondary">Unknown</span>';
                })
                ->rawColumns(['assigns', 'status', 'deadline', 'priority', 'estimated_time'])
                ->make(true);
        }
    }

    public function dailyTaskUpdate(Request $request)
    {
        // Fetch tasks assigned to team members
        if ($request->ajax()) {
            $taskLists = ProjectTasksModel::with(['assigns', 'statusDetail'])
                    ->whereDate('updated_at', now()->toDateString());

            return Datatables::of($taskLists)
                ->addIndexColumn()
                ->addColumn('description', function ($row) {
                    return $row->description ?? 'N/A';
                })
                ->addColumn('assigns', function ($row) {
                    if ($row->assigns()->count() > 0) {
                        return $row->assigns()->get()->map(function ($assign) {
                            return [
                                'first_name' => $assign->first_name,
                                'last_name' => $assign->last_name
                            ];
                        })->toArray();
                    }
                    return [];
                })
                ->addColumn('status', function ($row) {
                    return $row->statusDetail ? $row->statusDetail->status_name : 'Unknown';
                })
                ->addColumn('statusDetail', function ($row) {
                    return $row->statusDetail ? [
                        'status_name' => $row->statusDetail->status_name,
                        'bg_color' => $row->statusDetail->bg_color,
                        'font_color' => $row->statusDetail->font_color
                    ] : null;
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('jS M, Y') : '';
                })
                ->rawColumns(['status'])
                ->make(true);
        }
    }

    // this code merged in memberTaskList() method above
    public function taskDeadlinePriority(Request $request)
    {
        // View of all assigned tasks with deadlines and priorities.
        if ($request->ajax()) {
            $taskLists = ProjectTasksModel::with(['assigns', 'statusDetail']);
            return Datatables::of($taskLists)
                ->addIndexColumn()
                ->addColumn('assigns', function($row) {
                    if ($row->assigns()->count() > 0) {
                        return $row->assigns()->get()->map(function($assign) {
                            return '<span class="badge rounded-pill bg-label-primary mb-2 role-'.$assign->role.'">'
                                . $assign->first_name . ' ' . $assign->last_name .
                                '</span>';
                        })->implode('<br>');
                    } else {
                        return '';
                    }
                })
                ->addColumn('deadline', function($row) {
                    return '<span  rounded-pill bg-danger">25-Feb-2024</span>';
                })
                ->addColumn('priority', function($row) {
                    return '<span class="badge rounded-pill bg-secondary">High</span>';
                })
                ->rawColumns(['assigns', 'deadline', 'priority'])
                ->make(true);
        }
    }

    // this code merged in memberTaskList() method above 
    public function todayTaskDeadlines(Request $request)
    {
        // View of all assigned tasks with deadlines and priorities.
        if ($request->ajax()) {
            $taskLists = ProjectTasksModel::with(['assigns', 'statusDetail'])
                    ->whereDate('updated_at', now()->toDateString());
                    
            return Datatables::of($taskLists)
                ->addIndexColumn()
                ->addColumn('assigns', function($row) {
                    if ($row->assigns()->count() > 0) {
                        return $row->assigns()->get()->map(function($assign) {
                            return '<span class="badge rounded-pill bg-label-primary mb-2 role-'.$assign->role.'">'
                                . $assign->first_name . ' ' . $assign->last_name .
                                '</span>';
                        })->implode('<br>');
                    } else {
                        return '';
                    }
                })
                ->addColumn('deadline', function($row) {
                    return '<span class="badge rounded-pill bg-danger">25-Feb-2024</span>';
                })
                ->rawColumns(['assigns', 'deadline'])
                ->make(true);
        }
    }

    public function list(ProjectsDataTable $dataTable)
    {
        return $dataTable->render('projects.list');
    }

    public function edit($project_id)
    {
        $project = ProjectModel::find($project_id);
        if($project){
            return view('projects.edit', compact('project'));
        }else{
            return redirect(route('project.list'))->withError(__('Project not found'));
        }
    }

    public function update(Request $request, $project_id)
    {
        $project = ProjectModel::find($project_id);
        if($project){
            $validator = Validator::make($request->all(), [
                'project_name' => 'required|max:200',
                'status' => 'required',
                'project_description' => 'required',
                'project_hours' => 'required|numeric',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{
                $project->project_name = $request->get('project_name', 'Untitled project');
                $project->status = $request->get('status', 0);
                $project->description = $request->get('project_description', '');
                $project->client_id = $request->get('client_id', null);
                $project->project_hours = $request->get('project_hours', null);
                $project->start_date = $request->get('start_date', null);
                $project->end_date = $request->get('end_date', null);
                $project->updated_at = now();
                $project->save();

                return redirect(route('project.details', $project->id))->withSuccess(__('Project has been updated successfully'));
            }
        }else{
            return redirect(route('project.list'))->withError('Project not found');
        }
    }

    public function add()
    {
        return view('projects.add');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_name' => 'required|max:200',
            'status' => 'required',
            'project_description' => 'required',
            'project_hours' => 'required|numeric',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $project = new ProjectModel;

            $project->project_name = $request->get('project_name', 'Untitled project');
            $project->status = $request->get('status', 0);
            $project->description = $request->get('project_description', '');
            $project->client_id = $request->get('client_id', null);
            $project->project_hours = $request->get('project_hours', null);
            $project->start_date = $request->get('start_date', null);
            $project->end_date = $request->get('end_date', null);
            $project->created_at = now();
            $project->updated_at = now();
            $project->save();

            if(auth()->user()->role != 1){
                // if this is not admin, assign the project by default
                $assign = new ProjectAssignModel;

                $assign->project_id = $project->id;
                $assign->user_id = auth()->user()->id;
                $assign->assigned_by = auth()->user()->id;
                $assign->created_at = now();
                $assign->updated_at = now();
                $assign->save();
            }

            return redirect(route('project.details', $project->id))->withSuccess(__('Project has been created successfully'));
        }
    }

    public function destroy(Request $request, $project_id)
    {
        // needs to improve
        if($project_id){
            $project = ProjectModel::find($project_id);
            if($project){
                // task delete
                $project->tasks()->each(function($task){
                    $task->taskAssigns()->delete();
                });
                $project->tasks()->delete();
                // bug delete 
                $project->bugs()->each(function($bug) {
                    $bug->assigns()->delete();
                });
                $project->bugs()->delete();
                // project delete 
                $project->projectAssignes()->delete();
                ProjectModel::where('id', $project_id)->delete();
                
                return redirect(route('project.list'))->withSuccess(__('Project has been deleted successfully'));
            }else{
                return redirect(route('project.list'))->withError(__('Project not found or limited access'));
            }
        }else{
            return redirect(route('project.list'))->withError(__('Project not found or limited access'));
        }
    }

    public function details($project_id)
    {
        if(auth()->user()->role == 1){
            $project = ProjectModel::find($project_id);
        }else{
            $assigns = ProjectAssignModel::where('project_id', $project_id)->pluck('user_id')->toArray();
            if(in_array(auth()->user()->id, $assigns)){
                $project = ProjectModel::find($project_id);
            }else{
                return redirect(route('project.list'))->withError(__('Project not found or limited access'));
            }
        }
        return view('projects.details', compact('project'));
    }

    public function assign(Request $request)
    {
        if($request->ajax()){
            $project_id = $request->get('project-id');
            if($project_id){
                if($request->get('assign')){
                    foreach($request->get('assign') as $assign){
                        $assignment = new ProjectAssignModel;
                        $assignment->project_id = $project_id;
                        $assignment->user_id = $assign;
                        $assignment->assigned_by = auth()->user()->id;
                        $assignment->created_at = now();
                        $assignment->updated_at = now();
                        $assignment->save();
                    }
                }
                return response()->json(['success' => true, 'message' => __('Project assigned successfully')]);
            }else{
                return response()->json(['error' => true, 'message' => __('Something went wrong')]);
            }
        }
    }

    public function remove_assign(Request $request, $project_id, $user_id)
    {
        if($request->ajax()){
            if($project_id && $user_id){
                ProjectAssignModel::where('project_id', $project_id)->where('user_id', $user_id)->delete();
                return response()->json(['success' => true, 'message' => __('Task assign removed successfully')]);
            }else{
                return response()->json(['error' => true, 'message' => __('Something went wrong')]);
            }
        }else{
            return response()->json(['error' => true, 'message' => __('Something went wrong')]);
        }
    }

    // tasks
    public function task_list(ProjectTasksDataTable $dataTable, $project_id)
    {
        if(auth()->user()->role == 1){
            $project = ProjectModel::find($project_id);
        }else{
            $assigns = ProjectAssignModel::where('project_id', $project_id)->pluck('user_id')->toArray();
            if(in_array(auth()->user()->id, $assigns)){
                $project = ProjectModel::find($project_id);
            }else{
                return redirect(route('project.list'))->withError(__('Project not found or limited access'));
            }
        }
        return $dataTable->with('project', $project)->render('projects.tasks.task-list', compact('project'));
    }

    public function task_detail($project_id, $task_id)
    {
        if(auth()->user()->role == 1){
            $project = ProjectModel::find($project_id);
        }else{
            $assigns = ProjectAssignModel::where('project_id', $project_id)->pluck('user_id')->toArray();
            if(in_array(auth()->user()->id, $assigns)){
                $project = ProjectModel::find($project_id);
            }else{
                return redirect(route('project.list'))->withError(__('Project not found or limited access'));
            }
        }

        $task = ProjectTasksModel::find($task_id);
        $task_statuses = ProjectTaskStatusModel::where('status', 1)->get();
        $taskComments = ProjectTaskCommentModel::with('users')->where('task_id', $task_id)->latest()->paginate(3);
        return view('projects.tasks.task-detail', compact('task', 'task_statuses', 'taskComments'));
    }

    public function task_create($project_id)
    {
        if(auth()->user()->role == 1){
            $project = ProjectModel::find($project_id);
            if(!$project){
                return redirect(route('project.list'))->withError('Project not found or limited access');
            }
        }else{
            $assigns = ProjectAssignModel::where('project_id', $project_id)->pluck('user_id')->toArray();
            if(in_array(auth()->user()->id, $assigns)){
                $project = ProjectModel::find($project_id);
            }else{
                return redirect(route('project.list'))->withError(__('Project not found or limited access'));
            }
        }

        $tasks_status = ProjectTaskStatusModel::where('status', 1)->get();

        return view('projects.tasks.add-task', compact('project', 'tasks_status'));
    }

    public function task_store(Request $request, $project_id)
    {
        $project = ProjectModel::find($project_id);
        if($project->count() > 0){
            $validator = Validator::make($request->all(), [
                'task_name' => 'required|max:200',
                'status' => 'required',
                'milestone' => 'required',
                'estimated_time' => 'required|numeric',
                'task_description' => 'max:1000',
                'task_files.*' => 'mimes:png,jpeg,jpg,gif,pdf,doc,docx,mp4,webp|max:10240',
                'due_date' => 'required'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{
                $task = new ProjectTasksModel;

                $task->task_name = $request->get('task_name', 'Untitled');
                $task->description = $request->get('task_description', null);
                $task->estimated_time = $request->get('estimated_time', 0);
                $task->due_date = $request->get('due_date', null);
                $task->project_id = $project_id;
                $task->milestone_id = $request->get('milestone', '');
                $task->status = $request->get('status', 1);
                $task->priority = $request->get('priority', 'low');
                $task->created_at = now();
                $task->updated_at = now();
                $task->save();

                if($request->hasFile('task_files')){
                    foreach($request->file('task_files') as $task_file){
                        $new_filename = pathinfo($task_file->getClientOriginalName(), PATHINFO_FILENAME).'_'.uniqid().'.'.$task_file->getClientOriginalExtension();
                        $file_type = $task_file->getClientMimeType();
                        $file_size = $task_file->getSize();
                        $task_file->move(public_path('uploads/project/task'), $new_filename);
                        $task->files()->create([
                            'file_name' => $new_filename,
                            'file_type' => $file_type,
                            'file_size' => $file_size,
                            'uploaded_by' => auth()->user()->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }

                return redirect(route('project.task.list', $project_id))->withSuccess(__('Task added'));
            }
        }else{
            return redirect(route('project.list'))->withError(__('Project not found or limited access'));
        }
    }

    public function task_delete(Request $request, $project_id, $task_id)
    {
        if($task_id){
            $task = ProjectTasksModel::find($task_id);
            if($task){
                ProjectTaskAssignModel::where('task_id', $task_id)->delete();
                ProjectTasksModel::find($task_id)->delete();
                return redirect(route('project.task.list', [$project_id]))->withSuccess(__('Task deleted successfully!'));
            }else{
                return redirect(route('project.task.list', [$project_id]))->withError(__('Task not found or limited access'));
            }
        }else{
            return redirect(route('project.task.list', [$project_id]))->withError(__('Task not found or limited access'));
        }
    }

    public function task_assign(Request $request)
    {
        if($request->ajax()){
            $task_id = $request->get('task-id');
            if($task_id){
                $task = ProjectTasksModel::find($task_id);
                if($request->get('assign')){
                    foreach($request->get('assign') as $assign){
                        $assignment = new ProjectTaskAssignModel;
                        $assignment->task_id = $task_id;
                        $assignment->user_id = $assign;
                        $assignment->status = 1;
                        $assignment->created_at = now();
                        $assignment->updated_at = now();
                        $assignment->save();

                        // Fetch assigned user
                        // $user = User::find($assign);
                        // if ($user && $user->email) {
                        //     // Send email
                        //     Mail::raw("Dear {$user->name},\n\nYou have been assigned a new task: {$task->task_name}\n\nPlease check your dashboard for more details.\n\nRegards,\nDigitex CRM", function ($message) use ($user) {
                        //         $message->to($user->email)
                        //                 ->subject('New Task Assigned');
                        //     });
                        // }
                    }
                }
                return response()->json(['success' => true, 'message' => __('Task assigned successfully')]);
            }else{
                return response()->json(['error' => true, 'message' => __('Something went wrong')]);
            }
        }
    }

    public function remove_task_assign(Request $request, $project_id, $task_id, $user_id)
    {
        if($request->ajax()){
            if($project_id && $task_id && $user_id){
                ProjectTaskAssignModel::where('task_id', $task_id)->where('user_id', $user_id)->delete();
                return response()->json(['success' => true, 'message' => __('')]);
            }else{
                return response()->json(['error' => true, 'message' => __('Something went wrong')]);
            }
        }else{
            return response()->json(['error' => true, 'message' => __('Something went wrong')]);
        }
    }

    public function change_task_status($project_id, $task_id, $status_id)
    {
        if(auth()->user()->role == 1 || auth()->user()->role == 7){
            ProjectTasksModel::where('project_id', $project_id)->where('id', $task_id)->update([
                'status' => $status_id,
                'updated_at' => now()
            ]);
            return redirect(route('project.task.detail', [$project_id, $task_id]))->withSuccess(__('Task status updated'));
        }else{
            $assigns = ProjectTaskAssignModel::where('task_id', $task_id)->pluck('user_id')->toArray();
            if(in_array(auth()->user()->id, $assigns)){
                ProjectTasksModel::where('project_id', $project_id)->where('id', $task_id)->update([
                    'status' => $status_id,
                    'updated_at' => now()
                ]);
                return redirect(route('project.task.detail', [$project_id, $task_id]))->withSuccess(__('Task status updated'));
            }else{
                return redirect(route('project.task.detail', [$project_id, $task_id]))->withError(__('Task not found or limited access'));
            }
        }
    }

    public function task_comment_store(Request $request, $project_id, $task_id)
    {
        $task = ProjectTasksModel::find($task_id);

        if($task) {
            $comment = $task->projectTaskComment()->create([
                'comment' => $request->get('comment'),
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if (!$comment) {
                return response()->json(['status' => 'error', 'message' => 'Failed to add comment.'], 500);
            }

            return response()->json(['status' => 'success', 'message' => 'Comment Added Successfully!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Task Not Found.'], 404);
        }
    }

    public function download_task_file()
    {}

    // bugs
    public function bug_list(ProjectBugsDataTable $dataTable, $project_id)
    {
        if(auth()->user()->role == 1){
            $project = ProjectModel::find($project_id);
        }else{
            $assigns = ProjectAssignModel::where('project_id', $project_id)->pluck('user_id')->toArray();
            if(in_array(auth()->user()->id, $assigns)){
                $project = ProjectModel::find($project_id);
            }else{
                return redirect(route('project.list'))->withError(__('Project not found or limited access'));
            }
        }
        return $dataTable->with('project', $project)->render('projects.bugs.bug-list', compact('project'));
    }

    public function bug_create($project_id)
    {
        $viewData = [];
        $viewData['project'] = ProjectModel::find($project_id);
        if(!$viewData['project']){
            return redirect(route('projects.bugs.list', [$project_id]))->withError('Project not found or limited access');
        }
        $viewData['bugStatus'] = ProjectBugStatusModel::whereIn('id', [1, 2, 3, 6, 7, 9, 10, 11])->get();

        return view('projects.bugs.bug-add', $viewData);
    }

    public function bug_store(Request $request, $project_id)
    {
        $project = ProjectModel::find($project_id);

        if($project->count() > 0){
            $validator = Validator::make($request->all(), [
                'bug_name' => 'required|max:200',
                'milestone' => 'required',
                'estimated_time' => 'required|numeric',
                'bug_description' => 'max:1000',
                'status' => 'required',
                'bug_files.*' => 'mimes:png,jpeg,jpg,gif,pdf,doc,docx,mp4,webp|max:10240'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $bug = new ProjectBugsModel();
                $bug->bug_name = $request->get('bug_name', 'Untitled');
                $bug->description = $request->get('bug_description', null);
                $bug->estimated_time = $request->get('estimated_time', 0);
                $bug->project_id = $project_id;
                $bug->milestone_id = $request->get('milestone', '');
                $bug->status = $request->get('status');
                $bug->created_at = now();
                $bug->updated_at = now();
                $bug->save();

                if($request->hasFile('bug_files')){
                    foreach($request->file('bug_files') as $bug_file){
                        $new_filename = pathinfo($bug_file->getClientOriginalName(), PATHINFO_FILENAME).'_'.uniqid().'.'.$bug_file->getClientOriginalExtension();
                        $file_type = $bug_file->getClientMimeType();
                        $file_size = $bug_file->getSize();
                        $bug_file->move(public_path('uploads/project/bug'), $new_filename);
                        $bug->ProjectBugAttachmentModel()->create([
                            'file_name' => $new_filename,
                            'file_type' => $file_type,
                            'file_size' => $file_size,
                            'uploaded_by' => auth()->id(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }

                return redirect(route('project.bug.list', [$project_id]))->withSuccess(__('Bug Added Successfully!'));
            }
        }else{
            return redirect(route('project.list'))->withError(__('Project not found or limited access'));
        }
    }

    public function bug_detail($project_id, $bug_id)
    {
        $project = ProjectModel::find($project_id);
        $bug = ProjectBugsModel::find($bug_id);
        $bug_statuses = ProjectBugStatusModel::where('status', 1)->get();
        $bugComments = ProjectBugCommentModel::with('users')->where('bug_id', $bug_id)->latest()->paginate(3);
        
        return view('projects.bugs.bug-detail', compact('bug', 'project', 'bugComments', 'bug_statuses'));
    }

    public function bug_project_users(Request $request)
    {
        if($request->ajax()){
            $assigns = ProjectAssignModel::where('project_id', $request->get('project_id', null))->pluck('user_id')->toArray();
            $already_assigned = ProjectBugAssignModel::where('bug_id', $request->get('bug_id'))->pluck('user_id')->toArray();
            $rest = array_diff(array_merge($already_assigned, $assigns), array_intersect($assigns, $already_assigned));
            $users = User::whereIn('id', $rest)->get();
            return view('projects.modals.assign_task_resource', compact('users'));
        }
    }

    public function bug_edit(Request $request, $project_id, $bug_id)
    {
        $viewData = [];
        $viewData['project'] = ProjectModel::find($project_id);

        if($viewData['project']) {
                $viewData['bug'] = ProjectBugsModel::find($bug_id);
                $viewData['bugStatus'] = ProjectBugStatusModel::get();
                return view('projects.bugs.bug-edit', $viewData);
        } else {
            return redirect(route('project.list'))->withError(__('Project not found or limited access'));
        }
    }

    public function bug_update(Request $request, $project_id, $bug_id)
    {
        $project = ProjectModel::find($project_id);

        if($project->count() > 0){
            $validator = Validator::make($request->all(), [
                'bug_name' => 'required|max:200',
                'milestone' => 'required',
                'estimated_time' => 'required|numeric',
                'bug_description' => 'max:1000',
                'status' => 'required',
                'bug_files.*' => 'mimes:png,jpeg,jpg,gif,pdf,doc,docx,mp4,webp|max:10240'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $bug = ProjectBugsModel::find($bug_id);
                $bug->update([
                    'bug_name' => $request->get('bug_name'),
                    'description' => $request->get('bug_description'),
                    'estimated_time' => $request->get('estimated_time'),
                    'milestone_id' => $request->get('milestone'),
                    'status' => $request->get('status'),
                    'updated_at' => now()
                ]);

                if($request->hasFile('bug_files')){
                    foreach($request->file('bug_files') as $bug_file){
                        $new_filename = pathinfo($bug_file->getClientOriginalName(), PATHINFO_FILENAME).'_'.uniqid().'.'.$bug_file->getClientOriginalExtension();
                        $file_type = $bug_file->getClientMimeType();
                        $file_size = $bug_file->getSize();
                        $bug_file->move(public_path('uploads/project/bug'), $new_filename);
                        $bug->ProjectBugAttachmentModel()->create([
                            'file_name' => $new_filename,
                            'file_type' => $file_type,
                            'file_size' => $file_size,
                            'uploaded_by' => auth()->id(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }

                return redirect(route('project.bug.list', [$project_id]))->withSuccess(__('Bug Updated Successfully!'));
            }
        }else{
            return redirect(route('project.list'))->withError(__('Project not found or limited access'));
        }
    }

    public function bug_delete(Request $request, $project_id, $bug_id)
    {
        $bug = ProjectBugsModel::find($bug_id);

        if($bug) {
            $bug->bugAssigns()->each(function($assign) {
                $assign->delete();
            });
            $bug->projectBugAttachmentModel()->each(function($attachment) {
                $filePath = public_path('uploads/project/bug/' . $attachment->file_name);
                if(file_exists($filePath)) {
                    unlink($filePath);
                }
                $attachment->delete();
            });
            // $bug->bugComments()->each(function($comment) {
            //     $comment->delete();
            // });
            $bug->delete();

            return redirect(route('project.bug.list', [$project_id]))->withSuccess(__('Bug Deleted Successfully!'));
        } else {
            return redirect(route('project.bug.list', [$project_id]))->withError(__('Bug Not Found.'));
        }

    }

    public function bug_assign(Request $request)
    {
        if($request->ajax()){
            $bug_id = $request->get('bug-id');
            if($bug_id){
                if($request->get('assign')){
                    foreach($request->get('assign') as $assign){
                        $assignment = new ProjectBugAssignModel;
                        $assignment->bug_id = $bug_id;
                        $assignment->user_id = $assign;
                        $assignment->status = 1;
                        $assignment->created_at = now();
                        $assignment->updated_at = now();
                        $assignment->save();
                    }
                }
                return response()->json(['success' => true, 'message' => __('Bug Assigned Successfully.')]);
            }else{
                return response()->json(['error' => true, 'message' => __('Something went wrong.')]);
            }
        }
    }

    public function remove_bug_assign(Request $request, $project_id, $bug_id, $user_id)
    {
        if($request->ajax()){
            if($project_id && $bug_id && $user_id){
                ProjectBugAssignModel::where('bug_id', $bug_id)->where('user_id', $user_id)->delete();
                return response()->json(['success' => true, 'message' => __('Bug assign removed successfully')]);
            }else{
                return response()->json(['error' => true, 'message' => __('Something went wrong')]);
            }
        }else{
            return response()->json(['error' => true, 'message' => __('Something went wrong')]);
        }
    }

    public function change_bug_status($project_id, $bug_id, $status_id)
    {
        if(auth()->user()->role == 1 || auth()->user()->role == 7){
            ProjectBugsModel::where('project_id', $project_id)->where('id', $bug_id)->update([
                'status' => $status_id,
                'updated_at' => now()
            ]);
            return redirect(route('project.bug.detail', [$project_id, $bug_id]))->withSuccess(__('Bug status updated.'));
        }else{
            $assigns = ProjectBugTaskAssignModel::where('bug_id', $bug_id)->pluck('user_id')->toArray();
            if(in_array(auth()->user()->id, $assigns)){
                ProjectBugsModel::where('project_id', $project_id)->where('id', $bug_id)->update([
                    'status' => $status_id,
                    'updated_at' => now()
                ]);
                return redirect(route('project.bug.detail', [$project_id, $bug_id]))->withSuccess(__('Bug status updated.'));
            }else{
                return redirect(route('project.bug.detail', [$project_id, $bug_id]))->withError(__('Bug not found or limited access'));
            }
        }
    }

    public function download_bug_file($project_id, $bug_id, $file_id)
    {
        $bugFile = ProjectBugAttachmentModel::find($file_id);
        if(!$bugFile) {
            return redirect()->back()->withError('Bug File Not Found.');
        }

        $filePath = public_path('uploads/project/bug/' . $bugFile->file_name);
        if(!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File Not Found.');
        }

        return response()->download($filePath, $bugFile->file_name);
    }

    public function bug_comment_store(Request $request, $project_id, $bug_id)
    {
        $bug = ProjectBugsModel::find($bug_id);

        if($bug) {
            $comment = $bug->projectBugComment()->create([
                'comment' => $request->get('comment'),
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if (!$comment) {
                return response()->json(['status' => 'error', 'message' => 'Failed to add comment.'], 500);
            }

            return response()->json(['status' => 'success', 'message' => 'Comment Added Successfully!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Bug Not Found.'], 404);
        }
    }

    // milestones
    public function milestones_list(ProjectMilestonesDataTable $dataTable, $project_id)
    {
        $project = ProjectModel::find($project_id);
        if (!$project) {
            return redirect()->back()->withError(__('Project not found.'));
        }
        
        return $dataTable->with('project', $project)->render('projects.milestones.milestones-list', compact('project'));
    }
    
    public function milestones_create($project_id)
    {
        $project = ProjectModel::find($project_id);
        if (!$project) {
            return redirect()->back()->withError(__('Project not found.'));
        }

        return view('projects.milestones.add-milestone', compact('project'));
    }

    public function milestones_store(Request $request, $project_id)
    {
        $project = ProjectModel::find($project_id);

        if($project->count() > 0){
            $validator = Validator::make($request->all(), [
                'milestone_name' => 'required|max:200',
                'milestone_description' => 'max:1000'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{
                $project->milestones()->create([
                    'milestone_name' => $request->get('milestone_name'),
                    'description' => $request->get('milestone_description', null),
                    'created_by' => auth()->id()
                ]);

                return redirect(route('project.milestones.list', [$project_id]))->withSuccess(__('Milestone Added Successfully!'));
            }
        } else{
            return redirect()->back()->withError(__('Project not found or limited access'));
        }
    }

    public function milestones_detail($project_id, $milestone_id)
    {
        $viewData = [];
        $viewData['project'] = ProjectModel::find($project_id);
        $viewData['milestone'] = ProjectMilestoneModel::find($milestone_id);
        // Milestone Bug Progress Overview
        $viewData['totalBugCount'] = ProjectBugsModel::where(['milestone_id' => $milestone_id])
                                                            ->whereNotIn('status', [4, 8])->distinct('id')->count();
        $viewData['resolvedBugCount'] = ProjectBugsModel::where(['milestone_id' => $milestone_id])
                                                            ->whereIn('status', [5])->distinct('id')->count();
        $viewData['inProgressBugCount'] = ProjectBugsModel::where(['milestone_id' => $milestone_id])
                                                            ->whereIn('status', [1, 2, 3, 6, 7, 9, 10, 11])->distinct('id')->count();
        // Milestone Task Progress Overview
        $viewData['completedTaskCount'] = ProjectTasksModel::where(['milestone_id' => $milestone_id])
                                                            ->whereIn('status', [9, 10])->distinct('id')->count();
        $viewData['inProgressTaskCount'] = ProjectTasksModel::where(['milestone_id' => $milestone_id])
                                                            ->whereIn('status', [4, 5, 6, 7, 8])->distinct('id')->count();
        $viewData['pendingTaskCount'] = ProjectTasksModel::where(['milestone_id' => $milestone_id])
                                                            ->whereIn('status', [1, 2, 3, 11])->distinct('id')->count();
        // Milestones Completion Rate 
        $viewData['allTasks'] = $viewData['completedTaskCount'] + $viewData['inProgressTaskCount'] + $viewData['pendingTaskCount'];
        $viewData['completedTasksRate'] = ($viewData['completedTaskCount'] > 0) ? round(($viewData['completedTaskCount'] / $viewData['allTasks']) * 100, 2) : 0;
        $viewData['allBugs'] = ProjectBugsModel::where(['milestone_id' => $milestone_id])
                                                            ->whereIn('status', [1, 2, 3, 6, 7, 9, 10, 11])->distinct('id')->count();
        $viewData['completedBugsCount'] = ProjectBugsModel::where(['milestone_id' => $milestone_id])
                                                            ->whereIn('status', [5, 8])->distinct('id')->count();
        $viewData['completedBugsRate'] = ($viewData['completedBugsCount'] > 0) ? round(($viewData['completedBugsCount'] / $viewData['allBugs']) * 100, 2) : 0;

        return view('projects.milestones.milestone-detail', $viewData);
    }

    public function milestone_edit($project_id, $milestone_id)
    {
        $viewData = [];
        $viewData['project'] = ProjectModel::find($project_id);
        $viewData['milestone'] = ProjectMilestoneModel::find($milestone_id);

        if (!$viewData['project']) {
            return redirect()->back()->withError(__('Project not found.'));
        }
        if (!$viewData['milestone']) {
            return redirect()->back()->withError(__('Milestone not found.'));
        }

        return view('projects.milestones.milestone-edit', $viewData);
    }

    public function milestone_update(Request $request, $project_id, $milestone_id)
    {
        $milestone = ProjectMilestoneModel::find($milestone_id);

        if($milestone){
            $validator = Validator::make($request->all(), [
                'milestone_name' => 'required|max:200',
                'milestone_description' => 'max:1000'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{
                $milestone->update([
                    'milestone_name' => $request->get('milestone_name'),
                    'description' => $request->get('milestone_description')
                ]);

                return redirect(route('project.milestones.list', [$project_id]))->withSuccess(__('Milestone Updated Successfully!'));
            }
        } else{
            return redirect()->back()->withError(__('Milestone Not Found.'));
        }
    }

    public function milestones_delete($project_id, $milestone_id)
    {
        $milestone = ProjectMilestoneModel::find($milestone_id);

        if($milestone){
            // delete tasks
            $milestone->milestoneTasks()->each(function($task) {
                $task->taskAssigns()->delete();
            });
            $milestone->milestoneTasks()->each(function($task) {
                $task->taskAttachment()->delete();
            });
            $milestone->milestoneTasks()->delete();
            // delete bugs
            $milestone->milestoneBugs()->each(function($bug) {
                $bug->bugAssigns()->delete();
            });
            $milestone->milestoneBugs()->delete();
            // delete milestone
            $milestone->delete();

            return redirect(route('project.milestones.list', [$project_id]))->withSuccess(__('Milestone Deleted Successfully!'));
        }else{
            return redirect(route('project.milestones.list', [$project_id]))->withSuccess(__('Milestone Not Found.'));
        }
    }

    // ajax calls
    public function get_users(Request $request)
    {
        if($request->ajax()){
            $project_assigns = ProjectAssignModel::where('project_id', $request->get('project_id'))->pluck('user_id')->toArray();
            $users = User::whereNotIn('id', $project_assigns)->get();
            return view('projects.modals.assign_resource', compact('users'));
        }
    }

    public function get_project_users(Request $request)
    {
        if($request->ajax()){
            $assigns = ProjectAssignModel::where('project_id', $request->get('project_id', ''))->pluck('user_id')->toArray();
            $already_assigned = ProjectTaskAssignModel::where('task_id', $request->get('task_id'))->pluck('user_id')->toArray();
            $rest = array_diff(array_merge($already_assigned, $assigns), array_intersect($assigns, $already_assigned));
            $users = User::whereIn('id', $rest)->get();
            return view('projects.modals.assign_task_resource', compact('users'));
        }
    }
}
