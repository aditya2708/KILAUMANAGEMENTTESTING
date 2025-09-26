<?php

namespace App\Exports;


use Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use App\Models\Presensi;
use App\Models\Jabatan;
use App\Models\Kantor;
use App\Models\Profile;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use Carbon\Carbon;
class KehadiranExport implements FromView
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     
    public function __construct($request, $a, $b)
    {
        
        $this->a = $a ;
        $this->b = $b ;
        $this->request = $request ;
        return $this;
    }

    public function view(): View
    {
        $request = $this->request;
        $a = $this->a;
        $b = $this->b;
        
         $dari = $request->dari == '' ? Carbon::now()->toDateString() : $request->dari;
        $ke = $request->ke == '' ? $dari : $request->ke;
        
        $id_com = $request->com;
        
        $jabatan = Jabatan::where('id_com', Auth::user()->id_com)->get();
        
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kot = Auth::user()->id_kantor;
        
        
        $company = Profile::where(function($query) {
                        if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                            $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })
                    ->get();
      
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
                    $query->where("presensi.status", $request->status);
                }
            });
        };
        
        $statusAktif = $request->aktif != '' ? "karyawan.aktif = '$request->aktif'": "'karyawan.aktif is not null or karyawan.aktif is null'";
        
        if(Auth::user()->id_com != null ){
            if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                 $data = Presensi::whereRaw("$krywn AND $tgls AND $detailRekap AND $statusAktif")
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
                        ->orderBy('presensi.created_at','desc')->get();
                        
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
                        
                        $dataDetail = Presensi::selectRaw('*')
                        ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                        ->where('presensi.id_karyawan',$request->idKaryawan)
                            ->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })
                            ->where($jabatan)
                            ->whereRaw("$statusAktif AND $tgls");
                        
            }
            else if(Auth::user()->kepegawaian == 'admin' || Auth::user()->kepegawaian == 'hrd' || Auth::user()->keuangan == 'keuangan pusat'){
                        
                         $dataDetail = Presensi::selectRaw('*')
                         ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                         ->where('presensi.id_karyawan',$request->idKaryawan)
                            ->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })
                            ->where($jabatan)
                            ->whereRaw("$statusAktif AND $tgls");
                        
                        
                        $data = Presensi::whereRaw("$krywn AND   $tgls AND $detailRekap")
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
                        ->orderBy('created_at','desc')->get();
                        
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

            }else if(Auth::user()->kepegawaian == 'kacab' || Auth::user()->keuangan == 'keuangan cabang'){
                if($k == null){
                    
                         $dataDetail = Presensi::selectRaw('*')->where('presensi.id_karyawan',$request->idKaryawan)
                         ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                            ->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })
                            ->where($jabatan)
                            ->whereRaw("$statusAktif AND $tgls");
                    
                        $data = Presensi::whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND $detailRekap")
                        ->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('presensi.id_karyawan', function($query) {
                                $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->orderBy('created_at', 'desc')->get();
                        
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
                        ->whereRaw("karyawan.aktif = 1 AND   $tgls AND $krywn AND $detailRekap")
                        ->groupBy('presensi.id_karyawan', 'presensi.nama', 'jabatan.jabatan')
                        ->whereIn('presensi.id_karyawan', function($query) {
                                $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        });
                        
                }else{
                    if($request->kantor != ''){
                        
                        $dataDetail = Presensi::selectRaw('*')->where('presensi.id_karyawan',$request->idKaryawan)
                        ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                            ->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })
                            ->where($jabatan)
                            ->whereRaw("$statusAktif AND $tgls");
                        
                        $data = Presensi::where($jabatan)->where($status)->whereRaw("  $tgls AND $detailRekap")->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('id_karyawan', function($query) {
                                $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->orderBy('created_at', 'desc')->get();
                        
                        
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
                            ->whereRaw("karyawan.aktif = 1 AND   $tgls AND $krywn AND $detailRekap")
                            ->where(function($query) use ($request){
                                if($request->kantor == '' || empty($request->kantor)){
                                    $query->whereRaw("presensi.id_kantor IS NOT NULL");
                                }else{
                                    $query->whereIn("presensi.id_kantor", $request->kantor);
                                }
                            })
                            ->groupBy('presensi.id_karyawan', 'presensi.nama', 'jabatan.jabatan')
                            ->whereIn('presensi.id_karyawan', function($query) {
                                    $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                            });
                            
                    }else{
                        
                         $dataDetail = Presensi::selectRaw('*')->where('presensi.id_karyawan',$request->idKaryawan)
                         ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                            ->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })
                            ->where($jabatan)
                            ->whereRaw("$statusAktif AND $tgls");
                        
                        $data = Presensi::where($jabatan)->where($status)->whereRaw("presensi.id_kantor = '$kot' AND  $tgls AND $detailRekap")->orWhereRaw("presensi.id_kantor = '$k->id' AND  DATE(created_at) >= '$dari' AND DATE(created_at) <= '$ke'")->where(function($query) use ($request){
                            if($request->kantor == '' || empty($request->kantor)){
                                $query->whereRaw("presensi.id_kantor IS NOT NULL");
                            }else{
                                $query->whereIn("presensi.id_kantor", $request->kantor);
                            }
                        })->whereIn('id_karyawan', function($query) {
                                $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })->orderBy('created_at', 'desc')->get();
                        
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
                            
                    }
                    
                }
            }
        }

        
        
        if($request->tombol){
           return view('ekspor.kehadiran',[
                'data' => $data,
                'priode' => $a.'-'.$b,
                'kondisi' => 'kehadiran',
                'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()->name
            ]);
        }
       
        if($request->tombol1){
             return view('ekspor.kehadiran',[
                'data' => $datas->get(),
                'priode' => $a.'-'.$b,
                'kondisi' => 'rekap',
                'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()->name
            ]);
        }
        if($request->tombol2){
             return view('ekspor.kehadiran',[
                'data' => $dataDetail->get(),
                'nama' => $dataDetail->first()->nama,
                'priode' => $a.'-'.$b,
                'kondisi' => 'detail',
                'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()->name
            ]);
        }
                    
        }
}
