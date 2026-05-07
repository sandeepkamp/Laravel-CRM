<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Sales\LeadsModel;
use App\Models\Sales\DealsModel;

class ReportingPerson extends Model

{
    use HasFactory;

    protected $table = 'reporting_person';

    protected $fillable = [
        'emp_id',
        'reporting_person_id',
    ];
    
    public function reportingPerson()
    
    {
        return $this->belongsTo(User::class,'reporting_person_id','id');
    }
    
    public function subordinates()
    
    {
        return $this->belongsTo(User::class,'emp_id','id');
    }
}

