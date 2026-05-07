<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\Projects\ProjectMilestoneModel;
use App\Models\Projects\ProjectModel;
use App\Models\Projects\ProjectBugAssignModel;
use App\Models\User;
use App\Models\Projects\ProjectBugStatusModel;

class ProjectBugsModel extends Model
{
    protected $table = 'project_bug';
    protected $fillable = [
        'bug_name',
        'description',
        'estimated_time',
        'project_id',
        'milestone_id',
        'status',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'bug_name' => 'string',
        'description' => 'string',
        'estimated_time' => 'integer',
        'project_id' => 'integer',
        'milestone_id' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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
        return $this->hasManyThrough(User::class, ProjectBugAssignModel::class, 'bug_id', 'id', 'id', 'user_id');
    }

    public function status()
    {
        return $this->hasOne(ProjectBugStatusModel::class, 'id', 'status');
    }

    public function bugAssigns()
    {
        return $this->hasMany(ProjectBugAssignModel::class, 'bug_id', 'id');
    }

    public function projectBugAttachmentModel()
    {
        return $this->hasMany(ProjectBugAttachmentModel::class, 'bug_id', 'id');
    }

    public function bugStatus()
    {
        return $this->belongsTo(ProjectBugStatusModel::class, 'status', 'id');
    }

    public function projectBugComment()
    {
        return $this->hasMany(ProjectBugCommentModel::class, 'bug_id', 'id');
    }
}
