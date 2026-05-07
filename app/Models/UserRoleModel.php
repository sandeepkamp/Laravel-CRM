<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class UserRoleModel extends Model

{

    use HasFactory;
    
    protected $table = 'user_role';
    protected $fillable = ['name', 'department_id'];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

}

