<?php
namespace App\Exports;

use Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use App\Models\Rencana;
use App\Models\Kantor;
class RencanaExport implements FromView
{
    
    public function __construct($request)
    {
        $this->request = $request;
        return $this;
    }

    public function view(): View { 
        $request = $this->request;
            
        $nambulan = [
            1 => 'Januari',
            2 =>'Febuari',
            3 =>'Maret',
            4 =>'April', 
            5 =>'Mei',
            6 =>'Juni',
            7 =>'Juli',
            8 =>'Agustus',
            9 =>'September',
            10 =>'Oktober',
            11 =>'November' ,
            12 =>'Desember' 
        ];
      
        
        $kot = Auth::user()->id_kantor;
        $cari = Kantor::where('kantor_induk', $kot)->first();
        
        $kantor = $request->kntr;
        
        $unit = $request->unit;
        
        $month = $request->bulan == '' ? date('Y-m') : $request->bulan;
        
        $datas = Karyawan::selectRaw("rencana.*, karyawan.nama, karyawan.id_karyawan as id_kar, COUNT(IF($full AND marketing = 0 AND rencana.aktif = 1, id, null)) as jumlah")
                        ->leftJoin('rencana','karyawan.id_karyawan','=','rencana.id_karyawan')
                        ->where('karyawan.aktif', 1)
                        ->where('id_com', Auth::user()->id_com)
                        ->where('karyawan.id_kantor', $request->unit)
                        ->orderBy('karyawan.nama','ASC')
                        ->groupBy('id_kar')
                        ->get();
        $mergedData = [];
        foreach($datas as $d){
            $cobain = Rencana::select('tgl_awal')->whereRaw("marketing = 0 AND rencana.aktif = 1 AND $full AND id_karyawan = '$d->id_kar'")->pluck('tgl_awal')->toArray();
            $lagi = Rencana::select('tgl_awal')->whereRaw("marketing = 1 AND rencana.aktif = 1 AND $full AND id_karyawan = '$d->id_kar'")->pluck('tgl_awal')->toArray();
                
                
            $mergedData = array_reduce($cobain, function ($carry, $date) {
                $carry[$date][] = $date;
                return $carry;
            }, []);
                
            $mergedData = array_values($mergedData);
            
            $mergedData2 = array_reduce($lagi, function ($carrye, $datee) {
                $carrye[$datee][] = $datee;
                    return $carrye;
            }, []);
                
            $mergedData2 = array_values($mergedData2);
                
            $data[] = [
                    // 'id' => $d->id,
                'id_karyawan' => $d->id_kar,
                'nama' => $d->nama,
                'tgl' => $month,
                'jumlah_rencana' => $d->jumlah,
                'jumlah_hari' => count($mergedData). ' Tugas Umum dan '.count($mergedData2).' Merketing'
            ];
        }


        return view('ekspor.rencanaexport',[
            'bulan' => $month,
            'data' => $data,
            'kantor' => $request->kntr,
            'company' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name,
        ]);
    }
}