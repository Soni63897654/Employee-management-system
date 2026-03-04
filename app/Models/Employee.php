<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'employee_code',
        'department_id',
        'manager_id',
        'joining_date',
        'email',
        'phone_number',
    ];

    protected $casts = [
    'joining_date' => 'date:Y-m-d',
];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function manager() {
     return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }
}