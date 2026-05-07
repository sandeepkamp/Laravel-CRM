<?php

namespace App\Models;

use App\Models\Sales\DealsModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class HostingDetails extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'hosting_purchase';
    
    protected $fillable = [
        'client_id', 'hosting_provider_id', 'plan_details', 'date_of_purchase', 
        'activation_date', 'expiry_date', 'cost', 'status',
    ];
    
    const STATUS_ACTIVATE_PLAN = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_RENEW = 2;

    // Relationship with client (DealsModel)
    public function client()
    {
        return $this->belongsTo(DealsModel::class, 'client_id');
    }

    // Relationship with hosting provider
    public function hostingProvider()
    {
        return $this->belongsTo(HostingProvider::class, 'hosting_provider_id');
    }
    
    public function getStatusLabelAttribute()
    {
        if ($this->expiry_date && $this->expiry_date < now()) {
            return 'Renew';
        }
        return $this->status == self::STATUS_ACTIVE ? 'Active' : 'Inactive';
    }

}



   