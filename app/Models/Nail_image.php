<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nail_image extends Model
{
    use HasFactory;

    public $guarded=[];

    public function Patient(){

        return $this->belongsTo(Patient::class);
    }

}
