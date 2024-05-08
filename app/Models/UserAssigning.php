<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAssigning extends Model
{
    use HasFactory;
     protected $fillable = [
        'user_id',
        'level_id',
        'role_id',
        'activity_ids'

    ];


    public function user_activity(){
        return $this->belongsToMany(User::class);
    }
}
