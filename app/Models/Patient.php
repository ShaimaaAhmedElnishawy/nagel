<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Patient extends Model
{
    use HasApiTokens,HasFactory;

    public $timestamps=0;

    protected $fillable=['name','email','phone','password','address','image','DOB','gender'];

    public function Nail_image(){
        return $this-> hasMany(Nail_image::class);
    }

}
