<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use\App\Kolektor;

class KolektorController extends Controller
{
   public function index()
   {
       return Kolektor::orderBy('created_at','desc')->get();
   }

   public function store(Request $request)
   {
    $trayek = new \App\Kolektor;
    $trayek->wilayah = $request->wilayah;
    $trayek->kota = $request->kota;
    $trayek->namadonatur = $request->namadonatur;
    $trayek->namakolektor = $request->namakolektor;
    $trayek->terkumpul = $request->terkumpul;
    $trayek->status = $request->status;
    $trayek->save();
       return "Berhasil Tambah Data";
   }

   public function destroy($id)
   {
       $data = Kolektor::findOrFail($id);
       $data->delete();
       return "Hapus Data Sukses";
   }

   public function update($id, Request $request)
   {
       $data = Kolektor::findOrFail($id);
       $input = $request->all();
       $data->update($input);
       return "Edit Data Sukses";
   }

   public function edit($id)
   {
       $data = Kolektor::findOrFail($id);
       return $data;
   }
   



}
