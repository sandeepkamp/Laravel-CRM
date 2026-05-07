<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $table = 'interviews';

    protected $fillable = [
        'applicant_id',
        'interviewer_id',
        'scheduled_date',
        'feedback',
        'status',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship: Applicant
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    // Relationship: Interviewer (User model used for employees)
    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}
