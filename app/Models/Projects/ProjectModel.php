<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\Projects\ClientModel;
use App\Models\User;
use App\Models\Projects\ProjectAssignModel;
use App\Models\Projects\ProjectMilestoneModel;
use App\Models\Projects\ProjectBugsModel;
use App\Models\Projects\ProjectTasksModel;

class ProjectModel extends Model
{
    protected $table = 'projects';
    protected $fillable = [
        'project_name',
        'description',
        'project_hours',
        'status',
        'client_id',
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'project_name' => 'string',
        'description' => 'string',
        'project_hours' => 'integer',
        'status' => 'integer',
        'client_id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function client()
    {
        return $this->hasOne(ClientModel::class, 'id', 'client_id');
    }

    public function assigns()
    {
        return $this->hasManyThrough(User::class, ProjectAssignModel::class, 'project_id', 'id', 'id', 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTasksModel::class, 'project_id', 'id');
    }

    public function bugs()
    {
        return $this->hasMany(ProjectBugsModel::class, 'project_id', 'id');
    }

    public function milestones()
    {
        return $this->hasMany(ProjectMilestoneModel::class, 'project_id', 'id');
    }

    public function projectAssignes()
    {
        return $this->hasMany(ProjectAssignModel::class, 'project_id');
    }

    public function projectFeedback()
    {
        return $this->hasOne(ProjectFeedbackModel::class, 'project_id');
    }
}
