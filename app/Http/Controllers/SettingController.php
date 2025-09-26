<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kolektors;
use App\Models\Karyawan;
use App\Models\Kinerja;
use App\Models\Donatur;
use App\Models\Transaksi;
use App\Models\Transaksi_Perhari;
use App\Models\Kantor;
use App\Models\User;
use App\Models\SumberDana;
use App\Models\SaldoDana;
use App\Models\Prog;
use App\Models\UserSSO;
use App\Models\ProgBSZ;
use App\Models\COA;
use App\Models\Tunjangan;
use App\Models\Targets;
use App\Models\ProgPerus;
use Carbon\Carbon;
use Auth;
use DB;
use DateTime;
use Illuminate\Support\Str;

use DataTables;

class SettingController extends Controller
{
    public function set_target(Request $request){
        $kntr = Auth::user()->id_kantor;
        
        if(Auth::user()->level == 'admin' || Auth::user()->level == 'keuangan pusat'){
            $kota = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else if(Auth::user()->level == 'kacab'){
            $kota = Kantor::where('id', $kntr)->orWhere('kantor_induk', $kntr)->get();
        }
        
        if ($request->ajax()) {
            
            $id_com = Auth::user()->id_com;
            $kntr   = Auth::user()->id_kantor;
            $lev    = Auth::user()->kolekting;
            $r_bln  = $request->bln != '' ? $request->bln : date('Y-m');
            $t_bln  = date('Y-m-01', strtotime($r_bln));
            $r_thn  = $request->thn != '' ? $request->thn : date('Y');
            $r_unit = $request->unit;
            $w_prd  = $request->periode == 'tahun' ? "targets.periode = 'tahun'" : "targets.periode = 'bulan'";
            $w_tgl  = $request->periode == 'tahun' ? "YEAR(targets.tanggal) = '$r_thn'" : "DATE(targets.tanggal) = '$t_bln'";
            $w_jns  = $request->jenis == '' ? "targets.jenis_target IS NOT NULL" : "targets.jenis_target = '$request->jenis'";
            $d_tgl  = $request->periode == 'tahun' ? $r_thn.'-01-01' : $t_bln;
            
            $datay   = [];
            // ini
            $y = $request->bln == '' ? date('Y-m') : $request->bln;
            // $yy = $request->tahun == '' ? date('Y') : $request->tahun;
            
            // $period = $request->periode == 'tahun' ? "targets.periode = 'tahun'" : "targets.periode = 'bulan'";
            
            // $ganjen = $request->units;
            // $ganjen2 = $request->units2;
            
            // $ye = $request->tahun != '' ? $request->tahun : date('Y');
            
            // $bulan = Carbon::createFromFormat('Y-m', $y)->format('m');
            // $tahun = Carbon::createFromFormat('Y-m', $y)->format('Y');
            
            // $full = $request->periode == 'tahun' ? "YEAR(targets.tanggal) = '$ye'" : "MONTH(targets.tanggal) = '$bulan' AND  YEAR(targets.tanggal) = '$tahun'";
            // tutupini
            
            // $jenis = $request->jenis == '' ? "jenis_target IS NOT NULL" : "jenis_target = '$request->jenis'";
            
            // $tj = Tunjangan::first()->kolektor;
            // $id_com = Auth::user()->id_com;
            
            // $datay = [];
            
            // $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            // $kunit = $k != null ? $k->id : 'asdfghqwerty';
            // $kota = Auth::user()->id_kantor;
            // $lev = Auth::user()->kolekting;
            
            // if ($lev == 'admin') {
            //     $unit = $request->units2 == '' ? "users.id_kantor IS NOT NULL" :  "users.id_kantor = '$request->units2'";
            // } else if($lev == 'kacab') {
            //     if($k != null){
            //         if($ganjen2 == ''){
            //             $eh = $k->id;
            //             $unit = "(users.id_kantor = '$kota' OR users.id_kantor = '$eh')";
            //         }else{
            //             $unit = "users.id_kantor = '$request->units2'";
            //         }
            //     }else{
            //         $unit = $request->units2 == '' ? "users.id_kantor = '$kota'" :  "users.id_kantor = '$request->units2'";
            //     }
            // }
            
            // if($request->jenis == 'id_kan'){
            //     $data = Kantor::selectRaw("'kan' as jenisnyaa ,tambahan.id as idnya, tambahan.unit as jenisnya, targets.*, 0 as pimpinan")
            //                     ->leftJoin('targets',function($join) use ($full, $period){
            //                     $join->on('targets.id_jenis', 'tambahan.id')
            //                          ->where('targets.jenis_target', 'kan')
            //                          ->whereRaw("$full AND $period");
            //                     })
            //                     // ->leftJoin('targets','targets.id_jenis','=','tambahan.id')
            //                     ->whereRaw("id_com = '$id_com'")
            //                     ->where(function ($query) use ($kunit, $kota, $lev, $k) {
            //                         if ($lev == 'admin') {
            //                             $query->whereRaw("tambahan.id IS NOT NULL");
            //                         } else if($lev == 'kacab') {
            //                             if($k != null){
            //                                 $query->where('tambahan.id', $kota)->orWhere('tambahan.id', $kunit);
            //                             }else{
            //                                 $query->where('tambahan.id', $kota);
            //                             }
            //                         }
            //                     })
            //                     ->groupBy('idnya')
            //                     ->get();
            // }else if($request->jenis == 'id_kar'){
            //     $data = User::selectRaw("'kar' as jenisnyaa, users.id_karyawan as idnya, users.name as jenisnya, targets.*, tambahan.id_pimpinan as pimpinan")
            //                 ->leftJoin('targets',function($join) use ($full, $period){
            //                     $join->on('targets.id_jenis', 'users.id_karyawan')
            //                          ->where('targets.jenis_target', 'kar')
            //                          ->whereRaw("$full AND $period");
            //                     })
            //                 ->join('tambahan','tambahan.id','=','users.id_kantor')
            //                 ->whereRaw("users.id_com = '$id_com' AND id_karyawan IS NOT NULL AND aktif = '1' AND $unit AND status_kerja != 'Magang'")
            //                 ->where(function ($query) use ($kunit, $kota, $lev, $k) {
            //                     if ($lev == 'admin') {
            //                         $query->whereRaw("users.id_kantor IS NOT NULL");
            //                     } else if($lev == 'kacab') {
            //                         if($k != null){
            //                             $query->where('users.id_kantor', $kota)->orWhere('users.id_kantor', $kunit);
            //                         }else{
            //                             $query->where('users.id_kantor', $kota);
            //                         }
            //                     }
            //                 })
            //                 ->groupBy('idnya')
            //                 ->get();
            // }else if($request->jenis == 'prog'){
            //     $query = DB::table('prog as t1')
            //         ->selectRaw("t1.*, t1.id_program as root")
                    
            //         ->unionAll(
            //             DB::table('b as t0')
            //                 ->selectRaw("t3.*, t0.root")
            //                 ->join('prog as t3', 't3.id_program_parent', '=', 't0.id_program')
                                
            //         );
                    
            //     $data = DB::table('b as t')
            //             ->selectRaw("'prog' as jenisnyaa, t.parent, t.id_program as idnya, t.program as jenisnya, targets.alasan_tolak, targets.tanggal, 
            //             targets.kunjungan, targets.transaksi,  targets.minimal, targets.honor, targets.bonus, targets.id, SUM(targets.target) as target, 
            //             targets.user_approve, targets.id_kantor, targets.status, 0 as pimpinan")
                            
            //             ->withRecursiveExpression('b', $query)
                            
            //             ->leftJoin('targets',function($join) use ($full, $request, $period){
            //                     $join->on('targets.id_jenis', 't.id_program')
            //                          ->where('targets.jenis_target', 'prog')
            //                          ->whereRaw("$full AND $period")
            //                          ->where(function ($query) use ($request) {
            //                                 $query->where('targets.id_kantor', $request->units);
            //                             // if ($ganjen != '') {
            //                             // } else if($lev == 'kacab') {
            //                             //     if($k != null){
            //                             //         $query->where('users.id_kantor', $kota)->orWhere('users.id_kantor', $kunit);
            //                             //     }else{
            //                             //         $query->where('users.id_kantor', $kota);
            //                             //     }
            //                             // }
            //                         });
            //                     })
                            
            //             ->groupBy('root')
            //             ->get();
            // }
            
            if($request->jenis == 'id_kan'){
                $data   = Kantor::selectRaw("'kan' as jenisnyaa ,tambahan.id as idnya, tambahan.unit as jenisnya, targets.*, 0 as pimpinan")
                                ->leftJoin('targets',function($join) use ($w_tgl, $w_prd){
                                            $join->on('targets.id_jenis', 'tambahan.id')
                                                ->whereRaw("$w_tgl AND $w_prd")
                                                ->where('targets.jenis_target', 'kan');
                                            })
                                ->where(function ($query) use ($kntr, $lev) {
                                    if($lev != 'admin' || $lev != 'keuangan pusat') {
                                        $query->where('tambahan.id', $kntr)->orWhere('tambahan.kantor_induk', $kntr);
                                    }
                                })
                                ->where('tambahan.id_com', $id_com)
                                ->groupBy('idnya')
                                ->get();
            }else if($request->jenis == 'id_kar'){
                $data   = User::selectRaw("'kar' as jenisnyaa, users.id_karyawan as idnya, users.name as jenisnya, targets.*, tambahan.id_pimpinan as pimpinan")
                                ->leftJoin('targets',function($join) use ($w_tgl, $w_prd){
                                            $join->on('targets.id_jenis', 'users.id_karyawan')
                                                ->whereRaw("$w_tgl AND $w_prd")
                                                ->where('targets.jenis_target', 'kar');
                                            })
                                ->join('tambahan','tambahan.id','=','users.id_kantor')
                                ->whereRaw("users.id_com = '$id_com' AND id_karyawan IS NOT NULL AND aktif = '1' AND status_kerja != 'Magang'")
                                ->where(function ($query) use ($kntr, $lev, $r_unit) {
                                    if($r_unit == ''){
                                        if($lev != 'admin' || $lev != 'keuangan pusat') {
                                            $query->where('users.id_kantor', $kntr)->orWhere('users.kantor_induk', $kntr);
                                        }
                                    }else{
                                        $query->where('users.id_kantor', $r_unit);
                                    }
                                })
                                ->groupBy('idnya')
                                ->get();
            }else if($request->jenis == 'prog'){
                
                
                if(Auth::user()->name == 'Management'){
                    $query = DB::table('prog as t1')
                            ->selectRaw("t1.*, t1.id_program as root")
                            
                            ->unionAll(
                                DB::table('b as t0')
                                    ->selectRaw("t3.*, t0.root")
                                    ->join('prog as t3', 't3.id_program_parent', '=', 't0.id_program')
                                        
                            );
                
                
                    // $data = DB::table('b as t')
                    //   ->selectRaw("
                    //         'prog' as jenisnyaa, 
                    //         t.parent, 
                    //         t.id_program as idnya, 
                    //         t.program as jenisnya, 
                    //         targets.alasan_tolak, 
                    //         targets.tanggal, 
                    //         targets.kunjungan, 
                    //         targets.transaksi,  
                    //         targets.minimal, 
                    //         targets.honor, 
                    //         targets.bonus, 
                    //         targets.id, 
                    //         SUM(targets.target) as target, 
                    //         targets.user_approve, 
                    //         targets.id_kantor, 
                    //         targets.status, 
                    //         0 as pimpinan
                    //     ")
                            
                    //     ->withRecursiveExpression('b', $query)
                            
                    //     ->leftJoin('targets', function($join) use ($w_tgl, $w_prd, $r_unit, $lev, $kntr) {
                    //         $join->on('targets.id_jenis', '=', 't.id_program')
                    //             ->where('targets.jenis_target', 'prog')
                    //             ->whereRaw("targets.tanggal = '2025-01-01' AND periode = 'bulan'")
                    //             ->where(function ($que) use ($r_unit, $lev, $kntr) {
                                                        
                    //                                         $que->where('targets.id_kantor', $r_unit);
                    //                                 });
                    //     })
                            
                        // ->groupBy('t.id_program', 't.program', 't.parent', 'targets.alasan_tolak', 'targets.tanggal', 'targets.kunjungan', 'targets.transaksi', 'targets.minimal', 'targets.honor', 'targets.bonus', 'targets.id', 'targets.user_approve', 'targets.id_kantor', 'targets.status')
                        // ->get();
                        
                        
                    // $data = DB::table('b as t')
                    //     ->selectRaw("
                    //         'prog' as jenisnyaa, 
                    //         t.parent, 
                    //         t.id_program as idnya, 
                    //         t.program as jenisnya,
                    //         SUM(targets.target) as target 
                    //     ")
                            
                    //     ->withRecursiveExpression('b', $query)
                            
                    //     ->leftJoin('targets', function($join) use ($w_tgl, $w_prd, $r_unit, $lev, $kntr) {
                    //         $join->on('t.id_program', '=', 'targets.id_jenis')
                    //             ->where('targets.jenis_target', 'prog')
                    //             ->whereRaw("targets.tanggal = '2025-01-01' AND periode = 'bulan'")
                    //             ->where(function ($que) use ($r_unit, $lev, $kntr) {
                                                        
                    //                                         $que->where('targets.id_kantor', $r_unit);
                    //                                 });
                    //     })
                            
                    //     ->groupBy('root')
                    //     ->get();
                    
                    $data = DB::table('b as t')
                                ->selectRaw("
                                    root,
                                    'prog' as jenisnyaa, 
                                    t.parent, 
                                    t.id_program as idnya, 
                                    t.program as jenisnya
                                ")
                                ->withRecursiveExpression('b', $query)
                                ->groupBy('root')
                                ->get();
                }else{
                    $query  = DB::table('prog as t1')
                                ->selectRaw("t1.*, t1.id_program as root")
                                
                                ->unionAll(
                                    DB::table('b as t0')
                                        ->selectRaw("t3.*, t0.root")
                                        ->join('prog as t3', 't3.id_program_parent', '=', 't0.id_program')
                                            
                                );
                    $data   = DB::table('b as t')
                                    ->selectRaw("'prog' as jenisnyaa, t.parent, t.id_program as idnya, t.program as jenisnya, targets.alasan_tolak, targets.tanggal, 
                                                targets.kunjungan, targets.transaksi,  targets.minimal, targets.honor, targets.bonus, targets.id, SUM(targets.target) as target, 
                                                targets.user_approve, targets.id_kantor, targets.status, 0 as pimpinan")
                                        
                                    ->withRecursiveExpression('b', $query)
                                        
                                    ->leftJoin('targets',function($join) use ($w_tgl, $w_prd, $r_unit, $lev, $kntr){
                                                $join->on('targets.id_jenis', 't.id_program')
                                                    ->whereRaw("$w_tgl AND $w_prd")
                                                    ->where('targets.jenis_target', 'prog')
                                                    ->where(function ($que) use ($r_unit, $lev, $kntr) {
                                                        if($r_unit == ''){
                                                            if($lev != 'admin' || $lev != 'keuangan pusat') {
                                                                $liskan = Kantor::where('kantor_induk', $kntr)->pluck('id');
                                                                $que->where('targets.id_kantor', $kntr)->orWhereIn('targets.id_kantor', $liskan);
                                                            }
                                                        }else{
                                                            $que->where('targets.id_kantor', $r_unit);
                                                        }
                                                    });
                                                })
                                    ->groupBy('root')
                                    ->get();
                }
                                
            }
            
            $dayaa = [];
            
            
                    
            // $data = DB::table('targets')->whereRaw("tanggal = '2025-01-01' AND periode = 'bulan' AND id_kantor = 1 AND jenis_target = 'prog'")->get();
            
            if(Auth::user()->name == 'Management'){
                foreach($data as $dd){
                    
                    $sas = DB::table('targets')
                        ->where('id_jenis', $dd->idnya)
                        ->whereRaw("$w_tgl AND $w_prd")
                        ->where('targets.jenis_target', 'prog')
                        ->when($r_unit == '', function ($que) use ($lev, $kntr) {
                            if ($lev != 'admin' && $lev != 'keuangan pusat') {
                                $liskan = Kantor::where('kantor_induk', $kntr)->pluck('id');
                                $que->where('targets.id_kantor', $kntr)
                                    ->orWhereIn('targets.id_kantor', $liskan);
                            }
                        }, function ($que) use ($r_unit) {
                            $que->where('targets.id_kantor', $r_unit);
                        })
                        ->first();
                
                    // Nilai default target adalah target program itu sendiri jika ada
                    $total_target = $sas->target ?? 0;
                    
                    if ($dd->parent == 'y') {
                
                        // Jika program adalah parent, akumulasi target dari semua anak dan sub-anaknya
                        $total_target = DB::table('targets')
                            ->join('prog as p', 'targets.id_jenis', '=', 'p.id_program')
                            
                            ->whereRaw("$w_tgl AND $w_prd")
                            ->where('targets.jenis_target', 'prog')
                            ->when($r_unit == '', function ($que) use ($lev, $kntr) {
                                if ($lev != 'admin' && $lev != 'keuangan pusat') {
                                    $liskan = Kantor::where('kantor_induk', $kntr)->pluck('id');
                                    $que->where('targets.id_kantor', $kntr)
                                        ->orWhereIn('targets.id_kantor', $liskan);
                                }
                            }, function ($que) use ($r_unit) {
                                $que->where('targets.id_kantor', $r_unit);
                            })
                        ->sum('targets.target');
                    
                    }
                    
                    $dayaa[] = [
                        'root' => $dd->root,
                        'jenisnyaa' => $dd->jenisnyaa,
                        'parent' => $dd->parent,
                        'idnya' => $dd->idnya,
                        'target'    => $total_target,
                        'jenisnya'  => $dd->jenisnya,
                        // 'alasan_tolak'  => $sas->alasan_ditolak ?? 0,
                        // 'tanggal'       => $sas->tanggal ?? 0,
                        // 'kunjungan'     => $sas->kunjungan ?? 0,
                        // 'transaksi'     => $sas->transaksi ?? 0,
                        // 'minimal'       => $sas->minimal ?? 0,
                        // 'honor'         => $sas->honor ?? 0,
                        // 'bonus'         => $sas->bonus ?? 0,
                        'id'            => $sas->id ?? 0,
                        // 'user_approve'  => $sas->user_approve ?? 0,
                        // 'id_kantor'     => $sas->id_kantor ?? 0,
                        // 'status'        => $sas->status ?? 0,
                        'pimpinan' => 0
                    ];
                    
                }
                
                return $dayaa;
            }
            
            
            // foreach($data as $do){
            //     $c = User::where('id',$do->user_approve);
            //     if(count($c->get()) > 0){
            //         $ap = $c->first()->name;
            //     }else{
            //         $ap = '';
            //     }
                
            //     if($request->jenis == 'prog'){
            //         $ppp = $do->parent;
            //     }else{
            //         $ppp = '';
            //     }
                
            //     $haha = $do->target == null ? 0 : $do->target;
                
            //     if($request->periode == 'bulan'){
            //         $hehe = $y ==  date('Y-m', strtotime($do->tanggal)) ? $do->status : 3;
            //     }else{
            //         $hehe = $yy == date('Y', strtotime($do->tanggal)) ? $do->status : 3;
            //     }
                
                
            //     if($request->periode == 'bulan'){
            //         $exey = $request->thn == date('Y-m', strtotime($do->tanggal)) ? date('Y-m', strtotime($do->tanggal)) : $y;
            //     }else{
            //         $exey = $request->tahun == date('Y', strtotime($do->tanggal)) ? date('Y', strtotime($do->tanggal)) : date('Y');
            //     }
                
            //     $datay[] = [
            //         'idnya' => $do->idnya .'_'. $y .'_'. $do->jenisnyaa,
            //         'id_jenis' => $do->jenisnya,
            //         'tahun' => $exey,
            //         'target' => $haha,
            //         'kunjungan' => $y ==  date('Y-m', strtotime($do->tanggal)) ? $do->kunjungan : 0,
            //         'transaksi' => $y ==  date('Y-m', strtotime($do->tanggal)) ? $do->transaksi : 0,
            //         'minimal' => $y ==  date('Y-m', strtotime($do->tanggal)) ? $do->minimal : 0,
            //         'honor' => $y ==  date('Y-m', strtotime($do->tanggal)) ? $do->honor : 0,
            //         'bonus' => $y ==  date('Y-m', strtotime($do->tanggal)) ? $do->bonus : 0,
            //         'stts' => $hehe,
            //         'id_targetnya' => $do->id != null ? $do->id : null,
            //         'alasan' => $do->alasan_tolak,
            //         'jenisnya' => $do->jenisnyaa,
            //         'user_approve' => $ap,
            //         'parent' => $ppp,
            //         'pimpinan' => $do->pimpinan,
            //         'id_spesial' =>  $do->idnya
            //     ];
            // }
            
            $tottar = 0;
            foreach($data as $do){
                $c = User::where('id',$do->user_approve);
                if(count($c->get()) > 0){
                    $ap = $c->first()->name;
                }else{
                    $ap = '';
                }
                
                if($request->jenis == 'prog'){
                    $ppp = $do->parent;
                }else{
                    $ppp = '';
                }
                
                $haha = $do->target == null ? 0 : $do->target;
                
                if($ppp != 'y'){
                    $tottar += $haha;
                }
                
                if($do->id > 0){
                    $datay[] = [
                        'idnya'         => $do->idnya .'_'. $y .'_'. $do->jenisnyaa,
                        'id_jenis'      => $do->jenisnya,
                        'tgl'         => $do->tanggal,
                        'target'        => $haha,
                        'id_spesial'   => $do->idnya,
                        'kunjungan'     => $do->kunjungan,
                        'transaksi'     => $do->transaksi,
                        'minimal'       => $do->minimal,
                        'honor'         => $do->honor,
                        'bonus'         => $do->bonus,
                        
                        'status'        => $do->status,
                        'id_targetnya'  => $do->id != null ? $do->id : 0,
                        'alasan'        => $do->alasan_tolak,
                        'jenisnya'      => $do->jenisnyaa,
                        // 'user_approve'  => $ap,
                        'parent'        => $ppp,
                        'pimpinan'      => $do->pimpinan,
                        // 'id_spesial'    =>  $do->idnya,
                        'kontot'        => 0,
                        'periode'       => $request->periode,
                    ];
                }else{
                    $datay[] = [
                        'idnya'      => $do->idnya .'_'. $y .'_'. $do->jenisnyaa,
                        'id_jenis'          => $do->jenisnya,
                        'tgl'           => $d_tgl,
                        'target'        => 0,
                        'id_spesial'    => $do->idnya,
                        'kunjungan'     => 0,
                        'transaksi'     => 0,
                        'minimal'       => 0,
                        'honor'         => 0,
                        'bonus'         => 0,
                        
                        'status'        => 2,
                        'id_targetnya'  => 0,
                        'alasan'        => null,
                        'jenisnya'      => $do->jenisnyaa,
                        'parent'        => $ppp,
                        'pimpinan'      => $do->pimpinan,
                        'kontot'        => 0,
                        'periode'       => $request->periode,
                    ];
                }
            }
            
            // $datay[count($data)] = [
            //         // 'idnya'         => 0,
            //         'id_jenis'      => 0,
            //         'nama'          => 'Total',
            //         'tgl'           => $d_tgl,
            //         'target'        => $tottar,
                    
            //         'kunjungan'     => $do->kunjungan,
            //         'transaksi'     => $do->transaksi,
            //         'minimal'       => $do->minimal,
            //         'honor'         => $do->honor,
            //         'bonus'         => $do->bonus,
                    
            //         'status'        => 1,
            //         'id_targetnya'  => 0,
            //         'alasan'        => '',
            //         'jenisnya'      => '',
            //         'user_approve'  => '',
            //         'parent'        => $ppp,
            //         'pimpinan'      => '',
            //         'id_spesial'    => '',
            //         'kontot'        => 1,
            //         'periode'       => $request->periode
            // ];
            
            if($request->tab == 'tab1'){
            
                $datat = [
                        // 'idnya'         => 0,
                    'idnya'      => 0,
                    'id_jenis'          => 'Total',
                    'tgl'           => $d_tgl,
                    'target'        => $tottar,
                    'id_spesial'    => 0,
                    'kunjungan'     => $do->kunjungan,
                    'transaksi'     => $do->transaksi,
                    'minimal'       => $do->minimal,
                    'honor'         => $do->honor,
                    'bonus'         => $do->bonus,
                        
                    'status'        => 1,
                    'id_targetnya'  => 0,
                    'alasan'        => '',
                    'jenisnya'      => '',
                    'user_approve'  => '',
                    'parent'        => $ppp,
                    'pimpinan'      => '',
                    'id_spesial'    => '',
                    'kontot'        => 1,
                    'periode'       => $request->periode
                ];
            
                return $datat;
            }
            
            return DataTables::of($datay)
            ->addIndexColumn()
            ->addColumn('status', function ($datay) {
                if(Auth::user()->kolekting == 'admin'){
                    $p = 'pointer';
                }else{
                    $p = 'auto';
                }
                
                if($datay['jenisnya'] == 'prog'){
                    if($datay['status'] == 2){
                        $btn = '<span class="badge badge-warning" style="cursor:  '.$p.'">Pending</span>';
                    }else if($datay['status'] == 0){
                        $btn = '<span class="badge badge-danger" style="cursor: pointer">Rejected</span>';
                    }else if($datay['status'] == 1){
                        $btn = '<span class="badge badge-success" style="cursor: pointer">Approved</span>';
                    }else{
                        if($datay['parent'] == 'y'){
                            $btn = '';
                        }else{
                            $btn = '<span class="badge badge-secondary" style="cursor: '.$p.'">Empty</span>';
                        }
                    }
                }else{
                    if($datay['status'] == 2){
                        $btn = '<span class="badge badge-warning" style="cursor:  '.$p.'">Pending</span>';
                    }else if($datay['status'] == 0){
                        $btn = '<span class="badge badge-danger" style="cursor: pointer">Rejected</span>';
                    }else if($datay['status'] == 1){
                        $btn = '<span class="badge badge-success" style="cursor: pointer">Approved</span>';
                    }else{
                        $btn = '<span class="badge badge-secondary" style="cursor: '.$p.'">Empty</span>';
                    }
                    
                }
                

                return $btn;
            })
            ->addColumn('jenish', function ($datay) {
                if($datay['jenisnya'] == 'prog'){
                    if($datay['parent'] == 'y'){
                        $ahha = '<b>'.$datay['id_jenis'].'</b>';
                    }else{
                        $ahha = $datay['id_jenis'];
                    }
                }else{
                    $ahha =  $datay['id_jenis'];
                }
                return $ahha;
            })
            
            ->addColumn('tahun', function ($datay) {
                if($datay['periode'] == 'tahun'){
                    $ahha = date('Y', strtotime($datay['tgl']));
                }else{
                    $ahha = date('Y-m', strtotime($datay['tgl']));
                }
                return $ahha;
            })
            
            ->rawColumns(['status','jenish','tahun'])
            ->make(true);
        }
        
        return view('setting.target', compact('kota'));
    }
    
    public function getProgSer(Request $request){
        // return $request;
        $bulan =  $request->datay['tahun'];
        $kita = $request->datay['id_spesial'];
        $karyawan = $request->datay['id_spesial'];
        
        // return $kita;
        
        $data['tk'] = Targets::selectRaw("kantor.unit, targets.*")->join('tambahan as kantor','kantor.id','=','targets.id_jenis')->whereRaw("targets.id_kantor = '$request->unit' AND jenis_target = 'kan' AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'")->get();
        $data['to'] = Targets::whereRaw("targets.id_jenis = '$kita' AND jenis_target = 'kar' AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'")->first();
        if($request->toggle == 'true'){
            $data['prog'] = Targets::selectRaw("prog.program, prog.id_program, targets.*")->join('prog','prog.id_program','=','targets.id_jenis')->whereRaw("targets.id_kantor = '$request->unit' AND jenis_target = 'prog' AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'")->orderBy('prog.id_program','asc')->get();
        }else{
            $datass = Prog::whereRaw("parent = 'n'")->orderBy('prog.id_program','asc')->get();
            $daaa = [];
            foreach($datass as $dy){
                $g = $dy->id_program;
                $ehe = Targets::select('target','id_jenis')
                        ->whereRaw("targets.id_kantor = '$request->unit' AND jenis_target = 'prog' AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan' AND id_jenis ='$g' ")
                        ->orderBy('id_jenis', 'asc')
                        ->first();
                        
                $daaa[] = [
                    'target' => $ehe == null ? 0 : $ehe->target ,
                    'program' => $dy->program,
                    'id_jenis' => $dy->id_jenis,
                    'id_program' => $dy->id_program,
                ];
            }
            
            $data['prog'] = $daaa;
            
        }
        
        $cek = $data['prog'];
        
        $data['sistar'] = [];
        
        
        for($u = 0; $u < count($cek); $u++ ){
            $pp =  $cek[$u]['id_jenis'];
            // $sistar = Targets::selectRaw("prog.program, progperus.*, targets.target as targetnya")->leftJoin('prog','prog.id_program','=','targets.id_jenis')->leftjoin('progperus','progperus.id_program','=','targets.id_jenis')->whereRaw("targets.id_kantor = '$request->unit' AND jenis_target = 'prog' AND id_kar = '$karyawan' AND DATE_FORMAT(targets.tanggal, '%Y-%m') = '$bulan'")->orderBy('prog.id_program','asc')->first();
            $sistar = ProgPerus::whereRaw("id_program = '$pp' AND id_kar = '$kita' AND id_kantor = '$request->unit' AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'")->first();
            $sistarget = ProgPerus::selectRaw("SUM(target) as jumlah")->whereRaw("id_program = '$pp' AND id_kantor = '$request->unit' AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'")->get();
                
            if($sistar == null){
                $closing = 0;
                $penawaran = 0;
                $followup = 0;
            }else{
                $closing = $sistar->closing == '' ? 0 : $sistar->closing;
                $followup = $sistar->followup == '' ? 0 : $sistar->followup;
                $penawaran = $sistar->penawaran == '' ? 0 : $sistar->penawaran;
            }
            
            $data['sistar'][] = [
                'id' => $sistar == null ? '' : $sistar->id,
                'id_program' => $sistar == null ? '' : $sistar->id_program,
                'sisa_target' => $cek[$u]['target'] - $sistarget[0]->jumlah ,
                'target' => $sistar == null ? '': $sistar->target,
                'closing' => $closing,
                'followup' => $followup,
                'penawaran' => $penawaran,
                
            ]; 
        }
        
        return $data;
    }
    
    public function postProgSer(Request $request){
        
        // return $request->ngeri;
        
        foreach($request->ngeri as $val){
            $id_target = $val['id_target'];
            $id_kantor = $val['kantor'];
            $id_kar = $val['kar'];
            $sisa_target = $val['sisa'];
            $target = $val['value'];
            $tanggal = $val['tgl'];
            $id_program = $val['program'];
            $idnya = $val['idnya'];
            
            $penawaran = $val['penawaran'];
            $closing = $val['closing'];
            $followup = $val['follow'];
            // return $idnya;
            
            $aw = $tanggal.'-01';
            
            $targett = ProgPerus::whereRaw("date(tanggal) = '$aw' AND id_kantor = '$id_kantor' AND id_program = '$id_program' AND id_kar = '$id_kar'")->first();
            $sisa_targett = ProgPerus::selectRaw("SUM(sisa_target) as sistar")->whereRaw("date(tanggal) = '$aw' AND id_kantor = '$id_kantor' AND id_program = '$id_program'")->pluck('sistar')->toArray();
            // return $sisa_targett;
            if($targett != null){
                
                
                // if($targett->target != $target){
                    $input['id_kantor'] = $id_kantor;
                    $input['id_target'] = $id_target;
                    // $input['target'] =  $targett != null ? $targett->target + $target : $target;
                    $input['target'] = $target;
                    $input['tanggal'] = $tanggal.'-01';
                    
                    // if(count($sisa_targett) > 0){
                    //     $input['sisa_target']  = $sisa_targett[0] - $sisa_target;
                    // }else{
                    $input['sisa_target']  = $sisa_target;
                    // }
                    
                    $input['penawaran'] = $penawaran;
                    $input['followup'] = $followup;
                    $input['closing'] = $closing;
                    $input['id_kar'] = $id_kar;
                    $input['id_program'] = $id_program;
                    $input['user_insert'] = Auth::user()->id; 
                    ProgPerus::where('id', $targett->id)->update($input);
                // }
            }else{
                if($target != null){
                    $input['id_kantor'] = $id_kantor;
                    $input['id_target'] = $id_target;
                    $input['target'] = $target;
                    $input['tanggal'] = $tanggal.'-01';
                    $input['sisa_target']  = $sisa_target;
                    $input['id_kar'] = $id_kar;
                    $input['penawaran'] = $penawaran;
                    $input['followup'] = $followup;
                    $input['closing'] = $closing;
                    $input['id_program'] = $id_program;
                    $input['user_insert'] = Auth::user()->id; 
                    ProgPerus::create($input);
                }
            }
            
            // if($target != null){
                
                
            //     // salah kondisi
                
                
                
            //     // return $target;
                
            //     $aw = $tanggal.'-01';
                
            //     $targett = ProgPerus::whereRaw("date(tanggal) = '$aw' AND id_kantor = '$id_kantor' AND id_program = '$id_program' AND id_kar = '$id_kar'")->first();
            //     $sisa_targett = ProgPerus::whereRaw("date(tanggal) = '$aw' AND id_kantor = '$id_kantor' AND id_program = '$id_program'")->first();
                
            //     // $targett
                
            //     $input['id_kantor'] = $id_kantor;
            //     $input['id_target'] = $id_target;
                
            //     $input['tanggal'] = $tanggal.'-01';
                
            //     $input['id_kar'] = $id_kar;
            //     $input['id_program'] = $id_program;
            //     $input['user_insert'] = Auth::user()->id;  
                
            //     if($targett != null){
            //         $input['target'] = $targett != null ? $targett->target + $target : $target; 
            //         $input['sisa_target'] = $sisa_target;
            //         ProgPerus::where('id', $targett->id)->update($input);
            //     }else if($sisa_targett != null){
            //         $input['target'] = $target; 
            //         $input['sisa_target'] =  $sisa_targett != null ? $sisa_targett->sisa_target - $sisa_target : $sisa_target;
            //         ProgPerus::where('id', $sisa_targett->id)->update($input);
            //     }else{
            //         $input['target'] = $target; 
            //         $input['sisa_target'] =  $sisa_target;
            //         ProgPerus::create($input);
            //     }
                
                
                
                
            //     // $input['id_kantor'] = $id_kantor;
            //     // $input['id_target'] = $id_target;
            //     // $input['target'] = $crit != null ? $crit->target + $target : $target; 
            //     // $input['tanggal'] = $tanggal.'-01';
            //     // $input['sisa_target'] =  $coba != null ? $coba->sisa_target - $sisa_target : $sisa_target;
            //     // $input['id_kar'] = $id_kar;
            //     // $input['id_program'] = $id_program;
            //     // $input['user_insert'] = Auth::user()->id;    
            //     // // ProgPerus::create($input);
            //     // // return($nput);
            //     // if($crit != null) {
            //     //     ProgPerus::where('id', $crit->id)->create($input);
            //     //     // return($input);
            //     // }else if($coba != null){
            //     //     ProgPerus::where('id', $coba->id)->update($input);
            //     //     // return($input);
            //     // }else{
            //     //     ProgPerus::create($input);
            //     //     // return($input);
            //     // }
            // }
            
            
        }
        
        return response()->json(['success' => 'Data Added successfully.', 'response'=> 1]);
        
    }
    
    public function update_target(Request $request){
        // return $request;
        $unlock = preg_split("/_/",$request->idnya);
        
        $id = $unlock[0];
        $tgl = $unlock[1];
        $jenis = $unlock[2];
        
        $format = $tgl.'-01';
        
        $bulan = Carbon::createFromFormat('Y-m', $tgl)->format('m');
        $tahun = Carbon::createFromFormat('Y-m', $tgl)->format('Y');
        
        $target = $request->target == null ? 0 : preg_replace("/[^0-9]/", "",$request->target);
        $kunjungan = $request->kunjungan == null ? 0 : preg_replace("/[^0-9]/", "",$request->kunjungan);
        $transaksi = $request->transaksi == null ? 0 : preg_replace("/[^0-9]/", "",$request->transaksi);
        
        $minimal = $request->minimal == null ? 0 : preg_replace("/[^0-9]/", "",$request->minimal);
        $honor = $request->honor == null ? 0 : preg_replace("/[^0-9]/", "",$request->honor);
        $bonus = $request->bonus == null ? 0 : preg_replace("/[^0-9]/", "",$request->bonus);
        
        if($jenis == 'kar'){
            $xx = User::selectRaw("id_kantor")->whereRaw("id_karyawan = '$id'")->first()->id_kantor;
        }else if($jenis == 'kan'){
            $xx = Kantor::selectRaw("id")->whereRaw("id = '$id'")->first()->id;
        }else if($jenis == 'prog'){
            $xx = $request->unit;
        }
        
        if($jenis == 'kar'){
            $u = User::where('id_karyawan', $id)->first();
            
            $ah = Kantor::where('id', $u->id_kantor)->first()->id_pimpinan;
            
            $pimpinan = Karyawan::where('id_karyawan', $ah)->first();
            
            $tk = Targets::whereRaw("id_jenis = '$u->id_kantor' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND periode = 'bulan' ")->first()->target;
            
            $tt = Targets::selectRaw("SUM(target) as trgt")->whereRaw("jenis_target = 'kar' AND periode = 'bulan' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id_kantor = '$u->id_kantor' AND id_jenis != '$ah' ")->get();
            
            $ktl = Targets::whereRaw("id_jenis = '$id' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND periode ='bulan'");
            
            $tahunan = Targets::whereRaw("id_jenis = '$u->id_kantor' AND id_kantor = '$u->id_kantor' AND YEAR(tanggal) = '$tahun' AND periode ='tahun'")->first();
            
            if(count($ktl->get()) > 0){
                $tu = $ktl->first()->target;
            }else{
                $tu = 0;
            }
            
            $st = ($tk - $tt[0]->trgt) + $tu;
            
            // return([$tt[0]->trgt, $tk, $tu, $target]);
            
            if($tahunan == null){
                return response()->json(['failed' => 'fail', 'response' => 100, 'sisanya => 0']);
            }else if($target > $st){
                return response()->json(['failed' => 'fail', 'response' => 0, 'sisanya => 0']);
            }else{
                if($u->id_kantor == $pimpinan->id_kantor){
                    $nyari = Targets::whereRaw("id_jenis = '$id' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND periode = 'bulan'");
                    if(count($nyari->get()) > 0){
                        $nyari->update(['target' => $target, 'user_update' =>Auth::user()->id, 'status' => 2]);
                        
                        //perhitungan pimpinan
                        $tt = Targets::selectRaw("SUM(target) as trgt")->whereRaw("jenis_target = 'kar' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id_kantor = '$u->id_kantor' AND id_jenis != '$ah' AND periode = 'bulan'")->get();
                        $ts = $tk - $tt[0]->trgt;
                        
                        $pimpinan = Targets::whereRaw("id_jenis = '$ah' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND periode = 'bulan'");
                        
                        // return($ts);
                        
                        if(count($pimpinan->get()) > 0){
                            $pimpinan->update(['target' => $ts, 'user_update' =>Auth::user()->id, 'status' => 2]);
                        }else{
                            if($request->target != null){
                                $ipt_pimpinan['target'] = $ts;
                                $ipt_pimpinan['kunjungan'] = 0;
                                $ipt_pimpinan['transaksi'] = 0;
                                $ipt_pimpinan['minimal'] = 0;
                                $ipt_pimpinan['honor'] = 0;
                                $ipt_pimpinan['bonus'] = 0;
                            }else if($request->kunjungan != null){
                                $ipt_pimpinan['target'] = 0;
                                $ipt_pimpinan['kunjungan'] = $kunjungan;
                                $ipt_pimpinan['transaksi'] = 0;
                                $ipt_pimpinan['honor'] = 0;
                                $ipt_pimpinan['bonus'] = 0;
                            }else if($request->transaksi != null){
                                $input['target'] = 0;
                                $input['kunjungan'] = 0;
                                $input['transaksi'] = $transaksi;
                                $input['honor'] = 0;
                                $input['bonus'] = 0;
                            }else if($request->minimal != null){
                                $ipt_pimpinan['target'] = 0;
                                $ipt_pimpinan['kunjungan'] = 0;
                                $ipt_pimpinan['transaksi'] = 0;
                                $ipt_pimpinan['minimal'] = $minimal;
                                $ipt_pimpinan['honor'] = 0;
                                $ipt_pimpinan['bonus'] = 0;
                            }else if($request->honor != null){
                                $ipt_pimpinan['target'] = 0;
                                $ipt_pimpinan['kunjungan'] = 0;
                                $ipt_pimpinan['transaksi'] = 0;
                                $ipt_pimpinan['minimal'] = 0;
                                $ipt_pimpinan['honor'] = $honor;
                                $ipt_pimpinan['bonus'] = 0;
                            }else if($request->bonus != null){
                                $ipt_pimpinan['target'] = 0;
                                $ipt_pimpinan['kunjungan'] = 0;
                                $ipt_pimpinan['transaksi'] = 0;
                                $ipt_pimpinan['minimal'] = 0;
                                $ipt_pimpinan['honor'] = 0;
                                $ipt_pimpinan['bonus'] = $bonus;
                            }
                            $ipt_pimpinan['tanggal'] = $format;
                            $ipt_pimpinan['id_jenis'] = $ah;
                            $ipt_pimpinan['periode'] = 'bulan';
                            $ipt_pimpinan['jenis_target'] = $jenis;
                            $ipt_pimpinan['status'] = Auth::user()->kolekting == 'admin' ? 1 : 2;
                            $ipt_pimpinan['id_kantor'] = $xx;
                            $ipt_pimpinan['user_insert'] = Auth::user()->id;
                            $ipt_pimpinan['user_approve'] = Auth::user()->kolekting == 'admin' ? Auth::user()->id : null;
                            $ipt_pimpinan['user_update'] = null;
                            
                            Targets::create($ipt_pimpinan);
                        }
                        
                    }else{
                        if($request->target != null){
                            $input['target'] = $target;
                            $input['kunjungan'] = 0;
                            $input['transaksi'] = 0;
                            $input['minimal'] = 0;
                            $input['honor'] = 0;
                            $input['bonus'] = 0;
                        }else if($request->kunjungan != null){
                            $input['target'] = 0;
                            $input['kunjungan'] = $kunjungan;
                            $input['transaksi'] = 0;
                            $input['honor'] = 0;
                            $input['bonus'] = 0;
                        }else if($request->transaksi != null){
                            $input['target'] = 0;
                            $input['kunjungan'] = 0;
                            $input['transaksi'] = $transaksi;
                            $input['honor'] = 0;
                            $input['bonus'] = 0;
                        }else if($request->minimal != null){
                            $input['target'] = 0;
                            $input['kunjungan'] = 0;
                            $input['transaksi'] = 0;
                            $input['minimal'] = $minimal;
                            $input['honor'] = 0;
                            $input['bonus'] = 0;
                        }else if($request->honor != null){
                            $input['target'] = 0;
                            $input['kunjungan'] = 0;
                            $input['transaksi'] = 0;
                            $input['minimal'] = 0;
                            $input['honor'] = $honor;
                            $input['bonus'] = 0;
                        }else if($request->bonus != null){
                            $input['target'] = 0;
                            $input['kunjungan'] = 0;
                            $input['transaksi'] = 0;
                            $input['minimal'] = 0;
                            $input['honor'] = 0;
                            $input['bonus'] = $bonus;
                        }
                        
                        $input['tanggal'] = $format;
                        $input['id_jenis'] = $id;
                        $input['periode'] = 'bulan';
                        $input['jenis_target'] = $jenis;
                        $input['status'] = Auth::user()->kolekting == 'admin' ? 1 : 2;
                        $input['id_kantor'] = $xx;
                        $input['user_insert'] = Auth::user()->id;
                        $input['user_approve'] = Auth::user()->kolekting == 'admin' ? Auth::user()->id : null;
                        $input['user_update'] = null;
                        Targets::create($input);
                        
                        //perhitungan pimpinan
                        $tt = Targets::selectRaw("SUM(target) as trgt")->whereRaw("jenis_target = 'kar' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id_kantor = '$u->id_kantor' AND id_jenis != '$ah' AND periode = 'bulan'")->get();
                        $ts = $tk - $tt[0]->trgt;
                        
                        $pimpinan = Targets::whereRaw("id_jenis = '$ah' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND periode = 'bulan'");
                        
                        if(count($pimpinan->get()) > 0){
                            $pimpinan->update(['target' => $ts, 'user_update' =>Auth::user()->id, 'status' => 2]);
                        }else{
                            if($request->target != null){
                                $ipt_pimpinan['target'] = $ts;
                                $ipt_pimpinan['kunjungan'] = 0;
                                $ipt_pimpinan['transaksi'] = 0;
                                $ipt_pimpinan['minimal'] = 0;
                                $ipt_pimpinan['honor'] = 0;
                                $ipt_pimpinan['bonus'] = 0;
                            }else if($request->kunjungan != null){
                                $ipt_pimpinan['target'] = 0;
                                $ipt_pimpinan['kunjungan'] = $kunjungan;
                                $ipt_pimpinan['transaksi'] = 0;
                                $ipt_pimpinan['honor'] = 0;
                                $ipt_pimpinan['bonus'] = 0;
                            }else if($request->transaksi != null){
                                $input['target'] = 0;
                                $input['kunjungan'] = 0;
                                $input['transaksi'] = $transaksi;
                                $input['honor'] = 0;
                                $input['bonus'] = 0;
                            }else if($request->minimal != null){
                                $ipt_pimpinan['target'] = 0;
                                $ipt_pimpinan['kunjungan'] = 0;
                                $ipt_pimpinan['transaksi'] = 0;
                                $ipt_pimpinan['minimal'] = $minimal;
                                $ipt_pimpinan['honor'] = 0;
                                $ipt_pimpinan['bonus'] = 0;
                            }else if($request->honor != null){
                                $ipt_pimpinan['target'] = 0;
                                $ipt_pimpinan['kunjungan'] = 0;
                                $ipt_pimpinan['transaksi'] = 0;
                                $ipt_pimpinan['minimal'] = 0;
                                $ipt_pimpinan['honor'] = $honor;
                                $ipt_pimpinan['bonus'] = 0;
                            }else if($request->bonus != null){
                                $ipt_pimpinan['target'] = 0;
                                $ipt_pimpinan['kunjungan'] = 0;
                                $ipt_pimpinan['transaksi'] = 0;
                                $ipt_pimpinan['minimal'] = 0;
                                $ipt_pimpinan['honor'] = 0;
                                $ipt_pimpinan['bonus'] = $bonus;
                            }
                            $ipt_pimpinan['tanggal'] = $format;
                            $ipt_pimpinan['id_jenis'] = $ah;
                            $ipt_pimpinan['periode'] = 'bulan';
                            $ipt_pimpinan['jenis_target'] = $jenis;
                            $ipt_pimpinan['status'] = Auth::user()->kolekting == 'admin' ? 1 : 2;
                            $ipt_pimpinan['id_kantor'] = $xx;
                            $ipt_pimpinan['user_insert'] = Auth::user()->id;
                            $ipt_pimpinan['user_approve'] = Auth::user()->kolekting == 'admin' ? Auth::user()->id : null;
                            $ipt_pimpinan['user_update'] = null;
                            
                            Targets::create($ipt_pimpinan);
                        }
                    }
                    
                }else{
                    $nyari = Targets::whereRaw("id_jenis = '$id' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND periode = 'bulan'");
                    
                    if(count($nyari->get()) > 0){
                        $nyari->upate(['target' => $target, 'user_update' =>Auth::user()->id, 'status' => 2]);
                    }else{
                        if($request->target != null){
                            $input['target'] = $target;
                            $input['kunjungan'] = 0;
                            $input['transaksi'] = 0;
                            $input['minimal'] = 0;
                            $input['honor'] = 0;
                            $input['bonus'] = 0;
                        }else if($request->kunjungan != null){
                            $input['target'] = 0;
                            $input['kunjungan'] = $kunjungan;
                            $input['transaksi'] = 0;
                            $input['honor'] = 0;
                            $input['bonus'] = 0;
                        }else if($request->transaksi != null){
                            $input['target'] = 0;
                            $input['kunjungan'] = 0;
                            $input['transaksi'] = $transaksi;
                            $input['honor'] = 0;
                            $input['bonus'] = 0;
                        }else if($request->minimal != null){
                            $input['target'] = 0;
                            $input['kunjungan'] = 0;
                            $input['transaksi'] = 0;
                            $input['minimal'] = $minimal;
                            $input['honor'] = 0;
                            $input['bonus'] = 0;
                        }else if($request->honor != null){
                            $input['target'] = 0;
                            $input['kunjungan'] = 0;
                            $input['transaksi'] = 0;
                            $input['minimal'] = 0;
                            $input['honor'] = $honor;
                            $input['bonus'] = 0;
                        }else if($request->bonus != null){
                            $input['target'] = 0;
                            $input['kunjungan'] = 0;
                            $input['transaksi'] = 0;
                            $input['minimal'] = 0;
                            $input['honor'] = 0;
                            $input['bonus'] = $bonus;
                        }
                        
                        $input['tanggal'] = $format;
                        $input['id_jenis'] = $id;
                        $input['periode'] = 'bulan';
                        $input['jenis_target'] = $jenis;
                        $input['status'] = Auth::user()->kolekting == 'admin' ? 1 : 2;
                        $input['id_kantor'] = $xx;
                        $input['user_insert'] = Auth::user()->id;
                        $input['user_approve'] = Auth::user()->kolekting == 'admin' ? Auth::user()->id : null;
                        $input['user_update'] = null;
                        Targets::create($input);
                    }
                }
                
                return response()->json(['success' => 'Data Added successfully.', 'response'=> 1]);
            }
        }elseif($jenis == 'kan'){
            $unitz = $id;
            $tahunan = Targets::whereRaw("id_jenis = '$unitz' AND id_kantor = '$unitz' AND YEAR(tanggal) = '$tahun' AND periode ='tahun'")->first();
            $tk = Targets::whereRaw("id_jenis = '$unitz' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND periode = 'bulan' ")->first();
            $tk = $tk != NULL ? $tk->target : 0;
            
            $tt = Targets::selectRaw("SUM(target) as trgt")->whereRaw("jenis_target = 'kan' AND periode = 'bulan' AND YEAR(tanggal) = '$tahun' AND id_jenis = '$unitz' AND id_kantor = '$unitz'")->get();
            $tt = count($tt) > 0 ? $tt[0]->trgt : 0;
            
            $kondisi = $tahunan->target - $tt + $tk;
            
            // $jember =  $kondisi - $target;
            
            // return [$kondisi, $target];
            
            if($tahunan == null){
                return response()->json(['failed' => 'fail', 'response' => 100, 'sisanya' => 0]);
            }else if($target < $kondisi){
                return response()->json(['failed' => 'fail', 'response' => 11, 'sisanya => 0']);
            }else if($target > $kondisi){
                return response()->json(['failed' => 'fail', 'response' => 0, 'sisanya => 0']);
            // }else{
            }else{
                $same = Targets::whereRaw("id_jenis = '$id' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND periode='bulan' AND jenis_target = '$jenis'");
                
                if(count($same->get()) > 0){
                    $data = $same->first();
                    $y =  $data->status == 0 ? 2 : $data->status;
                    
                    Targets::where('id', $data->id)->update(['target' => $target, 'user_update' =>Auth::user()->id, 'status' => $y]);
                    
                }else{
                    $xx = Kantor::selectRaw("id")->whereRaw("id = '$id'")->first()->id;
                    
                    if($request->target != null){
                        $input['target'] = $target;
                        $input['kunjungan'] = 0;
                        $input['transaksi'] = 0;
                        $input['minimal'] = 0;
                        $input['honor'] = 0;
                        $input['bonus'] = 0;
                    }
                    
                    $input['tanggal'] = $format;
                    $input['id_jenis'] = $id;
                    $input['jenis_target'] = $jenis;
                    $input['status'] = Auth::user()->kolekting == 'admin' ? 1 : 2;
                    $input['id_kantor'] = $xx;
                    $input['periode'] = 'bulan';
                    $input['user_insert'] = Auth::user()->id;
                    $input['user_approve'] = Auth::user()->kolekting == 'admin' ? Auth::user()->id : null;
                    $input['user_update'] = null;
                    Targets::create($input);
                    
                    // return('2');
                }
                
                return response()->json(['success' => 'Data Added successfully.', 'response'=> 1, 'sisanya' => 0 ]);
            }
            
            // return $tt;
        }else{
            
            
        
            // if(Auth::user()->name == 'Management'){
            //     return $xx;
            // }
            
            $unitz = $id;
            
            $tk = Targets::whereRaw("id_jenis = '$xx' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND periode = 'bulan' ")->first();
            $tk = $tk != NULL ? $tk->target : 0;
            
            $tt = Targets::selectRaw("SUM(target) as trgt")->whereRaw("jenis_target = 'prog' AND periode = 'bulan' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id_kantor = '$xx'")->get();
            $tt = count($tt) > 0 ? $tt[0]->trgt : 0;
            
            $ktl = Targets::whereRaw("id_jenis = '$id' AND jenis_target = '$jenis' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND periode ='bulan'")->first();
            $tu = $ktl != NULL ? $ktl->target : 0;
            
            $tahunan = Targets::whereRaw("id_jenis = '$xx' AND id_kantor = '$xx' AND YEAR(tanggal) = '$tahun' AND periode = 'tahun'")->first();
            
            
            $st = ($tk - $tt) + $tu;
            
            
            if($tahunan == null){
                return response()->json(['failed' => 'fail', 'response' => 100, 'sisanya => 0']);
            }else if($target > $st){
                return response()->json(['failed' => 'fail', 'response' => 0, 'sisanya => 0']);
            }else{
            
                $same = Targets::whereRaw("id_jenis = '$id' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND periode='bulan' AND jenis_target = '$jenis'");
                
                if(count($same->get()) > 0){
                    $data = $same->first();
                    $y =  $data->status == 0 ? 2 : $data->status;
                    
                    Targets::where('id', $data->id)->update(['target' => $target, 'user_update' =>Auth::user()->id, 'status' => $y]);
                    
                }else{
                    if($jenis == 'kan'){
                        $xx = Kantor::selectRaw("id")->whereRaw("id = '$id'")->first()->id;
                    }else if($jenis == 'prog'){
                        $xx = $request->unit;
                    }
                    
                    if($request->target != null){
                        $input['target'] = $target;
                        $input['kunjungan'] = 0;
                        $input['transaksi'] = 0;
                        $input['minimal'] = 0;
                        $input['honor'] = 0;
                        $input['bonus'] = 0;
                    }
                    
                    $input['tanggal'] = $format;
                    $input['id_jenis'] = $id;
                    $input['jenis_target'] = $jenis;
                    $input['status'] = Auth::user()->kolekting == 'admin' ? 1 : 2;
                    $input['id_kantor'] = $xx;
                    $input['periode'] = 'bulan';
                    $input['user_insert'] = Auth::user()->id;
                    $input['user_approve'] = Auth::user()->kolekting == 'admin' ? Auth::user()->id : null;
                    $input['user_update'] = null;
                    Targets::create($input);
                }
                
                return response()->json(['success' => 'Data Added successfully.', 'response'=> 1, 'sisanya' => 0 ]);
            }
        }
        
    }
    
    public function setTahunan(Request $request){
        $tahun = $request->tahun == '' ? date('Y') : $request->tahun;
        
        if($request->jenis == 'kan'){
            $cari = Targets::whereRaw("YEAR(tanggal) = '$tahun' AND periode = 'tahun' AND id_kantor = '$request->unit'");
            if(count($cari->get()) > 0){
                $cari->update(['target' => preg_replace("/[^0-9]/", "",$request->target)]);
            }else{
                
                $unlock = preg_split("/_/",$request->unit);
                
                $input= [
                    'target' =>  preg_replace("/[^0-9]/", "",$request->target),
                    'periode' => 'tahun',
                    'id_jenis'=> $unlock[0],
                    'id_kantor'=> $unlock[0],
                    'tanggal' => $tahun.'-01-01',
                    'status' => 2,
                    'jenis_target' => 'kan',
                    'user_insert' => Auth::user()->id
                ];
                Targets::create($input);
            }
        }
        // return($input);
        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    public function setTargetNew (Request $request){
        
        // return($request);
        
        $unlock = preg_split("/_/",$request->unit);
        $unit = $unlock[0];
        
        $tanggalo = $request->target_bulanan;
        $wait = [];
        $tahun = $request->tahun == '' ? date('Y') : $request->tahun;
        for($t = 0; $t < count($tanggalo) ; $t++){
            
            $formattedNumber = sprintf('%02d', $t+1);
            
            $tar = Targets::whereDate('tanggal', $tahun.'-'.$formattedNumber.'-01')
                    ->where('periode','bulan')
                    ->where('id_jenis', $unit)
                    ->where('jenis_target', 'kan')
                    ->where('id_kantor', $unit)
                    ->first();
            
            if($tar != NULL){
                $ta = Targets::whereDate('tanggal', $tahun.'-'.$formattedNumber.'-01')
                    ->where('periode','bulan')
                    ->where('id_jenis', $unit)
                    ->where('jenis_target', 'kan')
                    ->where('id_kantor', $unit)
                    ->update([
                            'target'        => $tanggalo[$t],
                            'user_update'   => Auth::user()->id
                            ]);
            }else{
            
            // if(count($tar->get()) == 0){
                // $wait[] = [
                //     'target' =>  $tanggalo[$t],
                //     'periode' => 'bulan',
                //     'id_jenis'=> $request->unit,
                //     'id_kantor'=> $request->unit,
                //     'tanggal' => $tahun.'-'.$formattedNumber.'-01',
                //     'status' => 2,
                //     'jenis_target' => 'kan',
                //      => Auth::user()->id
                // ];
                
                $wait['target'] = $tanggalo[$t];
                $wait['periode'] = 'bulan';
                $wait['id_jenis'] =  $unit;
                $wait['id_kantor'] =  $unit;
                $wait['tanggal'] =   $tahun.'-'.$formattedNumber.'-01';
                $wait['status'] =  2;
                $wait['jenis_target'] = 'kan';
                $wait['user_insert'] =  Auth::user()->id;
            // }
            
            Targets::create($wait); 
            }
        }
        
        
        return response()->json(['success' => 'Data Added successfully.']);
        // return $wait;
    }
    
    public function getTargetPertahun (Request $request){
        
        $unlock = preg_split("/_/",$request->unit);
        $id = $unlock[0];
        $tgl = $unlock[1];
        $jenis = $unlock[2];
        
        $tahun = $request->tahun == '' ? date('Y') : $request->tahun;
        $find = Targets::selectRaw("targets.*, MONTH(targets.tanggal) AS month_number")->whereRaw("YEAR(tanggal) = '$tahun' AND periode = 'bulan' AND id_kantor = '$id' AND jenis_target = 'kan'")->get();
        $parah = [];
        $nya = [];
        $koe = [];
        
        
        foreach ($find as $target) {
            $bln    = date('n', strtotime($target->tanggal));
            // $b      = $bln;
            $bulan  = $bln < 10 ? '0'.$bln : $bln;
            
            $date = new DateTime($target->tanggal);
            $month = $date->format('n'); 
            $m = (int) $month;
            
            $nya[$bln -1] = $target->target;
        }
        
        for ($i = 0; $i < 12; $i++){
            if(isset($nya[$i])){
                $parah[$i] = $nya[$i];
            }else{
                $parah[$i] = 0;
            }
        }
        
        return $parah;
        
        
        // if(count($find) > 0){
            
        //     for ($i = 0; $i < 12; $i++){
                
                
        //         // if(isset($find[$i])){
        //         if($i == $find[$i]->number_month ){
        //             // $date = new DateTime($find[$i]->tanggal);
        //             // $month = $date->format('n');
                    
        //             // $date = new DateTime($find[$i]->tanggal);
        //             // $month = $date->format('n'); 
        //             // $m = (int) $month;
                    
        //             // if($m != $i){
        //             //     $parah[$i] = 0;
        //             // }else{
        //                 $parah[$i] = $find[$i]->target;
        //             // }
        //             // return $find[$i];
                    
        //             // // if(isset($month)){
        //             //     if((int) $month == [$i]){
        //             //     }else{
        //             //         $parah[$i] = 0;
                            
        //             //     }
        //             // }
        //             // else{
        //             //     $parah[$i] = 0;
        //             // }
        //         }else{
        //             $parah[$i] = 0;
        //         }
        //     }
        // }else{
        //     for ($i = 0; $i < 12; $i++){
        //         $parah[$i] = 0;
        //     }
        // }
        // return $parah;
    }
    
    public function acc_target(Request $request){
        $tolak = $request->acc == 0 ? $request->alasan : null;
        Targets::where('id', $request->id)->update(['status' => $request->acc, 'alasan_tolak' => $tolak, 'user_update' => Auth::user()->id, 'user_approve' =>Auth::user()->id]);
        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    public function saldo_dana(Request $request){
        if(Auth::user()->pengaturan == 'admin'){
            
        
        if($request->ajax()){
            $data = COA::selectRaw("coa.coa, coa.nama_coa, coa_expend, coa_receipt, coa.level, saldo_dana.operasi, saldo_dana.id")
            ->where('grup', 'like', '%6%')->leftJoin('saldo_dana', 'saldo_dana.coa_dana', '=', 'coa.coa')->orderBy('coa', 'ASC')->get();
          
       
    //   return($data);
       
        // foreach($data as $xx){
        //     $cr = unserialize($xx->coa_receipt);
        //     $ca = unserialize($xx->coa_expend);
            
            
        //      $sip[] = [ 
        //         'nama_coa' => ' '.$xx->nama_coa, 
        //         'coa' => $xx->coa,
        //         'ca' => $ca,
        //         // coa 400
        //         'cr' => $cr 
        //         // coa 500
        //     ]; 
        // }
        
        // $cc = implode(" ", ($sip[2]['ca']));
        // $dikit = (string)$cc;
        // $canya = preg_split("/\./", $dikit);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('coa_ex', function ($data) {
                    if($data->coa_expend != null){
                        $button = unserialize($data->coa_expend);
                    }else if($data->coa == '302.01.000.000'){
                   
            $datas = COA::selectRaw("coa.coa, coa.nama_coa, coa_expend, coa_receipt, coa.level, saldo_dana.operasi, saldo_dana.id")
            ->where('grup', 'like', '%6%')->leftJoin('saldo_dana', 'saldo_dana.coa_dana', '=', 'coa.coa')->orderBy('coa', 'ASC')->get();
          
          
            foreach($datas as $xx){
            $cr = unserialize($xx->coa_receipt);
            $ca = unserialize($xx->coa_expend);
             $sip[] = [ 
                'nama_coa' => ' '.$xx->nama_coa, 
                'coa' => $xx->coa,
                'ca' => $ca,
            ]; 
        }
        $cc = implode(" ", ($sip[2]['ca']));
        $dikit = (string)$cc;
        $cenya = preg_split("/\./", $dikit);
         $datass = COA::selectRaw("coa")->whereRaw("coa like '%$cenya[0].01.002%' OR coa = '$cc' ")->get();
                    for($i=0; $i < count($datass); $i++){
                        $t[$i] = $datass[$i]->coa;
                        }
                        
                $button = $t ;
                
                
                    }else if($data->coa == '302.02.000.000'){
            $datas = COA::selectRaw("coa.coa, coa.nama_coa, coa_expend, coa_receipt, coa.level, saldo_dana.operasi, saldo_dana.id")
            ->where('grup', 'like', '%6%')->leftJoin('saldo_dana', 'saldo_dana.coa_dana', '=', 'coa.coa')->orderBy('coa', 'ASC')->get();
            
            foreach($datas as $xx){
            $cr = unserialize($xx->coa_receipt);
            $ca = unserialize($xx->coa_expend);
             $sip[] = [ 
                'nama_coa' => ' '.$xx->nama_coa, 
                'coa' => $xx->coa,
                'ca' => $ca,
            ]; 
        }
            $cc = implode(" ", ($sip[2]['ca']));
            $dikit = (string)$cc;
            $cenya = preg_split("/\./", $dikit);
            $datass = COA::selectRaw("coa")->whereRaw("coa like '$cenya[0].01.001.000%' OR coa like '$cenya[0].03.000.000%'  
            OR coa like '%$cenya[0].04.000.000%' OR coa like '%$cenya[0].05.000.000%' ")->get();
                for($i=0; $i < count($datass); $i++){
                    $t[$i] = $datass[$i]->coa;
                        }
                    $button = $t ;
                    }
                    return $button;
                })
                ->addColumn('coa_re', function ($data) {
            if($data->coa_receipt != null){
                $button = unserialize($data->coa_receipt);
            }else if($data->coa == '302.01.000.000'){
                $datas = COA::selectRaw("coa.coa, coa.nama_coa, coa_expend, coa_receipt, coa.level, saldo_dana.operasi, saldo_dana.id")
                ->where('grup', 'like', '%6%')->leftJoin('saldo_dana', 'saldo_dana.coa_dana', '=', 'coa.coa')->orderBy('coa', 'ASC')->get();
                foreach($datas as $xx){
                $cr = unserialize($xx->coa_receipt);
                $ca = unserialize($xx->coa_expend);
                    $sip[] = [ 
                        'nama_coa' => ' '.$xx->nama_coa, 
                        'coa' => $xx->coa,
                        'cr' => $cr 
                        ]; 
                }
        
                    $pok = implode(" ", ($sip[2]['cr']));
                    $dikitaja = (string)$pok;
                    $crnya = preg_split("/\./", $dikitaja);
                    
                    $datass = COA::selectRaw("coa")->whereRaw("coa like '$crnya[0].01.000.000' ")->get();
                    for($i=0; $i < count($datass); $i++){
                        $t[$i] = $datass[$i]->coa;
                            }
                        $button = $t ;
                    
                
                }else if($data->coa == '302.02.000.000'){
                    $datas = COA::selectRaw("coa.coa, coa.nama_coa, coa_expend, coa_receipt, coa.level, saldo_dana.operasi, saldo_dana.id")
                        ->where('grup', 'like', '%6%')->leftJoin('saldo_dana', 'saldo_dana.coa_dana', '=', 'coa.coa')->orderBy('coa', 'ASC')->get();
                        foreach($datas as $xx){
                        $cr = unserialize($xx->coa_receipt);
                        $ca = unserialize($xx->coa_expend);
                            $sip[] = [ 
                                'nama_coa' => ' '.$xx->nama_coa, 
                                'coa' => $xx->coa,
                                'cr' => $cr 
                                ]; 
                }
        
                    $pok = implode(" ", ($sip[2]['cr']));
                    $dikitaja = (string)$pok;
                    $crnya = preg_split("/\./", $dikitaja);
                    
                    $datass = COA::selectRaw("coa")->whereRaw("coa like '%$crnya[0].02.000.000%' OR coa like '%$crnya[0].03.000.000%' 
                    OR coa like '%$crnya[0].03.000.000%' OR coa like '%$crnya[0].99.000.000%'")->get();
                    for($i=0; $i < count($datass); $i++){
                        $t[$i] = $datass[$i]->coa;
                            }
                        $button = $t ;
                // $button = 'meme*';
                    }
                    return $button;
                })
                ->make(true);
        }
        
        return view('setting.saldo_dana');
        }
    }
    
    public function post_saldo_dana(Request $request){
        // $ce = explode("," ,$request->de);
        // $cr = explode("," ,$request->dr);
        
        
        $ce = $request->de;
        $cr = $request->dr;
        
        $ingin = SaldoDana::find($request->id_hide);
        
        if($ingin == null){
            $input = [
                'coa_expend' => serialize($ce),
                'coa_receipt' => serialize($cr),
                'coa_dana' => $request->sd,
                'operasi' => $request->op,
            ];
            
            SaldoDana::create($input);
            
        }else{
            $input = [
                'coa_expend' => serialize($ce),
                'coa_receipt' => serialize($cr),
                'coa_dana' => $request->sd,
                'operasi' => $request->op,
            ];
            
            $ingin->update($input);
        }
        
        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    public function getPenerimaanSD_lama(Request $request){
        $coa_parent= COA::whereRaw("grup LIKE '%1%' AND parent = 'y'")->orderBy('coa', 'ASC')->get();
        
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa
            ];
        }
        return response()->json($h1);
    }
    
        public function getPenerimaanSD(Request $request){
        $coa_parent= COA::whereRaw("grup LIKE '%1%'  ")->orderBy('coa', 'ASC')->get();
        
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa
            ];
        }
        return response()->json($h1);
    }
    
    public function getPengeluaranSD(Request $request){
        $coa_parent= COA::whereRaw("grup LIKE '%2%' ")->orderBy('coa', 'ASC')->get();
        
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->parent.'- '.$val->nama_coa,
                "coa" => $val->coa,
                "id" => $val->coa,
                "parent" => $val->parent,
                "nama_coa" => $val->nama_coa
            ];
        }
        return response()->json($h1);
    }
    
