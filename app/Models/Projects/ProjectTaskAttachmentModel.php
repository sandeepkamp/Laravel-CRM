<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\Projects\ProjectTasksModel;
use App\Models\User;

class ProjectTaskAttachmentModel extends Model
{
    protected $table = 'task_attachments';
    protected $fillable = [
        'file_name',
        'file_type',
        'file_size',
        'task_id',
        'uploaded_by',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'file_name' => 'string',
        'file_type' => 'string',
        'file_size' => 'integer',
        'task_id' => 'integer',
        'uploaded_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function task()
    {
        return $this->belongsTo(ProjectTasksModel::class, 'id', 'task_id');
    }

    public function uploaded_by()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}
