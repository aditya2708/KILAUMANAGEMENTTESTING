<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\UserKolek;
use App\Models\Transaksi;
use League\Fractal\TransformerAbstract;

class KolekTransformer extends TransformerAbstract
{
    public function transform(UserKolek $user){
    $trans = Transaksi::whereMonth('tanggal', date('m'))->whereYear('tanggal', date('Y'))->where('id_koleks',$user->id)->where('approval', '!=', 0)->sum('jumlah');
        return [
            'id_koleks' => $user->id,
            'id_karyawan' => $user->id_karyawan,
            'id_kantor' => $user->id_kantor,
            'name' => $user->name,
            'email' => $user->email,
            'qty' => $user->qty,
            'kolektor' => $user->kolektor,
            'omset' => $trans,
            'target' => $user->target,
            'kota' => $user->kotug,
            'honor' => $user->honor,
            'bonus' => $user->bonus,
            'minimal' => $user->minimal,
            'registered' => $user->created_at->diffForHumans(),
            'aktif' => $user->aktif,
            'cam' => $user->cam,
        ];
    }

}