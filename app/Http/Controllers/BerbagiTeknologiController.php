<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Akses;
use App\Models\UserSSO;
use App\Models\Company;
use App\Models\Pricing;
use App\Models\Aplikasi;
use App\Models\PriceApk;
use App\Models\WaktuTrial;
use Auth;
use DB;
use DataTables;
use Carbon\Carbon;
use Str;
use Illuminate\Support\Facades\Http; 

class BerbagiTeknologiController extends Controller
{
    public function index(Request $req){
        return view('bt-views.home');
    }
    
    public function dashboard(Request $req){
        return view('bt-views.index');
    }
    
    public function akses(Request $req){
        return view('bt-views.akses');
    }
    
    public function apps(Request $req){
        return view('bt-views.apps');
    }
    
    public function getapps(Request $request)
    {
        $apps = Aplikasi::all();

        if ($apps->isEmpty()) {
            return response()->json([
                'headings' => ['No.', 'ID', 'Logo', 'Aplikasi', 'Deskripsi'],
                'data' => [],
            ]);
        }

        $data = [];
        foreach ($apps as $a) {
            $data[] = [
                $a->id,
                $a->logo,
                $a->aplikasi,
                $a->deskripsi,
            ];
        }
    
        return response()->json([
            'headings' => ['No.', 'ID', 'Logo', 'Aplikasi', 'Deskripsi'],
            'data' => $data,
        ]);
    }
    
    public function getakses(Request $req){
        $apps = Akses::all();

        if ($apps->isEmpty()) {
            return response()->json([
                'headings' => ['ID', 'Core', 'Setting', 'HCM'],
                'data' => [],
            ]);
        }
    
        $data = [];
        foreach ($apps as $a) {
            $data[] = [
                $a->id,
                $a->level,
                $a->pengaturan,
                $a->kepegawaian,
                
            ];
        }
        
        return response()->json([
            'headings' => ['ID', 'Core', 'Setting', 'HCM'],
            'data' => $data,
        ]);
    }
    
    public function postapps(Request $request){
        
        $validated = $request->validate([
            'id' => 'nullable|integer', // Field optional untuk cek apakah tambah atau edit
            'name' => 'required|string|max:255',
            'des' => 'required|string|max:255',
            'typee' => 'required',
            'link' => 'required',
            'jenis' => 'required',
            'foto' => 'required|string',
            'namafile_foto' => 'required|string|max:255',
        ]);
        
        $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
        $image_name = $validated['namafile_foto'];
        
        if (!empty($validated['foto'])) {
            $image_parts = explode(";base64,", $validated['foto']);
            if (count($image_parts) === 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $file = $folderPath . $image_name;
                file_put_contents($file, $image_base64);
            }
        }
        
        if (isset($validated['id'])) {
            
            
            $updateData = [
                'aplikasi' => $validated['name'],
                'deskripsi' => $validated['des'],
                'type' => $validated['typee'],
                'link' => $validated['urllogin'],
                'jenis' => $validated['jenis'],
            ];
        
            // Jika ada foto baru, tambahkan ke update
            if (!empty($validated['foto'])) {
                $updateData['logo'] = $image_name;
            }
            
            // Jika `id` ada, maka lakukan update data
            $affected = DB::table('aplikasi')
                ->where('id', $validated['id'])
                ->update($updateData);
        
            if ($affected) {
                return response()->json([
                    'message' => 'Aplikasi updated successfully',
                    'data' => [
                        'aplikasi' => $validated['name'],
                    ],
                ]);
            } else {
                return response()->json([
                    'message' => 'No changes made or ID not found',
                ]);
            }
        } else {
            // Jika `id` tidak ada, maka lakukan insert data baru
            $id = DB::table('aplikasi')->insertGetId([
                'aplikasi' => $validated['name'],
                'deskripsi' => $validated['des'],
                'type' => $validated['typee'],
                'link' => $validated['urllogin'],
                'jenis' => $validated['jenis'],
                'logo' => $image_name
            ]);
        
            return response()->json([
                'message' => 'Aplikasi added successfully',
                'data' => [
                    'aplikasi' => $validated['name'],
                ],
            ]);
        }
        
    }
    
