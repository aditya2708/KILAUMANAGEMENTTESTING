<?php

namespace App\Transformers;


use League\Fractal\TransformerAbstract;

class GolonganTransformer extends TransformerAbstract
{
    public function transform($user){
        return [
            'id_gol' => $user->id_gol,
            'golongan' => $user->golongan,
        ];
    }

}