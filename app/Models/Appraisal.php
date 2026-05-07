<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appraisal extends Model
{
    protected $table = 'appraisals';

    protected $fillable = [
        'employee_id',
        'period',
        'reviewer_id',
        'rating',
        'comments',
        'reviewed_at',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'reviewed_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Employee relationship
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // Reviewer relationship
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
