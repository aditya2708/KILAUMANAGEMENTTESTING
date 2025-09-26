<?php

namespace App\Transformers;

use App\Models\Transaksi;
use Auth;
use League\Fractal\TransformerAbstract;

class TransaksiTransformer extends TransformerAbstract
{
    public function transform(Transaksi $user){
        return [
            'id' => $user->id,
            'id_donatur' => $user->id_donatur,
            'id_koleks' => $user->id_koleks,
            'id_transaksi' => $user->id_transaksi,
            'pembayaran' => $user->pembayaran,
            'jumlah' => $user->jumlah,
            'status' => $user->status,
            'subprogram' => $user->subprogram,
            'keterangan' => $user->keterangan,
            'approval' => $user->approval,
            'alasan' => $user->alasan,
            'registered' => $user->created_at->format('d M Y'),
            'create' => $user->created_at->format('d/m/Y'),
            'bulan' => $user->created_at->format('m/Y'),
        ];
    }

}