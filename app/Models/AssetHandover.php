<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetHandover extends Model
{
    protected $table = 'asset_handovers';

    protected $fillable = [
        'employee_id',
        'asset_name',
        'handover_status',
        'handover_date',
    ];

    protected $casts = [
        'handover_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
