<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\Kolektors;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(Kolektors $user){
        return [
            'id_koleks' => $user->id_koleks,
            'name' => $user->name,
            'email' => $user->email,
            'qty' => $user->qty,
            'omset' => $user->omset,
            'target' => $user->target,
            'kota' => $user->kota,
            'honor' => $user->honor,
            'bonus' => $user->bonus,
            'registered' => $user->created_at->diffForHumans(),
        ];
    }

}