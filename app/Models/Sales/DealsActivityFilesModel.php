<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\Sales\DealsActivityModel;

class DealsActivityFilesModel extends Model
{
    use HasFactory;

    protected $table = 'files';
    protected $fillable = [
        'deal_id',
        'file_name',
        'original_name',
        'extension',
        'file_type',
        'created_at',
        'updated_at'
    ];

    public function deal_activity()
    {
        return $this->belongsTo(DealsActivityModel::class, 'deal_id', 'id');
    }
}
