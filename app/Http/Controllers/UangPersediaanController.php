<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\SetUP;
use App\Models\Kantor;
use App\Models\Bank;
use App\Models\COA;
use App\Models\User;
use App\Models\GrupCOA;
use Auth;
use DataTables;
use DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Excel;

class UangPersediaanController extends Controller
{

        function uang_persediaan(Request $request)
           {
        $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $bank = COA::whereRaw("grup = 4 AND id_kantor = '$k'")->get();
        // $saldo = Transaksi::select('jumlah')->where('approval',1)->where('via_input','transaksi')->sum('jumlah');
        // $namcoa = COA::whereRaw("grup = 6")->get();
        // $jabat = Jabatan::all();
            
    
      
         $kz = Auth::user()->id_kantor;
        $cek = Kantor::where('kantor_induk', $kz)->first();
        
        if(Auth::user()->level === 'admin' || Auth::user()->keuangan == 'keuangan pusat'){
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else{
            if($cek == null){
                $kantor = Kantor::where('id',Auth::user()->id_kantor)->get();
                
            }else{
                $kantor = Kantor::whereRaw("(id = $kz OR id = $cek->id)")->get();
            }
        }
      
        if($request->ajax())
         {
    
        $now = date('Y-m');
               $kntr = $request->kntr != '' ? "id = $request->kntr" : "id != ''";  
        // $kntr = $request->kntr != '' ? "tambahan.id == $request->kntr" : "tambahan.id != ''";  
        $dari = $request->dari == '' ? date('Y-m') : $request->dari;
        
        $tgl = date('Y-m-01', strtotime($dari));
        $kan = Kantor::where('id_com', Auth::user()->id_com)->whereRaw($kntr)->get();
        $cok = ['bank','bank','cash','cash'];
        $pek = ['transaksi','bulan','transaksi','bulan'];
            foreach ($kan as $datas){
                for ($j = 0; $j < 4; $j++){
                    $up = SetUP::where('id_kantor', $datas->id)->where('jenis', $pek[$j])->where('bayar', $cok[$j])->whereDate('tanggal', $tgl)->first();
                    
                        $bayar[] = [
                        "id" =>$datas->id.'_'.$tgl.'_'.$cok[$j].'_'.$pek[$j],
                        "tanggal" => date('F-Y', strtotime($dari)),
                        "id_kantor" => $datas->id,
                        "nominal" =>   $up != NULL ? $up->nominal : 0,
                        "bayar" =>     $cok[$j],
                        "jenis" =>     $pek[$j],
                        "unit" =>      $datas->unit,
                        ];
                        
                }
            }
          
            
            return DataTables::of($bayar)
    ->make(true);
        }
        return view('setting.uang_persediaan', compact('kantor'));

    }

   
    
//       function post_up(Request $request)
//           {
//         $unlock = preg_split("/_/",$request->id);
//         $id_kantor = $unlock[0];
//         $tgl = $unlock[1];
//         $bayar = $unlock[2];
//         $jenis = $unlock[3];
//         $nominal = $request->nominal;
//         // $useri = $unlock[4];
//         // $format = $tgl.'-01';
        
//         $cekdulu = SetUP::whereRaw("id_kantor = '$id_kantor' AND jenis = '$jenis' AND bayar = '$bayar' AND tanggal = '$tgl' ")->get();
       
//      if(count($cekdulu) > 0){
//       $cekbulan = 'bulan';
//              $cekdataperbulan = SetUP::whereRaw("id_kantor = '$id_kantor' AND jenis = '$cekbulan' AND bayar = '$bayar' AND tanggal = '$tgl' ")->get();
//              $nom = $cekdataperbulan[0]->nominal;
//     if($nom > $nominal)   {
//              $bacaan = 'wkwkwkkwk';
//              return($bacaan);
//     }

//   } else if(count($cekdulu) > 0) {
//           $duar = SetUP::whereRaw("id_kantor = '$id_kantor' AND jenis = '$jenis' AND bayar = '$bayar' AND tanggal = '$tgl' ")->update
//             ([
//                 'nominal' => $nominal,
//             ]);
// }else{
//     $data = new setUP;
//         $data->id_kantor = $id_kantor;
//         $data->jenis = $jenis;
//         $data->bayar = $bayar;
//         $data->tanggal = $tgl;
//         $data->nominal = $request->nominal == '' ? 0 : preg_replace("/[^0-9]/", "", $request->nominal);
//         $data->user_insert = Auth::user()->id;
//         $data->save(); 

// }

//         return response()->json(['success' => 'Data Added successfully.']);
//     }
    
       function post_up(Request $request)
           {
        $unlock = preg_split("/_/",$request->id);
        $id_kantor = $unlock[0];
        $tgl = $unlock[1];
        $bayar = $unlock[2];
        $jenis = $unlock[3];
        $nominal = $request->nominal;
        // $useri = $unlock[4];
        // $format = $tgl.'-01';
        
        $cekdulu = SetUP::whereRaw("id_kantor = '$id_kantor' AND jenis = '$jenis' AND bayar = '$bayar' AND tanggal = '$tgl' ")->get();
       
     if(count($cekdulu) > 0){
      $cekbulan = 'bulan';
             $cekdataperbulan = SetUP::whereRaw("id_kantor = '$id_kantor' AND jenis = '$cekbulan' AND bayar = '$bayar' AND tanggal = '$tgl' ")->get();
             $nom = $cekdataperbulan[0]->nominal;
    if($nom <= $nominal)   {
             $bacaan = 'Nominal yang di masukan lebih besar dari settingan bulanan';
             return($bacaan);
    }else{
          $duar = SetUP::whereRaw("id_kantor = '$id_kantor' AND jenis = '$jenis' AND bayar = '$bayar' AND tanggal = '$tgl' ")->update
            ([
                'nominal' => $nominal,
            ]);
             $bacaan = 'Berhasil';
             return($bacaan);
    }
}else{
    $data = new setUP;
        $data->id_kantor = $id_kantor;
        $data->jenis = $jenis;
        $data->bayar = $bayar;
        $data->tanggal = $tgl;
        $data->nominal = $request->nominal == '' ? 0 : preg_replace("/[^0-9]/", "", $request->nominal);
        $data->user_insert = Auth::user()->id;
        $data->save(); 

}
            $bacaan = 'Berhasil';
             return($bacaan);

        // return response()->json(['success' => 'Data Added successfully.']);
    }
    
    
      function cek_persediaan(Request $request)
          
           {
               
        $tgl = $request->tanggal != '' ? $request->tanggal : date('Y-m');
        $format = $tgl.'-01';
        $cekdulu = SetUP::whereRaw("id_kantor = '$request->kantor' AND jenis = '$request->jenis' AND bayar = '$request->bayar' AND tanggal = '$format' ")->get();
        
        return($cekdulu);

    }
    
    
      
}