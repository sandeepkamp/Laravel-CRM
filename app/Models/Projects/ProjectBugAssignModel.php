<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Projects\ProjectBugsModel;

class ProjectBugAssignModel extends Model
{
    protected $table = 'bug_assigns';
    protected $fillable = [
        'bug_id',
        'user_id',
        'status',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'bug_id' => 'integer',
        'user_id' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function bug()
    {
        return $this->belongsTo(ProjectBugsModel::class, 'id', 'bug_id');
    }
    
    public function assign()
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }
}
