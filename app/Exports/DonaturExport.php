<?php

namespace App\Exports;

use Auth;
use App\Models\Kantor;
use App\Models\Donatur;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use DB;

class DonaturExport implements FromView, WithChunkReading
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function __construct($request, $txt_judul, $ea)
    {
        
        // $this->tgl = $tgls ;
        // $this->warning = $w == '' ? [] : json_decode($w);
        // $this->status = $s == '' ? [] : json_decode($s);
        // $this->kota = $kotah == '' ? [] : json_decode($kotah);
        // $this->traknon = $traknon;
        // $this->traktif = $traktif;
        // $this->program = $program;
        
        // $this->koordinat = $koordinat;
        
        // $this->jk = $jk;
        // $this->ui = $ui;
        // $this->petugas =$petugas;
        $this->request = $request;
        $this->txt = $txt_judul;
        $this->ea = $ea;
        return $this;
    }
    
    public function view(): View
    {
        // $tgl = $this->tgl;
        
        // $thisw = $this->warning;
        // $thiss = $this->status;
        // $thisk = $this->kota;
        
        // $traktif = $this->traktif;
        // $traknon = $this->traknon;
        // $program = $this->program;
        
        // $jk = $this->jk;
        // $ui = $this->ui;
        // $petugas = $this->petugas;
        $request = $this->request;
        $txt_judul = $this->txt;
        $ea = $this->ea;
        
        if($request->koordinat == ''){
            $koordinat = "latitude != 'dfdfdf' AND longitude != 'fjdhfd'";
        }else if($request->koordinat == '1'){
            $koordinat = "latitude IS NOT NULL AND longitude IS NOT NULL";
        }else if($request->koordinat == '0'){
            $koordinat = "latitude IS NULL AND longitude IS NULL";
        }
        
        if ($request->tgl != '') {
            $tgl = explode(' - ', $request->tgl);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
                
        $now = date('Y-m-d');
        $tgls = $request->tgl != '' ? "DATE(donatur.created_at) >= '$dari' AND DATE(donatur.created_at) <= '$sampai'" : "DATE(donatur.created_at) IS NOT NULL AND DATE(donatur.created_at) IS NOT NULL ";
        
        
        if(Auth::user()->level == 'admin'  || Auth::user()->keuangan == 'keuangan pusat'){
            $w = $request->warning;
            $s = $request->status;
            $kotah = $request->kota;

            $data = Donatur::select('donatur.*','donatur.created_at as suki')->whereRaw("$tgls AND $koordinat")
                    ->where(function($query) use ($request, $w) {
                        if(isset($request->warning)){
                            $kon1 = in_array('aktif', $request->warning) ? "status != 'Ditarik' AND status != 'Off'" : "status IS NOT NULL";
                            $kon2 = in_array('nonaktif', $request->warning) ? "(status = 'Ditarik' OR status = 'Off')" : "status IS NOT NULL";
                            $kon3 = in_array('warning', $request->warning) ? "warning = 1" : "warning != 1";
                            $query->whereRaw("$kon1 AND $kon2 AND $kon3");
                            
                        }
                    })
                    
                    ->where(function($query) use ($request, $s) {
                        if(isset($request->status)){
                            $query->whereIn('status', $s);
                        }
                    })
                    
                    ->where(function($query) use ($request, $kotah) {
                        if(isset($request->kota)){
                            $query->whereIn('donatur.id_kantor', $kotah);
                        }
                    })
                    
                    ->where(function($query) use ($request) {
                        if(isset($request->petugas)){
                            $query->where('petugas', $request->petugas);
                        }
                    })
                    
                    ->where(function($query) use ($request) {
                        if(isset($request->ui)){
                            $query->where('user_insert', $request->ui);
                        }
                    })
                    
                    ->where(function($query) use ($request) {
                        if(isset($request->jk)){
                            if($request->jk == 'unknown'){
                                $query->where('jk', null);
                            }else{
                                $query->where('jk', $request->jk);
                            }
                        }
                    })
                    
                    ->orderBy('donatur.created_at','desc');
            
        }else if(Auth::user()->level == 'kacab'  || Auth::user()->keuangan == 'keuangan cabang'){
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            $id = Auth::user()->id_kantor;
            if(count($thisk) > 0){
                $data = Donatur::whereRaw("$tgl AND $koordinat")
                            ->where(function($query) use ($thisw) {
                                if($this->warning != ''){
                                    $kon1 = in_array('aktif', $thisw) ? "donatur.status != 'Ditarik' AND donatur.status != 'Off'" : "donatur.status IS NOT NULL";
                                    $kon2 = in_array('nonaktif', $thisw) ? "(donatur.status = 'Ditarik' OR donatur.status = 'Off')" : "donatur.status IS NOT NULL";
                                    $kon3 = in_array('warning', $thisw) ? "warning = 1" : "warning != 1";
                                    $query->whereRaw("$kon1 AND $kon2 AND $kon3");
                                    
                                }
                            })
                            
                            ->where(function($query) use ($thiss) {
                                if(count($thiss) > 0){
                                    $query->whereIn('donatur.status', $thiss);
                                }
                            })
                            
                            ->where(function($query) use ($thisk) {
                                if(count($thisk) > 0){
                                    $query->whereIn('donatur.id_kantor', $thisk);
                                }
                            })
                            
                            ->where(function($query) use ($petugas) {
                                if(isset($petugas) && $petugas != ''){
                                    $query->where('donatur.petugas', $petugas);
                                }
                            })
                            
                            ->where(function($query) use ($ui) {
                                if(isset($ui) && $ui != ''){
                                    $query->where('donatur.user_insert', $ui);
                                }
                            })
                            
                            ->where(function($query) use ($jk) {
                                if(isset($jk) && $jk != ''){
                                    if($jk == 'unknown'){
                                        $query->where('donatur.jk', null);
                                    }else{
                                        $query->where('donatur.jk', $jk);
                                    }
                                }
                            })
                            
                            ->orderBy('donatur.created_at','desc');
                
            }else{
                
                if($k == null){
                  $data = Donatur::select('donatur.*','donatur.created_at as suki')->whereRaw("$tgls AND id_kantor = '$kot' AND $koordinat")
                        ->where(function($query) use ($request, $w) {
                            if(isset($request->warning)){
                                $kon1 = in_array('aktif', $request->warning) ? "status != 'Ditarik' AND status != 'Off'" : "status IS NOT NULL";
                                $kon2 = in_array('nonaktif', $request->warning) ? "(status = 'Ditarik' OR status = 'Off')" : "status IS NOT NULL";
                                $kon3 = in_array('warning', $request->warning) ? "warning = 1" : "warning != 1";
                                $query->whereRaw("$kon1 AND $kon2 AND $kon3");
                                
                            }
                        })
                        
                        ->where(function($query) use ($request, $s) {
                            if(isset($request->status)){
                                $query->whereIn('status', $s);
                            }
                        })
                        ->where(function($query) use ($request) {
                            if(isset($request->petugas)){
                                $query->where('petugas', $request->petugas);
                            }
                        })
                        
                        ->where(function($query) use ($request) {
                            if(isset($request->ui)){
                                $query->where('user_insert', $request->ui);
                            }
                        })
                        
                        ->where(function($query) use ($request) {
                            if(isset($request->jk)){
                                if($request->jk == 'unknown'){
                                    $query->where('jk', null);
                                }else{
                                    $query->where('jk', $request->jk);
                                }
                            }
                        })
                            
                            ->orderBy('donatur.created_at','desc');
                }else{
                    
                    $data = Donatur::select('donatur.*','donatur.created_at as suki')->whereRaw("$tgls AND $koordinat")
                        ->where(function($query) use ($request, $w) {
                            if(isset($request->warning)){
                                $kon1 = in_array('aktif', $request->warning) ? "status != 'Ditarik' AND status != 'Off'" : "status IS NOT NULL";
                                $kon2 = in_array('nonaktif', $request->warning) ? "(status = 'Ditarik' OR status = 'Off')" : "status IS NOT NULL";
                                $kon3 = in_array('warning', $request->warning) ? "warning = 1" : "warning != 1";
                                $query->whereRaw("$kon1 AND $kon2 AND $kon3");
                                
                            }
                        })
                        
                        ->where(function($query) use ($request, $s) {
                            if(isset($request->status)){
                                $query->whereIn('status', $s);
                            }
                        })
                        ->where(function($query) use ($request, $kot, $k, $kotah) {
                            if(isset($request->kota)){
                                $query->whereIn('donatur.id_kantor', $kotah);
                            }else{
                                $query->whereRaw("(id_kantor = '$kot' OR id_kantor = '$k->id')");
                            }
                        })
                        
                        ->where(function($query) use ($request) {
                            if(isset($request->petugas)){
                                $query->where('petugas', $request->petugas);
                            }
                        })
                        
                        ->where(function($query) use ($request) {
                            if(isset($request->ui)){
                                $query->where('user_insert', $request->ui);
                            }
                        })
                        
                        ->where(function($query) use ($request) {
                            if(isset($request->jk)){
                                if($request->jk == 'unknown'){
                                    $query->where('jk', null);
                                }else{
                                    $query->where('jk', $request->jk);
                                }
                            }
                        })
                            
                        ->orderBy('donatur.created_at','desc');
                } 
            }
        }
                        
        if(isset($request->program)){
                // $data->where(function($query) use ($request) {
            $data->join('prosp', function($join) use ($request) {
                $join->on('prosp.id_don' ,'=', 'donatur.id')
                        ->select('prosp.id_prog')
                        ->where('prosp.id_prog', $request->program);
            });
        }
            
        if(isset($request->traktif) && $request->traktif != ''){
            if ($request->traktif != '') {
                $tglt = explode(' - ', $request->traktif);
                $darit = date('Y-m-d', strtotime($tglt[0]));
                $sampait = date('Y-m-d', strtotime($tglt[1]));
            }
                
            $tglst = "DATE(transaksi.tanggal) >= '$darit' AND DATE(transaksi.tanggal) <= '$sampait'";
            
            $data->join('transaksi', function($join) use ($request, $tglst) {
                $join->on('transaksi.id_donatur' ,'=', 'donatur.id')
                        ->select('transaksi.tanggal')
                        ->whereRaw("$tglst AND transaksi.status = 'Donasi'");
            });
        }
            
            
        if(isset($request->traknon) && $request->traknon != ''){
            if ($request->traknon != '') {
                $tgln = explode(' - ', $request->traknon);
                $darin = date('Y-m-d', strtotime($tgln[0]));
                $sampain = date('Y-m-d', strtotime($tgln[1]));
            }
            
            $tglsn = "DATE(lee.tanggal) >= '$darin' AND DATE(lee.tanggal) <= '$sampain'";
            
            $data->join('transaksi as lee', function($join) use ($request, $tglsn) {
                $join->on('lee.id_donatur' ,'=', 'donatur.id')
                        ->select('lee.tanggal')
                        ->whereRaw("$tglsn AND lee.jumlah <= 0");
            });
        }
                        
        $aha = $data->get();
        
        // return($aha);
            
        return view('ekspor.donaturekspor',[
            'ahhh' => $ea,
            'data' => $aha,        
            'periode' => $this->txt,
            'kompani' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name
        ]);
    }
    
    public function chunkSize(): int
    {
        return 1000; // Specify your desired chunk size here
    }
}
