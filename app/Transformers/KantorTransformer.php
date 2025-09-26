<?php

namespace App\Transformers;

use App\Models\Kantor;
use Auth;
use League\Fractal\TransformerAbstract;

class KantorTransformer extends TransformerAbstract
{
    public function transform(Kantor $user){
        return [
            'id' => $user->id,
            'unit' => $user->unit,
            'no_hp' => $user->no_hp,
            'kantor_induk' => $user->kantor_induk,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'acc_up' => $user->acc_up,
        ];
    }

}