    public function getKolektor(Request $request){
        $kota = $request->kota;
        $kolektor = Tunjangan::first()->kolektor;
        $so = Tunjangan::first()->so;
        
        $data = User::select('id','name')->whereRaw("id_kantor = '$kota' AND aktif = '1' AND (id_jabatan = '$kolektor' OR id_jabatan = '$so')")->get();
        return $data;
    }
    
    public function getTargetKantor(Request $request){
        $lev    = Auth::user()->pengaturan;
        $r_unit = $request->unit2;
        $kntr   = Auth::user()->id_kantor;
        $data = [];
        $coba = Kantor::select('id')->where(function ($query) use ($kntr, $lev, $r_unit) {
                    if($r_unit == ''){
                        if($lev != 'admin' || $lev != 'keuangan pusat') {
                            $query->where('id', $kntr)->orWhere('kantor_induk', $kntr);
                        }
                    }else{
                        $query->where('id', $r_unit);
                    }
                })
                ->pluck('id')->toArray();
                
        $id_jenis = $request->unit2 == '' ? "id_jenis IN ('" . implode("', '", $coba) . "')" : "id_jenis = '$r_unit'";
        $id_kantor = $request->unit2 == '' ? "targets.id_kantor IN ('" . implode("', '", $coba) . "')" : "targets.id_kantor = '$r_unit'";
        
        if($request->jenis == 'id_kar'){
            
            
            
            $y = $request->thn == '' ? date('Y-m') : $request->thn;
            $ganjen2 = $request->unit2 == '' ? Auth::user()->id_kantor : $request->unit2 ;
            $bulan = Carbon::createFromFormat('Y-m', $y)->format('m');
            $tahun = Carbon::createFromFormat('Y-m', $y)->format('Y');
            $full = "MONTH(targets.tanggal) = '$bulan' AND YEAR(targets.tanggal) = '$tahun'";
            
            $tk = Targets::select('target')
                ->whereRaw("jenis_target = 'kan' AND $full AND periode = 'bulan' AND $id_jenis")
                ->get();    
            
            $tt = Targets::selectRaw(" SUM(target) as trgt")
                ->join('karyawan','targets.id_jenis','=','karyawan.id_karyawan')
                ->whereRaw("jenis_target = 'kar' AND $full AND $id_kantor AND karyawan.aktif = 1 AND periode = 'bulan'")
                ->get();
            
            $data['tk'] = count($tk) == 0 ? 0 : array_sum($tk->pluck('target')->toArray());
            $data['tt'] = count($tt) == 0 ? 0 : $tt[0]->trgt;
            
            $kntr = Kantor::where('id', $ganjen2)->where('level','!=','kcp');
            
            if(count($kntr->get()) > 0){
                $pew = $kntr->first()->id_pimpinan;
                $targetpim = Targets::whereRaw("id_jenis = '$pew' AND $full");
                
                if(count($targetpim->get()) > 0){
                    $data['tp'] = $targetpim->first()->target;
                }else{
                    $data['tp'] = 0;
                }
            }else{
                $data['tp'] = 0;
            }
        }else if($request->jenis == 'prog'){
            $y = $request->thn == '' ? date('Y-m') : $request->thn;
            $unit = $request->unit2 == '' ? Auth::user()->id_kantor : $request->unit2;
            $bulan = Carbon::createFromFormat('Y-m', $y)->format('m');
            $tahun = Carbon::createFromFormat('Y-m', $y)->format('Y');
            $full = "MONTH(targets.tanggal) = '$bulan' AND  YEAR(targets.tanggal) = '$tahun'";
            
            $tk = Targets::select('target')->whereRaw("jenis_target = 'kan' AND $full AND $id_jenis AND periode = 'bulan'")
                
                ->get();
            $tt = Targets::selectRaw(" SUM(target) as trgt")->join('prog','targets.id_jenis','=','prog.id_program')
                ->whereRaw("jenis_target = 'prog' AND $full AND $id_kantor AND periode = 'bulan'")
                ->get();
        
            $data['tk'] = count($tk) == 0 ? 0 : array_sum($tk->pluck('target')->toArray());
            $data['tt'] = count($tt) == 0 ? 0 : $tt[0]->trgt;
            $data['tp'] = $data['tk'] - $data['tt'];
        }
        $ve = $data['tk'];
        $ze = $data['tt'];                
        $ye = $data['tp'];
        
        // // $ppp = $ze - $ye;  
        $p = $ve -$ze;
        
        $data['target_kantor'] = $ve;
        $data['target_terpakai'] = $ze;
        $data['target_sisa'] = $ve - $ze;
        
        return $data;
    }
    
