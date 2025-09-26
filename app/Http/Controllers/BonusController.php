<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kolektors;
use App\Models\Kinerja;
use App\Models\Donatur;
use App\Models\Prog;
use App\Models\Prosp;
use App\Models\Karyawan;
use App\Models\Transaksi;
use App\Models\Transaksi_Perhari;
use App\Models\Kantor;
use App\Models\User;
use App\Models\Tunjangan;
use App\Models\Jabatan;
use Carbon\Carbon;
use Auth;
use DB;

use DataTables;

class BonusController extends Controller
{
    public function index(Request $request){
        $kotas = Kantor::where('id_com', Auth::user()->id_com)->get();
        if(request()->ajax()){
            $bln = $request->bln == '' ? Carbon::now()->format('m-Y') : $request->bln;
            $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
            $sampai = $request->sampaii == '' ? $dari : $request->sampaii;
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            $rkot = $request->kotas;
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->first();
            // dd($k);
            $kunit = $k != null ? $k->id : 'asdfghqwerty';
            $kot = $request->kotas == "" ? "id_kantor != ''" : "id_kantor = '$request->kotas'";
            $kota = Auth::user()->id_kantor;
            $lev = Auth::user()->level;
            $tj = Tunjangan::first();
            
            if($request->tab == 'tab1'){
                if(Auth::user()->level == 'kacab' | Auth::user()->level == 'spv'){
                    if($request->plhtgl == 0){
                        $data = Transaksi_Perhari::select(\DB::raw("name, id,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) AS Omset,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), honor, 0)) AS honor,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), bonus_cap, 0)) AS totcap,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , donasi, 0)) AS donasi,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , t_donasi, 0)) AS t_donasi,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup, 0)) AS tutup,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup_x, 0)) AS tutup_x,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , ditarik, 0)) AS ditarik,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , k_hilang, 0)) AS k_hilang,
                                COUNT(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                                COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                                COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                                
                                ->where(function($query) use ($kunit, $kota, $rkot, $lev) {
                                    if($lev == 'kacab'){
                                        if($rkot == ""){
                                            $query->where('id_kantor', $kota)->orWhere('id_kantor', $kunit);
                                        }else{
                                            $query->where('id_kantor', $rkot);
                                        }
                                    }else{
                                        $query->where('id_kantor', $kota);
                                    }
                                })
                                ->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                                
                                ->groupBy('name', 'id')->get();
                    }else{
                        $data = Transaksi_Perhari::select(\DB::raw("name, id,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), honor, 0)) AS honor,
                            SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), bonus_cap, 0)) AS totcap,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , donasi, 0)) AS donasi,
                            SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', t_donasi, 0)) AS t_donasi,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tutup, 0)) AS tutup,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tutup_x, 0)) AS tutup_x,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , ditarik, 0)) AS ditarik,
                            SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , k_hilang, 0)) AS k_hilang,
                            COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                            COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                            COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                            ->where(function($query) use ($kunit, $kota, $rkot, $lev) {
                                if($lev == 'kacab'){
                                    if($rkot == ""){
                                        $query->where('id_kantor', $kota)->orWhere('id_kantor', $kunit);
                                    }else{
                                        $query->where('id_kantor', $rkot);
                                    }
                                }else{
                                    $query->where('id_kantor', $kota);
                                }
                            })
                            ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                            
                            ->groupBy('name', 'id')->get();
                    }
                
                }elseif(Auth::user()->level == 'admin'){
                    if($request->plhtgl == 0){
                        $data = Transaksi_Perhari::select(\DB::raw("name, id,
                            
                            SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) AS Omset,
                            SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), honor, 0)) AS honor,
                            SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), bonus_cap, 0)) AS totcap,
                            
                            SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , donasi, 0)) AS donasi,
                            SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , t_donasi, 0)) AS t_donasi,
                            SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup, 0)) AS tutup,
                            SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup_x, 0)) AS tutup_x,
                            SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , ditarik, 0)) AS ditarik,
                            SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , k_hilang, 0)) AS k_hilang,
                            COUNT(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                            COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                            COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                            ->whereRaw("$kot")
                            ->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                            // ->whereDate('tanggal', '>=', $dar)->whereDate('tanggal', '<=', $sam)
                            ->groupBy('name', 'id')->get();
                       
                    
                    }else{
                        $data = Transaksi_Perhari::select(\DB::raw("name, id,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), honor, 0)) AS honor,
                            SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), bonus_cap, 0)) AS totcap,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , donasi, 0)) AS donasi,
                            SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', t_donasi, 0)) AS t_donasi,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tutup, 0)) AS tutup,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tutup_x, 0)) AS tutup_x,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , ditarik, 0)) AS ditarik,
                            SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , k_hilang, 0)) AS k_hilang,
                            COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                            COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                            COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                            ->whereRaw("$kot")
                            ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                            ->groupBy('name', 'id')->get();
                       
                    }
                    
                }
                
                return DataTables::of($data)
                ->addIndexColumn()
                
                ->addColumn('Honor', function($data){
                    $cuk1 = number_format($data->honor, 0, ',', '.');
                    $honor = 'Rp. '.$cuk1;
                    return $honor;
                })
                ->addColumn('Totcap', function($data){
                    $cuk2 = number_format($data->totcap, 0, ',', '.');
                    $totcap = 'Rp. '.$cuk2;
                    return $totcap;
                })
                ->addColumn('totbon', function($data){
                    $user = User::where('id', $data->id)->first();
                    $tj = Tunjangan::first();
                    if($user->id_jabatan == $tj->kolektor | $user->id_jabatan == $tj->sokotak){
                        if ($data->Omset <= 10000000){
                            $bon = $data->Omset * 4/100;
                        }elseif ($data->Omset > 10000000 && $data->Omset <= 20000000){
                            $bon = ($data->Omset - 10000000) * 5/100 + 400000; 
                        }elseif ($data->Omset > 20000000){
                            $bon =($data->Omset - 20000000) * 6/100 + 900000;
                        }else{
                            $bon = 0;
                        }
                    }else{
                        $bon = 0;
                    }
                    
                    return $bon;
                })
                ->addColumn('Totbon', function($data){
                    $user = User::where('id', $data->id)->first();
                    $tj = Tunjangan::first();
                    if($user->id_jabatan == $tj->kolektor | $user->id_jabatan == $tj->sokotak){
                        if ($data->Omset <= 10000000){
                            $bon = number_format($data->Omset * 4/100, 0, ',', '.');
                        }elseif ($data->Omset > 10000000 && $data->Omset <= 20000000){
                            $bon = number_format(($data->Omset - 10000000) * 5/100 + 400000, 0, ',', '.'); 
                        }elseif ($data->Omset > 20000000){
                            $bon = number_format(($data->Omset - 20000000) * 6/100 + 900000, 0, ',', '.');
                        }else{
                            $bon = number_format(0, 0, ',', '.');
                        }
                    }else{
                        $bon = number_format(0, 0, ',', '.');
                    }
                    
                    $totbon = 'Rp. '.$bon;
                    return $totbon;
                })
                ->addColumn('totbon1', function($data){
                    $user = User::where('id', $data->id)->first();
                    $tj = Tunjangan::first();
                    if($user->id_jabatan == $tj->kolektor | $user->id_jabatan == $tj->sokotak){
                        $totbon2 = $data->honor + $data->totcap ;
                        if ($data->Omset <= 10000000){
                            $tot = $data->Omset * 4/100 + $totbon2; 
                        }elseif ($data->Omset > 10000000 && $data->Omset <= 20000000){
                            $tot = ($data->Omset - 10000000) * 5/100 + 400000 + $totbon2; 
                        }elseif ($data->Omset > 20000000){
                            $tot = ($data->Omset - 20000000) * 6/100 + 900000 + $totbon2;
                        }else{
                            $tot = 0;
                        }
                    }else{
                        $tot = 0;
                    }
                    return $tot;
                })
                ->addColumn('Totbon1', function($data){
                    $user = User::where('id', $data->id)->first();
                    $tj = Tunjangan::first();
                    if($user->id_jabatan == $tj->kolektor | $user->id_jabatan == $tj->sokotak){
                        $totbon2 = $data->honor + $data->totcap ;
                        if ($data->Omset <= 10000000){
                            $tot = number_format($data->Omset * 4/100 + $totbon2, 0, ',', '.'); 
                        }elseif ($data->Omset > 10000000 && $data->Omset <= 20000000){
                            $tot = number_format(($data->Omset - 10000000) * 5/100 + 400000 + $totbon2, 0, ',', '.'); 
                        }elseif ($data->Omset > 20000000){
                            $tot = number_format(($data->Omset - 20000000) * 6/100 + 900000 + $totbon2, 0, ',', '.');
                        }else{
                            $tot = number_format(0, 0, ',', '.');
                        }
                    }else{
                        $tot = number_format(0, 0, ',', '.');
                    }
                    $totbon3 = 'Rp. '.$tot;
                    return $totbon3;
                })
               
                
                // ->rawColumns(['st'])
                // ->rawColumns(['omset', 'growth'])
                // ->with('total', $data->sum('Omset'))
                ->make(true);
            }
            
            if($request->tab == 'tab2'){
                if($request->plhtgl == 0){
                    $data = Kantor::leftjoin('transaksi_perhari', 'transaksi_perhari.id_kantor', '=', 'tambahan.id')
                        ->select(\DB::raw("transaksi_perhari.id, transaksi_perhari.name, transaksi_perhari.id_kantor,
                        SUM(IF( DATE(transaksi_perhari.tanggal) >= '$dari' AND DATE(transaksi_perhari.tanggal) <= '$sampai' AND transaksi_perhari.id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), transaksi_perhari.jumlah, 0)) AS Omset,
                        SUM(IF( DATE(transaksi_perhari.tanggal) >= '$dari' AND DATE(transaksi_perhari.tanggal) <= '$sampai' AND transaksi_perhari.id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), transaksi_perhari.honor, 0)) AS honor,
                        SUM(IF( DATE(transaksi_perhari.tanggal) >= '$dari' AND DATE(transaksi_perhari.tanggal) <= '$sampai' AND transaksi_perhari.id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), transaksi_perhari.bonus_cap, 0)) AS totcap"))
                        ->where('tambahan.id_com', Auth::user()->id_com)
                        ->groupBy('transaksi_perhari.id', 'transaksi_perhari.name', 'transaksi_perhari.id_kantor')->orderBy('transaksi_perhari.name', 'ASC')->get();
                }else{
                    $data = Kantor::leftjoin('transaksi_perhari', 'transaksi_perhari.id_kantor', '=', 'tambahan.id')
                        ->select(\DB::raw("transaksi_perhari.id, transaksi_perhari.name, transaksi_perhari.id_kantor,
                        SUM(IF( MONTH(transaksi_perhari.tanggal) = '$bulan' AND YEAR(transaksi_perhari.tanggal) = '$tahun' AND transaksi_perhari.id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), transaksi_perhari.jumlah, 0)) AS Omset,
                        SUM(IF( MONTH(transaksi_perhari.tanggal) = '$bulan' AND YEAR(transaksi_perhari.tanggal) = '$tahun' AND transaksi_perhari.id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), transaksi_perhari.honor, 0)) AS honor,
                        SUM(IF( MONTH(transaksi_perhari.tanggal) = '$bulan' AND YEAR(transaksi_perhari.tanggal) = '$tahun' AND transaksi_perhari.id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), transaksi_perhari.bonus_cap, 0)) AS totcap"))
                        ->where('tambahan.id_com', Auth::user()->id_com)
                        ->groupBy('transaksi_perhari.id' , 'transaksi_perhari.name', 'transaksi_perhari.id_kantor')->orderBy('transaksi_perhari.name', 'ASC')->get();
                }
                
                $arr = [];
                    
                    // dd($data);
                foreach($data as $key => $item){
                    // $arr[$item['kota']][$key] = $item->Omset;
                    $totbon2 = $item->honor + $item->totcap ;
                    if ($item->Omset <= 10000000){
                        $tot = $item->Omset * 4/100 + $totbon2; 
                    }elseif ($item->Omset > 10000000 && $item->Omset <= 20000000){
                        $tot = ($item->Omset - 10000000) * 5/100 + 400000 + $totbon2; 
                    }elseif ($item->Omset > 20000000){
                        $tot = ($item->Omset - 20000000) * 6/100 + 900000 + $totbon2;
                    }else{
                        $tot = 0;
                    }
                   
                    $kantorr = Kantor::where('id', $item->id_kantor)->first();
                    
                    // dd($kantorr->unit);
                    $arr[] = [
                        "kota" => $item->id_kantor != null ? $kantorr->unit : null,
                        "bonus" => $tot,
                    ];
                    // dd($arr);
                    // if(array_key_exists($vals['kota'],$arr)){
                    //     $res[$vals['kota']]['count']    += $vals['count'];
                    // }
                    // else{
                    //     $res[$vals['browser']]  = $vals;
                    // }
                    // foreach(array_count_values(array_column($item, 'id'))[$item->id] as $k => $v){
                    //     $jml[$key][$k] = $v;
                    //   }
                }
                // dd($arr);
                
                // $items = '[{"code": "2132", "title":"Mechanical Engineers"},
                //              {"code": "2134", "title":"Chemical engineers"},
                //              {"code":"2132", "title":"Mechanical Engineers"}]';
                // $array = json_decode($items, true);

                // try this:
                
                $arr1 = collect($arr)->groupBy('kota')->map(function($item) {
                    $c = 0;
                    foreach ($item as $val){
                        $c += $val['bonus'];
                    }
                    return array_merge($item->first(), array("tot" => $c));
                })->all();
                
                $datas1 = array_values($arr1);
                $datas = array_filter($datas1, function($value) { return !is_null($value['kota']) && $value['kota'] !== ''; });
                // dd($datas1);

                // $tt  = array_count_values($arr);
                
                
                return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('Totbon1', function($datas){
                    $cuk1 = number_format($datas['tot'], 0, ',', '.');
                    $totbon3 = 'Rp. '.$cuk1;
                    return $totbon3;
                })
                ->make(true);
            }
            
            if($request->tab == 'tab22'){
                if($request->plhtgl == 0){
                    $data = Kantor::leftjoin('transaksi_perhari', 'transaksi_perhari.id_kantor', '=', 'tambahan.id')
                        ->select(\DB::raw("transaksi_perhari.id, transaksi_perhari.name, transaksi_perhari.id_kantor,
                        SUM(IF( DATE(transaksi_perhari.tanggal) >= '$dari' AND DATE(transaksi_perhari.tanggal) <= '$sampai' AND transaksi_perhari.id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), transaksi_perhari.jumlah, 0)) AS Omset,
                        SUM(IF( DATE(transaksi_perhari.tanggal) >= '$dari' AND DATE(transaksi_perhari.tanggal) <= '$sampai' AND transaksi_perhari.id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), transaksi_perhari.honor, 0)) AS honor,
                        SUM(IF( DATE(transaksi_perhari.tanggal) >= '$dari' AND DATE(transaksi_perhari.tanggal) <= '$sampai' AND transaksi_perhari.id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), transaksi_perhari.bonus_cap, 0)) AS totcap"))
                        ->groupBy('transaksi_perhari.id', 'transaksi_perhari.name', 'transaksi_perhari.id_kantor')->orderBy('transaksi_perhari.name', 'ASC')->get();
                }else{
                    $data = Kantor::leftjoin('transaksi_perhari', 'transaksi_perhari.id_kantor', '=', 'tambahan.id')
                        ->select(\DB::raw("transaksi_perhari.id, transaksi_perhari.name, transaksi_perhari.id_kantor,
                        SUM(IF( MONTH(transaksi_perhari.tanggal) = '$bulan' AND YEAR(transaksi_perhari.tanggal) = '$tahun' AND transaksi_perhari.id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), transaksi_perhari.jumlah, 0)) AS Omset,
                        SUM(IF( MONTH(transaksi_perhari.tanggal) = '$bulan' AND YEAR(transaksi_perhari.tanggal) = '$tahun' AND transaksi_perhari.id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), transaksi_perhari.honor, 0)) AS honor,
                        SUM(IF( MONTH(transaksi_perhari.tanggal) = '$bulan' AND YEAR(transaksi_perhari.tanggal) = '$tahun' AND transaksi_perhari.id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor' OR id_jabatan = '$tj->sokotak'), transaksi_perhari.bonus_cap, 0)) AS totcap"))
                        ->groupBy('transaksi_perhari.id' , 'transaksi_perhari.name', 'transaksi_perhari.id_kantor')->orderBy('transaksi_perhari.name', 'ASC')->get();
                }
                
                $arr = [];
                    
                    // dd($data);
                foreach($data as $key => $item){
                    // $arr[$item['kota']][$key] = $item->Omset;
                    $totbon2 = $item->honor + $item->totcap ;
                    if ($item->Omset <= 10000000){
                        $tot = $item->Omset * 4/100 + $totbon2; 
                    }elseif ($item->Omset > 10000000 && $item->Omset <= 20000000){
                        $tot = ($item->Omset - 10000000) * 5/100 + 400000 + $totbon2; 
                    }elseif ($item->Omset > 20000000){
                        $tot = ($item->Omset - 20000000) * 6/100 + 900000 + $totbon2;
                    }else{
                        $tot = 0;
                    }
                   
                    $kantorr = Kantor::where('id', $item->id_kantor)->first();
                    
                    $arr[] = [
                        "kota" => $item->id_kantor != null ? $kantorr->unit : null,
                        "bonus" => $tot,
                    ];
                    
                }
                
                $arr1 = collect($arr)->groupBy('kota')->map(function($item) {
                    $c = 0;
                    foreach ($item as $val){
                        $c += $val['bonus'];
                    }
                    return array_merge($item->first(), array("tot" => $c));
                })->all();
                
                $datas1 = array_values($arr1);
                $datas = array_filter($datas1, function($value) { return !is_null($value['kota']) && $value['kota'] !== ''; });
                $nil = 0;
                $dataes = [];
                foreach($datas as $d){
                    $nil += $d['tot'];
                }
                $dataes = $nil;
                
                return $dataes;
            }
            
        }
        return view('kolekting.bonus_kolekting', compact('kotas'));
    }
    
    
    public function bonus_sales(Request $request){
        
        $kotas = Kantor::where('id_com', Auth::user()->id_com)->get();
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kot = Auth::user()->id_kantor;
        
        $tj = Tunjangan::first();
            
        $jabatan = Jabatan::whereRaw("(id = '$tj->sokotak' OR id = '$tj->so' OR id = '$tj->kolektor')")->get();
        
        if($request->ajax()){
            
            $rkot = $request->kotas;
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            $kunit = $k != null ? $k->id : 'asdfghqwerty';
            $kot = $request->kotas == "" ? "id_kantor != ''" : "id_kantor = '$request->kotas'";
            
            $kotah = $request->unit == "" ? "id_kantor IS NOT NULL" : "id_kantor = '$request->unit'";
            $jabat = $request->jabat == "" ? "(jabatan = '$tj->sokotak' OR jabatan = '$tj->so')" : "jabatan = '$request->jabat'";
            
            $tj = Tunjangan::first();
            
            $kota = Auth::user()->id_kantor;
            $lev = Auth::user()->level;
            
            if($lev == 'admin'){
                $kara = Karyawan::whereRaw("aktif = 1 AND $jabat AND $kotah")->get();   
            }else{
                if($k == null){
                    $kara = Karyawan::whereRaw("id_kantor =  '$kota' AND $jabat AND aktif = 1 ")->get();
                }else{
                    if($request->unit == ""){
                        $kara = Karyawan::whereRaw("(id_kantor = '$kota' OR id_kantor = '$k->id') AND $jabat AND aktif = 1")->get();
                    }else{
                        $kara = Karyawan::whereRaw("id_kantor = '$request->unit' AND $jabat AND aktif = 1")->get();
                    }
                }
            }
            
            $datfin = [];
        //   return($kara); 
            foreach($kara as $kar){
                
                $blnn = $request->bln == '' ? date('Y-m-01') : $request->bln.'-01';
                
                $tgl_awal = date('Y-m-01', strtotime('-5 month', strtotime($blnn)));
                $tgl_trans = date('Y-m-01', strtotime($blnn));
                $tanggal = date('Y-m-t', strtotime($blnn));
                
                $idprosdon = Prosp::join('users', 'prosp.id_peg','=', 'users.id')
                            ->select(\DB::raw("id_don"))
                            ->where(function($query) use ($tgl_trans, $tanggal) {
                                $query->where('prosp.ket', '!=', 'open')->whereDate('prosp.tgl_fol', '>=', $tgl_trans)->whereDate('prosp.tgl_fol', '<=', $tanggal)
                                      ->orWhere('prosp.ket', 'open')->whereDate('prosp.updated_at', '>=', $tgl_trans)->whereDate('prosp.updated_at', '<=', $tanggal);
                            })
                            ->whereRaw("prosp.created_at IS NOT NULL")
                            ->where('users.id_karyawan', $kar->id_karyawan)
                            ->get();
                    
                            
                
                $id_prosdon = [];
                
                if(count($idprosdon) > 0){
                    foreach($idprosdon as $x => $v){
                        $id_prosdon[] = $v->id_don;
                    }
                }
                // return(count($id_prosdon));            
                $prosdon = Prosp::join('users', 'prosp.id_peg','=', 'users.id')
                            ->select(\DB::raw("
                                    COUNT(DISTINCT IF(prosp.ket = 'closing' AND prosp.created_at IS NOT NULL AND DATE(prosp.tgl_fol) >= '$tgl_trans' AND DATE(prosp.tgl_fol) <= '$tanggal', id_don, NULL)) AS closing,
                                    COUNT(DISTINCT IF(prosp.ket = 'open' AND prosp.created_at IS NOT NULL AND DATE(prosp.created_at) >= '$tgl_trans' AND DATE(prosp.created_at) <= '$tanggal', id_don, NULL)) AS open,
                                    COUNT(DISTINCT IF(prosp.ket = 'cancel' AND prosp.created_at IS NOT NULL AND DATE(prosp.tgl_fol) >= '$tgl_trans' AND DATE(prosp.tgl_fol) <= '$tanggal', id_don, NULL)) AS cancel
                                "))
                            ->where('users.id_karyawan',$kar->id_karyawan)
                            ->first();
                
                        
                $prosp = Transaksi::
                        join('prog', 'transaksi.id_program', '=', 'prog.id_program')->join('prosp', 'transaksi.id_pros','=', 'prosp.id')
                            ->select(\DB::raw("
                                transaksi.id_donatur, transaksi.donatur, DATE_FORMAT(transaksi.tanggal, '%Y-%m') AS bulan, 
                                transaksi.id_program, transaksi.subprogram, prosp.tgl_fol,
                                SUM(transaksi.jumlah) AS omset, transaksi.pembayaran
                                "))
                            ->whereIn('transaksi.id_pros', function($pr) use ($kar, $tgl_awal) {
                            $pr->select('id')->from('prosp')->whereIn('id_peg', function($query) use ($kar) {
                                    $query->select('id')->from('users')->where('id_karyawan', $kar->id_karyawan);
                                })->whereDate('tgl_fol','>=', $tgl_awal)->where('ket', 'closing')->where('status', 1)->where('created_at','!=',NULL)
                                // ->where('konprog', 0)
                                ;
                            })
                            ->whereDate('transaksi.tanggal','>=', $tgl_trans)
                            ->groupBy('transaksi.id_donatur', 'transaksi.donatur', 'bulan', 'transaksi.subprogram', 'transaksi.id_program', 'prosp.tgl_fol', 'transaksi.pembayaran')
                            // ->whereMonth('transaksi.created_at', 4)->whereYear('transaksi.created_at', date('Y'))
                            ->get();
                
  
                $totbonpo = 0;
                $tothonpo = 0;
                $totbonset = 0;
                $totpo = 0;
                $totbon = 0;
                    $poin = 0;
                    $honpo = 0;
                    $bonpo = 0;
                    $bonset = 0;
                $data = [];
                $total = [];
                $id_don = [];
                if(count($prosp) > 0){
                    foreach($prosp as $x => $v){
                        
                        
                        $bln = date_diff(date_create($tanggal), date_create($v->tgl_fol));
                        $b = $bln->m;
                        $p = Prog::where('id_program', $v->id_program)->first();
                        $omst = $v->pembayaran == 'noncash' ? $v->omset*($p->prenoncash/100) : $v->omset; 
                        $prog = $p->tes == 'b:0;' || @unserialize($p->tes) != false ? unserialize($p->tes) : [];
                        $honp = $p->honpo == 'b:0;' || @unserialize($p->honpo) != false ? unserialize($p->honpo) : [];
                        $bonp = $p->bonpo == 'b:0;' || @unserialize($p->bonpo) != false ? unserialize($p->bonpo) : [];
                        $bons = $p->bonset == 'b:0;' || @unserialize($p->bonset) != false ? unserialize($p->bonset) : [];
                        $konb = $p->konbon == 'b:0;' || @unserialize($p->konbon) != false ? unserialize($p->konbon) : [];
                        $inhonp = $p->inhonpo == 'b:0;' || @unserialize($p->inhonpo) != false ? unserialize($p->inhonpo) : [];
                        $inbonp = $p->inbonpo == 'b:0;' || @unserialize($p->inbonpo) != false ? unserialize($p->inbonpo) : [];
                        $inbons = $p->inbonset == 'b:0;' || @unserialize($p->inbonset) != false ? unserialize($p->inbonset) : [];
                        $bons2 = $p->bonset2 == 'b:0;' || @unserialize($p->bonset2) != false ? unserialize($p->bonset2) : [];
                        $minp2 = $p->minpo2 == 'b:0;' || @unserialize($p->minpo2) != false ? unserialize($p->minpo2) : [];
                        // dd($bons);
                        // return($inhonp);
                           
                        if($inhonp[$b] == 1){
                            $honpo = $omst < $p->minpo ? 0: floatval($honp[$b]);
                            $poin = $omst < $p->minpo ? 0 : 1;
                        }else if($inhonp[$b] == 2){
                            $honpo = round(($omst/$p->minpo)*$honp[$b]);
                            $poin = $omst/$p->minpo;
                        }else{
                            $honpo = 0;
                            $poin = 0;
                        }
                        
                        if($inbonp[$b] == 1){
                            $bonpo = $omst < $p->minpo ? 0 : floatval($bonp[$b]);
                        }else if($inbonp[$b] == 2){
                            $bonpo = round(($omst/$p->minpo)*$bonp[$b]);
                        }else{
                            $bonpo = 0;
                        }
                        
                        if($inbons[$b] == 1){
                            $bonset = $omst < $p->minpo ? 0 : floatval($bons[$b]);
                        }else if($inbons[$b] == 2){
                            $bonset = round($omst*($bons[$b]/100)); 
                        }else if($inbons[$b] == 3){
                            if($omst >= $p->minpo){
                                $bonset = floatval($bons[$b]);
                            }else if($omst < $p->minpo && $omst >= floatval($minp2[$b])){
                                $bonset = floatval($bons2[$b]);
                            }else{
                                $bonset = 0;
                            }
                        }else{
                            $bonset = 0;
                            // $bonset = $inbons[$b] == 1 ? round($omst*($bons[$b]/100)) : floatval($bons[$b]);
                        }
                        
                        if($poin > 0 | $honpo > 0 | $bonpo > 0 | $bonset > 0){
                        $id_don[] = $v->id_donatur;
                        $data[] = [
                            'b' => $b,
                            'bulan' => $v->bulan,
                            'id_donatur' => $v->id_donatur,
                            'donatur' => $v->donatur,
                            'subprogram' => $v->subprogram,
                            'tgl_fol' => $v->tgl_fol,
                            'omset' => $omst,
                            'minpo' => $p->minpo,
                            'poin' => $poin,
                            'honpo' => $honpo,
                            'bonpo' => $bonpo,
                            'bonset' => $bonset,
                            'totbon' => $honpo + $bonpo + $bonset
                            // 'kolektif' => $p->kolektif,
                        ];
                        }
                    }
                }else{
                  $data = []; 
                }
                
                // return($data);
                
                $tgl_gaji = $blnn;
                $month = date("m",strtotime($tgl_gaji));
                $year = date("Y",strtotime($tgl_gaji));
                $tj = Tunjangan::first();
                if($kar->jabatan == $tj->sokotak){
                    $kolek = Transaksi::leftjoin('users', 'transaksi.id_koleks', '=', 'users.id')
                            ->select(\DB::raw("transaksi.tanggal, users.id, 
                                SUM(jumlah) AS jumlah,
                                COUNT(DISTINCT IF(transaksi.subtot >= users.minimal AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) * users.honor AS honor
                                "))
                            ->whereMonth('transaksi.tanggal', $month)->whereYear('transaksi.tanggal', $year)
                            ->whereNotIn('transaksi.id_donatur', $id_don)
                            ->where('users.id_karyawan', $kar->id_karyawan)
                            ->groupBy('tanggal', 'id')
                            ->get();
                    
                    $omset  = 0;
                    $honor  = 0;
                    for($i=0; $i<count($kolek); $i++){
                    $omset  += $kolek[$i]['jumlah'];
                    $honor  += $kolek[$i]['honor'];
                    }
                    
                    // return($omset);        
                    if($kar->jabatan == $tj->sokotak){
                        $honor = $kolek != null ? $honor : 0;
                        // $boncap = $kolek != null ? $kolek->boncap : 0;
                        if ($omset <= 10000000){
                            $bon = $omset * 4/100;
                        }elseif ($omset > 10000000 && $omset <= 20000000){
                            $bon = ($omset - 10000000) * 5/100 + 400000; 
                        }elseif ($omset > 20000000){
                            $bon = ($omset - 20000000) * 6/100 + 900000;
                        }else{
                            $bon = 0;
                        }
                    }
                }
                
                for($i=0; $i<count($data); $i++){
                    $totpo  += $data[$i]['poin'];
                    $tothonpo  += $data[$i]['honpo'];
                    $totbonpo  += $data[$i]['bonpo'];
                    $totbonset  += $data[$i]['bonset'];
                    $totbon += $data[$i]['totbon'];
                }
                
                // return($total);
                
                $datfin[] = [
                            'id' => $kar->id_karyawan,
                            'petugas' => $kar->nama,
                            'jabatan' => Jabatan::where('id',$kar->jabatan)->first()->jabatan,
                            'kantor' => $kar->unit_kerja,
                            'bonpo' => round($totbonpo),
                            'bonset' => round($totbonset),
                            'honpo' => round($tothonpo),
                            'totpo' => round($totpo, 1),
                            'totbon' => $totbon
                        ];                   
                
            }
            
            // return($datfin);
            
            return DataTables::of($datfin)
            ->addIndexColumn()
           
            ->make(true);
        
        }
        return view('sales.data_bonus', compact('kotas','jabatan'));
    }
    
    public function bonus_sales_nih(Request $request, $id){
        
        if($request->ajax()){
            $kar = Karyawan::where('id_karyawan', $id)->first();
            
            $blnn = $request->bln == '' ? date('Y-m-01') : $request->bln.'-01';
            
            $tgl_awal = date('Y-m-01', strtotime('-5 month', strtotime($blnn)));
            $tgl_trans = date('Y-m-01', strtotime($blnn));
            $tanggal = date('Y-m-t', strtotime($blnn));
            
            $idprosdon = Prosp::join('users', 'prosp.id_peg','=', 'users.id')
                        ->select(\DB::raw("id_don"))
                        ->where(function($query) use ($tgl_trans, $tanggal) {
                            $query->where('prosp.ket', '!=', 'open')->whereDate('prosp.tgl_fol', '>=', $tgl_trans)->whereDate('prosp.tgl_fol', '<=', $tanggal)
                                  ->orWhere('prosp.ket', 'open')->whereDate('prosp.updated_at', '>=', $tgl_trans)->whereDate('prosp.updated_at', '<=', $tanggal);
                        })
                        ->whereRaw("prosp.created_at IS NOT NULL")
                        ->where('users.id_karyawan',$id)
                        ->get();
            
            $id_prosdon = [];
            
            if(count($idprosdon) > 0){
                foreach($idprosdon as $x => $v){
                    $id_prosdon[] = $v->id_don;
                }
            }
            // return(count($id_prosdon));            
            $prosdon = Prosp::join('users', 'prosp.id_peg','=', 'users.id')
                        ->select(\DB::raw("
                                COUNT(DISTINCT IF(prosp.ket = 'closing' AND prosp.created_at IS NOT NULL AND DATE(prosp.tgl_fol) >= '$tgl_trans' AND DATE(prosp.tgl_fol) <= '$tanggal', id_don, NULL)) AS closing,
                                COUNT(DISTINCT IF(prosp.ket = 'open' AND prosp.created_at IS NOT NULL AND DATE(prosp.created_at) >= '$tgl_trans' AND DATE(prosp.created_at) <= '$tanggal', id_don, NULL)) AS open,
                                COUNT(DISTINCT IF(prosp.ket = 'cancel' AND prosp.created_at IS NOT NULL AND DATE(prosp.tgl_fol) >= '$tgl_trans' AND DATE(prosp.tgl_fol) <= '$tanggal', id_don, NULL)) AS cancel
                            "))
                        ->where('users.id_karyawan',$id)
                        ->first();
            // return($prosdon);            
            $prosp = Transaksi::
                    join('prog', 'transaksi.id_program', '=', 'prog.id_program')->join('prosp', 'transaksi.id_pros','=', 'prosp.id')
                        ->select(\DB::raw("
                            transaksi.id_donatur, transaksi.donatur, DATE_FORMAT(transaksi.tanggal, '%Y-%m') AS bulan, 
                            transaksi.id_program, transaksi.subprogram, prosp.tgl_fol,
                            SUM(transaksi.jumlah) AS omset, transaksi.pembayaran, transaksi.kolektor
                            "))
                        ->whereIn('transaksi.id_pros', function($pr) use ($id, $tgl_awal) {
                        $pr->select('id')->from('prosp')->whereIn('id_peg', function($query) use ($id) {
                                $query->select('id')->from('users')->where('id_karyawan', $id);
                            })->whereDate('tgl_fol','>=', $tgl_awal)->where('ket', 'closing')->where('status', 1)->where('created_at','!=',NULL)
                            // ->where('konprog', 0)
                            ;
                        })
                        ->whereDate('transaksi.tanggal','>=', $tgl_trans)
                        ->groupBy('transaksi.id_donatur', 'transaksi.donatur', 'bulan', 'transaksi.subprogram', 'transaksi.id_program', 'prosp.tgl_fol', 'transaksi.pembayaran' ,'transaksi.kolektor')
                        // ->whereMonth('transaksi.created_at', 4)->whereYear('transaksi.created_at', date('Y'))
                        ->get();
                        
            
            $totbonpo = 0;
            $tothonpo = 0;
            $totbonset = 0;
            $totpo = 0;
                $poin = 0;
                $honpo = 0;
                $bonpo = 0;
                $bonset = 0;
            $data = [];
            $total = [];
            $id_don = [];
            if(count($prosp) > 0){
                foreach($prosp as $x => $v){
                    
                    $bln = date_diff(date_create($tanggal), date_create($v->tgl_fol));
                    // return($bln->m);
                    $b = $bln->m;
                    $p = Prog::where('id_program', $v->id_program)->first();
                    $omst = $v->pembayaran == 'noncash' ? $v->omset*($p->prenoncash/100) : $v->omset; 
                    $prog = $p->tes === 'b:0;' || @unserialize($p->tes) !== false ? unserialize($p->tes) : [];
                    $honp = $p->honpo === 'b:0;' || @unserialize($p->honpo) !== false ? unserialize($p->honpo) : [];
                    $bonp = $p->bonpo === 'b:0;' || @unserialize($p->bonpo) !== false ? unserialize($p->bonpo) : [];
                    $bons = $p->bonset === 'b:0;' || @unserialize($p->bonset) !== false ? unserialize($p->bonset) : [];
                    $konb = $p->konbon === 'b:0;' || @unserialize($p->konbon) !== false ? unserialize($p->konbon) : [];
                    $inhonp = $p->inhonpo === 'b:0;' || @unserialize($p->inhonpo) !== false ? unserialize($p->inhonpo) : [];
                    $inbonp = $p->inbonpo === 'b:0;' || @unserialize($p->inbonpo) !== false ? unserialize($p->inbonpo) : [];
                    $inbons = $p->inbonset === 'b:0;' || @unserialize($p->inbonset) !== false ? unserialize($p->inbonset) : [];
                    $bons2 = $p->bonset2 === 'b:0;' || @unserialize($p->bonset2) !== false ? unserialize($p->bonset2) : [];
                    $minp2 = $p->minpo2 === 'b:0;' || @unserialize($p->minpo2) !== false ? unserialize($p->minpo2) : [];
                    // dd($bons);
                       
                    if($inhonp[$b] == 1){
                        $honpo = $omst < $p->minpo ? 0: floatval($honp[$b]);
                        $poin = $omst < $p->minpo ? 0 : 1;
                    }else if($inhonp[$b] == 2){
                        $honpo = round(($omst/$p->minpo)*$honp[$b]);
                        $poin = $omst/$p->minpo;
                    }else{
                        $honpo = 0;
                        $poin = 0;
                    }
                    
                    if($inbonp[$b] == 1){
                        $bonpo = $omst < $p->minpo ? 0 : floatval($bonp[$b]);
                    }else if($inbonp[$b] == 2){
                        $bonpo = round(($omst/$p->minpo)*$bonp[$b]);
                    }else{
                        $bonpo = 0;
                    }
                    
                    if($inbons[$b] == 1){
                        $bonset = $omst < $p->minpo ? 0 : floatval($bons[$b]);
                    }else if($inbons[$b] == 2){
                        $bonset = round($omst*($bons[$b]/100)); 
                    }else if($inbons[$b] == 3){
                        if($omst >= $p->minpo){
                            $bonset = floatval($bons[$b]);
                        }else if($omst < $p->minpo && $omst >= floatval($minp2[$b])){
                            $bonset = floatval($bons2[$b]);
                        }else{
                            $bonset = 0;
                        }
                    }else{
                        $bonset = 0;
                        // $bonset = $inbons[$b] == 1 ? round($omst*($bons[$b]/100)) : floatval($bons[$b]);
                    }
                    
                    if($poin > 0 | $honpo > 0 | $bonpo > 0 | $bonset > 0){
                    $id_don[] = $v->id_donatur;
                    $data[] = [
                        'petugas' => $v->kolektor,
                        'b' => $b,
                        'bulan' => $v->bulan,
                        'id_donatur' => $v->id_donatur,
                        'donatur' => $v->donatur,
                        'subprogram' => $v->subprogram,
                        'tgl_fol' => $v->tgl_fol,
                        'omset' => $omst,
                        'minpo' => $p->minpo,
                        'poin' => round($poin, 1),
                        'honpo' => $honpo,
                        'bonpo' => $bonpo,
                        'bonset' => $bonset,
                        'totbon' => $honpo + $bonpo + $bonset
                        // 'kolektif' => $p->kolektif,
                    ];
                    }
                }
            }else{
              $data = []; 
            }
            
            $tgl_gaji = $kar->tgl_gaji;
            $month = date("m",strtotime($tgl_gaji));
            $year = date("Y",strtotime($tgl_gaji));
            $tj = Tunjangan::first();
            if($kar->jabatan == $tj->sokotak){
                $kolek = Transaksi::leftjoin('users', 'transaksi.id_koleks', '=', 'users.id')
                        ->select(\DB::raw("transaksi.tanggal, users.id, 
                            SUM(jumlah) AS jumlah,
                            COUNT(DISTINCT IF(transaksi.subtot >= users.minimal AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) * users.honor AS honor
                            "))
                        ->whereMonth('transaksi.tanggal', $month)->whereYear('transaksi.tanggal', $year)
                        ->whereNotIn('transaksi.id_donatur', $id_don)
                        ->where('users.id_karyawan',$id)
                        ->groupBy('tanggal', 'id')
                        ->get();
                
                $omset  = 0;
                $honor  = 0;
                for($i=0; $i<count($kolek); $i++){
                $omset  += $kolek[$i]['jumlah'];
                $honor  += $kolek[$i]['honor'];
                }
                
                // return($omset);        
                if($kar->jabatan == $tj->sokotak){
                    $honor = $kolek != null ? $honor : 0;
                    // $boncap = $kolek != null ? $kolek->boncap : 0;
                    if ($omset <= 10000000){
                        $bon = $omset * 4/100;
                    }elseif ($omset > 10000000 && $omset <= 20000000){
                        $bon = ($omset - 10000000) * 5/100 + 400000; 
                    }elseif ($omset > 20000000){
                        $bon = ($omset - 20000000) * 6/100 + 900000;
                    }else{
                        $bon = 0;
                    }
                }
            }
            // dd($id_don, $data);
            for($i=0; $i<count($data); $i++){
            $totpo  += $data[$i]['poin'];
            $tothonpo  += $data[$i]['honpo'];
            $totbonpo  += $data[$i]['bonpo'];
            $totbonset  += $data[$i]['bonset'];
            }
            
            $daftin = [];
            
            $daftin = [
                'data' => $data
            ];
        }
        return($daftin);
    }

}