    public function editapps($id){
        
        $data = Aplikasi::find($id);
        
        if (!$data) {
            return response()->json([
                'message' => 'Gagal GET DATA ID : '.$id,
                'data' => [],
            ]);
        }
    
        return response()->json([
            'message' => 'Berhasil GET DATA ID : '.$id,
            'data' => $data,
        ]);
    }
    
    public function delapps(Request $request){
        
        $donatur = Aplikasi::where('id', $request->id);
        $donatur->delete();
        return response()->json(['success' => 'Data is successfully deleted']);
    }
    
    public function company(Request $req){
        return view('bt-views.company');
    }
    
    public function getcompany(Request $request)
    {
        $companies = Company::selectRaw("id_com as id, name, id_hc, email, direktur, limit_user, jenis_perusahaan.jenis as jeniss, jumlah_karyawan, aktif")->leftjoin('jenis_perusahaan','company.id_jp','=','id')
        ->get();

        if ($companies->isEmpty()) {
            return response()->json([
                'headings' => ['id', 'Perusahaan', 'Holding', 'Email', 'Pimpinan', 'Karyawan', 'Jenis Bisnis','Aktif'],
                'data' => [],
            ]);
        }

        $data = [];
        foreach ($companies as $company) {
            $hace = Company::select('name')->where('id_com', $company->id_hc)->first();
            
            $data[] = [
                $company->id,
                $company->name,
                !$hace ? '' : $hace->name,
                $company->email,
                $company->direktur,
                $company->jumlah_karyawan,
                $company->jeniss,
                $company->aktif,
            ];
        }
    
        return response()->json([
            'headings' => ['id','Perusahaan', 'Holding', 'Email', 'Pimpinan', 'Karyawan','Jenis Bisnis','Aktif'],
            'data' => $data,
        ]);
    }
    
    public function autoaktivasi(Request $request)
    {
        Company::where('id_com', 4)->update(['auto_aktivasi' => $request->status]);
       
        return response()->json([
            'message' => 'Sukses',
        ]);
    }
    
    public function getcompanyNonAktive(Request $request)
    {
        return 'hehe';
        $companies = Company::selectRaw("id_com as id, name, id_hc, email, direktur, limit_user, jenis_perusahaan.jenis as jeniss, jumlah_karyawan, aktif")->leftjoin('jenis_perusahaan','company.id_jp','=','id')
        ->get();
    
        return response()->json([
            'headings' => ['id','Perusahaan', 'Holding', 'Email', 'Pimpinan', 'Karyawan','Jenis Bisnis','Aktif'],
            'data' => $companies,
        ]);
    }
    
    public function postcompany(Request $request){
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        // Simpan data ke tabel `company`
        $id = DB::table('company')->insertGetId([
            'name' => $validated['name'],
            'aktif' => 0,
        ]);
    
        return response()->json([
            'message' => 'Company added successfully',
            'data' => [
                'id' => $id,
                'name' => $validated['name'],
            ],
        ]);
    }
    
    public function delcompany(Request $request){
        $donatur = Company::where('id_com', $request->id);
        $donatur->delete();
        return response()->json(['success' => 'Data is successfully deleted']);
    }
    
    public function user_manage(){
        return view('bt-views.user_manage');
    }
    
    public function getusers(Request $req){
        $apps = UserSSO::all();

        if ($apps->isEmpty()) {
            return response()->json([
                'headings' => ['ID', 'User', 'Email'],
                'data' => [],
            ]);
        }
    
        $data = [];
        foreach ($apps as $a) {
            $data[] = [
                $a->id,
                $a->nama,
                $a->email,
            ];
        }
        
        return response()->json([
            'headings' => ['ID', 'User', 'Email'],
            'data' => $data,
        ]);
    }
    
