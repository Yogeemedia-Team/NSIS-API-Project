<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_type',
        'access_level',
        'access_attributes',

    ];

    public function user(){
        return $this->belongsToMany(User::class);
    }
}
