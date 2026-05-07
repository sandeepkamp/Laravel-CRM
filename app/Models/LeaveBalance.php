<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $table = 'leave_balances';

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'year',
        'opening_balance',
        'used_days',
        'closing_balance',
    ];

    protected $casts = [
        'year' => 'integer',
        'opening_balance' => 'integer',
        'used_days' => 'integer',
        'closing_balance' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id');
    }
}
