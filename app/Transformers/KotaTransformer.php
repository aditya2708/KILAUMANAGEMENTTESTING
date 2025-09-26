<?php

namespace App\Transformers;


use League\Fractal\TransformerAbstract;

class KotaTransformer extends TransformerAbstract
{
    public function transform($kota){
        return [
            'city_id' => $kota->city_id,
            'province_id' => $kota->province_id,
            'name' => $kota->name,
            'province' => $kota->province,
            'id' => $kota->name
        ];
    }

}