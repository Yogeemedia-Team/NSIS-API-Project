<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentExtraCurricular extends Model
{
    use HasFactory;
    use HasFactory;
    protected $connection = 'student_service';
    protected $table = 'student_extra_curricular';
    protected $fillable = [
       'student_id',
       'extra_curricular_id',
       'start_from',
       'end_from',
       'status',
   ];   

   public function ExtraCurriculars()
   {
       return $this->belongsTo(MasterExtracurri::class, 'extra_curricular_id', 'id');
   }
}
