<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name','owner_user_id'];

    public function owner()
    {
        return $this->belongsTo(User::class,'owner_user_id');
    }

    public function members()
    {
        
        return GroupUsers::where('group_id','=',$this->id)->count();
    }

}
