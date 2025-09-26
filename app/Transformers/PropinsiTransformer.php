<?php

namespace App\Transformers;


use League\Fractal\TransformerAbstract;

class PropinsiTransformer extends TransformerAbstract
{
    public function transform($prop){
        return [
            'id' => $prop->province_id,
            'name' => $prop->name,
            // 'id' => $kota->name
        ];
    }

}