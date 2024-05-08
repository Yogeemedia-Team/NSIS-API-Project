<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentParent extends Model
{
    use HasFactory;
     protected $connection = 'student_service';
     protected $fillable = [
        'organization_id',
        'student_id',
        'sp_father_first_name',
        'sp_father_last_name',
        'sp_father_nic',
        'sp_father_higher_education_qualification',
        'sp_father_occupation',
        'sp_father_official_address',
        'sp_father_permanent_address',
        'sp_father_contact_official',
        'sp_father_contact_mobile',
        'sp_mother_first_name',
        'sp_mother_last_name',
        'sp_mother_nic',
        'sp_mother_higher_education_qualification',
        'sp_mother_occupation',
        'sp_mother_official_address',
        'sp_mother_permanent_address',
        'sp_mother_contact_official',
        'sp_mother_contact_mobile',
     ];


}
