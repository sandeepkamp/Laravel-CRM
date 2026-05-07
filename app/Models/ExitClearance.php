<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExitClearance extends Model
{
    protected $table = 'exit_clearances';

    protected $fillable = [
        'employee_id',
        'clearance_by',
        'status',
        'remarks',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function clearedBy()
    {
        return $this->belongsTo(User::class, 'clearance_by');
    }
}
