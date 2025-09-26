<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kolektors;
use App\Models\Kinerja;
use App\Models\User;
use App\Models\Jalur;
use App\Models\Kantor;
use App\Models\Donatur;
use App\Models\Tunjangan;
use Auth;
use DB;

use DataTables;

class AssigmentController extends Controller
{
    public function asgs()
    {
        $kerja = Donatur::where('petugas',Auth::user()->name)->get();
        return view ('assigment.index', compact('kerja'));
    }


    public function allasis()
    {
        $kerja = Kinerja::orderBy('updated_at','asc')->get();
        return view ('allasigment.index', compact('kerja'));
    }
    
    public function assigmentadmin(Request $request)
    {
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kot = Auth::user()->id_kantor;
            
        if($request->ajax()){
            $assigment = Donatur::selectRaw("donatur.*, donatur.created_at as tggl")
                        ->where(function($q) use ($request, $kot, $k) {
                            if(Auth::user()->level == 'kacab'){
                                if($request->kota != ''){
                                    $q->where('id_kantor', $request->kota);
                                }else{
                                    if($k == null){
                                        $q->where('id_kantor', $kot);
                                    }else{
                                        $q->whereRaw("id_kantor = '$kot' OR id_kantor = '$k->id'");
                                    }
                                }
                            }else if(Auth::user()->level == 'admin'){
                                if($request->kota != ''){
                                    $q->where('id_kantor', $request->kota);
                                }else{
                                    $q->whereRaw("id_kantor IS NOT NULL");
                                }
                            }
                        })
                        ->where(function($q) use ($request) {
                            if($request->jalurah != ''){
                                $q->where('id_jalur', $request->jalurah);
                            }
                        })
                        
                        ->where(function($q) use ($request) {
                            if($request->stts != ''){
                                $q->where('donatur.status', $request->stts);
                            }
                        })
                        ->where(function($q) use ($request) {
                            if($request->pembayaran != ''){
                                $q->where('pembayaran', $request->pembayaran);
                            }else{
                                $q->where('pembayaran', '!=', '-');
                            }
                        })
                        ->where(function($q) use ($request) {
                            if($request->petugas != ''){
                                $q->where('id_koleks', $request->petugas);
                            }
                        })
                        
                        ->where(function($q) use ($request) {
                            if($request->warnings != ''){
                                $q->where('warning', $request->warnings);
                            }
                        })
                        
                        ->whereIn('donatur.id', function($query){
                                    $query->select('id_don')->from('prosp')->where('ket', 'closing');
                                })
                        ->where('donatur.status', '!=', 'Off')->where('donatur.status', '!=', 'Ditarik');
                        
            
            if($request->tab == 'tab1'){
                
                
                if(Auth::user()->level == 'admin'){
                    $unt = isset($request->unt) ? "id_kantor IN ('" . implode("', '", $request->unt) . "')" : "id_kantor != 'dfdf'" ;
                    $ja = \DB::select("SELECT jalur, kota,
                     COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS totkun,
                     COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS tottup
                     FROM donatur WHERE DATE_FORMAT(created_at, '%Y-%m') <> DATE_FORMAT(CURDATE(), '%Y-%m') AND id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83)) AND $unt
                     GROUP BY kota, jalur HAVING COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 
                     OR COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 ORDER BY totkun DESC");
                }else if(Auth::user()->level == 'kacab'){
                    if($k == null){
                        $unt = isset($request->unt) ? "id_kantor IN ('" . implode("', '", $request->unt) . "')" : "id_kantor = '$kot'" ;
                        $ja = \DB::select("SELECT jalur, id_kantor, kota, 
                         COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS totkun,
                         COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS tottup
                         FROM donatur WHERE DATE_FORMAT(created_at, '%Y-%m') <> DATE_FORMAT(CURDATE(), '%Y-%m') AND $unt AND id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))
                         GROUP BY id_kantor, kota, jalur HAVING COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 
                         OR COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 ORDER BY totkun DESC");
                    }else{
                        $unt = isset($request->unt) ? "id_kantor IN ('" . implode("', '", $request->unt) . "')" : "(id_kantor = '$kot' OR id_kantor = '$k->id')" ;
                        $ja = \DB::select("SELECT jalur, id_kantor, kota, 
                         COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS totkun,
                         COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS tottup
                         FROM donatur WHERE DATE_FORMAT(created_at, '%Y-%m') <> DATE_FORMAT(CURDATE(), '%Y-%m') AND $unt AND id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))
                         GROUP BY id_kantor, kota, jalur HAVING COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 
                         OR COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 ORDER BY totkun DESC");
                    }
                }
                
