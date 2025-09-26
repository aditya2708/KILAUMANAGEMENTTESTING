<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\Kolektors;
use App\Models\Kinerja;
use App\Models\Donatur;
use App\Models\Prog;
use Auth;
use League\Fractal\TransformerAbstract;

class LapFolTransformer extends TransformerAbstract
{
    public function transform($user){
            $program = $user->program != null ? unserialize($user->program) : [];
            $sumdan = $user->id_sumdan != null ? unserialize($user->id_sumdan) : [];
            $ket = $user->ket != null ? unserialize($user->ket) : [];
            
            if($program == []){
                $nama_prog = [];
            }else{
            foreach($program as $key => $val){
                $y = Prog::where('id_program', $val)->first();
                if($y != null){
                $nama_prog[] = $y->program;
                // $sokol[] = $y->sokol;
                // $program[] = $y->id_program;
                // $sumdan[] = $y->id_sumber_dana;
                }
            }
            }
        return [
            'id' => $user->id,
            'id_karyawan' => $user->id_karyawan,
            'id_don' => $user->id_don,
            'tgl_fol' => $user->tgl_fol,
            'name' => $user->name,
            'nama' => $user->nama,
            'pembayaran' => $user->pembayaran,
            'jalur' => $user->jalur,
            'program' => $program,
            'nama_program' => $nama_prog,
            'deskripsi' => $user->deskripsi,
            'feedback' => $user->feedback,
            'id_sumdan' => $sumdan,
            'ket' => $ket,
            'bukti' => $user->bukti,
            'created_at' => date('d-m-Y', strtotime($user->created_at)),
        ];
    }

}