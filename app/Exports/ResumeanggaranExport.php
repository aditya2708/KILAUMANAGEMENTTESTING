<?php

namespace App\Exports;

use App\Models\Kantor;
use App\Models\COA;
use App\Models\Jabatan;
use DB;
use Auth;
use App\Models\Anggaran;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ResumeanggaranExport  implements FromView
{
    public function __construct($kntr,$dari,$sampai,$periode)
    {
        
        $this->kantor = $kntr;
        $this->tanggal1 = $dari;
        $this->tanggal2 = $sampai ;
        $this->periode = $periode ;
    }

    public function view(): View 
    {
        $kntr = $this->kantor;
        $dari = $this->tanggal1;
        $sampai = $this->tanggal2;
        $periode = $this->periode;
                        // SUM(IF(DATE_FORMAT(anggaran.tanggal,'%Y') >= '$tgl1' AND DATE_FORMAT(anggaran.tanggal,'%Y') <= '$tgl2' AND anggaran.acc = '1' , (anggaran.anggaran + anggaran.tambahan + anggaran.relokasi / anggaran.realisasi) * 100 , 0)) AS persen, 

        
        if($periode == 'tahun'){
        $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
            ->leftjoin('users','users.id','=','anggaran.user_input')
            ->leftjoin('pengeluaran','pengeluaran.id_anggaran','=','anggaran.id_anggaran')
             ->selectRaw("anggaran.coa,anggaran.nama_akun,anggaran.tanggal,anggaran.program,anggaran.jabatan,tambahan.unit,users.name,pengeluaran.nominal,
             SUM(IF(DATE_FORMAT(anggaran.tanggal,'%Y') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%Y') <= '$sampai' AND anggaran.acc = '1' , anggaran.anggaran, 0)) AS anggaran,
             SUM(IF(DATE_FORMAT(anggaran.tanggal,'%Y') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%Y') <= '$sampai' AND anggaran.acc = '1' , anggaran.relokasi, 0)) AS relokasi,
             SUM(IF(DATE_FORMAT(anggaran.tanggal,'%Y') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%Y') <= '$sampai' AND anggaran.acc = '1' , anggaran.tambahan, 0)) AS tambahan,
             SUM(IF(DATE_FORMAT(pengeluaran.tgl,'%Y') >= '$dari' AND DATE_FORMAT(pengeluaran.tgl,'%Y') <= '$sampai' AND pengeluaran.acc = '1' , pengeluaran.nominal, 0)) AS realisasi,
             SUM(IF(DATE_FORMAT(anggaran.tanggal,'%Y') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%Y') <= '$sampai' AND anggaran.acc = '1' , anggaran.anggaran + anggaran.tambahan + anggaran.relokasi, 0)) AS tot 
             ") 
             ->whereRaw("anggaran.acc = '1' AND $kntr AND DATE_FORMAT(anggaran.tanggal,'%Y') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%Y') <= '$sampai' ")
             ->groupBy('anggaran.coa','anggaran.nama_akun')
             ->get();
        }else if($periode == 'bulan'){
              $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
                     ->leftjoin('users','users.id','=','anggaran.user_input')
                    ->leftjoin('pengeluaran','pengeluaran.id_anggaran','=','anggaran.id_anggaran')
                     ->selectRaw("anggaran.coa,anggaran.nama_akun,anggaran.tanggal,anggaran.program,anggaran.jabatan,tambahan.unit,users.name,pengeluaran.nominal,
                     SUM(IF(DATE_FORMAT(anggaran.tanggal,'%m') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%m') <= '$sampai' AND anggaran.acc = '1' , anggaran.anggaran, 0)) AS anggaran,
                     SUM(IF(DATE_FORMAT(anggaran.tanggal,'%m') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%m') <= '$sampai' AND anggaran.acc = '1' , anggaran.relokasi, 0)) AS relokasi,
                     SUM(IF(DATE_FORMAT(anggaran.tanggal,'%m') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%m') <= '$sampai' AND anggaran.acc = '1' , anggaran.tambahan, 0)) AS tambahan,
                     SUM(IF(DATE_FORMAT(pengeluaran.tgl,'%m') >= '$dari' AND DATE_FORMAT(pengeluaran.tgl,'%m') <= '$sampai' AND pengeluaran.acc = '1' , pengeluaran.nominal, 0)) AS realisasi,
                     SUM(IF(DATE_FORMAT(anggaran.tanggal,'%m') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%m') <= '$sampai' AND anggaran.acc = '1' , anggaran.anggaran + anggaran.tambahan + anggaran.relokasi, 0)) AS tot 
                     ") 
                     
                     ->whereRaw("anggaran.acc = '1' AND $kntr AND DATE_FORMAT(anggaran.tanggal,'%m') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%m') <= '$sampai' ")
                    ->groupBy('anggaran.coa','anggaran.nama_akun')
                     ->get();
        }else{
              $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
                     ->leftjoin('users','users.id','=','anggaran.user_input')
                     ->leftjoin('pengeluaran','pengeluaran.id_anggaran','=','anggaran.id_anggaran')
                     ->selectRaw("anggaran.coa,anggaran.nama_akun,anggaran.tanggal,anggaran.program,anggaran.jabatan,tambahan.unit,users.name,pengeluaran.nominal,
                     SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.anggaran, 0)) AS anggaran,
                     SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.relokasi, 0)) AS relokasi,
                     SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.tambahan, 0)) AS tambahan,
                     SUM(IF(DATE(pengeluaran.tgl) >= '$dari' AND DATE(pengeluaran.tgl) <= '$sampai' AND pengeluaran.acc = '1' , pengeluaran.nominal, 0)) AS realisasi,
                     SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.anggaran + anggaran.tambahan + anggaran.relokasi, 0)) AS tot ,
                    SUM(IF(DATE(pengeluaran.tgl) >= '$dari' AND DATE(pengeluaran.tgl) <= '$sampai' AND pengeluaran.acc = '1' , pengeluaran.nominal, 0)) / SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.anggaran + anggaran.tambahan + anggaran.relokasi , 0))  * 100  AS persen
                     ") 
                //   SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.realisasi * 100 / (anggaran.anggaran + anggaran.tambahan + anggaran.relokasi), 0)) AS persen, 
                //      SUM(IF(DATE(anggaran.tanggal) >= '$dari' AND DATE(anggaran.tanggal) <= '$sampai' AND anggaran.acc = '1' , anggaran.anggaran + anggaran.tambahan + anggaran.relokasi - anggaran.realisasi , 0)) AS sisa
                        // ->selectRaw("anggaran.*, tambahan.unit,users.name")
                     ->whereRaw("anggaran.acc = '1' AND $kntr AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') <= '$sampai' ")
                     ->groupBy('anggaran.coa','anggaran.nama_akun')
                     ->get();
        }
            
          
        return view('ekspor.resumeanggaran',[
            'data' => $datas,
            'priode' => 'Resume Anggaran Priode '.$dari. ' s/d '. $sampai,
            'company' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name
        ]);    
            
            
            
            
        //  $data = Anggaran::
        // leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
        //  ->leftJoin('coa','coa.coa','=','anggaran.coa')
        //  ->selectRaw("anggaran.tanggal,anggaran.coa,coa.nama_coa,anggaran.anggaran,anggaran.relokasi,anggaran.tambahan
        // ,tambahan.unit,anggaran.kantor,anggaran.realisasi,anggaran.jabatan
        //  ")
        //  ->whereRaw("$kntr AND  DATE(anggaran.tanggal) >= '$tgl1'  AND DATE(anggaran.tanggal)<= '$tgl2'")
        //  ->groupBy('anggaran.coa','anggaran.nama_akun')
        //   ->get();
          
        // foreach($data as $val){
        //       if(-$val->realisasi < $val->relokasi){
        //               $wew = $val->anggaran + $val->tambahan + $val->relokasi;
        //           $rek = $val->realisasi;
        //           $crot =  number_format( $wew - $rek );
        //           }else if (-$val->realisasi = $val->relokasi){
        //             $wew = $val->anggaran + $val->tambahan + $val->relokasi;
        //           $rek = $val->realisasi;
        //           $crot =  number_format( $wew );
        //           }else{
        //           $wew = $val->anggaran + $val->tambahan + $val->relokasi;
        //           $rek = $val->realisasi;
        //           $crot =  number_format( $wew - $rek);
        //          }
        //     // $wew = $datas->anggaran + $datas->tambahan + $datas->relokasi;
        //     // $rek = $datas->realisasi;
        //     $datas[] = [
        //         "tanggal" => $val->tanggal,
        //         "coa" => $val->coa,
        //         "nama_coa" => $val->nama_akun,
        //         "anggaran" =>$val->anggaran,
        //         "relokasi" =>$val->relokasi,
        //         "tambahan" =>$val->tambahan,
        //         "total" =>$val->tambahan + $val->anggaran + $val->relokasi ,
        //         'realisasi'=>$val->realisasi,
        //         "persen"=>round(( $wew / $rek) * 100,1),
        //         'sisa'=> $crot,
        //         'program'=>$val->program,
        //         'jabatan'=>$val->jabatan,
        //         'kantor'=> $val->unit,
        //         'id_kantor'=> $val->kantor,
        //     ];
        // }
            //   return collect($data);
           
    }
    
  
}


