<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\PenUser;
use League\Fractal\TransformerAbstract;

class UserPenTransformer extends TransformerAbstract
{
    public function transform($user){
        return [
            'id' => $user->id_users,
            'name' => $user->username,
            'email' => $user->username,
        ];
    }

}