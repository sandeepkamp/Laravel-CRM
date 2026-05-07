<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    protected $table = 'job_postings';

    protected $fillable = [
        'title',
        'description',
        'department_id',
        'location',
        'posted_date',
        'status',
    ];

    protected $casts = [
        'posted_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship: Department
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}
