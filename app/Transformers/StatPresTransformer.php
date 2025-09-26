<?php

namespace App\Transformers;

use Auth;
use League\Fractal\TransformerAbstract;

class StatPresTransformer extends TransformerAbstract
{
    public function transform($user){
        return [
            // 'id_presensi' => $user->id_presensi,
            'id_karyawan' => $user->id_karyawan,
            'nama' => $user->nama,
            'jabatan' => $user->jabatan,
            'id_jabatan' => $user->id_jabatan,
            'pr_jabatan' => $user->pr_jabatan,
            'id_kantor' => $user->id_kantor,
            'kantor_induk' => $user->kantor_induk,
            'status' => $user->status,
            'jumlah' => $user->jumlah,
            
        ];
    }

}