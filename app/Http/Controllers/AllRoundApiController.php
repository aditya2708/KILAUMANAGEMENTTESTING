<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Auth;
use Str;
use Image;
use DB;
use App\Models\UserSSO;
use App\Models\User;
use App\Models\Aplikasi;
use App\Models\Donatur;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\UserSSOAuth;
use App\Models\Jabatan;
use App\Models\Company;
use App\Models\Karyawan;

class AllRoundApiController extends Controller
{
    public function masuk(Request $request){
        
        // return $request->header();
        
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::guard('user')->attempt($credentials)) {
            $user = Auth::guard('user')->user();
            
            if (!$user) {
                // \Log::error('User tidak ditemukan setelah autentikasi');
                return response()->json(['error' => 'User not found after authentication'], 500);
            }
            
            $token = $user->createToken('authToken')->plainTextToken;
        
            return response()->json([
                'token' => $token,
                'user' => $user,
                // 'url' => $user->spesial == 0 ? url($user->perus.'/dashboard') : url('berbagi-teknologi/home'),
                'url' => 'https://kilauindonesia.org/kilau/dashboard'
            ], 200);
            
        } 
        
        return response()->json(['message' => 'Unauthorized'], 401);
        
        // return $request;
        
        // $aha = User::where('api_token', $request->token)->where('email', $request->email);
        // // return $aha->first();
        // if($aha->exists()){
        //     // if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password, 'aktif' => 1])) {
        //     $user = $aha->where('aktif', 1)->first();
        //     if($user && Auth::guard('user')->loginUsingId($user->id)){
        //         // session()->regenerate();
        //         if(Auth::guard('user')->user()->level == null && Auth::guard('user')->user()->pengaturan == null && Auth::guard('user')->user()->kepegawaian == null && Auth::guard('user')->user()->keuangan == null && Auth::guard('user')->user()->kolekting == null ){
        //             return response()->json([
        //                 'success' => false,
        //                 'message' => 'Anda tidak punya akses di web ini!'
        //             ]);
                    
        //         }else{
        //             \LogActivity::addToLogsLogin(Auth::guard('user')->user()->name.' Telah Logging', Auth::guard('user')->user()->id, Auth::guard('user')->user()->kantor_induk, Auth::guard('user')->user()->id_kantor);
                    
                    
        //             // return response()->json([
        //             //     'success' => true,
        //             //     'id_com' => Auth::guard('user')->user()->id_com,
        //             //     'url' => Auth::guard('user')->user()->spesial == 0 ? url(Auth::guard('user')->user()->perus.'/dashboard') : url('berbagi-teknologi/home'),
        //             //     'message' => 'sukses'
        //             // ]);
        //         }
        //     // }elseif (Auth::guard('kolektor')->attempt($request->only('email','password'))) {
        //     //     return response()->json([
        //     //         'success' => true,
        //     //         'message' => 'gatau'
        //     //     ], 200);
        //     }
            
        // }else{
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Anda tidak mempunyai AKUN !'
        //     // ], 401);
        //     ]);
        // }
        
