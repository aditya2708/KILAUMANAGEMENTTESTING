<?php

namespace App\Transformers;

use App\Models\RequestKar;
use Auth;
use League\Fractal\TransformerAbstract;

class RequestTransformer extends TransformerAbstract
{   
    public function transform(RequestKar $user){
        return [
            'id_request' => $user->id_request,
            'id_status' => $user->id_status,
            'id_presensi' => $user->id_presensi,
            'id_karyawan' => $user->id_karyawan,
            'id_jabatan' => $user->id_jabatan,
            'pr_jabatan' => $user->pr_jabatan,
            'jabatan' => $user->jabatan,
            'nomerhp' => $user->nomerhp,
            'id_kantor' => $user->id_kantor,
            'kantor_induk' => $user->kantor_induk,
            'nama' => $user->nama,
            'status' => $user->status,
            'jumlah' => $user->jumlah,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'foto' => $user->foto,
            'ket' => $user->ket,
            'acc' => $user->acc,
            'lampiran' => $user->lampiran,
            'created' => $user->created_at->format('d M Y'),
            'updated' => $user->updated_at->format('d M Y'),
            'date' => $user->created_at->format('Y-m-d'),
            'up' => $user->updated_at,
            'tgl' => $user->tg_mulai,
            'tg_mulai' => date('d-m-Y', strtotime($user->tg_mulai)),
            'tg_akhir' => date('d-m-Y', strtotime($user->tg_akhir)),
            'alasan' => $user->alasan,
            'shift' => $user->shift,
            'id_shift' => $user->id_shift,
            'kategori' => $user->kategori,
            'jamco' => $user->jamco,
            'jamci' => $user->jamci,
            'bukti_from_admin' => $user->bukti_from_admin,
            'detail' => $user
            
        ];
    }

}