<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Company;
use App\Models\Karyawan;
use App\Models\Jabatan;

use App\Models\Jalur;

use Auth;

class ProfileController extends Controller
{
    public function index(Request $request){

        $com = '';
        $anak = '';
        $comNonAktif=NULL;
        if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
            $prog = Company::selectRaw("company.* , COUNT(karyawan.id_karyawans) as jumlah")
                ->leftJoin('karyawan', function($join) {
                    $join->on('karyawan.id_com', '=', 'company.id_com')
                    ->where('karyawan.aktif', '=', '1');
                })
            // $prog = Company::
                
                ->where('company.aktif', 1)
                ->where('company.id_hc', Auth::user()->id_com)
                ->orWhere('company.id_com', Auth::user()->id_com)
                ->groupBy('company.id_com')
                ->get();
            // $prog = Profile::get();
        $comNonAktif = Company::where('aktif',0)->where('id_hc', Auth::user()->id_com)->get();
        $com = Company::where('id_com',Auth::user()->id_com)->first();
        $anak = Company::where('id_hc',$com->id_hc)->get();
        }else{
            $prog = Company::where('id_com', Auth::user()->id_com)->where('company.aktif', 1)->get();
            // $prog = Profile::get();
        }
        $karyawan = karyawan::where('karyawan.id_com', Auth::user()->id_com)->where('karyawan.aktif', 1)
            ->get();
        
