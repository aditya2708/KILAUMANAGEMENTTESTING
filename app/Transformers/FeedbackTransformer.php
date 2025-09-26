<?php

namespace App\Transformers;

use App\Models\Feedback;
use Auth;
use League\Fractal\TransformerAbstract;

class FeedbackTransformer extends TransformerAbstract
{
    public function transform(Feedback $user){
        return [
            'id_feedback' => $user->id_feedback,
            'id_karyawan' => $user->id_karyawan,
            'id_laporan' => $user->id_laporan,
            'id_jabatan' => $user->id_jabatan,
            'pr_jabatan' => $user->pr_jabatan,
            'id_kantor' => $user->id_kantor,
            'kantor_induk' => $user->kantor_induk,
            'nama_atasan' => ucwords($user->nama_atasan),
            'feedback' => $user->feedback,
            'jam' => $user->created_at->format('H:i'),
            'sec_vn' => $user->sec_vn,
            'vn' => $user->vn,
            'baca' => $user->baca,
            
        ];
    }

}