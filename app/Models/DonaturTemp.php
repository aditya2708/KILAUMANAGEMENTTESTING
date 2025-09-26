<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonaturTemp extends Model
{
    protected $table ="donatur_temp";

    protected $fillable = [
     'id','nama','np','petugas','email','jk','ttl','no_hp','alamat','agama','status_nikah','penghasilan','pendidikan','pekerjaan','gambar_donatur','id_sumdan','program','statprog','id_peg',
     'pembayaran','id_jalur','jalur','provinsi','kota','wilayah','status','acc','username','password','latitude','longitude','setoran','total','tgl_kolek','dikolek','bukti','approval','alasan',
     'warning','tempwar','id_koleks','id_so','tgl_fol','ket','id_don','tahun_lahir','jenis_donatur','orng_dihubungi','jabatan','nohap','id_kantor','token','retup','ketup','user_insert','user_update',
     'user_trans','tesum'
 ];
}
