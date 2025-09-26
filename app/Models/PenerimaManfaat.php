<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenerimaManfaat extends Model
{
    protected $table ="penerima_manfaat";
	protected $primaryKey = 'id';
    protected $fillable = [
     'penerima_manfaat','tgl_lahir' , 'nama_pj', 'alamat', 'hp', 'asnaf', 'tgl_reg', 'kantor', 'nik', 'email', 'jenis_pm',	'jk', 'latitude', 'longitude','foto_pm','id_prov','id_kota','kota'
 ];
}