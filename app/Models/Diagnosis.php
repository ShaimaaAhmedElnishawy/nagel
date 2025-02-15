<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasFactory;
    protected $table='diagnosis';

    public function disease(){
        return $this->hasOne(Disease::class);
    }
    
    public function image(){
        return $this->hasOne(Nail_image::class);
    }

    // public function patient(){
    //     return $this->hasOne(Patient::class);
    // }
}

