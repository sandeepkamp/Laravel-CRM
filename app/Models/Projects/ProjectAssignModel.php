<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\Projects\ProjectModel;

class ProjectAssignModel extends Model
{
    protected $table = 'project_assigns';
    protected $fillable = [
        'project_id',
        'user_id',
        'assigned_by',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'project_id' => 'integer',
        'user_id' => 'integer',
        'assigned_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function project()
    {
        return $this->belongsTo(ProjectModel::class, 'project_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
