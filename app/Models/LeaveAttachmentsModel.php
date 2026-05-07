<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\LeaveManagementModel;

class LeaveAttachmentsModel extends Model
{
    protected $table = 'leave_attachments';
    protected $casts = [
        'leave_id' => 'integer',
        'file_name' => 'string',
        'file_type' => 'string',
        'file_size' => 'integer',
        'uploaded_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected $fillable = [
        'leave_id',
        'file_name',
        'file_type',
        'file_size',
        'uploaded_by',
        'created_at',
        'updated_at'
    ];

    public function leave()
    {
        return $this->belongsTo(LeaveManagementModel::class, 'id', 'leave_id');
    }
}
