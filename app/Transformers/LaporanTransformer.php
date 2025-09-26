<?php

namespace App\Transformers;

use App\Models\Laporan;

use League\Fractal\TransformerAbstract;

class LaporanTransformer extends TransformerAbstract
{
    public function transform(Laporan $user){
        return [
            'id_laporan' => $user->id_laporan,
            'id_karyawan' => $user->id_karyawan,
            'id_jabatan' => $user->id_jabatan,
            'id_kantor' => $user->kantor,
            'pr_jabatan' => $user->pr_jabatan,
            'jabatan' => $user->jabatan,
            'kantor_induk' => $user->kantor_induk,
            'nama' => $user->nama,
            'jabatan' => $user->jabatan,
            'nomerhp' => $user->nomerhp,
            'lampiran' => $user->lampiran,
            'vn' => $user->vn,
            'sec_vn' => $user->sec_vn,
            'link_lap' => $user->link_lap,
            'ket' => $user->ket,
            'capaian' => $user->capaian,
            'target' => $user->target,
            'stat_feed' => $user->stat_feed,
            'tgl' => $user->created_at->format('Y-m-d'),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }

}