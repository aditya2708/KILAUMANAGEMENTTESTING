<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donatur extends Model
{
    protected $table ="donatur";
	protected $primaryKey = 'id';
    protected $guarded = [];

//     protected $fillable = [
//      'nama','status','bukti','pembayaran', 'petugas','acc','total','setoran','tgl_kolek','dikolek','no_hp', 'alamat', 
//      'program','id_jalur', 'jalur', 'longitude', 'latitude','provinsi' ,'kota', 'email' ,'wilayah', 'username', 'password','np','jk','ttl',
//      'agama','status_nikah','penghasilan','pendidikan','pekerjaan','gambar_donatur','warning','tgl_fol','ket', 'tahun_lahir', 'jenis_donatur', 'orng_dihubungi', 'jabatan', 'nohap', 'id_kantor', 'id_sumdan',
//      'user_insert', 'user_update','statprog', 'token', 'retup' ,'ketup','user_trans', 'id_peg'
//  ];
}
