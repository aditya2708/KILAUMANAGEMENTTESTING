<?php

namespace App\Transformers;

use App\Models\Tambahan;
use Auth;
use League\Fractal\TransformerAbstract;

class TbhTransformer extends TransformerAbstract
{
    public function transform(Tambahan $user){
        return [
            'id' => $user->id,
            'unit' => strtolower($user->unit),
            'no_hp' => $user->no_hp,
        ];
    }

}