    public function getUserById($id){
        $data = UserSSO::select('id','nama','email','kilau')->find($id);
        
        if($data){
            return response()->json([
                'message' => 'GET DATA successfully',
                'data' => $data,
            ]);
        }
        
        return response()->json([
                'message' => 'GET DATA failed',
                'data' => [],
        ]);
    }
    
    public function postUserAkses(Request $req){
        
        $affected = UserSSO::where('id', $req->id)
                ->update([
                    'kilau' => $req->cmskilau
                ]);
        
        if ($affected) {
            return response()->json([
                'message' => 'Berhasil, Hak Akses Telah diberikan',
            ]);
        } else {
            return response()->json([
                'message' => 'Gagal, Memberikan Hak Akses',
            ]);
        }
    }
    
    public function notify(){
        
        $data = Company::selectRaw("id_com, name, direktur, alamat, DATE(created_at) as tanggal, TIME(created_at) as jam, created_at as tgl")->where('baca', 1)->get();
        
        $lur = [];
        
        if ($data) {
            
            foreach($data as $d){
                
                $waktuInput = Carbon::parse($d->tgl);
                $waktuSekarang = Carbon::now();
            
                $selisihHari = $waktuInput->diffInDays($waktuSekarang);
                $selisihJam = $waktuInput->diffInHours($waktuSekarang);
                $selisihMenit = $waktuInput->diffInMinutes($waktuSekarang);
            
                if ($selisihHari >= 7) {
                    $dat = $waktuInput->format('d M Y'); // Lebih dari 7 hari, tampilkan tanggal
                } elseif ($selisihHari > 0) {
                     $dat = "$selisihHari hari yang lalu";
                } elseif ($selisihJam > 0) {
                     $dat = "$selisihJam jam yang lalu";
                } else {
                     $dat = "$selisihMenit menit yang lalu";
                }
                
                $lur[] = [
                    'id' => $d->id_com,
                    'message' => $d->name,
                    'time' => $dat
                ];
            }
            
            return response()->json([
                'message' => 'Ada Notif',
                'notifications' => $lur
            ]);
        } else {
            return response()->json([
                'message' => 'Kosong',
                'notifications' => []
            ]);
        }
    }
    
    public function hapusNotif(Request $req){
        
        $affected = Company::where('id_com', $req->id)
                ->update([
                    'baca' => $req->baca
                ]);
        
        if ($affected) {
            return response()->json([
                'message' => 'Berhasil, Remove Notif',
            ]);
        } else {
            return response()->json([
                'message' => 'Gagal, Remove Notif',
            ]);
        }
    }
    
    public function AktifCompany(Request $req){
        
        // foreach ($req->id as $id) {
        $id = $req->id;
        $val = Company::find($id);
        
        $token = Str::random(60);
        $pass = bcrypt(12345678);
        $name = 'Management';
    
            
        // Cek apakah user dengan email ini sudah ada
        $existingUser = User::where('email', $val->email)->first();
    
        if ($existingUser) {
            // Jika email sudah ada, hanya update company
            $val->update(['aktif' => $req->status]);
            $existingUser->update(['aktif' => $req->status]);
        } else {
            // Email belum ada, lakukan update company dan create user + sso
            $val->update(['aktif' => $req->status]);
    
            User::create([
                'name' => $name,
                'email' => $val->email,
                'password' => $pass,
                'kepegawaian' => 'admin',
                'presensi' => 'admin',
                'pengaturan' => 'admin',
                'api_token' => $token,
                'aktif' => 1,
                'level_hc' => 1,
                'id_com' => $val->id_com
            ]);
    
            UserSSO::create([
                'nama' => $name,
                'email' => $val->email,
                'password' => $pass,
                'token' => $token,
            ]);
            
           // asumsinya ini password default yang dipakai
            
        }
        
        
        // }
       

        // $kondis = $req->status == 1 ? 'Aktif' : 'Nonaktif';
        
        // $affected = Company::where('id_com', $req->id)
        //         ->update([
        //             'aktif' => $req->status
        //         ]);
        
        // if ($affected) {
        //     return response()->json([
        //         'message' => ['Berhasil, '.$kondis],
        //     ]);
        // } else {
        
        
            $no_wa = (string) $val->wa;
            $password_plain = '12345678';
            $pesan = "Assalamualaikum 🙏\n\n"
            . "Perusahaan Anda sudah diaktivasi. Silakan login dengan akun:\n\n"
            . "📧 Email    : $val->email\n"
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
        
            return response()->json([
                'message' => ['Berhasil,'],
            ]);
        // }
    }
    
