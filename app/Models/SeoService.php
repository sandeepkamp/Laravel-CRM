<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Sales\DealsModel;

class SeoService extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'website_url',
        'industry',
        'seo_package',
        'start_date',
        'renewal_date',
        'status',
    ];
    
    public function client()
    {
        return $this->belongsTo(DealsModel::class, 'client_id');
    }

}
