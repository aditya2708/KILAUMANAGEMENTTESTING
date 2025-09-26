<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jabatan;
use App\Models\Daerah;
use App\Models\Karyawan;
use App\Models\SkemaGaji;
use App\Models\Terlambat;
use App\Models\User;
use App\Models\Tunjangan;
use App\Models\Kantor;
use App\Models\Bpjs;
use App\Models\Profile;
use DataTables;
use Auth;

class TunjanganController extends Controller
{
    public function index (Request $request) 
    {
      
        $company = Profile::where(function($query) {
                        if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                            $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })
                    ->get();
                    
        // Ambil data dari database
        $jabatan = Jabatan::where('id_com', Auth::user()->id_com)->orderBy('pr_jabatan','ASC')->get();
        
        $daerah = Daerah::all();
        $daerah = Daerah::all();
        
         if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                  $bpjs = Bpjs::where('id_com',  $request->com)->get();
          }else{
                  $bpjs = Bpjs::where('id_com', Auth::user()->id_com)->get();
          }
        $bpjsnama = Bpjs::limit(5)->get();
        
        if($request->ajax())
        {

            if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                $data = Tunjangan::where('id_com',$request->com)->get();
            }else{
                $data = Tunjangan::where('id_com',Auth::user()->id_com)->get();

            }
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('tjber', function($data){
                        $tjberas = 'Rp.'.number_format($data->tj_beras, 0, ',', '.');
                        return $tjberas;
                    })
                    ->addColumn('jmlber', function($data){
                        $jmlber = $data->jml_beras .' kg';
                        return $jmlber;
                    })
                    ->addColumn('tjpas', function($data){
                        $tjpas = $data->tj_pasangan .'%';
                        return $tjpas;
                    })
                    ->addColumn('tjan', function($data){
                        $tjanak = $data->tj_anak .'%';
                        return $tjanak;
                    })
                    ->addColumn('tjtrans', function($data){
                        $tjtrans = 'Rp.'.number_format($data->tj_transport, 0, ',', '.');
                        return $tjtrans;
                    })
                    ->addColumn('potongan', function($data){
                        $pot = 'Rp.'.number_format($data->potongan, 0, ',', '.');
                        return $pot;
                    })
                    ->addColumn('action', function($data){
                        $button = ' <div class="btn-group">
                                        <a href="#" class="gettt btn btn-success btn-sm edit" id="'.$data->id_tj.'" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-edit"></i></a>
                                    </div>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        
        $kntr = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', $kntr)->first();
        
        $kota = Kantor::where('tambahan.id_com', Auth::user()->id_com)->where(function($query) use ($k, $kntr){
                    if(Auth::user()->kepegawaian == 'kacab'){
                        if($k == null){
                            $query->where('id', Auth::user()->id_kantor);
                        }else{
                            $query->whereRaw("id = '$k->id' OR id = '$kntr'");
                        }
                    }
                })->get(); 
        
        return view ('setting.management_gaji',compact('jabatan','daerah','bpjsnama','bpjs','company', 'kota'));
    }
    
    private function buildHierarchy($jabatans, $parentId = null)
    {
        $tree = [];
        foreach ($jabatans as $jabatan) {
            if ($jabatan->pr_jabatan == $parentId) {
                $children = $this->buildHierarchy($jabatans, $jabatan->id);
                if (!empty($children)) {
                    $jabatan->children = $children;
                }
                $tree[] = $jabatan;
            }
        }
        return $tree;
    }
    
    public function edit($id){
            $data = Tunjangan::findOrFail($id);
            return response()->json(['result' => $data]);
    }
    
    public function getjabatan(Request $request)
    {
          if($request->com != ''){
                 $data = Jabatan::where('id_com',$request->com)->get();
            }else{
                 $data = Jabatan::where('id_com',Auth::user()->id_com)->get();
            }
            
        // $data = Jabatan::all();
        return response()->json(['result' => $data]);
    }
    
    public function gettunjangan(Request $request)
    {
          if($request->com != ''){
                  $data = Tunjangan::where('id_com',  $request->com)->get();
          }else{
                  $data = Tunjangan::where('id_com', Auth::user()->id_com)->get();
          }
           
          if($request->id_kar != ''){
              $ii = Karyawan::where('id_karyawan', $request->id_kar)->first()->id_daerah;
          }else{
              $ii = 0;
          }
           
          if($request->com != ''){
                  $datay = Daerah::where('id_com',  $request->com)->get();
          }else{
                  $datay = Daerah::where('id_com', Auth::user()->id_com)->where('id_daerah',$ii)->first();
          }      
        // $data = Tunjangan::all();
        
        return response()->json(['result' => $data, 'hasil' => $datay]);
    }

    public function update(Request $request) {
        
        if($request->ajax())
            {
                $id = $request->id_tj;
                $kinerja = Tunjangan::findOrFail($id);
            
                $beras = $request->tj_beras != '' ? preg_replace("/[^0-9]/", "", $request->tj_beras) : 0;
                $transport = $request->tj_transport != '' ? preg_replace("/[^0-9]/", "", $request->tj_transport) : 0;
                $potongan = $request->potongan != '' ? preg_replace("/[^0-9]/", "", $request->potongan) : 0;
                // dd($beras);
                $input = array(
                    'tj_beras' => $beras,
                    'jml_beras' => $request->jml_beras,
                    'tj_pasangan' => $request->tj_pasangan,
                    'tj_anak' => $request->tj_anak,
                    'tj_transport' => $transport,
                    'potongan' => $potongan,
                    'tj_plt' => $request->tj_plt,
                    );  
          
                $kinerja->update($input);
                \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Setting Tunjangan Beras');
            }
        
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function update_bpjs(Request $req){
        // return($req);
        $tot = count($req->nama);
        for($i = 0 ; $i < $tot; $i++){
            if($req->modal == 'bpjs'){
                $dataa = [
                    'karyawan' => $req->jenis[$i]
                ];
            }else if($req->modal == 'ketenagakerjaan'){
                $dataa = [
                    'perusahaan' => $req->jiji[$i]
                ];
            }else if($req->modal == 'kesehatan'){
                $dataa = [
                    'perusahaan' => $req->jiju[$i]
                ];
            }
            
            Bpjs::where(function($query) use ($req, $i){
                    if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                        $query->where('id_com', $request->com)
                        ->where('id_bpjs', $req->nama[$i]);
                    }else{
                        $query->where('id_com', Auth::user()->id_com)
                        ->where('id_bpjs', $req->nama[$i]);
                    }
            })
            ->update($dataa);
                    
                
        }
        
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Setting BPJS');
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
     public function updatebpjs(Request $request) {
        if($request->ajax())
        {

        $cari = Bpjs::where(function($query) use ($request){
                        if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                            $query->where('id_com', $request->com);
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })
          ->get();
        $itung = count($cari);
        if($cari->count() == 0 ){
            $nenen = count($request->jenis);
            // dd($nenen);
             for($i = 0 ; $i < $nenen; $i++){
                    $data = new Bpjs;
                    $data->nama_jenis = $request->jenis[$i];
                    $data->perusahaan = $request->empty_perusahaan[$i];
                    $data->karyawan = $request->empty_karyawan[$i];
                    $data->id_com = $request->com;
                    $data->save(); 
                 
                // Bpjs::where('id_bpjs', $request->id_bpjs[$i])->update([
                //     'nama_jenis' => $request->jenis[$i],
                //     'perusahaan' => $request->perusahaan[$i],
                //     'karyawan' => $request->karyawan[$i]
                // ]);
                
            }
        
              return response()->json(['success' => 'Data is successfully Added']);
        }else{
            
            $tot = count($request->jenis);
            for($i = 0 ; $i < $tot; $i++){
                Bpjs::where(function($query) use ($request){
                        if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                            $query->where('id_com', $request->com)
                            ->where('nama_jenis', $request->jenis[$i]);
                        }else{
                            $query->where('id_com', Auth::user()->id_com)
                            ->where('nama_jenis', $request->jenis[$i]);
                        }
                    })->update([
                    'nama_jenis' => $request->jenis[$i],
                    'perusahaan' => $request-> perusahaan[$i],
                    'karyawan' => $request->karyawan[$i]
                ]);
            }
            \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Setting BPJS');
            
            // $id = $request->id_hehe;
            // $beras = $request->umr != '' ? preg_replace("/[^0-9]/", "", $request->umr) : 0;
            // $gg = Tunjangan::where('id_tj',$id)->update(['umr' => $beras, 'persentasi'=> $request->persentasi]);
            }
        }

        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function updatembl(Request $request) {
        // return($request);
        
        // if($request->ajax())
        //     {
        if($request->id_h == ''){
              $data = new Tunjangan;
                    $data->sokotak = $request->sokotak;
                    $data->kolektor = $request->jabatan;
                    $data->spv_kol = $request->spv;
                    $data->spv_so = $request->spv_so;
                    $data->so = $request->so;
                    $data->id_com = $request->com;
                    $data->save();
        }else{
                $id = $request->id_h;
                $gg = Tunjangan::where('id_tj',$id)->update([
                    'sokotak' => $request->sokotak,
                    'kolektor'=> $request->jabatan, 
                    'spv_kol' => $request->spv, 
                    'spv_so' => $request->spv_so,
                    'so' => $request->so,
                    // 'sokotak' => $request->sales,
                ]);
        }
            // }

        return response()->json(['success' => 'Data is successfully updated']);
    }
    
     public function updatetj(Request $request) {
        if($request->ajax())
            {
                $count = Jabatan::all();
                $hitung = count($count);
                for($i=0; $i < $hitung; $i++){
                    $idx = $request->input('input'.$i) != '' ? preg_replace("/[^0-9]/", "", $request->input('input'.$i)) : 0;
                    $cek = $request->input('cek'.$i) != "" ? "1" : "0";
                    $kondisi = $request->input('kon_plt'.$i) != '' ? $request->input('kon_plt'.$i) : NULL ;
                    if($request->input('kon_plt'.$i) == 'n'){
                        $vaa = preg_replace("/[^0-9]/", "", $request->input('nom'.$i));
                    }else if($request->input('kon_plt'.$i) == 'p'){
                        $vaa = $request->input('pres'.$i);
                    }else{
                        $vaa = NULL;
                    }
                    
                    // $idx1 = $request->input('jab_daerah'.$i) != '' ? preg_replace("/[^0-9]/", "", $request->input('jab_daerah'.$i)) : 0;
                    // dd($cek);
                    $idz = $request->input('hide_id'.$i); 
                    // $input = array('tj_jabatan' => $idx);
                    $gg = Jabatan::where('id',$idz)->update([
                        'tj_jabatan' => $idx,
                        'tj_plt' => $vaa,
                        'tj_training' => $cek,
                        'kon_tj_plt' => $kondisi,
                    ]);
                }
                \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Setting Tunjangan Jabatan');
            }

        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function tambahh(Request $request){
        if($request->ajax()){
            $daerah = Daerah::all();
            $tot_arr = count($request->arr);
            for($i = 0; $i<$tot_arr; $i++){
                
                foreach($daerah as $val){
                    if($request->arr[$i]['id_daerah'] == $val->id_daerah){
                        Daerah::where('id_daerah', $request->arr[$i]['id_daerah'])->update([
                            'id_provinsi' => $request->arr[$i]['id_provinsi'],
                            'id_kota' => $request->arr[$i]['id_kota'],
                            'kota' => $request->arr[$i]['kota'],
                            'tj_daerah' => $request->arr[$i]['tj_daerah'] != '' ? preg_replace("/[^0-9]/", "", $request->arr[$i]['tj_daerah']) : 0,
                            'tj_jab_daerah' => $request->arr[$i]['tj_jab_daerah'] != '' ? preg_replace("/[^0-9]/", "", $request->arr[$i]['tj_jab_daerah']) : 0,
                            'umk' => $request->arr[$i]['umk'] != '' ? preg_replace("/[^0-9]/", "", $request->arr[$i]['umk']) : 0,
                            'id_com' => Auth::user()->level == 0 ? Auth::user()->id_com : $request->arr[$i]['id_com'] , 
                        ]);
                    }
                }
                if($request->arr[$i]['id_daerah'] == 0){
                    $data = new Daerah;
                    $data->id_provinsi = $request->arr[$i]['id_provinsi'];
                    $data->id_kota = $request->arr[$i]['id_kota'];
                    $data->kota = $request->arr[$i]['kota'];
                    $data->tj_daerah = $request->arr[$i]['tj_daerah'] != '' ? preg_replace("/[^0-9]/", "", $request->arr[$i]['tj_daerah']) : 0;
                    $data->tj_jab_daerah = $request->arr[$i]['tj_jab_daerah'] != '' ? preg_replace("/[^0-9]/", "", $request->arr[$i]['tj_jab_daerah']) : 0;
                    $data->umk = $request->arr[$i]['umk'] != '' ? preg_replace("/[^0-9]/", "", $request->arr[$i]['umk']) : 0;
                    $data->id_com = Auth::user()->level == 0 ? Auth::user()->id_com : $request->arr[$i]['com']; 
                    $data->save();
                }
                
            }
            
            // \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Setting Tunjangan Daerah');
            
        // $b = [];
        // $h = [];
        // $z = [];
        // $last = Daerah::orderBy('id_daerah','desc')->first();
        // $count = Daerah::all();
        // $hitung = count($count);
        // if($hitung == 0){
        //     for($i=1; $i >= 1; $i++){
        //         $b[] = $request->input('kota'.$i);
        //         $h[] = $request->input('tj_daerah'.$i);
        //         $z[] = $i;
        //         // $input = array(
        //         //     'kota' => $request->input('kota'.$i),
        //         //     'tj_daerah' => $request->input('tj_daerah'.$i)
        //         //     );
        //         // Daerah::truncate();
        //         // Daerah::create($input);
        //     }
        //         dd($b, $h, $z);
        //     return response()->json(['success' => 'Data Added successfully.']);
        // }else{
        //     for($i=$hitung; $i <= 50   ; $i++){
        //         $b[] = $request->input('kota'.$i);
        //         $h[] = $request->input('tj_daerah'.$i);
        //         $z[] = $i;
        //         // $input = array(
        //         //     'kota' => $request->input('kota'.$i),
        //         //     'tj_daerah' => $request->input('tj_daerah'.$i)
        //         //     );
        //         // Daerah::truncate();
        //         // Daerah::create($input);
        //     }
        //     dd([$b, $h, $z]);
        //     }
                return response()->json(['success' => 'Data Added successfully.']);
        }
    }
    
    public function getdaerah(Request $request){
        if($request->com != ''){
            $data = Daerah::where('id_com',$request->com)->get();
        }else{
            $data = Daerah::where('id_com',Auth::user()->id_com)->get();
        }
        return response()->json(['result' => $data]);
    }
    
    public function getlast(){
        $count = Daerah::all();
        $data = [
            'daerah' => Daerah::orderBy('id_daerah','desc')->first(),
            'count' => count($count),
            ];
        return response()->json(['result' => $data]);
    }
    
    public function ubahh(Request $request) {
        
        if($request->ajax())
        {
            // dd($request->id);
            $tot_arr = count($request->id);
            $kota = [];
            $tj = [];
            $id = [];
            foreach($request->kota as $key => $val){
                $kota[] = $val;
            }
            foreach($request->tj_daerah as $key => $val){
                $tj[] = $val;
            }
            foreach($request->id as $key => $val){
                $id[] = $val;
            }
            for($i = 0; $i < $tot_arr; $i++){
                Daerah::where('id_daerah', $id[$i])->update([
                    'kota' => $kota[$i],
                    'tj_daerah' => $tj[$i],
                ]);
            }
                // $count = Daerah::all();
                // $hitung = count($count);
                // for($i=1; $i<= $hitung; $i++){
                //     $idd = $request->input('hide_id'.$i); 
                //     $kota = $request->input('kota'.$i) ;
                //     $tj = $request->input('tj_daerah'.$i);
                //     $gg = Daerah::where('id_daerah',$idd)->update([
                //         // 'id_daerah' => $create_by,
                //         'kota' => $kota,
                //         'tj_daerah' => $tj
                //         ]);
                // }
                    // dd($idd);
        }

        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function setterlambat(Request $request){
        if($request->ajax()){
            // dd($request[0]['kota']);
            $terlambat = Terlambat::all();
            $id = [];
            $awal = [];
            $akhir = [];
            $potongan = [];
            foreach($request->id as $key => $val){
                $id[] = $val;
            }
            foreach($request->awal as $key => $val){
                $awal[] = $val;
            }
            foreach($request->akhir as $key => $val){
                $akhir[] = $val;
            }
            foreach($request->potongan as $key => $val){
                $potongan[] = $val;
            }
            $tot_arr = count($request->awal);
            // dd($id[0]);
            for($i = 0; $i < $tot_arr; $i++){
                if($id[$i] == 0){
                    $data = new Terlambat;
                    $data->awal = $awal[$i];
                    $data->akhir = $akhir[$i];
                    $data->potongan = $potongan[$i] != '' ? preg_replace("/[^0-9]/", "", $potongan[$i]) : 0;
                    $data->id_com = Auth::user()->level == 0 ? Auth::user()->id_com : $request->com;
                    $data->save();
                }
                foreach($terlambat as $val){
                    if($val->id_terlambat == $id[$i]){
                        Terlambat::where('id_terlambat', $id[$i])->update([
                            'awal' => $awal[$i],
                            'akhir' => $akhir[$i],
                            'potongan' => $potongan[$i] != '' ? preg_replace("/[^0-9]/", "", $potongan[$i]) : 0,
                        ]);
                    }
                }
                
                
            }
            
            $tunjangan = Tunjangan::where('id_com',$request->com)->get();
            if(count($tunjangan) == 0){
                $data = new Tunjangan;
                $data->pul = $request->pul != '' ? preg_replace("/[^0-9]/", "", $request->pul) : 0 ;
                $data->lappul =$request->lappul != '' ? preg_replace("/[^0-9]/", "", $request->lappul) : 0;
                $data->id_com = Auth::user()->level == 0 ? Auth::user()->id_com : $request->com;
                $data->save();      
                
            }else{
            $input = [
                'pul' => $request->pul != '' ? preg_replace("/[^0-9]/", "", $request->pul) : 0,
                'lappul' => $request->lappul != '' ? preg_replace("/[^0-9]/", "", $request->lappul) : 0
            ];
            $p = Tunjangan::where('id_com',  Auth::user()->level == 0 ? Auth::user()->id_com : $request->com )->update($input);
            
            }
            
            
            \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Setting Keterlambatan');
            
        return response()->json(['success' => 'Data is successfully updated']);
        }
    }
    
    public function set_terlambat(Request $req){
        
        if($req->modal == 'tidaklaporanataupresensipulang'){
            $input = [
                'pul' => $req->pul != '' ? $req->pul : 0
            ];
        }else{
            $input = [
                'lappul' => $req->luppul != '' ? $req->luppul : 0
            ];
        }
        
        Tunjangan::where('id_com',  Auth::user()->level == 0 ? Auth::user()->id_com : $request->com )->update($input);
        
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Setting Keterlambatan');
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function getterlambat(Request $request){
        if($request->com != ''){
            $data = Terlambat::where('id_com',$request->com)->get();
        }else{
            $data = Terlambat::where('id_com',Auth::user()->id_com)->get();

        }
        return response()->json(['result' => $data]);
    }
    
    
    public function gethukuman(Request $request){
            if($request->com != ''){
                 $data = Tunjangan::where('id_com',$request->com)->get();
            }else{
                 $data = Tunjangan::where('id_com',Auth::user()->id_com)->get();
            }
        return response()->json(['result' => $data]);
    }
    
    public function hapusterlambat($id){
        $data = Terlambat::findOrFail($id);
        $data->delete();
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Setting Keterlambatan');
    }
    
    public function hapus($id){
        $data = Daerah::findOrFail($id);
        
        $data->delete();
         \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Setting Tunjangan Daerah');
    }
    
    
    
    public function getbpjs(Request $request){
            if($request->com != ''){
                $data = Bpjs::where('id_com',$request->com)->get();
            }else{
                $data = Bpjs::where('id_com',Auth::user()->id_com)->get();
            }
        return $data;
    }
    
    public function listJi(Request $req){
            
        if($req->ajax()){
            
            $unit = $req->unit != '' ? "id_kantor = '$req->unit'" : "id_kantor IS NOT NULL";
            $skema = $req->skema != '' ? "skema_gaji = '$req->skema'" : "skema_gaji IS NOT NULL";
            
            if(Auth::user()->id_com != null ){
                $id_com = Auth::user()->id_com;
                $data = User::whereRaw("id_com = '$id_com' AND aktif = 1 AND $unit AND $skema")->orderBy('name', 'asc');
                return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('tombol', function($data){
                    $skemanya = SkemaGaji::where('id', $data->skema_gaji);
                    $hai = count($skemanya->get()) > 0 ? $skemanya->first()->nama : kosong;
                    $bo = '<button type="button" class="btn btn-xxs btn-primary takanada" data-bs-target="#detailSkema" data-bs-toggle="modal" data-bs-dismiss="modal" data-nama="'.$data->name.'" data-text="'.$hai.'" id="'.$data->skema_gaji.'" data-id="'.$data->id.'" data-kar="'.$data->id_karyawan.'" data-skema="'.$data->skema_gaji.'">'.$hai.'</button>';
                    return $bo;
                })
                ->rawColumns(['tombol'])
                ->make(true);
            }
        }
    }
    
    public function getSkemaIdkar(Request $req){
        $data = User::where('id_karyawan', $req->id)->first();
            
        return $data;
    }
    
    public function ubahSkemaGaji(Request $req){
        $cari = User::where('id', $req->id)->first();
        
        User::where('id',$req->id)->update(['skema_gaji' => $req->value]);
        
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Skema Gaji '. $cari->name. ' dari Skema '. $cari->skema_gaji .' ke '. $req->value);
            
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
}