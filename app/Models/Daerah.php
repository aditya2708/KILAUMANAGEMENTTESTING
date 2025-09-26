<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Daerah extends Model
{
    protected $table ="daerah";
    public $timestamps = false;
	protected $primaryKey = 'id_daerah';
    protected $fillable = [
     'id_provinsi', 'id_kota','kota', 'tj_daerah', 'tj_jab_daerah', 'umk','id_com'
 ];
}