<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resignation extends Model
{
    protected $table = 'resignations';

    protected $fillable = [
        'employee_id',
        'reason',
        'resignation_date',
        'last_working_day',
        'status',
        'approved_by',
    ];

    protected $casts = [
        'resignation_date' => 'date',
        'last_working_day' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with employee
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // Relationship with approver
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
