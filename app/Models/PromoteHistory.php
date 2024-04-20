<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoteHistory extends Model
{
    use HasFactory;
    protected $connection = 'student_service';
    protected $table = 'promote_history';
    protected $fillable = [
       'prev_sd_year_grade_class_id',
       'promoted_sd_year_grade_class_id',
       'promoted_start_from',
       'promoted_end_from',
   ];   

}
