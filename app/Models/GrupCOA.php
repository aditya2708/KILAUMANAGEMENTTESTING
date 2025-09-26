<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupCOA extends Model
{
    protected $table ="grup_coa";

    protected $fillable = [
     'name'];
    
    protected $primaryKey = 'id';
}
