<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    protected $table = 'salary_payments';

    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'gross_salary',
        'payment_mode',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'gross_salary' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }
}
