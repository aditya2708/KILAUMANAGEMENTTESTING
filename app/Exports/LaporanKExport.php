<?php

namespace App\Exports;

use Auth;
use App\Models\Transaksi;
use App\Models\Laporan;
use App\Models\Jabatan;
use App\Models\Kantor;
use App\Models\Profile;
use Carbon\Carbon;
use DB;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class LaporanKExport implements FromView
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     
    public function __construct($request, $waktu)
    {
        $this->request = $request ;
        $this->waktu = $waktu ;
        return $this;
    }

    public function view(): View
    {
        $waktu = $this->waktu;
        $request = $this->request;
        
        // dd($request->plhtgl);
        
         if($request->daterange != '') {
            $tgl = explode(' - ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
        if($request->blns != '') {
            $tglsss = explode('-', $request->blns);
            $y = date('Y', strtotime($tglsss[1]));
            $m = date('m', strtotime($tglsss[0]));
        }
        $now = date('Y-m-d');
        $yearNow = date('Y');
        $monthNow = date('m');
        
            
        $kntr = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', $kntr)->first();
        $id_com = $request->com;
        $jabatan = Jabatan::where('id_com', Auth::user()->id_com)->get();
        $kota = Kantor::where('tambahan.id_com', Auth::user()->id_com)->where(function($query) use ($k, $kntr){
                        if(Auth::user()->kepegawaian == 'kacab'){
                            if($k == null){
                                $query->where('id', Auth::user()->id_kantor);
                            }else{
                                $query->whereRaw("id = '$k->id' OR id = '$kntr'");
                            }
                        }
                    })->get(); 
       
       $company = Profile::where(function($query) {
                        if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                            $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })->get();
        // $jabat = $request->jabatan != '' ? "karyawan.id_jabatan IN ($request->jabatan)" : "karyawan.id_jabatan IS NOT NULL";
        // $kota = $request->kota != '' ? "laporan.id_kantor = '$request->kota'" : "laporan.id_kantor IS NOT NULL";
                 
        if($request->plhtgl == '0'){
            $tgls = $request->daterange != '' ? "DATE(laporan.created_at) >= '$dari' AND DATE(laporan.created_at) <= '$sampai'" : "DATE(laporan.created_at) >= '$now' AND DATE(laporan.created_at) <= '$now'" ;
        }else if($request->plhtgl == '1'){
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
                $data = Laporan::selectRaw("jabatan.jabatan,laporan.id_karyawan,laporan.nama,laporan.id_kantor,laporan.id_jabatan,laporan.id_laporan, laporan.ket as ket, laporan.created_at as tanggal")->join('jabatan','jabatan.id','=','laporan.id_jabatan')
                ->whereRaw("$tgls")
                ->where(function($query) use ($k, $kntr, $request){
                    if(Auth::user()->kepegawaian == 'kacab'){
                        if($k == null){
                            $query->where('id_kantor', Auth::user()->id_kantor);
                        }else{
                           if (empty($request->kota) || is_null($request->kota)) {
                                // Kondisi ketika $request->kota kosong atau tidak didefinisikan (undefined)
                                $query->where("id_kantor", $k->id)->orWhere('id_kantor', $kntr);
                            } else {
                                // Kondisi ketika $request->kota memiliki nilai
                                $query->whereIn('id_kantor', $request->kota);
                            }
                        }
                    }else{
                        if($request->kota == ''){
                            $query->whereRaw("id_kantor IS NOT NULL");
                        }else{
                            $query->whereIn('id_kantor', $request->kota);
                        }
                    }
                })
                ->where(function($query) use ( $request){
                    if($request->jabatan == '' || empty($request->jabatan)){
                        $query->whereRaw("id_jabatan IS NOT NULL");
                    }else{
                        $query->whereIn('id_jabatan', $request->jabatan);
                    }
                })
                ->where(function($query) use ( $request){
                    if($request->karyawan == '' || empty($request->karyawan)){
                        $query->whereRaw("id_karyawan IS NOT NULL");
                    }else{
                        $query->whereIn('id_karyawan', $request->karyawan);
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
                                        $query->whereIn('id_com', function($q) {
                                            $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                        });
                                    } 
                                }else{
                                    $query->where('karyawan.id_com', Auth::user()->id_com);
                                }
                            });
                    
                })
                ->orderBy('laporan.created_at', 'desc')->get();
            // Loop melalui data karyawan
            $result = [];
        foreach ($data as $karyawan) {
            $keterangan = preg_replace('/\n/', ' - ', $karyawan['ket']);
            $tanggal = strtotime($karyawan['tanggal']);
            $result[] = [
                'id_karyawan' => strval($karyawan['id_karyawan']),
                'tanggal' => date('d-m-Y', $tanggal),
                'nama' => $karyawan['nama'],
                'jabatan' => $karyawan['jabatan'],
                'ket' => $keterangan,
                ];
        }
            return view('ekspor.laporankaryawanexport',[
                'data' => $result,
                'priode' => $waktu,
                'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()->name
            ]);
            
    }
}