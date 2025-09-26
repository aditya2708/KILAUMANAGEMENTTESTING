<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Userkolektor;
use App\Models\User;
use App\Models\Profile;
use App\Models\Company;
use App\Models\UserKolek;
use App\Models\Akses;
use App\Models\Menu;
use App\Models\Kolektors;
use Auth;
use App\Models\Karyawan;
use App\Models\UserSSO;
use App\Models\Tunjangan;
use App\Models\JamKerja;
use App\Models\Jabatan;
use App\Models\Kantor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Str;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use DataTables;
use DB;
use App\Models\LogActivity as LogActivityModel;
use Illuminate\Support\Facades\Http; 

class AuthController extends Controller
{

    use AuthenticatesUsers;
    
    // public function __construct()
    // {
    //     if(Auth::check() && Auth::user()->role_id == 1){
    //         $this->redirectTo = route('admin.dashboard');
    //     } elseif(Auth::check() && Auth::user()->role_id == 2){
    //         $this->redirectTo = route('user.dashboard');
    //     }
       
    //     $this->middleware('guest')->except('logout');
    // }
    
    public function companyLayout(Request $request){
        $company = Profile::
                    where(function($query) {
                        if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                            $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })
                    ->
                    get();
        return response()->json($company);
    }
    
    public function masuk(Request $request){
        
        // return $request;
        $aha = User::where('api_token', $request->token)->where('email', $request->email);
        // return $aha->first();
        if($aha->exists()){
            // if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password, 'aktif' => 1])) {
            $user = $aha->where('aktif', 1)->first();
            if($user && Auth::guard('user')->loginUsingId($user->id)){
                if(Auth::guard('user')->user()->level == null && Auth::guard('user')->user()->pengaturan == null && Auth::guard('user')->user()->kepegawaian == null && Auth::guard('user')->user()->keuangan == null && Auth::guard('user')->user()->kolekting == null ){
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak punya akses di web ini!'
                    // ], 401);
                    ]);
                    
                }else{
                    \LogActivity::addToLogsLogin(Auth::guard('user')->user()->name.' Telah Logging', Auth::guard('user')->user()->id, Auth::guard('user')->user()->kantor_induk, Auth::guard('user')->user()->id_kantor);
                    
                    return response()->json([
                        'success' => true,
                        'id_com' => Auth::guard('user')->user()->id_com,
                        'url' => Auth::guard('user')->user()->spesial == 0 ? url(Auth::guard('user')->user()->perus.'/dashboard') : url('berbagi-teknologi/home'),
                        'message' => 'sukses'
                    // ], 200);
                    ]);
                }
            // }elseif (Auth::guard('kolektor')->attempt($request->only('email','password'))) {
            //     return response()->json([
            //         'success' => true,
            //         'message' => 'gatau'
            //     ], 200);
            }
            
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak mempunyai AKUN !'
            // ], 401);
            ]);
        }
        
        // if(Auth::loginUsingId(1))
        
    }

    public function create(){
        return view('formlogin.register');
    }
    
    public function daftar_perusahaan(){
        return view('auth.regis_perusahaan');
    }
    
    public function sidebar(Request $request){
        $kkk = DB::table('users')->where('id_com', 1)->get();
        if($request->ajax()){
            $data = DB::table('sidebar');
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('par', function($data){
                $y = DB::table('sidebar')->where('id', $data->id_parent)->first();
                if($y == null){
                    $button = '';
                }else{
                    $button = $y->menu;    
                }
                
                return $button;
            })
            
            ->addColumn('user', function($data){
               if($data->id_user != null){
                   $sus = unserialize($data->id_user);
                   $y = DB::table('users')->whereIn('id', $sus)->get();
                    if($y == null){
                        $button = '';
                    }else{
                        // return($y);
                        $button = [];
                        foreach($y as $b){
                            $button[] = [ $b->name ];
                        }
                    }
               }else{
                   $button = '';
               }
                   
                
                
                return $button;
            })
                    
            ->rawColumns(['par','user'])
            ->make(true);
        }
        return view('auth.menu', compact('kkk'));
    }
    
    public function sidebarid($id){
        $data = DB::table('sidebar')->where('id', $id)->first();
        
        return $data;
    }
    
    public function giveakses(Request $request){
        $akses = serialize($request->akses);
        DB::table('sidebar')->where('id', $request->id_hide)->update([
            'id_user' => $akses
        ]);
        return response()->json(['success' => 'Data is successfully updated']);
        
    }
    
    public function daftar_post(Request $request){
        $db = DB::table('company')->where('email', $request->email)->first();
        
        if($db == null){
            // return (string)$request->nowa;
            $linkAktivasi = 'https://kilauindonesia.org/berbagi-teknologi/company';
            $pesan = "Assalamualaikum Berbagi Teknologi 🙏\n\n"
                . "Ada perusahaan baru dengan identitas:\n"
                . "📌 Nama Perusahaan : $request->perus\n"
                . "📞 Nomor Whatsapp  : $request->nowa\n"
                . "📧 Email           : $request->email\n\n"
                . "Ayo aktivasi sekarang! Klik link berikut untuk aktivasi perusahaan:\n"
                . "$linkAktivasi\n\n"
                . "Wassalamualaikum.";
    
                $nomorAdminBT = '085117563869';
            
            $response = Http::withHeaders([
            'Authorization' => 'KFxveAKzfq3pu5XyXXbQ' // Ganti dengan token asli dari Fonnte
            ])->asForm()->post('https://api.fonnte.com/send', [
                    'target' => $nomorAdminBT,    // Nomor WA tujuan, contoh: 08123456789
                    'message' => $pesan,          // Isi pesan
                    'countryCode' => '62',        // Optional, default 62
                ]);
            
            $autoAktif = Company::where('id_com', 4)->first();
            
            if($autoAktif->auto_aktivasi){
                $token = Str::random(60);
                $pass = bcrypt(12345678);
                $name = 'Management';
            
                $company = Company::create([
                    'id_hc'  => null,
                    'level_hc' => 1,
                    'id_jp' => $request->jp,
                    'alias' => $request->alias,
                    'jenis' => $request->jenis_perusahaan,
                    'name' => $request->perus,
                    'wa' => $request->input('nowa'),
                    'email' =>$request->email,
                    'jumlah_karyawan' =>$request->jumkar ?? 10,
                    'limit_user' => 10,
                    'aktif' => 1,
                    'user_insert' => $request->nama ?? null,
                    'baca' => 1,
                ]);

                
                
                User::create([
                    'name' => $name,
                    'email' => $request->email,
                    'password' => $pass,
                    'kepegawaian' => 'admin',
                    'presensi' => 'admin',
                    'pengaturan' => 'admin',
                    'api_token' => $token,
                    'aktif' => 1,
                    'level_hc' => 1,
                    'id_com' => $company->id_com//ambil dari Company yang baru saja di buat
                ]);
    
                UserSSO::create([
                    'nama' => $name,
                    'email' => $request->email,
                    'password' => $pass,
                    'token' => $token,
                ]);
                
              
                $no_wa = (string) $request->input('nowa');
                $password_plain = '12345678';
                $pesan = "Assalamualaikum 🙏\n\n"
                . "Perusahaan Anda sudah diaktivasi. Silakan login dengan akun:\n\n"
                . "📧 Email    : $request->email\n"
                . "🔐 Password : $password_plain\n\n"
                . "\n\n"
                . "Untuk tutorial penggunaan tonton vidio dari link dibawah ini:  \n\n"
                . "- \n\n"
                . "Dihubungi oleh: Berbagi Teknologi \n\n"
                . "Support: wa.me/+6285117563869 \n\n"
                . "Wassalamualaikum.";
        
                $response = Http::withHeaders([
                'Authorization' => 'KFxveAKzfq3pu5XyXXbQ' // Ganti dengan token asli dari Fonnte
                ])->asForm()->post('https://api.fonnte.com/send', [
                    'target' => $no_wa,           // Nomor WA tujuan, contoh: 08123456789
                    'message' => $pesan,          // Isi pesan
                    'countryCode' => '62',        // Optional, default 62
                ]);
            }else{
                Company::create([
                    'id_hc'  => null,
                    'level_hc' => 1,
                    'id_jp' => $request->jp,
                    'alias' => $request->alias,
                    'jenis' => $request->jenis_perusahaan,
                    'name' => $request->perus,
                    'wa' => $request->input('nowa'),
                    'email' =>$request->email,
                    'jumlah_karyawan' =>$request->jumkar ?? 10,
                    'limit_user' => 10,
                    'aktif' => 0,
                    'user_insert' => $request->nama ?? null,
                    'baca' => 1,
        
                ]);
                \LogActivity::addToLog($request->nama.' Menambahkan Data Perusahaan '.$request->perus);
               
            }
            if ($response->successful()) {
                 return response()->json(["message"=>"Berhasil regis perushaan, Akun akan dikirimkan melalui whatsapp!", 'status' => 200]); // atau return json jika ingin parsing: $response->json()
            } else {
                return response()->json(['errors' => true, 'message' => 'Pastikan form diisi dengan benar!']); // atau return ['error' => true, 'message' => 'Gagal kirim WA']
            }
           
        }else{
            return response()->json(["errors" => "Sepertinya email anda sudah terdaftar!"]);
        }
        
    }

    public function buat(Request $request){
        // dd($request->all());
        \LogActivity::addToLog(Auth::user()->name.' Menambahkan Data User '.$request->nama);
        Userkolektor::create([
            'nama' => $request->nama,
            'level'  =>$request->level,
            'email' =>$request->email,
            'kota' =>$request->kota,
            'no_hp' =>$request->no_hp,
            'alamat' =>$request->alamat,
            'username' =>$request->username,
            'password'=>bcrypt($request->password),
            'remember_token'=>  Str::random(40),

        ]);
        return redirect('dashboard');
    }

    public function logout ()
    {
        // // dd('jh');
        if (Auth::guard('user')->check()){
            \LogActivity::addToLogsLogout(Auth::guard('user')->user()->name.' Telah Logout', Auth::guard('user')->user()->id, Auth::guard('user')->user()->kantor_induk, Auth::guard('user')->user()->id_kantor);
            Auth::guard('user')->logout();
        }elseif (Auth::guard('kolektor')->check()){
            Auth::guard('kolektor')->logout();
        }
        return redirect('https://home.kilauindonesia.org/');
    }
    
    public function store(Request $request){

        $input = $request->all();
        $name = $request->name;
        $com = Company::where('id_com', Auth::user()->id_com)->first();
        $user = User::where('id_com', Auth::user()->id_com)->get();
        if($user->count() > $com->limit_user){
            return response()->json(['errors' => 'Limit user anda sudah habis, silahkan hubungi penyedia untuk upgrade!']);
        }
        // return 'hehe';
        // $cari = Karyawan::where('id_karyawan', $request->name)->where('id_com', Auth::user()->id_com)->first();
        // return($cari);
    
        unset($input['namekar']);
        $input['kotug']    = $request->kota;
        $input['skema_gaji']    = Auth::user()->skema_gaji == 0 ? 1 : Auth::user()->skema_gaji;
        $input['id_com']    = Auth::user()->id_com;
        $input['password']  = bcrypt($request->password);
        $input['api_token'] = Str::random(60);
        
        $input['minimal']    = $request->minimal != '' ? preg_replace("/[^0-9]/", "", $request->minimal) : null;
        $input['target']    = $request->target != '' ? preg_replace("/[^0-9]/", "", $request->target) : null;
        $input['honor']     = $request->honor != '' ? preg_replace("/[^0-9]/", "", $request->honor) : null;
        $input['bonus']     = $request->bonus != '' ? preg_replace("/[^0-9]/", "", $request->bonus) : null;
        
        $input['diluar'] = $request->jenis == 'lapangan' ? 1 : 0; 
        
        User::create($input);
        
        $cek = UserSSO::where('email', $request->email)->first();
        
        if($cek == null){
            $user = new UserSSO();
            $user->nama = $request->name;
            $user->email = $request->email;
            $user->password = $input['password'];
            $user->token = $input['api_token'];
            $user->save();
        }
        
        
        \LogActivity::addToLog(Auth::user()->name.' Membuat Data User '.$name);
        return redirect('management-user')->with('success', 'Data user untuk '.$name.' berhasil dibuat');
    }
    public function storeUserPerusahaan(Request $request){

    // return $request->all();
    
        $com = Company::whereIn('id_com', $request->id)->get();
        // return $com;
        foreach ($com as $val){
            // return $val->email;
            $token = Str::random(60);
            $pass = bcrypt(12345678);
            $name = 'Management';
            
            Company::whereIn('id_com', $request->id)->update(['aktif' => 1]);
            User::create([
              'name' => $name,
              'email' => $val->email,
              'password' => $pass,
              'kepegawaian' => 'admin',
              'presensi' =>  'admin',
              'api_token' =>  $token,
              'aktif' => 1,
              'id_com' => $val->id_com
              ]);
            UserSSO::create([
              'nama' => $name,
              'email' => $val->email,
              'password' => $pass,
              'token' =>  $token,
              ]);
        }
          // 🔽 Kirim WA otomatis via Fonnte
            $no_wa = (string) $val->wa; // Pastikan ini ada di tabel Company
            $password_plain = '12345678'; // asumsinya ini password default yang dipakai
            
            $pesan = "Assalamualaikum 🙏\n\n"
                . "Perusahaan Anda sudah diaktivasi. Silakan login dengan akun:\n\n"
                . "📧 Email    : $request->email\n"
                . "🔐 Password : $password_plain\n\n"
                . "\n\n"
                . "Untuk tutorial penggunaan tonton vidio dari link dibawah ini:  \n\n"
                . "https://youtu.be/93N3xpz2Lq8?si=1yMYvj-yFp8m_qTT  \n\n"
                . "\n\n"
                . "Dihubungi oleh: Berbagi Teknologi \n\n"
                . "Wassalamualaikum.";

                $response = Http::withHeaders([
                'Authorization' => 'KFxveAKzfq3pu5XyXXbQ' // Ganti dengan token asli dari Fonnte
                ])->asForm()->post('https://api.fonnte.com/send', [
                    'target' => $no_wa,           // Nomor WA tujuan, contoh: 08123456789
                    'message' => $pesan,          // Isi pesan
                    'countryCode' => '62',        // Optional, default 62
                ]);
            
        
        // \LogActivity::addToLog(' Membuat Data User '.$name);
        return response()->json([
            'message' => 'Berhasil aktivasi perusahaan!, Akun akan dikirimkan ke wa yang anda daftarkan!',
            'status' => 200
            ]);
    }

    public function buatkan(){
        return view('setting.create');
    }
    
    public function akses(Request $request){
        $company = Company::
            where(function($query) {
                        if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1){
                            $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })
                    ->
                    get();
                    
        $kot = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', $kot)->first();
        
        if(Auth::user()->level == 'admin' || Auth::user()->level == null){
            $unit = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else if(Auth::user()->level == 'kacab'){
            if($k == null){
                $unit = Kantor::where('id', $kot)->where('id_com', Auth::user()->id_com)->get();
            }else{
                $unit = Kantor::whereRaw("(id = '$kot' OR id = '$k->id')")->where('id_com', Auth::user()->id_com)->get();
            }
        }
                    
        $id = Auth::user()->id_com;
        // $level = DB::table('akses')->where('id_com', Auth::user()->id_com)->get();
        $level = Akses::where(function($query) use ($id) {
                            if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                $query->where('id_com', $id);
                            }else{
                                $query->where('id_com', Auth::user()->id_com);
                            }
                        })
                    ->distinct()->get();
                    
        $data = User::where('id_com', Auth::user()->id_com)->get();
        $shift  = JamKerja::select('shift')->where('id_com', Auth::user()->id_com)->distinct()->get();
        $karyawan = karyawan::join('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                    ->select('jabatan.jabatan', 'karyawan.id_karyawans', 'karyawan.nama', 'karyawan.id_karyawan')
                    ->where(function($query) use ($request){
                        if($request->id_coms != '' || $request->id_coms != null ){
                            $query->where('karyawan.id_com', $request->id_coms);
                        }else{
                            $query->where('karyawan.id_com', Auth::user()->id_com);
                        };
                    })
                    // ->orf
                    ->where('karyawan.aktif', 1)
                    ->whereNotIn('email', function($query){
                        $query->select('email')->from('users');
                    })
                    ->get();
                    
        if($request->ajax())
        {
            $id_com = $request->com;
            if ($request->tgl != '') {
                $tgl = explode(' s.d. ', $request->tgl);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            
            // $now = date('Y-m-d');
            // $tgls = $request->daterange != '' ? "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'" : "DATE(transaksi.tanggal) >= '$now' AND DATE(transaksi.tanggal) <= '$now'";
            
            $data = DB::table('users')
                    ->where(function($query) use ($id_com){
                                
                        if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                            if($id_com > 0){
                                $query->where('id_com', $id_com);
                            }else if($id_com == '0'){
                                // $query->
                                // whereIn('id_com', function($q) {
                                //     $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                // })
                                // ;
                            }else{
                                $query->where('id_com', Auth::user()->id_com);
                            }
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                                    
                        }
                    })
                    
                    ->where(function($q) use ($request){
                       if(isset($request->stts)){
                           $q->whereIn('aktif', $request->stts);
                       } 
                    })
                    
                    ->where(function($q) use ($request){
                       if(isset($request->unit)){
                           $q->whereIn('id_kantor', $request->unit);
                       } 
                    })
                    
                    ->where(function($q) use ($request){
                       if(isset($request->tgl)){
                            $tgl = explode(' s.d. ', $request->tgl);
                            $dari = date('Y-m-d', strtotime($tgl[0]));
                            $sampai = date('Y-m-d', strtotime($tgl[1]));
                           
                            $q->whereRaw("DATE(users.created_at) >= '$dari' AND DATE(users.created_at) <= '$sampai'");
                       } 
                    });
            
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('nama', function($data){
                        // $tj = '<a href="#" data-bs-toggle="modal" id="'.$data->id_karyawan.'" data-bs-target="#edkar" style="color:#1f5daa;" class="edit_kar">'.$data->name.'</a>';
                        // return $tj;
                    })
                    ->addColumn('edit', function($data){
                        $button2 = '<button id="'.$data->id_karyawan.'" data-bs-toggle="modal" data-bs-target="#exampleModal" class="update btn btn-success btn-sm"><i class="fa fa-edit"></i></button>';
                        return $button2;
                    })
                    ->addColumn('akses', function($data){
                        if($data->aktif == 1){
                            $button2 = '<button type="button" aktif="'.$data->aktif.'" id="'.$data->id.'" class="veriv btn btn-warning btn-sm">Non-Aktifkan</button>';
                        }else{
                            $button2 = '<button type="button" aktif="'.$data->aktif.'"  id="'.$data->id.'" class="veriv btn btn-info btn-sm">Aktifkan</button>';
                        }
                        return $button2;
                    })
                    
                    ->editColumn('wow', function($data){
                if ($data->aktif == 1){
                            $c = 'checked';
                        }else{
                            $c = '';
                        } 
                                // $button = '<input type="checkbox" id="toggle-two"  class="toggle-class " data-id="'. $kerja->id .'" data-toggle="toggle" data-style="slow" data-on="Onsite" data-off="Off"  >';
                        $button = '<label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'))" id="checkbox" class="toggle-class" status="'.$data->aktif.'" data-id="'. $data->id . '"  data-value="'. $data->aktif . '" type="checkbox" '.( $data->aktif  == 1 ? "checked" : "").' /> <div class="slider round"> </div> </label>';
                        return $button;
            })
                    
                    ->addColumn('hapus', function($data){
                        $button = ' <div class="btn-group">
                                        <button type="button" id="'.$data->id.'" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </div>';
                        return $button;
                    })
                  ->addColumn('changeAccount', function ($data) {
                        $button = '<div class="btn-group">';
                        if ($data->level != null || $data->aktif == 1) {
                            $button .= '<button type="button" id="'.$data->id.'" class="changeAccount btn btn-info btn-sm">';
                        } else {
                            $button .= '<button type="button" id="'.$data->id.'" class="changeAccount btn btn-info btn-sm disabled">';
                        }
                    
                        $button .= '<i class="fa fa-sync"></i></button>';
                        $button .= '</div>';
                        
                        return $button;
                    })

                    ->rawColumns(['edit','akses', 'hapus', 'nama','wow','changeAccount'])
                    ->make(true);
        }
        return view('setting.management_user',compact('data','karyawan', 'shift', 'level', 'company','unit'));
    }
    
    public function changeAccount(Request $request){
        if(!empty($request->id) || $request->id != ''){
            $data = User::where('id', $request->id)->where('id_com', Auth::user()->id_com)->first();
            if (Auth::guard('user')->check()){
                \LogActivity::addToLogs(Auth::guard('user')->user()->name.' Telah Logout', Auth::guard('user')->user()->id, Auth::guard('user')->user()->kantor_induk, Auth::guard('user')->user()->id_kantor);
                Auth::guard('user')->logout();
            }elseif (Auth::guard('kolektor')->check()){
                Auth::guard('kolektor')->logout();
            }
            if ($data) {
                // dd($data);
                Auth::guard('user')->login($data);
                return 
                response()->json([
                    'status' => 'success',
                    'message' => 'Berhasil',
                    'url' => 'https://kilauindonesia.org/kilau/dashboard']);
            } 
        }else {
            $previousUser = Auth::user();
            Auth::guard('user')->login($previousUser);
            return 
            response()->json([
                'status' => 'failed',
                'message' => 'Data not found',
                'url' => 'https://kilauindonesia.org/kilau/management-user'
                ]);
        }
    }
    
    public function edkar($id){
        if(request()->ajax())
        {
            // $data = User::where('id_karyawan', $id)->where('id_com', Auth::user()->id_com)->first();
            $data = User::where('id_karyawan', $id)->first();

            return response()->json(['result' => $data]);
        }
    }
    
    public function namekaryawan(Request $request){
        $id_com = $request->com;
        
        $data = karyawan::join('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                    ->select('jabatan.jabatan', 'karyawan.id_karyawans', 'karyawan.nama', 'karyawan.id_karyawan')
                    ->where(function($query) use ($id_com){ 
                        if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                            if($id_com > 0){
                                $query->where('id_com', $id_com);
                            }else if($id_com == '0'){
                                $query->whereIn('id_com', function($q) {
                                    $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                });
                            }else{
                                $query->where('id_com', Auth::user()->id_com);
                            }
                        }else{
                            $query->where('karyawan.id_com', Auth::user()->id_com);
                        }
                    })
                    ->whereNotIn('email', function($query){
                        $query->select('email')->from('users');
                    })
                    ->where('aktif', 1)
                    ->get();
            return $data;
    }
    
    public function getkarid($id){
        if(request()->ajax())
        {   
            $get = karyawan::where('id_karyawan',$id)->where('id_com', Auth::user()->id_com)->first();
            
            $data = [
                'kar' => $get,
                'unit' => Kantor::get(),
                'jabat' => Jabatan::get(),
                'cek' => karyawan::where('id_karyawan',$id)->count(),
            ];
            return response()->json(['result' => $data]);
        }
    }
    
    public function geteditkar($id){
        if(request()->ajax())
        {   
            $get = User::where('id',$id)->where('id_com', Auth::user()->id_com)->first();
            
            $data = [
                'coba' => User::get(),
                'useee' => $get,
                'kyn' => \DB::select("SELECT * from tambahan "),
                'jabatan' => \DB::select("SELECT * from jabatan "),
                'level' => \DB::select("SELECT * from akses "),
            ];
            return response()->json(['result' => $data]);
        }
    }
    
    
    public function patch(Request $request){
    
    $input = $request->except(['pass', 'pwlama', 'pwbaru', 'konpwbaru', 'hidden_id1','hidden_id_com']);
    if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
        $user = User::where('id_karyawan',$request->hidden_id1)->where('id_com', $request->hidden_id_com)->first();
    }else{
        $user = User::where('id_karyawan',$request->hidden_id1)->where('id_com', Auth::user()->id_com)->first();
    }
        
    $pass = $user->password;

    // dd($request->pass);
    if($request->pass == 'ganti'){
        if(\Hash::check($request->pwlama, $pass))
        {
            if($request->pwbaru == $request->konpwbaru){
                $input['password']  = \Hash::make($request->pwbaru);
            }else{
            // return back()->with('error','Data gagal disimpan, Konfirmasi password tidak sama dengan password baru');
            return response()->json(['error' => 'Data gagal disimpan, Konfirmasi password tidak sama dengan password baru']);
            }
        }else{
            return response()->json(['error' => 'Data gagal disimpan, Password lama yang anda masukan salah']);
        }
    }elseif($request->pass == 'reset') {
        $input['password']  = \Hash::make('12345678');
    }
  
    
    $input['minimal']    = $request->minimal != '' ? preg_replace("/[^0-9]/", "", $request->minimal) : null;
    $input['target']    = $request->target != '' ? preg_replace("/[^0-9]/", "", $request->target) : null;
    $input['honor']     = $request->honor != '' ? preg_replace("/[^0-9]/", "", $request->honor) : null;
    $input['bonus']     = $request->bonus != '' ? preg_replace("/[^0-9]/", "", $request->bonus) : null;
    $input['diluar'] = $request->jenis == 'lapangan' ? 1 : 0; 
    // $input['shift'] = $request->shift;
    // $request->except('credit_card');
    User::where('id_karyawan', $request->hidden_id1)->update($input);
    
    if($request->pass == 'tetap'){
        // dd($name);
        // return back()->with('success', 'Data '.$name.' berhasil diupdate');
        return response()->json(['success' => 'Data is successfully updated']);
    }else{
        if(Auth::user()->id_karyawan == $request->hidden_id1){
            Session::flush();
            return redirect('/')->with('success', 'Data dan Password berhasil diupdate. Silahkan login dengan password baru');
        } else {
            // return back()->with('success', 'Data dan Password '.$name.' berhasil diupdate');
            return response()->json(['success' => 'Data is successfully updated']);
        }
    }
}

    public function destroy($id){
      $user = User::findOrFail($id);
      \LogActivity::addToLog(Auth::user()->name.' Menghapus Data User '.$user->name);
      $user->delete();
    //   return back();
    return response()->json(['success' => 'Data is successfully updated']);
  }
  
    public function offuser($id){
    $data = User::where('id',$id)->where('id_com', Auth::user()->id_com)->first();

    $aktif = $data->aktif;

    if($aktif == 1){
        \LogActivity::addToLog(Auth::user()->name.' Menonaktifkan Data User '.$data->name);
        User::where('id',$id)->update([
            'aktif'=> 0
            
        ]);
    }else{
         \LogActivity::addToLog(Auth::user()->name.' Mengaktifkan Data User '.$data->name);
        User::where('id',$id)->update([
            'aktif'=> 1
        ]);
    }
  
    // return back();
    return response()->json(['success' => 'Data is successfully updated']);
}

    public function changeaktifakses(Request $request){
    $data = User::where('id',$request->id)->where('id_com', Auth::user()->id_com)->first();

    $aktif = $data->aktif;

    if($aktif == 1){
        \LogActivity::addToLog(Auth::user()->name.' Menonaktifkan Data User '.$data->name);
        User::where('id',$request->id)->update([
            'aktif'=> 0
            
        ]);
    }else{
         \LogActivity::addToLog(Auth::user()->name.' Mengaktifkan Data User '.$data->name);
        User::where('id',$request->id)->update([
            'aktif'=> 1
        ]);
    }
  
    // return back();
    return response()->json(['success' => 'Data is successfully updated']);
}
   
    function getkar(Request $request){
             $id = $request->name;
             $kar = Karyawan::where('id_karyawan', $id)->where('id_com', Auth::user()->id_com)->get();
        return response()->json($kar);
    }
    
    function getjab(Request $request){
             $id = $request->id_jab;
             $jab = Jabatan::where('id', '=', $id)->where('id_com', Auth::user()->id_com)->get();
        return response()->json($jab);
    }
    
    function getkan(Request $request){
             $id = $request->id_kan;
             $kan = Kantor::where('id', '=', $id)->where('id_com', Auth::user()->id_com)->get();
        return response()->json($kan);
    }
    
    
    
    public function upkar(Request $request) {
        // dd('sad');
        $jab = Jabatan::where('id',$request->id_jabatan)->where('id_com', Auth::user()->id_com)->first();
        $pr_jab = $jab->pr_jabatan;
        
        $kan = Kantor::where('id',$request->id_kantor)->where('id_com', Auth::user()->id_com)->first();
        $kota = $kan->unit;
        $kan_induk = $kan->kantor_induk;
        
        $form_data = array(
            'nama'=>$request->name,
            'email'=>$request->email,
            'jabatan'=>$request->id_jabatan,
            'pr_jabatan'=>$pr_jab,
            'id_kantor'=>$request->id_kantor,
            'unit_kerja'=>$kota,
            'kantor_induk'=>$kan_induk,
        );
         \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data User '.$request->name);
        Karyawan::where('id_karyawan',$request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
        
        
        // $kar = Karyawan::where('id_karyawan', '=', $id)->update([
        //     'nama'=>$request->name,
        //     'email'=>$request->email,
        //     'jabatan'=>$request->id_jabatan,
        //     'pr_jabatan'=>$pr_jab,
        //     'id_kantor'=>$request->id_kantor,
        //     'unit_kerja'=>$kota,
        //     'kantor_induk'=>$kan_induk,
        //     ]);
        // return redirect('akses');
    }
    
    public function getspv(Request $request){
        $tj = Tunjangan::where('id_com', Auth::user()->id_com)->first();
        $jbt = Jabatan::where('id',$request->id)->where('id_com', Auth::user()->id_com)->first();
        // dd($data);
        $data = [];
        if(!empty($request->id)){
            if($request->id == $tj->kolektor || $request->id == $tj->so || $request->id == $tj->sokotak){
                $data = Karyawan::where('jabatan', $jbt->pr_jabatan)->get();
            }else{
                $data = [];
            }
        }
        return response()->json($data);
    }
    
    public function getmentor(Request $request){
        $tj = Tunjangan::where('id_com', Auth::user()->id_com)->first();
        $jbt = Jabatan::where('id',$request->id)->where('id_com', Auth::user()->id_com)->first();
        // dd($data);
        $data = [];
        if(!empty($request->id)){
            $data = Karyawan::whereRaw("(jabatan = '$jbt->pr_jabatan' OR jabatan = '$request->id') AND aktif='1' ")->where('id_com', Auth::user()->id_com)->get();
        }
        return response()->json($data);
    }
    
    public function getpj(Request $request){
        $tj = Tunjangan::where('id_com', Auth::user()->id_com)->first();
        $jbt = Jabatan::where('id',$request->id)->where('id_com', Auth::user()->id_com)->first();
        // dd($data);
        $data = [];
        if(!empty($request->id)){
            $data = Karyawan::whereRaw("aktif='1' ")->get();
        }
        return response()->json($data);
    }
    
    public function ceklogin(Request $request){
        if(Auth::user()->id_com == 1){
            $data['a'] = Auth::user()->id_com;
            $data['b'] = url('/');
        }else{
            $data['a'] = Auth::user()->id_com;
            $data['b'] = url('/');
        }
        return response()->json($data);
    }
    
    public function countUsersLogin()
    {
            //  $sessions = $request->session()->all();
            //  dd($sessions);
            $currentTime = now();
       // Ambil data dari query Anda
        $results = LogActivityModel::selectRaw('users.name, log_activity.jenis_aksi, MAX(log_activity.created_at) as latest_created_at')
            ->whereDate('log_activity.created_at', '=', $currentTime->toDateString())
            ->join('users', 'users.id', '=', 'log_activity.user_id')
            ->whereIn('log_activity.jenis_aksi', ['Login', 'Logout'])
            ->groupBy('users.name', 'log_activity.jenis_aksi')
            ->orderBy('latest_created_at', 'desc')
            ->get();
        
        // Buat array asosiatif untuk menyimpan data terbaru berdasarkan nama pengguna
        $latestDataByUser = [];
        
        foreach ($results as $result) {
            $name = $result->name;
        
            // Jika objek untuk nama pengguna belum ada atau created_at lebih baru
            if (!isset($latestDataByUser[$name]) || $result->latest_created_at > $latestDataByUser[$name]->latest_created_at) {
                $latestDataByUser[$name] = $result;
            }
        }

// $latestDataByUser sekarang berisi data terbaru berdasarkan nama pengguna


        // Hasil: $currentTime->current_time berisi waktu saat ini

        // Jika Anda ingin menghitung lebih dari satu informasi, Anda dapat menyimpannya dalam array
        // Misalnya, informasi pengguna yang telah login, seperti nama mereka
        // $userInfo = [];
        // if ($loggedInUsers) {
        //     $userInfo[] = ['name' => Auth::user()->name];
        // }
        $hasil = [];
        foreach($latestDataByUser as $i => $val){
            $hasil[]=[
                'name' => $val->name,
                'aksi' => $val->jenis_aksi,
                ];
        }
        return response()->json($hasil);
    }

    
    public function hclevelid(Request $request){
        $level = Akses::where('id_com', $request->id_coms)->distinct()->get();
        return $level;
    }
    public function searchSidebar(Request $request){
        // dd($request);
        $searchQuery = $request->query('query');

        $data = DB::table('sidebar')
        ->select('sidebar.*', 'pas.menu as menu_parent')
        ->whereIn('sidebar.menu', $request->menu)
        ->whereRaw("(sidebar.link IS NOT NULL OR sidebar.link != '') AND sidebar.menu LIKE ?", ["%$searchQuery%"])
        ->leftJoin('sidebar as pas', 'sidebar.id_parent', '=', 'pas.id')
        ->get();
        // dd($data);

        return response()->json($data);
    }
    

//     public function baliklogin () {
// // dd(Auth::guard('kolektor'));
//         return view ('formlogin.login');
        
//     }
}
