<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\Projects\ProjectBugsModel;
use App\Models\Projects\ProjectTasksModel;
use App\Models\Projects\ProjectModel;


class ProjectMilestoneModel extends Model
{
    protected $table = 'milestones';
    protected $fillable = [
        'project_id',
        'milestone_name',
        'description',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'project_id' => 'integer',
        'milestone_name' => 'string',
        'description' => 'string',
        'created_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function project()
    {
        return $this->belongsTo(ProjectModel::class, 'id', 'project_id');
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTasksModel::class, 'id', 'milestone_id');
    }

    public function bugs()
    {
        return $this->hasMany(ProjectBugsModel::class, 'id', 'milestone_id');
    }

    public function projectModel() 
    {
        return $this->belongsTo(ProjectModel::class, 'project_id', 'id');
    }

    public function milestoneTasks()
    {
        return $this->hasMany(ProjectTasksModel::class, 'milestone_id', 'id');
    }

    public function milestoneBugs()
    {
        return $this->hasMany(ProjectBugsModel::class, 'milestone_id', 'id');
    }
}
