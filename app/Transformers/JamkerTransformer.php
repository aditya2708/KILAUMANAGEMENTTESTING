<?php

namespace App\Transformers;

use App\Models\JamKerja;
use Auth;
use League\Fractal\TransformerAbstract;

class JamkerTransformer extends TransformerAbstract
{
    public function transform(JamKerja $user){
        $day = date('D');
        $dayList = array(
            'Sun' => 'Minggu',
            'Mon' => 'Senin',
            'Tue' => 'Selasa',
            'Wed' => 'Rabu',
            'Thu' => 'Kamis',
            'Fri' => 'Jumat',
            'Sat' => 'Sabtu'
        );
        $dayList2 = array(
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        );
        return [
            'id_jamker' => $user->id_jamker,
            'nama_hari' => $dayList2[$user->nama_hari],
            'cek_in' => $user->cek_in,
            'terlambat' => $user->terlambat,
            'break_out' => $user->break_out,
            'break_in' => $user->break_in,
            'cek_out' => $user->cek_out,
            'status' => $user->status,
            'hari' => $dayList[$day]
        ];
    }

}
