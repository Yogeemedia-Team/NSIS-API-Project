<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAdmission extends Model
{
    use HasFactory;
    protected $connection = 'student_service';
    protected $fillable = [
        'admission_no',
        'total_amount',
        'no_of_instalments',
        'status',
    ];   
 
    public function admissionInstalments()
    {
        return $this->hasMany(AdmissionInstalment::class, 'admission_table_id','id');
    }

}