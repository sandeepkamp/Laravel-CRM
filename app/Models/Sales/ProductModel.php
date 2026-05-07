<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $table = 'products';

    public function subscription(){

        return $this->hasOne(DealSubscription::class, 'product_id','id');
    }
}
