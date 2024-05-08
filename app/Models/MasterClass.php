<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterClass extends Model
{
    use HasFactory;
    protected $connection = 'core_service';
    protected $fillable = [
        'organization_id',
        'class_name',

    ];


     public function yearGradeClasses()
    {
        return $this->hasMany(YearGradeClass::class, 'id');
    }
}
