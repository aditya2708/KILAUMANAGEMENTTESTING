<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamKerja extends Model
{
    protected $table ="jam_kerja";
	protected $primaryKey = 'id_jamker';
    protected $fillable = [
     'nama_hari', 
     'cek_in', 
     'shift',
     'terlambat',
     'break_out', 
     'break_in', 
     'cek_out', 
     'status',
     'user_update', 
     'id_com'
 ];
}