    public function setTargetPimpinan(Request $request){
        $ganjen2 = $request->unit2 == '' ? Auth::user()->id_kantor : $request->unit2 ;
        $y = $request->thn == '' ? date('Y-m') : $request->thn;
        
        $bulan = Carbon::createFromFormat('Y-m', $y)->format('m');
        $tahun = Carbon::createFromFormat('Y-m', $y)->format('Y');
        
        $kntr = Kantor::where('id', $ganjen2)->first()->id_pimpinan;
        
        $full = "MONTH(targets.tanggal) = '$bulan' AND  YEAR(targets.tanggal) = '$tahun'";
        $targetpim = Targets::whereRaw("id_jenis = '$kntr' AND $full");
        
        
        // if(count($targetpim->get()) > 0){
        //     // $targetpim->update([
        //     //     'target' =>$request->sisa
        //     // ]);
        //     $yaa = $targetpim->first();
        // }
        
        if($request->tab == 'tab1'){
            $coba = Kantor::where('id', $ganjen2);
            if(count($coba->get())){
                $ehe = $coba->first()->id_pimpinan;
                $user = Karyawan::where('id_karyawan', $ehe)->first();
                if($user->id_kantor == $ganjen2){
                    $sip = Targets::whereRaw("id_jenis = '$user->id_karyawan' AND $full");
                    if(count($sip->get()) > 0){
                        $sip->update(['target' => $request->sisa]);
                    }else{
                        $input = [
                            'target' => $request->sisa,
                            'id_jenis' => $user->id_karyawan,
                            'tanggal' => date('Y-m-01'),
                            'status' => 2,
                            'id_kantor' => $user->id_kantor,
                            'jenis_target' => 'kar',
                            'user_insert' => Auth::user()->id,
                        ];
                        
                        Targets::create($input);
                    }
                                // ->update(['target' =>$request->sisa]);
                }else{
                    $ya = Targets::whereRaw("$full AND id_kantor = '$ganjen2'")->get();
                    $itung = $request->sisa/count($ya);
                    $yass = [];
                    foreach($ya as $yes){
                        $yass[] = [
                            'id_jenis' => $yes->id_jenis,
                            'target' => $itung
                        ];
                    }
                    
                    Targets::whereRaw("$full AND id_kantor = '$ganjen2'")->update($yass);
                    
                }
                
                return response()->json(['success' => 'Data Added successfully.']);
            }
        }
        
        if($request->tab == 'tab2'){
            
            if(count($targetpim->get()) > 0){
                $targetpim->update([
                    'target' =>$request->sisa,
                    'user_update' => Auth::user()->id
                ]);
            }
            return response()->json(['success' => 'Data Added successfully.']);
        }
        
        if($request->tab == 'tab3'){
            
            return response()->json(['success' => 'Data Added successfully.']);
        }
        
        // return response()->json(['success' => 'Data Added successfully.']);
        
    }
    
