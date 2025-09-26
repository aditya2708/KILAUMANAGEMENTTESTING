<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\KaryawanNew;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KaryawanExport;
use App\Imports\KaryawanImport;
use App\Models\Golongan;
use App\Models\Kenaikan;
use App\Models\Akses;
use App\Models\Profile;
use App\Models\Company;
use App\Models\JamKerja;
use App\Models\RekapJabatan;
use App\Models\RekapKeluarga;
use App\Models\MutasiKaryawan;
use App\Models\Daerah;
use App\Models\User;
use App\Models\NomorSK;
use Auth;
use DataTables;
use App\Models\Kantor;
use Carbon\Carbon;
use DB;
use PDF;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage  as Storager;
use DateTime;

class KaryawanController extends Controller
{
    public function limitkar(Request $request){
        $company = Company::where('id_com', Auth::user()->id_com)->first();
        $limit = $company->limit_user;
        $karyawan = Karyawan::where('id_com', Auth::user()->id_com)->get();
        
        if($karyawan->count > $limit){
            return response()->json([ 'message','Limit karyawan anda hanya sudah habis, silahkan hubungi tim penyedia untuk upgrade']);
        }
        return;
        
    }
   public function index(Request $request)
   {
    
    $units = Kantor::where('id_com', Auth::user()->id_com)->get(); 
 
   
        $jabatbaru = Jabatan::where('id_com', Auth::user()->id_com)->get();
        
        if(Auth::user()->level == 'admin'){
        $karyawan = karyawan::join('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')->where('karyawan.aktif', 1)
        ->select('jabatan.jabatan', 'karyawan.id_karyawans', 'karyawan.nama', 'karyawan.id_karyawan')->where('karyawan.id_com', Auth::user()->id_com)
        // ->where('karyawan.id_kantor', $request->unit)
        ->get();
        }else{
            
        $karyawan = karyawan::join('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')->where('karyawan.aktif', 1)
        ->select('jabatan.jabatan', 'karyawan.id_karyawans', 'karyawan.nama', 'karyawan.id_karyawan')
        ->where('karyawan.id_kantor', Auth::user()->id_kantor)
        ->get();
        }
        $daerah = Daerah::where('id_com', Auth::user()->id_com)->get();
        
        $company = Profile::where(function($query) {
                        if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                            $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })->get();
                    
        
        if($request->ajax()){
            
            if ($request->tglAktif != '') {
                $tglAktif = explode(' s/d ', $request->tglAktif);
                // return($tgl);
                $dariAktif = date('Y-m-d', strtotime($tglAktif[0]));
                $sampaiAktif = date('Y-m-d', strtotime($tglAktif[1]));
                $tglAktif = "AND DATE(karyawan.tgl_aktif) >= '$dariAktif' AND DATE(karyawan.tgl_aktif) <= '$sampaiAktif'";
            }else{
                // $tglAktif = "karyawan.tgl_aktif IS NOT NULL OR karyawan.tgl_aktif IS NULL";
                $tglAktif = null;
            }
            
            if ($request->tglNonAktif != '') {
                $tglNonAktif = explode(' s/d ', $request->tglNonAktif);
                // return($tgl);
                $dariNonAktif = date('Y-m-d', strtotime($tglNonAktif[0]));
                $sampaiNonAktif = date('Y-m-d', strtotime($tglNonAktif[1]));
                $tglNonAktif = "AND DATE(karyawan.tgl_nonaktif) >= '$dariNonAktif' AND DATE(karyawan.tgl_nonaktif) <= '$sampaiNonAktif'";
            }else{
                // $tglNonAktif = "karyawan.tgl_nonaktif IS NOT NULL OR karyawan.tgl_nonaktif IS NULL";
                $tglNonAktif = null;
            }
            
            // return $request->status;
            
            $id_com = $request->com;
            $unit = $request->unit != '' ? "id_kantor = '$request->unit'" : "id_kantor IS NOT NULL";
            $jabat = $request->jabata != '' ? "jabatan = '$request->jabata'" : "jabatan IS NOT NULL";
            $status = "aktif = '$request->status'";
            $jenis = $request->jenis_t != '' ? "status_kerja = '$request->jenis_t'" : "status_kerja != '-' ";
            
            $kntr = Auth::user()->id_kantor;
            $k = Kantor::where('kantor_induk', $kntr)->first();
            
            // $tglNonAktif AND $tglAktif AND
            
            if(Auth::user()->id_com != null ){
                $karyawan = Karyawan::whereRaw("$jabat AND $status AND $jenis $tglAktif $tglNonAktif ")
                            ->where(function($query) use ($k, $kntr, $request,$unit){
                                if(Auth::user()->kepegawaian == 'kacab'){
                                    if($k == null){
                                        $query->where('id_kantor', Auth::user()->id_kantor);
                                    }else{
                                        if($request->unit == ''){
                                            $query->whereRaw("id_kantor = '$k->id' OR id_kantor = '$kntr'");
                                        }else{
                                             $query->whereRaw('id_kantor', $request->unit);
                                        }
                                    }
                                }
                                else{
                                    if($request->unit == ''){
                                        $query->whereRaw("id_kantor IS NOT NULL");
                                    }else{
                                        $query->whereRaw("$unit");
                                    }
                                }
                            })
                            // ->where('id_com', Auth::user()->id_com)
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
            }
             
            return DataTables::of($karyawan)
            ->addIndexColumn()
            ->addColumn('jabat', function($karyawan){
                $jabats = Jabatan::select('jabatan')->where('id',$karyawan->jabatan)->first();
                $jabat = $jabats->jabatan ?? null;
                return $jabat;
            })
            ->addColumn('anak', function($karyawan){
                $anak = unserialize($karyawan->nm_anak);
                $bo = '';
                if ($anak == true){
                  $bo = $anak;
                }
                return $bo;
            })
            ->addColumn('umur_anak', function($karyawan){
                $now = Carbon::now();
                $anak = unserialize($karyawan->tgl_lahir_anak);
                $bo = '';
                if ($anak == true){
                    // $j = explode(",", $anak);
                    // $bo = $anak[0];
                    $age = [];
                    for($i =0; $i < count($anak);$i++){
                        $b_day = Carbon::parse($anak[$i]); 
                        $age[] = $b_day->diffInYears($now).' Tahun';
                    }
                    $bo = implode(",",$age);
                
                }
                return $bo;
            })
            ->addColumn('pasangan', function($karyawan){
                // $now = Carbon::now();
                $anak = unserialize($karyawan->nm_pasangan);
                $bo = '';
                if ($anak == true){
                    $bo = $anak;
                }
                return $bo;
            })
            ->addColumn('tgl_pasangan', function($karyawan){
                // $now = Carbon::now();
                $anak = unserialize($karyawan->tgl_lahir);
                $bo = '';
                if ($anak == true){
                    $bo = $anak;
                }
                return $bo;
            })
            ->addColumn('tglnikah_pasangan', function($karyawan){
                $now = Carbon::now();
                $anak = unserialize($karyawan->tgl_nikah);
                $bo = '';
                if ($anak == true){
                    $bo = $anak;
                }
                return $bo;
            })
            ->addColumn('status_anak', function($karyawan){
                // $now = Carbon::now();
                $anak = unserialize($karyawan->status_anak);
                $bo = '';
                if ($anak == true){
                    
                    $bo = $anak;
                
                }
                return $bo;
            })
            ->addColumn('stts_kerja', function($karyawan){
                $bo = $karyawan->status_kerja != NULL ? $karyawan->status_kerja : '-';
                return $bo;
            })
            ->addColumn('details', function($karyawan){
                $button = '<a class="btn btn-info btn-sm" href="'. url('karyawan/detail/'.$karyawan->id_karyawans) .'"><i class="fa fa-eye"></i></a>';
                return $button;
            })
            ->addColumn('edit', function($karyawan){
                $button = '<div class="btn-group"><a class="btn btn-success btn-sm" href="'. url('karyawan/edit/'.$karyawan->id_karyawans) .'"><i class="fa fa-edit"></i></a></div>';
                return $button;
            })
            ->addColumn('aktif', function($karyawan){
                if ($karyawan->aktif == 1){
                    $button = '<a class="btn btn-warning btn-sm aktifken" href="javascript:void(0)" id="'.$karyawan->id_karyawans.'" status="'.$karyawan->aktif.'">Non-Aktifkan</a>';
                }else{
                    $button = '<a class="btn btn-info btn-sm aktifken"  href="javascript:void(0)" id="'.$karyawan->id_karyawans.'" status="'.$karyawan->aktif.'">Aktifkan</a>';
                }
                return $button;
            })
            ->editColumn('wow', function($karyawan){
                if ($karyawan->aktif == 1){
                            $c = 'checked';
                        }else{
                            $c = '';
                        } 
                                // $button = '<input type="checkbox" id="toggle-two"  class="toggle-class " data-id="'. $kerja->id .'" data-toggle="toggle" data-style="slow" data-on="Onsite" data-off="Off"  >';
                        $button = '<label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'))" id="checkbox" class="toggle-class" status="'.$karyawan->aktif.'" data-id="'. $karyawan->id_karyawans . '"  data-value="'. $karyawan->aktif . '" type="checkbox" '.( $karyawan->aktif  == 1 ? "checked" : "").' /> <div class="slider round"> </div> </label>';
                        return $button;
            })
            ->addColumn('hapus', function($karyawan){
                $button = '<button type="submit" class="btn btn-sm btn-danger delete" id="'.$karyawan->id_karyawans.'"><i class="fa fa-trash"></i></button>';
                return $button;
            })
            ->rawColumns(['jabat','details','edit','aktif','hapus','wow','stts_kerja'])
            ->make(true);
            // ->toJson();
        }
        
       return view('karyawan.index', compact('karyawan','jabatbaru','company','units','daerah'));
   }
    
    public function karyawanExport(Request $request){
        
        $joss = $request->com != '' ? DB::table('company')->selectRaw('name')->where('id_com', $request->com)->first()->name :DB::table('company')->selectRaw('name')->where('id_com', 1)->first()->name;
                
            // if($request->tombol == 'xls'){
            //     $r = Excel::download(new KaryawanExport($request), 'karyawan.xls');
            //     ob_end_clean();
            //     return $r;
            // }else{
            //     $r = Excel::download(new KaryawanExport($request), 'karyawan.csv');
            //     ob_end_clean();
            //     return $r;
            // }
            
              if($request->tombol == 'xls'){
                $r = Excel::download(new KaryawanExport($request), 'karyawan '.$joss.'.xls');
                ob_end_clean();
                return $r;
            }else{
                $r = Excel::download(new KaryawanExport($request), 'karyawan ' .$joss.'.xls');
                ob_end_clean();
                return $r;
            }
            
    }
    
   public function create()
   {
       $gol = Golongan::all();
       $daerah = Daerah::all();
       $pass = Karyawan::where('aktif', 1)->where('id_com',Auth::user()->id_com)->get();
       $company = Profile::where(function($query) {
                        if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                            $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })
                    ->get();
       return view ('karyawan.create',compact('gol', 'daerah' , 'pass','company'));
   }
   
   public function createx()
   {
       $gol = Golongan::all();
       $daerah = Daerah::all();
       $pass = Karyawan::where('aktif', 1)->where('id_com',Auth::user()->id_com)->get();
       return view ('another-company.create',compact('gol', 'daerah' , 'pass'));
   }
   
    public function cekcompany(Request $request){
        $data = Profile::select('gaji')->where('id_com', $request->com)->first();
        return $data;
    }


   public function talent(){
       $karyawan = Karyawan::all(); 
    return view ('karyawan.create',compact('karyawan'));
}

    public function terimakasih(){
        return view ('another-company.terimakasih');
    }

