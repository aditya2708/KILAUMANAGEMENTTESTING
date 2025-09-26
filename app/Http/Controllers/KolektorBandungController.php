<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use\App\Kolektors;
use\App\Kinerja;
use Str;


class KolektorBandungController extends Controller
{
    public function index()
    {
        $kolektorbdg = Kolektors::orderBy('created_at','asc')->get();
        return view ('kolekbandung.index',compact('kolektorbdg'));
    }

    public function create(){
        return view ('kolekbandung.create');
}

public function store(Request $request){
    Kolektors::create([
        'name' => $request->name,
        'level'  =>$request->level = 'kolektor',
        'email' =>$request->email,
        'kota' =>$request->kota,
        'qty' =>$request->qty,
        'target' =>$request->target,
        'password'=>bcrypt($request->password),
        'api_token'=>  Str::random(60),

    ]);
    return redirect('kolekbandung');

}

public function kinerjabandung()
{
    $kerja = Kinerja::get();
    return view ('kolekbandung.kinerja',compact('kerja'));
}

public function destroy($id)
{
    $kolektorbdg = Kolektors::findOrFail($id);
    $kolektorbdg->delete();
    return redirect('kolekbandung');
}
}
