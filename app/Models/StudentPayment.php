<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPayment extends Model
{
    use HasFactory;
    protected $connection = 'student_service';
    protected $fillable = ['invoice_id', 'payment_id', 'admission_no', 'date', 'due_date', 'outstanding_balance', 'total','total_due','status','paid_from'];

    public function studentDetailwithInvoice()
    {
        return $this->hasMany(AccountPayable::class, 'admission_no', 'sd_admission_no');
    }

    public function accountPayable()
    {
        return $this->belongsTo(AccountPayable::class, 'invoice_id', 'invoice_number');
    }

}
