<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearGradeClass extends Model
{
    use HasFactory;
    protected $connection = 'core_service';
    protected $fillable = [
        'organization_id',
        'title',
        'year',
        'master_grade_id',
        'master_class_id',
        'monthly_fee',
        'active_status',
        'Total_number_of_students',
    ];

    public function grade()
    {
        return $this->belongsTo(MasterGrade::class, 'master_grade_id', 'id');
    }

    public function class()
    {
        return $this->belongsTo(MasterClass::class, 'master_class_id', 'id');
    }

    public function studentDetails()
    {
        return $this->hasMany(StudentDetail::class, 'id', 'sd_year_grade_class_id');
    }
}
