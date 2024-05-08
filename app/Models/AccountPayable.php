<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPayable extends Model
{
    use HasFactory;
    protected $connection = 'student_service';
    protected $fillable = ['invoice_number','admission_no','outstanding_balance', 'amount', 'type', 'due_date', 'eligibility', 'status'];

    public function studentDetails()
    {
        return $this->hasMany(StudentDetail::class, 'sd_admission_no', 'admission_no');
    }

    
}