public function store(Request $request){
    if($request->id_com == ''){
        $com = Auth::user()->id_com;
        $cari = Profile::select('gaji')->where('id_com', $com)->first();
    }else{
        $com = $request->id_com;
        $cari = Profile::select('gaji')->where('id_com', $com)->first();
    }
    
     function saveBase64ImageWithMove($base64Image, $imageName = null, $destinationFolder = "upload/")
        {
            if (empty($base64Image)) {
                return null;
            }
        
            // Buat folder jika belum ada
            if (!file_exists(public_path($destinationFolder))) {
                mkdir(public_path($destinationFolder), 0777, true);
            }
        
            // Pisahkan base64 data
            $image_parts = explode(";base64,", $base64Image);
            if (count($image_parts) < 2) {
                return null; // Data tidak valid
            }
        
            $image_type_aux = explode("image/", $image_parts[0]);
            if (count($image_type_aux) < 2) {
                return null; // Format tidak sesuai
            }
        
            $image_type = $image_type_aux[1]; // Jenis gambar (jpeg, png, dll)
            $image_base64 = base64_decode($image_parts[1]);
        
            // Gunakan nama default jika tidak diberikan
            $imageName = $imageName ?? time() . '.' . $image_type;
            $tempPath = storage_path('app/temp/') . $imageName;
        
            // Buat folder sementara jika belum ada
            if (!file_exists(storage_path('app/temp/'))) {
                mkdir(storage_path('app/temp/'), 0777, true);
            }
        
            // Simpan sementara di storage/temp
            file_put_contents($tempPath, $image_base64);
        
            // Pindahkan file ke lokasi tujuan menggunakan move()
            $destinationPath = public_path($destinationFolder . $imageName);
            rename($tempPath, $destinationPath);
        
            return $imageName;
        }
    
    if($cari->gaji == 1){
        $user = \DB::select("SELECT * FROM karyawan WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
        $gol = Golongan::where('id_gol', $request->id_gol);
        $urut = count($user) + 1;
        $nourut = $urut < 10 ? '0'.$urut : $urut;
            
        $input = $request->all();
        // return($input);
        $ttl = date('ymd',(strtotime($request->ttl)));
        $tgk = date('ym',(strtotime($request->tgl_kerja)));
        $jk = $request->jk == 'Pria' ? '1' : '2';
        $id_kar = $ttl.$tgk.$jk.$nourut;
        // dd($id_kar);
        $input['id_karyawan'] = $id_kar;
        // dd($request->all());
        
        unset($input['halah']);
        unset($input['namafile_foto']);
        unset($input['namafile_scan_kk']);
        unset($input['mentor']);
        unset($input['magang']);
        unset($input['pj_agen']);
      if (!empty($request->foto)) {
            $imageName = saveBase64ImageWithMove($request->foto, $request->namafile_foto);
            if ($imageName) {
                $input['gambar_identitas'] = $imageName;
            }
        }        
      if (!empty($request->scan_kk)) {
            $imageName = saveBase64ImageWithMove($request->scan_kk, $request->namafile_scan_kk);
            if ($imageName) {
                $input['scan_kk'] = $imageName;
            }
        }        
      if (!empty($request->ijazah)) {
            $imageName = saveBase64ImageWithMove($request->ijazah, $request->halah ?? null);
            if ($imageName) {
                $input['ijazah'] = $imageName;
            }
        }     
        
        // if(!empty($request->scan_kk)){
        //     $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
        //     $image_parts = explode(";base64,", $request->scan_kk);
        //     $image_type_aux = explode("image/", $image_parts[0]);
        //     $image_type = $image_type_aux[1];
        //     $image_base64 = base64_decode($image_parts[1]);
        //     $image_name = $request->namafile_scan_kk;
        //     $file = $folderPath . $image_name;
        //     file_put_contents($file, $image_base64);
            
        //     $input['scan_kk'] = $image_name;
        //     $ii = $image_name;
        //     // dd('stop');
        // }
         
        $nm_pasangan = [];
        $tgl_lahir = [];
        $tgl_nikah = [];
        $id_pasangan = [];
        
        $nm_anak = [];
        $tgl_lahir_anak = [];
        $status_anak = [];
        if(!empty($request->arr_anak)){
            foreach($request->arr_anak as $val){
                $nm_anak[] = $val['nm_anak'];
                $tgl_lahir_anak[] = $val['tgl_lahir_anak'];
                $status_anak[] =  $val['status'];
                
            }
        }
        
        if(!empty($request->arr_pas)){
            foreach($request->arr_pas as $val){
                $nm_pasangan[] = $val['nm_pasangan'];
                $tgl_lahir[] = $val['tgl_lahir'];
                $tgl_nikah[] =  $val['tgl_nikah'];
                $id_pasangan[] = $val['id_pasangan'];
            }
        }
    
        // dd($request->nm_anak);
        $input['nm_pasangan'] = !empty($request->arr_pas) ? serialize($nm_pasangan) : null;
        $input['tgl_lahir'] = !empty($request->arr_pas) ? serialize($tgl_lahir) : null;
        $input['tgl_nikah'] = !empty($request->arr_pas) ? serialize($tgl_nikah) : null;
        $input['id_pasangan'] = !empty($request->arr_pas) ? serialize($id_pasangan) : null;
        
        $input['nm_anak'] = !empty($request->arr_anak) ? serialize($nm_anak) : null;
        $input['tgl_lahir_anak'] = !empty($request->arr_anak) ? serialize($tgl_lahir_anak) : null;
        $input['status_anak'] = !empty($request->arr_anak) ? serialize($status_anak) : null;
        
        $input['golongan'] = $gol->count() > 0 ? $gol->first()->golongan : null;
        $input['tgl_mk'] = $request->tgl_kerja;
        $input['id_com'] = $com;
        $input['id_mentor'] = $request->mentor == '' ? NULL : $request->mentor;
        $input['id_pj_agen'] = $request->pj_agen == '' ? NULL : $request->pj_agen;
        $input['tgl_gol'] = $request->tgl_kerja;
        $input['tgl_mutasi'] = $request->tgl_kerja;
        $input['tgl_gaji'] = $request->tgl_kerja;
        $input['tgl_aktif'] = $request->tgl_kerja;
        $input['tj_pas'] = $request->tj_pas == "true" ? "1" : "0";
        $input['jab_daerah'] = $request->jab_daerah == "true" ? "1" : "0";
        $input['plt'] = $request->plt == "true" ? "1" : "0";
        $input['warning_pasangan'] = $request->warning_pasangan == "true" ? "1" : "0";
        $input['user_insert'] = Auth::user()->id;
        // return($input);
        // $input;
        Karyawan::create($input);
        
        
        if(!empty($id_pasangan)){
            Karyawan::whereIn('id_karyawan', $id_pasangan)->update([
                "id_pasangan" => serialize(array($id_kar)),
                "status_nikah" => "Menikah",
                "no_kk" => $request->no_kk,
                "scan_kk" => $request->namafile_scan_kk,
                "nm_pasangan" => serialize(array($request->nama)),
                "tgl_lahir" => serialize(array($request->ttl)),
                "tgl_nikah" => serialize($tgl_nikah),
                "nm_anak" => !empty($request->arr_anak) ? serialize($nm_anak) : null,
                "tgl_lahir_anak" => !empty($request->arr_anak) ? serialize($tgl_lahir_anak) : null,
                "status_anak" => !empty($request->arr_anak) ? serialize($status_anak) : null,
            ]);
        }
        
        if($request->status_kerja != 'Magang' && $request->status_kerja != 'Agen'){
            $data = new Kenaikan;
            $data->id_karyawan = $id_kar;
            $data->nama = $request->nama;
            $data->masa_kerja = $request->masa_kerja;
            $data->golongan = $gol->first()->golongan;
            $data->tgl_mk = $request->tgl_kerja;
            $data->tgl_gol = $request->tgl_kerja;
            $data->status_kerja = $request->status_kerja;
            $data->user_insert = Auth::user()->id;
            
            $data->save();
            
        }
        
        $dat = new RekapJabatan;
        $dat->id_karyawan = $id_kar;
        $dat->nama = $request->nama;
        $dat->id_jabatan = $request->jabatan;
        $dat->tgl_jab = $request->tgl_kerja;
        $dat->user_insert = Auth::user()->id;
        
        $dat->save();
        
        $datas = new RekapKeluarga;
        $datas->id_karyawan = $id_kar;
        $datas->nama = $request->nama;
        $datas->status_nikah = $request->status_nikah;
        $datas->no_kk = $request->no_kk;
        $datas->user_insert = Auth::user()->id;
        
        if($request->status_nikah == 'Menikah' || $request->status_nikah == 'Bercerai'){
            $datas->scan_kk = $imageName;
        }
        
        $datas->id_pasangan = !empty($request->arr_pas) ? serialize($id_pasangan) : null;
        $datas->nm_pasangan = !empty($request->arr_pas) ? serialize($nm_pasangan) : null;
        $datas->tgl_lahir = !empty($request->arr_pas) ? serialize($tgl_lahir) : null;
        $datas->tgl_nikah = !empty($request->arr_pas) ? serialize($tgl_nikah) : null;
        $datas->nm_anak = !empty($request->arr_anak) ? serialize($nm_anak) : null;
        $datas->tgl_lahir_anak = !empty($request->arr_anak) ? serialize($tgl_lahir_anak) : null;
        $datas->status_anak = !empty($request->arr_anak) ? serialize($status_anak) : null;
        
        $datas->save();   
    }else{
        $user = \DB::select("SELECT * FROM karyawan WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
        $urut = count($user) + 1;
        $nourut = $urut < 10 ? '0'.$urut : $urut;
            
        $input = $request->all();
        unset($input['namafile_foto']);
        unset($input['namafile_scan_kk']);
        unset($input['mentor']);
        unset($input['magang']);
        unset($input['pj_agen']); 
        unset($input['halah']);
        $ttl = date('ymd',(strtotime($request->ttl)));
        $jk = $request->jk == 'Pria' ? '1' : '2';
        $id_kar = $ttl.$jk.$nourut;
        $input['id_karyawan'] = $id_kar;
        if(!empty($request->foto)){
            $imageName = saveBase64ImageWithMove($request->foto, $request->namafile_foto);
            if ($imageName) {
                $input['gambar_identitas'] = $imageName;
            }
        }
        if(!empty($request->ijazah)){
            $imageName = saveBase64ImageWithMove($request->ijazah, $request->namafile_scan_iz);
            if ($imageName) {
                $input['ijazah'] = $imageName;
            }
        }
         if (!empty($request->scan_kk)) {
            $imageName = saveBase64ImageWithMove($request->scan_kk, $request->namafile_scan_kk);
            if ($imageName) {
                $input['scan_kk'] = $imageName;
            }
        } 
        
        // if(!empty($request->scan_iz)){
        //     $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
        //     $image_parts = explode(";base64,", $request->scan_iz);
        //     $image_type_aux = explode("image/", $image_parts[0]);
        //     $image_type = $image_type_aux[1];
        //     $image_base64 = base64_decode($image_parts[1]);
        //     $image_name = $request->namafile_scan_iz;
        //     $file = $folderPath . $image_name;
        //     file_put_contents($file, $image_base64);
            
        //     $input['ijazah'] = $image_name;
        //     // dd('stop');
        // }
       
        $input['id_com'] = $com;
        $input['user_insert'] = null;
        
        Karyawan::create($input);
        
        if(!empty($id_pasangan)){
            Karyawan::whereIn('id_karyawan', $id_pasangan)->update([
                "id_pasangan" => serialize(array($id_kar)),
                "status_nikah" => "Menikah",
                "no_kk" => $request->no_kk,
                "scan_kk" => $request->namafile_scan_kk,
                "nm_pasangan" => serialize(array($request->nama)),
                "tgl_lahir" => serialize(array($request->ttl)),
                "tgl_nikah" => serialize($tgl_nikah),
                "nm_anak" => !empty($request->arr_anak) ? serialize($nm_anak) : null,
                "tgl_lahir_anak" => !empty($request->arr_anak) ? serialize($tgl_lahir_anak) : null,
                "status_anak" => !empty($request->arr_anak) ? serialize($status_anak) : null,
            ]);
        }
        
        if($request->status_kerja != 'Magang' && $request->status_kerja != 'Agen'){
            $data = new Kenaikan;
            $data->id_karyawan = $id_kar;
            $data->nama = $request->nama;
            $data->masa_kerja = $request->masa_kerja;
            $data->golongan = null;
            $data->tgl_mk = $request->tgl_kerja;
            $data->tgl_gol = $request->tgl_kerja;
            $data->status_kerja = $request->status_kerja;
            $data->user_insert = Auth::user()->id;
            
            $data->save();
            
        }
        
        $dat = new RekapJabatan;
        $dat->id_karyawan =  $id_kar;
        $dat->nama = $request->nama;
        $dat->id_jabatan = $request->jabatan;
        $dat->tgl_jab = $request->tgl_kerja;
        $dat->user_insert = Auth::user()->id;
        
        $dat->save();
        
        $datas = new RekapKeluarga;
        $datas->id_karyawan =  $id_kar;
        $datas->nama = $request->nama;
        $datas->status_nikah = $request->status_nikah;
        $datas->no_kk = $request->no_kk;
        $datas->user_insert = Auth::user()->id;
        
        if($request->status_nikah == 'Menikah' || $request->status_nikah == 'Bercerai'){
            $datas->scan_kk = $imageName;
        }
        
        $datas->id_pasangan = !empty($request->arr_pas) ? serialize($id_pasangan) : null;
        $datas->nm_pasangan = !empty($request->arr_pas) ? serialize($nm_pasangan) : null;
        $datas->tgl_lahir = !empty($request->arr_pas) ? serialize($tgl_lahir) : null;
        $datas->tgl_nikah = !empty($request->arr_pas) ? serialize($tgl_nikah) : null;
        $datas->nm_anak = !empty($request->arr_anak) ? serialize($nm_anak) : null;
        $datas->tgl_lahir_anak = !empty($request->arr_anak) ? serialize($tgl_lahir_anak) : null;
        $datas->status_anak = !empty($request->arr_anak) ? serialize($status_anak) : null;
        
        $datas->save();   
    }
    
    \LogActivity::addToLog(Auth::user()->name.' Menambahkan Data Karyawan '.$request->nama);
    
    return response()->json(["data" => "successs"]);
    
    // return redirect('karyawan')->with('munculkon', $id_kar);
}

public function postx(Request $request){
    $user = \DB::select("SELECT * FROM karyawan WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
    $urut = count($user) + 1;
    $nourut = $urut < 10 ? '0'.$urut : $urut;
        
    $input = $request->all();
    // return($input);
    $ttl = date('ymd',(strtotime($request->ttl)));
    $jk = $request->jk == 'Pria' ? '1' : '2';
    $id_kar = $ttl.$jk.$nourut;
    // dd($id_kar);
    $input['id_karyawan'] = $request->id_kar;
    if(!empty($request->foto)){
        $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
        $image_parts = explode(";base64,", $request->foto);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $image_name = $request->namafile_foto;
        $file = $folderPath . $image_name;
        file_put_contents($file, $image_base64);
        
        $input['gambar_identitas'] = $image_name;
        // dd('stop');
    }
    
    if(!empty($request->scan_iz)){
        $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
        $image_parts = explode(";base64,", $request->scan_iz);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $image_name = $request->namafile_scan_iz;
        $file = $folderPath . $image_name;
        file_put_contents($file, $image_base64);
        
        $input['ijazah'] = $image_name;
        // dd('stop');
    }
    
    // if(!empty($request->scan_kk)){
    //     $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
    //     $image_parts = explode(";base64,", $request->scan_kk);
    //     $image_type_aux = explode("image/", $image_parts[0]);
    //     $image_type = $image_type_aux[1];
    //     $image_base64 = base64_decode($image_parts[1]);
    //     $image_name = $request->namafile_scan_kk;
    //     $file = $folderPath . $image_name;
    //     file_put_contents($file, $image_base64);
        
    //     $input['scan_kk'] = $image_name;
    //     $ii = $image_name;
    //     // dd('stop');
    // }
   
    $input['id_com'] = null;
    $input['user_insert'] = null;
    
    KaryawanNew::create($input);
    
    return response()->json(["data" => "successs"]);
}

public function show($id){
    $karyawan = Karyawan::select('*')->where('id_karyawans', $id)->first();
    $jab = Jabatan::where('id', $karyawan->jabatan)->first();
    $daera = Daerah::where('id_daerah', $karyawan->id_daerah)->first();
    if($daera == null){
        $daerah = '';
    }else{
        $daerah = $daera->kota;
    }
    $unit = Kantor::where('id_com', Auth::user()->id_com)->get(); 
    $jabat = Jabatan::where('id_com', Auth::user()->id_com)->get();
    
    return view ('karyawan.detail',compact('karyawan', 'jab', 'daerah','unit','jabat'));
}

public function detailkaryawan($id){
     $karyawan = Karyawan::where('id_karyawans', $id)->first();
     return $karyawan;
}

public function destroy($id)
{
    $trayek = Karyawan::findOrFail($id);
    
    $trayek->delete();
    return redirect('karyawan');
}

public function destroyy($id)
{
    $kar = Karyawan::findOrFail($id);
    \LogActivity::addToLog(Auth::user()->name.' Menghapus Data Karyawan '.$kar->nama);
    $kar->delete();
    // return back();
    return response()->json(['success' => 'Data is successfully updated']);
}

public function changesttsaktif(Request $request){
    $data = Karyawan::where('id_karyawans',$request->id)->first();

    $aktif = $data->aktif;

    if($aktif == 1){
        Karyawan::where('id_karyawans',$request->id)->update([
            'aktif'=> 0,
            'tgl_nonaktif'=> date('Y-m-d'),
            'user_update' => Auth::user()->id
            
        ]);
        \LogActivity::addToLog(Auth::user()->name.' Menonaktifkan Status Data Karyawan '.$data->nama);
    }else{
        $kar = Karyawan::findOrFail($request->id);
        $kar->aktif = 1;
        $kar->tgl_aktif = date('Y-m-d');
        $kar->user_update = Auth::user()->id;
        
        User::where('id_karyawan',$kar->id_karyawan)->update([
            'kon_gaji' => 1
        ]);
        
        $kar->update();
        
        \LogActivity::addToLog(Auth::user()->name.' Mengaktifkan Status Data Karyawan '.$data->nama);
    }
  
    return response()->json(['success' => 'Data is successfully updated']);
}

// public function aktifken($id){
//     $data = Karyawan::where('id_karyawans',$id)->first();

//     $aktif = $data->aktif;

//     if($aktif == 1){
//         Karyawan::where('id_karyawans',$id)->update([
//             'aktif'=> 0,
//             'user_update' => Auth::user()->id
            
//         ]);
//         \LogActivity::addToLog(Auth::user()->name.' Menonaktifkan Status Data Karyawan '.$data->nama);
//     }else{
//         Karyawan::where('id_karyawans',$id)->update([
//             'aktif'=> 1,
//             'user_update' => Auth::user()->id
//         ]);
//         \LogActivity::addToLog(Auth::user()->name.' Mengaktifkan Status Data Karyawan '.$data->nama);
//     }
  
//     return response()->json(['success' => 'Data is successfully updated']);
// }

public function update($id, Request $request)
{
    // $data = Karyawan::findOrFail($id);
    $input = [
        "nama" => $request->nama,
        "nik" => $request->nik,
        "ttl" => $request->ttl,
        "jk" => $request->jk,
        "email" => $request->email,
        // "status_nikah" => $request->status_nikah,
        "nomerhp" => $request->nomerhp,
        "hobi" => $request->hobi,
        "alamat" => $request->alamat,
        "pendidikan" => $request->pendidikan,
        "nm_sekolah" => $request->nm_sekolah,
        "jurusan" => $request->jurusan,
        "th_lulus" => $request->th_lulus,
        "gelar" => $request->gelar,
        "no_rek" => $request->norek,
        "user_update" => Auth::user()->id
        
    ];
    
    if(!empty($request->foto)){
        $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
        $image_parts = explode(";base64,", $request->foto);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $image_name = $request->namafile_foto;
        $file = $folderPath . $image_name;
        file_put_contents($file, $image_base64);
        
        $input['gambar_identitas'] = $image_name;
        // dd('stop');
    }
    
    if(!empty($request->scan_iz)){
        $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
        $image_parts = explode(";base64,", $request->scan_iz);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $image_name = $request->namafile_scan_iz;
        $file = $folderPath . $image_name;
        file_put_contents($file, $image_base64);
        
        $input['ijazah'] = $image_name;
        // dd('stop');
    }
    // dd($input);
    // return($request);
    Karyawan::where('id_karyawan', $id)->update($input);
    // 
    
    \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Karyawan '.$request->nama);
    return response()->json(["data" => "success"]);
}

public function updatepen($id, Request $request)
{
    $input = [
        "pendidikan" => $request->pendidikan,
        "nm_sekolah" => $request->nm_sekolah,
        "jurusan" => $request->jurusan,
        "th_lulus" => $request->th_lulus,
        "gelar" => $request->gelar,
        "user_update" => Auth::user()->id
        
    ];
    
    
    if(!empty($request->scan_iz)){
        $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
        $image_parts = explode(";base64,", $request->scan_iz);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $image_name = $request->namafile_scan_iz;
        $file = $folderPath . $image_name;
        file_put_contents($file, $image_base64);
        
        $input['ijazah'] = $image_name;
        // dd('stop');
    }
    // dd($input);
    // return($request);
    Karyawan::where('id_karyawan', $id)->update($input);
    // 
    
    \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Karyawan '.$request->nama);
    return response()->json(["data" => "success"]);
}


public function edit($id)
{
    $karyawan = Karyawan::findOrFail($id);
    $gol = Golongan::all();
    $daerah = Daerah::all();
    // dd($karyawan);
    return view('karyawan.edit',compact('karyawan','gol','daerah'));
}


public function exports()
{
    if(Auth::user()->id_com == 3){
        $r = Excel::download(new KaryawanExport(), 'kar_regsosek3212.xls');
    }else{
        $r = Excel::download(new KaryawanExport(), 'karyawan.xls');
    }
    ob_end_clean();
    return $r;
}

public function import(Request $request)
{
    $this->validate($request, [
        'file' => 'required|mimes:csv,xls,xlsx'
    ]);

    $file = $request->file('file');

    // membuat nama file unik
    $nama_file = $file->hashName();

    //temporary file
    $path = $file->storeAs('public/excel/',$nama_file);

    // import data
    $import = Excel::import(new KaryawanImport, storage_path('app/public/excel/'.$nama_file));

    //remove from server
    Storage::delete($path);

    if($import) {
        //redirect
        return redirect('karyawan');
    } else {
        //redirect
        return redirect('karyawan');
    }
}

public function offkar($id){
    $data = Karyawan::where('id_karyawans',$id)->first();

    $aktif = $data->aktif;

    if($aktif == 1){
        Karyawan::where('id_karyawans',$id)->update([
            'aktif'=> 0
            
        ]);
    }else{
        Karyawan::where('id_karyawans',$id)->update([
            'aktif'=> 1
        ]);
    }
  
    return back();
}

public function karyawan(Request $request, Karyawan $user){
            if(!Auth::guard('karyawan')->attempt(['email' => $request->email, 'password' => $request->password])){
                return response()->json(['error' => 'salah bos',401]);
            }else{
                $userk = $user->find(Auth::guard('karyawan')->user()->id_karyawans);
            return response()->json([
                'berhasil' => $userk,
                'token' => $userk->id_karyawan,
                ]);
            }
            
            
            }

public function getgol($id){
    $data['karyawan'] = Karyawan::where('id_karyawan', $id)->first();
    $datax = Karyawan::where('id_karyawan', $id)->first();
    $data['gol'] = Golongan::all();
    $data['jabatan'] = Jabatan::all();
    $data['anak'] = unserialize($data['karyawan']->nm_anak);
    $data['tgl_lahir_anak'] = unserialize($data['karyawan']->tgl_lahir_anak);
    $data['status_anak'] = unserialize($data['karyawan']->status_anak);
    
    $data['pasangan'] = unserialize($data['karyawan']->nm_pasangan);
    $data['tgl_lahir'] = unserialize($data['karyawan']->tgl_lahir);
    $data['tgl_nikah'] = unserialize($data['karyawan']->tgl_nikah);
    $data['id_pasangan'] = unserialize($data['karyawan']->id_pasangan);
    
    $jbt = Jabatan::where('id',$datax->jabatan)->first();
    // $kntr = Kantor::where('id',$datax->id_kantor)->first();
    $data['mentor'] = Karyawan::whereRaw("(jabatan = '$jbt->pr_jabatan' OR jabatan = '$datax->jabatan') AND id_karyawan != '$id' AND aktif='1' AND id_kantor = '$datax->id_kantor' ")->get();
    
    
    $data['data_pas'] = [];
    $data['data_anak'] = [];
    if(!empty($data['pasangan'])){
        for ($i = 0; $i < count($data['pasangan']); $i++){
            $data['data_pas'][] = [
                "id_pasangan" => !empty($data['id_pasangan'][$i]) ? $data['id_pasangan'][$i] : null,
                "nm_pasangan" => $data['pasangan'][$i],
                "tgl_lahir" => $data['tgl_lahir'][$i],
                "tgl_nikah" => $data['tgl_nikah'][$i]
            ];
        }
    }
   
    if(!empty($data['anak'])){
        for ($x = 0; $x < count($data['anak']); $x++){
            $data['data_anak'][] = [
                "nm_anak" => $data['anak'][$x],
                "tgl_lahir_anak" => $data['tgl_lahir_anak'][$x],
                "status" => $data['status_anak'][$x]
            ];
        }
    }
    
    
    
    return $data;
}

public function postgol(Request $request,$id){
    // $arr_pas = json_decode($request->arr_pas);
    // dd($arr_pas);
    $gol = Golongan::where('id_gol', $request->id_gol)->first();
    $kar = Karyawan::where('id_karyawan', $id)->first();
    $date = date('Y-m-d');


    // dd($con);
    $tgl_mk = $request->masa != $kar->masa_kerja ? $request->tgl_sk : $kar->tgl_mk;
    $tgl_gol = $request->id_gol != $kar->id_gol ? $request->tgl_sk : $kar->tgl_gol;
    
    if($request->hasFile('upload_sk')){
        $file_sk = $request->file('upload_sk')->getClientOriginalName();
    }else{
        $file_sk = $request->file_sk_name;
    }
    
     $nomor = NomorSK::where('id_com', Auth::user()->id_com)
            ->whereYear('created_at', date('Y')) // Sesuaikan dengan tahun yang diinginkan
            ->whereMonth('created_at', date('m')) // Sesuaikan dengan bulan yang diinginkan
            ->max('urut');
    // // dd($request->tgl_sk);
    if($request->action == 'pangkat'){
        if(Auth::user()->level != 'kacab'){
            NomorSK::where('id_com', Auth::user()->id_com)->create([
                'urut' => $nomor == null || $nomor == '' ? 1 : $nomor + 1,
                'id_com' => Auth::user()->id_com,
                'user_insert' => Auth::user()->id,
            ]);
        }
         if($request->kepegawaian== 'kacab'){
            $con = Kenaikan::where('id_karyawan', $id)->whereDate('created_at', $date)->first();

            if($con){
                    $data = Kenaikan::findOrFail($con->id_naik);
                    $data->nama = $kar->nama;
                    $data->masa_kerja = $request->masa_kerja;
                    $data->golongan = $gol->golongan;
                    $data->tgl_mk = $tgl_mk;
                    $data->tgl_gol = $tgl_gol;
                    $data->status_kerja = $request->status_kerja;
                    $data->user_insert = Auth::user()->id;
                    $data->user_approve = null;
                    $data->tgl_sk = $request->tgl_sk;
                    $data->keterangan = $request->ket_alasan_sk;
                    $data->id_mentor = $request->mntor;
                    $data->jkk = $request->status_kerja == 'Contract' ? $request->jkk: 0;
                    $data->jkm =  $request->status_kerja == 'Contract' ? $request->jkm:0;
                    $data->jht =  $request->status_kerja == 'Contract' ? $request->jht:0;
                    $data->jpn =  $request->status_kerja == 'Contract' ? $request->jpn:0;
                    $data->kesehatan =  $request->status_kerja == 'Contract' ?  $request->kesehatan:0;
                    $data->no_rek = $request->no_rek;
                    $data->acc = 2;
                    if($request->hasFile('upload_sk')){
                        $image = $request->file('upload_sk');
                
                        if($image->isValid()){
                            $image_name = $image->getClientOriginalName();
                            $upload_path = 'fileSK';
                            $image->move($upload_path, $image_name);
                            $data->file_sk = $image_name;
                        }
                    }else{
                        $data->file_sk = $file_sk;
                    }
                    
                    $data->update();
                    
                    
            }else{
                    $data = new Kenaikan;
                    $data->id_karyawan = $id;
                    $data->nama = $kar->nama;
                    $data->masa_kerja = $request->masa_kerja;
                    $data->golongan = $gol->golongan;
                    $data->tgl_mk = $tgl_mk;
                    $data->tgl_gol = $tgl_gol;
                    $data->status_kerja = $request->status_kerja;
                    $data->user_insert = Auth::user()->id;
                    $data->no_rek = $request->no_rek;
                    $data->acc = 2;
                    $data->tgl_sk = $request->tgl_sk;
                    $data->keterangan = $request->ket_alasan_sk;
                    $data->id_mentor = $request->mntor;
                    $data->jkk = $request->status_kerja == 'Contract' ? $request->jkk: 0;
                    $data->jkm =  $request->status_kerja == 'Contract' ? $request->jkm:0;
                    $data->jht =  $request->status_kerja == 'Contract' ? $request->jht:0;
                    $data->jpn =  $request->status_kerja == 'Contract' ? $request->jpn:0;
                    $data->kesehatan =  $request->status_kerja == 'Contract' ?  $request->kesehatan:0;
                    if($request->hasFile('upload_sk')){
                        $image = $request->file('upload_sk');
                
                        if($image->isValid()){
                            $image_name = $image->getClientOriginalName();
                            $upload_path = 'fileSK';
                            $image->move($upload_path, $image_name);
                            $data->file_sk = $image_name;
                        }
                    }else{
                        $data->file_sk = $file_sk;
                    }
                    
                    $data->save();
                }
                
                
         }else{
            $con = Kenaikan::where('id_karyawan', $id)->whereDate('created_at', $date)->first();
            Karyawan::where('id_karyawan', $id)->update([
              'masa_kerja' => $request->masa_kerja,
              'id_gol' => $request->id_gol,
              'golongan' => $gol->golongan,
              'no_rek' => $request->no_rek,
              'tgl_mk' => $tgl_mk,
              'tgl_gol' => $tgl_gol,
              'file_sk' => $file_sk,
              'user_update' => Auth::user()->id,
              'status_kerja' => $request->status_kerja,
              
            ]);
        
            if($con){
                $data = Kenaikan::findOrFail($con->id_naik);
                $data->nama = $kar->nama;
                $data->masa_kerja = $request->masa_kerja;
                $data->golongan = $gol->golongan;
                $data->tgl_mk = $tgl_mk;
                $data->tgl_gol = $tgl_gol;
                $data->status_kerja = $request->status_kerja;
                $data->acc = 1;
                $data->user_insert = Auth::user()->id;
                $data->user_approve = Auth::user()->id;
                $data->no_rek = $request->no_rek;
                $data->tgl_sk = $request->tgl_sk;
                $data->keterangan = $request->ket_alasan_sk;
                $data->id_mentor = $request->mntor;
                $data->jkk = $request->status_kerja == 'Contract' ? $request->jkk: 0;
                $data->jkm =  $request->status_kerja == 'Contract' ? $request->jkm:0;
                $data->jht =  $request->status_kerja == 'Contract' ? $request->jht:0;
                $data->jpn =  $request->status_kerja == 'Contract' ? $request->jpn:0;
                $data->kesehatan =  $request->status_kerja == 'Contract' ?  $request->kesehatan:0;
                if($request->hasFile('upload_sk')){
                    $image = $request->file('upload_sk');
            
                    if($image->isValid()){
                        $image_name = $image->getClientOriginalName();
                        $upload_path = 'fileSK';
                        $image->move($upload_path, $image_name);
                        $data->file_sk = $image_name;
                    }
                }else{
                    $data->file_sk = $file_sk;
                }
                
                $data->update();
                
                
            }else{
                $data = new Kenaikan;
                $data->id_karyawan = $id;
                $data->nama = $kar->nama;
                $data->masa_kerja = $request->masa_kerja;
                $data->golongan = $gol->golongan;
                $data->tgl_mk = $tgl_mk;
                $data->tgl_gol = $tgl_gol;
                $data->status_kerja = $request->status_kerja;
                $data->user_insert = Auth::user()->id;
                $data->no_rek = $request->no_rek;
                $data->acc = 1;
                $data->user_approve = Auth::user()->id;
                $data->tgl_sk = $request->tgl_sk;
                $data->keterangan = $request->ket_alasan_sk;
                $data->id_mentor = $request->mntor;
                $data->jkk = $request->status_kerja == 'Contract' ? $request->jkk: 0;
                $data->jkm =  $request->status_kerja == 'Contract' ? $request->jkm:0;
                $data->jht =  $request->status_kerja == 'Contract' ? $request->jht:0;
                $data->jpn =  $request->status_kerja == 'Contract' ? $request->jpn:0;
                $data->kesehatan =  $request->status_kerja == 'Contract' ?  $request->kesehatan:0;
                if($request->hasFile('upload_sk')){
                    $image = $request->file('upload_sk');
            
                    if($image->isValid()){
                        $image_name = $image->getClientOriginalName();
                        $upload_path = 'fileSK';
                        $image->move($upload_path, $image_name);
                        $data->file_sk = $image_name;
                    }
                }else{
                    $data->file_sk = $file_sk;
                }
                
                $data->save();
            }
            
            $p = Karyawan::where('id_karyawan',$id)->first();
            if($request->status_kerja == 'Magang' && $p->status_kerja == 'Magang' ){
                Karyawan::where('id_karyawan', $id)->update([
                    'id_mentor' => $request->mntor,
                    ]);
            }else if($request->status_kerja != 'Magang'){
                Karyawan::where('id_karyawan', $id)->update([
                    'id_mentor' => NULL,
                    ]);
            }
            
            
            if($p->status_kerja == 'Contract'){
                $jjk = $request->jkk == '' ? $p->jkk : $request->jkk ;
                $jkm = $request->jkm == '' ? $p->jkm : $request->jkm ;
                $jht = $request->jht == '' ? $p->jht : $request->jht ;
                $jpn = $request->jpn == '' ? $p->jpn : $request->jpn ;
                $ksh = $request->kesehatan == '' ? $p->kesehatan : $request->kesehatan;
                $gg = Karyawan::where('id_karyawan',$id)->update([
                    'jkk' => $jjk,
                    'jkm' => $jkm,
                    'jht' => $jht,
                    'jpn' => $jpn,
                    'kesehatan' => $ksh,
                    'user_update' => Auth::user()->id
                ]);
                
            }else{
                $jjk = $request->jkk == '' ? 0 : $request->jkk ;
                $jkm = $request->jkm == '' ? 0 : $request->jkm ;
                $jht = $request->jht == '' ? 0 : $request->jht ;
                $jpn = $request->jpn == '' ? 0 : $request->jpn ;
                $ksh = $request->kesehatan == '' ? 0 : $request->kesehatan;
                $gg = Karyawan::where('id_karyawan',$id)->update([
                    'jkk' => $jjk,
                    'jkm' => $jkm,
                    'jht' => $jht,
                    'jpn' => $jpn,
                    'kesehatan' => $ksh,
                    'user_update' => Auth::user()->id
                ]);
            }
         }
         

        
        
        
        // dd($gg);
        
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Kenaikan Pangkat '.$kar->nama);
    }else if($request->action == 'jabatan'){
        if(Auth::user()->level != 'kacab'){
            NomorSK::where('id_com', Auth::user()->id_com)->create([
                'urut' => $nomor == null || $nomor == '' ? 1 : $nomor + 1,
                'id_com' => Auth::user()->id_com,
                'user_insert' => Auth::user()->id,
            ]);
        }
        if($request->kepegawaian== 'kacab'){
            $con = RekapJabatan::where('id_karyawan', $id)->whereDate('created_at', $date)->first();
            $jab = Jabatan::where('id', $request->jabatan)->first();
            // Karyawan::where('id_karyawan', $id)->update([
            //   'jabatan' => $request->jabatan,
            //   'pr_jabatan' => $jab->pr_jabatan,
            //   'jab_daerah' => $request->jab_daerah == 'on' ? "1" : "0",
            //   'id_spv' => $request->id_spv,
            //   'plt' => $request->plt == 'on' ? "1" : "0",
            //   'user_update' => Auth::user()->id
            // ]);
            
            if($con){
                $data = RekapJabatan::findOrFail($con->id_rekjab);
                $data->id_karyawan = $id;
                $data->nama = $kar->nama;
                $data->id_jabatan = $request->jabatan;
                $data->tgl_jab = $request->tgl_jab;
                $data->keterangan = $request->ket_alasan_jab;
                $data->id_spv = $request->id_spv;
                $data->user_insert = Auth::user()->id;
                $data->user_approve = null;
                $data->plt = $request->plt == 'on' ? "1" : "0";
                $data->jab_daerah = $request->jab_daerah == 'on' ? "1" : "0";
                $data->acc = 2;
                // $data->jab_daerah = $request->jab_daerah == 'on' ? "1" : "0";
                if($request->hasFile('upload_file')){
                    $image = $request->file('upload_file');
            
                    if($image->isValid()){
                        $image_name = $image->getClientOriginalName();
                        $upload_path = 'fileSK';
                        $image->move($upload_path, $image_name);
                        $data->file = $image_name;
                    }
                }
                
                $data->update();
                
            } else{
                $data = new RekapJabatan;
                $data->id_karyawan = $id;
                $data->nama = $kar->nama;
                $data->id_jabatan = $request->jabatan;
                $data->tgl_jab = $request->tgl_jab;
                $data->keterangan = $request->ket_alasan_jab;
                $data->id_spv = $request->id_spv;
                $data->user_insert = Auth::user()->id;
                $data->plt = $request->plt == 'on' ? "1" : "0";
                $data->jab_daerah = $request->jab_daerah == 'on' ? "1" : "0";
                $data->acc = 2;
                // $data->jab_daerah = $request->jab_daerah == 'on' ? "1" : "0";
                if($request->hasFile('upload_file')){
                    $image = $request->file('upload_file');
            
                    if($image->isValid()){
                        $image_name = $image->getClientOriginalName();
                        $upload_path = 'fileSK';
                        $image->move($upload_path, $image_name);
                        $data->file = $image_name;
                    }
                }
                
                $data->save();
            }
         return response()->json(['success' => 'masuk  kacab ']);
        }else{
        $con = RekapJabatan::where('id_karyawan', $id)->whereDate('created_at', $date)->first();
        $jab = Jabatan::where('id', $request->jabatan)->first();
        
        Karyawan::where('id_karyawan', $id)->update([
          'jabatan' => $request->jabatan,
          'pr_jabatan' => $jab->pr_jabatan == 'null' ? null :  $jab->pr_jabatan,
          'jab_daerah' => $request->jab_daerah == 'on' ? "1" : "0",
          'id_spv' => $request->id_spv,
          'plt' => $request->plt == 'on' ? "1" : "0",
          'user_update' => Auth::user()->id
        ]);
        
        if($con){
            $data = RekapJabatan::findOrFail($con->id_rekjab);
            $data->id_karyawan = $id;
            $data->nama = $kar->nama;
            $data->id_jabatan = $request->jabatan;
            $data->tgl_jab = $request->tgl_jab;
            $data->keterangan = $request->ket_alasan_jab;
            $data->id_spv = $request->id_spv;
            $data->user_insert = Auth::user()->id;
            $data->user_approve = Auth::user()->id;
            $data->plt = $request->plt == 'on' ? "1" : "0";
            $data->jab_daerah = $request->jab_daerah == 'on' ? "1" : "0";
            $data->acc = 1;
            // $data->jab_daerah = $request->jab_daerah == 'on' ? "1" : "0";
            if($request->hasFile('upload_file')){
                $image = $request->file('upload_file');
        
                if($image->isValid()){
                    $image_name = $image->getClientOriginalName();
                    $upload_path = 'fileSK';
                    $image->move($upload_path, $image_name);
                    $data->file = $image_name;
                }
            }else{
                $data->file = $file_sk;
            }
            
            $data->update();
            
        } else{
            $data = new RekapJabatan;
            $data->id_karyawan = $id;
            $data->nama = $kar->nama;
            $data->id_jabatan = $request->jabatan;
            $data->tgl_jab = $request->tgl_jab;
            $data->id_spv = $request->id_spv;
            $data->user_insert = Auth::user()->id;
            $data->user_approve = Auth::user()->id;
            $data->plt = $request->plt == 'on' ? "1" : "0";
            $data->jab_daerah = $request->jab_daerah == 'on' ? "1" : "0";
            $data->acc = 1;
            // $data->jab_daerah = $request->jab_daerah == 'on' ? "1" : "0";
            if($request->hasFile('upload_file')){
                $image = $request->file('upload_file');
        
                if($image->isValid()){
                    $image_name = $image->getClientOriginalName();
                    $upload_path = 'fileSK';
                    $image->move($upload_path, $image_name);
                    $data->file = $image_name;
                }
            }else{
                $data->file = $file_sk;
            }
            
            $data->save();
        }
        
        //  return response()->json(['success' => 'berhasil']);
    }
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Kenaikan Jabatan '.$kar->nama);
    }else if($request->action == 'keluarga'){
        // return($request->arr_pas);
        $con = RekapKeluarga::where('id_karyawan', $id)->whereDate('created_at', $date)->first();
        $peg = Karyawan::where('id_karyawan', $id)->first();
        $rek_arr_anak = json_decode($request->arr_anak);
        $rek_arr_pas = json_decode($request->arr_pas);
        
        $nm_pasangan = [];
        $tgl_lahir = [];
        $tgl_nikah = [];
        $id_pasangan = [];
        
        $nm_anak = [];
        $tgl_lahir_anak = [];
        $status_anak = [];
        if(!empty($rek_arr_anak)){
            foreach($rek_arr_anak as $val){
                $nm_anak[] = $val->nm_anak;
                $tgl_lahir_anak[] = $val->tgl_lahir_anak;
                $status_anak[] =  $val->status;
                
            }
        }  
        
        
        if(!empty($rek_arr_pas)){
            foreach($rek_arr_pas as $val){
                $nm_pasangan[] = $val->nm_pasangan;
                $tgl_lahir[] = $val->tgl_lahir;
                $tgl_nikah[] =  $val->tgl_nikah;
                $id_pasangan[] = $request->tj_pas == null ? null : $val->id_pasangan;
            }
        }
        
        // return([$nm_pasangan,$tgl_nikah, $id_pasangan, $tgl_nikah ]);
        // $nm_pasangan = $request->nm_pasangan != '' ? serialize($request->nm_pasangan) : null;
        // $tgl_lahir = $request->tgl_lahir != '' ? serialize($request->tgl_lahir) : null;
        // $tgl_nikah = $request->tgl_nikah != '' ? serialize($request->tgl_nikah) : null;
        
        // $nm_anak = $request->nm_anak != '' ? serialize($request->nm_anak) : null;
        // $tgl_lahir_anak = $request->tgl_lahir_anak != '' ? serialize($request->tgl_lahir_anak) : null;
        // $status_anak = $request->status_anak != '' ? serialize($request->status_anak) : null;
        
        // dd($nm_pasangan);
        
        if($request->kepegawaian== 'kacab'){
            if($con){
                $data = RekapKeluarga::findOrFail($con->id_rekkel);
                $data->id_karyawan = $id;
                $data->nama = $kar->nama;
                $data->status_nikah = $request->status_nikah;
                $data->no_kk = $request->no_kk;
                $data->acc = 2;
                $data->user_approve = null;
                // $data->scan_kk = $request->file('scan_kk')->getClientOriginalName();
                if($request->hasFile('scan_kk')){
                    
                    $image = $request->file('scan_kk');
                    $data->scan_kk = $image->getClientOriginalName();
                    // if($image->isValid()){
                    //     $image_name = $image->getClientOriginalName();
                    //     $upload_path = 'upload';
                    //     $image->move($upload_path, $image_name);
                    //     $data->scan_kk = $image_name;
                    // }
                }
                // $data->id_pasangan = !empty($rek_arr_pas) ? serialize($id_pasangan) : null;
                $data->id_pasangan = !empty($rek_arr_pas) ? serialize($id_pasangan) : null;
                $data->nm_pasangan = !empty($rek_arr_pas) ? serialize($nm_pasangan) : null;
                $data->tgl_lahir = !empty($rek_arr_pas) ? serialize($tgl_lahir) : null;
                $data->tgl_nikah = !empty($rek_arr_pas) ? serialize($tgl_nikah) : null;
                $data->nm_anak = !empty($rek_arr_anak) ? serialize($nm_anak) : null;
                $data->tgl_lahir_anak = !empty($rek_arr_anak) ? serialize($tgl_lahir_anak) : null;
                $data->status_anak = !empty($rek_arr_anak) ? serialize($status_anak) : null;
                $data->user_insert = Auth::user()->id;
                $data->update();
                
            } else{
                $data = new RekapKeluarga;
                $data->id_karyawan = $id;
                $data->nama = $kar->nama;
                $data->status_nikah = $request->status_nikah;
                $data->no_kk = $request->no_kk;
                $data->acc = 2;
                if($request->hasFile('scan_kk')){
                    $image = $request->file('scan_kk');
                    $data->scan_kk = $image->getClientOriginalName();
                    // dd($request->file('scan_kk')->isValid());
                    // if($request->file('scan_kk')->isValid()){
                    //     $image_name = $image->getClientOriginalName();
                    //     $upload_path = 'upload';
                    //     $image->move($upload_path, $image_name);
                    //     $dsa = $image_name;
                    // }
                }
                
                // $data->scan_kk = $request->file('scan_kk')->getClientOriginalName();
                $data->id_pasangan = !empty($rek_arr_pas) ? serialize($id_pasangan) : null;
                $data->nm_pasangan = !empty($rek_arr_pas) ? serialize($nm_pasangan) : null;
                $data->tgl_lahir = !empty($rek_arr_pas) ? serialize($tgl_lahir) : null;
                $data->tgl_nikah = !empty($rek_arr_pas) ? serialize($tgl_nikah) : null;
                $data->nm_anak = !empty($rek_arr_anak) ? serialize($nm_anak) : null;
                $data->tgl_lahir_anak = !empty($rek_arr_anak) ? serialize($tgl_lahir_anak) : null;
                $data->status_anak = !empty($rek_arr_anak) ? serialize($status_anak) : null;
                $data->user_insert = Auth::user()->id;
                
                
                
                $data->save();
            }
        }else{
            
            $data = Karyawan::findOrFail($peg->id_karyawans);
            $data->status_nikah = $request->status_nikah;
            if($request->status_nikah == 'Belum Menikah'){
                $data->no_kk = null;
                $data->scan_kk = null;
                $data->nm_pasangan = null;
                $data->id_pasangan = null;
                $data->tgl_lahir = null;
                $data->tgl_nikah = null;
                $data->nm_anak = null;
                $data->tgl_lahir_anak = null;
                $data->status_anak = null;
                $data->user_update = Auth::user()->id;
            }else{
                
                $data->no_kk = $request->no_kk;
                
                
                if($request->hasFile('scan_kk')){
                    $image = $request->file('scan_kk');
                    // dd($image);
                    if($image->isValid()){
                        $image_name = $image->getClientOriginalName();
                        $upload_path = 'upload';
                        $image->move($upload_path, $image_name);
                        $data->scan_kk = $image_name;
                    }
                }
                $data->id_pasangan = !empty($rek_arr_pas) ? serialize($id_pasangan) : null;
                $data->nm_pasangan = !empty($rek_arr_pas) ? serialize($nm_pasangan) : null;
                $data->tgl_lahir = !empty($rek_arr_pas) ? serialize($tgl_lahir) : null;
                $data->tgl_nikah = !empty($rek_arr_pas) ? serialize($tgl_nikah) : null;
                $data->nm_anak = !empty($rek_arr_anak) ? serialize($nm_anak) : null;
                $data->tgl_lahir_anak = !empty($rek_arr_anak) ? serialize($tgl_lahir_anak) : null;
                $data->status_anak = !empty($rek_arr_anak) ? serialize($status_anak) : null;
                $data->tj_pas = $request->tj_pas == 'on' ? "1" : "0";
                $data->warning_pasangan = $request->warning_pasangan == 'on' ? "1" : "0";
                $data->user_update = Auth::user()->id;
            }
            
            $data->update();
            if(!empty($id_pasangan)){
                Karyawan::whereIn('id_karyawan', $id_pasangan)->update([
                    "id_pasangan" => serialize(array($id_pasangan)),
                    "status_nikah" => "Menikah",
                    "no_kk" => $request->no_kk,
                    "scan_kk" => $request->namafile_scan_kk,
                    "nm_pasangan" => serialize(array($request->nama)),
                    "tgl_lahir" => serialize(array($request->ttl)),
                    "tgl_nikah" => serialize($tgl_nikah),
                    "nm_anak" => !empty($request->arr_anak) ? serialize($nm_anak) : null,
                    "tgl_lahir_anak" => !empty($request->arr_anak) ? serialize($tgl_lahir_anak) : null,
                    "status_anak" => !empty($request->arr_anak) ? serialize($status_anak) : null,
                    "user_update" => Auth::user()->id
                ]);
            }
            
            if($con){
                $data = RekapKeluarga::findOrFail($con->id_rekkel);
                $data->id_karyawan = $id;
                $data->nama = $kar->nama;
                $data->status_nikah = $request->status_nikah;
                $data->no_kk = $request->no_kk;
                $data->acc = 1;
                $data->user_approve = Auth::user()->id;
                // $data->scan_kk = $request->file('scan_kk')->getClientOriginalName();
                if($request->hasFile('scan_kk')){
                    
                    $image = $request->file('scan_kk');
                    $data->scan_kk = $image->getClientOriginalName();
                    // if($image->isValid()){
                    //     $image_name = $image->getClientOriginalName();
                    //     $upload_path = 'upload';
                    //     $image->move($upload_path, $image_name);
                    //     $data->scan_kk = $image_name;
                    // }
                }
                // $data->id_pasangan = !empty($rek_arr_pas) ? serialize($id_pasangan) : null;
                $data->id_pasangan = !empty($rek_arr_pas) ? serialize($id_pasangan) : null;
                $data->nm_pasangan = !empty($rek_arr_pas) ? serialize($nm_pasangan) : null;
                $data->tgl_lahir = !empty($rek_arr_pas) ? serialize($tgl_lahir) : null;
                $data->tgl_nikah = !empty($rek_arr_pas) ? serialize($tgl_nikah) : null;
                $data->nm_anak = !empty($rek_arr_anak) ? serialize($nm_anak) : null;
                $data->tgl_lahir_anak = !empty($rek_arr_anak) ? serialize($tgl_lahir_anak) : null;
                $data->status_anak = !empty($rek_arr_anak) ? serialize($status_anak) : null;
                $data->user_insert = Auth::user()->id;
                $data->update();
                
            } else{
                
                
                $data = new RekapKeluarga;
                $data->id_karyawan = $id;
                $data->nama = $kar->nama;
                $data->status_nikah = $request->status_nikah;
                $data->no_kk = $request->no_kk;
                $data->acc = 1;
                $data->user_approve = Auth::user()->id;
                if($request->hasFile('scan_kk')){
                    $image = $request->file('scan_kk');
                    $data->scan_kk = $image->getClientOriginalName();
                    // dd($request->file('scan_kk')->isValid());
                    // if($request->file('scan_kk')->isValid()){
                    //     $image_name = $image->getClientOriginalName();
                    //     $upload_path = 'upload';
                    //     $image->move($upload_path, $image_name);
                    //     $dsa = $image_name;
                    // }
                }
                
                // $data->scan_kk = $request->file('scan_kk')->getClientOriginalName();
                $data->id_pasangan = !empty($rek_arr_pas) ? serialize($id_pasangan) : null;
                $data->nm_pasangan = !empty($rek_arr_pas) ? serialize($nm_pasangan) : null;
                $data->tgl_lahir = !empty($rek_arr_pas) ? serialize($tgl_lahir) : null;
                $data->tgl_nikah = !empty($rek_arr_pas) ? serialize($tgl_nikah) : null;
                $data->nm_anak = !empty($rek_arr_anak) ? serialize($nm_anak) : null;
                $data->tgl_lahir_anak = !empty($rek_arr_anak) ? serialize($tgl_lahir_anak) : null;
                $data->status_anak = !empty($rek_arr_anak) ? serialize($status_anak) : null;
                $data->user_insert = Auth::user()->id;
                
                
                
                $data->save();
            }
        
        }
        
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Keluarga '.$kar->nama);
        
    }
    
   
    
    return response()->json(['success' => 'berhasil']);
}

    public function getbpjskar($id, Request $request)
    {
        if(request()->ajax())
        {   
            // $id = $request->id_hide;
            $data = Karyawan::where('id_karyawan', $id)->first();
            return response()->json(['result' => $data]);
        }
    }
    
    public function postbpjskar($id, Request $request)
    {
        // $id = $request->id_hide;
        
        $kar = Karyawan::where('id_karyawan', $id)->first();
        $jjk = $request->jkk;
        $jkm = $request->jkm;
        $jht = $request->jht;
        $jpn = $request->jpn;
        $gg = Karyawan::where('id_karyawan',$id)->update([
            'jkk' => $jjk,
            'jkm' => $jkm,
            'jht' => $jht,
            'jpn' => $jpn,
            'kesehatan' => $request->kesehatan,
            'user_update' => Auth::user()->id
        ]);
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Keikutsertaan BPJS Karyawan '.$kar->nama);
        return response()->json(['success' => 'Data is successfully updated']);
    }

public function riwayat(Request $request){
     
        if($request->ajax())
        {
            // dd($request->id);
            $data = Kenaikan::where('id_karyawan', $request->id)->get();
            // dd($data);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('details', function($data){
                        $button = '<a class="btn btn-info btn-xs" target="_blank" href="'. url('fileSK/'.$data->file_sk) .'">Lihat</a>';
                        return $button;
                    })
                    
                    ->rawColumns(['details'])
                    ->make(true);
        }
        // return view('sample_data');
}

public function riwayatjabatan(Request $request){
     
        if($request->ajax())
        {
            // dd($request->id);
            $data = RekapJabatan::where('id_karyawan', $request->id)->get();
            
            // dd($data);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('jabatan', function($data){
                        $jab = Jabatan::where('id', $data->id_jabatan)->first();
                        $cek = $jab->jabatan;
                        return $cek;
                    })
                    ->addColumn('details', function($data){
                        $button = '<a class="btn btn-info btn-xs" target="_blank" href="'. url('fileSK/'.$data->file) .'">Lihat</a>';
                        return $button;
                    })
                    
                    ->rawColumns(['details'])
                    ->make(true);
        }
        // return view('sample_data');
}

