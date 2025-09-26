<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bpjs extends Model
{
    protected $table ="bpjs";
	protected $primaryKey = 'id_bpjs';
    protected $fillable = [
     'nama_jenis', 'perusahaan', 'karyawan'
 ];
}