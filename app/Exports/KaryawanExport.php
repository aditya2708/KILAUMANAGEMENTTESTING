<?php

namespace App\Exports;


use Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use App\Models\Presensi;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Kantor;
use App\Models\Profile;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use Carbon\Carbon;
class KaryawanExport implements FromView
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     
    public function __construct($request)
    {
        
        $this->request = $request ;
        return $this;
    }

    public function view(): View
    {
        $request = $this->request;
        
        $id_com = $request->com;
        $unit = $request->unit != '' ? "karyawan.id_kantor = $request->unit" : "karyawan.id_kantor IS NOT NULL";
        $jabat = $request->jabata != '' ? "karyawan.jabatan = $request->jabata" : "karyawan.jabatan IS NOT NULL";
        $status = $request->status != '' ? "karyawan.aktif = $request->status" : "karyawan.aktif = 1";
        $jenis = $request->jenis_t != '' ? "karyawan.status_kerja = '$request->jenis_t'" : "karyawan.status_kerja != '-' ";
        
        $kntr = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', $kntr)->first();
        
        if ($request->tglAktif != '') {
            $tglAktif = explode(' s/d ', $request->tglAktif);
            // return($tgl);
            $dariAktif = date('Y-m-d', strtotime($tglAktif[0]));
            $sampaiAktif = date('Y-m-d', strtotime($tglAktif[1]));
            $tglAktif = "DATE(karyawan.tgl_aktif) >= '$dariAktif' AND DATE(karyawan.tgl_aktif) <= '$sampaiAktif'";
        }else{
            $tglAktif = "karyawan.tgl_aktif IS NOT NULL OR karyawan.tgl_aktif IS NULL";
        }
        
        if ($request->tglNonAktif != '') {
            $tglNonAktif = explode(' s/d ', $request->tglNonAktif);
            // return($tgl);
            $dariNonAktif = date('Y-m-d', strtotime($tglNonAktif[0]));
            $sampaiNonAktif = date('Y-m-d', strtotime($tglNonAktif[1]));
            $tglNonAktif = "DATE(karyawan.tgl_nonaktif) >= '$dariNonAktif' AND DATE(karyawan.tgl_nonaktif) <= '$sampaiNonAktif'";
        }else{
            $tglNonAktif = "karyawan.tgl_nonaktif IS NOT NULL OR karyawan.tgl_nonaktif IS NULL";
        }

        if(Auth::user()->id_com != null ){
            $karyawan = Karyawan::selectRaw('karyawan.nik, jabatan.jabatan, karyawan.email, karyawan.nomerhp, karyawan.tgl_lahir, karyawan.jk, karyawan.alamat, karyawan.pendidikan, karyawan.nm_sekolah, karyawan.th_lulus, karyawan.jurusan, karyawan.unit_kerja, karyawan.id_kantor, karyawan.nama, karyawan.id_karyawan')
            ->whereRaw("$jabat AND $tglAktif AND $tglNonAktif AND $status AND $jenis")
            ->join('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
            ->where(function($query) use ($k, $kntr, $request){
                if(Auth::user()->kepegawaian == 'kacab'){
                    if($k == null){
                        $query->where('karyawan.id_kantor', Auth::user()->id_kantor);
                    }else{
                        if($request->unit == ''){
                            $query->whereRaw("karyawan.id_kantor = '$k->id' OR karyawan.id_kantor = '$kntr'");
                        }else{
                             $query->where('karyawan.id_kantor', $request->unit);
                        }
                    }
                }else{
                    if($request->unit == ''){
                        $query->whereRaw("karyawan.id_kantor IS NOT NULL");
                    }else{
                        $query->where('karyawan.id_kantor', $request->unit);
                    }
                }
            })
            // ->where('id_com', Auth::user()->id_com)
            
            ->where(function($query) use ($id_com){
                if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                    if($id_com > 0){
                        $query->where('karyawan.id_com', $id_com);
                    }else if($id_com == '0'){
                        $query->whereIn('karyawan.id_com', function($q) {
                            $q->select('karyawan.id_com')->from('company')->where('karyawan.id_hc', Auth::user()->id_com);
                        });
                    }else{
                       $query->where('karyawan.id_com', Auth::user()->id_com);
                    } 
                }else{
                    $query->where('karyawan.id_com', Auth::user()->id_com);
                }
            })->get();
        }
             
        foreach($karyawan as $key => $val){
            $data[] = [
                "nik" => $val->nik,
                "jabatan" => $val->jabatan,
                "email" => $val->email,
                "nomerhp" => $val->nomerhp,
                "tgl_lahir" => $val->tgl_lahir,
                "jk" => $val->jk,
                "alamat" => $val->alamat,
                "pendidikan" => $val->pendidikan,
                "nm_sekolah" => $val->nm_sekolah,
                "th_lulus" => $val->th_lulus,
                "jurusan" => $val->jurusan,
                "unit_kerja" => $val->unit_kerja,
                "id_kantor" => $val->id_kantor,
                "nama" => $val->nama,
                "id" => $val->id_karyawan,
            ];
        }

        return view('ekspor.karyawanexport',[
            'data' => $data,
            'company' => $id_com != '' ? DB::table('company')->selectRaw('name')->where('id_com', $id_com)->first()->name :DB::table('company')->selectRaw('name')->where('id_com', 1)->first()->name
        ]);
                    
    }
       
}