        // if(Auth::loginUsingId(1))
        
    }
    
     public function getCoa(Request $request)
{
    $query = DB::table('coa');

    // search (opsional)
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('coa', 'like', "%{$search}%")
              ->orWhere('nama_coa', 'like', "%{$search}%");
        });
    }

    // pagination dengan meta data Laravel
    $perPage = $request->input('per_page', 10);
    $paginator = $query->paginate($perPage);

    return response()->json([
        'status' => 'success',
        'data'   => $paginator
    ]);
}


    
    
    public function getCompany(Request $request)
    {
        $perPage = $request->input('per_page', 15);      // default 15
        $search  = $request->input('q');                 // ?q=keyword
    
        $query = Company::select('id_com', 'name', 'alias', 'email', 'web')
                        ->where('aktif', 1);             // tampilkan yg aktif saja
    
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('alias', 'like', "%$search%");
            });
        }
    
        $companies = $query->orderBy('name')->paginate($perPage);
    
        return response()->json($companies); // Laravel otomatis sertakan meta pagination
    }
    
    public function createCompany(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:191',
            'direktur'  => 'nullable|string|max:100',
            'alias'     => 'nullable|string|max:160',
            'sk'        => 'nullable|string|max:100',
            'npwp'      => 'nullable|string|max:100',
            'wa'        => 'nullable|string|max:100',
            'email'     => 'nullable|email|max:120',
            'web'       => 'nullable|string',
            'berdiri'   => 'nullable|string|max:100',
            'alamat'    => 'nullable|string',
            'akses'     => 'nullable|string|max:100',
            'jenis'     => 'nullable|string|max:100',
        ]);
    
        $company = new Company($validated);
        $company->aktif = 1; // default aktif
        $company->limit_com = 1; // sesuai default
        $company->fitur = 'free'; // default fitur
        $company->save();
    
        return response()->json([
            'message' => 'Company berhasil dibuat',
            'data'    => $company
        ], 201);
    }
   
    
    public function cari_donatur(Request $request)
    {
        $q = $request->search;
        $data = Donatur::where(function ($query) use ($q) {
                    $query->where('nama', 'LIKE', '%' . $q . '%')
                        ->orWhere('no_hp', 'LIKE', '%' . $q . '%')
                        ->orWhere('email', 'LIKE', '%'.$q.'%');
                })
                ->get();
        
        if (count($data) > 0) {
            foreach ($data as $key => $val) {
                $list[] = [
                    "text" => $val->nama,
                    "no_hp" => $val->no_hp,
                    "kota" => $val->kota,
                    "email" => $val->email,
                    "alamat" => $val->alamat,
                    "nama" => $val->nama,
                    "id" => $val->id,

                ];
            }
            return json_encode($list);
        } else {
            return "data tidak ditemukan";
        }
    }
    
    //  public function loginsso(Request $request, UserSSOAuth $user){
    //     // return response()->json([$request->email, $request->password, User::where('email', $request->email)->first()]);

    //     if(!Auth::guard('usersso')->attempt(['email' => $request->email, 'password' => $request->password])){
    //         return response()->json(['error' => 'salah bos atas']);
    //     }else{
            
    //         $userk = Auth::guard('usersso')->user();
            
    //         // return $userk;
            
    //         if($userk != null){
                
    //             $ror = User::selectRaw("name as nama, email, api_token as token, level, referral_code, kepegawaian, keuangan, kolekting, kolekting, presensi, cms, aktif")->where('api_token', $userk->token)->where('email', $userk->email)->first();
                
    //             if(!$ror){
    //                 $pp = $userk;
    //             }else{
    //                 $pp = $ror;
    //             }
                
    //             // \LogActivity::addToLog($.' telah Logging ');
    //             return response()->json([
    //                 'berhasil' => $pp,
    //                 'token' => $userk->token,
    //                 'messae' => 'sukses'
    //             ]);
    //         }
    //         return response()->json(['error' => 'salah bosss bawah']);
    //     }
    // }
    
    public function loginsso(Request $request, UserSSOAuth $user)
    {
        if (!Auth::guard('usersso')->attempt([
                'email'    => $request->email,
                'password' => $request->password
            ])) {
            return response()->json(['error' => 'salah bos atas']);
        }
    
        $userk = Auth::guard('usersso')->user();
        if (!$userk) {
            return response()->json(['error' => 'salah bosss bawah']);
        }
    
        /* ---------- data user seperti semula ---------- */
        $pp = User::selectRaw("
                    name        AS nama,
                    email,
                    api_token   AS token,
                    level,
                    referral_code,
                    kepegawaian,
                    keuangan,
                    kolekting,
                    presensi,
                    cms,
                    aktif
                ")
                ->where('api_token', $userk->token)
                ->where('email',    $userk->email)
                ->first() ?? $userk;
    
        /* ---------- ambil foto karyawan langsung dari DB ---------- */
        $filename = DB::table('karyawan')
                      ->where('email', $userk->email)
                      ->value('gambar_identitas');
        
        /* ---------- bangun URL foto ---------- */
        $pp->foto = $filename
            ? asset("upload/{$filename}")   // ➜ https://kilauindonesia.org/kilau/upload/...
            : null;
    
        return response()->json([
            'berhasil' => $pp,
            'token'    => $userk->token,
            'message'  => 'sukses'
        ]);
    }
    
    
    public function createUser(Request $request)
    {
        // Validasi sederhana
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:users_sso,email',
            'password' => 'required|string|min:6',
        ]);
    
        $token = Str::random(60);
        $hashedPassword = bcrypt($request->password); // atau Hash::make()
    
        // 1. Create user ke tabel `users`
        $user = new User();
        $referralCode = strtolower(Str::slug($request->nama)) . '-' . Str::random(6);
        $user->referral_code = $referralCode;
        $user->name = $request->nama;
        $user->email = $request->email;
        $user->password = $hashedPassword;
        $user->level = 'umum'; // default level
        $user->api_token = $token;
        $user->save();
    
        // 2. Create user ke tabel `users_sso`
        $usersso = new UserSSO();
        $usersso->nama = $request->nama;
        $usersso->email = $request->email;
        $usersso->password = $hashedPassword;
        $usersso->token = $token;
        $usersso->save();
    
        return response()->json([
            'message' => 'User berhasil dibuat',
            'user' => $user,
            'usersso' => $usersso,
            'token' => $token
        ], 201);
    }
    
    public function createUserBp (Request $request){
        // return $request;
        $user = new UserSSO();
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->token = $request->token;
        $user->save();
        
        return response()->json(['message' => 'User created successfully', 'user' => $user], 200);
    }
    
    public function datKar (){
        // url('/upload/'.$karyawan->gambar_identitas)
        $url = url('kilau/upload');
        $data = Karyawan::selectRaw("karyawan.id_karyawan, karyawan.nama, jabatan.jabatan, CONCAT('$url','/',karyawan.gambar_identitas) as url_image, karyawan.pendidikan")
                ->join('jabatan','jabatan.id','=','karyawan.jabatan')
                ->where('karyawan.aktif', 1)
                ->get();
        
        return response()->json(['message' => 'success', 'data' => $data], 200);
    }
    
    public function count_donatur (Request $req){
        $kon1 = "status IS NOT NULL";
        $kon2 = $req->kon == 0 ? "(status = 'Ditarik' OR status = 'Off')" : "status != 'Ditarik' AND status != 'Off'";
        $kon = $req->kon == '' ? $kon1 : $kon2;
        
        $data = Donatur::select('donatur.id','donatur.created_at as suki')
        
                    ->whereRaw("$kon")
        
                    ->whereIn('donatur.id', function($query){
                        $query->select('id_don')->from('prosp')->where('ket','closing');
                    })
                    ->count();
                    
                        // ->where(function($query) use ($kon) {
                        //     if(isset($req->warning)){
                        //         $kon1 = in_array('aktif', $kon) ? "status != 'Ditarik' AND status != 'Off'" : "status IS NOT NULL";
                        //         $kon2 = in_array('nonaktif', $kon) ? "(status = 'Ditarik' OR status = 'Off')" : "status IS NOT NULL";
                        //         $query->whereRaw("$kon1 AND $kon2");
                            
                        //     }
                        // })
                        // ->groupBy('id')->get();
                        
        return response()->json(['message' => 'success', 'data' => $data], 200);
    }
    
    public function updateUser (Request $request){
        
        $user = UserSSO::where('email', $request->email)->first();
        $user->password = $request->token;
        $user->token = $request->password;
        $user->update();
            
        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }
    
    public function listapp (){
        
        $data = Aplikasi::all();
            
        return response()->json(['data' => $data], 200);
    }
    
    public function cekTokenUser (Request $req){
        
         // Temukan user berdasarkan email
        $user = UserSSO::where('email', $req->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Cek apakah token sudah ada
        if ($user->token) {
            return response()->json([
                'message' => 'Token already exists',
                'token' => $user->token
            ], 200);
        }
    
        // Jika belum ada, buat token baru
        // $user->token = Str::random(60);
        // $user->save();
    
        // return response()->json([
        //     'message' => 'New token generated',
        //     'token' => $user->token
        // ], 201);
    }
    
    public function apilaporan (Request $req){
        
        if($req->jenis == ''){
            $jenis = "pembayaran IS NOT NULL";
        }else{
            if($req->jenis == 'cash'){
                $jenis = "pembayaran != 'noncash'";
            }else{
                $jenis = "pembayaran = 'noncash'";
            }
        }
        
        $tgl = $req->tanggal == '' ? date('Y-m-d', strtotime('-1 month')) : $req->tanggal;
        $y = date('Y', strtotime($tgl));
        $m = date('m', strtotime($tgl));
        
        // Penghimpunan Terikat
        
        // if($req->tab == 'penghimpunan'){
        //     $bahan = Transaksi::selectRaw("akun,  coa_kredit, SUM(jumlah) as penghimpunan, (SELECT sumber_dana FROM sumber_dana WHERE transaksi.id_sumdan = id_sumber_dana) as sumber_dana")
        //                     ->whereRaw("YEAR(tanggal) = '$y' AND MONTH(tanggal) = '$m' AND approval = 1 AND via_input != 'mutasi' AND id_sumdan IS NOT NULL AND jumlah > 0 AND $jenis")
        //                     ->groupBy('coa_kredit')
        //                     ->get();
        // }
        
        // if($req->tab == 'penyaluran'){
            $bahan = Pengeluaran::selectRaw("jenis_transaksi,  coa_debet, SUM(nominal) as penghimpunan")
                            ->whereRaw("YEAR(tgl) = '$y' AND MONTH(tgl) = '$m' AND acc = 1 AND via_input != 'mutasi' AND nominal > 0")
                            ->groupBy('coa_debet')
                            ->get();
        // }
        
        return $bahan;
        
        $data = [];
        
        foreach($bahan as $bah){
            
            $total = 0;
            
            $data[$bah->sumber_dana][] = [
                'Program' => $bah->akun,
                'Jumlah' => $total += $bah->penghimpunan
            ];
        }
        
        return $data;
        
        
        // Alokasi Terikat
        
        // Penghimpunan Tidak Terikat
        
        // Alokasi Tidak Terikat
    }
}