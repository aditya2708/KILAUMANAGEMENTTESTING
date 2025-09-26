<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\Program;
use Auth;
use League\Fractal\TransformerAbstract;

class ProgramTransformer extends TransformerAbstract
{
    public function transform(Program $user){
        return [
            'id' => $user->id,
            'program' => $user->program,
            'subprogram' => $user->subprogram,
            'keterangan' => $user->keterangan,
            'prioritas' => $user->prioritas,
        ];
    }

}