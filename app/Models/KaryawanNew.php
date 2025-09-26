<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class KaryawanNew extends Authenticatable
{
    protected $table ="new_karyawan";
    // protected $guard = "karyawan";
    protected $guarded = [];

    // protected $fillable = [
    //  'nama', 'email', 'nomerhp', 'ttl', 'nik', 'hobi', 'alamat', 'pendidikan', 'nm_sekolah', 'th_lulus', 'ijazah', 'jurusan', 'gelar', 
    //  'status_nikah', 'no_kk', 'scan_kk', 'id_gol','masa_kerja', 'golongan','id_pasangan', 'nm_pasangan', 'tgl_lahir', 'tgl_nikah','nm_anak', 'tgl_lahir_anak', 'status_anak','nm_anak1', 'nm_anak2', 'nm_anak3', 'nm_anak4',
    //  'tgl_kerja', 'unit_kerja', 'jabatan', 'durasi_kerja', 'k_nama1', 'k_jabatan1', 'k_durasi1', 'k_nama2', 'k_jabatan2', 'k_durasi2',
    //  'o_nama1', 'o_jabatan1', 'o_nama2', 'o_jabatan2', 'p_nama1', 'p_durasi1', 'p_nama2', 'p_durasi2', 'kecocokan', 'seharusnya', 'alasan',
    //  'jk', 'gambar_identitas','id_karyawan','password','pr_jabatan','id_kantor','kantor_induk','tgl_gaji','id_daerah','tgl_mk', 'tgl_mutasi','tgl_gol', 'status_kerja', 'file_sk', 'no_rek', 'tj_pas', 'jab_daerah', 'plt', 'warning_pasangan',
    //  'user_insert', 'user_update', 'id_mentor','jemag','id_pj_agen', 'id_com'
//  ];
 
 public function getRouteKeyName(){
    return 'id_karyawans';
  }

//   protected $hidden = [
//     'password'
// ];

protected $primaryKey = "id_karyawans";

}
