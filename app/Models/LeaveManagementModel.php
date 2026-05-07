<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\LeaveAttachmentsModel;

class LeaveManagementModel extends Model
{
    protected $table = 'leaves';
    protected $casts = [
        'description' => 'string',
        'user_id' => 'integer',
        'leave_type_id' => 'integer',
        'leave_from' => 'datetime',
        'leave_to' => 'datetime',
        'submitted_by' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected $fillable = [
        'description',
        'user_id',
        'leave_type_id',
        'leave_from',
        'leave_to',
        'submitted_by',
        'status',
        'created_at',
        'updated_at'
    ];

    public function attachments()
    {
        return $this->hasMany(LeaveAttachmentsModel::class, 'leave_id', 'id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function leaveTypes()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

}
