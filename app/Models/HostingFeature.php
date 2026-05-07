<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Factories\HasFactory;

class HostingFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_type',
        'storage',
        'bandwidth',
        'no_of_emails',
        'ssl',
        'backup',
    ];
}
