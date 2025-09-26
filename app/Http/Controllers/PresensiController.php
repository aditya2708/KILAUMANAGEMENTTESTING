<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kolektor;
use App\Models\Presensi;
use App\Models\Feedback;
use App\Models\Tambahan;
use App\Models\Jabatan;
use App\Models\Profile;
use App\Models\User;
use App\Models\Kantor;
use App\Models\Laporan;
use App\Models\Karyawan;
use App\Models\Tandatangan;
use App\Models\Donatur;
use App\Models\Jenreq;
use App\Models\RequestKar;
use App\Models\Pengumuman;
use App\Models\Company;
use App\Models\SaldoAw;
use App\Models\Transaksi_Perhari_All;
use App\Models\Prosp;
use App\Models\Prog;
use App\Models\ProgPerus;
use App\Models\Targets;
use App\Models\LinkParam;
use Carbon\Carbon;
use DB;
use Auth;
use DataTables;

// use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Exports\KehadiranKacabQueryExport;
use App\Exports\LaporanKExport;
use App\Exports\DaftarRExport;
use App\Exports\KehadiranExport;
use App\Exports\RekapKExport;
use App\Exports\DetailRekapExport;
use Excel;
use PDF;


class PresensiController extends Controller
{
   public function index(Request $request)
   {
        $dari = $request->dari == '' ? Carbon::now()->toDateString() : $request->dari;
        $ke = $request->ke == '' ? $dari : $request->ke;
        
        $id_com = $request->com;
        
        $jabatan = Jabatan::where('id_com', Auth::user()->id_com)->get();
        $kantor = Kantor::select('tambahan.id','tambahan.unit','karyawan.aktif')->join('karyawan','karyawan.id_kantor','=','tambahan.id')->where('aktif',1)->where('tambahan.id_com', Auth::user()->id_com)->distinct()->get();
        
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kot = Auth::user()->id_kantor;
        
        if(Auth::user()->level === 'admin' || Auth::user()->level === 'operator admin' || Auth::user()->kepegawaian === 'hrd'){
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get(); 
        }else if(Auth::user()->level == ('kacab') | Auth::user()->level == ('keuangan cabang') | Auth::user()->level == ('agen')){
            if($k == null){
                $kantor = Kantor::where('id',Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->get();
            }else{
                $kantor = Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->select('unit', 'id')->get();
                
            }
        }else if(Auth::user()->level == ('keuangan unit') | Auth::user()->level == ('spv') | Auth::user()->keuangan == ('keuangan cabang')){
            $kantor::where('id',Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->get();
        }
        
        $company = Profile::where(function($query) {
                        if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                            $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })
                    ->get();
      
        if($request->ajax()){
                //   dd($request);

            // dd($request->krywn);
        if($request->tglrange != '') {
                $tgl = explode(' s.d. ', $request->tglrange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
                // dd($tgl, $dari, $sampai);
            }
        $bln = $request->blns == '' ? Carbon::now()->format('m-Y') : $request->blns;
        
        $now = date('Y-m-d');
        
        $dat = [];
        $month = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $year = Carbon::createFromFormat('m-Y', $bln)->format('Y');
       
        if($request->plhtgl == 0){
            $tgls = $request->tglrange != '' ? "DATE(presensi.created_at) >= '$dari' AND DATE(presensi.created_at) <= '$sampai'" : "DATE(presensi.created_at) >= '$now' AND DATE(presensi.created_at) <= '$now'" ;
        }else{
            $tgls = "MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year'";
        }
       
        $krywn = $request->krywn != null ? "presensi.id_karyawan = '$request->krywn'" : "presensi.id_karyawan IS NOT NULL";
        // $kntr = $request->kantor != null ? "presensi.id_kantor = '$request->kantor'": "presensi.id_kantor IS NOT NULL";
        // $jab = $request->jabatan != null ? "presensi.id_jabatan IN ($request->jabatan)" : "presensi.id_jabatan IS NOT NULL";
        // $stat = $request->status != null ? "presensi.status = '$request->status'" : "presensi.status IS NOT NULL";
        
        $detailRekap =  $request->tab == 'rekap' ? "presensi.id_karyawan = '$request->id_karyawan'" : "presensi.id_karyawan IS NOT NULL";
        
        
        $jabatan = function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                if ($request->jabatan == '' || empty($request->jabatan)) {
                    $query->whereRaw("presensi.id_jabatan IS NOT NULL");
                } else {
                    $query->whereIn("presensi.id_jabatan", $request->jabatan);
                }
            });
        };
        
        
        $status = function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                if ($request->status == '' || empty($request->status)) {
                    $query->whereRaw("presensi.status IS NOT NULL");
                } else {
                    $query->whereIn("presensi.status", $request->status);
                }
            });
        };
        
        // $statusAktif = $request->aktif != '' ? "presensi.id_karyawan is not null": "presensi.id_karyawan is not null";
        $statusAktif = $request->aktif != '' ? "karyawan.aktif = '$request->aktif'": "karyawan.aktif = 1";
        
        if(Auth::user()->id_com != null ){
            if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                 $data = Presensi::selectRaw('presensi.*')
                        ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                        ->whereRaw("$krywn AND $tgls AND $detailRekap AND $statusAktif")
                        ->whereIn('presensi.id_karyawan', function($query) use ($id_com){
                            $query->select('id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })
                        ->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })
                        ->where($jabatan)
                        ->leftJoin('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                        ->where($status)
                        ->orderBy('presensi.created_at','desc');
                        
                        $datas = Presensi::selectRaw("presensi.id_karyawan, presensi.nama, jabatan.jabatan,
                        SUM(CASE WHEN presensi.status = 'Hadir' AND $tgls THEN 1 ELSE 0 END) AS jum_hadir,
                        SUM(CASE WHEN presensi.status = 'Terlambat' AND $tgls THEN 1 ELSE 0 END) AS jum_terlambat,
                        SUM(CASE WHEN presensi.status = 'Bolos' AND $tgls THEN 1 ELSE 0 END) AS jum_bolos,
                        SUM(CASE WHEN presensi.status = 'Sakit' AND $tgls THEN 1 ELSE 0 END) AS jum_sakit,
                        SUM(CASE WHEN presensi.status = 'Perdin' AND $tgls THEN 1 ELSE 0 END) AS jum_perdin,
                        SUM(CASE WHEN presensi.status = 'Cuti' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti,
                        SUM(CASE WHEN presensi.status = 'Cuti Penting' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti_penting")
                        ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                        ->join('jabatan','karyawan.jabatan','=','jabatan.id')
                        ->whereRaw("$tgls AND $krywn AND $detailRekap AND $statusAktif")
                        ->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->where($jabatan)->where($status)
                        ->groupBy('presensi.id_karyawan', 'presensi.nama', 'jabatan.jabatan')
                        ->whereIn('presensi.id_karyawan', function($query) use ($id_com){
                            $query->select('id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        });
                        
                        $dat['hadir'] = Presensi::whereRaw(" $krywn AND $tgls AND $statusAktif")->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')->where($jabatan)->where($status)->where('status', 'Hadir')->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('presensi.id_karyawan', function($query) use ($id_com){
                            $query->select('id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['sakit'] = Presensi::whereRaw(" $krywn AND $tgls AND $statusAktif")->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')->where($jabatan)->where($status)->where('status', 'Sakit')->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('presensi.id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['bolos'] = Presensi::whereRaw(" $krywn AND $tgls AND $statusAktif")->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')->where($jabatan)->where($status)->where('status', 'Bolos')->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('presensi.id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['perdin'] = Presensi::whereRaw(" $krywn AND $tgls AND $statusAktif")->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')->where($jabatan)->where($status)->where('status', 'Perdin')->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('presensi.id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['terlambat'] = Presensi::whereRaw(" $krywn AND $tgls AND $statusAktif")->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')->where($jabatan)->where($status)->where('status', 'Terlambat')->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('presensi.id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['cuti'] = Presensi::whereRaw(" $krywn AND $tgls AND $statusAktif")->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')->where($jabatan)->where($status)->where('status', 'Cuti')->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('presensi.id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['cuti_penting'] = Presensi::whereRaw("$krywn AND  $tgls AND $statusAktif")->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')->where($jabatan)->where($status)->where('status', 'Cuti Penting')->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('presensi.id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['total_hadir'] = Presensi::whereRaw(" $krywn AND $tgls AND $statusAktif")->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')->where($jabatan)->where($status)->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('presensi.id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                
            }
            else if(Auth::user()->kepegawaian == 'admin' || Auth::user()->kepegawaian == 'hrd' || Auth::user()->keuangan == 'keuangan pusat' || Auth::user()->level === 'operator pusat' ){
                
                        $data = Presensi::selectRaw('presensi.*')->whereRaw("$krywn AND $tgls AND $detailRekap")
                        ->whereIn('id_karyawan', function($query) use ($id_com){
                            $query->select('id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })
                        ->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })
                        ->where($jabatan)
                        ->where($status)
                        ->orderBy('created_at','desc');
                        
                        $datas = Presensi::selectRaw("presensi.id_karyawan, presensi.nama, jabatan.jabatan,
                        SUM(CASE WHEN presensi.status = 'Hadir' AND $tgls THEN 1 ELSE 0 END) AS jum_hadir,
                        SUM(CASE WHEN presensi.status = 'Terlambat' AND $tgls THEN 1 ELSE 0 END) AS jum_terlambat,
                        SUM(CASE WHEN presensi.status = 'Bolos' AND $tgls THEN 1 ELSE 0 END) AS jum_bolos,
                        SUM(CASE WHEN presensi.status = 'Sakit' AND $tgls THEN 1 ELSE 0 END) AS jum_sakit,
                        SUM(CASE WHEN presensi.status = 'Perdin' AND $tgls THEN 1 ELSE 0 END) AS jum_perdin,
                        SUM(CASE WHEN presensi.status = 'Cuti' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti,
                        SUM(CASE WHEN presensi.status = 'Cuti Penting' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti_penting")
                        ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                        ->join('jabatan','karyawan.jabatan','=','jabatan.id')
                        ->whereRaw("karyawan.aktif = 1 AND  $tgls AND $krywn AND $detailRekap")
                        ->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->where($jabatan)->where($status)
                        ->groupBy('presensi.id_karyawan', 'presensi.nama', 'jabatan.jabatan')
                        ->whereIn('presensi.id_karyawan', function($query) use ($id_com){
                            $query->select('id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        });
                        
                        $dat['hadir'] = Presensi::whereRaw(" $krywn AND $tgls")->where($jabatan)->where($status)->where('status', 'Hadir')->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('id_karyawan', function($query) use ($id_com){
                            $query->select('id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['sakit'] = Presensi::whereRaw(" $krywn AND $tgls")->where($jabatan)->where($status)->where('status', 'Sakit')->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['bolos'] = Presensi::whereRaw(" $krywn AND $tgls")->where($jabatan)->where($status)->where('status', 'Bolos')->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['perdin'] = Presensi::whereRaw(" $krywn AND $tgls")->where($jabatan)->where($status)->where('status', 'Perdin')->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['terlambat'] = Presensi::whereRaw(" $krywn AND $tgls")->where($jabatan)->where($status)->where('status', 'Terlambat')->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['cuti'] = Presensi::whereRaw(" $krywn AND $tgls")->where($jabatan)->where($status)->where('status', 'Cuti')->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['cuti_penting'] = Presensi::whereRaw("$krywn AND  $tgls")->where($jabatan)->where($status)->where('status', 'Cuti Penting')
                            ->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');
                        $dat['total_hadir'] = Presensi::whereRaw(" $krywn AND $tgls")->where($jabatan)->where($status)->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('id_karyawan', function($query) use ($id_com){
                            $query->select('karyawan.id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        })->sum('jumlah');

            }else if(Auth::user()->level== 'kacab' || Auth::user()->keuangan == 'keuangan cabang'){
                if($k == null){
                        $data = Presensi::selectRaw('presensi.*')
                                ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                                ->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND $detailRekap AND $statusAktif")
                                ->where(function($query) use ($request){
                                    if($request->kantor == '' || empty($request->kantor)){
                                        $query->whereRaw("presensi.id_kantor IS NOT NULL");
                                    }else{
                                        $query->where('presensi.id_kantor', $request->kantor);
                                    }
                                })->whereIn('presensi.id_karyawan', function($query) use ($statusAktif) {
                                        $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                                })->orderBy('created_at', 'desc');
                        
                        $datas = Presensi::where($jabatan)->where($status)->selectRaw("presensi.id_karyawan, presensi.nama, jabatan.jabatan,
                        SUM(IF(presensi.status = 'Hadir' AND $tgls, presensi.jumlah, 0)) AS jum_hadir,
                        SUM(IF(presensi.status = 'Terlambat' AND $tgls, presensi.jumlah, 0)) AS jum_terlambat,
                        SUM(CASE WHEN presensi.status = 'Bolos' AND $tgls THEN 1 ELSE 0 END) AS jum_bolos,
                        SUM(CASE WHEN presensi.status = 'Sakit' AND $tgls THEN 1 ELSE 0 END) AS jum_sakit,
                        SUM(CASE WHEN presensi.status = 'Perdin' AND $tgls THEN 1 ELSE 0 END) AS jum_perdin,
                        SUM(CASE WHEN presensi.status = 'Cuti' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti,
                        SUM(CASE WHEN presensi.status = 'Cuti Penting' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti_penting")
                        ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                        ->join('jabatan','karyawan.jabatan','=','jabatan.id')
                        ->whereRaw("$statusAktif AND   $tgls AND $krywn AND $detailRekap")
                        ->groupBy('presensi.id_karyawan', 'presensi.nama', 'jabatan.jabatan')
                        ->whereIn('presensi.id_karyawan', function($query) {
                                $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        });
                        
                       
                        $dat['hadir'] = Presensi::where($jabatan)->where($status)->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND $detailRekap")->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->where('presensi.id_kantor', $request->kantor);
                            }
                        })->where('status', 'Hadir')->whereIn('id_karyawan', function($query) {
                                $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
                        $dat['sakit'] = Presensi::where($jabatan)->where($status)->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND $detailRekap")->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->where('presensi.id_kantor', $request->kantor);
                            }
                        })->where('status', 'Sakit')->whereIn('id_karyawan', function($query) {
                                $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
                        $dat['bolos'] = Presensi::where($jabatan)->where($status)->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND $detailRekap")
                        ->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->where('presensi.id_kantor', $request->kantor);
                            }
                        })->where('status', 'Bolos')->whereIn('id_karyawan', function($query) {
                                $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
                        $dat['perdin'] = Presensi::where($jabatan)->where($status)->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND $detailRekap")->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->where('presensi.id_kantor', $request->kantor);
                            }
                        })->where('status', 'Perdin')->whereIn('id_karyawan', function($query) {
                                $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
                        $dat['terlambat'] = Presensi::where($jabatan)->where($status)->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND $detailRekap")->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->where('presensi.id_kantor', $request->kantor);
                            }
                        })->where('status', 'Terlambat')->whereIn('id_karyawan', function($query) {
                                $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
                        $dat['cuti'] = Presensi::where($jabatan)->where($status)->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND $detailRekap")->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->where('presensi.id_kantor', $request->kantor);
                            }
                        })->where('status', 'Cuti')->whereIn('id_karyawan', function($query) {
                                $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
                        $dat['cuti_penting'] = Presensi::where($jabatan)->where($status)->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND $detailRekap")->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->where('presensi.id_kantor', $request->kantor);
                            }
                        })->where('status', 'Cuti Penting')->whereIn('id_karyawan', function($query) {
                                $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
                        $dat['total_hadir'] = Presensi::where($jabatan)->where($status)->whereRaw("presensi.id_kantor = '$kot' AND  $krywn AND $tgls AND $detailRekap")->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->where('presensi.id_kantor', $request->kantor);
                            }
                        })->whereIn('id_karyawan', function($query) {
                                $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
        
                }else{
                    if($request->kantor != ''){
                        $data = Presensi::selectRaw('presensi.*')->where($jabatan)->where($status)->whereRaw("$tgls AND $detailRekap")
                            ->whereRaw("presensi.id_kantor = '$request->kantor'")
                        // ->where(function($query) use ($request){
                            // if($request->kantor == ''){
                            //     $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            // }else{
                            //     $query->whereIn('presensi.id_kantor', $request->kantor);
                            // }
                        // })
                        
                        ->whereIn('id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })
                        ->orderBy('created_at', 'desc');
                        
                            $datas = Presensi::where($jabatan)->where($status)->selectRaw("presensi.id_karyawan, presensi.nama, jabatan.jabatan,
                            SUM(CASE WHEN presensi.status = 'Hadir' AND $tgls THEN 1 ELSE 0 END) AS jum_hadir,
                            SUM(CASE WHEN presensi.status = 'Terlambat' AND $tgls THEN 1 ELSE 0 END) AS jum_terlambat,
                            SUM(CASE WHEN presensi.status = 'Bolos' AND $tgls THEN 1 ELSE 0 END) AS jum_bolos,
                            SUM(CASE WHEN presensi.status = 'Sakit' AND $tgls THEN 1 ELSE 0 END) AS jum_sakit,
                            SUM(CASE WHEN presensi.status = 'Perdin' AND $tgls THEN 1 ELSE 0 END) AS jum_perdin,
                            SUM(CASE WHEN presensi.status = 'Cuti' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti,
                            SUM(CASE WHEN presensi.status = 'Cuti Penting' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti_penting")
                            ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                            ->join('jabatan','karyawan.jabatan','=','jabatan.id')
                            ->whereRaw("$statusAktif AND   $tgls AND $krywn AND $detailRekap")
                            ->whereRaw("presensi.id_kantor = '$request->kantor'")
                            // ->where(function($query) use ($request){
                            //     if($request->kantor == '' || empty($request->kantor)){
                            //         $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            //     }else{
                            //         $query->whereIn("presensi.id_kantor", $request->kantor);
                            //     }
                            // })
                            ->groupBy('presensi.id_karyawan', 'presensi.nama', 'jabatan.jabatan')
                            
                            ->whereIn('presensi.id_karyawan', function($query) {
                                    $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                            });
                            
                           
                            $dat['hadir'] = Presensi::where($jabatan)->where($status)->whereRaw("  $tgls")->whereRaw("presensi.id_kantor = '$request->kantor'")
                            
                            // ->where(function($query) use ($request){
                            //     if($request->kantor == '' || empty($request->kantor)){
                            //         $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            //     }else{
                            //         $query->whereIn("presensi.id_kantor", $request->kantor);
                            //     }
                            // })
                            ->whereDate('created_at', '>=', $dari)->whereDate('created_at', '<=', $ke)->where('status', 'Hadir')->whereIn('id_karyawan', function($query) {
                                    $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                            })->sum('jumlah');
                            
                            
                            $dat['sakit'] = Presensi::where($jabatan)->where($status)->whereRaw("  $tgls")->whereRaw("presensi.id_kantor = '$request->kantor'")
                            // ->where(function($query) use ($request){
                            //     if($request->kantor == '' || empty($request->kantor)){
                            //         $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            //     }else{
                            //         $query->whereIn("presensi.id_kantor", $request->kantor);
                            //     }
                            // })
                            ->whereDate('created_at', '>=', $dari)->whereDate('created_at', '<=', $ke)->where('status', 'Sakit')
                            // ->where(function($query) use ($request){
                            //     if($request->kantor == '' || empty($request->kantor)){
                            //             $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            //         }else{
                            //             $query->whereIn("presensi.id_kantor", $request->kantor);
                            //         }
                            //     })
                            ->whereIn('id_karyawan', function($query) {
                                    $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                            })->sum('jumlah');
                            $dat['bolos'] = Presensi::where($jabatan)->where($status)->whereRaw("  $tgls")->whereRaw("presensi.id_kantor = '$request->kantor'")
                        //     ->where(function($query) use ($request){
                        //     if($request->kantor == '' || empty($request->kantor)){
                        //         $query->whereRaw("presensi.id_kantor IS NOT NULL");
                        //     }else{
                        //         $query->whereIn("presensi.id_kantor", $request->kantor);
                        //     }
                        // })
                        ->whereDate('created_at', '>=', $dari)->whereDate('created_at', '<=', $ke)->where('status', 'Bolos')->whereIn('id_karyawan', function($query) {
                                $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
                            $dat['perdin'] = Presensi::where($jabatan)->where($status)->whereRaw("  $tgls")->whereRaw("presensi.id_kantor = '$request->kantor'")
                        //     ->where(function($query) use ($request){
                        //     if($request->kantor == '' || empty($request->kantor)){
                        //         $query->whereRaw("presensi.id_kantor IS NOT NULL");
                        //     }else{
                        //         $query->whereIn("presensi.id_kantor", $request->kantor);
                        //     }
                        // })
                        ->whereDate('created_at', '>=', $dari)->whereDate('created_at', '<=', $ke)->where('status', 'Perdin')->whereIn('id_karyawan', function($query){
                                $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
                            $dat['terlambat'] = Presensi::where($jabatan)->where($status)->whereRaw("  $tgls")->whereRaw("presensi.id_kantor = '$request->kantor'")
                        //     ->where(function($query) use ($request){
                        //     if($request->kantor == '' || empty($request->kantor)){
                        //         $query->whereRaw("presensi.id_kantor IS NOT NULL");
                        //     }else{
                        //         $query->whereIn("presensi.id_kantor", $request->kantor);
                        //     }
                        // })
                        ->whereDate('created_at', '>=', $dari)->whereDate('created_at', '<=', $ke)->where('status', 'Terlambat')->whereIn('id_karyawan', function($query) {
                                $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
                            $dat['cuti'] = Presensi::where($jabatan)->where($status)->whereRaw("  $tgls")->whereRaw("presensi.id_kantor = '$request->kantor'")
                        //     ->where(function($query) use ($request){
                        //     if($request->kantor == '' || empty($request->kantor)){
                        //         $query->whereRaw("presensi.id_kantor IS NOT NULL");
                        //     }else{
                        //         $query->whereIn("presensi.id_kantor", $request->kantor);
                        //     }
                        // })
                        ->where('status', 'Cuti')->whereIn('id_karyawan', function($query) {
                                $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
                            $dat['cuti_penting'] = Presensi::where($jabatan)->where($status)->whereRaw("  $tgls")->whereRaw("presensi.id_kantor = '$request->kantor'")
                        //     ->where(function($query) use ($request){
                        //     if($request->kantor == '' || empty($request->kantor)){
                        //         $query->whereRaw("presensi.id_kantor IS NOT NULL");
                        //     }else{
                        //         $query->whereIn("presensi.id_kantor", $request->kantor);
                        //     }
                        // })
                        ->where('status', 'Cuti Penting')->whereIn('id_karyawan', function($query) {
                                $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
                            $dat['total_hadir'] = Presensi::where($jabatan)->where($status)->whereRaw("  $krywn AND $tgls")->whereRaw("presensi.id_kantor = '$request->kantor'")
                        //     ->where(function($query) use ($request){
                        //     if($request->kantor == '' || empty($request->kantor)){
                        //         $query->whereRaw("presensi.id_kantor IS NOT NULL");
                        //     }else{
                        //         $query->whereIn("presensi.id_kantor", $request->kantor);
                        //     }
                        // })
                        ->whereIn('id_karyawan', function($query) {
                                $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->sum('jumlah');
                    }else{
                            $data = Presensi::selectRaw('presensi.*')
                                    ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                                    ->where($jabatan)
                                    ->where($status)
                                    ->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND $detailRekap AND $statusAktif")
                                    ->orWhereRaw("presensi.id_kantor = '$k->id' AND  DATE(presensi.created_at) >= '$dari' AND DATE(presensi.created_at) <= '$ke'")->where(function($query) use ($request){
                                    if($request->kantor == '' || empty($request->kantor)){
                                        $query->whereRaw("presensi.id_kantor IS NOT NULL");
                                    }else{
                                        $query->whereIn("presensi.id_kantor", $request->kantor);
                                    }
                                })->whereIn('presensi.id_karyawan', function($query) {
                                        $query->select('karyawan.id_karyawan')->from('karyawan')->where('karyawan.id_com', Auth::user()->id_com);
                                })->orderBy('presensi.created_at', 'desc');
                                
                                    $datas = Presensi::where($jabatan)->where($status)->selectRaw("presensi.id_karyawan, presensi.nama, jabatan.jabatan,
                                    SUM(CASE WHEN presensi.status = 'Hadir' AND $tgls THEN 1 ELSE 0 END) AS jum_hadir,
                                    SUM(CASE WHEN presensi.status = 'Terlambat' AND $tgls THEN 1 ELSE 0 END) AS jum_terlambat,
                                    SUM(CASE WHEN presensi.status = 'Bolos' AND $tgls THEN 1 ELSE 0 END) AS jum_bolos,
                                    SUM(CASE WHEN presensi.status = 'Sakit' AND $tgls THEN 1 ELSE 0 END) AS jum_sakit,
                                    SUM(CASE WHEN presensi.status = 'Perdin' AND $tgls THEN 1 ELSE 0 END) AS jum_perdin,
                                    SUM(CASE WHEN presensi.status = 'Cuti' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti,
                                    SUM(CASE WHEN presensi.status = 'Cuti Penting' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti_penting")
                                    ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                                    ->join('jabatan','karyawan.jabatan','=','jabatan.id')
                                    ->where(function($query) use ($request){
                                    if($request->kantor == '' || empty($request->kantor)){
                                        $query->whereRaw("presensi.id_kantor IS NOT NULL");
                                    }else{
                                        $query->whereIn("presensi.id_kantor", $request->kantor);
                                    }
                                })->whereRaw("karyawan.aktif = 1 AND (presensi.id_kantor = '$kot' OR presensi.id_kantor = '$k->id') AND  $tgls AND $krywn AND $detailRekap")
                                    ->groupBy('presensi.id_karyawan', 'presensi.nama', 'jabatan.jabatan')
                                    ->whereIn('presensi.id_karyawan', function($query) {
                                            $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                                    });
                                    
                                   
                                    $dat['hadir'] = Presensi::where($jabatan)->where($status)->where(function($query) use ($request){
                                    if($request->kantor == '' || empty($request->kantor)){
                                        $query->whereRaw("presensi.id_kantor IS NOT NULL");
                                    }else{
                                        $query->whereIn("presensi.id_kantor", $request->kantor);
                                    }
                                })->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND status = 'Hadir'")->orWhereRaw("presensi.id_kantor = '$k->id' AND  $tgls AND status = 'Hadir'")->whereIn('id_karyawan', function($query) {
                                        $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                                })->sum('jumlah');
                                    $dat['sakit'] = Presensi::where($jabatan)->where($status)->where(function($query) use ($request){
                                    if($request->kantor == '' || empty($request->kantor)){
                                        $query->whereRaw("presensi.id_kantor IS NOT NULL");
                                    }else{
                                        $query->whereIn("presensi.id_kantor", $request->kantor);
                                    }
                                })->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND status = 'Sakit'")->orWhereRaw("presensi.id_kantor = '$k->id' AND  $tgls AND status = 'Sakit'")->whereIn('id_karyawan', function($query) {
                                        $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                                })->sum('jumlah');
                                    $dat['bolos'] = Presensi::where($jabatan)->where($status)->where(function($query) use ($request){
                                    if($request->kantor == '' || empty($request->kantor)){
                                        $query->whereRaw("presensi.id_kantor IS NOT NULL");
                                    }else{
                                        $query->whereIn("presensi.id_kantor", $request->kantor);
                                    }
                                })->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND status = 'Bolos'")->orWhereRaw("presensi.id_kantor = '$k->id' AND  $tgls AND status = 'Bolos'")->whereIn('id_karyawan', function($query) {
                                        $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                                })->sum('jumlah');
                                    $dat['perdin'] = Presensi::where($jabatan)->where($status)->where(function($query) use ($request){
                                    if($request->kantor == '' || empty($request->kantor)){
                                        $query->whereRaw("presensi.id_kantor IS NOT NULL");
                                    }else{
                                        $query->whereIn("presensi.id_kantor", $request->kantor);
                                    }
                                })->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND status = 'Perdin'")->orWhereRaw("presensi.id_kantor = '$k->id' AND  $tgls AND status = 'Perdin'")->whereIn('id_karyawan', function($query) {
                                        $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                                })->sum('jumlah');
                                    $dat['terlambat'] = Presensi::where($jabatan)->where($status)->where(function($query) use ($request){
                                    if($request->kantor == '' || empty($request->kantor)){
                                        $query->whereRaw("presensi.id_kantor IS NOT NULL");
                                    }else{
                                        $query->whereIn("presensi.id_kantor", $request->kantor);
                                    }
                                })->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND status = 'Terlambat'")->orWhereRaw("presensi.id_kantor = '$k->id' AND  $tgls AND status = 'Terlambat'")->whereIn('id_karyawan', function($query) {
                                        $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                                })->sum('jumlah');
                                    $dat['cuti'] = Presensi::where($jabatan)->where($status)->where(function($query) use ($request){
                                    if($request->kantor == '' || empty($request->kantor)){
                                        $query->whereRaw("presensi.id_kantor IS NOT NULL");
                                    }else{
                                        $query->whereIn("presensi.id_kantor", $request->kantor);
                                    }
                                })->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND status = 'Cuti'")->orWhereRaw("presensi.id_kantor = '$k->id' AND  $tgls AND status = 'Cuti'")->whereIn('id_karyawan', function($query) {
                                        $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                                })->sum('jumlah');
                                    $dat['cuti_penting'] = Presensi::where($jabatan)->where($status)->where(function($query) use ($request){
                                    if($request->kantor == '' || empty($request->kantor)){
                                        $query->whereRaw("presensi.id_kantor IS NOT NULL");
                                    }else{
                                        $query->whereIn("presensi.id_kantor", $request->kantor);
                                    }
                                })->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND status = 'Cuti Penting'")->orWhereRaw("presensi.id_kantor = '$k->id' AND  $tgls AND status = 'Cuti Penting'")->whereIn('id_karyawan', function($query) {
                                        $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                                })->sum('jumlah');
                                    $dat['total_hadir'] = Presensi::where($jabatan)->where($status)->where(function($query) use ($request){
                                    if($request->kantor == '' || empty($request->kantor)){
                                        $query->whereRaw("presensi.id_kantor IS NOT NULL");
                                    }else{
                                        $query->whereIn("presensi.id_kantor", $request->kantor);
                                    }
                                })->whereRaw("presensi.id_kantor = '$kot' AND  $krywn AND $tgls ")->orWhereRaw("presensi.id_kantor = '$k->id' AND  $tgls AND status = 'Cuti Penting'")->whereIn('id_karyawan', function($query) {
                                        $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                                })->sum('jumlah');
                    }
                    
                }
            }
        }

        
        if($request->tab == 'tab'){
                return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($data){
                    
                        if($data->status == 'Hadir'){
                        $wow = '<span class="badge badge badge-success light"><i class="fa fa-circle text-success me-1"></i>Hadir</span>';
                        }elseif($data->status == 'Terlambat'){
                            $wow = '<span class="badge badge badge-danger light"><i class="fa fa-circle text-danger me-1"></i>Terlambat</span>';
                        }elseif($data->status == 'Sakit'){
                            $wow = '<span class="badge badge badge-warning light "><i class="fa fa-circle text-warning me-1"></i>Sakit</span>';
                        }elseif($data->status == 'Perdin'){
                            $wow = '<span class="badge badge badge-info light"><i class="fa fa-circle text-info me-1"></i>Perjalanan Dinas</span>';
                        }elseif($data->status == 'Bolos'){
                            $wow = '<span class="badge badge badge-default light"><i class="fa fa-circle text-default me-1"></i>Bolos</span>';
                        }elseif($data->status == 'Cuti'){
                            $wow = '<span class="badge badge badge-primary light"><i class="fa fa-circle text-primary me-1"></i>Cuti</span>';
                        }elseif($data->status == 'Cuti Penting'){
                            $wow = '<span class="badge badge badge-primary  light"><i class="fa fa-circle text-primary me-1"></i>Cuti Penting</span>';
                        }else{
                            $wow = '<span class="label">Tidak ada Status</span>';
                        }
                    
                    
                    return $wow;  
                }) 
                ->addColumn('kelola', function($data){
                    $button = '<a href="javascript:void(0)" type="button" class="btn gett btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#exampleModal" id="'.$data->id_presensi.'"><i class="fa fa-eye"></i></a>';
                    // if($data->status == 'Sakit' || $data->status == 'Perdin'){
                    //     $button .= '&nbsp;&nbsp;&nbsp;<a href="'. url('gambarLampiran/'.$data->lampiran).'" class="btn btn-warning btn-sm" target="_blank">Lampiran</a>';
                    // }
                    return $button;
                })
                ->addColumn('tgl', function($data){
                    $button = $data->created_at->isoFormat('DD-MM-Y');
                    return $button;
                })
                ->addColumn('lambat', function($data){
                    $button = $data->keterlambatan .' menit';
                    return $button;
                })
                 ->addColumn('hari', function($data){
                    $button = $data->jumlah .' Hari';
                    return $button;
                })
                ->rawColumns(['status','kelola'])
                ->make(true);
            
        }
       
      
        if($request->tab == 'tab1'){
        // dd($datas->get());
            return DataTables::of($datas)
                    ->addColumn('namas', function($datas){
                        $trr = '<a data-bs-toggle="modal"  class="dalwar" id="'.$datas->id_karyawan.'" data-bs-target="#detailRekap" href="javascript:void(0)" style="color:#1f5daa" data="'.$datas->nama.'">'.$datas->nama.'</a>';
                        return $trr;
                    })
                    ->rawColumns(['status','kelola','namas'])
                    ->make(true);
        }
                    
        return $dat;
        }
       
       return view('presensi.kehadiran', compact('jabatan', 'kantor','company'));
   }
   
   public function detailPresensi($id, Request $request){
        $now = date('Y-m-d');
        if($request->daterange != '') {
            $tgl = explode(' s.d. ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
            // dd($tgl, $dari, $sampai);
        }
        $bln = $request->blns == '' ? Carbon::now()->format('m-Y') : $request->blns;
        $month = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $year = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        if($request->plhtgl == 0){
            $prd = $request->daterange != '' ? "DATE(presensi.created_at) >= '$dari' AND DATE(presensi.created_at) <= '$sampai'" : "DATE(presensi.created_at) >= '$now' AND DATE(presensi.created_at) <= '$now'" ;
        }else{
            $prd = $request->blns != '' ? "MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year'" : "MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year'";
        }
       
        
        $jab = $request->jabatan != null ? "presensi.id_jabatan = '$request->jabatan'" : "presensi.id_jabatan IS NOT NULL";
        $stat = $request->status != null ? "presensi.status = '$request->status'" : "presensi.status IS NOT NULL";
       
        $data = Presensi::selectRaw('*')->where('id_karyawan',$id)
                ->where(function($query) use ($request){
                    if($request->kantor == '' || empty($request->kantor)){
                        $query->whereRaw("presensi.id_kantor IS NOT NULL");
                    }else{
                        $query->whereIn("presensi.id_kantor", $request->kantor);
                    }
                })
                ->where(function($query) use ($request){
                    if($request->jabatan == '' || empty($request->jabatan)){
                        $query->whereRaw("presensi.id_jabatan IS NOT NULL");
                    }else{
                        $query->whereIn("presensi.id_jabatan", $request->jabatan);
                    }
                })
                ->whereRaw("$stat AND $prd")->get();
        
         return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($data){
                        if($data->status == 'Hadir'){
                        $wow = '<span class="badge badge badge-success light"><i class="fa fa-circle text-success me-1"></i>Hadir</span>';
                        }elseif($data->status == 'Terlambat'){
                            $wow = '<span class="badge badge badge-danger light"><i class="fa fa-circle text-danger me-1"></i>Terlambat</span>';
                        }elseif($data->status == 'Sakit'){
                            $wow = '<span class="badge badge badge-warning light "><i class="fa fa-circle text-warning me-1"></i>Sakit</span>';
                        }elseif($data->status == 'Perdin'){
                            $wow = '<span class="badge badge badge-info light"><i class="fa fa-circle text-info me-1"></i>Perjalanan Dinas</span>';
                        }elseif($data->status == 'Bolos'){
                            $wow = '<span class="badge badge badge-default light"><i class="fa fa-circle text-default me-1"></i>Bolos</span>';
                        }elseif($data->status == 'Cuti'){
                            $wow = '<span class="badge badge badge-primary light"><i class="fa fa-circle text-primary me-1"></i>Cuti</span>';
                        }elseif($data->status == 'Cuti Penting'){
                            $wow = '<span class="badge badge badge-primary  light"><i class="fa fa-circle text-primary me-1"></i>Cuti Penting</span>';
                        }else{
                            $wow = '<span class="label">Tidak ada Status</span>';
                        }
                    
                    
                    return $wow;  
                }) 
                ->addColumn('kelola', function($data){
                    $button = '<a href="javascript:void(0)" type="button" class="btn gett btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#exampleModal" id="'.$data->id_presensi.'"><i class="fa fa-eye"></i></a>';
                    // if($data->status == 'Sakit' || $data->status == 'Perdin'){
                    //     $button .= '&nbsp;&nbsp;&nbsp;<a href="'. url('gambarLampiran/'.$data->lampiran).'" class="btn btn-warning btn-sm" target="_blank">Lampiran</a>';
                    // }
                    return $button;
                })
                ->rawColumns(['status','kelola'])
                ->make(true);
   }
   
   public function exportk(Request $request){
        
        $now = date('Y-m-d');
        $monthNow = date('m-Y');
        if($request->daterange != '') {
                $tgl = explode(' s.d. ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
                // dd($tgl, $dari, $sampai);
        }
        $opsi = $request->plhtgl;
       
        if($opsi == 0){
            $a = 'Periode';
            if($request->daterange != '') {
                $b = $dari.' s.d '.$sampai;
            }else{
                $b = $now.' s.d '.$now;
            }
        }else{
            $a = 'Bulan';
            if($request->blns != '') {
                $b = $request->blns;
            }else{
                $b = $monthNow;
            }
        }
        
        if($request->tombol == 'xls'){
           $response = Excel::download(new KehadiranExport($request, $a, $b), 'daftar-kehadiran-karyawan-'.$a.'-'.$b.'.xlsx');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data Kehadiran Karyawan');
        }else if($request->tombol == 'csv'){
            $r = Excel::download(new KehadiranExport($request, $a, $b), 'daftar-kehadiran-karyawan-'.$a.'-'.$b.'.csv');
            ob_end_clean();
            return $r;
        }else if($request->tombol1 == 'xls'){
          $response = Excel::download(new KehadiranExport($request, $a, $b), 'rekap-kehadiran-karyawan-'.$a.'-'.$b.'.xlsx');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data Rekap Kehadiran Karyawan');
        }else if($request->tombol1 == 'csv'){
            $response = Excel::download(new KehadiranExport($request, $a, $b), 'rekap-kehadiran-karyawan-'.$a.'-'.$b.'.csv');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data Rekap Kehadiran Karyawan');
        }else if($request->tombol2 == 'xls'){
          $response = Excel::download(new KehadiranExport($request, $a, $b), 'detail-rekap-kehadiran- ' . $request->namaKaryawan .' -'.$a.'-'.$b.'.xlsx');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data Detail  Rekap Kehadiran ' . $request->namaKaryawan );
        }else if($request->tombol2 == 'csv'){
            $response = Excel::download(new KehadiranExport($request, $a, $b), 'detail-rekap-kehadiran- ' . $request->namaKaryawan .' -'.$a.'-'.$b.'.csv');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data Detail Rekap Kehadiran ' . $request->namaKaryawan);
        }
        return $response;
    }
    
   public function reqDet($id){
       $data = DB::table('request')->where('id_request', $id)->first();
       return response()->json($data);
   }
   
   public function acc($id){
       Presensi::where('id_presensi', $id)->update([
            'acc' => 1
       ]);
       return redirect('/kehadiran');
   }
   
    public function getjandk(Request $request){
        // return 'aha';
        $id_com = $request->com;
        $kntr = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', $kntr)->first();
        if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
            if($id_com > 0){
                 $data['karyawan'] = Karyawan::select('nama','id_karyawan')
                ->where('karyawan.aktif', 1)
                ->where('id_com', $id_com)
                ->get();
                $data['jabatan'] = Jabatan::where('id_com', $id_com)->get();
                $data['kota'] = Kantor::where('tambahan.id_com', $id_com)->where(function($query) use ($k, $kntr){
                                if(Auth::user()->level== 'kacab'){
                                    if($k == null){
                                        $query->where('id', Auth::user()->id_kantor);
                                    }else{
                                        $query->whereRaw("id = '$k->id' OR id = '$kntr'");
                                    }
                                }
                            })->get();    
            }else if($id_com == '0'){
                 $data['karyawan'] = Karyawan::select('nama','id_karyawan')
                ->where('karyawan.aktif', 1)
                ->where('id_com', $id_com)
                ->get();
                $data['jabatan'] = Jabatan::where(function($query) use ($id_com){ 
                                        $query->whereIn('id_com', function($q) {
                                            $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                        });
                                    })->get();
                $data['kota'] = Kantor::where(function($query) use ($id_com){ 
                                        $query->whereIn('id_com', function($q) {
                                            $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                        });
                                    })
                            ->where(function($query) use ($k, $kntr){
                                if(Auth::user()->level == 'kacab'){
                                    if($k == null){
                                        $query->where('id', Auth::user()->id_kantor);
                                    }else{
                                        $query->whereRaw("id = '$k->id' OR id = '$kntr'");
                                    }
                                }
                            })->get(); 
            }else{
                $data['karyawan'] = Karyawan::select('nama','id_karyawan')
            ->where('karyawan.aktif', 1)
            ->where('id_com', $id_com)
            ->get();
                $data['jabatan'] = Jabatan::where('id_com', Auth::user()->id_com)->get();
                $data['kota'] = Kantor::where('tambahan.id_com', Auth::user()->id_com)->where(function($query) use ($k, $kntr){
                                if(Auth::user()->level == 'kacab'){
                                    if($k == null){
                                        $query->where('id', Auth::user()->id_kantor);
                                    }else{
                                        $query->whereRaw("id = '$k->id' OR id = '$kntr'");
                                    }
                                }
                            })->get(); 
            }
            
        }
        return $data;
   }
   
   public function laporan(Request $request)
    {
        if ($request->tglrange != '') {
            $tgl = explode(' - ', $request->tglrange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
    
        if ($request->blns != '') {
            $tglsss = explode('-', $request->blns);
            $y = date('Y', strtotime($tglsss[1]));
            $m = date('m', strtotime($tglsss[0]));
        }
    
        $now = date('Y-m-d');
        $yearNow = date('Y');
        $monthNow = date('m');
    
        $kntr = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', $kntr)->first();
        $id_com = $request->com ?? null;
        $jabatan = Jabatan::where('id_com', Auth::user()->id_com)->get();
        $kota = Kantor::where('tambahan.id_com', Auth::user()->id_com)
            ->where(function ($query) use ($k, $kntr) {
                if (Auth::user()->level == 'kacab') {
                    if ($k == null) {
                        $query->where('id', Auth::user()->id_kantor);
                    } else {
                        $query->whereRaw("id = '$k->id' OR id = '$kntr'");
                    }
                }
            })->get();
    
        $company = Profile::where(function ($query) {
            if (Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1) {
                $query->where('id_hc', Auth::user()->id_com)
                      ->orWhere('id_com', Auth::user()->id_com);
            } else {
                $query->where('id_com', Auth::user()->id_com);
            }
        })->get();
    
        if ($request->plhtgl == '0') {
            $tgls = $request->tglrange != '' ? "DATE(laporan.created_at) >= '$dari' AND DATE(laporan.created_at) <= '$sampai'" : "DATE(laporan.created_at) >= '$now' AND DATE(laporan.created_at) <= '$now'";
        } else if ($request->plhtgl == '1') {
            // Jika memilih filter berdasarkan bulan
            if ($request->blns != '') {
                $tglsss = explode('-', $request->blns);
                $y = $tglsss[1];
                $m = $tglsss[0];
                $tgls = "MONTH(laporan.created_at) = '$m' AND YEAR(laporan.created_at) = '$y'";
            } else {
                $tgls = "MONTH(laporan.created_at) = '$monthNow' AND YEAR(laporan.created_at) = '$yearNow'";
            }
        }
    
        if ($request->ajax()) {
            if (Auth::user()->id_com != null) {
                $data = Laporan::selectRaw("laporan.created_at, jabatan.jabatan as jabatan, laporan.id_karyawan, laporan.nama, laporan.id_kantor, laporan.id_jabatan, laporan.id_laporan, '' as kelola")
                    ->join('jabatan', 'jabatan.id', '=', 'laporan.id_jabatan')
                    ->whereRaw("$tgls")
                    ->where(function ($query) use ($k, $kntr, $request) {
                        if (Auth::user()->level == 'kacab') {
                            if ($k == null) {
                                $query->where('id_kantor', Auth::user()->id_kantor);
                            } else {
                                if (empty($request->kota) || is_null($request->kota)) {
                                    // Kondisi ketika $request->kota kosong atau tidak didefinisikan
                                    $query->where("id_kantor", $k->id)
                                          ->orWhere('id_kantor', $kntr);
                                } else {
                                    // Kondisi ketika $request->kota memiliki nilai
                                    $query->whereIn('id_kantor', $request->kota);
                                }
                            }
                        } else {
                            if ($request->kota == '') {
                                $query->whereRaw("id_kantor IS NOT NULL");
                            } else {
                                $query->whereIn('id_kantor', $request->kota);
                            }
                        }
                    })
                    ->where(function ($query) use ($request) {
                        if ($request->jabatan == '' || empty($request->jabatan)) {
                            $query->whereRaw("id_jabatan IS NOT NULL");
                        } else {
                            $query->whereIn('id_jabatan', $request->jabatan);
                        }
                    })
                    ->where(function ($query) use ($request) {
                        if ($request->karyawan == '' || empty($request->karyawan)) {
                            $query->whereRaw("id_karyawan IS NOT NULL");
                        } else {
                            $query->whereIn('id_karyawan', $request->karyawan);
                        }
                    })
                    ->where(function ($query) use ($request) {
                        if ($request->search !== '' && !empty($request->search)) {
                            $searchTerm = '%' . $request->search . '%';
                            $query->where('laporan.nama', 'LIKE', $searchTerm)
                                  ->orWhere('id_karyawan', 'LIKE', $searchTerm)
                                  ->orWhere('jabatan', 'LIKE', $searchTerm);
                        }
                    })
                    ->whereIn('id_karyawan', function ($query) use ($id_com) {
                        $query->select('id_karyawan')
                              ->from('karyawan')
                              ->where(function ($query) use ($id_com) {
                                  if (Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1) {
                                      if ($id_com > 0) {
                                          $query->where('id_com', $id_com);
                                      } else if ($id_com == '0') {
                                          $query->whereIn('id_com', function ($q) {
                                              $q->select('id_com')
                                                ->from('company')
                                                ->where('id_hc', Auth::user()->id_com);
                                          });
                                      } else {
                                          $query->where('karyawan.id_com', Auth::user()->id_com);
                                      }
                                  } else {
                                      $query->where('karyawan.id_com', Auth::user()->id_com);
                                  }
                              });
                    })
                    ->orderBy('laporan.created_at', 'desc');
            }
    
            // Tambahan pengecekan: Jika user dengan id_jabatan 23 (SPV MARKOM) login,
            // batasi akses data laporan hanya untuk Digital Marketing (id_jabatan 10)
            if (Auth::user()->id_jabatan == 23) {
                $data = $data->where('laporan.id_jabatan', 10);
            }
    
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kelola', function ($data) {
                    $button = '<a href="javascript:void(0)" class="btn btn-sm btn-primary gett" data-nama="' . $data->nama . '" data-tanggal="' . $data->created_at . '" data-karyawan="' . $data->id_karyawan . '" id="' . $data->id_laporan . '" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-eye"></i></a>';
                    return $button;
                })
                ->rawColumns(['kelola'])
                ->make(true);
        }
    
        return view('presensi.laporan_karyawan', compact('jabatan', 'kota', 'company'));
    }

   
//   public function laporan(Request $request){
//         if($request->tglrange != '') {
//             $tgl = explode(' - ', $request->tglrange);
//             $dari = date('Y-m-d', strtotime($tgl[0]));
//             $sampai = date('Y-m-d', strtotime($tgl[1]));
//         }
        
//         if($request->blns != '') {
//             $tglsss = explode('-', $request->blns);
//             $y = date('Y', strtotime($tglsss[1]));
//             $m = date('m', strtotime($tglsss[0]));
//         }
        
//         $now = date('Y-m-d');
//         $yearNow = date('Y');
//         $monthNow = date('m');
        
            
//         $kntr = Auth::user()->id_kantor;
//         $k = Kantor::where('kantor_induk', $kntr)->first();
//         $id_com = $request->com ?? null;
//         $jabatan = Jabatan::where('id_com', Auth::user()->id_com)->get();
//         $kota = Kantor::where('tambahan.id_com', Auth::user()->id_com)->where(function($query) use ($k, $kntr){
//                         if(Auth::user()->level == 'kacab'){
//                             if($k == null){
//                                 $query->where('id', Auth::user()->id_kantor);
//                             }else{
//                                 $query->whereRaw("id = '$k->id' OR id = '$kntr'");
//                             }
//                         }
//                     })->get(); 
       
//       $company = Profile::where(function($query) {
//                         if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
//                             $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
//                         }else{
//                             $query->where('id_com', Auth::user()->id_com);
//                         }
//                     })->get();
//         // $jabat = $request->jabatan != '' ? "karyawan.id_jabatan IN ($request->jabatan)" : "karyawan.id_jabatan IS NOT NULL";
//         // $kota = $request->kota != '' ? "laporan.id_kantor = '$request->kota'" : "laporan.id_kantor IS NOT NULL";
                 
//         if($request->plhtgl == '0'){
//             $tgls = $request->tglrange != '' ? "DATE(laporan.created_at) >= '$dari' AND DATE(laporan.created_at) <= '$sampai'" : "DATE(laporan.created_at) >= '$now' AND DATE(laporan.created_at) <= '$now'" ;
//         }else if($request->plhtgl == '1'){
//           // Jika memilih filter berdasarkan bulan
//             if ($request->blns != '') {
//                 $tglsss = explode('-', $request->blns);
//                 $y = $tglsss[1];
//                 $m = $tglsss[0];
//                 $tgls = "MONTH(laporan.created_at) = '$m' AND YEAR(laporan.created_at) = '$y'";
//             } else {
//                 $tgls = "MONTH(laporan.created_at) = '$monthNow' AND YEAR(laporan.created_at) = '$yearNow'";
//             }           
//         }
       
//       if($request->ajax())
//       {
//             // $karyawan = DB::table('karyawan')->selectRaw('nama, id_karyawan')->whereIn('jabatan', $request->jabatan)->get();
//             // $jabat = $request->jabatan != '' ? "laporan.id_jabatan IN ($request->jabatan)" : "laporan.id_jabatan IS NOT NULL";
//             // $kota = $request->kota != '' ? "laporan.id_kantor = '$request->kota'" : "laporan.id_kantor IS NOT NULL";
//             // $kota = $request->kota;
//             if(Auth::user()->id_com != null ){
//                 $data = Laporan::selectRaw("laporan.created_at, jabatan.jabatan as jabatan,laporan.id_karyawan,laporan.nama,laporan.id_kantor,laporan.id_jabatan,laporan.id_laporan, '' as kelola")->join('jabatan','jabatan.id','=','laporan.id_jabatan')
//                 ->whereRaw("$tgls")
//                 ->where(function($query) use ($k, $kntr, $request){
//                     if(Auth::user()->level == 'kacab'){
//                         if($k == null){
//                             $query->where('id_kantor', Auth::user()->id_kantor);
//                         }else{
//                           if (empty($request->kota) || is_null($request->kota)) {
//                                 // Kondisi ketika $request->kota kosong atau tidak didefinisikan (undefined)
//                                 $query->where("id_kantor", $k->id)->orWhere('id_kantor', $kntr);
//                             } else {
//                                 // Kondisi ketika $request->kota memiliki nilai
//                                 $query->whereIn('id_kantor', $request->kota);
//                             }
//                         }
//                     }else{
//                         if($request->kota == ''){
//                             $query->whereRaw("id_kantor IS NOT NULL");
//                         }else{
//                             $query->whereIn('id_kantor', $request->kota);
//                         }
//                     }
//                 })
//                 ->where(function($query) use ( $request){
//                     if($request->jabatan == '' || empty($request->jabatan)){
//                         $query->whereRaw("id_jabatan IS NOT NULL");
//                     }else{
//                         $query->whereIn('id_jabatan', $request->jabatan);
//                     }
//                 })
//                 ->where(function($query) use ( $request){
//                     if($request->karyawan == '' || empty($request->karyawan)){
//                         $query->whereRaw("id_karyawan IS NOT NULL");
//                     }else{
//                         $query->whereIn('id_karyawan', $request->karyawan);
//                     }
//                 })
//                 ->where(function($query) use ($request){
//                     if ($request->search !== '' && !empty($request->search)) {
//                         $searchTerm = '%' . $request->search . '%';
//                         $query->where('laporan.nama', 'LIKE', $searchTerm)
//                               ->orWhere('id_karyawan', 'LIKE', $searchTerm)
//                               ->orWhere('jabatan', 'LIKE', $searchTerm);
//                         // Anda dapat menambahkan lebih banyak kolom sesuai kebutuhan
//                     }
//                 })
//                 ->whereIn('id_karyawan', function($query) use ($id_com){
//                     $query->select('id_karyawan')
//                             ->from('karyawan')
//                             ->where(function($query) use ($id_com){
//                                 if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
//                                     if($id_com > 0){
//                                         $query->where('id_com', $id_com);
//                                     }else if($id_com == '0'){
//                                         $query->whereIn('id_com', function($q) {
//                                             $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
//                                         });
//                                     }else{
//                                          $query->where('karyawan.id_com', Auth::user()->id_com);
//                                     } 
//                                 }else{
//                                     $query->where('karyawan.id_com', Auth::user()->id_com);
//                                 }
//                             });
                    
//                 })
//                 ->orderBy('laporan.created_at', 'desc');
//             }
//          return DataTables::of($data)
//                 ->addIndexColumn()
//                 ->addColumn('kelola', function($data){
//                     $button = '<a href="javascript:void(0)" class="btn btn-sm btn-primary gett" data-nama="'.$data->nama.'" data-tanggal="'.$data->created_at.'" data-karyawan="'.$data->id_karyawan.'" id="'.$data->id_laporan.'" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-eye"></i></a>';
//                     return $button;
//                 })
//                 ->rawColumns(['kelola'])
//                 ->make(true);
//             // return ($karyawan);
//       }   
       
//       return view('presensi.laporan_karyawan',compact('jabatan','kota','company'));
//   }
   
   public function kardinamis (Request $request){
       $id_com = isset($request->com) ? $request->com : Auth::user()->id_com;
       if(isset($request->com)){
        $user = Karyawan::select('nama','id_karyawan')
            ->where(function($query) use ($request){
                    if(!empty($request->kota)){
                        $query->whereIn('id_kantor', $request->kota);
                    }
                    
                    if(!empty($request->jabatan)){
                        $query->whereIn('jabatan', $request->jabatan);
                    }
                })
            ->where('karyawan.aktif', 1)
            ->where('id_com', $id_com)
            ->get();
       }else{
        $user = Karyawan::select('nama','id_karyawan')
            // ->where(function($query) use ($request){
            //         if(!empty($request->kota)){
            //             $query->whereIn('id_kantor', $request->kota);
            //         }
                    
            //         if(!empty($request->jabatan)){
            //             $query->whereIn('jabatan', $request->jabatan);
            //         }
            //     })
            ->where('karyawan.aktif', 1)
            ->where('id_com', Auth::user()->id_com)
            ->get();
       }
        return $user;
   }
   
   public function getCapaianSet(Request $req){
        $id     = $req->id_kar;
        $u      = User::where('id_karyawan', $id)->first();
        $tgl    = date('Y-m-d', strtotime($req->tgl));
        $tgl_d  = date('Y-m-01', strtotime($tgl));
        $tgl_a  = date('Y-m-t', strtotime($tgl));
        
        $omprog = Prog::leftjoin("transaksi",function($join) use ($u, $tgl_d, $tgl_a){
                        $join->on('prog.id_program', '=', 'transaksi.id_program')
                            ->whereDate('transaksi.tanggal', '>=', $tgl_d)
                            ->whereDate('transaksi.tanggal', '<=', $tgl_a)
                            ->where('transaksi.approval', '>', 0)
                            ->where('transaksi.id_koleks', $u->id);
                        })
                        ->selectRaw("prog.id_program, prog.program, SUM(transaksi.jumlah) AS capbulan, SUM(IF(transaksi.tanggal = '$tgl', jumlah, 0)) AS caphari")
                        ->where('parent', 'n')
                        ->where('aktif', 'y')
                        ->groupBy('prog.id_program', 'prog.program')
                        ->get()
                        ;
                        
        $data       = [];  
        $i          = 0;
        $totbulan   = 0;
        $tothari    = 0;
        foreach($omprog as $v){
            $tar    = ProgPerus::whereDate('tanggal', $tgl_d)->where('id_kar', $id)->where('id_program', $v->id_program)->first();
            if($tar != null || $v->capbulan > 0){
            $i          += 1;
            $totbulan   += $v->capbulan;
            $tothari    += $v->caphari;
            $data[]     = [
                            'id_program'    => $v->id_program,
                            'program'       => $v->program,
                            'target'        => $tar != null ? number_format($tar->target, 0, ',' , '.') : 0,
                            'capbulan'      => number_format($v->capbulan, 0, ',' , '.'),
                            'caphari'       => number_format($v->caphari, 0, ',' , '.'),
                            'kontot'        => 0
                            ];
            }
        }   
            $tardan     = Targets::whereDate('tanggal', $tgl_d)->where('id_jenis', $id)->where('jenis_target', 'kar')->first();
            $data[$i]   = [
                            'id_program'    => 0,
                            'program'       => 'Total',
                            'target'        => $tardan != null ? number_format($tardan->target, 0, ',' , '.') : 0,
                            'capbulan'      => number_format($totbulan, 0, ',' , '.'),
                            'caphari'       => number_format($tothari, 0, ',' , '.'),
                            'kontot'        => 1
                            ]; 
        return $data;
   }
   
   public function exportlk(Request $request){
        // return($request);
       
       if($request->daterange != '') {
                $tgl = explode(' - ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
        }else{
            $dari = date('Y-m-d');
            $sampai = date('Y-m-d');
        }
        
        if($request->blns != ''){
            $bulan = $request->blns;
        }else{
            $bulan = date('m-Y');
        }
        if($request->plhtgl == '0'){
            $waktu = 'Periode ' . $dari .' sd '. $sampai;
        }else if($request->plhtgl == '1'){
            $waktu = 'Bulan ' . $bulan; 
        }
        if($request->tombol == 'xls'){
          $response = Excel::download(new LaporanKExport($request, $waktu), 'laporan-karyawan-'.$waktu.'.xlsx');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data Laporan Karyawan');
        }else if($request->tombol == 'csv'){
          $response = Excel::download(new LaporanKExport($request, $waktu), 'laporan-karyawan'.$waktu.'.csv');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data Laporan Karyawan');
        }
        return $response;
   }
   
   public function ambilkirim($id){
        if(request()->ajax())
        {   
            $get = Laporan::findOrFail($id);
            $data = [
                'feed' => Feedback::where('id_laporan', $get->id_laporan)->get(),
                'lap' => $get,
                'feedd' => Feedback::where('id_laporan', $get->id_laporan)->count(),
            ];
            // dd($data);
            return response()->json(['result' => $data]);
        }
    }
    
    public function ambilkirimdong($id){
        if(request()->ajax())
        {   
            $data =  Presensi::where('id_presensi',$id)->orderBy('created_at', 'desc')->first();
            $tipe = User::where('id_karyawan', $data->id_karyawan)->first()->jenis;
            // dd($data);
            return response()->json(['result' => $data, 'tipe' => $tipe]);
        }
    }
    
    public function eksdatakacab(Request $request)
    {
        // $tgl = $request->tgl;
        // // $nmunit = $request->unit != '' ? $getkntr->unit :'semua-unit-kerja';
        // // $unit = $request->unit != '' ? $request->unit :'semua-unit-kerja';
        // $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        // if($k == null){
        //     $unit = Auth::user()->id_kantor();
        //     $getkntr = Kantor::where('id',$unit)->first();
        // }else{
        //     $unit = Auth::user()->id_kantor();
        //     $getkntr = Kantor::where('id',$unit)->first();
        // }
        // $bulan = Carbon::createFromFormat('m-Y', $tgl)->format('m');
        // $tahun = Carbon::createFromFormat('m-Y', $tgl)->format('Y');
        // return Excel::download(new GajiQueryExport($unit, 789$bulan, $tahun), $tgl.'-kehadiran-'.$getkntr->unit.'.xlsx');
    }
    
    public function get_karyawan_nih( Request $request){
        if(request()->ajax())
        {         
        $jabatan = function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                if ($request->jabatan == '' || empty($request->jabatan)) {
                    $query->whereRaw("jabatan IS NOT NULL");
                } else {
                    $query->whereIn("jabatan", $request->jabatan);
                }
            });
        };
        
            // $kots = $request->kots == "" ? "id_kantor IS NOT NULL": "id_kantor = '$request->kots'" ;
            $jabs = $request->jabs == "" ? "jabatan IS NOT NULL": "jabatan = '$request->jabs'" ;
            $aktif = $request->aktif == "" ? "aktif IS NOT NULL": "aktif = '$request->aktif'" ;
            $coa_parent= Karyawan::whereRaw("$aktif")
            ->where(function($query) use ($request){
                if($request->kantor == '' || empty($request->kantor)){
                    $query->whereRaw("id_kantor IS NOT NULL");
                }else{
                    $query->whereIn("id_kantor", $request->kantor);
                }
            })
            ->where($jabatan)
            ->where('id_com', Auth::user()->id_com)->orderBy('nama', 'asc')->get();
            
            // return($coa_parent);
        
            foreach($coa_parent as $key => $val){
                $jabat = Kantor::where('id',$val->id_kantor)->where('id_com', Auth::user()->id_com)->first()->unit;
                $h1[] = [
                    "text" => $val->nama,
                    "nama" => $val->nama,
                    "unit" => $jabat,
                    "id" => $val->id_karyawan,
                    'statuss' => $val->aktif == 1 ? 'Aktif' : 'Nonaktif'
                ];
            }
        }
        return response()->json($h1);
        
        // return($request);
        // $q = $request->search;
        // $data = Karyawan::where(function($query) use ($q) {
        //             $query->where('nama', 'LIKE', '%'.$q.'%');
        //         })
        //         // ->where('jabatan',$jbt)
        //         ->get();
        // if (count($data) > 0) {
        //     //  $list = array();
        //      foreach($data as $key => $val){
        //          $jabat = Kantor::where('id',$val->id_kantor)->first()->unit;
        //         //  dd($val->id_jabatan);
        //          $list[] = [
        //                 "text" => $val->nama,
        //                 "nama" => $val->nama,
        //                 "unit" => $jabat,
        //                 "id" => $val->id_karyawan,
        //                 'statuss' => $val->aktif == 1 ? 'Aktif' : 'Nonaktif'
                        
        //             ];
        //      }
        //      return json_encode($list);
        //  } else {
        //      return "hasil kosong";
        //  }
    }
    
    public function daftar_req(Request $request)
    {
        $jabatan = Jabatan::where('id_com', Auth::user()->id_com)->get();
        $status = DB::table('request')->select('status')->distinct()->get();
        
        $company = Profile::where(function($query) {
                        if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                            $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })->get();
        
        if ($request->ajax()) {
            $id_com = $request->com;
            if($request->tglrange != '') {
                $tgl = explode(' - ', $request->tglrange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }

            $now = date('Y-m-d');
            $tgls = $request->tglrange != '' ? "DATE(request.created_at) >= '$dari' AND DATE(request.created_at) <= '$sampai'" : "DATE(request.created_at) IS NOT NULL AND DATE(request.created_at) IS NOT NULL" ;

            $jabat = $request->jabatan != '' ? "request.id_jabatan = '$request->jabatan'" : "request.id_jabatan IS NOT NULL";
            $kota = $request->kota != '' ? "request.id_kantor = '$request->kota'" : "request.id_kantor IS NOT NULL";
            $stts = $request->status != '' ? "request.status = '$request->status'" : "request.status IS NOT NULL";
            $kett = $request->kett != '' ? "request.acc = '$request->kett'" : "request.acc IS NOT NULL";
            
            $kntr = Auth::user()->id_kantor;
            $k = Kantor::where('kantor_induk', $kntr)->first();
            // dd($request);
            if(Auth::user()->id_com != null ){
                // if(Auth::user()->kepegawaian == 'hrd' || Auth::user()->kepegawaian == 'admin' || Auth::user()->keuangan == 'keuangan pusat'){
                    $data = RequestKar::select('request.*', 'jabatan.jabatan')->join('jabatan', 'jabatan.id', '=', 'request.id_jabatan')
                        ->whereRaw("$jabat AND $tgls AND $stts AND $kett")
                        // ->where('request.id_com', $id_com)
                        ->where(function($query) use ($k, $kntr, $request){
                            if(Auth::user()->level == 'kacab'){
                                if($k == null){
                                    $query->where('id_kantor', Auth::user()->id_kantor);
                                }else{
                                    if($request->kota == ''){
                                        $query->whereRaw("id_kantor = '$k->id' OR id_kantor = '$kntr'");
                                    }else{
                                         $query->where('id_kantor', $request->kota);
                                    }
                                }
                            }else{
                                if($request->kota == ''){
                                    $query->whereRaw("id_kantor IS NOT NULL");
                                }else{
                                    $query->where('id_kantor', $request->kota);
                                }
                            }
                        })
                        
                        ->whereIn('id_karyawan', function($query) use ($id_com){
                            $query->select('id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
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
                                    });
                            
                        });
                    
                    if($request->tab == 'approveAll'){
                        foreach($data->get() as $val){
                            $tanggal = date('Y-m-d', strtotime($val->tg_mulai));
                            $jam = date('H:i:s');
                            
                            $datareq = RequestKar::find($val->id_request);
                            $datareq->acc = 1;
                            $datareq->alasan = null;
                            $datareq->user_update = Auth::user()->id;
                            $datareq->update();
                            
                            $pres = Presensi::where('id_reqbolos', $val->id_request)->get();
                 
                            if(count($pres) > 0){
                                $up_pres = Presensi::where('id_reqbolos', $val->id_request)->update(['ket' => $datareq->ket, 'status' => $datareq->status]);
                            }else{
                                if ($val->status == 'Pulang Awal') {
                                    $data = Presensi::find($val->id_presensi);
                                    $data->id_request = $val->id_request;
                                    $data->cek_out = $jam;
                                    $data->acc = 1;
                        
                                    $data->update();
                                    \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $data->nama . ' Pulang Awal');
                                } else if ($val->status == 'Dinas Luar') {
                                    $data = Presensi::find($val->id_presensi);
                                    $data->id_request = $val->id_request;
                        
                                    $data->update();
                                    \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $data->nama . ' Dinas Luar');
                                } else if ($val->status == 'Perdin' && $val->id_presensi != '') {
                                    $data = Presensi::find($val->id_presensi);
                                    $data->id_request = $val->id_request;
                                    $data->acc = 1;
                        
                                    $data->update();
                                    \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $data->nama . ' Perdin');
                                } else if ($val->status == 'Pindah Shift') {
                        
                                    User::where('id_shift', $datareq->id_shift)->update(['id_shift' => NULL, 'acc_shift' => 1]);
                                    Presensi::where('id_shift', $datareq->id_shift)->update(['acc_shift' => 1]);
                        
                                    \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $val->nama . ' Pindah Shift');
                                } else {
                                    $datam = new Presensi;
                                    $datam->id_karyawan = $val->id_karyawan;
                                    $datam->id_jabatan = $val->id_jabatan;
                                    $datam->pr_jabatan = $val->pr_jabatan;
                                    $datam->id_kantor = $val->id_kantor;
                                    $datam->kantor_induk = $val->kantor_induk;
                                    $datam->nama = $val->nama;
                                    // $data->cek_in = $jam;
                                    $datam->ket = $val->ket;
                                    $datam->status = $val->status;
                                    $datam->jumlah = $val->jumlah;
                                    $datam->created_at = $tanggal . ' ' . $jam;
                                    $datam->lampiran = $val->lampiran;
                                    $datam->id_req = $val->id_request;
                                    $datam->acc = 1;
                        
                                    if ($val->status == 'Perdin') {
                                        $datam->foto = $val->foto;
                                        $datam->latitude = $val->latitude;
                                        $datam->longitude = $val->longitude;
                                    }
                                    $datam->save();
                                    // \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $request->nama . ' ' . $request->status);
                                }
                            }
                        }
                        return response()->json(['success' => 'berhasil']);
                    }
                    
                    if($request->tab == 'rejectAll'){
                        foreach($data->get() as $val){
                            
                            $datareq = RequestKar::find($val->id_request);
                            $datareq->alasan = $val->alasan;
                            $datareq->acc = 2;
                            $datareq->user_update = Auth::user()->id;
                            $datareq->update();
                            
                            $pres = Presensi::where('id_reqbolos', $val->id_request)->get();
                            if(count($pres) > 0){
                                $up_pres = Presensi::where('id_reqbolos', $id)->update(['ket' => '(Request '.$datareq->status.' Ditolak Dengan Alasan : '.$datareq->alasan.') | '.$datareq->ket, 'status' => 'Bolos']);
                            }else{
                                if ($val->acc == 1 || $val->acc == 2) {
                                    if ($val->status == 'Pulang Awal') {
                                        $data = Presensi::find($val->id_presensi);
                                        $data->id_request = null;
                                        $data->cek_out = null;
                                        $data->acc = 0;
                        
                                        $data->update();
                                        \LogActivity::addToLog(Auth::user()->name . ' Menolak Request ' . $val->nama . ' Pulang Awal');
                                    } else if ($val->status == 'Dinas Luar') {
                                        $data = Presensi::find($val->id_presensi);
                                        $data->id_request = null;
                        
                                        $data->update();
                                        \LogActivity::addToLog(Auth::user()->name . ' Menolak Request ' . $val->nama . ' Dinas Luar');
                                    } else if ($val->status == 'Pindah Shift') {
                                    
                                        User::where('id_karyawan', $datareq->id_karyawan)->update(['id_shift' => $datareq->id_shift, 'acc_shift' => 2]);
                                        Presensi::where('id_shift', $datareq->id_shift)->update(['acc_shift' => 2]);
                        
                                        \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $val->nama . ' Pindah Shift');
                                    } else if ($val->status == 'Perdin' && $val->id_presensi != '') {
                                        $data = Presensi::find($val->id_presensi);
                                        $data->id_request = null;
                                        $data->acc = 0;
                        
                                        $data->update();
                                        \LogActivity::addToLog(Auth::user()->name . ' Menolak Request ' . $val->nama . ' Perdin');
                                    } else {
                                        $data = Presensi::where('id_req', $val->id_request)->delete();
                                    }
                                }
                            }
                    
                         }
                        return response()->json(['success' => 'berhasil']);
                    }
                    
                        
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('konfirm', function ($data) {
                    if ($data->acc == 1) {
                        $button = '<span class="badge badge-success">Acc<span class="ms-1 fa fa-check"></span>';
                        // <span class="badge badge-success light"><i class="fa fa-circle text-success me-1"></i>Acc</span>';
                    }elseif($data->acc == 0){
                        $button = '<span class="badge badge-warning">Pending<span class="ms-1 fa fa-check"></span>';
                    }else {
                        $button = '<span class="badge badge-danger">Rejected<span class="ms-1 fas fa-ban"></span>';
                        // <span class="badge badge-warning light"><i class="fa fa-circle text-warning me-1"></i>Pending</span>';
                    }
                    return $button;
                })
                ->addColumn('kelola', function ($data) {
                    $button = '<a href="javascript:void(0)" class="btn btn-primary btn-xs gett" id="' . $data->id_request . '" data-bs-toggle="modal" data-bs-target="#rincian"><i class="fa fa-eye"></i></a>';
                    return $button;
                })
                ->addColumn('tgl', function ($data) {
                    $button = date('Y-m-d', strtotime($data->created_at));
                    return $button;
                })
                
                ->addColumn('stts', function($data){
                    if($data->status == 'Hadir'){
                        $wow = '<span class="badge badge badge-success light"><i class="fa fa-circle text-success me-1"></i>Hadir</span>';
                    }elseif($data->status == 'Terlambat'){
                        $wow = '<span class="badge badge badge-danger light"><i class="fa fa-circle text-danger me-1"></i>Terlambat</span>';
                    }elseif($data->status == 'Sakit'){
                        $wow = '<span class="badge badge badge-primary light "><i class="fa fa-circle text-primary me-1"></i>Sakit</span>';
                    }elseif($data->status == 'Perdin'){
                        $wow = '<span class="badge badge badge-info light"><i class="fa fa-circle text-info me-1"></i>Perjalanan Dinas</span>';
                    }elseif($data->status == 'Bolos'){
                        $wow = '<span class="badge badge badge-danger light"><i class="fa fa-circle text-danger me-1"></i>Bolos</span>';
                    }elseif($data->status == 'Cuti'){
                        $wow = '<span class="badge badge badge-primary light"><i class="fa fa-circle text-primary me-1"></i>Cuti</span>';
                    }elseif($data->status == 'Cuti Penting'){
                        $wow = '<span class="badge badge badge-primary  light"><i class="fa fa-circle text-primary me-1"></i>Cuti Penting</span>';
                    }elseif($data->status == 'Pulang Awal'){
                        $wow = '<span class="badge badge badge-warning  light"><i class="fa fa-circle text-warning me-1"></i>Pulang Awal</span>';
                    }elseif($data->status == 'Dinas Luar'){
                        $wow = '<span class="badge badge badge-info light"><i class="fa fa-circle text-info me-1"></i>Dinas Luar</span>';
                    }else{
                        $wow = '<span class="badge badge badge-light  light"><i class="fa fa-circle text-dark me-1"></i>Pindah Shift</span>';
                    }
                    return $wow;
                })
                
                ->rawColumns(['konfirm', 'kelola','stts'])
                ->make(true);
        }

        return view('presensi.daftar_request', compact('jabatan','status','company'));
    }
    
    public function accmilih(Request $request){
        $id = $request->id[0]['id'];
        
        $data = RequestKar::whereIn('id_request', $id);
        // dd($id,$data->get());
        foreach($data->get() as $val){
                $tanggal = date('Y-m-d', strtotime($val->tg_mulai));
                $jam = date('H:i:s');
                
                $datareq = RequestKar::find($val->id_request);
                $datareq->acc = 1;
                $datareq->alasan = null;
                $datareq->user_update = Auth::user()->id;
                $datareq->update();
                
                $pres = Presensi::where('id_reqbolos', $val->id_request)->get();
                
            if(count($pres) > 0){
                $up_pres = Presensi::where('id_reqbolos', $val->id_request)->update(['ket' => $datareq->ket, 'status' => $datareq->status]);
            }else{
                if ($val->status == 'Pulang Awal') {
                    $data = Presensi::find($val->id_presensi);
                    $data->id_request = $val->id_request;
                    $data->cek_out = $jam;
                    $data->acc = 1;
        
                    $data->update();
                    \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $data->nama . ' Pulang Awal');
                } else if ($val->status == 'Dinas Luar') {
                    $data = Presensi::find($val->id_presensi);
                    $data->id_request = $val->id_request;
        
                    $data->update();
                    \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $data->nama . ' Dinas Luar');
                } else if ($val->status == 'Perdin' && $val->id_presensi != '') {
                    $data = Presensi::find($val->id_presensi);
                    $data->id_request = $val->id_request;
                    $data->acc = 1;
        
                    $data->update();
                    \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $data->nama . ' Perdin');
                } else if ($val->status == 'Pindah Shift') {
        
                    User::where('id_shift', $datareq->id_shift)->update(['id_shift' => NULL, 'acc_shift' => 1]);
                    Presensi::where('id_shift', $datareq->id_shift)->update(['acc_shift' => 1]);
        
                    \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $val->nama . ' Pindah Shift');
                } else {
                    $datam = new Presensi;
                    $datam->id_karyawan = $val->id_karyawan;
                    $datam->id_jabatan = $val->id_jabatan;
                    $datam->pr_jabatan = $val->pr_jabatan;
                    $datam->id_kantor = $val->id_kantor;
                    $datam->kantor_induk = $val->kantor_induk;
                    $datam->nama = $val->nama;
                    // $data->cek_in = $jam;
                    $datam->ket = $val->ket;
                    $datam->status = $val->status;
                    $datam->jumlah = $val->jumlah;
                    $datam->created_at = $tanggal . ' ' . $jam;
                    $datam->lampiran = $val->lampiran;
                    $datam->id_req = $val->id_request;
                    $datam->acc = 1;
        
                    if ($val->status == 'Perdin') {
                        $datam->foto = $val->foto;
                        $datam->latitude = $val->latitude;
                        $datam->longitude = $val->longitude;
                    }
                    $datam->save();
                    // \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $request->nama . ' ' . $request->status);
                }
            }
        }

        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function rejectmilih(Request $request){
         $id = $request->id[0]['id'];
        
        $data = RequestKar::whereIn('id_request', $id);
        // dd($id,$data->get());
        foreach($data->get() as $val){
                            
            $datareq = RequestKar::find($val->id_request);
            $datareq->alasan = $val->alasan;
            $datareq->acc = 2;
            $datareq->user_update = Auth::user()->id;
            $datareq->update();
            
            $pres = Presensi::where('id_reqbolos', $val->id_request)->get();
            if(count($pres) > 0){
                $up_pres = Presensi::where('id_reqbolos', $id)->update(['ket' => '(Request '.$datareq->status.' Ditolak Dengan Alasan : '.$datareq->alasan.') | '.$datareq->ket, 'status' => 'Bolos']);
            }else{
                if ($val->acc == 1 || $val->acc == 2) {
                    if ($val->status == 'Pulang Awal') {
                        $data = Presensi::find($val->id_presensi);
                        $data->id_request = null;
                        $data->cek_out = null;
                        $data->acc = 0;
        
                        $data->update();
                        \LogActivity::addToLog(Auth::user()->name . ' Menolak Request ' . $val->nama . ' Pulang Awal');
                    } else if ($val->status == 'Dinas Luar') {
                        $data = Presensi::find($val->id_presensi);
                        $data->id_request = null;
        
                        $data->update();
                        \LogActivity::addToLog(Auth::user()->name . ' Menolak Request ' . $val->nama . ' Dinas Luar');
                    } else if ($val->status == 'Pindah Shift') {
                    
                        User::where('id_karyawan', $datareq->id_karyawan)->update(['id_shift' => $datareq->id_shift, 'acc_shift' => 2]);
                        Presensi::where('id_shift', $datareq->id_shift)->update(['acc_shift' => 2]);
        
                        \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $val->nama . ' Pindah Shift');
                    } else if ($val->status == 'Perdin' && $val->id_presensi != '') {
                        $data = Presensi::find($val->id_presensi);
                        $data->id_request = null;
                        $data->acc = 0;
        
                        $data->update();
                        \LogActivity::addToLog(Auth::user()->name . ' Menolak Request ' . $val->nama . ' Perdin');
                    } else {
                        $data = Presensi::where('id_req', $val->id_request)->delete();
                    }
                }
            }
    
         }
        
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function exportdr(Request $request){
      
      
       if($request->daterange != '') {
            $tgl = explode(' - ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
        $now = date('Y-m-d');
        $idcoms = $request->com;
        $datesss = $request->daterange == '' ? $now : $request->daterange;
        $tgls = $request->daterange != '' ? "DATE(request.created_at) >= '$dari' AND DATE(request.created_at) <= '$sampai'" : "DATE(request.created_at) >= '$now' AND DATE(request.created_at) <= '$now'" ;
        $jabat = $request->jabat != '' ? "request.id_jabatan = '$request->jabatan'" : "request.id_jabatan IS NOT NULL";
        $kota = $request->unit != '' ? "request.id_kantor = '$request->kota'" : "request.id_kantor IS NOT NULL";
        $stts = $request->status != '' ? "request.status = '$request->status'" : "request.status IS NOT NULL";
        $kett = $request->kett != '' ? "request.acc = '$request->kett'" : "request.acc IS NOT NULL";
        $id_com = $idcoms != '' ? "karyawan.id_com = '$idcoms'" : "karyawan.id_com IS NOT NULL";

        if($request->daterange != '') {
            $a = $dari.' s.d '.$sampai;
        }else{
            $a = $now.' s.d '.$now;
        }
        
        if(Auth::user()->level == 'kacab'){
            $kot = Auth::user()->id_kantor;
            $k = Kantor::where('kantor_induk', $kot)->where('id_com', Auth::user()->id_com)->first()->id;
        }else{
            $k = 'kosong';
        }
        $req = $request->kantor != '' ? $request->kantor : 'kosong';
        // dd($request);
        
        if($request->tombol == 'xls'){
            $response = Excel::download(new DaftarRExport($request), 'daftar-request-karyawan-periode'.$a.'.xlsx');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data Request Karyawan');
        }else if($request->tombol == 'csv'){
            $response = Excel::download(new DaftarRExport($request), 'daftar-request-karyawan-periode'.$a.'.csv');
            ob_end_clean();
            \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data Request Karyawan');
        }

        // $response = Excel::download(new DaftarRExport($tgls, $jabat, $kota, $stts, $kett, $k, $req,$id_com), 'daftar-request-karyawan-periode'.$a.'.xlsx');
        // ob_end_clean();
        // \LogActivity::addToLog(Auth::user()->name . ' Mengekspor Data Request Karyawan');
        return $response;
    }

    public function get_rinreq($id, Request $request)
    {
        if ($request->ajax()) {
            $data = RequestKar::select('request.*', 'jabatan.jabatan', 'karyawan.nomerhp')->join('jabatan', 'jabatan.id', '=', 'request.id_jabatan')->join('karyawan', 'karyawan.id_karyawan', '=', 'request.id_karyawan')->whereRaw("id_request = '$id'")->first();
            return response()->json($data);
        }
    }

    public function konfirm(Request $request, $id)
    {
        $data = RequestKar::where('id_request', $id)->first();
        $presensi = Presensi::where('id_presensi', $data->id_presensi)->first();

        $datareq = RequestKar::find($id);
        
    
        $datareq->acc = 1;
        $datareq->alasan = null;
        $datareq->user_update = Auth::user()->id;
        $datareq->update();
        
        $pres = Presensi::where('id_reqbolos', $id)->get();
        if(count($pres) > 0){
            $up_pres = Presensi::where('id_reqbolos', $id)->update(['ket' => $data->ket, 'status' => $data->status]);
        }else{
    
            $tanggal = date('Y-m-d', strtotime($data->tg_mulai));
            $jam = date('H:i:s');
            
            // return($data->status);
            if ($data->status == 'Pulang Awal') {
                $data = Presensi::find($data->id_presensi);
                $data->id_request = $id;
                $data->cek_out = $jam;
                $data->acc = 1;
    
                $data->update();
                \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $data->nama . ' Pulang Awal');
            } else if ($data->status == 'Dinas Luar') {
                $data = Presensi::find($data->id_presensi);
                $data->id_request = $id;
    
                $data->update();
                \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $data->nama . ' Dinas Luar');
            } else if ($data->status == 'Perdin' && $data->id_presensi != '') {
                $data = Presensi::find($data->id_presensi);
                $data->id_request = $id;
                $data->acc = 1;
    
                $data->update();
                \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $data->nama . ' Perdin');
            } else if ($data->status == 'Pindah Shift') {
    
                User::where('id_shift', $datareq->id_shift)->update(['id_shift' => NULL, 'acc_shift' => 1]);
                Presensi::where('id_shift', $datareq->id_shift)->update(['acc_shift' => 1]);
    
                \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $data->nama . ' Pindah Shift');
            } else {
                $datam = new Presensi;
                $datam->id_karyawan = $data->id_karyawan;
                $datam->id_jabatan = $data->id_jabatan;
                $datam->pr_jabatan = $data->pr_jabatan;
                $datam->id_kantor = $data->id_kantor;
                $datam->kantor_induk = $data->kantor_induk;
                $datam->nama = $data->nama;
                // $data->cek_in = $jam;
                $datam->ket = $data->ket;
                $datam->status = $data->status;
                $datam->jumlah = $data->jumlah;
                $datam->created_at = $tanggal . ' ' . $jam;
                $datam->lampiran = $data->lampiran;
                $datam->id_req = $id;
                $datam->acc = 1;
    
                if ($data->status == 'Perdin') {
                    $datam->foto = $data->foto;
                    $datam->latitude = $data->latitude;
                    $datam->longitude = $data->longitude;
                }
                $datam->save();
                \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $request->nama . ' ' . $request->status);
            }
        }

        return response()->json(['success' => 'berhasil']);
    }
    
    public function post_feedback(Request $request)
    {
        // return($request);
        // $data = Laporan::where('id_laporan', $request->id_lap)->first();
        $data = User::where('id_karyawan',Auth::user()->id_karyawan)->first();
        // return($data);
        
        $datam = new Feedback;
        $datam->id_karyawan = $data->id_karyawan;
        $datam->id_jabatan = $data->id_jabatan;
        $datam->id_kantor = $data->id_kantor;
        $datam->id_laporan = $request->id_lap;
        $datam->pr_jabatan = $data->pr_jabatan;
        $datam->kantor_induk = $data->kantor_induk;
        $datam->nama_atasan = $data->name;
        $datam->feedback = $request->feeds;
        $datam->sec_vn = NULL;
        $datam->vn = NULL;
        $datam->baca = 0;
        $datam->save();
        return response()->json(['success' => 'berhasil']);
    }

    public function tolak($id)
    {
        $datareq = RequestKar::find($id);
        
        $pres = Presensi::where('id_reqbolos', $id)->get();
        $get_req = RequestKar::where('id_request', $id)->first();
            
        if(count($pres) > 0){
            $up_pres = Presensi::where('id_reqbolos', $id)->update(['ket' => '(Request '.$datareq->status.' Ditolak Dengan Alasan : '.$datareq->alasan.') | '.$datareq->ket, 'status' => 'Bolos']);
        }else{
            
            $presensi = Presensi::where('id_presensi', $get_req->id_presensi)->first();
    
            if ($datareq->acc == 1) {
                if ($get_req->status == 'Pulang Awal') {
                    $data = Presensi::find($get_req->id_presensi);
                    $data->id_request = null;
                    $data->cek_out = null;
                    $data->acc = 0;
    
                    $data->update();
                    \LogActivity::addToLog(Auth::user()->name . ' Menolak Request ' . $get_req->nama . ' Pulang Awal');
                } else if ($get_req->status == 'Dinas Luar') {
                    $data = Presensi::find($get_req->id_presensi);
                    $data->id_request = null;
    
                    $data->update();
                    \LogActivity::addToLog(Auth::user()->name . ' Menolak Request ' . $get_req->nama . ' Dinas Luar');
                } else if ($get_req->status == 'Pindah Shift') {
    
                    User::where('id_karyawan', $datareq->id_karyawan)->update(['id_shift' => $datareq->id_shift, 'acc_shift' => 2]);
                    Presensi::where('id_shift', $datareq->id_shift)->update(['acc_shift' => 2]);
    
                    \LogActivity::addToLog(Auth::user()->name . ' Menyetujui Request ' . $get_req->nama . ' Pindah Shift');
                } else if ($get_req->status == 'Perdin' && $get_req->id_presensi != '') {
                    $data = Presensi::find($get_req->id_presensi);
                    $data->id_request = null;
                    $data->acc = 0;
    
                    $data->update();
                    \LogActivity::addToLog(Auth::user()->name . ' Menolak Request ' . $get_req->nama . ' Perdin');
                } else {
                    $data = Presensi::where('id_req', $id)->delete();
                }
            }
        }

        $datareq->alasan = $get_req->alasan;
        $datareq->acc = 2;
        $datareq->user_update = Auth::user()->id;
        $datareq->update();

        return response()->json(['success' => 'berhasil']);
    }
    
     
    // PENGUMUMAN
    public function kar_pengumuman(Request $request){
        // return $request;
        $user = User::select('name','id')
            ->whereIn('id_kantor', $request->id_kantor)
            // ->whereIn('id_jabatan', $request->jabatan)
            ->where('aktif', 1)
            ->get();
        return $user;
    }
    
    public function daftar_pengumuman(Request $request){
      
        $user = User::select('name','id')->get();
        $kantor = Kantor::select('unit','id','kantor_induk')
                ->where('id_com', Auth::user()->id_com)
                ->where(function($query) {
                        if(Auth::user()->kepegawaian != 'admin'){
                            $query->where('id', Auth::user()->id_kantor)
                            ->orWhere('kantor_induk', Auth::user()->id_kantor);
                        }
                    })
                ->get();
        // dd($kan);
        if($request->ajax()){
            
            $dari = $request->tgl_awal?? "1990-01-01";
            $sampai = $request->tgl_akhir?? date('Y-m-d');
            $tgl = "DATE(tgl_awal) >= '$dari' AND DATE(tgl_akhir) <= '$sampai'";
            // dd($dari,$sampai);
            $idc = Auth::user()->id_com;
            if($request->com == '' ){
                $id_com = "id_com = '$idc'";
            }else if($request->com == '0' ){
                $id_com = "id_com IS NULL OR id_com > 0";
            }else{
                $id_com = "id_com = '$request->com'";
            }
            // dd($id_com);
            $kantor1 = Kantor::select('unit','id','kantor_induk')
                ->whereRaw($id_com)
                ->where(function($query) {
                        if(Auth::user()->kepegawaian != 'admin'){
                            $query->where('id', Auth::user()->id_kantor)
                            ->orWhere('kantor_induk', Auth::user()->id_kantor);
                        }
                    })
                ->get();
                
            // $arus = $request->id_us == 'N;' ? [] : unserialize($request->id_user);
            
            $kan = [];
            foreach($kantor1 as $m => $n){
                $kan[] = $n->id;
            }
            
            
            // $user = User::select('name','id')->whereIn('id', $request->id_kantor)
            //     ->get();
            $filkan = !$request->kota ? $kan : $request->kota;
            $filjen = !$request->jenis ? [] : $request->jenis;
            // $filawal = !$request->tgl_awal ? [] : $request->tgl_awal;
            // $filakhir = !$request->tgl_akhir ? [] : $request->tgl_akhir;
            // return($kantor);
            $peng = Pengumuman::orderBy('created_at','desc')
                    ->whereRaw("$id_com AND $tgl")
                    ->get();
            // dd($peng);
            if (!empty($filjen)) {
                $peng = $peng->whereIn('jenis', $filjen);
            }else{
                $peng = $peng->whereNotNull('jenis');
            }
            
            $data = [];
         
            $id = [];
            if(count($peng) > 0){
                // dd('ddd');
                foreach($peng as $x => $v){
                    $un = unserialize($v->id_kantor);
                    // if(count($filkan) > 0){
                        foreach($un as $i => $d){
                                if(in_array($d, $filkan) && !in_array( $v->id, $id)) {
                                    $id[] = $v->id;
                                    $data[] = [
                                        'id' => $v->id,
                                        'isi' => $v->isi,
                                        'jenis' => $v->jenis,
                                        'tgl_awal' => $v->tgl_awal,
                                        'tgl_akhir' => $v->tgl_akhir,
                                        'jam_awal' => $v->jam_awal,
                                        'jam_akhir' => $v->jam_akhir,
                                        'id_kantor' => unserialize($v->id_kantor),
                                        'id_com' => $v->id_com,
                                        'id_user' => unserialize($v->id_user),
                                    ];
                                }
                        }
                    // }else{
                    //     $data[] = [
                    //         'id' => $v->id,
                    //         'isi' => $v->isi,
                    //         'jenis' => $v->jenis,
                    //         'tgl_awal' => $v->tgl_awal,
                    //         'tgl_akhir' => $v->tgl_akhir,
                    //         'id_kantor' => unserialize($v->id_kantor),
                    //         'id_com' => $v->id_com,
                    //     ];
                    // }
                }
            }
                    
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('detail', function ($data) {
                    $button = '<a href="javascript:void(0)" class="mt-3 mb-3 btn btn-primary btn-xs see_detail" id="' . $data['id'] . '" data-bs-toggle="modal" data-bs-target=".pengumuman2"><i class="fa fa-eye"></i></a>';
                    return $button;
                    })
                    ->addColumn('kantor', function ($data) {
                        $o = [];
                        $p = Kantor::select('unit')->whereIn('id', $data['id_kantor'])->get();
                        if(count($p) > 0){
                            foreach($p as $x){
                                $o[] = $x->unit; 
                            }
                        }else{
                            $o = '';
                        }
                        return $o;
                    }) 
                    ->rawColumns(['detail','kantor'])
                    ->make(true);
        }
        return view('presensi.daftar_pengumuman',compact('kantor','user'));
    }
    
    public function entry_pengumuman(Request $request){
        if($request->ajax()){
            $err['jenis'] = $request->jenis ;
            $err['isi'] = $request->isi ;
            $err['kantor'] = $request->id_kantor;
            $err['date1'] = $request->tgl_awal ;
            $err['date2'] = $request->tgl_akhir ;
            $err['prtuk'] = $request->peruntukan ;
            $err['users_kar'] = $request->id_user ;
            $err['j_lembur'] = $request->j_lembur ;
            $err['jam_awal'] = $request->jam_awal ;
            $err['jam_akhir'] = $request->jam_akhir ;
            $validasi = Validator::make($request->all(),[
                'isi' => 'required',
                'jenis' => 'required',
                'tgl_awal' => 'required',
                'tgl_akhir' => 'required',
                'id_kantor' => 'required',
            ]);
            
            if ($validasi->fails() || $err['j_lembur'] == 'jam' &&  ( $err['jam_awal'] == null || $err['jam_akhir'] == null) || $err['jenis'] == 'Lembur' && $request->j_lembur == null|| $err['prtuk'] == '' || ($err['prtuk'] == '2' && $err['users_kar'] == "") ||  $err['date1'] < date('Y-m-d') || $err['date2'] < date('Y-m-d') || $err['date1'] > $err['date2']) {
                return response()->json([
                'data' => $err,
                'errors' => 'Gagal Tambah Data']);
            }else{
                $data = [
                    'isi' => $request->isi,
                    'jenis' => $request->jenis,
                    'j_lembur' => $request->j_lembur,
                    'tgl_awal' => $request->tgl_awal,
                    'tgl_akhir' => $request->tgl_akhir,
                    'jam_awal' => $request->jam_awal,
                    'jam_akhir' => $request->jam_akhir,
                    'id_kantor' => serialize($request->id_kantor),
                    'id_com' => Auth::user()->id_com,
                    'id_user' => serialize($request->id_user),
                    'peruntukan' => $request->peruntukan,
                    'user_insert' => Auth::user()->id,
                ];
            Pengumuman::create($data);
            return response()->json([
                'success' => 'Berhasil tambah data']);
            }
        }
    }
    
    public function detail_pengumuman($id, Request $request){
        if ($request->ajax()) {
            $data = Pengumuman::find($id);
            $data['kan'] = unserialize($data->id_kantor);
            $data['user'] = unserialize($data->id_user);
            $arus = $data->id_user == 'N;' ? [] : unserialize($data->id_user);
            $p = Kantor::select('unit')->whereIn('id', unserialize($data->id_kantor))->get();
            $u = User::select('name')->whereIn('id', $arus)->get();
            $kantor = []; 
                if (count($p) > 0) {
                    foreach ($p as $x) {
                        $kantor[] = $x->unit; 
                    }
                    $data['kantor'] = $kantor; 
                } else {
                    $data['kantor'] = [];
                }
                
                $user = []; 
                if (count($u) > 0) {
                    foreach ($u as $z) {
                        $user[] = $z->name; 
                    }
                    $data['users'] = $user; 
                } else {
                    $data['users'] = [];
                }
                return response()->json($data);
        }
    }
    
    public function delete_pengumuman(Request $request, $id)
    {
        if($request->ajax()) {
            $data = Pengumuman::findOrFail($id);
            $data->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.'
            ]);
        } else {
            abort(404);
        }
    }
    
    public function edit_pengumuman(Request $request, $id){
         if($request->ajax()){
            $err['jenis'] = $request->jenis ;
            $err['isi'] = $request->isi ;
            $err['kantor'] = $request->id_kantor;
            $err['date1'] = $request->tgl_awal ;
            $err['date2'] = $request->tgl_akhir ;
            $err['prtuk'] = $request->peruntukan ;
            $err['users_kar'] = $request->id_user ;
            $err['j_lembur'] = $request->j_lembur ;
            $err['jam_awal'] = $request->jam_awal ;
            $err['jam_akhir'] = $request->jam_akhir ;
            $validasi = Validator::make($request->all(),[
                'isi' => 'required',
                'jenis' => 'required',
                'tgl_awal' => 'required',
                'tgl_akhir' => 'required',
                'id_kantor' => 'required',
            ]);
            
            if ($validasi->fails() || $err['j_lembur'] == 'jam' &&  ( $err['jam_awal'] == null || $err['jam_akhir'] == null) || $err['jenis'] == 'Lembur' && $request->j_lembur == null|| $err['prtuk'] == '' || ($err['prtuk'] == '2' && $err['users_kar'] == "") ||  $err['date1'] < date('Y-m-d') || $err['date2'] < date('Y-m-d') || $err['date1'] > $err['date2']) {
                return response()->json([
                'data' => $err,
                'errors' => 'Gagal update, pastikan data benar!']);
            }else{
            $data = [
                   'isi' => $request->isi,
                    'jenis' => $request->jenis,
                    'j_lembur' => $request->j_lembur,
                    'tgl_awal' => $request->tgl_awal,
                    'tgl_akhir' => $request->tgl_akhir,
                    'jam_awal' => $request->jam_awal,
                    'jam_akhir' => $request->jam_akhir,
                    'id_kantor' => serialize($request->id_kantor),
                    // 'id_com' => Auth::user()->id_com,
                    'id_user' => serialize($request->id_user),
                    'peruntukan' => $request->peruntukan,
                    'user_insert' => Auth::user()->id,
            ];
            Pengumuman::where('id', $request->id)->update($data);
            return response()->json(['success' => 'Berhasil update data']);
            
            }
        }
    }
    
    public function notif_pengumuman(Request $request){
      
        $user = User::select('name','id')->get();
        $kantor = Kantor::select('unit','id','kantor_induk')
                ->where('id_com', Auth::user()->id_com)
                ->where(function($query) {
                        if(Auth::user()->kepegawaian != 'admin'){
                            $query->where('id', Auth::user()->id_kantor)
                            ->orWhere('kantor_induk', Auth::user()->id_kantor);
                        }
                    })
                ->get();
                
        // $arus = $request->id_us == 'N;' ? [] : unserialize($request->id_user);
        
        $kan = [];
        foreach($kantor as $m => $n){
            $kan[] = $n->id;
        }
        
        // dd($kan);
        if($request->ajax()){
            $tg = date('Y-m-d', strtotime('+1 month', strtotime(date('Y-m-d'))));
            $filkan = !$request->kota ? $kan : $request->kota;
            // return($tg);
            $peng = Pengumuman::orderBy('created_at','desc')
                    ->where('id_com', Auth::user()->id_com)
                    ->whereDate('tgl_akhir', '>=', date('Y-m-d'))
                    ->whereDate('tgl_awal', '<=', $tg)
                    ->get();
            $salwal = [];
            // if(Auth::user()->level == 'keuangan pusat' || Auth::user()->level == 'admin'){
            // if(Auth::user()->id == 6){
                $salwal = SaldoAw::leftjoin('coa','coa.coa','=','saldo_awal.coa')
                                    ->select('saldo_awal.*', 'coa.nama_coa')
                                    // ->where('saldo_awal.id','<', 10)
                                    ->where('saldo_awal.canclos', 1)
                                    ->get();
            // }
            
            $data = [];
         
            $id = [];
            if(count($peng) > 0){
                foreach($peng as $x => $v){
                    $un = unserialize($v->id_kantor);
                    // if(count($filkan) > 0){
                        foreach($un as $i => $d){
                                if(in_array($d, $filkan) && !in_array($v->id,$id)) {
                                    $id[] = $v->id;
                                    $data[] = [
                                        'id' => $v->id,
                                        'isi' => $v->isi,
                                        'ket' => null,
                                        'jenis' => $v->jenis,
                                        'tgl_awal' => $v->tgl_awal,
                                        'tgl_akhir' => $v->tgl_akhir,
                                        'jam_awal' => $v->jam_awal,
                                        'jam_akhir' => $v->jam_akhir,
                                        'id_kantor' => unserialize($v->id_kantor),
                                        'id_com' => $v->id_com,
                                        'id_user' => unserialize($v->id_user),
                                        'tab' => 'peng',
                                    ];
                                }
                        }
                }
            }
            
            if(Auth::user()->level == 'keuangan pusat' || Auth::user()->level == 'admin'){
                foreach($salwal as $x => $v){
                    $bln = date('m', strtotime($v->bulan));
                    $thn = date('Y', strtotime($v->bulan));
                    $data[count($peng) + $x] = [
                        'id' => $v->id,
                        'isi' => $v->coa,
                        'ket' => $v->nama_coa,
                        'jenis' => 'Batal Closing',
                        'tgl_awal' => 'Bulan '.$bln.' Tahun '.$thn ,
                        'tgl_akhir' => $v->bulan,
                        'jam_awal' => null,
                        'jam_akhir' => null,
                        'id_kantor' => null,
                        'id_com' => null,
                        'id_user' => null,
                        'tab' => 'salwal',
                    ];
                }
            }
               
        }
            return $data;        
    }
    
    public function on_link_param(Request $request){
        $getlp = LinkParam::where('id_user', Auth::user()->id)->where('link', $request->link)->first();
        if($getlp != null){
            $datam = LinkParam::find($getlp->id);
            $datam->p1      = $request->p1;
            $datam->p2      = $request->p2;
            $datam->p3      = $request->p3;
            $datam->p4      = $request->p4;
            $datam->aktif   = 1;
            $datam->update();
        }else{
            $datam = new LinkParam;
            $datam->id_user = Auth::user()->id;
            $datam->link    = $request->link;
            $datam->p1      = $request->p1;
            $datam->p2      = $request->p2;
            $datam->p3      = $request->p3;
            $datam->p4      = $request->p4;
            $datam->aktif   = 1;
            $datam->save();
        }
        return response()->json(['success' => 'berhasil']);
    }
    
    public function off_link_param(Request $request){
        $getlp = LinkParam::where('id_user', Auth::user()->id)->where('link', $request->link)->first();
        if($getlp != null){
            $datam = LinkParam::find($getlp->id);
            $datam->aktif   = 0;
            $datam->update();
        }
        return response()->json(['success' => 'berhasil']);
    }
    // AKHIR PENGUMUMAN
    
    //SETTING REQUEST
    public function parentRequest(Request $request){
        $data    = Jenreq::where('statsub', 'dengan')->where('id_com',Auth::user()->id_com)->get();
        
        return response()->json($data);
    }
    
    public function hapus($id){
        $data = DB::table('request')->where('id_jenreq', $id)->count();
        if($data > 0){
            return response()->json(['error' => 'Gagal menghapus data. Error: Jenis request sudah digunakan!'], 500);
        }else{
            try {
                Jenreq::findOrFail($id)->delete();
                return response()->json('Berhasil');
            } catch (\Exception $e) {
                return response()->json(['error' => 'Gagal menghapus data. Error: ' . $e->getMessage()], 500);
            }
        }
    }

    
    public function setting_request(Request $request){
         $data = Jenreq::select('jenis')->get();
        if($request->ajax()){
            // dd($request);
            $id_com = Auth::user()->id_com;
            $com = $request->com;
            // if(Auth::user()->level_hc == '1'){
            // $filCompany = $com == '' || $com == '0' ? "id_com != 'haha'" : "id_com = '$com'";
            // }else{
            //     $filCompany = "id_com = '$id_com'";
            // }
            $data = Jenreq::where(function($query) use ($com){
                        if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                            if($com > 0){
                                $query->where('id_com', $com);
                            }else if($com == '0'){
                                $query->whereIn('id_com', function($q) {
                                    $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                });
                            }else{
                                $query->where('id_com', Auth::user()->id_com);
                            } 
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                })
                ->orderBy('created_at','desc');
                return DataTables::of($data)
                    ->addColumn('hapus', function($data){
                        $button = '<button type="button" class="btn btn-sm btn-danger center-block hapus" id="'. $data->id .'" title="Hapus"><i class="fa fa-trash"></i></button>';
                        return $button;
                    })
                    ->rawColumns(['hapus'])
                    ->addIndexColumn()
                    ->make(true);
        }
        return view('presensi.setting_request',compact('data'));
    }
    
    public function save_setting_request(Request $request){
         if($request->ajax()){
            // $validasi = Validator::make($request->all(),[
            //     "jenis" => "required",
            //     "kategory" => "required",
            //     "statpres" => "required",
            //     "walap" => "required",
            //     "statsub" => "required",
            //     "id_parent" => "required",
            //     "per_limit" => "required",
            //     "jum_limit" => "required",
            //     "req_limit" => "required",
            //     "lam" => "required",
            //     "foto" => "required",
            //     "lok" => "required",
            // ]);
            
            // if ($validasi->fails()) {
            //     return response()->json([
            //     'errors' => 'Gagal Update Data']);
            // }else{
                $data = [
                    "jenis" =>  $request->jenis,
                    "kategori" =>  $request->kategori,
                    "statpres" =>  $request->statpres,
                    "walap" =>  $request->walap,
                    "statsub" =>  $request->statsub,
                    "id_parent" =>  $request->id_parent,
                    "per_limit" =>  $request->per_limit,
                    "jum_limit" =>  $request->jum_limit ?? 0,
                    "req_limit" =>  $request->req_limit,
                    "lam" =>  $request->lam,
                    "foto" =>  $request->foto,
                    "lok" =>  $request->lok,
                    "id_com" =>  Auth::user()->id_com,
                ];
                // dd($request->id);
                Jenreq::updateOrCreate(['id' => $request->id],$data);
                // Jenreq::create($data);
                return response()->json(['success' => 'Berhasil!']);
            // }
        }
    }
    
    //END SETTING REQUEST
    public function lapo_mar(Request $req)
    {
        $id     = $req->id_karyawan;
        $u      = User::where('id_karyawan', $id)->first();
        $tgl    = date('Y-m-d', strtotime($req->tgl));
        $v      = Transaksi_Perhari_All::where('id', $u->id)->whereDate('Tanggal', $tgl)->first(); 
        $w      = Prosp::selectRaw("COUNT(IF(ket = 'closing' AND tgl_fol = '$tgl', id, NULL)) AS closing, 
                                    COUNT(IF(ket = 'cancel' AND tgl_fol = '$tgl', id, NULL)) AS cancel, 
                                    COUNT(IF(ket = 'open' AND DATE(created_at) = '$tgl', id, NULL)) AS open ")
                        ->where(function($q) use ($tgl) {
                            $q->whereDate('tgl_fol', $tgl)->where('ket', '!=', 'open')
                                ->orWhereDate('created_at', $tgl)->where('ket', 'open');
                        })
                        ->where('id_peg', $u->id)
                        ->first()
                        ;
        $data = [];
        if($v != null){
                $data['data'] = [
                    'id'            => $v->id,
                    'name'          => $v->name,
                    'donasi'        => $v->donasi,
                    't_donasi'      => $v->t_donasi,
                    'tutup'         => $v->tutup,
                    'tutup_x'       => $v->tutup_x,
                    'ditarik'       => $v->ditarik,
                    'k_hilang'      => $v->k_hilang,
                    'kunjungan'     => $v->k_hilang + $v->ditarik + $v->tutup_x + $v->tutup + $v->t_donasi + $v->donasi,
                    'tf_donasi'     => $v->tf_donasi,
                    'tf_t_donasi'   => $v->tf_t_donasi,
                    'tf_off'        => $v->tf_off,
                    'tf'            => $v->tf_donasi + $v->tf_t_donasi + $v->tf_off,
                    'closing'       => $w->closing,
                    'open'          => $w->open,
                    'cancel'        => $w->cancel,
                    'prospek'       => $w->closing + $w->open + $w->cancel,
                    'capaian'       => 0,
                    // 'capaian'       => ($v->donasi + $v->t_donasi + $v->tutup + $v->tutup_x + $v->ditarik + $v->k_hilang)/Auth::user()->kunjungan * 100
                ];
        }else{
          $data['data'] = []; 
        }
        return($data);
    }
    
}
