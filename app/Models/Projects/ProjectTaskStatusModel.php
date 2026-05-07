<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\Projects\ProjectTasksModel;

class ProjectTaskStatusModel extends Model
{
    protected $table = 'task_status';
    protected $fillable = [
        'status_name',
        'slug',
        'description',
        'bg_color',
        'font_color',
        'status',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'status_name' => 'string',
        'slug' => 'string',
        'description' => 'string',
        'bg_color' => 'string',
        'font_color' => 'string',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function tasks()
    {
        return $this->hasMany(ProjectTasksModel::class, 'status', 'id');
    }
}
