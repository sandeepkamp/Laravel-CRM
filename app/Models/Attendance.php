<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';

    protected $fillable = [
        'employee_id',
        'employee_code',
        'employee_name',
        'date',
        'punch_in',
        'punch_out',
        'lunch_start',
        'lunch_end',
        'break_start',
        'break_end',
    ];

    // Employee relationship
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
