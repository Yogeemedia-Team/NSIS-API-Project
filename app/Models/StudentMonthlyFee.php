<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentMonthlyFee extends Model
{
    use HasFactory;
    protected $connection = 'student_service';
    protected $table = 'student_monthly_fee';
    protected $fillable = [
       'sd_year_grade_class_id',
       'student_id',
       'monthly_fee',
       'start_from',
       'end_from',
       'status',
   ];

   public function student()
   {
       return $this->belongsTo(StudentDetail::class);
   }
}

