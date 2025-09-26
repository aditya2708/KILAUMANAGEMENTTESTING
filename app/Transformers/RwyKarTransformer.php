<?php

namespace App\Transformers;

use App\Models\Presensi;
use League\Fractal\TransformerAbstract;

class RwyKarTransformer extends TransformerAbstract
{
    public function transform($user){
        // $month = date('m');
        // $year = date('Y');
        $pres = Presensi::selectRaw("id_karyawan, SUM(IF(status = 'Hadir', jumlah, 0)) AS hadir, SUM(IF(status = 'Sakit', jumlah, 0)) AS sakit, SUM(IF(status = 'Bolos', jumlah, 0)) AS bolos,
                                     SUM(IF(status = 'Terlambat', jumlah, 0)) AS terlambat, SUM(IF(status = 'Cuti', jumlah, 0)) AS cuti, SUM(IF(status = 'Perdin', jumlah, 0)) AS perdin")
                                    ->where('id_karyawan', $user->id_karyawan)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->first();
        // $hadir = Presensi::where('id_karyawan', $user->id_karyawan)->where('status', 'Hadir')->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('jumlah');
        // $sakit = Presensi::where('id_karyawan', $user->id_karyawan)->where('status', 'Sakit')->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('jumlah');
        // $bolos = Presensi::where('id_karyawan', $user->id_karyawan)->where('status', 'Bolos')->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('jumlah');
        // $terlambat = Presensi::where('id_karyawan', $user->id_karyawan)->where('status', 'Terlambat')->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('jumlah');
        // $cuti = Presensi::where('id_karyawan', $user->id_karyawan)->where('status', 'Cuti')->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('jumlah');
        // $perdin = Presensi::where('id_karyawan', $user->id_karyawan)->where('status', 'Perdin')->whereMonth('created_at',$month)->whereYear('created_at',$year)->sum('jumlah');
        
        $nm_pas = $user->nm_pasangan != null ? unserialize($user->nm_pasangan) : [];
        $tgl_lahir = $user->tgl_lahir != null ? unserialize($user->tgl_lahir) : [];
        $tgl_nikah = $user->tgl_nikah != null ? unserialize($user->tgl_nikah) : [];
        $anak = $user->nm_anak != null ? unserialize($user->nm_anak) : []; 
        $tgl_anak = $user->tgl_lahir_anak != null ? unserialize($user->tgl_lahir_anak) : [];
        $sts_anak = $user->status_anak != null ? unserialize($user->status_anak) : [];
        
        return [
            'id_karyawan' => $user->id_karyawan,
            'nama' => ucwords($user->nama),
            'id_jabatan' => $user->id,
            'id_spv' => $user->id_spv,
            'plt' => $user->plt,
            'ttl' => $user->ttl,
            'tj_pas' => $user->tj_pas,
            'jab_daerah' => $user->jab_daerah,
            'unit_kerja' => $user->unit_kerja,
            'email' => $user->email,
            'id_kantor' => $user->id_kantor,
            'jabatan' => $user->jabatan,
            'tgl_gaji' => $user->tgl_gaji,
            'status_kerja' => $user->status_kerja,
            'masa_kerja' => $user->masa_kerja,
            'id_gol' => $user->id_gol,
            'no_rek' => $user->no_rek,
            'presensi' => [
                'hadir' => $pres == null ? 0 : $pres->hadir,
                'sakit' => $pres == null ? 0 : $pres->sakit,
                'bolos' => $pres == null ? 0 : $pres->bolos,
                'cuti' => $pres == null ? 0 : $pres->cuti,
                'perdin' => $pres == null ? 0 : $pres->perdin,
                'terlambat' => $pres == null ? 0 : $pres->terlambat,
            ],
            'nm_pasangan' => $nm_pas,
            'tgl_lahir' => $tgl_lahir,
            'tgl_nikah' => $tgl_nikah,
            'nm_anak' => $anak,
            'tgl_anak' => $tgl_anak,
            'status_anak' => $sts_anak,
            'status_nikah' => $user->status_nikah,
            'scan_kk' => $user->scan_kk,
            'no_kk' => $user->no_kk,
            'jml_pas' => $nm_pas != null ? count($nm_pas) : 0,
            'jml_anak' => $anak != null ? count($anak) : 0,
            'tgl_mk' => $user->tgl_mk,
            'tgl_gol' => $user->tgl_gol,
            'file_sk' => $user->file_sk,
            'war_naik' => $user->war_naik,
            'kondisi' => $user->kondisi,
            'id_com' => $user->id_com,
        ];
    }

}