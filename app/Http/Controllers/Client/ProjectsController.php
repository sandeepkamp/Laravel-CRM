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
use App\Models\Projects\ProjectBugsModel;
use App\Models\Projects\ProjectTasksModel;
use App\Models\Projects\ProjectFeedbackModel;
use App\Models\Projects\ProjectMilestoneModel;
use App\DataTables\Projects\ClientsTasksDataTable;
use App\Models\Projects\ProjectTaskAttachmentModel;
use App\DataTables\Projects\ClientsProjectsDataTable;
use App\DataTables\Projects\ClientsMilestoneDataTable;
use App\DataTables\Projects\ClientsProjectBugsDataTable;

class ProjectsController extends Controller
{
    public function index(Request $request)
    {
        $viewData = [];
        $clientId = Auth::guard('clients')->id();

        // ---projects overview---
        $viewData['totalProjectsCount'] = ProjectModel::whereClientId($clientId)->count();
        $viewData['ongoingProjectsCount'] = ProjectModel::where('client_id', $clientId)
                            ->whereIn('status', [1, 2, 3, 4, 5, 6]) // 7 => completed projetcs, 8 => Handed over to client
                            ->count();
        $viewData['completedProjectsCount'] = ProjectModel::where(['client_id' => $clientId, 'status' => 7])->count(); // 7 => completed projetcs
        $viewData['handedOverProjectsCount'] = ProjectModel::where(['client_id' => $clientId, 'status' => 8])->count(); // 8 => Handed over to client
 
        return view('customer.projects.index', $viewData);
    }

    public function projectsList(ClientsProjectsDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    public function projectDetails($project_id)
    {
        $project = ProjectModel::find($project_id);
            
        return view('customer.projects.details', compact('project'));
    }

    public function projectsTaskList(ClientsTasksDataTable $dataTable, $project_id)
    {
        $project = ProjectModel::find($project_id);
        
        return $dataTable->with('project', $project)->render('customer.tasks.task-list', compact('project'));
    }

    public function feedbackSubmission(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer',
            'client_id'  => 'required|integer',
            'comment'    => 'nullable|string|max:1000',
            'rating'     => 'required|integer|min:1|max:5',
        ]);

        $feedback = new ProjectFeedbackModel();
        $feedback->project_id = $request->project_id;
        $feedback->client_id  = $request->client_id;
        $feedback->comment    = $request->comment;
        $feedback->rating     = $request->rating;
        $feedback->save();

        return response()->json(['success' => true, 'message' => 'Feedback given successfully!']);
    }

    public function projectTaskDetail($project_id, $task_id)
    {
        $project = ProjectModel::find($project_id);
        $task = ProjectTasksModel::find($task_id);
        
        return view('customer.tasks.task-details', compact('task', 'project'));
    }
    
    public function downloadTaskFile($projectId, $taskId, $fileId)
    {
        $file = ProjectTaskAttachmentModel::find($fileId);
        $filePath = public_path('uploads/project/task/' . $file->file_name);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }
        
        return response()->download($filePath, $file->file_name);
    }
    
    public function projectMilestoneList(ClientsMilestoneDataTable $dataTable, $project_id)
    {
        $project = ProjectModel::find($project_id);
        
        return $dataTable->with('project', $project)->render('customer.milestones.list', compact('project'));
    }
    
    public function projectMilestoneDetail($id, $milestone_id)
    {
        $project = ProjectModel::find($id);
        $milestone = ProjectMilestoneModel::find($milestone_id);
        
        return view('customer.milestones.detail', compact('project', 'milestone'));
    }
    
    public function projectBugList(ClientsProjectBugsDataTable $dataTable, $project_id)
    {
        $project = ProjectModel::find($project_id);
        
        return $dataTable->with('project', $project)->render('customer.bugs.bug-list', compact('project'));
    }
    
    public function projectBugDetail($project_id, $bug_id)
    {
        $project = ProjectModel::find($project_id);
        $bug = ProjectBugsModel::find($bug_id);
        
        return view('customer.bugs.bug-detail', compact('bug', 'project'));
    }
}
