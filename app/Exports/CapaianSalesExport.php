<?php

namespace App\Exports;

use Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Tunjangan;
use App\Models\Jabatan;
use App\Models\Kantor;
use App\Models\LapFol;
use Carbon\Carbon;
use DB;
class CapaianSalesExport implements FromView
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     
    public function __construct($request)
    {
        $this->request = $request;
        return $this;
    }

    public function view(): View
    {
        $request = $this->request;
        $tj = Tunjangan::first();
            
        $jabatan = Jabatan::whereRaw("(id = '$tj->sokotak' OR id = '$tj->so' OR id = '$tj->kolektor')")->get();
        $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
        $sampai = $request->sampaii == '' ? $dari : $request->sampaii;
        $unit = $request->unit == '' ? "id_kantor IS NOT NULL" : "id_kantor =  $request->unit";
        $bln = $request->bln == '' ? Carbon::now()->format('m-Y') : $request->bln;
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        
        $jabat = $request->jabat != '' ? "users.id_jabatan = '$request->jabat'" : "(users.id_jabatan = '$tj->sokotak' OR users.id_jabatan = '$tj->so' OR users.id_jabatan = '$tj->kolektor')";
        
        $rkot = $request->unit;
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        // dd($k);
        $kunit = $k != null ? $k->id : 'asdfghqwerty';
        $kota = Auth::user()->id_kantor;
        $lev = Auth::user()->level;
        
        if(Auth::user()->level == ('admin') || Auth::user()->level == ('keuangan pusat') ){
            if($request->plhtgl == 0){
                $data = LapFol::join('users', 'users.id','=','lap_folup.id_peg')
                        ->select(\DB::raw("users.name, lap_folup.id_peg, users.id_jabatan,
                        COUNT(DISTINCT IF( DATE(lap_folup.created_at) >= '$dari' AND DATE(lap_folup.created_at) <= '$sampai' AND lap_folup.ket = 'cancel', lap_folup.id_don, NULL)) AS cancel,
                        COUNT(DISTINCT IF( DATE(lap_folup.created_at) >= '$dari' AND DATE(lap_folup.created_at) <= '$sampai' AND lap_folup.ket = 'open', lap_folup.id_don, NULL)) AS open,
                        COUNT(DISTINCT IF( DATE(lap_folup.created_at) >= '$dari' AND DATE(lap_folup.created_at) <= '$sampai' AND lap_folup.ket = 'closing', lap_folup.id_don, NULL)) AS closing"))
                        ->whereRaw("$jabat AND users.aktif = '1' AND $unit")
                        ->groupBy('users.name','lap_folup.id_peg');
            }else{
                $data = LapFol::join('users', 'users.id','=','lap_folup.id_peg')
                        ->select(\DB::raw("users.name, lap_folup.id_peg, users.id_jabatan,
                        COUNT(DISTINCT IF( MONTH(lap_folup.created_at) = '$bulan' AND YEAR(lap_folup.created_at) = '$tahun' AND lap_folup.ket = 'cancel', lap_folup.id_don, NULL)) AS cancel,
                        COUNT(DISTINCT IF( MONTH(lap_folup.created_at) = '$bulan' AND YEAR(lap_folup.created_at) = '$tahun' AND lap_folup.ket = 'open', lap_folup.id_don, NULL)) AS open,
                        COUNT(DISTINCT IF( MONTH(lap_folup.created_at) = '$bulan' AND YEAR(lap_folup.created_at) = '$tahun' AND lap_folup.ket = 'closing', lap_folup.id_don, NULL)) AS closing"))
                        ->whereRaw("$jabat AND users.aktif = '1' AND $unit")
                        ->groupBy('users.name','lap_folup.id_peg');
            }
        }else if(Auth::user()->level == ('kacab') || Auth::user()->level == ('keuangan cabang') || Auth::user()->level == ('agen')){
            if($request->plhtgl == 0){
                $data = LapFol::join('users', 'users.id','=','lap_folup.id_peg')
                        ->join('donatur','donatur.id','=','lap_folup.id_don')
                        ->select(\DB::raw("lap_folup.id_peg, users.name, users.id_jabatan,
                        COUNT(DISTINCT IF( DATE(lap_folup.created_at) >= '$dari' AND DATE(lap_folup.created_at) <= '$sampai' AND lap_folup.ket = 'cancel', lap_folup.id_don, NULL)) AS cancel,
                        COUNT(DISTINCT IF( DATE(lap_folup.created_at) >= '$dari' AND DATE(lap_folup.created_at) <= '$sampai' AND lap_folup.ket = 'open', lap_folup.id_don, NULL)) AS open,
                        COUNT(DISTINCT IF( DATE(lap_folup.created_at) >= '$dari' AND DATE(lap_folup.created_at) <= '$sampai' AND lap_folup.ket = 'closing', lap_folup.id_don, NULL)) AS closing"))
                        ->where(function($query) use ($kunit, $kota, $rkot, $lev) {
                            if($lev == 'kacab'){
                                if($rkot == ""){
                                    $query->where('donatur.id_kantor', $kota)->orWhere('donatur.id_kantor', $kunit);
                                }else{
                                    $query->where('donatur.id_kantor', $rkot);
                                }
                            }else{
                                $query->where('donatur.id_kantor', $kota);
                            }
                        })
                        ->whereRaw("$jabat AND users.aktif = '1'")
                        ->groupBy('users.name','lap_folup.id_peg');
            }else{
                $data = LapFol::join('users', 'users.id','=','lap_folup.id_peg')
                        ->join('donatur','donatur.id','=','lap_folup.id_don')
                        ->select(\DB::raw("lap_folup.id_peg, users.name, users.id_jabatan,
                        COUNT(DISTINCT IF( MONTH(lap_folup.created_at) = '$bulan' AND YEAR(lap_folup.created_at) = '$tahun' AND lap_folup.ket = 'cancel', lap_folup.id_don, NULL)) AS cancel,
                        COUNT(DISTINCT IF( MONTH(lap_folup.created_at) = '$bulan' AND YEAR(lap_folup.created_at) = '$tahun' AND lap_folup.ket = 'open', lap_folup.id_don, NULL)) AS open,
                        COUNT(DISTINCT IF( MONTH(lap_folup.created_at) = '$bulan' AND YEAR(lap_folup.created_at) = '$tahun' AND lap_folup.ket = 'closing', lap_folup.id_don, NULL)) AS closing"))
                        ->where(function($query) use ($kunit, $kota, $rkot, $lev) {
                            if($lev == 'kacab'){
                                if($rkot == ""){
                                    $query->where('donatur.id_kantor', $kota)->orWhere('donatur.id_kantor', $kunit);
                                }else{
                                    $query->where('donatur.id_kantor', $rkot);
                                }
                            }else{
                                $query->where('donatur.id_kantor', $kota);
                            }
                        })
                        ->whereRaw("$jabat AND users.aktif = '1'")
                        ->groupBy('users.name','lap_folup.id_peg');
            }
        }
        $datas = $data->get();
        $btn = [];
        foreach($datas as $item){
              $y = Jabatan::where('id', $item->id_jabatan)->first();
                if($y == null){
                    $btn[] = [''];
                }else{
                    $btn[] = [
                        'jabatan' => $y->jabatan
                        ];
                }
        }
        
        return view('ekspor.capaiansalesexport',[
            'data' => $datas,
            'jabatan' => $btn,
            'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()
        ]);

    }    
    
}
