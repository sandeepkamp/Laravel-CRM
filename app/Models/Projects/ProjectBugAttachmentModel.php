<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\Projects\ProjectMilestoneModel;
use App\Models\Projects\ProjectModel;
use App\Models\Projects\ProjectBugAssignModel;
use App\Models\User;
use App\Models\Projects\ProjectBugStatusModel;

class ProjectBugAttachmentModel extends Model
{
    protected $table = 'bug_attachments';

    protected $fillable = [
        'file_name',
        'file_type',
        'file_size',
        'bug_id',
        'uploaded_by',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'file_name' => 'string',
        'file_type' => 'string',
        'file_size' => 'integer',
        'bug_id' => 'integer',
        'uploaded_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
