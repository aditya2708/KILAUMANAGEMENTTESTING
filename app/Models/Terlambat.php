<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Terlambat extends Model
{
    protected $table ="set_terlambat";
    protected $primaryKey = 'id_terlambat';
    protected $fillable = [
     'awal', 'akhir', 'potongan','id_com'
 ];
}
