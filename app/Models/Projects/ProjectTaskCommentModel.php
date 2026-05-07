<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\Projects\ProjectMilestoneModel;
use App\Models\Projects\ProjectModel;
use App\Models\Projects\ProjectBugAssignModel;
use App\Models\User;
use App\Models\Projects\ProjectBugStatusModel;

class ProjectTaskCommentModel extends Model
{
    protected $table = 'project_task_comments';

    protected $fillable = [
        'comment',
        'task_id',
        'user_id',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'comment' => 'string',
        'task_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