      public function bukti_setor(Request $request)
    {

            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
            $petugas = User::where('aktif', 1)->where('id_com', Auth::user()->id_com)->get();
            $sumber = SumberDana::get();
            $progbsz = ProgBSZ::where('aktif','1')->get();

            
        if($request->ajax()){
        $jenis = $request->jenis == '' ? "id_sumber_dana IS NOT NULL" : "id_sumber_dana = '$request->jenis'";

            
            $data = Prog::whereRaw("$jenis")->orderByDesc('id_program')->get();
            
            return DataTables::of($data)
            ->addIndexColumn()
            
            ->addColumn('bsz', function($data){
              
                 $p = ProgBSZ::select('nama')->where('id', $data->id_bsz)->first();
                if ($p) {
                     return $p->nama;
                } else {
                     return '';
                }
              
            })
            
              ->addColumn('aksi', function ($data) {

                    $button = '<div class="btn-group"><a class="btn btn-rounded btn-danger btn-sm  hapuspasang hapus" aksi="hapus" id="' . $data->id_program . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top"   title="Klik Hapus Data Program Bukti Setor Zakat" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Hapus Program Bukti Setor Zakat"><i class="fa fa-trash"></i></a>';
                    return $button;
            })
            ->rawColumns(['bsz','aksi'])
            ->make(true);
        }
        return view('setting.bukti_setor', compact('kantor','petugas','sumber','progbsz')); 
    }
    
