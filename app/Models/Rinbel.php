<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rinbel extends Model
{
    protected $table ="rincian_belum";
	protected $primaryKey = 'id';
    protected $fillable = [
     'id_karyawan', 'name', 'kota', 'totkun', 'totup', 'belkun', 'beltup', 'tanggal'
 ];
}