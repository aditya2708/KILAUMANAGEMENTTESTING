<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestKar extends Model
{
    protected $table ="request";
    protected $primaryKey = 'id_request';
   protected $guarded = [];
    
    // protected $guarded = [];
}