        function post_bsz(Request $request){
        $data = new ProgBSZ;
        $data->nama = $request->nama;
        $data->keterangan = $request->keterangan;
        $data->aktif = $request->status;
        $data->save(); 
        return response()->json(['success' => 'Data Added successfully.']);
    }
    
          public function listbsz(Request $request)
    {

        if($request->ajax()){
          
            $data = ProgBSZ::get();
            
            return DataTables::of($data)
              ->addIndexColumn()
             ->addColumn('st', function ($data) {
                $aktif = $data->aktif == 1 ? 'Aktif' : 'Tidak Aktif';
                    return $aktif;
                })
              ->rawColumns(['st'])
            ->make(true);
        }
       
    }

        public function bszBy(Request $request, $id){
        $data['ui'] = ProgBSZ::whereRaw("id = '$id'")->first();
        return $data;
    }
    
        public function edbsz_stat(Request $request){
            ProgBSZ::where('id', $request->id)->update([
                'nama' =>$request->nama,
                'keterangan' =>$request->keterangan,
                'aktif' =>$request->status,
            ]);

         return response()->json(['success' => 'Data Added successfully.']);
    }
    
            public function progBy(Request $request, $id){
        $data['ui'] = Prog::whereRaw("id_program = '$id'")->first();
        return $data;
    }
    
    
    public function bsz_pasang(Request $request){
        if($request->aksi == 'pasangkan'){
            Prog::whereIn('id_program', $request->id)->update([
                'id_bsz' =>$request->idbsz,
                // 'id_karyawan' =>$request->petugas,
            ]);
            Tunjangan::where('id_tj', '1')->update([
                'diterima_bsz' =>$request->petugas,
            ]);  
        }else if($request->aksi == 'hapus'){
            Prog::where('id_program', $request->id)->update([
                'id_bsz' =>null,
                // 'id_karyawan' =>null,
            ]); 
        }

         return response()->json(['success' => 'Data Added successfully.']);
    }
    
