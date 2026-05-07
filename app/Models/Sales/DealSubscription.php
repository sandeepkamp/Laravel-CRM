<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealSubscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'deals_subscriptions';  
}
