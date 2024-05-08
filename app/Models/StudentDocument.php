<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    use HasFactory;
     protected $connection = 'student_service';
       protected $fillable = [
        'organization_id',
        'student_id',
        'sd_profile_picture',
        'sd_birth_certificate',
        'sd_nic_father',
        'sd_nic_mother',
        'sd_marriage_certificate',
        'sd_permission_letter',
        'sd_leaving_certificate',
    ];
}
