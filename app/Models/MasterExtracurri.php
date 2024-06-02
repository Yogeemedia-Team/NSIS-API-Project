<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterExtracurri extends Model
{
    use HasFactory;
    protected $connection = 'core_service';
    protected $table = 'master_extracurricular';
    protected $fillable = [
        'organization_id',
        'extracurricular_name',

    ];

}
