<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\Prog;
use Auth;
use League\Fractal\TransformerAbstract;

class ProgTransformer extends TransformerAbstract
{
    public function transform($user){
        return [
            'id_program' => $user->id_program,
            'program' => $user->program,
            'id_program_parent' => $user->id_program_parent,
            'id_sumber_dana' => $user->id_sumber_dana,
            'parent' => $user->parent,
            'level' => $user->level,
            'harga_umum' => $user->harga_umum,
            'spc' => $user->spc,
            'jp' => $user->jp,
            'coa_individu' => $user->coa_individu,
            'coa_entitas' => $user->coa_entitas,
            'ket_prog' => $user->ket,
            'duplikat' => $user->duplikat,
            'kotak' => $user->kotak,
            'id_catcamp' => $user->id_catcamp,
        ];
    }

}