<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankStatement extends Model
{
    protected $table = 'bank_statements';

    protected $fillable = [
        'employee_id',
        'bank_name',
        'branch_name',
        'account_number',
        'ifsc_code',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship: Employee (User)
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }
}
