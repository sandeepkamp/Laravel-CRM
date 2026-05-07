<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

use App\Models\Projects\ProjectModel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ClientResetPasswordNotification;

class ClientModel extends Authenticatable
{
    use Notifiable;

    protected $table = 'clients';
    protected $guard = 'clients';
    protected $appends = [
        'full_name'
    ];
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'status',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'full_name' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'password' => 'hashed',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ClientResetPasswordNotification($token));
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function projects()
    {
        return $this->hasMany(ProjectModel::class, 'client_id', 'id');
    }
}
