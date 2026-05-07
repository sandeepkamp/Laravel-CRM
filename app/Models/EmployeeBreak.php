<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeBreak extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'start_time',
        'end_time',
        'duration',
        'reason',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