    public function setwarning(Request $request){
        $data = Tunjangan::select('id_tj','mindon','jumbul')->first();

        return $data;
    }
    
    public function updatewarning(Request $request){
        Tunjangan::where('id_tj', $request->id_tj)->update([
                'mindon' => $request->mindon,
                'jumbul' => $request->jumbul,
            ]);  

        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    public function cekdataa(Request $request){
        $data1 = UserSSO::where('email', '!=', null)->get(); // Fetch the existing records
        $data2 = $request['data'];
        
        // Extract emails from $data1
        $emailsData1 = array_map('strtolower', $data1->pluck('email')->toArray());
        
        // // Filter $data2 to include only data with emails not in $emailsData1
        // $filteredData2 = array_filter($data2, function ($item) use ($emailsData1) {
        //     return !in_array($item['username'], $emailsData1);
        // });
        
        // // Convert filtered data into Eloquent model instances
        // $filteredData2Collection = collect($filteredData2)->map(function ($item) {
        //     return new UserSSO([
        //         'email' => $item['username'], // Map 'username' to 'email' field of UserSSO
        //         // Add other fields as needed
        //     ]);
        // });
        
        // // Merge $data1 with $filteredData2Collection
        // $result = $data1->merge($filteredData2Collection);
        
        // return $result;
        
        $filteredData2 = array_filter($data2, function ($item) use ($emailsData1) {
            return !in_array(strtolower($item['username']), $emailsData1);
        });
        
        if(count($filteredData2) > 0){
            foreach($filteredData2 as $ya){
                $input = [
                    'nama' => strstr($ya['username'], '@', true),
                    'email' => $ya['username'],
                    'password' => bcrypt(12345678),
                    'token' => Str::random(60)
                ];
                        
                UserSSO::create($input);
            }
            
            return response()->json([
                'success' => 'Data Added successfully.',
                'data' => count($filteredData2)
            ]);
        }
        
        return response()->json([
            'failed' => 'Data Kosong.',
            'data' => $filteredData2
        ]);
        
    }
    
}