                return $ja;
            }
            
            if(isset($request->program)){
                            // $data->where(function($query) use ($request) {
                $assigment->join('prosp', function($join) use ($request) {
                    $join->on('prosp.id_don' ,'=', 'donatur.id')
                        ->select('prosp.id_prog')
                        ->where('prosp.id_prog', $request->program);
                });
            }
                        
            
            return DataTables::of($assigment)
            ->addIndexColumn()
            ->addColumn('statushm', function($assigment){
                if($assigment->status == 'Donasi'){
                    $button = '<span class="badge light badge-success"><i class="fa fa-circle text-success me-1"></i>'.$assigment->status.'</span>';
                }elseif($assigment->status == 'Tidak Donasi'){
                    $button = '<span class="badge light badge-warning"><i class="fa fa-circle text-warning me-1"></i>'.$assigment->status.'</span>';
                }elseif($assigment->status == 'Tutup'){
                    $button = '<span class="badge light badge-danger"><i class="fa fa-circle text-danger me-1"></i>'.$assigment->status.'</span>';
                }elseif($assigment->status == 'Tutup 2x'){
                    $button = '<span class="badge light badge-success"><i class="fa fa-circle text-success me-1"></i>'.$assigment->status.'</span>';
                }elseif($assigment->status == 'Ditarik'){
                    $button = '<span class="badge light badge-danger"><i class="fa fa-circle text-danger me-1"></i>'.$assigment->status.'</span>';
                }elseif($assigment->status == 'Off'){
                    $button = '<span class="badge light badge-danger"><i class="fa fa-circle text-danger me-1"></i>'.$assigment->status.'</span>';
                }elseif($assigment->status == 'Kotak Hilang'){
                    $button = '<span class="badge light badge-danger"><i class="fa fa-circle text-danger me-1"></i>'.$assigment->status.'</span>';
                }elseif($assigment->status == 'belum dikunjungi'){
                    $button = '<span class="badge light badge-primary"><i class="fa fa-circle text-primary me-1"></i>'.$assigment->status.'</span>';
                }else{
                    $button = '<span class="badge light badge-secondary"><i class="fa fa-circle text-secondary me-1"></i>'.$assigment->status.'</span>';
                }
                return $button;
            })
            ->editColumn('action', function($assigment){
                if($assigment->acc == 1){
                    $c = 'checked';
                }else{
                    $c = '';
                } 
                        // $button = '<input type="checkbox" id="toggle-two"  class="toggle-class " data-id="'. $kerja->id .'" data-toggle="toggle" data-style="slow" data-on="Onsite" data-off="Off"  >';
                $button = '<label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'))" id="checkbox" class="toggle-class"  data-id="'. $assigment->id . '"  data-value="'. $assigment->acc . '" type="checkbox" '.($assigment->acc == true ? "checked" : "").' /> <div class="slider round"> </div> </label>';
                return $button;
            })
            ->addColumn('tglll', function($assigment){
                $button = date('Y-m-d', strtotime($assigment->tggl));
                return $button;
            })
            ->rawColumns(['statushm','action','tglll'])
            ->make(true);
        }
        
        $pemb = Donatur::select('pembayaran')->whereRaw("(pembayaran = 'transfer' OR pembayaran = 'dijemput')")->distinct()->get();
        $stat = Donatur::select('status')->whereRaw("status != 'Off' AND status != 'Ditarik'")->distinct()->get();
        // return($pemb);
        $sinkron = \DB::select("SELECT * from sinkron where id = '1' ");
        
        if(Auth::user()->level == 'admin'){
            $belumass = \DB::select("SELECT jalur, kota,
             COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS totkun,
             COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS tottup
             FROM donatur WHERE DATE_FORMAT(created_at, '%Y-%m') <> DATE_FORMAT(CURDATE(), '%Y-%m') AND id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))
             GROUP BY kota, jalur HAVING COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 
             OR COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 ORDER BY totkun DESC");
        }else if(Auth::user()->level == 'kacab'){
            if($k == null){
                $belumass = \DB::select("SELECT jalur, id_kantor, kota, 
                 COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS totkun,
                 COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS tottup
                 FROM donatur WHERE DATE_FORMAT(created_at, '%Y-%m') <> DATE_FORMAT(CURDATE(), '%Y-%m') AND id_kantor = '$kot' AND id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))
                 GROUP BY id_kantor, kota, jalur HAVING COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 
                 OR COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 ORDER BY totkun DESC");
            }else{
                $belumass = \DB::select("SELECT jalur, id_kantor, kota, 
                 COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS totkun,
                 COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS tottup
                 FROM donatur WHERE DATE_FORMAT(created_at, '%Y-%m') <> DATE_FORMAT(CURDATE(), '%Y-%m') AND (id_kantor = '$kot' OR id_kantor = '$k->id') AND id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))
                 GROUP BY id_kantor, kota, jalur HAVING COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 
                 OR COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) > 0 ORDER BY totkun DESC");
            }
        }
         
        if(Auth::user()->level == 'kacab'){
            if($k == null){
                $kota = Kantor::where('id', $kot)->where('id_com', Auth::user()->id_com)->get();
                $datao = User::whereRaw("aktif = 1 AND kolektor IS NOT NULL AND  id_kantor = '$kot'")->get() ;
            }else{
                $kota = Kantor::whereRaw("(id = '$kot' OR id = '$k->id')")->where('id_com', Auth::user()->id_com)->get();
                $datao = User::whereRaw("aktif = 1 AND kolektor IS NOT NULL AND  (id_kantor = '$kot' OR id_kantor = '$k->id')")->get() ;
            }
        }else{
            $kota = Kantor::where('id_com', Auth::user()->id_com)->get();
            $datao = User::whereRaw("aktif = 1 AND kolektor IS NOT NULL")->get() ;
        }
        
        
        // return($belumass);
        
        return view('kolekting.assignment_admin', compact('belumass','pemb','stat','kota', 'k', 'kot', 'datao'));
    }
    
    public function assign_all(Request $request) {
        // return($request);
        // 
        $rwarning = $request->warning != '' ? $request->warning : 0;
        $kot = Auth::user()->id_kantor;
            
        // if($request->ajax()){
            
        $ass_all = Donatur::where(function($q) use ($request, $kot) {
                                if(Auth::user()->level == 'kacab'){
                                    if($request->kota != ''){
                                        $q->where('id_kantor', $request->kota);
                                    }else{
                                        $q->where('id_kantor', $kot);
                                    }
                                }else if(Auth::user()->level == 'admin'){
                                    if($request->kota != ''){
                                        $q->where('id_kantor', $request->kota);
                                    }
                                }else{
                                    $q->where('id_kantor', $kot);
                                }
                            })
                            ->where(function($q) use ($request) {
                                if($request->jalurah != ''){
                                    $q->where('id_jalur', $request->jalurah);
                                }
                            })
                            ->where(function($q) use ($request) {
                                if($request->stts != ''){
                                    $q->where('status', $request->stts);
                                }else{
                                    $q->where('status', 'belum dikunjungi');
                                }
                            })
                            ->where(function($q) use ($request) {
                                if($request->pembayaran != ''){
                                    $q->where('pembayaran', $request->pembayaran);
                                }
                            })
                            ->where(function($q) use ($request) {
                                if($request->petugas != ''){
                                    $q->where('id_koleks', $request->petugas);
                                }
                            })
                            ->where(function($q) use ($rwarning) {
                                if($rwarning != 1){
                                    $q->where('warning', '!=', 1);
                                }
                            })
                            ->whereIn('id', function($query){
                                    $query->select('id_don')->from('prosp')->where('ket', 'closing');
                                })
                            ->where('status', '!=', 'Off')->where('status', '!=', 'Ditarik')
                            ->update([
                                'acc' => 1,
                                'tgl_kolek' => date('Y-m-d H:i:s')
                            ]);
                            // ->get();
            // return($ass_all);   
                                
            
            
            // $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            // $jalur = $request->jalurah != '' ? "id_jalur = '$request->jalurah'" : "id_jalur != 'hahaha'";
            // $kota = $request->kota != '' ? "id_kantor = '$request->kota'" : "kota IS NOT NULL";
            // $stts = $request->stts != '' ? "status = '$request->stts'" : "status IS NOT NULL";
            // $pembayaran = $request->pembayaran != '' ? "pembayaran = '$request->pembayaran'" : "pembayaran != '-'";
            // $petugas = $request->petugas != '' ? "id_koleks = '$request->petugas'" : "id IS NOT NULL";
            
            // if(Auth::user()->level == 'admin'){
            //     $assigment = Donatur::whereRaw("$jalur AND $kota AND status != 'Ditarik' AND status != 'Off' AND status != 'Kotak Hilang' AND $stts AND $pembayaran AND warning != 1 ")
            //                 ->update([
            //                     'acc' => 1,
            //                     'tgl_kolek' => date('Y-m-d H:i:s')
            //                 ]);
            // }else if(Auth::user()->level == 'kacab'){
            //     if($k == null){
            //         $assigment = Donatur::whereRaw("$jalur AND $petugas AND id_kantor = '$kot' AND status != 'Ditarik' AND status != 'Off' AND status != 'Kotak Hilang' AND $stts AND $pembayaran AND warning != 1 ")
            //                     ->update([
            //                         'acc' => 1,
            //                         'tgl_kolek' => date('Y-m-d H:i:s')
            //                     ]);
            //     }else{
            //         if($request->kota != ''){
            //             $assigment = Donatur::whereRaw("$jalur AND $petugas AND $kota AND status != 'Ditarik' AND status != 'Off' AND status != 'Kotak Hilang' AND $stts AND $pembayaran AND warning != 1")
            //                         ->update([
            //                             'acc' => 1,
            //                             'tgl_kolek' => date('Y-m-d H:i:s')
            //                         ]);
            //         }else{
            //             $assigment = Donatur::whereRaw("$jalur AND $petugas AND id_kantor = '$kot' AND status != 'Ditarik' AND status != 'Off' AND status != 'Kotak Hilang' AND $stts AND $pembayaran AND warning != 1")
            //                         ->orWhereRaw("id_kantor = '$k->id' AND $petugas AND $jalur AND status != 'Ditarik' AND status != 'Off' AND status != 'Kotak Hilang' AND $stts AND $pembayaran AND warning != 1")
            //                         ->update([
            //                             'acc' => 1,
            //                             'tgl_kolek' => date('Y-m-d H:i:s')
            //                         ]);
                        
            //         }
            //     }
            // }else{
            //     $assigment = Donatur::whereRaw("$jalur AND id_kantor = '$kot' AND $petugas AND status != 'Ditarik' AND status != 'Off' AND status != 'Kotak Hilang' AND $stts AND $pembayaran AND warning != 1")
            //     ->update([
            //         'acc' => 1,
            //         'tgl_kolek' => date('Y-m-d H:i:s')
            //     ]);
            // }
        // }
        
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function changeStatusadm(Request $request) 
    {
        
        $don = Donatur::find($request->id);
        $petugas = User::where('id', $don->id_koleks)->first();
        $trans = Tunjangan::first();
        
        // if($don->warning == 1 && $petugas->id_jabatan == $trans->kolektor){
        //     $don = [];
        // }else{
            $don->acc = $request->acc;
            $don->tgl_kolek = date('Y-m-d H:i:s');
            $don->update();
        // }
       
        return response()->json(['success' => 'Update Berhasil']);
    }
    
    function actionsadm(Request $request)
    {
        if($request->ajax())
        {
            if($request->action == 'edit')
            {
                
                $trans = Tunjangan::first();
                $don = Donatur::find($request->id);
                $caripetugas = $request->petugas != '' ? User::where('id', $request->petugas)->first()->name : $don->petugas;
                $carikolek = $request->petugas != '' ? $request->petugas : $don->id_koleks;
                $caritgl = $request->tgl_kolek != '' ? $request->tgl_kolek :  $don->tgl_kolek;
                $caripem = $request->pembayaran != '' ? $request->pembayaran : $don->pembayaran;
                $tgl_ass = $request->jadwal_assignment != '' ? $request->jadwal_assignment : $don->jadwal_assignment;
                $create_jadwal = $don->create_jadwal == '' ? Auth::user()->id : $don->create_jadwal;
                // return([$don, $petugas]);
                
                // if($don->warning == 1 && $petugas->id_jabatan == $trans->kolektor){
                //     $don = [];
                // }else{
                    $don->petugas	    = $caripetugas;
                    $don->id_koleks     = $carikolek;
                    $don->tgl_kolek     = $caritgl ;
                    $don->pembayaran	= $caripem;
                    $don->jadwal_assignment = $tgl_ass;
                    $don->create_jadwal = $create_jadwal;
                    
                    $p =  $don->update();
                
                    return response()->json($p);
            }
           
        }
    }
    
    public function cek_jlr(){
        $kota = $_GET['kota'];
        $data = Jalur::whereIn('id_jalur', function($query){
                    $query->select('id_jalur')
                    ->from('donatur')
                    ->where('status', '!=' ,'Ditarik');
                })->where('id_kantor', $kota)->get();
        return $data;
    }
    
    public function getjlr_multiple(Request $request){
        $now = date('Y-m-d');
        $tanggal = $request->tgll;
        
        $date1 = date('m', strtotime($now));
        $date2 = date('m', strtotime($tanggal));
        
        if($date1 == $date2){
            $data = Donatur::selectRaw("id_jalur, jalur")->where('id_kantor', $request->unitt)->whereRaw("status != 'Ditarik' AND status != 'Off'")->groupBy('id_jalur')->orderBy('jalur','asc')->get();
        }else{
            $data = Donatur::selectRaw("id_jalur, jalur")->where('id_kantor', $request->unitt)->groupBy('id_jalur')->orderBy('jalur','asc')->get(); 
        }
        
        return response()->json($data);
    }
    
    public function jadwalkan(Request $request){
        
        $cay = explode(",",$request->donnn);
        $data = Donatur::whereRaw("id IN ('" . implode("', '", $cay) . "')")
        // ->get();
        ->update(['jadwal_assignment' => $request->tgll, 'create_jadwal' => Auth::user()->id]);
        
        return response()->json(['success' => 'Update Berhasil']);
        // return $data;
    }
    
    public function ganti_petugas_bang(Request $request){
        // return $request;
        if($request->oye == 'pilihan'){
            $cay = explode(",",$request->donnn);
            $data = Donatur::whereRaw("id IN ('" . implode("', '", $cay) . "')")
                    // ->get();
                    ->update(['id_koleks' => $request->ptg, 'petugas' => $request->name]);
            
        }else{
            $rwarning = $request->warn != '' ? $request->warn : 0;
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            $kot = Auth::user()->id_kantor;
            $data = Donatur::where(function($q) use ($request, $kot) {
                        if(Auth::user()->level == 'kacab'){
                            if($request->kota != ''){
                                $q->where('id_kantor', $request->kota);
                            }else{
                                $q->where('id_kantor', $kot);
                            }
                        }else if(Auth::user()->level == 'admin'){
                            if($request->kota != ''){
                                $q->where('id_kantor', $request->kota);
                            }
                        }else{
                            $q->where('id_kantor', $kot);
                        }
                    })
                    
                    ->where(function($q) use ($request) {
                        if($request->jalurah != ''){
                            $q->where('id_jalur', $request->jalurah);
                        }
                    })
                    
                    ->where(function($q) use ($request) {
                        if($request->stts != ''){
                            $q->where('status', $request->stts);
                        }else{
                            $q->where('status', 'belum dikunjungi');
                        }
                    })
                    
                    ->where(function($q) use ($request) {
                        if($request->pembayaran != ''){
                            $q->where('pembayaran', $request->pembayaran);
                        }else{
                            $q->where('pembayaran', '!=', '-');
                        }
                    })
                    
                    ->where(function($q) use ($rwarning) {
                        if($rwarning != 1){
                            $q->where('warning', '!=', 1);
                        }
                    })
                        
                    ->where(function($q) use ($request) {
                        if($request->petugas != ''){
                            $q->where('id_koleks', $request->petugas);
                        }
                    })
                    
                    ->whereIn('id', function($q){
                        $q->select('id_don')->from('prosp')->where('ket','closing');
                    })
                    
                    ->where('status', '!=', 'Off')->where('status', '!=', 'Ditarik')
                    
                    // ->get();
                    
                    ->update(['id_koleks' => $request->ptg, 'petugas' => $request->name]);
        }
        
        
        // return $data;
        return response()->json(['success' => 'Update Berhasil']);
    }
    
    public function jadwalkan_all(Request $request){
        
        $rwarning = $request->warn != '' ? $request->warn : 0;
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kot = Auth::user()->id_kantor;
        
        $data = Donatur:: whereIn('id', function($q){
                    $q->select('id_don')->from('prosp')->where('ket','closing');
                })
                ->where(function($q) use ($request, $kot) {
                    if(Auth::user()->level == 'kacab'){
                        if($request->kota != ''){
                            $q->where('id_kantor', $request->kota);
                        }else{
                            $q->where('id_kantor', $kot);
                        }
                    }else if(Auth::user()->level == 'admin'){
                        if($request->kota != ''){
                            $q->where('id_kantor', $request->kota);
                        }
                    }else{
                        $q->where('id_kantor', $kot);
                    }
                })
                ->where(function($q) use ($request) {
                    if($request->jalurah != ''){
                        $q->where('id_jalur', $request->jalurah);
                    }
                })
                ->where(function($q) use ($request) {
                    if($request->stts != ''){
                        $q->where('status', $request->stts);
                    }else{
                        $q->where('status', 'belum dikunjungi');
                    }
                })
                ->where(function($q) use ($request) {
                    if($request->pembayaran != ''){
                        $q->where('pembayaran', $request->pembayaran);
                    }else{
                        $q->where('pembayaran', '!=', '-');
                    }
                })
                
                ->where(function($q) use ($rwarning) {
                    if($rwarning != 1){
                        $q->where('warning', '!=', 1);
                    }
                })
                
                ->where(function($q) use ($request) {
                    if($request->petugas != ''){
                        $q->where('id_koleks', $request->petugas);
                    }
                })
                ->where('status', '!=', 'Off')->where('status', '!=', 'Ditarik')
                // ->get();
                ->update(['jadwal_assignment' => $request->tgll, 'create_jadwal' => Auth::user()->id]);
                // return $data;
        return response()->json(['success' => 'Update Berhasil']);
    }
    
    public function set_warning(Request $request){
        
        $rwarning = $request->warn != '' ? $request->warn : 0;
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kot = Auth::user()->id_kantor;
        $mindon = $request->mindon;
        $jumbul = $request->jumbul;
        $program = $request->program;
        
        
        
        // $data = Donatur:: whereIn('id', function($q){
        //             $q->select('id_don')->from('prosp')->where('ket','closing');
        //         })
                
        //         ->where(function($q) use ($request, $kot, $k) {
        //             if(Auth::user()->level == 'kacab'){
        //                 if($request->kota != ''){
        //                     $q->where('id_kantor', $request->kota);
        //                 }else{
        //                     if($k == null){
        //                         $q->where('id_kantor', $kot);
        //                     }else{
        //                         $q->whereRaw("id_kantor = '$kot' OR id_kantor = '$k->id'");
        //                     }
        //                 }
        //             }else if(Auth::user()->level == 'admin'){
        //                 if($request->kota != ''){
        //                     $q->where('id_kantor', $request->kota);
        //                 }
        //             }
        //         })
                
        //         ->where(function($q) use ($request) {
        //             if($request->jalurah != ''){
        //                 $q->where('id_jalur', $request->jalurah);
        //             }
        //         })
                
        //         ->where(function($q) use ($request) {
        //             if($request->stts != ''){
        //                 $q->where('status', $request->stts);
        //             }else{
        //                 $q->where('status', 'belum dikunjungi');
        //             }
        //         })
                
        //         ->where(function($q) use ($request) {
        //             if($request->pembayaran != ''){
        //                 $q->where('pembayaran', $request->pembayaran);
        //             }else{
        //                 $q->where('pembayaran', '!=', '-');
        //             }
        //         })
                
        //         ->where(function($q) use ($rwarning) {
        //             if($rwarning != 1){
        //                 $q->where('warning', '!=', 1);
        //             }
        //         })
                
        //         ->where(function($q) use ($request) {
        //             if($request->petugas != ''){
        //                 $q->where('id_koleks', $request->petugas);
        //             }
        //         })
                
        //         ->where('status', '!=', 'Off')->where('status', '!=', 'Ditarik')
                
        //         ->get();
                // ->update(['jadwal_assignment' => $request->tgll, 'create_jadwal' => Auth::user()->id]);
                
                // return $data;
                $cia = $program == '' ? "donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))" : "donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND id_prog = '$cia')";
                
                if(Auth::user()->kolekting == 'admin'){
                    if($request->kota == ''){
                        Donatur::whereRaw("$cia")->update(['warning' => 0]);
                    }else{
                        Donatur::whereRaw("donatur.id_kantor = '$request->kota' AND $cia")->update(['warning' => 0]);
                    }
                }else{
                    $ide = Auth::user()->id;
                    Donatur::whereRaw("user_warning = '$ide' AND donatur.id_kantor = '$request->kota' AND $cia")->update(['user_warning' => 0, 'warning' => 0]);
                }
                
                
                if(Auth::user()->level == 'kacab'){
                    if($request->kota != ''){
                        $kan = "donatur.id_kantor = '$request->kota'";
                    }else{
                        if($k == null){
                            $kan = "donatur.id_kantor = '$kot'";
                        }else{
                            $kan = "(donatur.id_kantor = '$kot' OR donatur.id_kantor = '$k->id')";
                        }
                    }
                }else if(Auth::user()->level == 'admin'){
                    if($request->kota != ''){
                        $kan = "donatur.id_kantor = '$request->kota'";
                    }else{
                        $kan = "donatur.id_kantor IS NOT NULL";
                    }
                }
                
                $now = date('Y-m-d');
                $bulan_now = date('Y-m-t', strtotime('-1 month', strtotime($now)));
                $interval = date('Y-m-01', strtotime('-'.$jumbul.' month', strtotime($now)));
                $datas = Donatur::selectRaw("DATE_FORMAT(transaksi.tanggal, '%Y-%m') as bulan, id_donatur,
                            SUM(IF(donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND $kan AND $cia, jumlah, 0 )) as ju
                            ")
                            
                        ->join('transaksi','donatur.id','=','transaksi.id_donatur')
                            ->whereIn('donatur.id', function($q){
                            $q->select('id_don')->from('prosp')->where('ket','closing');
                        })
                        
                        ->whereRaw("donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND $cia")
                        
                        ->where(function($q) use ($request, $kot, $k) {
                            if(Auth::user()->level == 'kacab'){
                                if($request->kota != ''){
                                    $q->where('donatur.id_kantor', $request->kota);
                                }else{
                                    if($k == null){
                                        $q->where('id_kantor', $kot);
                                    }else{
                                        $q->whereRaw("donatur.id_kantor = '$kot' OR donatur.id_kantor = '$k->id'");
                                    }
                                }
                            }else if(Auth::user()->level == 'admin'){
                                if($request->kota != ''){
                                    $q->where('donatur.id_kantor', $request->kota);
                                }else{
                                    $q->whereRaw("donatur.id_kantor IS NOT NULL");
                                }
                            }
                        })
                        ->groupBy('id_donatur','bulan')
                        ->get();
                        
                $targetAmount = $mindon;
                $jumbul = $jumbul;
                 
                $result = [];
                $count = 0;
                $sepong = [];
                $coy = [];
                $result2 = [];
                
                $groupedData = collect($datas)->groupBy('id_donatur')->toArray();
                
                foreach ($groupedData as $donatur => $donaturData) {
                    
                    $kon = count(array_column($donaturData, 'bulan'));
                    $hasil = count(array_filter($donaturData, function ($item) use ($targetAmount) {
                            return $item['ju'] <  $targetAmount;
                    }));
                    
                    // $result[] = [
                    //     "bulan" => array_column($donaturData, 'bulan'),
                    //     "id_donatur" => $donaturData[0]['id_donatur'],
                    //     "jumlah" => array_column($donaturData, 'ju'),
                    //     "count_bulan" => $kon,
                    //     'donasi_lebih_dari_'.$targetAmount =>  $hasil
                        
                    // ];
                    
                    if($kon == $jumbul){
                        if($hasil == $jumbul){
                            
                            // $result2[] = [
                            //     "id_donatur" => $donaturData[0]['id_donatur'],
                            //     'donasi_lebih_dari_'.$targetAmount =>  $hasil
                            // ];
                            
                             Donatur::find($donaturData[0]['id_donatur'])->update(['warning' => 1, 'user_warning' => Auth::user()->id, 'jumbul' => $jumbul, 'mindon' => $mindon, 'user_update' => Auth::user()->id ]);
                        }
                    }
                }
                // return $result2;
        return response()->json(['success' => 'Update Berhasil']);
    }

    public function getjumbul(Request $request){
        $cari = Donatur::find($request->idd);
        
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kot = Auth::user()->id_kantor;
        $mindon = $cari->mindon;
        $jumbul = $cari->jumbul;
        
        $iddd = $cari->id;
        
        if(Auth::user()->level == 'kacab'){
            if($request->kota != ''){
                $kan = "donatur.id_kantor = '$request->kota'";
            }else{
                if($k == null){
                    $kan = "donatur.id_kantor = '$kot'";
                }else{
                    $kan = "donatur.id_kantor = '$kot' OR donatur.id_kantor = '$k->id'";
                }
            }
        }else if(Auth::user()->level == 'admin'){
            if($request->kota != ''){
                $kan = "donatur.id_kantor = '$request->kota'";
            }else {
                 $kan = "donatur.id_kantor IS NOT NULL";
            }
        }
        
        $now = date('Y-m-d');
        $bulan_now = date('Y-m-t', strtotime('-1 month', strtotime($now)));
        $interval = date('Y-m-01', strtotime('-'.$jumbul.' month', strtotime($now)));
        $datas = Donatur::selectRaw("DATE_FORMAT(transaksi.tanggal, '%Y-%m') as bulan, id_donatur, nama, 
                        SUM(IF(donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND $kan AND donatur.id = '$iddd', jumlah, 0 )) as ju
                        ")
                            
                ->join('transaksi','donatur.id','=','transaksi.id_donatur')
                    ->whereIn('donatur.id', function($q){
                        $q->select('id_don')->from('prosp')->where('ket','closing');
                })
                        
                ->whereRaw("donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND id_donatur = '$iddd'")
                        
                ->where(function($q) use ($request, $kot, $k) {
                    if(Auth::user()->level == 'kacab'){
                        if($request->kota != ''){
                            $q->where('donatur.id_kantor', $request->kota);
                        }else{
                            if($k == null){
                                $q->where('donatur.id_kantor', $kot);
                            }else{
                                $q->whereRaw("donatur.id_kantor = '$kot' OR donatur.id_kantor = '$k->id'");
                            }
                        }
                    }else if(Auth::user()->level == 'admin'){
                        if($request->kota != ''){
                            $q->where('donatur.id_kantor', $request->kota);
                        }
                    }
                })
                ->groupBy('id_donatur','bulan')
                ->get();
                
        return [$datas, $mindon, $jumbul];
                        
        //         $targetAmount = $mindon;
        //         $jumbul = $jumbul;
                 
        //         $result = [];
        //         $count = 0;
        //         $sepong = [];
        //         $coy = [];
        //         $result2 = [];
                
        //         $groupedData = collect($datas)->groupBy('id_donatur')->toArray();
                
        //         foreach ($groupedData as $donatur => $donaturData) {
                    
        //             $kon = count(array_column($donaturData, 'bulan'));
        //             $hasil = count(array_filter($donaturData, function ($item) use ($targetAmount) {
        //                     return $item['ju'] <  $targetAmount;
        //             }));
                    
        //             // $result[] = [
        //             //     "bulan" => array_column($donaturData, 'bulan'),
        //             //     "id_donatur" => $donaturData[0]['id_donatur'],
        //             //     "jumlah" => array_column($donaturData, 'ju'),
        //             //     "count_bulan" => $kon,
        //             //     'donasi_lebih_dari_'.$targetAmount =>  $hasil
                        
        //             // ];
                    
        //             if($kon == $jumbul){
        //                 if($hasil == $jumbul){
                            
        //                     $result2[] = [
        //                         "id_donatur" => $donaturData[0]['id_donatur'],
        //                         'donasi_lebih_dari_'.$targetAmount =>  $hasil
        //                     ];
                            
        //                     //  Donatur::find($donaturData[0]['id_donatur'])->update(['warning' => 1, 'jumbul' => $jumbul, 'mindon' => $mindon, 'user_update' => Auth::user()->id ]);
        //                 }
        //             }
        //         }
        // return $result2;
    }

}
