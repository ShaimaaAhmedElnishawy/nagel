<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Doctor extends Model
{
    use HasApiTokens ,HasFactory;
    
    protected $guarded=[];

    public function clinic(){
        return $this->hasMany(Clinic::class);
    }

    public function schuduall(){
        return $this->hasMany(Scheduall::class);
    }
}
