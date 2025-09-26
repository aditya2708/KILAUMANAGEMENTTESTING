<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenAnak extends Model
{
    protected $table ="kilauindonesia_pendidikan.anak";
	protected $primaryKey = 'id_anak';
    protected $fillable = [
     'id_anak','id_keluarga','id_anak_pend','id_kelompok','id_shelter','id_donatur','id_level_anak_binaan','anak_ke','dari_bersaudara','nick_name','full_name','agama','tempat_lahir','tanggal_lahir','jenis_kelamin','tinggal_bersama','status_validasi','status_cpb','jenis_anak_binaan','hafalan','pelajaran_favorit','hobi','prestasi','jarak_rumah','transportasi','foto','status'
 ];
}