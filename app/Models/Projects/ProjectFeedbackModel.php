<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\Projects\ProjectBugsModel;
use App\Models\Projects\ProjectTasksModel;
use App\Models\Projects\ProjectModel;


class ProjectFeedbackModel extends Model
{
    protected $table = 'project_feedback';

    protected $fillable = [
        'project_id',
        'client_id',
        'comment',
        'rating',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'project_id' => 'integer',
        'client_id' => 'integer',
        'comment' => 'string',
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function projectModel() 
    {
        return $this->belongsTo(ProjectModel::class, 'project_id', 'id');
    }
}
