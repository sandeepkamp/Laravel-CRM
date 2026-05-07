<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'leave_types';

    protected $fillable = [
        'name',
        'description',
        'max_days_per_year',
        'bg_color',
        'font_color',
    ];

    protected $casts = [
        'max_days_per_year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