public function riwayatkeluarga(Request $request){
     
        if($request->ajax())
        {
            // dd($request->id);
            $data = RekapKeluarga::where('id_karyawan', $request->id)->get();
            
            // dd($data);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($request) {
                        return $request->created_at->format('Y-m-d'); // human readable format
                    })
                    ->addColumn('status_nikah', function($data){
                        if($data->status_nikah == 'Menikah'){
                            $btn = '<span class="badge badge-success">'.$data->status_nikah.'</span>';
                        }elseif($data->status_nikah == 'Belum Menikah'){
                            $btn = '<span class="badge badge-warning">'.$data->status_nikah.'</span>';
                        }else{
                            $btn = '<span class="badge badge-danger">'.$data->status_nikah.'</span>';
                        }
                        return $btn;
                    })
                    ->addColumn('details', function($data){
                        if(!empty($data->scan_kk)){
                            $button = '<a class="btn btn-info btn-xs" target="_blank" href="'. url('upload/'.$data->scan_kk) .'">Lihat</a>';
                        }else{
                            $button = 'Tidak Ada';
                        }
                        return $button;
                    })
                    ->addColumn('jum_pasangan', function($data){
                        if(!empty($data->nm_pasangan)){
                            $pas = unserialize($data->nm_pasangan);
                            $tot = count($pas);
                        }else{
                            $tot = 0;
                        }
                        return $tot;
                    })
                    ->addColumn('jum_anak', function($data){
                        if(!empty($data->nm_anak)){
                            $anak = unserialize($data->nm_anak);
                            $tot1 = count($anak);
                            return $tot1;
                        }else{
                            return 0;
                        }
                        
                    })
                    
                    ->rawColumns(['details', 'status_nikah'])
                    ->make(true);
        }
        // return view('sample_data');
}

