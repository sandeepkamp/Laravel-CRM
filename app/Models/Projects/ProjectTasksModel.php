<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\Projects\ProjectMilestoneModel;
use App\Models\Projects\ProjectModel;
use App\Models\Projects\ProjectTaskAssignModel;
use App\Models\User;
use App\Models\Projects\ProjectTaskStatusModel;
use App\Models\Projects\ProjectTaskAttachmentModel;

class ProjectTasksModel extends Model
{
    protected $table = 'project_tasks';
    protected $fillable = [
        'task_name',
        'description',
        'estimated_time',
        'start_time',
        'end_time',
        'project_id',
        'milestone_id',
        'due_date',
        'priority',
        'status',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'task_name' => 'string',
        'description' => 'string',
        'estimated_time' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'project_id' => 'integer',
        'milestone_id' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(ProjectModel::class, 'project_id', 'id');
    }

    public function milestone()
    {
        return $this->belongsTo(ProjectMilestoneModel::class, 'milestone_id', 'id');
    }

    public function assigns()
    {
        return $this->hasManyThrough(User::class, ProjectTaskAssignModel::class, 'task_id', 'id', 'id', 'user_id');
    }

    public function status()
    {
        return $this->hasOne(ProjectTaskStatusModel::class, 'id', 'status');
    }
    
    public function files()
    {
        return $this->hasMany(ProjectTaskAttachmentModel::class, 'task_id', 'id');
    }

    public function statusDetail() 
    {
        return $this->belongsTo(ProjectTaskStatusModel::class, 'status');
    }
    public function taskAssigns() 
    {
        return $this->hasMany(ProjectTaskAssignModel::class, 'task_id', 'id');
    }

    public function taskStatus()
    {
        return $this->hasOne(ProjectTaskStatusModel::class, 'id', 'status');
    }

    public function taskAttachment()
    {
        return $this->hasMany(ProjectTaskAttachmentModel::class, 'task_id', 'id');
    }

    public function projectTaskComment()
    {
        return $this->hasMany(ProjectTaskCommentModel::class, 'task_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_assigns', 'task_id', 'user_id');
    }
}
