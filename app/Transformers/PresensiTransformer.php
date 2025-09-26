<?php

namespace App\Transformers;

use App\Models\Presensi;
use Auth;
use League\Fractal\TransformerAbstract;

class PresensiTransformer extends TransformerAbstract
{
    public function transform(Presensi $user){
        return [
            'id_presensi' => $user->id_presensi,
            'id_karyawan' => $user->id_karyawan,
            'id_jabatan' => $user->id_jabatan,
            'pr_jabatan' => $user->pr_jabatan,
            'id_kantor' => $user->id_kantor,
            'id_request' => $user->id_request,
            'id_req' => $user->id_req,
            'kantor_induk' => $user->kantor_induk,
            'nama' => $user->nama,
            'acc' => $user->acc_shift == 2 ? '0' : $user->acc,
            'acc_out' => $user->acc_out,
            'cek_in' => $user->cek_in,
            'keterlambatan' => $user->keterlambatan,
            'break_out' => $user->break_out,
            'break_in' => $user->break_in,
            'cek_out' => $user->cek_out,
            'status' => $user->status,
            'jumlah' => $user->jumlah,
            'ket' => $user->ket,
            'foto' => $user->foto,
            'foto_out' => $user->foto_out,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'created' => $user->created_at->format('d M Y'),
            'updated' => $user->updated_at->format('d M Y'),
            'date' => $user->created_at->format('Y-m-d'),
            'up' => $user->updated_at,
            
        ];
    }

}