public function riwayatmutasi(Request $request){
     
        if($request->ajax())
        {
            // dd($request->id);
            $data = MutasiKaryawan::where('id_karyawan', $request->id)->get();
            
            // dd($data);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('kantor_asal', function($data){
                        $kan = Kantor::where('id', $data->kantor_asal)->where('id_com', Auth::user()->id_com)->first();
                        $cek = $kan->unit;
                        return $cek;
                    })
                    ->addColumn('kantor_baru', function($data){
                        $kan = Kantor::where('id', $data->kantor_baru)->where('id_com', Auth::user()->id_com)->first();
                        $cek = $kan->unit;
                        return $cek;
                    })
                    
                    ->addColumn('file', function($data){
                        $button = '<a class="btn btn-info btn-xs" target="_blank" href="'. url('fileSK/'.$data->file_sk) .'">Lihat</a>';
                        return $button;
                    })
                    
                    ->rawColumns(['file'])
                    ->make(true);
        }
        // return view('sample_data');
}

    
    public function getkaryawan(Request $request){
        $pass = [];
        $pass = Karyawan::where('karyawan.aktif', 1)->where('karyawan.id_com', Auth::user()->id_com)
                            ->join('jabatan','jabatan.id', '=','karyawan.jabatan')->get();
                            $h1=[];
                            
                            // if($request->tab == 'nama'){
                                foreach($pass as $key => $val){
                                    $h1[] = [
                                        "text" => $val->nama,
                                        "nama" => $val->nama,
                                        "id" => $val->nama,
                                        "unit_kerja" => $val->unit_kerja,
                                        "jabatan" => $val->jabatan,
                                    ];
                                }
                            // }
        return response()->json($h1);
    }
    
    public function getKaryawanById($id){
        // dd($id);
        $data = Karyawan::join('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
        ->join('daerah', 'daerah.id_daerah', '=', 'karyawan.id_daerah')
        ->where('karyawan.aktif', 1)
        ->where('karyawan.id_karyawan', $id)
        ->select('jabatan.jabatan', 'karyawan.id_daerah', 'daerah.kota as lembur', 'karyawan.id_karyawans', 'karyawan.ttl', 'karyawan.nama', 'karyawan.unit_kerja','karyawan.id_karyawan', 'karyawan.nm_anak', 'karyawan.tgl_lahir_anak', 'karyawan.status_anak')->first();
        // $data['anak'] =  unserialize($data->nm_anak);
        $data['anak'] = unserialize($data->nm_anak);
        $data['tgl_lahir_anak'] = unserialize($data->tgl_lahir_anak);
        $data['status_anak'] = unserialize($data->status_anak);
        
        // if(!empty($data['anak'])){
        //     for ($x = 0; $x < count($data['anak']); $x++){
        //         $data['data'][] = [
        //             "nm_anak" => $data['anak'][$x],
        //             "tgl_lahir_anak" => $data['tgl_lahir_anak'][$x],
        //             "status_anak" => $data['status_anak'][$x]
        //         ];
        //     }
        // }
    
        
        return response()->json($data);
    }
    
    
    public function mutasi_karyawan($id, Request $request){
        $nomor = NomorSK::where('id_com', Auth::user()->id_com)
                ->whereYear('created_at', date('Y')) // Sesuaikan dengan tahun yang diinginkan
                ->whereMonth('created_at', date('m')) // Sesuaikan dengan bulan yang diinginkan
                ->max('urut');
        $date = date('Y-m-d');
        
        $datas = Karyawan::where('id_karyawan', $id)->first();
        if($request->jab_new != ''){
            $jab = Jabatan::where('id', $request->jab_new)->first();
        }
        
        $con = RekapJabatan::where('id_karyawan', $id)->whereDate('created_at', $date)->first();
        $con1 = MutasiKaryawan::where('id_karyawan', $id)->whereDate('created_at', $date)->first();
        $birthDate = new DateTime($datas->tgl_mutasi);
    	$today = new DateTime("today");
    	
    // 	if ($birthDate > $today) { 
    // 	    exit("0 tahun 0 bulan");
    // 	}
    	
    	$y = $today->diff($birthDate)->y;
    	$m = $today->diff($birthDate)->m;
    	$d = $today->diff($birthDate)->d;

        if($con1){
            $data = MutasiKaryawan::findOrFail($con1->id_mutasi);
            $data->id_karyawan = $id;
            $data->kantor_asal = $datas->id_kantor;
            $data->kantor_baru = $request->kantor_baru;
            $data->jabatan_asal = $datas->jabatan;
            $data->jabatan_baru = $request->jab_new ?? null;
            $data->lokasi_asal = $request->lokasi_asal;
            $data->lokasi_baru = $request->lokasi_baru ?? null;
            $data->id_spv = $request->id_spv ?? null;
            $data->user_approve = Auth::user()->level == 'admin' ? Auth::user()->id : null ;
            $data->acc = Auth::user()->kepegawaian== 'kacab' ? 2 : 1; 
            // $data->jab_daerah = $request->jab_daerah == 'on' ? "1" : "0";
            if( Auth::user()->kepegawaian== 'kacab'){
                $data->file_sk = null;
            }else{
                if($request->hasFile('file_sk')){
                    $image = $request->file('file_sk');
                    if($image->isValid()){
                        $image_name = $image->getClientOriginalName();
                        $upload_path = 'fileSK';
                        $image->move($upload_path, $image_name);
                        $data->file_sk = $image_name;
                    }
                }else if($request->file_sk_mutasi != ''){
                    $data->file_sk = $request->file_sk_mutasi;
                }
            }
            
            $data->tgl_mutasi = $request->tgl_mutasi;
            $data->durasi = $y." tahun ".$m." bulan";
            $data->user_insert = Auth::user()->id;
            $data->update();
        }else{
            $data = new MutasiKaryawan;
            $data->id_karyawan = $request->id_karyawan;
            $data->kantor_asal = $datas->id_kantor;
            $data->kantor_baru = $request->kantor_baru;
            $data->jabatan_asal = $datas->jabatan;
            $data->jabatan_baru = $request->jab_new ?? null;
            $data->lokasi_asal = $request->lokasi_asal;
            $data->lokasi_baru = $request->lokasi_baru ?? null;
            $data->id_spv = $request->id_spv ?? null;
            $data->user_approve = Auth::user()->level == 'admin' ? Auth::user()->id : null ;
            $data->acc = Auth::user()->kepegawaian== 'kacab' ? 2 : 1;
            $data->user_insert = Auth::user()->id;
            if( Auth::user()->kepegawaian== 'kacab'){
                $data->file_sk = null;
            }else{
                if($request->hasFile('file_sk')){
                    $image = $request->file('file_sk');
                    if($image->isValid()){
                        $image_name = $image->getClientOriginalName();
                        $upload_path = 'fileSK';
                        $image->move($upload_path, $image_name);
                        $data->file_sk = $image_name;
                    }
                }else if($request->file_sk_mutasi != ''){
                    $data->file_sk = $request->file_sk_mutasi;
                }
            }
            
            $data->tgl_mutasi = $request->tgl_mutasi;
            $data->durasi = $y." tahun ".$m." bulan";
            
            $data->save();
        }
        $kantor = Kantor::where('id', $request->kantor_baru)->where('id_com', Auth::user()->id_com)->first();
        
        if($request->jab_new != '' && $request->jab_new != $datas->jabatan){
            
            if(Auth::user()->level != 'kacab'){
                Karyawan::where('id_karyawan', $id)->update([
                    'id_kantor' => $request->kantor_baru,
                    'unit_kerja' => $kantor->unit,
                    'id_daerah' => $request->lokasi_baru,
                    'kantor_induk' => $kantor->kantor_induk,
                    'tgl_mutasi' => $request->tgl_mutasi,
                    'jabatan' => $request->jab_new,
                    'id_spv' => $request->id_spv,
                    'pr_jabatan' => $jab->pr_jabatan == 'null' ? null :  $jab->pr_jabatan,
                    'user_update' => Auth::user()->id,
                ]);
            }
        
        
            if($con){
                $data = RekapJabatan::findOrFail($con->id_rekjab);
                $data->id_karyawan = $id;
                $data->nama = $datas->nama;
                $data->id_jabatan = Auth::user()->level != 'kacab' ? $request->jab_new : $datas->jabatan;
                $data->tgl_jab = $request->tgl_mutasi;
                $data->id_spv = $request->id_spv;
                $data->user_insert = Auth::user()->id;
                // $data->jab_daerah = $request->jab_daerah == 'on' ? "1" : "0";
                if( Auth::user()->kepegawaian== 'kacab'){
                    $data->file = null;
                    
                }else{
                    if($request->hasFile('file_sk')){
                        $image = $request->file('file_sk');
                        if($image->isValid()){
                            $image_name = $image->getClientOriginalName();
                            $upload_path = 'fileSK';
                            $image->move($upload_path, $image_name);
                            $data->file = $image_name;
                        }
                    }else if($request->file_sk_mutasi != ''){
                        $data->file = $request->file_sk_mutasi;
                    }
                }
                
                $data->update();
                
            } else{
                $data = new RekapJabatan;
                $data->id_karyawan = $id;
                $data->nama = $datas->nama;
                $data->id_jabatan = Auth::user()->level != 'kacab' ? $request->jab_new : $datas->jabatan;
                $data->tgl_jab = $request->tgl_mutasi;
                $data->id_spv = $request->id_spv;
                $data->user_insert = Auth::user()->id;
                // $data->jab_daerah = $request->jab_daerah == 'on' ? "1" : "0";
                    if( Auth::user()->kepegawaian== 'kacab'){
                        $data->file = null;
                    }else{
                        if($request->hasFile('file_sk')){
                            $image = $request->file('file_sk');
                            if($image->isValid()){
                                $image_name = $image->getClientOriginalName();
                                $upload_path = 'fileSK';
                                $image->move($upload_path, $image_name);
                                $data->file = $image_name;
                            }
                        }else if($request->file_sk_mutasi != ''){
                            $data->file = $request->file_sk_mutasi;
                        }
                    }
                
                $data->save();
            }
        }else{
            
            if(Auth::user()->level != 'kacab'){
                Karyawan::where('id_karyawan', $id)->update([
                    'id_kantor' => $request->kantor_baru,
                    'unit_kerja' => $kantor->unit,
                    'kantor_induk' => $kantor->kantor_induk,
                    'tgl_mutasi' => $request->tgl_mutasi,
                    'user_update' => Auth::user()->id,
                    'id_daerah' => $request->lokasi_baru,
                ]);
            }
        }
        
        if(Auth::user()->level != 'kacab'){
            NomorSK::where('id_com', Auth::user()->id_com)->create([
                'urut' => $nomor == null || $nomor == '' ? 1 : $nomor + 1,
                'id_com' => Auth::user()->id_com,
                'user_insert' => Auth::user()->id,
            ]);
        }
        
        
        // \LogActivity::addToLog(Auth::user()->name.' Memutasi '.$datas->nama);
        return response()->json(['data' => 'success']);
    }
    
    
      public function perubahankaryawan(Request $request)
   {
        // $unit = Kantor::where('id_com', Auth::user()->id_com)->get(); 
        // $jabat = Jabatan::where('id_com', Auth::user()->id_com)->get();
        // $karyawan = karyawan::join('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')->where('karyawan.aktif', 1)->select('jabatan.jabatan', 'karyawan.id_karyawans', 'karyawan.nama', 'karyawan.id_karyawan')->where('karyawan.id_com', Auth::user()->id_com)->get();

            $jabat = Jabatan::get();
            $tgl_awal = $request->dari;
            $tgl_akhir = $request->sampai != '' ? $request->sampai : date('Y-m-d');
            $perubahan = $request->perubahan != '' ? $request->perubahan : null;
            $status = $request->status != '' ? $request->status : null;
            
        if($request->ajax()){
                $id_com = $request->com;

                $data = DB::table('rekap_jabatan')
                    ->selectRaw("DATE_FORMAT(rekap_jabatan.created_at,'%Y-%m-%d') as tanggal_buat,karyawan.nama,rekap_jabatan.id_rekjab as id ,rekap_jabatan.user_approve as user_approve,'jabatan' as nama_tabel,rekap_jabatan.acc as acc,rekap_jabatan.id_karyawan as id_karyawan")
                    ->whereIn('rekap_jabatan.id_karyawan', function($query) use ($id_com){
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
                    ->leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'rekap_jabatan.id_karyawan')
                    ->unionAll(DB::table('kenaikan')
                        ->selectRaw("DATE_FORMAT(kenaikan.created_at,'%Y-%m-%d') as tanggal_buat,karyawan.nama,kenaikan.id_naik as id ,kenaikan.user_approve as user_approve,'pangkat' as nama_tabel,kenaikan.acc as acc,kenaikan.id_karyawan as id_karyawan")
                        ->whereIn('kenaikan.id_karyawan', function($query) use ($id_com){
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
                        ->leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'kenaikan.id_karyawan'))
                    ->unionAll(DB::table('rekap_keluarga')
                        ->selectRaw("DATE_FORMAT(rekap_keluarga.created_at,'%Y-%m-%d') as tanggal_buat,karyawan.nama,rekap_keluarga.id_rekkel as id ,rekap_keluarga.user_approve as user_approve,'keluarga' as nama_tabel,rekap_keluarga.acc as acc,rekap_keluarga.id_karyawan as id_karyawan")
                        ->whereIn('rekap_keluarga.id_karyawan', function($query) use ($id_com){
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
                        ->leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'rekap_keluarga.id_karyawan'))
                    ->unionAll(DB::table('mutasi_karyawan')
                        ->selectRaw("DATE_FORMAT(mutasi_karyawan.created_at,'%Y-%m-%d') as tanggal_buat,karyawan.nama,mutasi_karyawan.id_mutasi as id ,mutasi_karyawan.user_approve as user_approve,'mutasi' as nama_tabel,mutasi_karyawan.acc as acc,mutasi_karyawan.id_karyawan as id_karyawan")
                        ->whereIn('mutasi_karyawan.id_karyawan', function($query) use ($id_com){
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
                        ->leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'mutasi_karyawan.id_karyawan'))
                    ->get();
            
                $datas = $data;
            
                if ($status !== null) {
                    $datas = $datas->where('acc', '=', $status);
                }
            
                if ($perubahan !== null) {
                    $datas = $datas->where('nama_tabel', '=', $perubahan);
                }
            
                $datas = $datas
                    ->where('tanggal_buat', '>=', $tgl_awal)
                    ->where('tanggal_buat', '<=', $tgl_akhir);
            
                $Results = $datas->all();


                



        // $data = DB::table('rekap_jabatan')
        //     ->selectRaw("DATE_FORMAT(rekap_jabatan.created_at,'%Y-%m-%d') as tanggal_buat,karyawan.nama,rekap_jabatan.id_rekjab as id ,rekap_jabatan.user_approve as user_approve,'jabatan' as nama_tabel,rekap_jabatan.acc as acc")
        //     ->leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'rekap_jabatan.id_karyawan')
        //     ->unionAll(DB::table('kenaikan')
        //         ->selectRaw("DATE_FORMAT(kenaikan.created_at,'%Y-%m-%d') as tanggal_buat,karyawan.nama,kenaikan.id_naik as id ,kenaikan.user_approve as user_approve,'pangkat' as nama_tabel,kenaikan.acc as acc")
        //             ->leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'kenaikan.id_karyawan'))
        //     ->unionAll(DB::table('rekap_keluarga')
        //         ->selectRaw("DATE_FORMAT(rekap_keluarga.created_at,'%Y-%m-%d') as tanggal_buat,karyawan.nama,rekap_keluarga.id_rekkel as id ,rekap_keluarga.user_approve as user_approve,'keluarga' as nama_tabel,rekap_keluarga.acc as acc")
        //             ->leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'rekap_keluarga.id_karyawan'))
        //     ->get();
        
        // $datas = $data->where('nama_tabel', '=', $perubahan)
        //     ->where('tanggal_buat', '>=', $tgl_awal)
        //     ->where('tanggal_buat', '<=', $tgl_akhir)
        //     ->where('acc', '=', $status);
        
        // $Results = $datas->all();


            // if($perubahan == 'jabatan'){ 
                
            //     $data = RekapJabatan::leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'rekap_jabatan.id_karyawan')
            //     ->selectRaw(" rekap_jabatan.* ,DATE_FORMAT(rekap_jabatan.created_at,'%Y-%m-%d') as tanggal_buat , karyawan.nama")
            //     ->whereRaw("DATE_FORMAT(rekap_jabatan.created_at,'%Y-%m-%d') >= '$tgl_awal' AND DATE_FORMAT(rekap_jabatan.created_at,'%Y-%m-%d') <= '$tgl_akhir'AND rekap_jabatan.acc = '$status'")
            //     ->get();
                
            // }else if($perubahan == 'pangkat'){
                
            //     $data = Kenaikan::leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'kenaikan.id_karyawan')
            // ->selectRaw("kenaikan.*,DATE_FORMAT(kenaikan.created_at,'%Y-%m-%d') as tanggal_buat , karyawan.nama")
            // ->whereRaw("DATE_FORMAT(kenaikan.created_at,'%Y-%m-%d') >= '$tgl_awal' AND DATE_FORMAT(kenaikan.created_at,'%Y-%m-%d') <= '$tgl_akhir' AND kenaikan.acc= '$status'")
            // ->get();
            
            // }else if($perubahan == 'keluarga'){
                
            //     $data = RekapKeluarga::leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'rekap_keluarga.id_karyawan')
            // ->selectRaw("rekap_keluarga.*,DATE_FORMAT(rekap_keluarga.created_at,'%Y-%m-%d')as tanggal_buat , karyawan.nama")
            // ->whereRaw("DATE_FORMAT(rekap_keluarga.created_at,'%Y-%m-%d') >= '$tgl_awal' AND DATE_FORMAT(rekap_keluarga.created_at,'%Y-%m-%d') <= '$tgl_akhir' AND rekap_keluarga.acc= '$status' ")
            // ->get();
            
            // }
            
            return DataTables::of($Results)
            ->addIndexColumn()
            ->addColumn('apr', function($Results){
                if($Results->acc == 1){
                $button = '<label class="btn btn-success btn-sm"  style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Approved"><i class="fa fa-check"></i></label>';
                    }else if($Results->acc == 0){
                    $button = '<label class="btn btn-danger btn-sm" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Rejected"><i class="fa fa-ban"></i></label>';
                        }else if($Results->acc == 2){
                            $button = '<label class="btn btn-warning btn-sm" style="cursor: auto" data-toggle="tooltip" data-placement="top" title="Pending"><i class="fa fa-stream"></i></label>';
                        }
                
                return $button;
            })
            
            ->addColumn('userap', function($Results){
                $ss = '';
                if( $Results -> user_approve != ''){
                    $ss = User::select('users.name')->where('id',$Results->user_approve )->first()->name;
                }else{
                    $ss = '';
                }
                return $ss;
            })

              ->rawColumns(['apr','userap'])
            ->make(true);
        }
        
       return view('karyawan.pengajuan_perubahan',compact('jabat'));
   }
   
     public function perbkarBy(Request $request, $id){
        $data['karyawan'] = Karyawan::where('id_karyawan', $request->id_karyawan)->leftJoin('jabatan', 'jabatan.id', '=','karyawan.jabatan')->first();
        
        if($request->perubahan == 'jabatan'){
            $data['ui'] = RekapJabatan::leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'rekap_jabatan.id_karyawan')
            ->selectRaw(" rekap_jabatan.* ,DATE_FORMAT(rekap_jabatan.created_at,'%Y-%m-%d') as tanggal_buat , karyawan.nama")
            ->where('rekap_jabatan.id_rekjab', $id)
            ->first();
             $data['jab'] = Jabatan::where('id',  $data['ui']->id_jabatan)->first();
            $data['user_input'] = User::where('id', $data['ui']->user_insert)->first();

        }else if($request->perubahan == 'pangkat'){
             $data['ui'] = Kenaikan::leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'kenaikan.id_karyawan')
            ->selectRaw("kenaikan.*,DATE_FORMAT(kenaikan.created_at,'%Y-%m-%d') as tanggal_buat , karyawan.nama")
             ->where('kenaikan.id_naik', $id)
            ->first();
            $data['user_input'] = User::where('id', $data['ui']->user_insert)->first();
            $data['mentor'] =  Karyawan::where('id_karyawan', $data['ui']->id_mentor)->first();
        }else if($request->perubahan == 'mutasi'){
            $data['ui'] = MutasiKaryawan::leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'mutasi_karyawan.id_karyawan')
                ->leftJoin('tambahan as baru', 'baru.id', '=', 'mutasi_karyawan.kantor_baru')
                ->leftJoin('tambahan as asal', 'asal.id', '=', 'mutasi_karyawan.kantor_asal')
                ->leftJoin('jabatan as jab_asal', 'jab_asal.id', '=', 'mutasi_karyawan.jabatan_asal')
                ->leftJoin('jabatan as jab_baru', 'jab_baru.id', '=', 'mutasi_karyawan.jabatan_baru')
                ->selectRaw("mutasi_karyawan.*,DATE_FORMAT(mutasi_karyawan.created_at,'%Y-%m-%d') as tanggal_buat , karyawan.nama,baru.unit as unit_baru,asal.unit as unit_asal,jab_asal.jabatan as jabatan_asal,jab_baru.jabatan as jabatan_baru")
                ->where('mutasi_karyawan.id_mutasi', $id)
                ->first();
            $data['user_input'] = User::where('id', $data['ui']->user_insert)->first();
            $data['mentor'] =  Karyawan::where('id_karyawan', $data['ui']->id_mutasi)->first();
        }
        else if($request->perubahan == 'keluarga'){
            $data['ui'] = RekapKeluarga::leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'rekap_keluarga.id_karyawan')
            ->selectRaw("rekap_keluarga.*,DATE_FORMAT(rekap_keluarga.created_at,'%Y-%m-%d')as tanggal_buat , karyawan.nama")
            ->where('rekap_keluarga.id_rekkel', $id)
            ->first();
            
            $data['user_input'] = User::where('id', $data['ui']->user_insert)->first();

            $data['anak'] = unserialize($data['ui']->nm_anak);
            $data['tgl_lahir_anak'] = unserialize($data['ui']->tgl_lahir_anak);
            $data['status_anak'] = unserialize($data['ui']->status_anak);
            $data['pasangan'] = unserialize($data['ui']->nm_pasangan);
            $data['tgl_lahir'] = unserialize($data['ui']->tgl_lahir);
            $data['tgl_nikah'] = unserialize($data['ui']->tgl_nikah);
            $data['id_pasangan'] = unserialize($data['ui']->id_pasangan);
            
            
            $data['anak_dulu'] = unserialize($data['karyawan']->nm_anak);
            $data['tgl_lahir_anak_dulu'] = unserialize($data['karyawan']->tgl_lahir_anak);
            $data['status_anak_dulu'] = unserialize($data['karyawan']->status_anak);
            $data['pasangan_dulu'] = unserialize($data['karyawan']->nm_pasangan);
            $data['tgl_lahir_dulu'] = unserialize($data['karyawan']->tgl_lahir);
            $data['tgl_nikah_dulu'] = unserialize($data['karyawan']->tgl_nikah);
            $data['id_pasangan_dulu'] = unserialize($data['karyawan']->id_pasangan);
            
            
           $data['data_pas_dulu'] = [];
           $data['data_anak_dulu'] = [];
           
           $data['data_pas'] = [];
           $data['data_anak'] = [];
           
           
            if(!empty($data['pasangan_dulu'])){
                for ($b = 0; $b < count($data['pasangan_dulu']); $b++){
                    $data['data_pas_dulu'][] = [
                        "id_pasangan" => !empty($data['id_pasangan_dulu'][$b]) ? $data['id_pasangan_dulu'][$b] : null,
                        "nm_pasangan" => $data['pasangan_dulu'][$b],
                        "tgl_lahir" => $data['tgl_lahir_dulu'][$b],
                        "tgl_nikah" => $data['tgl_nikah_dulu'][$b]
                    ];
                }
            }
                   
            if(!empty($data['anak_dulu'])){
                for ($a = 0; $a < count($data['anak_dulu']); $a++){
                    $data['data_anak_dulu'][] = [
                        "nm_anak" => $data['anak_dulu'][$a],
                        "tgl_lahir_anak" => $data['tgl_lahir_anak_dulu'][$a],
                        "status" => $data['status_anak_dulu'][$a]
                    ];
                }
            }
            
           
            if(!empty($data['pasangan'])){
                for ($i = 0; $i < count($data['pasangan']); $i++){
                    $data['data_pas'][] = [
                        "id_pasangan" => !empty($data['id_pasangan'][$i]) ? $data['id_pasangan'][$i] : null,
                        "nm_pasangan" => $data['pasangan'][$i],
                        "tgl_lahir" => $data['tgl_lahir'][$i],
                        "tgl_nikah" => $data['tgl_nikah'][$i]
                    ];
                }
            }
                   
            if(!empty($data['anak'])){
                for ($x = 0; $x < count($data['anak']); $x++){
                    $data['data_anak'][] = [
                        "nm_anak" => $data['anak'][$x],
                        "tgl_lahir_anak" => $data['tgl_lahir_anak'][$x],
                        "status" => $data['status_anak'][$x]
                    ];
                }
            }
       
       
        
        }
   
        return $data;
    }
   
   public function acc_perubahan(Request $request,$id)
    {
                
        if($request->aksi == 'reject'){
            if($request->perubahan == 'pangkat'){
                Kenaikan::where('id_naik', $id)->update([
                    "acc" => 0,
                    "alasan" => $request->alasan,
                    "user_approve" =>Auth::user()->id,
                ]);
            
            }else if($request->perubahan == 'jabatan'){
                RekapJabatan::where('id_rekjab', $id)->update([
                    "acc" => 0,
                    "alasan" => $request->alasan,
                    "user_approve" =>Auth::user()->id,
                ]);
                
            }else if($request->perubahan == 'jabatan'){
                RekapKeluarga::where('id_rekkel', $id)->update([
                    "acc" => 0,
                    "alasan" => $request->alasan,
                    "user_approve" =>Auth::user()->id,
                ]);
            }else if($request->perubahan == 'mutasi'){
                RekapJabatan::where('id_rekkel', $id)->update([
                    "acc" => 0,
                    "alasan" => $request->alasan,
                    "user_approve" =>Auth::user()->id,
                ]);
                
                MutasiKaryawan::where('id_mutasi', $id)->update([
                    "acc" => 0,
                    "alasan" => $request->alasan,
                    "user_approve" =>Auth::user()->id,
                ]);
            }
            
        }else  if($request->aksi == 'acc'){
             $nomor = NomorSK::where('id_com', Auth::user()->id_com)
                ->whereYear('created_at', date('Y')) // Sesuaikan dengan tahun yang diinginkan
                ->whereMonth('created_at', date('m')) // Sesuaikan dengan bulan yang diinginkan
                ->max('urut');
            if($request->perubahan == 'pangkat'){
                $data = Kenaikan::where('id_naik', $id)->first();
                $gol = Golongan::where('golongan', $data->golongan)->first();
                $request->validate([
                    'file_sk' => 'nullable|mimes:jpeg,png,jpg,pdf,mimetypes:text/plain|max:2048', // Sesuaikan dengan jenis file yang diizinkan dan ukuran maksimumnya
                ]);
            
                if ($request->hasFile('file_sk')) {
                    $file = $request->file('file_sk');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads'), $fileName);
            
                    if (file_exists(public_path($data->file_sk))) {
                        unlink(public_path($data->file_sk));
                    }
            
                    $filePath = 'uploads/' . $fileName;
                } else {
                    $filePath = 'uploads/' . $request->namafile_sk;
                }
                Karyawan::where('id_karyawan', $request->karyawan)->update([
                    'masa_kerja' => $data->masa_kerja,
                    'id_gol' => $gol->id_gol,
                    'golongan' => $data->golongan,
                    'no_rek' => $data->no_rek,
                    'tgl_mk' => $data->tgl_mk,
                    'tgl_gol' => $data->tgl_gol,
                    'file_sk' => $filePath,
                    'status_kerja' => $data->status_kerja,
                    'jkk' => $data->jjk,
                    'jkm' => $data->jkm,
                    'jht' => $data->jht,
                    'jpn' => $data->jpn,
                    'kesehatan' => $data->kesehatan,
                    'user_update' => Auth::user()->id,
                ]);
                
                
                NomorSK::where('id_com', Auth::user()->id_com)->create([
                    'urut' => $nomor == null || $nomor == '' ? 1 : $nomor + 1,
                    'id_com' => Auth::user()->id_com,
                    'user_insert' => Auth::user()->id,
                ]);
                

                $p = Karyawan::where('id_karyawan',$request->karyawan)->first();
                if($data->status_kerja == 'Magang' && $p->status_kerja == 'Magang' ){
                    Karyawan::where('id_karyawan', $request->karyawan)->update([
                        'id_mentor' => $data->id_mentor,
                        ]);
                }else if($data->status_kerja != 'Magang'){
                    Karyawan::where('id_karyawan', $request->karyawan)->update([
                        'id_mentor' => NULL,
                        ]);
                }
        
                Kenaikan::where('id_naik', $id)->update([
                    "no_sk" => $request->nomor_sk ?? null,
                    "acc" => 1,
                    "user_approve" =>Auth::user()->id,
                ]);
            
            }else if($request->perubahan == 'mutasi'){
                // dd($request);
                $data = MutasiKaryawan::where('id_mutasi', $id)->first();
                
                $request->validate([
                    'file_sk' => 'nullable|mimes:jpeg,png,jpg,pdf,mimetypes:text/plain|max:2048', // Sesuaikan dengan jenis file yang diizinkan dan ukuran maksimumnya
                ]);
            
                if($request->hasFile('file_sk')){
                    $image = $request->file('file_sk');
                    if($image->isValid()){
                        $image_name = $image->getClientOriginalName();
                        $upload_path = 'fileSK';
                        $image->move($upload_path, $image_name);
                        $fileName = $image_name;
                    }
                }else if($request->namafile_sk != ''){
                    $fileName = $request->namafile_sk;
                }
                
                MutasiKaryawan::where('id_mutasi', $id)->update([
                    "acc" => 1,
                    "file_sk" => $fileName,
                    "user_approve" =>Auth::user()->id,
                ]);
                
                
                $jab = Jabatan::where('id', $data->jabatan_baru)->first();
                $kantor = Kantor::where('id', $data->kantor_baru)->where('id_com', Auth::user()->id_com)->first();
                
                NomorSK::where('id_com', Auth::user()->id_com)->create([
                    'urut' => $nomor == null || $nomor == '' ? 1 : $nomor + 1,
                    'id_com' => Auth::user()->id_com,
                    'user_insert' => Auth::user()->id,
                ]);
                
                Karyawan::updateOrCreate([
                    'id_karyawan' => $data->id_karyawan
                    ],[
                    'id_kantor' => $data->kantor_baru,
                    'id_daerah' => $data->lokasi_baru,
                    'unit_kerja' => $kantor->unit,
                    'kantor_induk' => $kantor->kantor_induk,
                    'tgl_mutasi' => $data->tgl_mutasi,
                    'jabatan' => $data->jabatan_baru,
                    'id_spv' => $data->id_spv,
                    'pr_jabatan' => $jab->pr_jabatan == 'null' ? null :  $jab->pr_jabatan,
                    'user_update' => Auth::user()->id,
                ]);

                RekapJabatan::where('id_karyawan', $data->id_karyawan)->latest()->update([
                    "file" => $fileName ?? null,
                    "acc" => 1,
                    "user_approve" => Auth::user()->id,
                    "id_jabatan" => $data->jabatan_baru,
                ]);
            
            }else if($request->perubahan == 'jabatan'){
                 $data = RekapJabatan::where('id_rekjab', $id)->first();
                 $jab = Jabatan::where('id', $data->id_jabatan)->first();
    
                $request->validate([
                    'file_sk' => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048', 
                ]);
        
        
                if ($request->hasFile('file_sk')) {
                    $file = $request->file('file_sk');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads'), $fileName);
            
                    if (file_exists(public_path($data->file))) {
                        unlink(public_path($data->file));
                    }
            
                    $filePath = 'uploads/' . $fileName;
                } else {
                    $filePath = 'uploads/' . $request->namafile_sk;
                }
                
                NomorSK::where('id_com', Auth::user()->id_com)->create([
                    'urut' => $nomor == null || $nomor == '' ? 1 : $nomor + 1,
                    'id_com' => Auth::user()->id_com,
                    'user_insert' => Auth::user()->id,
                ]);
                
                Karyawan::where('id_karyawan', $request->karyawan)->update([
                  'jabatan' => $data->id_jabatan,
                  'pr_jabatan' => $jab->pr_jabatan == 'null' ? null :  $jab->pr_jabatan,
                  'jab_daerah' => $data->jab_daerah ,
                  'plt' => $data->plt,
                  'id_spv' => $data->id_spv,
                  'file_sk' => $filePath,
                  'user_update' => Auth::user()->id
                ]);      
                  
                RekapJabatan::where('id_rekjab', $id)->update([
                    "no_sk" => $request->nomor_sk ?? null,
                    "acc" => 1,
                    "file" => $filePath,
                    "user_approve" =>Auth::user()->id,
                ]);   
            }else if($request->perubahan == 'keluarga'){
                    $data = RekapKeluarga::where('id_rekkel', $id)->first();
                          if($data->status_nikah == 'Belum Menikah'){
                              
                            Karyawan::where('id_karyawan', $request->karyawan)->update([
                              'no_kk' => null,
                              'scan_kk' => null,
                              'nm_pasangan' =>null ,
                              'id_pasangan' => null,
                              'tgl_lahir' => null,
                              'tgl_nikah' => null,
                              'nm_anak' =>null,
                              'tgl_lahir_anak' =>null,
                              'status_anak' =>null,
                              'user_update' => Auth::user()->id
                            ]); 
                          
                        }
                        else if($data->status_nikah == 'Bercerai' || $data->status_nikah == 'Menikah'){
                            Karyawan::where('id_karyawan', $request->karyawan)->update([
                                "id_pasangan" => $data->id_pasangan,
                                "status_nikah" => $data->status_nikah,
                                "no_kk" => $data->no_kk,
                                "scan_kk" => $data->scan_kk,
                                "nm_pasangan" => $data->nm_pasangan,
                                "tgl_lahir" => $data->tgl_lahir,
                                "tgl_nikah" => $data->tgl_nikah,
                                "nm_anak" => !empty($data->nm_anak) ?$data->nm_anak : null,
                                "tgl_lahir_anak" => !empty($data->tgl_lahir_anak) ? $data->tgl_lahir_anak : null,
                                "status_anak" => !empty($data->status_anak) ? $data->status_anak : null,
                                "user_update" => Auth::user()->id
                            ]);
                            
                        }
                        
                        
                         RekapKeluarga::where('id_rekkel', $id)->update([
                            "acc" => 1,
                            "user_approve" =>Auth::user()->id,
                        ]);   
                    }
                
            }
    
        return response()->json(['success' => 'berhasil']);
    }
   
   
   public function getkantorhc(Request $request){
       
       if($request->tab = 'ss'){
            $kans = Kantor::where('id_com',$request->id_coms )->get(); 
      }else{
            $kans = Kantor::where('id_com', Auth::user()->id_com)->get(); 
        }
        return $kans;
    }
   
   
      public function getkaryawanhc(Request $request){
       if($request->tab == 'ss'){
            $karys = Karyawan::leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')->where('karyawan.id_com',$request->id_coms)->where('karyawan.aktif', 1)->get(); 
      }else if ($request->tab == 'aa'){
         $karys =  karyawan::join('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                    ->where('karyawan.aktif', 1)
                    ->select('jabatan.jabatan', 'karyawan.id_karyawans', 'karyawan.nama', 'karyawan.id_karyawan')
                    ->where('karyawan.id_com', $request->id_coms)
                    ->whereNotIn('email', function($query){
                        $query->select('email')->from('users');
                    })
                    ->get();
        }else if($request->tab == 'aa' && $request->id_coms == '' || $request->tab == 'aa' && $request->id_coms == null){
              $karys =  karyawan::join('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
              ->where('karyawan.aktif', 1)
                    ->select('jabatan.jabatan', 'karyawan.id_karyawans', 'karyawan.nama', 'karyawan.id_karyawan')
                    ->where('karyawan.id_com', Auth::user()->id_com)
                    ->whereNotIn('email', function($query){
                        $query->select('email')->from('users');
                    })
                    ->get();
        }else{
            $karys = Karyawan::leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
            ->where('karyawan.aktif', 1)->where('karyawan.id_com', Auth::user()->id_com)->get(); 
            
        }
        return $karys;
    }
    
   
   public function getjabatanhc(Request $request){
       
       if($request->tab = 'ss'){
            $jabats = Jabatan::where('id_com',$request->id_coms )->get();
      }else{
            $jabats = Jabatan::where('id_com', Auth::user()->id_com)->get();
        }
        return $jabats;
    }

  public function getakseshc(Request $request){
       if($request->tab = 'ss'){
            $akses = Akses::where('id_com',$request->id_coms )->get();
            // $kans = Kantor::where('id_com',$request->id_coms )->get(); 
      }else{
            $akses = Akses::where('id_com', Auth::user()->id_com)->get();
            // $kans = Kantor::where('id_com', Auth::user()->id_com)->get(); 
        }
        return $akses;
    }
    
    
      public function getjamhc(Request $request){
       if($request->tab = 'ss'){
            $shift  = JamKerja::select('shift')->where('id_com', $request->id_coms)->distinct()->get();
      }else{
            $shift  = JamKerja::select('shift')->where('id_com', Auth::user()->id_com)->distinct()->get();
        }
        return $shift;
    }
    
    public function getlevelhc(Request $request){
       if($request->tab = 'ss'){
          $level = Akses::where(function($query) use ($request) {
                    $query->where('id_com', $request->id_coms);
                        })
            ->distinct()->get();
      }else{
        $level = Akses::where('id_com', Auth::user()->id_com)->distinct()->get();
        }
        return $level;
    }
    
     public function itungkar(Request $request){
       if($request->tab = 'ss' && Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
        $com['hc'] = Company::where('id_com',$request->com)->first();
        $com['karnya'] = Karyawan::where('id_com',$request->com)->get(); 
        $com['jum'] = count($com['karnya']);
      }else{
        $com['hc'] = Company::where('id_com',Auth::user()->id_com)->first();
        $com['karnya'] = Karyawan::where('id_com',Auth::user()->id_com)->get(); 
        $com['jum'] = count($com['karnya']);
        }
        
        return $com;
    }
    
    
}
