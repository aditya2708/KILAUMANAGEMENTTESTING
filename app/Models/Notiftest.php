<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notiftest extends Model
{
    protected $table ="notiftest";

    protected $fillable = [
     'id','nama', 'token','created_at','updated_at'
    ];
    
    protected $primaryKey = 'id';
}