    public function levelcompany(Request $req){
        $data = Pricing::whereNull('company')->get();
        $dataCompany = Pricing::whereNotNull('company')->get();
        $dataApk = PriceApk::all();
        $dataTrial = WaktuTrial::first();
        $company = Company::where('id_com', 4)->first();
        return view('bt-views.levelcompany', compact('data', 'dataApk', 'dataCompany', 'company','dataTrial'));
    }
    
    public function entrylevelprice(Request $req)
    {
        $data = $req->input('data');
        if (!is_array($data) || empty($data)) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak valid atau kosong.'
            ], 400);
        }
    
        DB::beginTransaction();
        try {
            // === Proses Data Level ===
            if (isset($data['level']) && is_array($data['level'])) {
                foreach ($data['level'] as $item) {
                    if (empty($item['id'])) {
                        // Jika level tidak diisi, lewati
                        if (empty($item) || $item <= 0) {
                            continue;
                        }
                        for ($i = 1; $i <= $item; $i++) {
                            Pricing::create([
                                'level' => 0,
                                'karyawan' => 0,
                                'harga' => 0,
                            ]);
                        }
                    } else {
                        Pricing::updateOrCreate(
                            ['id' => $item['id']],
                            [
                                'karyawan' => $item['karyawan'] ?? 0,
                                'harga' => $item['harga'] ?? 0,
                                'level' => $item['level'] ?? 0,
                            ]
                        );
                    }
                }
            }
    
            // === Proses Data APK ===
            if (isset($data['listApk']) && is_array($data['listApk'])) {
                foreach ($data['listApk'] as $item) {
                    if (empty($item['id'])) {
                        // Jika apk tidak diisi, lewati
                        if (empty($item) || $item <= 0) {
                            continue;
                        }
    
                        for ($i = 1; $i <= $item; $i++) {
                            PriceApk::create([
                                'nama_apk' => '',
                                'versi_apk' => 0,
                                'harga_apk' => 0,
                            ]);
                        }
                    } else {
                        PriceApk::updateOrCreate(
                            ['id' => $item['id']],
                            [
                                'nama_apk' => $item['nama_apk'] ?? '',
                                'versi_apk' => $item['versi_apk'] ?? 0,
                                'harga_apk' => $item['harga_apk'] ?? 0,
                            ]
                        );
                    }
                }
            }
            
            if (isset($data['listCompany']) && is_array($data['listCompany'])) {
                foreach ($data['listCompany'] as $item) {
                   
                        Pricing::updateOrCreate(
                            ['id' => $item['id']],
                            [
                                'harga' => $item['harga'],
                                'company' => $item['company'],
                            ]
                        );
                }
            }
            
            if($data['newCompany']['company'] != null){
                if (isset($data['newCompany']) && is_array($data['newCompany'])) {
                    Pricing::create([
                        'company' => $data['newCompany']['company'],
                        'harga' =>$data['newCompany']['harga'],
                    ]);
                }
            }
            
            if($data['listTrial']){
                if (isset($data['listTrial'])) {
                    WaktuTrial::findOrFail($data['listTrial']['id'])->update(
                        [
                            'id_com' => $data['listTrial']['id_com'],
                            'waktu' => $data['listTrial']['waktu']
                        ]);
                }
            }
    
            DB::commit();
    
            return response()->json([
                'data' => Pricing::whereNull('company')->get(),
                'dataCompany' => Pricing::whereNotNull('company')->get(),
                'dataApk' => PriceApk::all(),
                'status' => true,
                'message' => 'Berhasil menyimpan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage(),
            ], 400);
        }
    }



}