        $jab = Jabatan::select('jabatan','id')
            ->where('id_com', Auth::user()->id_com)->get();
        return view('setting.profile', compact('prog', 'jab','karyawan','com','anak', 'comNonAktif'));
    }
    
    public function jab( Request $request){
        // return $request;
        $jab = Jabatan::
            // ->where('nama',$request->nama)
            where('id_com',$request->id_coms)
            ->get();
        return $jab;
    }

    public function nama(Request $request){
        $user = karyawan::join('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                    ->select('jabatan.jabatan', 'karyawan.id_karyawans', 'karyawan.nama', 'karyawan.id_karyawan', 'karyawan.id_com')
                    ->where(function($query) use ($request){
                        if($request->id_coms != '' || $request->id_coms != null ){
                            $query->where('karyawan.id_com', $request->id_coms);
                        }else{
                            $query->where('karyawan.id_com', Auth::user()->id_com);
                        };
                    })
                    ->where('karyawan.aktif', 1)
                    ->whereNotIn('email', function($query){
                        $query->select('email')->from('users');
                    })
                    ->get();
        return $user;

    }
    
    // public function update($id, Request $request)
    // {
    //     $profil = Profile::findOrFail($id);
    //     $input = $request->all();
    //     if($request->hasFile('logo')){
    //         $image = $request->file('logo');
    //         if(isset($data->logo)&&file_exists('upload/'.$data->logo)){
    //             unlink('upload/'.$data->logo);
    //         }
    
    //         if($image->isValid()){
    //             $image_name = $image->getClientOriginalName();
    //             $upload_path = 'upload';
    //             $image->move($upload_path, $image_name);
    //             $input['logo'] = $image_name;
    //         }
    //     }
        
    //     if($request->hasFile('ttd')){
    //         $image = $request->file('ttd');
    //         if(isset($data->logo)&&file_exists('upload/'.$data->ttd)){
    //             unlink('upload/'.$data->logo);
    //         }
    
    //         if($image->isValid()){
    //             $image_name = $image->getClientOriginalName();
    //             $upload_path = 'upload';
    //             $image->move($upload_path, $image_name);
    //             $input['ttd'] = $image_name;
    //         }
    //     }
        
      
      
    	
    // 	if(!$request->direktur || !$request->id_jabdir){
    //         return back();
    //     }else{
    //         $profil->update([
    //         'direktur' => $request->direktur,
    //         'id_direktur' => $request->id_direktur,
    //         'id_jabdir' => $request->id_jabdir,
    //         ]);
    //         \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Profile Perusahaan');
    //         return back();
    //     }
        
    //       $profil->update($input);
    // }
    
    public function profileamd(Request $request)
    {
  $data = Profile::where('id_com',Auth::user()->id_com)->first();
    if($request->tab == 'pimpinan'){
        if(!empty($request->fotottd)){
            $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
            $image_parts = explode(";base64,", $request->fotottd);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $image_name = $request->namafile_fotottd;
            $file = $folderPath . $image_name;
            file_put_contents($file, $image_base64);
        }
      
            Profile::where('id_com',Auth::user()->id_com)->update([
            'direktur' => $request->nama,
            'id_direktur' => $request->namaid,
            'id_jabdir' => $request->idpiljab,
            'ttd' => $request->namafile_fotottd != '' ? $request->namafile_fotottd : $data->ttd,
            ]);
            
            \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Profile Perusahaan');
               return response()->json(['success' => 'Data is successfully updated']);
            // return back();
      
    }else if($request->tab == 'ketenagakerjaan'){
        // dd($request->all());
          Profile::where('id_com',Auth::user()->id_com)->update([
            'jkk' => $request->jkk,
            'jkm' => $request->jkm,
            'jht' => $request->jht,
            'jpn' => $request->jpn,
            'kesehatan' => $request->kesehatan,
            ]);
        
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Profile Perusahaan');
        return response()->json(['success' => 'Data is successfully updated']);
        
    }else if($request->tab == 'informasi'){
        // dd(Auth::user()->id_com);
        // dd($request->all());
          if(!empty($request->base64)){
            $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
            $image_parts = explode(";base64,", $request->base64);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $image_name = $request->nama_file;
            $file = $folderPath . $image_name;
            file_put_contents($file, $image_base64);
        }
        
          Company::where('id_com',Auth::user()->id_com)->update([
            'name' => $request->name1,
            'alamat' => $request->alamat1,
            'alias' => $request->alias1,
            'sk' => $request->sk1,
            'npwp' => $request->npwp1,
            'sms' => $request->sms1,
            'wa' => $request->wa1,
            'email' => $request->email1,
            'web' => $request->web1,
            'berdiri' => $request->berdiri1,
            'akses' => $request->akses1,
            'jenis' => $request->jenis2,
            'level_hc' => Auth::user()->level_hc,
            'logo' => $request->nama_file2 != '' ? $request->nama_file : $data->logo,
            ]);
        
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Profile Perusahaan');
        return response()->json(['success' => 'Data is successfully updated']);
    }  
        
}
    
    
    public function get_jalur_lokdon(Request $request){
        $data = Jalur::select('*')
            ->where(function($q) use ($request){
                $q->whereIn('id_kantor', $request->unit);
            })
            ->orderByRaw("concat(id_kantor, ' ', nama_jalur) ASC")
            ->get();
        return $data;
    }

    public function ubah(Request $request)
    {
        $id = $request->id_hide;
        $jjk = $request->jkk;
        $jkm = $request->jkm;
        $jht = $request->jht;
        $jpn = $request->jpn;
        $gg = Profile::where('id_com',$id)->update([
            'jkk' => $jjk,
            'jkm' => $jkm,
            'jht' => $jht,
            'jpn' => $jpn,
            'kesehatan' => $request->kesehatan
        ]);
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Keikutsertaan BPJS Perusahaan');
        return response()->json(['success' => 'Data is successfully updated']);
        // return back();
    }
    
    public function comby(Request $request){
        $kompeni = Company::where('id_com', $request->com)
        ->first();
    
        return $kompeni;
    }
    
    
     public function updatehc( Request $request)
    {

    $data = Profile::where('id_com',$request->id)->first();
    if($request->tab == 'pimpinan'){
        if(!empty($request->fotottd)){
            $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
            $image_parts = explode(";base64,", $request->fotottd);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $image_name = $request->namafile_fotottd;
            $file = $folderPath . $image_name;
            file_put_contents($file, $image_base64);
        }
      
            Profile::where('id_com',$request->id)->update([
            'direktur' => $request->nama,
            'id_direktur' => $request->namaid,
            'id_jabdir' => $request->piljab,
            'ttd' => $request->namafile_fotottd,
            ]);
            
            \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Profile Perusahaan');
               return response()->json(['success' => 'Data is successfully updated']);
            // return back();
      
    }else if($request->tab == 'ketenagakerjaan'){
        
          Profile::where('id_com',$request->id)->update([
            'jkk' => $request->jkk,
            'jkm' => $request->jkm,
            'jht' => $request->jht,
            'jpn' => $request->jpn,
            'kesehatan' => $request->kesehatan,
            ]);
        
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Profile Perusahaan');
        return response()->json(['success' => 'Data is successfully updated']);
        
    }else if($request->tab == 'informasi'){
          if(!empty($request->base64)){
            $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
            $image_parts = explode(";base64,", $request->base64);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $image_name = $request->nama_file;
            $file = $folderPath . $image_name;
            file_put_contents($file, $image_base64);
        }
        
          Profile::where('id_com',$request->id)->update([
            'name' => $request->name1,
            'alamat' => $request->alamat1,
            'alias' => $request->alias1,
            'sk' => $request->sk1,
            'npwp' => $request->npwp1,
            'sms' => $request->sms1,
            'wa' => $request->wa1,
            'email' => $request->email1,
            'web' => $request->web1,
            'berdiri' => $request->berdiri1,
            'akses' => $request->akses1,
            'jenis' => $request->jenis2,
            'logo' => $request->nama_file != '' ? $request->nama_file : $data->logo,
            ]);
        
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Profile Perusahaan');
        return response()->json(['success' => 'Data is successfully updated']);
    }  
        //   $profil->update($input);
    }
    
       public function createperus()
   {
     
       $pass = Karyawan::where('aktif', 1)->where('id_com',Auth::user()->id_com)->get();
       $company = Profile::where(function($query) {
                        if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                            $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })
                    ->get();
       return view ('setting.create_perusahaan',compact( 'pass','company'));
   }
    
    
        public function tambahcom(Request $request)
    {
        // dd($request->all());
            $count = Company::where('id_hc', Auth::user()->id_com)->get()->count();
            $company = Company::where('id_com', Auth::user()->id_com)->first();
            if($count >= $company->limit_com){
                return response()->json(['errors' => 'Limit company anda sudah habis, silahkan hubungi tim developer!']);
            }
            // dd($company->limit_com);
      if(!empty($request->base64)){
            $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
            $image_parts = explode(";base64,", $request->base64);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $image_name = $request->nama_file;
            $file = $folderPath . $image_name;
            file_put_contents($file, $image_base64);
        }
      
        $data = new Company;
        $data->id_hc =  Auth::user()->id_com;
        $data->level_hc = 0;
        $data->name = $request->nama;
        $data->alias = $request->alias;
        $data->sk = $request->sk;
        $data->npwp = $request->npwp;
        $data->sms = $request->sms;
        $data->wa = $request->wa;
        $data->email = $request->email;
        $data->web = $request->web;
        $data->berdiri = $request->berdiri;
        $data->alamat = $request->alamat;
        $data->akses = $request->akses;
        $data->jenis = $request->jenis;
        $data->logo = $request->nama_file;
        $data->jkk = $request->jkk;
        $data->jkm = $request->jkm;
        $data->jht = $request->jht;
        $data->jpn = $request->jpn;
        $data->kesehatan = $request->kesehatan;
        $data->aktif = 0;
        $data->limit_user = 10;
        $data->user_insert = Auth::user()->id;
        $data->save(); 
        return response()->json(['success' => 'Data Added successfully.']);
       
    }
    
      public function itungcom()
   {
        $com['hc'] = Company::where('id_com',Auth::user()->id_com)->first();
        $anak = Company::where('id_hc',$com['hc']->id_hc)->get();
        $com['jum'] = count($anak);
        
        return($com);
   }
    
}