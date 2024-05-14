<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionInstalment extends Model
{
    use HasFactory;
    protected $connection = 'student_service';
    protected $fillable = [
        'admission_table_id',
        'admission_no',
        'instalment_amount',
        'instalments_no',
        'reference_no',
        'paid_date',
        'due_date',
        'status',

    ];   


}
