<?php

namespace App\Transformers;

use App\Models\FeedbackUser;
use Auth;
use League\Fractal\TransformerAbstract;

class FeedbackUserTransformer extends TransformerAbstract
{
    public function transform(Feedback $user){
        return [
            'id_feed_user' => $user->id_feed_user,
            'id_karyawan' => $user->id_karyawan,
            'nama' => $user->nama,
            'feedback' => $user->feedback,
            
        ];
    }

}