<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPromoteHistory extends Model
{
    use HasFactory;
    protected $connection = 'student_service';
    protected $table = 'student_promote_history';
    protected $fillable = [
       'student_id',
       'promote_history_id',
       'monthly_fee',
       'st_monthly_fee_id',
   ];   

}
