<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadLabels extends Model
{
    use HasFactory;

    protected $table = 'lead_labels';

    public function lead()
    {
        return $this->belongsTo(LeadsModel::class, 'lead_id', 'id');
    }
    public function label()
    {
        return $this->hasOne(LeadLabelList::class, 'id', 'lead_label');
    }
}
