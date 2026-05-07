<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Projects\ProjectTasksModel;

class ProjectTaskAssignModel extends Model
{
    protected $table = 'task_assigns';
    protected $fillable = [
        'task_id',
        'user_id',
        'status',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'task_id' => 'integer',
        'user_id' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function task()
    {
        return $this->belongsTo(ProjectTasksModel::class, 'id', 'task_id');
    }
    
    public function assign()
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

    public function projectTasks() {
        return $this->belongsTo(ProjectTasksModel::class,  'task_id');
    }

    public function assignUsers()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
