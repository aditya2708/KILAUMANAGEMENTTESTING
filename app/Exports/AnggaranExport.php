<?php

namespace App\Exports;

use App\Models\Anggaran;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use Auth;

class AnggaranExport  implements FromView
{
    public function __construct($stts,$now,$kntr,$dari,$sampai,$darib,$sampaib,$darit,$sampait,$periode)
    {
        
        $this->stts = $stts;
        $this->now = $now;
        $this->kntr = $kntr;
        $this->dari = $dari;
        $this->sampai = $sampai;
        $this->darib = $darib;
        $this->sampaib = $sampaib;
        $this->darit = $darit;
        $this->sampait = $sampait;
        $this->periode = $periode;
    }

    public function view(): View {  
        
       $stts =  $this->stts;
       $now =  $this->now ;
       $kntr =  $this->kntr ;
       $dari =  $this->dari;
       $sampai =  $this->sampai ;
       $darib =  $this->darib ;
       $sampaib =  $this->sampaib ;
       $darit =  $this->darit ;
       $sampait =  $this->sampait;
       $periode =  $this->periode;
       
        if($periode == 'harian' || $periode == '' ){
             $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
                 ->leftjoin('users','users.id','=','anggaran.user_input')
                 ->selectRaw("anggaran.*, tambahan.unit,users.name")
                 ->whereRaw("$kntr AND $stts AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') >= '$dari' AND DATE_FORMAT(anggaran.tanggal,'%Y-%m-%d') <= '$sampai' ")
                 ->get();
            // $realisasi = Pengeluaran::whereRaw("id = '$datas->id_anggaran'")->get();     
        }else if($periode == 'bulan'){
          $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
                 ->leftjoin('users','users.id','=','anggaran.user_input')
                 ->selectRaw("anggaran.*, tambahan.unit,users.name ")
                 ->whereRaw("$kntr AND $stts AND DATE_FORMAT(anggaran.tanggal,'%Y-%m') >= '$darib' AND DATE_FORMAT(anggaran.tanggal,'%Y-%m') <= '$sampaib' ")
                 ->get();
                
        }else if($periode == 'tahun'){
             $datas = Anggaran::leftJoin('tambahan','tambahan.id','=','anggaran.kantor')
                 ->leftjoin('users','users.id','=','anggaran.user_input')
                 ->selectRaw("anggaran.*, tambahan.unit,users.name ")
                 ->whereRaw("$kntr AND $stts AND DATE_FORMAT(anggaran.tanggal,'%Y') >= '$darit' AND DATE_FORMAT(anggaran.tanggal,'%Y') <= '$sampait' ")
                 ->get();
        }

        return view('ekspor.approveanggaran',[
            'data' => $datas,
            'priode' => $periode == 'harian' || $periode == '' ? 'Anggaran Priode Tanggal '.$dari. ' s/d '. $sampai : 'Anggaran Priode Bulan '.$darib. ' s/d '. $sampaib,
            'company' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name
        ]);
    }
  
}


