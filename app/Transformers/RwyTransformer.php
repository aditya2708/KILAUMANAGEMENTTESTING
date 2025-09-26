<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\Kolektors;
use App\Models\Kinerja;
use App\Models\Donatur;
use Auth;
use League\Fractal\TransformerAbstract;

class RwyTransformer extends TransformerAbstract
{
    public function transform(Donatur $user){
        return [
            'id' => $user->id,
            'id_kantor' => $user->id_kantor,
            'id_transaksi' => $user->id_transaksi,
            'tanggal' => $user->tanggal,
            'nama' => $user->nama,
            'status' => $user->status,
            'jalur' => $user->jalur,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'alamat' => $user->alamat,
            'tgl_kolek' => $user->tgl_kolek,
            'pembayaran' => $user->pembayaran,
            'program' => $user->program,
            'no_hp' => $user->no_hp,
            'setoran' => $user->setoran,
            'kota' => $user->kota,
            'bukti' => $user->bukti,
            'acc' => $user->acc,
            'dikolek' => $user->dikolek,
            'gambar_donatur' => $user->gambar_donatur,
            'registered' => $user->created_at->format('d M Y'),
            'updated' => $user->updated_at->format('d M Y'),
            'date' => $user->created_at->format('Y-m-d'),
            'up' => $user->updated_at,
            'approval' => $user->approval,
            
        ];
    }

}