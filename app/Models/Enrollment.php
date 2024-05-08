<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;
    protected $connection = 'student_service';
    protected $fillable = [
        'admission_id',
        'grade_class_id',
        'first_name',
        'last_name',
        'name_with_initials',
        'name_in_full',
        'address_line1',
        'address_line2',
        'address_city',
        'telephone_residence',
        'telephone_mobile',
        'telephone_whatsapp',
        'email_address',
        'sex',
        'date_of_birth',
        'religion',
        'ethnicity',
        'birthcertificate_number',
        'profle_picture_path',
        'health_conditions',
        'applied_date',
        'admission_status',
    ];
}
