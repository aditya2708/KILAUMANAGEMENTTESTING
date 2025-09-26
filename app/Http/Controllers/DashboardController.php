<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kolektors;
use App\Models\Karyawan;
use App\Models\Donatur;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\Profile;
use App\Models\Transaksi_Perhari;
use App\Models\Kantor;
use Carbon\Carbon;
use Auth;
use DB;

use DataTables;

class DashboardController extends Controller
{
    public function index(Request $request){ 
        // dd(Auth::user());
        $id_com = $request->com;
        $tot_kar = 0;
        $tot_don = 0;
        $tot_kan = 0;
        $tot_tar = 0;
        $data = [];
        $pp = DB::table('tambahan')->where('id', Auth::user()->id_kantor)->first();
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kunit = $k != null ? $k->id : 'asdfghqwerty';
        $lev = Auth::user()->level;
        // dd($lev);
        if(Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat' || Auth::user()->level == 'operator pusat'){
            $tot_kar = Karyawan::where('aktif', 1)->where(function($query) use ($id_com){
                                if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                    if($id_com > 0){
                                        $query->where('id_com', $id_com);
                                    }else if($id_com == '0'){
                                        $query->whereIn('id_com', function($q) {
                                            $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                        });
                                    }else{
                                       $query->where('karyawan.id_com', Auth::user()->id_com);
                                    } 
                                }else{
                                    $query->where('karyawan.id_com', Auth::user()->id_com);
                                }
                            })->count();
            $tot_don = Donatur::where('status', '!=', 'Ditarik')
                        ->where('status', '!=', 'Off')
                        ->whereIn('id_koleks', function($query) use ($id_com){
                            $query->select('id')
                                    ->from('users')
                                    ->where(function($query) use ($id_com){
                                        if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                            if($id_com > 0){
                                                $query->where('id_com', $id_com);
                                            }else if($id_com == '0'){
                                                $query->whereIn('id_com', function($q) {
                                                    $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                                });
                                            }else{
                                                $query->where('id_com', Auth::user()->id_com);
                                            } 
                                        }else{
                                            $query->where('id_com', Auth::user()->id_com);
                                        }
                                    });
                            
                        })
                        ->count();
            $tot_tar = Transaksi::where('jumlah','>' ,'0')
                        ->where('via_input','transaksi')
                        ->whereIn('id_kantor', function($query) use ($id_com){
                            $query->select('id')
                                    ->from('tambahan')
                                    ->where(function($query) use ($id_com){
                                        if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                            if($id_com > 0){
                                                $query->where('id_com', $id_com);
                                            }else if($id_com == '0'){
                                                $query->whereIn('id_com', function($q) {
                                                    $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                                });
                                            }else{
                                                $query->where('id_com', Auth::user()->id_com);
                                            } 
                                        }else{
                                            $query->where('id_com', Auth::user()->id_com);
                                        }
                                    });
                            
                        })
                        ->distinct('id_transaksi')
                        ->count();
            $tot_kan = Kantor::where(function($query) use ($id_com){
                                if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                    if($id_com > 0){
                                        $query->where('id_com', $id_com);
                                    }else if($id_com == '0'){
                                        $query->whereIn('id_com', function($q) {
                                            $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                        });
                                    }else{
                                       $query->where('id_com', Auth::user()->id_com);
                                    } 
                                }else{
                                    $query->where('id_com', Auth::user()->id_com);
                                }
                            })->count();
            
            $bln = Carbon::now()->format('m-Y') ;
            $bln2 = Carbon::now()->format('m-Y');
            
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            $data = Transaksi_Perhari::select(\DB::raw("name, id,
                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset"))
                ->whereIn('id_kantor', function($query) use ($id_com){
                            $query->select('id')
                                    ->from('tambahan')
                                    ->where(function($query) use ($id_com){
                                        if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                            if($id_com > 0){
                                                $query->where('id_com', $id_com);
                                            }else if($id_com == '0'){
                                                $query->whereIn('id_com', function($q) {
                                                    $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                                });
                                            }else{
                                                $query->where('id_com', Auth::user()->id_com);
                                            } 
                                        }else{
                                            $query->where('id_com', Auth::user()->id_com);
                                        }
                                    });
                            
                        })
                ->groupBy('name', 'id')->where('aktif',1)->limit(5)->orderBy('Omset', 'DESC')->get();
                
               
        }elseif(Auth::user()->level == 'kacab' || Auth::user()->kepegawaian == 'kacab' || Auth::user()->level == 'spv' || Auth::user()->keuangan == 'keuangan cabang'){
            $tot_kar = Karyawan::where('aktif', 1)->where('id_kantor', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->count();
            $tot_don = Donatur::where('status', '!=', 'Ditarik')->where('status', '!=', 'Off')->where(function($query) use ($kunit) {
                        $query->where('id_kantor', Auth::user()->id_kantor)->orWhere('id_kantor', $kunit);  
                    })->count();
            $tot_tar = Transaksi::where('status', 'Donasi')->where('via_input','transaksi')->where(function($query) use ($kunit) {
                        $query->where('id_kantor', Auth::user()->id_kantor)->orWhere('id_kantor', $kunit);  
                    })->distinct('id_transaksi')->count();
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->first();
            $tot_kan =  Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->select('unit', 'id')->count();
            // $tot_kan = Kantor::all()->count();
            
            $bln = Carbon::now()->format('m-Y') ;
            $bln2 = Carbon::now()->format('m-Y');
            
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            $data = Transaksi_Perhari::select(\DB::raw("name, id,
                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset"))
                    ->where(function($query) use ($kunit) {
                        $query->where('id_kantor', Auth::user()->id_kantor)->orWhere('id_kantor', $kunit);
                            
                    })
                     ->whereIn('id_kantor', function($query) use ($id_com){
                            $query->select('id')
                                    ->from('tambahan')
                                    ->where(function($query) use ($id_com){
                                        if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                            if($id_com > 0){
                                                $query->where('id_com', $id_com);
                                            }else if($id_com == '0'){
                                                $query->whereIn('id_com', function($q) {
                                                    $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                                });
                                            }else{
                                                $query->where('id_com', Auth::user()->id_com);
                                            } 
                                        }else{
                                            $query->where('id_com', Auth::user()->id_com);
                                        }
                                    });
                            
                        })
                    ->groupBy('name', 'id')->limit(5)->orderBy('Omset', 'DESC')->get();
        }else if(Auth::user()->keuangan == 'agen'){
            return redirect('pengeluaran');
        }
        
        if(request()->ajax()){
            if($request->tab == "tab"){
                $transaksi = Transaksi::where('via_input','transaksi')
                    ->where(function($query) use ($kunit, $lev) {
                                if($lev == 'kacab'){
                                    $query->where('id_kantor', Auth::user()->id_kantor)->orWhere('id_kantor', $kunit);
                                }else{
                                    $query->where('id_kantor', Auth::user()->id_kantor);
                                }
                            })
                            ->whereIn('id_kantor', function($query) use ($id_com){
                                $query->select('id')
                                        ->from('tambahan')
                                        ->where(function($query) use ($id_com){
                                            if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                                if($id_com > 0){
                                                    $query->where('id_com', $id_com);
                                                }else if($id_com == '0'){
                                                    $query->whereIn('id_com', function($q) {
                                                        $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                                    });
                                                }else{
                                                    $query->where('id_com', Auth::user()->id_com);
                                                } 
                                            }else{
                                                $query->where('id_com', Auth::user()->id_com);
                                            }
                                        });
                                
                            })
                            ->take(5)->latest()->get();
                return DataTables::of($transaksi)
                    ->addIndexColumn()
                    ->addColumn('jml', function($transaksi){
                        $jml = number_format($transaksi->jumlah, 0, ',', '.');
                        return $jml;
                    })
                    
                    ->addColumn('id_tr', function($transaksi){
                        $link = "https://kilauindonesia.org/datakilau/kwitansi/$transaksi->id_transaksi";
                        $trr = '<a href="'.$link.'" target="_blank">'.$transaksi->id_transaksi .'</a>';
                        return $trr;
                    })
                    ->rawColumns(['id_tr'])
                    ->make(true);
            }
        }
        
        // return $tot_don;
        return view('dashboard.index', compact('tot_kar', 'tot_don', 'tot_tar', 'tot_kan','data', 'pp'));   
    }
    
    public function nyoba(){
        return view('nyoba');  
    }
    
    public function badge_doang(){
        $m = date('m');
        $y = date('Y');
        
        $user = Auth::user()->id_kantor;
        if(Auth::user()->level = 'admin'){
            $t = Transaksi::selectRaw("COUNT(id) as transaksi")->whereRaw("approval = 2 AND id_kantor = '$user' AND MONTH(tanggal) = '$m' AND YEAR(tanggal) = '$y'")->get();
            $p = Pengeluaran::selectRaw("COUNT(id) as pengeluaran")->whereRaw("acc = 2 AND kantor = '$user' AND MONTH(tgl) = '$m' AND YEAR(tgl) = '$y'")->get();
        }else if(Auth::user()->level = 'kacab'){
            $k = Kantor::where('id_kantor', Auth::user()->id_kantor)->first();
            if($k == null){
                $t = Transaksi::selectRaw("COUNT(id) as transaksi")->whereRaw("approval = 2 AND id_kantor = '$user' AND MONTH(tanggal) = '$m' AND YEAR(tanggal) = '$y'")->get();
                $p = Pengeluaran::selectRaw("COUNT(id) as pengeluaran")->whereRaw("acc = 2 AND kantor = '$user' AND MONTH(tgl) = '$m' AND YEAR(tgl) = '$y'")->get();
            }else{
                $t = Transaksi::selectRaw("COUNT(id) as transaksi")->whereRaw("approval = 2 AND (id_kantor = '$k->id' OR id_kantor = '$user') AND MONTH(tanggal) = '$m' AND YEAR(tanggal) = '$y'")->get();
                $p = Pengeluaran::selectRaw("COUNT(id) as pengeluaran")->whereRaw("acc = 2 AND (kantor = '$k->id' OR kantor = '$user') AND MONTH(tgl) = '$m' AND YEAR(tgl) = '$y'")->get();
            }
        }
        
        $data = [];
        
        $data =[
            'transaksi' => $t,
            'pengeluaran' => $p
            ];
        return $data;
    }
    
    public function dashboard_tab(Request $request){
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kunit = $k != null ? $k->id : 'asdfghqwerty';
        $lev = Auth::user()->level;
        if(Auth::user()->level == 'admin' || Auth::user()->level == 'keuangan pusat'){
            $tot_kar = Karyawan::where('aktif', 1)->count();
            $tot_don = Donatur::where('status', '!=', 'Ditarik')->where('status', '!=', 'Off')->count();
            $tot_tar = Transaksi::where('status', 'Donasi')->where('via_input','transaksi')->distinct('id_transaksi')->count();
            $tot_kan = Kantor::all()->count();
            
            $bln = Carbon::now()->format('m-Y') ;
            $bln2 = Carbon::now()->format('m-Y');
            
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            $data = Transaksi_Perhari::select(\DB::raw("name, id,
                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset"))
                ->groupBy('name', 'id')->where('aktif',1)->limit(5)->orderBy('Omset', 'DESC')->get();
        }elseif(Auth::user()->level == 'kacab' || Auth::user()->level == 'spv' || Auth::user()->keuangan == 'keuangan cabang'){
            $tot_kar = Karyawan::where('aktif', 1)->where('id_kantor', Auth::user()->id_kantor)->count();
            $tot_don = Donatur::where('status', '!=', 'Ditarik')->where('status', '!=', 'Off')->where(function($query) use ($kunit) {
                        $query->where('id_kantor', Auth::user()->id_kantor)->orWhere('id_kantor', $kunit);  
                    })->count();
            $tot_tar = Transaksi::where('status', 'Donasi')->where('via_input','transaksi')->where(function($query) use ($kunit) {
                        $query->where('id_kantor', Auth::user()->id_kantor)->orWhere('id_kantor', $kunit);  
                    })->distinct('id_transaksi')->count();
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            $tot_kan =  Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->select('unit', 'id')->count();
            // $tot_kan = Kantor::all()->count();
            
            $bln = Carbon::now()->format('m-Y') ;
            $bln2 = Carbon::now()->format('m-Y');
            
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            $data = Transaksi_Perhari::select(\DB::raw("name, id,
                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset"))
                    ->where(function($query) use ($kunit) {
                        $query->where('id_kantor', Auth::user()->id_kantor)->orWhere('id_kantor', $kunit);
                            
                    })
                    ->groupBy('name', 'id')->limit(5)->orderBy('Omset', 'DESC')->get();
        }else if(Auth::user()->keuangan == 'agen'){
            return redirect('pengeluaran');
        }
        
        if(request()->ajax()){
            if($request->tab == "tab"){
                $transaksi = Transaksi::where('via_input','transaksi')
                    ->where(function($query) use ($kunit, $lev) {
                                if($lev == 'kacab'){
                                    $query->where('id_kantor', Auth::user()->id_kantor)->orWhere('id_kantor', $kunit);
                                }else{
                                    $query->where('id_kantor', Auth::user()->id_kantor);
                                }
                            })->take(5)->latest()->get();
                return DataTables::of($transaksi)
                    ->addIndexColumn()
                    ->addColumn('jml', function($transaksi){
                        $jml = number_format($transaksi->jumlah, 0, ',', '.');
                        return $jml;
                    })
                    
                    ->addColumn('id_tr', function($transaksi){
                        $link = "https://kilauindonesia.org/datakilau/kwitansi/$transaksi->id_transaksi";
                        $trr = '<a href="'.$link.'" target="_blank">'.$transaksi->id_transaksi .'</a>';
                        return $trr;
                    })
                    ->rawColumns(['id_tr'])
                    ->make(true);
            }
        }
        return view('dashboard.nyoba_d', compact('tot_kar', 'tot_don', 'tot_tar', 'tot_kan','data'));   
    }
    
    public function ubahaja(Request $request){
        DB::table('tambahan')->where('id', Auth::user()->id_kantor)->update([
                'title' => $request->tit,
                'des' => $request->des,
                'user_update' => Auth::user()->id
            ]);
        
        return redirect('dashboard');
    }
    
    public function handsome(){
        return view('diroh.index');
    }
    
    public function target(){
        
        $bln = date('m-Y');
        $bln2 = $bln;
        $approve = "transaksi.approval = 1";
        $bayar = "transaksi.pembayaran != 'mutasi'";
        
        $bulannew = date('m');
        $tahunnew = date('Y');
        
        $kot = "users.id_kantor IS NOT NULL";
        
        $kota = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', $kota)->where('id_com', Auth::user()->id_com)->first();
        
        if (Auth::user()->kolekting =='admin') {
            $data['kantor'] = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                            ->leftjoin('targets',function($join) use ($bln) {
                                $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                    ->whereRaw("DATE_FORMAT(targets.tanggal, '%m-%Y') = '$bln' AND periode = 'bulan' AND jenis_target = 'kan'");
                            })
                            ->selectRaw("tambahan.id, tambahan.unit, targets.target, SUM(IF(DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset")
                            ->where('tambahan.id_com', Auth::user()->id_com)
                            ->whereRaw("$approve AND $bayar")
                            ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC')->get();
                            
            $data['user'] = Transaksi::join('users', 'transaksi.id_koleks', '=', 'users.id')
                                ->leftjoin('targets',function($join) use ($bulannew, $tahunnew) {
                                    $join->on('users.id_karyawan' ,'=', 'targets.id_jenis')
                                            ->where('jenis_target','kar')
                                            ->where('periode','bulan')
                                            ->whereMonth('targets.tanggal', $bulannew)
                                            ->whereYear('targets.tanggal', $tahunnew);
                                        })
                                ->where('transaksi.via_input', 'transaksi')
                                ->selectRaw("users.id, users.name, transaksi.id_koleks as id, users.id_jabatan, targets.target as target_dana,
                                        SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset")
                                        
                                ->whereRaw("$approve AND $bayar AND users.aktif = 1")
                                        
                                ->where('users.id_com', Auth::user()->id_com)
                                ->groupBy('name','transaksi.id_koleks','users.id_jabatan')->orderBy('transaksi.kolektor', 'ASC')->get();
        }else{
            $data['kantor'] = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bln) {
                                            $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                    ->whereRaw("DATE_FORMAT(targets.tanggal, '%m-%Y') = '$bln' AND periode = 'bulan' AND jenis_target = 'kan'");
                                        })
                                    ->select(\DB::raw("tambahan.id, tambahan.unit, id_transaksi, targets.target, SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset"))
                                    ->where(function ($query) use ($k, $kota) {
                                        if ($k == null) {
                                            $query->where('transaksi.id_kantor', $kota);
                                        } else {
                                            $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $k->id);
                                        }
                                    })
                                    ->whereRaw("$approve AND $bayar")
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC')->get();
                                    
            $data['user'] = Transaksi::join('users', 'transaksi.id_koleks', '=', 'users.id')
                                ->leftjoin('targets',function($join) use ($bulannew, $tahunnew) {
                                    $join->on('users.id_karyawan' ,'=', 'targets.id_jenis')
                                        ->where('jenis_target','kar')
                                        ->where('periode','bulan')
                                        ->whereMonth('targets.tanggal', $bulannew)
                                        ->whereYear('targets.tanggal', $tahunnew);
                                })
                                ->where('transaksi.via_input', 'transaksi')
                                ->select(\DB::raw("users.id, users.name, transaksi.id_koleks as id, users.id_jabatan, targets.target as target_dana,
                                        SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset
                                    "))
    
                                ->where(function ($query) use ($k, $kota) {
                                        if ($k == null) {
                                            $query->where('transaksi.id_kantor', $kota)->where('users.id_kantor', $kota);
                                        } else {
                                            $query->whereRaw("(transaksi.id_kantor = '$kota' OR transaksi.id_kantor = '$k->id') AND (users.id_kantor = '$kota' OR users.id_kantor = '$k->id')");
                                        }
                                    })
                                
                                ->whereRaw("$approve AND $bayar AND users.aktif = 1")
                                
                                ->where('users.id_com', Auth::user()->id_com)
                                ->groupBy('name','transaksi.id_koleks','users.id_jabatan')->orderBy('transaksi.kolektor', 'ASC')->get();
        }
        
        
        return $data;
    }
    
    public function targetId(Request $req){
        $id = $req->id;
        $bln = date('m-Y');
        $bln2 = $bln;
        $approve = "transaksi.approval = 1";
        $bayar = "transaksi.pembayaran != 'mutasi'";
        
        $bulannew = date('m');
        $tahunnew = date('Y');
        
        $kot = "users.id_kantor IS NOT NULL";
        
        $kota = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', $kota)->where('id_com', Auth::user()->id_com)->first();
        
        if (Auth::user()->kolekting =='admin') {
            if($req->tipe == 'kantor'){
                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                ->selectRaw("tambahan.unit as names, tambahan.unit, transaksi.subprogram, transaksi.id_transaksi, transaksi.kolektor, transaksi.jumlah, transaksi.donatur, transaksi.pembayaran, transaksi.tanggal")
                                ->where('tambahan.id_com', Auth::user()->id_com)
                                ->whereRaw("$approve AND $bayar AND tambahan.id = '$id' AND transaksi.jumlah > 0 AND MONTH(tanggal) = '$bulannew' AND YEAR(tanggal) = '$tahunnew'")
                                ->get();
            }else{
                            
                $data = Transaksi::join('users', 'transaksi.id_koleks', '=', 'users.id')
                                    ->where('transaksi.via_input', 'transaksi')
                                    ->selectRaw("users.name as names, users.id, users.name, transaksi.subprogram, transaksi.id_transaksi, transaksi.kolektor, transaksi.jumlah, transaksi.donatur, transaksi.pembayaran, transaksi.tanggal")
                                    ->whereRaw("$approve AND $bayar AND users.aktif = 1 AND users.id = '$id' AND transaksi.jumlah > 0 AND MONTH(tanggal) = '$bulannew' AND YEAR(tanggal) = '$tahunnew'")
                                    ->where('users.id_com', Auth::user()->id_com)
                                    ->get();
            }
        }else{
            if($req->tipe == 'kantor'){
                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    
                                    ->selectRaw("tambahan.unit as names, tambahan.unit, transaksi.subprogram, transaksi.id_transaksi, transaksi.kolektor, transaksi.jumlah, transaksi.donatur, transaksi.pembayaran, transaksi.tanggal")
                                    ->where(function ($query) use ($k, $kota) {
                                        if ($k == null) {
                                            $query->where('transaksi.id_kantor', $kota);
                                        } else {
                                            $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $k->id);
                                        }
                                    })
                                    ->whereRaw("$approve AND $bayar AND tambahan.id = '$id' AND transaksi.jumlah > 0 AND MONTH(tanggal) = '$bulannew' AND YEAR(tanggal) = '$tahunnew'")
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->get();
            }else{
                                    
                $data = Transaksi::join('users', 'transaksi.id_koleks', '=', 'users.id')
                                
                                ->where('transaksi.via_input', 'transaksi')
                                ->selectRaw("users.name as names, users.id, users.name, transaksi.subprogram, transaksi.id_transaksi, transaksi.kolektor, transaksi.jumlah, transaksi.donatur, transaksi.pembayaran, transaksi.tanggal")
                                ->where(function ($query) use ($k, $kota) {
                                        if ($k == null) {
                                            $query->where('transaksi.id_kantor', $kota)->where('users.id_kantor', $kota);
                                        } else {
                                            $query->whereRaw("(transaksi.id_kantor = '$kota' OR transaksi.id_kantor = '$k->id') AND (users.id_kantor = '$kota' OR users.id_kantor = '$k->id')");
                                        }
                                    })
                                
                                ->whereRaw("$approve AND $bayar AND users.aktif = 1 AND users.id = '$id' AND transaksi.jumlah > 0 AND MONTH(tanggal) = '$bulannew' AND YEAR(tanggal) = '$tahunnew'")
                                
                                ->where('users.id_com', Auth::user()->id_com)
                                ->get();
            }
        }
        
        
        return $data;
    }
}
