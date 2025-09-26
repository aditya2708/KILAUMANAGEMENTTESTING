<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SumberDana extends Model
{
    public $timestamps = false;
    protected $table ="sumber_dana";
    protected $primaryKey ="id_sumber_dana";

    protected $fillable = [
     'sumber_dana', 'active', 'dtu', 'id_program_temp'
 ];
}