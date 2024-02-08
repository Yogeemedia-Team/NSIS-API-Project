<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDetail extends Model
{
    use HasFactory;
     protected $connection = 'student_service';

    protected $fillable = [
        'student_id',
        'organization_id',
        'sd_year_grade_class_id',
        'sd_admission_no',
        'sd_first_name',
        'sd_last_name',
        'sd_name_with_initials',
        'sd_name_in_full',
        'sd_address_line1',
        'sd_address_line2',
        'sd_address_city',
        'sd_telephone_residence',
        'sd_telephone_mobile',
        'sd_telephone_whatsapp',
        'sd_email_address',
        'sd_gender',
        'sd_date_of_birth',
        'sd_religion',
        'sd_ethnicity',
        'sd_birth_certificate_number',
        'sd_profile_picture',
        'sd_health_conditions',
        'sd_admission_date',
        'sd_admission_payment_amount',
        'sd_no_of_installments',
        'sd_extra_pay', 
        'sd_payment_id',
        'sd_total_due'
    ];


     public function documents()
    {
        return $this->hasMany(StudentDocument::class, 'student_id', 'student_id');
    }


     public function parent_data()
    {
        return $this->hasMany(StudentParent::class, 'student_id', 'student_id');
    }

     public function sibling_data()
    {
        return $this->hasMany(StudentSibling::class, 'student_id', 'student_id');
    }

     public function year_class_data()
    {
        return $this->belongsTo(YearGradeClass::class, 'sd_year_grade_class_id', 'id');
    }


        public function grade()
    {
        return $this->belongsTo(MasterGrade::class, 'id');
    }

    public function class()
    {
        return $this->belongsTo(MasterClass::class, 'id');
    }


    public function yearGradeClass()
    {
        return $this->belongsTo(YearGradeClass::class, 'sd_year_grade_class_id', 'id');
    }

    public function studentDetail()
    {
        return $this->hasMany(AccountPayable::class, 'student_id', 'student_id');
    }
    
    public function StudentPayment()
    {
        return $this->hasMany(StudentPayment::class, 'admission_no', 'sd_admission_no');
    }
}
