<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenlapKeuangan;
use App\Models\RumlapKeuangan;
use App\Models\SaldoAw;
use App\Models\Kantor;
use App\Models\COA;
use Carbon\Carbon;
use Auth;
use DB;
use DateTime;
use Staudenmeir\LaravelCte;
use DataTables;
use Excel;
use PDF;
use App\Exports\JenisLaporanExport;
class SettingLaporanController extends Controller
{

    function jenis_laporan(Request $request){
        $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $jenislap = JenlapKeuangan::whereRaw("aktif = 'y'")->get();
        // $saldo = Transaksi::select('jumlah')->where('approval',1)->where('via_input','transaksi')->sum('jumlah');
        // $namcoa = COA::whereRaw("grup = 6")->get();
        // $jabat = Jabatan::all();
        $id_jenlap = $request->jnisss != '' ? "rumlap_keuangan.id_jenlap = $request->jnisss" : "rumlap_keuangan.id_jenlap != ''";  
    
        $pilihparent = RumlapKeuangan::whereRaw("$id_jenlap AND perent = 'y' ")->get();
      
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
    
        
      
        $laporan = RumlapKeuangan::select('rumlap_keuangan.*')->whereRaw("$id_jenlap")->orderBy('rumlap_keuangan.urutan', 'ASC')->get();
        return DataTables::of($laporan)
        ->addIndexColumn()
            ->addColumn('namanya', function ($laporan) {
                if($laporan->rumus == '' && $laporan->level == '1' && $laporan->perent == 'y' ){  
                    $c = '<b>'. $laporan->nama. '</b>';
                }else if($laporan->rumus != '' && $laporan->level == '2' && $laporan->nama == 'Penyaluran' && $laporan->perent == 'y' || $laporan->nama ==  'Penerimaan' && $laporan->perent == 'y' || $laporan->perent == 'y'){
                    $c = '&nbsp; <b>'. $laporan->nama. '</b>';
                }else if($laporan->level == '1' && $laporan->rumus != '' && $laporan->perent == 'y'){
                   $c = '<b>'. $laporan->nama. '</b>';
                }else {
                      $c = '&nbsp;&nbsp;'.$laporan->nama;
                }
            
                return $c;
            })
            
             ->addColumn('aksi', function ($laporan) {
                // if ($laporan->aktif  == 'n' ) {
                //         $c = 'checked';
                //     } else {
                //         $c = '';
                //     }
                
                //   $button = '<div class="btn-group"><span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="' . ($laporan->aktif  == 'n' ? 'Nonaktif' : 'Aktif') . '"><label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'))" id="checkbox" class="toggle-class" data-id="' . $laporan->id . '"  type="checkbox" ' . ($laporan->aktif  == 'n' ? "" : "checked") . ' /> <div class="slider round"> </div> </label></span>';
                //          $button .= '<button type="button" class="btn btn-rounded btn-sm btn-primary kirimid" data-bs-toggle="modal" data-bs-target="#modalkwitansi" id="'.$laporan->id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Kirim Kwitansi" style="margin-left: 5%"><i class="fa fa-paper-plane"></i></button><div>';
                //         // $button .= '<button type="button" class="btn btn-rounded btn-sm btn-danger downurutan" name="edit" id="' . $laporan->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Turun Urutan" style="margin-left: 5%"> <i class="fa-solid fa-up"></i></button>';
                //         $button .= '<button type="button" class="btn btn-rounded btn-sm btn-danger downurutan" name="edit" id="' . $laporan->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Turun Urutan" style="margin-left: 5%"> <i class="fa-solid fa-up"></i></button>
                //         <div>';
                
                    $button = '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="' . ($laporan->aktif  == 'n' ? 'Nonaktif' : 'Aktif') . '"><label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'))" id="checkbox" class="toggle-class" data-id="' . $laporan->id . '"  type="checkbox" ' . ($laporan->aktif  == 'n' ? "" : "checked") . ' /> <div class="slider round"> </div> </label></span>';
                   
                   
                    //  $button =  '<div class="btn-group mb-1">
                    //                  <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="' . ($laporan->aktif  == 'n' ? 'Nonaktif' : 'Aktif') . '"><label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'))" id="checkbox" class="toggle-class" data-id="' . $laporan->id . '"  type="checkbox" ' . ($laporan->aktif  == 'n' ? "" : "checked") . ' /> <div class="slider round"> </div> </label></span>
                    //                 <button type="button" class="btn btn-rounded btn-sm btn-danger downurutan" name="edit" id="' . $laporan->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Untuk Turun Urutan" style="margin-left: 5%"> <i class="fa fa-paper-plane"></i></button>
    
                    //                 </div>';
                   
                    return $button;
            })
            
            
              ->addColumn('naik', function ($laporan) {
                // if ($laporan->aktif  == 'n' ) {
                //         $c = 'checked';
                //     } else {
                //         $c = '';
                //     }
                    // $button = '<input type="checkbox" id="toggle-two"  class="toggle-class " data-id="'. $kerja->id .'" data-toggle="toggle" data-style="slow" data-on="Onsite" data-off="Off"  >';
                    $button = '<div class="btn-group"><a class="btn btn-rounded btn-primary btn-sm upurutan" no="1" id2="naikan" id="' . $laporan->id . '" jenlap="' . $laporan->id_jenlap . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top"   title="Klik Naikan Urutan" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Naikan Urutan"> <i class="fa fa-angle-up"></i></a>';
                    return $button;
            })
                ->addColumn('turun', function ($laporan) {
                // if ($laporan->aktif  == 'n' ) {
                //         $c = 'checked';
                //     } else {
                //         $c = '';
                //     }
                    // $button = '<input type="checkbox" id="toggle-two"  class="toggle-class " data-id="'. $kerja->id .'" data-toggle="toggle" data-style="slow" data-on="Onsite" data-off="Off"  >';
                    $button = '<div class="btn-group"><a class="btn btn-rounded btn-primary btn-sm downurutan"  no="1" id2="turunkan" id="' . $laporan->id . '"  jenlap="' . $laporan->id_jenlap . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top"   title="Klik Turunkan Urutan" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Turun Urutan"><i class="fa fa-angle-down"></i></a>';
                    return $button;
            })
            ->addColumn('hapus', function ($laporan) {

                    $button = '<div class="btn-group"><a class="btn btn-rounded btn-danger btn-sm hapus"  no="1" urutan="'. $laporan->urutan.'" id="' . $laporan->id . '"  jenlap="' . $laporan->id_jenlap . '" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top"   title="Klik Hapus Data" data-bs-toggle="tooltip" data-bs-placement="top" title="Klik Hapus Data"><i class="fa fa-trash"></i></a>';
                    return $button;
            })
            
        ->rawColumns(['namanya','aksi','naik','turun','hapus'])
        ->make(true);
        }
        return view('setting.jenis_laporan',compact('jenislap','pilihparent'));

    }
    
    function post_laporan(Request $request){
        $data = new JenlapKeuangan;
        $data->deskripsi = $request->deksirpsi;
        $data->aktif = $request->status;
        $data->save(); 
        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    function list_laporan(Request $request){
                if($request->ajax())
         {
    
        $lapor = JenlapKeuangan::select('jenlap_keuangan.*')->get();
         }
       return DataTables::of($lapor)
        ->make(true);
    }

    function post_rumus(Request $request){
        // $cek = RumlapKeuangan::whereRaw("urutan = $request->urutan AND $stts");
        $cek = RumlapKeuangan::select('id','nama','urutan')->where('urutan', '>=', $request->urutan)->where('id_jenlap',$request->id_jenlap)->get();

        if(count($cek) >= 1){
             foreach ($cek as $data) {
                $tambah = $data->urutan + 1;
                $data->update(['urutan' => $tambah]);
            }
        }
        $data = new RumlapKeuangan;
        $data->nama = $request->nama;
        $data->rumus = $request->rumus;
        $data->level = $request->level;
        $data->urutan = $request->urutan;
        $data->id_jenlap = $request->id_jenlap;
        $data->perent = $request->perent;
        $data->perent_id = $request->id_parent;
        $data->indikator = 'coa';
        $data->aktif = 'y';
        $data->save(); 
        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    function edit_rumus_stts(Request $request){
    $data = RumlapKeuangan::where('id',$request->id)->first();
    $aktif = $data->aktif;
         if($aktif == 'y'){
               RumlapKeuangan::where('id', $request->id)->update([
                'aktif' =>'n',
            ]);
         }else{
               RumlapKeuangan::where('id', $request->id)->update([
               'aktif' =>'y',
            ]);
         }
          
        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    function edurut_rumus(Request $request){
        $data = RumlapKeuangan::where('id',$request->id)->first();
        $aksi = $request->id2;
        $wot = RumlapKeuangan::select('id','urutan','perent')->where('id',$request->id)->first();
        $datper = RumlapKeuangan::where('id',$data->perent_id)->first();
        
        $datperlain = RumlapKeuangan::where('perent_id',$datper->perent_id)->orderBy('urutan','DESC')->first();
        $perentlain = RumlapKeuangan::where('perent_id',$datperlain->id)->orderBy('urutan','ASC')->first();
        
        $bukandatapil = RumlapKeuangan::where('id',$perentlain->id)->orWhere('perent_id',$perentlain->id)->get(); 
        $datapil = RumlapKeuangan::where('id',$data->id)->orWhere('perent_id',$data->id)->get(); 


                    $naik = $wot->urutan - 1 ;
                    $turun = $wot->urutan + 1 ;
                    $sttsperent = $wot->perent ;

                if($aksi == 'naikan'){
                    
                    if($sttsperent != 'n'){
                        
                        RumlapKeuangan::where('id',$perentlain->id)->orWhere('perent_id',$perentlain->id)->update([
                                        'urutan' => \DB::raw('urutan + 1') ]);   
                        
                        RumlapKeuangan::where('perent_id', $data->id)->orWhere('id', $data->id)->update([
                                        'urutan' => \DB::raw('urutan - 1') ]);   
                                        
                                        
                    }else if($naik <= $datper->urutan ){
                     return response()->json(['gagal' => 'Data gagal.']);
                    }else{
                        
                        //yang diturunkan
                        RumlapKeuangan::where('urutan', $naik)
                                          ->where('id_jenlap',$request->jenlap)
                                          ->update([
                                            'urutan' => $wot->urutan ]);    
                        
                            
                            //yang dinaikan
                        RumlapKeuangan::where('id',$wot->id)
                                          ->where('id_jenlap',$request->jenlap)
                                          ->update([
                                            'urutan' => $naik]);
                        
                    }    
                    
                 
            
                  
                }else{
                     if($sttsperent != 'n'){
                        
                        RumlapKeuangan::where('id',$perentlain->id)->orWhere('perent_id',$perentlain->id)->update([
                                        'urutan' => \DB::raw('urutan - 1') ]);   
                        
                        RumlapKeuangan::where('perent_id', $data->id)->orWhere('id', $data->id)->update([
                                        'urutan' => \DB::raw('urutan + 1') ]);   
                                        
                                        
                    }else if($turun >= $datperlain->urutan ){
                     return response()->json(['gagal' => 'Data gagal.']);
                    }else{
                    //yang dinaikan
                    RumlapKeuangan::where('urutan', $turun)
                                  ->where('id_jenlap',$request->jenlap)
                                  ->update([
                                    'urutan' =>  $wot->urutan ]);  
                                    
                    //yang diturunkan
                    RumlapKeuangan::where('id',$wot->id)
                                  ->where('id_jenlap',$request->jenlap)
                                  ->update([
                                    'urutan' => $turun]);

                                    
                    }
                    
                }
          
          
        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    public function lapBy(Request $request, $id){
        $data['ui'] = JenlapKeuangan::whereRaw("id = '$id'")->first();
        return $data;
    }
    
    public function rumBy(Request $request, $id){
        $data['ui'] = RumlapKeuangan::whereRaw("id = '$id'")->first();
        return $data;
    }
    
    public function edlap_stat(Request $request){
            JenlapKeuangan::where('id', $request->id)->update([
                'deskripsi' =>$request->deksirpsi,
                'aktif' =>$request->status,
            ]);

         return response()->json(['success' => 'Data Added successfully.']);
    }
    
    public function edrum(Request $request){
            RumlapKeuangan::where('id', $request->id)->update([
                'nama' =>$request->ednama,
                'rumus' =>$request->edrumus,
                'level' =>$request->edlevel,
                'indikator' =>$request->edindikator,
                
            ]);

         return response()->json(['success' => 'Data Added successfully.']);
    }
    
    public function laporan_keuangan1(Request $request){
       
        $query = DB::table('coa as t1')
                    ->select('t1.*', 't1.id as root')
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->select('t3.*', 't0.root')
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                    );
        
        
         $k = Auth::user()->id_kantor;
        $kan = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();  

        if(Auth::user()->level == 'admin'){
             $kantor = Kantor::whereRaw("id_coa != '' ")->get();
        }elseif(Auth::user()->level == 'kacab'){
            if($kan == null){
                $kantor = Kantor::whereRaw("id = $k")->get();
            }else{
                $kantor = Kantor::whereRaw("(id = $k OR id = $kan->id)")->get();
            }
        }
      $jenis = JenlapKeuangan::where('aktif','y')->get();
        
        if ($request->ajax()) {
            $kntr = $request->kota == '' ? "id_kantor = ''" : "id_kantor = '$request->kota'";
            if($request->mulbul == 1){
                $bulans = !$request->bln2 ? date('m') : $request->bln2[count($request->bln2)-1];
            }else{
                $bulans = $request->bln == '' ? date('m') : $request->bln;
            }
          
            

            $p = $request->tahuns == '' ? date('Y') : $request->tahuns;
            $currentYear = $p;
            $range = 1; // rentang tahun yang ingin ditampilkan
            $oldestYear = $currentYear - $range;
            
      
        
          
            $d = [];
            
            for ($year = $currentYear; $year >= $oldestYear; $year--) {
                $d[] = $year;
            }
            

            // return($d);
            $baru = SaldoAw::selectRaw("DISTINCT(bulan)")->whereYear('bulan',$d[0])->latest();
            
            $ngitung = count($baru->get());
            // return($baru->first()->bulan);
            
            if($ngitung > 0){
                $l = date('m', strtotime($baru->first()->bulan));
            }else{
                $l = '12';
            }
    
          
      if($request->jenis == '2'){
                $tet = DB::table('b as t')
                    ->selectRaw("root, t.coa, t.id_parent, t.level ,t.nama_coa, SUM(t2.konak) as saldo1, SUM(t3.konak) as saldo2, t.id, t.grup, t.id_kantor")
                    ->withRecursiveExpression('b', $query)
                    
                    ->leftjoin('saldo_awal as t2',function($join) use ($d,$bulans) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t2.bulan) = '$d[1]' AND MONTH(t2.bulan) = '$bulans'");
                    })
                    
                    ->leftjoin('saldo_awal as t3',function($join) use ($d,$l,$bulans) {
                            $join->on('t3.coa' ,'=', 't.coa')
                                ->whereRaw("YEAR(t3.bulan) = '$d[0]' AND MONTH(t3.bulan) = '$bulans'");
                    })
                    
                    ->leftjoin('saldo_awal as t4',function($join) use ($kntr) {
                            $join->on('t4.coa' ,'=', 't.coa')
                                ->whereRaw("$kntr");
                    })
                
                    ->groupBy('root')
                    ->get();
                    
      }else if($request->jenis == '1'){
          
            $tet = RumlapKeuangan::where('aktif','y')->get();
            // $tet = DB::table('b as t')
            //         ->selectRaw("root, t.nama, t.id_parent, t.level ,t.nama_coa, SUM(t2.konak) as saldo1, SUM(t3.konak) as saldo2, t.id, t.grup, t.id_kantor")
            //         ->withRecursiveExpression('b', $query)
                    
            //         ->leftjoin('saldo_awal as t2',function($join) use ($d,$bulans) {
            //                 $join->on('t2.coa' ,'=', 't.coa')
            //                     ->whereRaw("YEAR(t2.bulan) = '$d[1]' AND MONTH(t2.bulan) = '$bulans'");
            //         })
            //     ->groupBy('root')
            //     ->get();
                
          foreach($tet as $s){
                    $a[] = [
                        'nama' => $s->nama,
                        'rumus' =>  preg_split("/[\+]+/",$s->rumus),
                        'level' => $s->level,
                    ];
            }
            
             for ($j = 0; $j < count($a); $j++){
                    $up = COA::selectRaw("SUM(coa.konak) as saldo1")->where('coa', $a[$j])->get();
                        // $jaran[] = [
                        // "nama" => $up->nama_coa,
                        // "rumus" =>     $a[$j],
                        // "saldo1" =>$up->saldo1,
                        // ];
                        
                }    
            
        //  $teto = coa::selectRaw("SUM(coa.konak) as saldo1")
        //  ->where('coa',$a)->get();
          
      }
         
     
            // $data = [];
            // $a = [];
            // foreach($saldo as $keys => $s){
                
            //         $a[] = [
            //             'coa' => $s->coa,
            //             'nama_coa' => $s->nama_coa,
            //             'id_kantor' => $s->id_kantor,
            //             'level' => $s->level,
            //             'grup' => $s->grup,
            //             'id_parent' => $s->id_parent,
            //             'saldo1' => $s->saldo1,
            //             'saldo2' => $s->saldo2,
            //         ];
            // }
            
                   

            // foreach ($a as $da) {
            //     if ($da['level'] != '3' && $da['level'] != '4'  && $da['grup'] == '1,9' || $da['grup'] == '6' && $da['level'] != '2'  ) {
            //         $data[] = [
            //             'coa' => $da['coa'],
            //             'nama_coa' => $da['nama_coa'],
            //             'level' => $da['level'],
            //             'grup' => $da['grup'],
            //             'id_parent' => $da['id_parent'],
            //             'saldo1' => $da['saldo1'] == null ? 0 : $da['saldo1'],
            //             'saldo2' => $da['saldo2'] == null ? 0 : $da['saldo2'],
            //         ];
            //     }
            // }

          

            return DataTables::of($up)
            // ->addColumn('coah', function($a){
            //     if($a->rumus == '0' && $a->level == '1' ){  
            //         $c = '<b>'. $tet->nama. '</b>';
            //     }else if($a->rumus != '0' && $a->level == '2' && $a->nama == 'Penyaluran' || $a->nama ==  'Penerimaan' ){
            //         $c = '&nbsp; <b>'. $a->nama. '</b>';
            //     }else if($a->level == '1' && $a->rumus != '0'){
            //       $c = '<b>'. $a->nama. '</b>';
            //     }else {
            //           $c = '&nbsp;&nbsp;'.$a->nama;
            //     }
            //     return $c;
            // })
            // ->rawColumns(['coah'])
            ->make(true);
        }
        
        return view ('fins-laporan.laporan_keuangan',compact('kantor','jenis'));
    }
    
    public function export(Request $request){
            
       $jenis = $request->jns ;  
     $deskripsi = DB::table('jenlap_keuangan')->where('jenlap_keuangan.id', $jenis)->first()->deskripsi;
        if($request->tombol == 'xls'){
            $response =  Excel::download(new JenisLaporanExport( $jenis), $deskripsi.'.xlsx');

        }else{
            $response =  Excel::download(new JenisLaporanExport( $jenis), $deskripsi.'.csv');
        }
        ob_end_clean();
        
        return $response;
    }
    
    public function destroyy( $id)
{
    $data = RumlapKeuangan::findOrFail($id);
    $cek = RumlapKeuangan::select('id','nama','urutan')->where('urutan', '>=', $data->urutan)->where('id_jenlap',$data->id_jenlap)->get();

    if(count($cek) >= 1){
             foreach ($cek as $dat) {
                $kurang = $dat->urutan - 1;
                $dat->update(['urutan' => $kurang]);
            }
        }

    \LogActivity::addToLog(Auth::user()->name.' Menghapus Data Rumus Laporan '.$data->nama);
    $data->delete();
    return response()->json(['success' => 'Data is successfully updated']);
}
 
     public function parentcoa(Request $request){
        // $id_jenlap = $request->jenis != '' ? "id_jenlap = $request->jenis" : "id_jenlap != ''";  

        $jenisnya = $request->jenis == '' ? 1 : $request->jenis;  
        $coa_parent= RumlapKeuangan::whereRaw("id_jenlap = '$jenisnya' AND perent = 'y' ")->orderBy('urutan', 'ASC')->get(); 
        foreach($coa_parent as $key => $val){
            $h1[] = [
                "text" => $val->nama.'- '.$val->rumus,
                "rumus" => $val->rumus,
                "nama" => $val->nama,
                "id" => $val->id,
                "parent" => $val->parent,
                "level" => $val->level,
            ];
        }
        return response()->json($h1);
    }
    
 
    
}