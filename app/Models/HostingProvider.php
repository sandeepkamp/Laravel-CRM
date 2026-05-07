<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostingProvider extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'hosting_provider';

    // Primary key
    protected $primaryKey = 'provider_id';

    // Allow mass assignment
    protected $fillable = [
        'provider_name',
    ];

    // Relationships
    public function hostingDetails()
    {
        return $this->hasMany(HostingDetails::class, 'hosting_provider_id', 'provider_id');
    }
}
