<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeHistory extends Model
{
    protected $table = 'employee_histories';

    protected $fillable = [
        'employee_id',
        'company_name',
        'designation',
        'from_date',
        'to_date',
        'reason_for_leaving',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    public function submittedByUser()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
