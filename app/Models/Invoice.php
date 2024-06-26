<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $connection = 'student_service';
    protected $fillable = [
        'invoice_number',
        'admission_no',
        'due_date',
        'invoice_total',
        'total_paid', 
        'total_due',
        'status',
        'new_total_due',
        'current_total_outstanding'
    ];

    public function accountPaybles()
    {
        return $this->hasMany(AccountPayable::class, 'invoice_number','invoice_number');
    }

    
}
