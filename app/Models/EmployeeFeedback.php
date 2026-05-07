<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeFeedback extends Model
{
    protected $table = 'employee_feedbacks';

    protected $fillable = [
        'employee_id',
        'submitted_by',
        'feedback_text',
        'rating',
        'quarter',
        'submitted_at',
        'update_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'update_at' => 'datetime',
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
        return $this->belongsTo(User::class, 'submitted_by', 'id');
    }
}
