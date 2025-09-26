<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class JabatanTransformer extends TransformerAbstract
{
    public function transform($user){
        
        return [
            'id_jabatan' => $user->id,
            'jabatan' => $user->jabatan,
        ];
    }

}