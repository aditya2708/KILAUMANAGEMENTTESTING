<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donatur;

class DonaturApiController extends Controller
{
    public function index()
    {
        return Donatur::orderBy('created_at','desc')->get();
    }
    
    public function cek_donatur(Request $req){
        $nik = $req->nik == '' ? 'kosong' : $req->nik;
        $hp = $req->hp == '' ? 'kosong' : $req->hp;
        $email = $req->email == '' ? 'kosong' : $req->email;
        
        $donatur = Donatur::whereRaw("nik LIKE '%$nik%' OR email LIKE '%$email%' OR no_hp LIKE '%$hp%'")->first();

        // Respon jika donatur ditemukan atau tidak
        if ($donatur) {
            return response()->json([
                'success' => true,
                'message' => 'Donatur ditemukan.',
                'data' => $donatur,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Donatur tidak ditemukan.',
        ], 404);
    }
}
