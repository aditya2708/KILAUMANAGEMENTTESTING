<?php

namespace App\Exports;

use Auth;
use DB;
use App\Models\Target;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Transaksi;
use App\Models\Transaksi_Perhari;
use App\Models\Transaksi_Perhari_Pending;
use App\Models\Transaksi_Perhari_All;
use App\Models\User;
use App\Models\Donatur;
use App\Models\Kantor;
use App\Models\Tunjangan;
use App\Models\Program;
use App\Models\Prog;
use DateTime;
use App\Models\UserKolek;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class HomeExport implements FromView
{
    public function __construct( $request )
    {
    
        $this->request = $request;
        return $this;
    }

    public function view(): View
    {
        $request = $this->request;
            $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
            $sampai = $request->sampaii == '' ? Carbon::now()->toDateString() : $request->sampaii;
            $dari2 = $request->dari2 == '' ? Carbon::now()->toDateString() : $request->dari2;
            $sampai2 = $request->sampai2 == '' ? Carbon::now()->toDateString() : $request->sampai2;
            $field = $request->field;
            $cit = $request->kotas;
    
            // dd($field);
            $kota = Auth::user()->id_kantor;
            $sum = UserKolek::get();
        
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->first();
    
    
                $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
                $sampai = $request->sampaii == '' ? $dari : $request->sampaii;
                $dari2 = $request->dari2 == '' ? Carbon::now()->toDateString() : $request->dari2;
                $sampai2 = $request->sampai2 == '' ? $dari2 : $request->sampai2;
                $bln = $request->bln == '' ? Carbon::now()->format('m-Y') : $request->bln;
                $bln2 = $request->bln2 == '' ? Carbon::now()->format('m-Y') : $request->bln2;
                $bayarin = $request->bayar;
    
                $approve = $request->approve == '' ? "approval IS NOT NULL" : "approval = '$request->approve'";
    
                $field = $request->field;
                // $cit = $request->kotas;
                $kot = $request->kotas == "" ? "transaksi.id_kantor != ''" : "transaksi.id_kantor = '$request->kotas'";
    
                $rkot = $request->kotas;
                
                $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
                $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
                
                // dd($k);
                $kunit = $k != null ? $k->id : 'asdfghqwerty';
                $kota = Auth::user()->id_kantor;
                $lev = Auth::user()->kolekting;
                $sum = UserKolek::get();
                
                $bulannew = date('m', strtotime($dari));
                $tahunnew =date('Y', strtotime($dari));
                
                $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
                $bulan1 = Carbon::createFromFormat('m-Y', $bln2)->format('m');
                $tahun1 = Carbon::createFromFormat('m-Y', $bln2)->format('Y');
                
    
                $dat = [];
                
                if ($request->tab == 'tab3') {
                    
                    $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
                    $sampai = $request->sampaii == '' ? Carbon::now()->toDateString() : $request->sampaii;
                    $dari2 = $request->dari2 == '' ? Carbon::now()->toDateString() : $request->dari2;
                    $sampai2 = $request->sampai2 == '' ? Carbon::now()->toDateString() : $request->sampai2;
                    $field = $request->field;
                    $cit = $request->kotas;
                    $kot = $request->kotas == "" ? "id_kantor != 'hahaha'" : "id_kantor = '$request->kotas'";
                    $kot2 = $request->kotas == "" ? "transaksi.id_kantor != 'hahaha'" : "transaksi.id_kantor = '$request->kotas'";
                    // $bayar = $request->bayar == '' ? "transaksi.pembayaran IS NOT NULL" : "transaksi.pembayaran = '$request->bayar'";
                    $bayar = $request->bayar;
                    $approve = $request->approve == '' ? "transaksi.approval IS NOT NULL" : "transaksi.approval = '$request->approve'";
                    $tahunn = $request->thn == '' ? date('Y') : $request->thn ;
                    $tahunn2 = $request->thn1 == '' ? date('Y') : $request->thn1;
                
                    // $target_b = DB::table('targets')->
    
                    $kota = Auth::user()->id_kantor;
                    $sum = UserKolek::get();
                    $cari = $request->cari;
                    
                    if (Auth::user()->kolekting =='admin') {
                        if ($field == 'program') {
                            if ($request->plhtgl == 0) {
                                $data = Prog::join('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth"))
                                    ->whereRaw("$kot2 AND $approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->where('transaksi.via_input', 'transaksi')
                                    ->groupBy('prog.program');
                            } else if($request->plhtgl == 1) {
                                $data = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program, transaksi.id_program as id,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100 ) as growth"))
    
                                    ->whereRaw("$kot2 AND $approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            }else{
                                $data = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) * 100 ) as growth"))
    
                                    ->whereRaw("$kot2 AND $approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            }
                        } elseif ($field == 'kota') {
                            if ($request->plhtgl == 0) {
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bulan, $tahun) {
                                        $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                            ->whereMonth('targets.tanggal', $bulan)
                                            ->whereYear('targets.tanggal', $tahun);
                                    })
                                    ->select(\DB::raw("tambahan.unit, targets.target, transaksi.id_kantor as id,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' AND transaksi.jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                ->where('tambahan.id_com', Auth::user()->id_com)
                                ->whereRaw("$approve")
                                ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            } else if(($request->plhtgl == 1)) {
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bln) {
                                            $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                // ->whereMonth('targets.tanggal', $bulan)
                                                // ->whereYear('targets.tanggal', $tahun);
                                                ->whereRaw("DATE_FORMAT(targets.tanggal, '%m-%Y') = '$bln'");
                                        })
                                    ->select(\DB::raw("tambahan.unit, targets.target,transaksi.id_kantor as id,
                                SUM(IF(DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF(DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                ->where('tambahan.id_com', Auth::user()->id_com)
                                ->whereRaw("$approve")
                                ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }else{
                                $targets = DB::table('targets')->selectRaw("id_jenis, SUM(targets.target) as tt")->leftjoin('tambahan', 'tambahan.id','=','targets.id_jenis')
                                        ->where('jenis_target','kan')
                                        ->whereYear('targets.tanggal', $tahunn)
                                        ->groupBy('id_jenis');
                                
                                // return($targets->get());
                                
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')
                                    ->leftJoin(DB::raw('(' . $targets->toSql() . ') as targets_subquery'), function ($join) {
                                        $join->on('tambahan.id', '=', DB::raw('targets_subquery.id_jenis'));
                                    })
                                    ->mergeBindings($targets)
                                    // ->leftjoin('targets',function($join) use ($tahunn) {
                                    //     $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                    //         ->whereYear('targets.tanggal', $tahunn);
                                    // })
                                    ->select(\DB::raw("tambahan.unit, tt as target,transaksi.id_kantor as id,
                                        SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                        SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                        
                                        COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                        COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                        
                                        ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) * 100) as growth,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                ->where('transaksi.via_input', 'transaksi')
                                ->where('tambahan.id_com', Auth::user()->id_com)
                                ->whereRaw("$approve")
                                ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }
                        }
                    } elseif (Auth::user()->kolekting == 'kacab' || Auth::user()->kolekting =='spv') {
                        if ($field == 'program') {
                            if ($request->plhtgl == 0) {
                                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth"))
                                    ->whereRaw("$approve")
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            } else if($request->plhtgl == 1) {
                                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100 ) as growth"))
                                    ->whereRaw("$approve")
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            }else{
                                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) * 100 ) as growth"))
                                    ->whereRaw("$approve")
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            }
                        } elseif ($field == 'kota') {
                            if ($request->plhtgl == 0) {
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bulan, $tahun) {
                                            $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                ->whereMonth('targets.tanggal', $bulan)
                                                ->whereYear('targets.tanggal', $tahun);
                                        })
                                    ->select(\DB::raw("tambahan.unit, id_transaksi, targets.target, transaksi.id_kantor as id,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                             $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->whereRaw("$approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            } else if($request->plhtgl == 1){
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bln) {
                                            $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                    ->whereRaw("DATE_FORMAT(targets.tanggal, '%m-%Y') = '$bln'");
                                        })
                                    ->select(\DB::raw("tambahan.unit, id_transaksi, targets.target, transaksi.id_kantor as id,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                             $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->whereRaw("$approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }else{
                                $targets = DB::table('targets')->selectRaw("id_jenis, SUM(targets.target) as tt")->leftjoin('tambahan', 'tambahan.id','=','targets.id_jenis')
                                        ->where('jenis_target','kan')
                                        ->whereYear('targets.tanggal', $tahunn)
                                        ->groupBy('id_jenis');
                                
                                // return($targets->get());
                                
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')
                                    ->leftJoin(DB::raw('(' . $targets->toSql() . ') as targets_subquery'), function ($join) {
                                        $join->on('tambahan.id', '=', DB::raw('targets_subquery.id_jenis'));
                                    })
                                    ->mergeBindings($targets)
                                    ->select(\DB::raw("tambahan.unit, id_transaksi, tt as target, transaksi.id_kantor as id,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->whereRaw("$approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }
                        }
                    }
                    
                    $euy = $data->get();
                    if($request->toggle == "true"){
                        $aha = [];
                        foreach($euy as $e){
                            if($request->vs == 'no'){
                                if($e->Omset > 0){
                                    $aha[] = $e;
                                }
                            }else{
                                if( $e->Omset2 != 0 || $e->Omset != 0){
                                    $aha[] = $e;
                                }
                            }
                        }
                    }else{
                        $aha = $data->get();
                    }
                    // return($euy);
                    
                    // return($aha);
                    
                    return view('ekspor.homeexport',[
                                'field' => $request->field,
                                'data' => $aha,
                                'vs' => $request->vs,
                                'berdasarkan' => $request->jdl,
                                'period' => $request->priod,
                                'kompani' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name
                    ]);
                }
                
    }
    
  
}


