<?php

namespace App\Transformers;


use League\Fractal\TransformerAbstract;

class GajiNewTransformer extends TransformerAbstract
{
    public function transform($user){
        $potlaptab = $user->potlaptab === 'b:0;' || @unserialize($user->potlaptab) !== false ? unserialize($user->potlaptab) : [];
        $arpotkol = $user->arpotkol === 'b:0;' || @unserialize($user->arpotkol) !== false ? unserialize($user->arpotkol) : [];
        $arrinbon = $user->arrinbon === 'b:0;' || @unserialize($user->arrinbon) !== false ? unserialize($user->arrinbon) : [];
        return [
            'id_gaji' => $user->id_gaji,
            'id_karyawan' => $user->id_karyawan,
            'nama' => $user->nama,
            'masa_kerja' => $user->masa_kerja,
            'golongan' => $user->golongan,
            'id_jabatan' => $user->id_jabatan,
            'jabatan' => $user->jabatan,
            'id_kantor' => $user->id_kantor,
            'gapok' => number_format($user->gapok,0, ',' , '.'),
            'tj_jabatan' => number_format($user->tj_jabatan,0, ',' , '.'),
            'tj_daerah' => number_format($user->tj_daerah,0, ',' , '.'),
            'tj_p_daerah' => number_format($user->tj_p_daerah,0, ',' , '.'),
            'tj_anak' => number_format($user->tj_anak,0, ',' , '.'),
            'tj_pasangan' => number_format($user->tj_pasangan,0, ',' , '.'),
            'tj_beras' => number_format($user->tj_beras,0, ',' , '.'),
            'transport' => number_format($user->transport,0, ',' , '.'),
            'jml_hari' => $user->jml_hari,
            'total' => number_format($user->total,0, ',' , '.'),
            'tgl_gaji' => $user->created_at->format('Y-m-d'),
            'thp' => number_format($user->thp,0, ',' , '.'),
            'bpjs' => round($user->bpjs),
            'potab' => round($user->potab),
            'potongan' => round($user->potongan),
            'tot_tj_bpjs' => number_format($user->ketenagakerjaan,0, ',' , '.'),
            'bpjs_sehat' => number_format($user->kesehatan,0, ',' , '.'),
            'bonus' => round($user->bonus),
            'bokin' => round($user->bokin),
            'potdis' => round($user->potdis),
            'potdll' => round($user->potdll),
            'no_rek' => $user->no_rek,
            'nik' => $user->nik,
            'status_kerja' => $user->status_kerja,
            'status' => $user->status,
            'created' => $user->created,
            'potlaptab' => $potlaptab,
            'arpotkol' => $arpotkol,
            'arrinbon' => $arrinbon,
        ];
    }

}