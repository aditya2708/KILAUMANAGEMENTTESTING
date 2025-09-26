<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\Kolektors;
use App\Models\Kinerja;
use App\Models\Donatur;
use App\Models\Prog;
use Auth;
use League\Fractal\TransformerAbstract;

class AssisTransformer extends TransformerAbstract
{
    public function transform($user){
        

        // Unserialize program
        $programs = unserialize($user->program);
        
        // Jika program berisi array ID program
        if (is_array($programs)) {
            // Ambil semua nama program dari tabel 'prog' berdasarkan ID program yang ada
            $namaProgram = Prog::whereIn('id_program', $programs)->pluck('program');
        } else {
            // Jika hanya satu program, ambil nama program langsung
            $namaProgram = Prog::where('id_program', $programs)->pluck('program')->first();
        }
        return [
            'id' => $user->id,
            'id_temp' => $user->id_temp,
            'id_transaksi' => $user->id_transaksi,
            'tanggal' => $user->tanggal,
            'petugas' => $user->petugas,
            'nama' => $user->nama,
            'email' => $user->email,
            'np' => $user->np,
            'status' => $user->status,
            'jalur' => $user->jalur,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'alamat' => $user->alamat,
            'tgl_kolek' => $user->tgl_kolek,
            'pembayaran' => $user->pembayaran,
            'program' => unserialize($user->program),
            'nama_program' => $namaProgram,
            'no_hp' => $user->no_hp,
            'nohap' => $user->nohap,
            'orng_dihubungi' => $user->orng_dihubungi,
            'jabatan' => $user->jabatan,
            'setoran' => $user->setoran,
            'kota' => $user->kota,
            'bukti' => $user->bukti,
            'acc' => $user->acc,
            'dikolek' => $user->dikolek,
            'gambar_donatur' => $user->gambar_donatur,
            'registered' => $user->created_at->format('d M Y'),
            'updated' => $user->updated_at->format('d M Y'),
            'date' => $user->created_at->format('Y-m-d'),
            'warning' => $user->warning,
            'ket' => $user->ket,
            'jenis_donatur' => $user->jenis_donatur,
            'gambar' => $user->gambar_donatur,
            'tgl_fol' => $user->tgl_fol,
            'id_sumdan' => unserialize($user->id_sumdan),
        ];
    }

}