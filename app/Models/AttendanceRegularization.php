<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRegularization extends Model
{
    protected $table = 'attendance_regularizations';

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'reason',
        'status',
        'requested_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'requested_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship: Employee (User)
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
