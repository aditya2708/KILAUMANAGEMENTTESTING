<?php

namespace App\Exports;

use Auth;
use App\Models\Presensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RekapKExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // $tgls, $jabat, $kota, $stts, $opsi, $month, $year
    
    public function __construct(string $tgls, string $jabat, string $kota,  string $stts,  string $opsi, string $month, string $year, string $k, string $req)
    {
        
        $this->tgl = $tgls ;
        $this->jabat = $jabat;
        $this->kota = $kota;
        $this->stts = $stts;
        $this->opsi = $opsi;
        $this->month = $month;
        $this->year = $year;
        $this->k = $k;
        $this->req = $req;
        return $this;
    }
    
    public function collection()
    {
        
        $jab = $this->jabat;
        $kntr = $this->kota;
        $tgls = $this->tgl;
        $stat = $this->stts;
        $opsi = $this->opsi;
        $month = $this->month;
        $year = $this->year;
        $k = $this->k;
        $req = $this->req;
        
        if(Auth::user()->kepegawaian == 'admin' || Auth::user()->kepegawaian == 'hrd' || Auth::user()->keuangan == 'keuangan pusat'){
            if($opsi == 0){
                $datas = Presensi::selectRaw("presensi.id_karyawan, presensi.nama, jabatan.jabatan, tambahan.unit,
                            SUM(CASE WHEN presensi.status = 'Hadir' AND $tgls THEN 1 ELSE 0 END) AS jum_hadir,
                            SUM(CASE WHEN presensi.status = 'Terlambat' AND $tgls THEN 1 ELSE 0 END) AS jum_terlambat,
                            SUM(CASE WHEN presensi.status = 'Bolos' AND $tgls THEN 1 ELSE 0 END) AS jum_bolos,
                            SUM(CASE WHEN presensi.status = 'Sakit' AND $tgls THEN 1 ELSE 0 END) AS jum_sakit,
                            SUM(CASE WHEN presensi.status = 'Perdin' AND $tgls THEN 1 ELSE 0 END) AS jum_perdin,
                            SUM(CASE WHEN presensi.status = 'Cuti' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti,
                            SUM(CASE WHEN presensi.status = 'Cuti Penting' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti_penting")
                            ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                            ->join('jabatan','karyawan.jabatan','=','jabatan.id')
                            ->join('tambahan','tambahan.id','=','presensi.id_kantor')
                            ->whereRaw("karyawan.aktif = 1 AND $kntr AND $jab AND $stat AND $tgls")
                            ->groupBy('presensi.id_karyawan', 'presensi.nama', 'jabatan.jabatan')
                            ->whereIn('karyawan.id_karyawan', function($query) {
                                    $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                            });
            }else{
                $datas = $datas = Presensi::selectRaw("presensi.id_karyawan, presensi.nama, jabatan.jabatan, tambahan.unit,
                            SUM(CASE WHEN presensi.status = 'Hadir' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_hadir,
                            SUM(CASE WHEN presensi.status = 'Terlambat' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_terlambat,
                            SUM(CASE WHEN presensi.status = 'Bolos' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_bolos,
                            SUM(CASE WHEN presensi.status = 'Sakit' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_sakit,
                            SUM(CASE WHEN presensi.status = 'Perdin' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_perdin,
                            SUM(CASE WHEN presensi.status = 'Cuti' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_cuti,
                            SUM(CASE WHEN presensi.status = 'Cuti Penting' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_cuti_penting")
                            ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                            ->join('jabatan','karyawan.jabatan','=','jabatan.id')
                            ->join('tambahan','tambahan.id','=','presensi.id_kantor')
                            ->whereRaw("karyawan.aktif = 1 AND $kntr AND $jab AND $stat")
                            ->groupBy('presensi.id_karyawan')
                            ->whereIn('presensi.id_karyawan', function($query) {
                                    $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                            });
            }
        }else{
            if($opsi == 0){
                $datas = Presensi::selectRaw("presensi.id_karyawan, presensi.nama, jabatan.jabatan, tambahan.unit,
                            SUM(CASE WHEN presensi.status = 'Hadir' AND $tgls THEN 1 ELSE 0 END) AS jum_hadir,
                            SUM(CASE WHEN presensi.status = 'Terlambat' AND $tgls THEN 1 ELSE 0 END) AS jum_terlambat,
                            SUM(CASE WHEN presensi.status = 'Bolos' AND $tgls THEN 1 ELSE 0 END) AS jum_bolos,
                            SUM(CASE WHEN presensi.status = 'Sakit' AND $tgls THEN 1 ELSE 0 END) AS jum_sakit,
                            SUM(CASE WHEN presensi.status = 'Perdin' AND $tgls THEN 1 ELSE 0 END) AS jum_perdin,
                            SUM(CASE WHEN presensi.status = 'Cuti' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti,
                            SUM(CASE WHEN presensi.status = 'Cuti Penting' AND $tgls THEN 1 ELSE 0 END) AS jum_cuti_penting")
                            ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                            ->join('jabatan','karyawan.jabatan','=','jabatan.id')
                            ->join('tambahan','tambahan.id','=','presensi.id_kantor')
                            ->whereRaw("karyawan.aktif = 1 AND $jab AND $stat AND $tgls")
                            ->where(function ($query) use ($k, $req, $kntr) {
                                if($k == null){
                                    $query->where('presensi.id_kantor', Auth::user()->id_kantor);
                                }else{
                                    if($req != 'kosong'){
                                        $query->whereRaw("$kntr");
                                    }else{
                                        $query->where('presensi.id_kantor', $k)->orWhere('presensi.id_kantor', Auth::user()->id_kantor);
                                    }
                                }
                            })
                            ->groupBy('presensi.id_karyawan', 'presensi.nama', 'jabatan.jabatan')
                            ->whereIn('karyawan.id_karyawan', function($query) {
                                    $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                            });
            }else{
                $datas = $datas = Presensi::selectRaw("presensi.id_karyawan, presensi.nama, jabatan.jabatan, tambahan.unit,
                            SUM(CASE WHEN presensi.status = 'Hadir' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_hadir,
                            SUM(CASE WHEN presensi.status = 'Terlambat' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_terlambat,
                            SUM(CASE WHEN presensi.status = 'Bolos' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_bolos,
                            SUM(CASE WHEN presensi.status = 'Sakit' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_sakit,
                            SUM(CASE WHEN presensi.status = 'Perdin' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_perdin,
                            SUM(CASE WHEN presensi.status = 'Cuti' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_cuti,
                            SUM(CASE WHEN presensi.status = 'Cuti Penting' AND MONTH(presensi.created_at) = '$month' AND YEAR(presensi.created_at) = '$year' THEN 1 ELSE 0 END) AS jum_cuti_penting")
                            ->join('karyawan','presensi.id_karyawan','=','karyawan.id_karyawan')
                            ->join('jabatan','karyawan.jabatan','=','jabatan.id')
                            ->join('tambahan','tambahan.id','=','presensi.id_kantor')
                            ->whereRaw("karyawan.aktif = 1 AND $jab AND $stat")
                            ->where(function ($query) use ($k, $req, $kntr) {
                                if($k == null){
                                    $query->where('presensi.id_kantor', Auth::user()->id_kantor);
                                }else{
                                    if($req != ''){
                                        $query->whereRaw("$kntr");
                                    }else{
                                        $query->where('presensi.id_kantor', $k->id)->orWhere('presensi.id_kantor', Auth::user()->id_kantor);
                                    }
                                }
                            })
                            ->groupBy('presensi.id_karyawan')
                            ->whereIn('presensi.id_karyawan', function($query) {
                                    $query->select('karyawan.id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                            });
            }
        }
        
        $data = $datas->get();
        
        return $data;
    }
    
    
    public function headings(): array
    {
        return [
            'Id Karyawan', 'Nama', 'Jabatan', 'Kantor', 'Hadir', 'Terlambat', 'Bolos', 'Sakit', 'Perdin', 'Cuti', 'Cuti Penting'
        ];
    }
}
