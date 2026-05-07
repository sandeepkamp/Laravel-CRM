<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\Projects\ProjectBugsModel;

class ProjectBugStatusModel extends Model
{
    protected $table = 'bug_status';
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

    public function bugs()
    {
        return $this->hasMany(ProjectBugsModel::class, 'status', 'id');
    }
}
