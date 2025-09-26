<?php

namespace App\Transformers;

use\App\User;
use\App\Kolektors;
use\App\Kinerja;
use\App\Donatur;
use\App\Prog;
use Auth;
use League\Fractal\TransformerAbstract;

class AssisoTransformer extends TransformerAbstract
{
    public function transform($user){
            // $program = empty($user->program[0]) ? unserialize($user->program) : [];
            // $sumdan = empty($user->id_sumdan[0]) ? unserialize($user->id_sumdan) : [];
            $prog = $user->program === 'b:0;' || @unserialize($user->program) !== false ? unserialize($user->program) : [];  
            $statprog = $user->statprog === 'b:0;' || @unserialize($user->statprog) !== false ? unserialize($user->statprog) : [];  
            // $sumdan = $user->id_sumdan != null ? unserialize($user->id_sumdan) : [];
            
            if($prog == []){
                $nama_prog = [];
                $program = [];
                $sumdan = [];
                $kotak = [];
            }else{
            foreach($prog as $key => $val){
                $y = Prog::where('id_program', $val)->first();
                // dd($y[]);
                if($y != null){
                $nama_prog[] = $y->program;
                $sokol[] = $y->sokol;
                $program[] = $y->id_program;
                $sumdan[] = $y->id_sumber_dana;
                $kotak[] = $y->kotak;
                }
            }
            }
            
            // $kantor = ['id_kantor' => $user->id_kantor, 'unit' => $user->unit];
        return [
            'id' => $user->id,
            'id_laphub' => $user->id_laphub,
            'id_kantor' => $user->id_kantor,
            // 'kantor' => ['id_kantor' => $user->id_kantor, 'unit' => $user->unit],
            'id_transaksi' => $user->id_transaksi,
            'tanggal' => $user->tanggal,
            'petugas' => $user->petugas,
            'nama' => $user->nama,
            'email' => $user->email,
            'deskripsi' => $user->deskripsi,
            'unit' => $user->unit,
            'np' => $user->np,
            'upno' => $user->no_hp == null ? 1 : 0,
            'uplok' => $user->latitude == null | $user->longitude == null ? 1 : 0,
            'upgam' => $user->gambar_donatur == null ? 1 : 0,
            'status' => $user->status,
            'jalur' => $user->jalur,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'alamat' => $user->alamat,
            'tgl_kolek' => $user->tgl_kolek,
            'pembayaran' => $user->pembayaran,
            'program' => $program,
            'statprog' => $statprog,
            'nama_program' => $nama_prog,
            // 'sokol' => $sokol,
            'id_sumdan' => $sumdan,
            'kotak' => $kotak,
            'feedback' => $user->feedback,
            'retup' => $user->retup,
            'ketup' => $user->ketup,
            'no_hp' => $user->no_hp,
            'nohap' => $user->nohap,
            'orng_dihubungi' => $user->orng_dihubungi,
            'jabatan' => $user->jabatan,
            'setoran' => $user->setoran,
            'provinsi' => $user->provinsi,
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
            'jk' => $user->jk,
            'tahun_lahir' => $user->tahun_lahir,
            'pekerjaan' => $user->pekerjaan,
            'tgl_fol' => $user->tgl_fol,
            'id_koleks' => $user->id_koleks,
        ];
    }

}