<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferLetter extends Model
{
    protected $table = 'offer_letters';

    protected $fillable = [
        'applicant_id',
        'offer_date',
        'joining_date',
        'offer_file_path',
        'status',
    ];

    protected $casts = [
        'offer_date' => 'date',
        'joining_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship: Applicant
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }
}
