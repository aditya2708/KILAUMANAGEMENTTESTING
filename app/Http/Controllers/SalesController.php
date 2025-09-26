<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserKolek;
use Auth;
use App\Models\Target;
use App\Models\Karyawan;
use App\Models\Transaksi;
use App\Models\Transaksi_Perhari;
use App\Models\User;
use App\Models\Donatur;
use App\Models\Kantor;
use App\Models\Tunjangan;
use App\Models\Program;
use App\Models\Prog;
use App\Models\Prosp;
use App\Models\LapFol;
use Carbon\Carbon;
use DataTables;
use Excel;
use App\Models\Jabatan;
use App\Exports\CapaianSalesExport;

class SalesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $tj = Tunjangan::first();
            
        $jabatan = Jabatan::whereRaw("(id = '$tj->sokotak' OR id = '$tj->so' OR id = '$tj->kolektor')")->get();
        
        if(request()->ajax()){
            $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
            $sampai = $request->sampaii == '' ? $dari : $request->sampaii;
            $unit = $request->unit == '' ? "id_kantor IS NOT NULL" : "id_kantor =  $request->unit";
            $bln = $request->bln == '' ? Carbon::now()->format('m-Y') : $request->bln;
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            // dd($request);
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
            
            // return($data->get());
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('total', function($data){
                $cuk1 = $data->open + $data->cancel + $data->closing;
                return $cuk1;
            })
            
            ->addColumn('names', function($data){
                $btn = '<a data-bs-toggle="modal"  class="dalwar" id="'.$data->id_peg.'" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa">'.$data->name.'</a>';
                return $btn;
            })
            
            ->addColumn('jabatan', function($data){
                $y = Jabatan::where('id', $data->id_jabatan)->first();
                if($y == null){
                    $btn = '';
                }else{
                    $btn = $y->jabatan;
                }
                return $btn;
            })
                      
            ->rawColumns(['total','names'])
            ->make(true);
        }
        
        return view('sales.capaian_sales', compact('jabatan')); 
    }
    
    public function salesExport(Request $request){
        
        if($request->tombol == 'xls'){
            $export = Excel::download(new CapaianSalesExport($request), 'capaiansales.xlsx');
            ob_end_clean();
            return $export;
        }else{
            $export = Excel::download(new CapaianSalesExport($request), 'capaiansales.csv');
            ob_end_clean();
            return $export;
        }
    }
    
    public function get_data_id(Request $request){
        // return($request);
        // if($request->ajax())
        // {
        $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
        $sampai = $request->sampaii == '' ? $dari : $request->sampaii;
            
            
        $bln = $request->bln == '' ? Carbon::now()->format('m-Y') : $request->bln;
        
        $kot = $request->kotas == "" ? "kota != ''" : "kota = '$request->kotas'";
        $kot2 = $request->kotas == "" ? "" : $request->kotas;
        
        // $kota = Auth::user()->kota;
        // $sum = UserKolek::get();
        
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        
        // $month = date("n",strtotime($dari));
        // $year = date("Y",strtotime($dari));
        
        $data = [];
        if($request->plhtgl == 0){
            $data['datranmod'] = LapFol::join('users', 'users.id','=','lap_folup.id_peg')
                                    ->join('donatur','lap_folup.id_don','=','donatur.id')
                                    ->join('prog','prog.id_program','=','lap_folup.id_prog')
                                    ->selectRaw("lap_folup.id_pros, donatur.nama, lap_folup.created_at, lap_folup.ket, lap_folup.id_prog, prog.program, lap_folup.id_don, lap_folup.tgl_fol, lap_folup.jenis")
                                    ->whereRaw("lap_folup.id_peg = '$request->id' AND DATE(lap_folup.created_at) >= '$dari' AND DATE(lap_folup.created_at) <= '$sampai'")
                                    ->get();
        }else{
            $data['datranmod'] = LapFol::join('users', 'users.id','=','lap_folup.id_peg')
                                    ->join('donatur','lap_folup.id_don','=','donatur.id')
                                    ->join('prog','prog.id_program','=','lap_folup.id_prog')
                                    ->selectRaw("lap_folup.id_pros, donatur.nama, lap_folup.created_at, lap_folup.ket, lap_folup.id_prog, prog.program, lap_folup.id_don, lap_folup.tgl_fol, lap_folup.jenis")
                                    ->whereRaw("lap_folup.id_peg = '$request->id' AND MONTH(lap_folup.created_at) = '$bulan' AND YEAR(lap_folup.created_at) = '$tahun'")
                                    ->get();
        }
        
        return $data;
    }
    
    public function get_lap_id($id, Request $request){
        if($request->ajax()){
            $data = LapFol::whereRaw("id_pros = '$id'")->get();
        }
        return $data;
    }
    
    public function sales()
    {
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        
        if(AUth::user()->level === 'admin'){
            $unit = Kantor::all(); 
        }else if(Auth::user()->level == ('kacab') | Auth::user()->level == ('keuangan cabang') | Auth::user()->level == ('agen')){
            if($k == null){
                $unit = Kantor::where('id',Auth::user()->id_kantor)->get();
            }else{
                $unit = Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->select('unit', 'id')->get();
                
            }
        }else if(Auth::user()->level == ('keuangan unit') | Auth::user()->level == ('spv')){
            $unit = Kantor::where('id',Auth::user()->id_kantor)->get();
        }
        $kets = Prosp::select('ket')->distinct()->get();
        
        return view('sales.sales',compact('unit','kets'));
    }
    
    public function get_sales_data(Request $request){
        if($request->ajax()){
            $unit = $request->unit != '' ? "donatur.id_kantor = $request->unit" : "donatur.id_kantor IS NOT NULL";
            $ketpros = $request->ketpros != '' ? "prosp.ket = '$request->ketpros'" : "prosp.ket IS NOT NULL";
            $stts = $request->stts != '' ? "prosp.status = $request->stts" : "prosp.status IS NOT NULL";
            
            $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
            $sampai = $request->sampai != '' ? $request->sampai : $dari; 
            $bln = $request->bln != '' ? $request->bln : date('m-Y');
            $bln1 = date('Y-m-01', strtotime('01-'.$bln));
            
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            $kot = Auth::user()->id_kantor;
            
            if(Auth::user()->level == ('admin') | Auth::user()->level == ('keuangan pusat') ){
                if($request->plhtgl == 0){
                    $data = Prosp::select(['prosp.konprog','prosp.status','prosp.tgl_fol','prosp.created_at','prosp.id_peg','prosp.id_don','prosp.id_prog','prosp.ket','prosp.id','donatur.nama','users.name','prog.program'])
                            ->join('donatur','donatur.id','=','prosp.id_don')
                            ->join('users','users.id','=','prosp.id_peg')
                            ->join('prog','prog.id_program','=','prosp.id_prog')
                            ->whereRaw("$unit AND $ketpros AND $stts AND DATE(prosp.created_at) >= '$dari' AND DATE(prosp.created_at) <= '$sampai'");
                }else{
                    $data = Prosp::select(['prosp.konprog','prosp.status','prosp.tgl_fol','prosp.created_at','prosp.id_peg','prosp.id_don','prosp.id_prog','prosp.ket','prosp.id','donatur.nama','users.name','prog.program'])
                            ->join('donatur','donatur.id','=','prosp.id_don')
                            ->join('users','users.id','=','prosp.id_peg')
                            ->join('prog','prog.id_program','=','prosp.id_prog')
                            ->whereRaw("$unit AND $ketpros AND $stts AND MONTH(prosp.created_at) = '$bulan' AND YEAR(prosp.created_at) = '$tahun'");
                }
            }else if(Auth::user()->level == ('kacab') | Auth::user()->level == ('keuangan cabang') | Auth::user()->level == ('agen')){
                if($k == null){
                    if($request->plhtgl == 0){
                        $data = Prosp::select(['prosp.konprog','prosp.status','prosp.tgl_fol','prosp.created_at','prosp.id_peg','prosp.id_don','prosp.id_prog','prosp.ket','prosp.id','donatur.nama','users.name','prog.program'])
                                ->join('donatur','donatur.id','=','prosp.id_don')
                                ->join('users','users.id','=','prosp.id_peg')
                                ->join('prog','prog.id_program','=','prosp.id_prog')
                                ->whereRaw("donatur.id_kantor = '$kot' AND $ketpros AND $stts AND DATE(prosp.created_at) >= '$dari' AND DATE(prosp.created_at) <= '$sampai'");
                    }else{
                        $data = Prosp::select(['prosp.konprog','prosp.status','prosp.tgl_fol','prosp.created_at','prosp.id_peg','prosp.id_don','prosp.id_prog','prosp.ket','prosp.id','donatur.nama','users.name','prog.program'])
                                ->join('donatur','donatur.id','=','prosp.id_don')
                                ->join('users','users.id','=','prosp.id_peg')
                                ->join('prog','prog.id_program','=','prosp.id_prog')
                                ->whereRaw("donatur.id_kantor = '$kot' AND $ketpros AND $stts AND MONTH(prosp.created_at) = '$bulan' AND YEAR(prosp.created_at) = '$tahun'");
                    } 
                }else{
                    if($request->unit != ''){
                        if($request->plhtgl == 0){
                            $data = Prosp::select(['prosp.konprog','prosp.status','prosp.tgl_fol','prosp.created_at','prosp.id_peg','prosp.id_don','prosp.id_prog','prosp.ket','prosp.id','donatur.nama','users.name','prog.program'])
                                    ->join('donatur','donatur.id','=','prosp.id_don')
                                    ->join('users','users.id','=','prosp.id_peg')
                                    ->join('prog','prog.id_program','=','prosp.id_prog')
                                    ->whereRaw("$unit AND $ketpros AND $stts AND DATE(prosp.created_at) >= '$dari' AND DATE(prosp.created_at) <= '$sampai'");
                        }else{
                            $data = Prosp::select(['prosp.konprog','prosp.status','prosp.tgl_fol','prosp.created_at','prosp.id_peg','prosp.id_don','prosp.id_prog','prosp.ket','prosp.id','donatur.nama','users.name','prog.program'])
                                    ->join('donatur','donatur.id','=','prosp.id_don')
                                    ->join('users','users.id','=','prosp.id_peg')
                                    ->join('prog','prog.id_program','=','prosp.id_prog')
                                    ->whereRaw("$unit AND $ketpros AND $stts AND MONTH(prosp.created_at) = '$bulan' AND YEAR(prosp.created_at) = '$tahun'");
                        }
                    }else{
                        if($request->plhtgl == 0){
                            $data = Prosp::select(['prosp.konprog','prosp.status','prosp.tgl_fol','prosp.created_at','prosp.id_peg','prosp.id_don','prosp.id_prog','prosp.ket','prosp.id','donatur.nama','users.name','prog.program'])
                                    ->join('donatur','donatur.id','=','prosp.id_don')
                                    ->join('users','users.id','=','prosp.id_peg')
                                    ->join('prog','prog.id_program','=','prosp.id_prog')
                                    ->whereRaw("donatur.id_kantor = '$kot' AND $ketpros AND $stts AND DATE(prosp.created_at) >= '$dari' AND DATE(prosp.created_at) <= '$sampai'")
                                    ->orWhereRaw("donatur.id_kantor = '$k->unit' AND $ketpros AND $stts AND DATE(prosp.created_at) >= '$dari' AND DATE(prosp.created_at) <= '$sampai'");
                        }else{
                            $data = Prosp::select(['prosp.konprog','prosp.status','prosp.tgl_fol','prosp.created_at','prosp.id_peg','prosp.id_don','prosp.id_prog','prosp.ket','prosp.id','donatur.nama','users.name','prog.program'])
                                    ->join('donatur','donatur.id','=','prosp.id_don')
                                    ->join('users','users.id','=','prosp.id_peg')
                                    ->join('prog','prog.id_program','=','prosp.id_prog')
                                    ->whereRaw("donatur.id_kantor = '$kot' AND $ketpros AND $stts AND MONTH(prosp.created_at) = '$bulan' AND YEAR(prosp.created_at) = '$tahun'")
                                    ->orWhereRaw("donatur.id_kantor = '$k->unit' AND $ketpros AND $stts AND MONTH(prosp.created_at) = '$bulan' AND YEAR(prosp.created_at) = '$tahun'");
                        }  
                    }   
                }
            }else if (Auth::user()->level == ('keuangan unit') | Auth::user()->level == ('spv')){
                if($request->plhtgl == 0){
                    $data = Prosp::select(['prosp.konprog','prosp.status','prosp.tgl_fol','prosp.created_at','prosp.id_peg','prosp.id_don','prosp.id_prog','prosp.ket','prosp.id','donatur.nama','users.name','prog.program'])
                            ->join('donatur','donatur.id','=','prosp.id_don')
                            ->join('users','users.id','=','prosp.id_peg')
                            ->join('prog','prog.id_program','=','prosp.id_prog')
                            ->whereRaw("donatur.id_kantor = '$kot' AND $ketpros AND $stts AND DATE(prosp.created_at) >= '$dari' AND DATE(prosp.created_at) <= '$sampai'");
                }else{
                    $data = Prosp::select(['prosp.konprog','prosp.status','prosp.tgl_fol','prosp.created_at','prosp.id_peg','prosp.id_don','prosp.id_prog','prosp.ket','prosp.id','donatur.nama','users.name','prog.program'])
                            ->join('donatur','donatur.id','=','prosp.id_don')
                            ->join('users','users.id','=','prosp.id_peg')
                            ->join('prog','prog.id_program','=','prosp.id_prog')
                            ->whereRaw("donatur.id_kantor = '$kot' AND $ketpros AND $stts AND MONTH(prosp.created_at) = '$bulan' AND YEAR(prosp.created_at) = '$tahun'");
                }
                            
            }
            
            
                    
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('tmbl', function($data){
                if ($data->status == 1){
                    $c = '<span class="label label-success label-xs" style="pointer: none">Aktif</span>';
                }else{
                    $c = '<span class="label label-danger label-xs" style="pointer: none">Nonaktif</span>';
                }
                
                return $c;
            })
            
            ->addColumn('transak', function($data){
                if($data->ket == 'closing'){
                    $c = '<span class="label label-info label-xs diwale" style="cursor: pointer" data-toggle="modal" data-target="#modaldonasi" id="'.$data->id.'">Lihat Transaksi Langsung</span>';
                }else{
                    $c = '<span class="label label-danger label-xs diwax" style="cursor: pointer" data-toggle="modal" data-target="#modaldo" id="'.$data->id.'">Lihat Laporan FolloW Up</span>';
                }
                return $c;
            })
            
            ->editColumn('btn', function($data){
                if ($data->konprog == 1){
                            $c = 'checked';
                        }else{
                            $c = '';
                        } 
                             
                $button = '<label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'))" id="checkbox" class="toggle-class" status="'.$data->konprog.'" data-id="'. $data->id . '"  data-value="'. $data->konprog . '" type="checkbox" '.( $data->konprog  == 1 ? "checked" : "").' /> <div class="slider round"> </div> </label>';
                return $button;
            })
            
            ->rawColumns(['tmbl','btn','transak'])
            ->make(true);
        }
    }
   
    public function change_stts_props(Request $request){
       
        $data = Prosp::where('id',$request->id)->first();
        
        $nama = Donatur::where('id',$data->id_don)->first();
    
        $aktif = $data->konprog;
    
        if($aktif == 1){
            Prosp::where('id',$request->id)->update([
                'konprog'=> 0
            ]);
            \LogActivity::addToLog(Auth::user()->name.' Disable Prospek '.$nama->nama);
        }else{
            Prosp::where('id',$request->id)->update([
                'konprog'=> 1,
            ]);
            \LogActivity::addToLog(Auth::user()->name.' Acc Prospek '.$nama->nama);
        }
      
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function transaksi_langsung($id, Request $request){
        $now = Carbon::now()->toDateString();
        if(request()->ajax())
        {
            $unit = $request->unit != '' ? "donatur.id_kantor = $request->unit" : "donatur.id_kantor IS NOT NULL";
            $ketpros = $request->ketpros != '' ? "prosp.ket = '$request->ketpros'" : "prosp.ket IS NOT NULL";
            $stts = $request->stts != '' ? "prosp.status = $request->stts" : "prosp.status IS NOT NULL";
            
            $data = Prosp::whereRaw("id = $id")->first();
            
            // $don = $data->id_don != NULL ? "id_donatur = $data->id_don" : "id_donatur IS NOT NULL";
            
            $get = Transaksi::whereRaw("id_donatur = $data->id_don AND DATE(created_at) = '$data->tgl_fol'")->get();
            
            return $get;
        }
    }
    
    public function laporan_folup($id, Request $request){
        $now = Carbon::now()->toDateString();
        if(request()->ajax())
        {
            $unit = $request->unit != '' ? "donatur.id_kantor = $request->unit" : "donatur.id_kantor IS NOT NULL";
            $ketpros = $request->ketpros != '' ? "prosp.ket = '$request->ketpros'" : "prosp.ket IS NOT NULL";
            $stts = $request->stts != '' ? "prosp.status = $request->stts" : "prosp.status IS NOT NULL";
            
            $data = Prosp::whereRaw("id = $id")->first();
            
            // $don = $data->id_don != NULL ? "id_donatur = $data->id_don" : "id_donatur IS NOT NULL";
            
            $get = LapFol::whereRaw("id_don = $data->id_don AND DATE(created_at) = '$data->tgl_fol'")->get();
            
            return $get;
        }
    }

}
