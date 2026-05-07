<?php

namespace App\Http\Controllers\Client;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Projects\ClientModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Projects\ProjectModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Projects\ProjectTasksModel;
use App\Models\Projects\ProjectFeedbackModel;
use App\Models\Projects\ProjectMilestoneModel;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $viewData = [];
        $clientId = Auth::guard('clients')->id();

        // ---statistics---
        $viewData['projectsCount'] = ProjectModel::whereClientId($clientId)->count();
        $viewData['completedProjectsCount'] = ProjectModel::where(['client_id' => $clientId, 'status' => 7])->count(); // 7 => completed projetcs
        $viewData['completedTaskCount'] = ProjectTasksModel::whereHas('project', function($q) use($clientId) {
                $q->where('client_id', $clientId);
            })->whereStatus(9)->count(); // 9 => completed task
        $viewData['milestonesCount'] = ProjectMilestoneModel::whereHas('projectModel', function ($q) use ($clientId) {
                $q->where('client_id', $clientId);
            })->count();

        // Budget Evalution
        $viewData['budgets'] = ProjectModel::with(['tasks' => function($q) {
                $q->select('project_id', 'estimated_time');
            }])
            ->where('client_id', $clientId)
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
        
        // Deliverables Due This Week
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();
        $viewData['deliverables'] = ProjectModel::where('client_id', $clientId)
                                ->whereBetween('end_date', [$startOfWeek, $endOfWeek])
                                ->get();

        // dd($viewData);
        return view('customer.dashboard', $viewData);
    }

    /*
     * show profile edit page
     */

    public function profile()
    {
        $client = Auth::guard('clients')->user();

        return view('customer.profile', compact('client'));
    }

    /*
     * update profile
     */

    public function updateProfile(Request $request)
    {
        $client = auth()->guard('clients')->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email:rfc,dns|max:255|unique:clients,email,'.$client->id,
            'password' => 'exclude_if:password,null|string|min:8|same:confirm_password',
            'confirm_password' => 'required_with:password'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else{
            $client->first_name = $request->input('first_name', 'Digitex');
            $client->last_name = $request->input('last_name', 'Technologies');
            $client->email = $request->input('email');
            $client->phone = $request->input('phone');
            if($request->input('password') && $request->input('confirm_password')){
                $client->password = Hash::make($request->input('password'));
            }

            $client->save();

            return redirect()->back()->withSuccess('Profile updated successfully');
        }
    }

    public function projectEvaluation(Request $request)
    {
        $clientId = Auth::guard('clients')->id();
        $query = ProjectFeedbackModel::whereClientId($clientId)->with(['projectModel']);

        return DataTables::of($query->limit(5))
                ->filter(function ($que) use ($request) {
                    if ($request->has('search') && !empty($request->input('search.value'))) {
                        $search = $request->input('search.value');
                        $que->where(function ($q) use ($search) {
                            $q->whereHas('projectModel', function ($q) use ($search) {
                                $q->where('project_name', 'LIKE', "%{$search}%");
                            });
                        });          
                    }
                })
                ->addIndexColumn()
                ->addColumn('project_name', function ($row) {
                    return $row->projectModel ? '<a href="'.route('clients.projects-details', $row->project_id).'">'.ucwords($row->projectModel->project_name).'</a>' : 'N/A';
                })
                ->addColumn('comment', function ($row) {
                    $comment = $row->comment;
                    if (strlen($comment) > 50) {
                        return '<span>' . substr($comment, 0, 15) . '...</span>
                                <a href="javascript:void(0)" class="text-primary read-more" data-description="' . htmlspecialchars($comment) . '" data-description-label="'.ucwords($row->projectModel->project_name).': Remarks" title="Read More">Read More</a>';
                    } else {
                        return $comment;
                    }
                })
                ->rawColumns(['comment', 'project_name'])
                ->make(true);
    }

    public function ongoingProjects(Request $request)
    {
        $clientId = Auth::guard('clients')->id();
        $query = ProjectModel::where('client_id', $clientId)
                    ->whereIn('status', [1, 2, 3, 4, 5, 6]);

        // Apply DataTables search filter
        if ($request->has('search') && !empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('project_name', 'LIKE', "%{$search}%");
            });          
        }

        return DataTables::of($query->limit(5))
                ->addIndexColumn()
                ->addColumn('project_name', function($row) {
                    return '<a href="'.route('clients.projects-details', $row->id).'">'.ucwords($row->project_name).'</a>';
                })
                ->addColumn('project_hours', function($row) {
                    return intdiv($row->project_hours, 60) . 'h ' . ($row->project_hours % 60) . 'm';
                })
                ->addColumn('start_date', function($row) {
                    return $row->start_date ? $row->start_date->format('jS M, Y') : 'NA';
                })
                ->addColumn('end_date', function($row) {
                    return $row->end_date ? $row->end_date->format('jS M, Y') : 'NA';
                })
                ->addColumn('status', function($row) {
                    $statusDescriptions = [
                        1 => ['text' => 'Initiation Stage', 'color' => 'bg-primary'],
                        2 => ['text' => 'Planning Stage', 'color' => 'bg-info'],
                        3 => ['text' => 'Execution Stage', 'color' => 'bg-warning'],
                        4 => ['text' => 'Monitoring & Controlling Stage', 'color' => 'bg-dark'],
                        5 => ['text' => 'Testing Phase', 'color' => 'bg-danger'],
                        6 => ['text' => 'Closure Stage', 'color' => 'bg-success']
                    ];
                    $status = $statusDescriptions[$row->status] ?? ['text' => 'Unknown Status', 'color' => 'bg-secondary'];
                    return '<span class="badge ' . $status['color'] . '">' . $status['text'] . '</span>';
                })
                ->rawColumns(['project_name', 'status'])
                ->make(true);
    }
}
