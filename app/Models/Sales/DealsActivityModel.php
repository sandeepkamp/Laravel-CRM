<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sales\DealsModel;

class DealsActivityModel extends Model
{
    use HasFactory;

    protected $table = 'deals_activity';

    public function lead()
    {
        return $this->belongsTo(DealsModel::class, 'deal_id', 'id');
    }

    public function files()
    {
        return $this->hasOne(DealsActivityFilesModel::class, 'activity_id', 'id');
    }
}
