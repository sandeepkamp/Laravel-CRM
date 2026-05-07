<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerHosting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_name',
        'domain_name',
        'hosting_plan',
        // 'hosting_feature_id',
        'start_date',
        'expiry_date',
        'status',
    ];
}
