<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\UserKolek;
use League\Fractal\TransformerAbstract;

class UserKarTransformer extends TransformerAbstract
{
    public function transform($user){
        return [
            'id' => $user->id,
            'id_karyawan' => $user->id_karyawan,
            'name' => $user->name,
            'aktif' => $user->aktif,
            'email' => $user->email,
            'jabatan' => $user->jabatan,
            'id_jabatan' => $user->id_jabatan,
            'pr_jabatan' => $user->pr_jabatan,
            'id_kantor' => $user->id_kantor,
            'kantor_induk' => $user->kantor_induk,
            'presensi' => $user->presensi,
            'jenis' => $user->jenis,
            'level' => $user->level,
            'pengaturan' => $user->pengaturan,
            'kepegawaian' => $user->kepegawaian,
            'keuangan' => $user->keuangan,
            'kolekting' => $user->kolekting,
            'kolektor' => $user->kolektor,
            'presensi' => $user->presensi,
            'minimal' => $user->minimal,
            'jenis' => $user->jenis,
            'kunjungan' => $user->kunjungan,
            'qty' => $user->qty,
            'omset' => $user->omset,
            'target' => $user->target,
            'kota' => $user->kota,
            'honor' => $user->honor,
            'bonus' => $user->bonus,
            'tema' => $user->tema,
            'color' => $user->color,
            'shift' => $user->shift,
            'up_shift' => $user->up_shift,
            'tim' => $user->tim,
            'registered' => $user->created_at->diffForHumans(),
            'cam' => $user->cam,
            'gaji_com' => $user->gaji,
            'id_com' => $user->id_com,
            'level_hc' => $user->level_hc,
            'injenreq' => $user->injenreq,
            'injamker' => $user->injamker,
            'gambar_identitas' => $user->gambar_identitas,
            'alamat' => $user->alamat,
            'nomorhp' => $user->nomerhp,
            'theme' => $user->theme,
            'detail' => $user,
        ];
    }

}