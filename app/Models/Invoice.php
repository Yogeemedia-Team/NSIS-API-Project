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
        'status'
    ];

    
}
