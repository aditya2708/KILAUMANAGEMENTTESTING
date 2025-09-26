<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $table ="provinces";

    protected $fillable = [
     'name'];
    
    protected $primaryKey = 'province_id';
}
