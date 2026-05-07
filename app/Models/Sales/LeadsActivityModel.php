<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sales\LeadsModel;

class LeadsActivityModel extends Model
{
    use HasFactory;
    protected $table = 'leads_activity';
    protected $with = ['lead'];
    protected $casts = [
        'time_from' => 'datetime',
        'time_to' => 'datetime'
    ];

    public function lead()
    {
        return $this->belongsTo(LeadsModel::class, 'lead_id', 'id');
    }
}
