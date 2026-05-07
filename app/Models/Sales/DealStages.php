<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealStages extends Model
{
    use HasFactory;

     protected $table = 'deals_stages';
     protected $appends = ['stage_data'];

     public function getStageDataAttribute()
     {
        return DealStagesList::where('id', $this->stage)->first();
     }
     
     public function stage()
    {
        return $this->hasOne(DealStagesList::class, 'id', 'stage_id'); // Fix the foreign key column reference
    }

}
