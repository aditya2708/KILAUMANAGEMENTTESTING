<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Userkolektor;
use App\Models\User;
use App\Models\UserKolek;
use App\Models\Kolektors;
use App\Models\Kinerja;
use App\Models\Donatur;
use App\Models\DonaturTemp;
use App\Models\Donaturs;
use App\Models\Transaksi;
use App\Models\Tambahan;
use App\Models\Program;
use App\Models\Prog;
use App\Models\Jalur;
use App\Models\LapFol;
use App\Models\LapHub;
use App\Models\SumberDana;
use App\Models\Kota;
use App\Models\Provinsi;
use App\Models\Kantor;
use App\Models\RUpDon;
use App\Models\Bank;
use App\Models\Prospek;
use App\Models\Prosp;
use App\Models\Karyawan;
use App\Models\COA;
use App\Models\Tunjangan;
use App\Models\Transaksi_Perhari;
use App\Models\UpNoLoc;
use App\Models\SaldoAw;
use DB;
use Auth;
use Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Transformers\UserTransformer;
use App\Transformers\KolekTransformer;
use App\Transformers\AssisTransformer;
use App\Transformers\AssissTransformer;
use App\Transformers\AssisoTransformer;
use App\Transformers\RwyTransformer;
use App\Transformers\LaporanTransformer;
use App\Transformers\TransaksiTransformer;
use App\Transformers\TbhTransformer;
use App\Transformers\ProgramTransformer;
use App\Transformers\ProgTransformer;
use App\Transformers\KotaTransformer;
use App\Transformers\PropinsiTransformer;
use App\Transformers\LapFolTransformer;
use App\Transformers\DonaturTransformer;
use App\Transformers\DonTransformer;
use App\Transformers\DonIdTransformer;
use App\Transformers\DonSpvTransformer;
use App\Transformers\DonCabTransformer;
use App\Transformers\DonRangeTransformer;
use App\Transformers\DonUpTransformer;
use App\Transformers\DonNewTransformer;
use Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ApiUserKolektorController extends Controller
{
    public function index()
    {
        return Userkolektor::orderBy('created_at','desc')->get();
    }
 
    public function store(Request $request)
    {
     $userkolektor = new \App\Userkolektor;
     $userkolektor->nama = $request->nama;
     $userkolektor->no_hp = $request->no_hp;
     $userkolektor->alamat = $request->alamat;
     $userkolektor->kota = $request->kota;
     $userkolektor->email = $request->email;
     $userkolektor->username = $request->username;
     $userkolektor->password = $request->password;
     $userkolektor->level = $request->level;
     $userkolektor->save();
        return "Berhasil Tambah Data";
    }
 
    public function destroy($id)
    {
        $data = Userkolektor::findOrFail($id);
        $data->delete();
        return "Hapus Data Sukses";
    }
 
    public function update($id, Request $request)
    {
        $data = Userkolektor::findOrFail($id);
        $input = $request->all();
        $data->update($input);
        return "Edit Data Sukses";
    }
 
    public function edit($id)
    {
        $data = Userkolektor::findOrFail($id);
        return $data;
    }

    public function getlogin (Request $request)
    {
        // dd($request->all());

        $data = Userkolektor::where('email',$request->email)->first();
        if($data){
            if (auth()->attempt($request->only('email','password'))){
            }
            else{
               return response()->json(['berhasil' => $data]);
            }
        }
        else{
             return response()->json(['error' => 'gagal login',401]);
        }
    }
    
    
     public function loginuser(Kolektors $user){
        $users = $user->all();
        return fractal()
        ->collection($users)
        ->transformWith(new KolekTransformer())
        ->includeCharacters()
        ->toArray();
        }
 
    
    // public function login(Request $request, Kolektors $user){
    // if(!Auth::guard('kolektor')->attempt(['email' => $request->email, 'password' => $request->password])){
    //     return response()->json(['error' => 'salah bos',401]);
    // }else{
    //     $userk = $user->find(Auth::guard('kolektor')->user()->id_koleks);
    //     return response()->json([
    //         'berhasil' => $userk,
    //         'token' => $userk->api_token,
    //         ]);
       
        
    // }
    // }
    
    public function login(Request $request, UserKolek $user){
    if(!Auth::guard('userkolek')->attempt(['email' => $request->email, 'password' => $request->password, 'aktif' => 1])){
        return response()->json(['error' => 'salah bos',401]);
    }else{
        $userk = $user->find(Auth::guard('userkolek')->user()->id);
        if($userk->kolektor != null){
        \LogActivity::addToLog(Auth::guard('userkolek')->user()->name.' telah Logging Kolekting App ');
        return response()->json([
            'berhasil' => $userk,
            'token' => $userk->api_token,
            'kol' => $userk->kolektor,
            ]);
        }
        return response()->json(['error' => 'salah bos',401]);
    }
    }
    
    public function donatur(Request $request, Donaturs $user){
        if(!Auth::guard('donatur')->attempt(['email' => $request->email, 'password' => $request->password])){
            return response()->json(['error' => 'salah bos',401]);
        }else{
            $userk = $user->find(Auth::guard('donatur')->user()->id_donaturs);
        return response()->json([
            'berhasil' => $userk,
            'token' => $userk->api_token,
            ]);
        }
        
        
        }

    
//       public function profile(Kolektors $user){
//     $userp = $user->find(Auth::user()->id_koleks);
//     return fractal($userp, new KolekTransformer())->addMeta(['token' => $userp->api_token])->toArray();
// }

//   public function profile(Kolektors $user){
//     $user = Kolektors::find(Auth::user()->id_koleks);
//     $fractal = fractal()
//     ->item($user)
//     ->transformWith(new KolekTransformer())
//     ->toArray();
//     return response()->json($fractal);
// }

public function profile(UserKolek $user){
    if(Auth::user()->kolektor != null && Auth::user()->aktif == 1){
        $user = UserKolek::find(Auth::user()->id);
    }else{
        $user = UserKolek::find(0);
    }
    $fractal = fractal()
    ->item($user)
    ->transformWith(new KolekTransformer())
    ->toArray();
    return response()->json($fractal);
    // }
}

public function profilekol(UserKolek $user){
    // if(Auth::user()->kolektor != null && Auth::user()->aktif == 1){
        $user = UserKolek::find(Auth::user()->id);
    // }else{
    //     $user = UserKolek::find(0);
    // }
    $fractal = fractal()
    ->item($user)
    ->transformWith(new KolekTransformer())
    ->toArray();
    return response()->json($fractal);
    // }
}

public function updates(Request $request, Kolektors $user)
{
    $user = Kolektors::find(Auth::user()->id_koleks);
     $user->name = $request->get('email',$user->email);
    $user->update();

    $fractal = fractal()
    ->item($user)
    ->transformWith(new KolekTransformer())
    ->toArray();
    return response()->json($fractal);
}
    
    

    public function user(Request $request)
    {
        $data = Userkolektor::get();
        return $data;
        
    } 

    public function sukses(){
        
        return view ('sukses');
    }
    
    // $kerja = Kinerja::where('id_koleks',Auth::user()->id_koleks)->get();
    // return response()->json([
    //     'berhasil' => $kerja,
    //     'token' => Auth::user()->api_token
    //     ]);
    
    
    // $kerja = Donatur::where('petugas',Auth::user()->name)->where('status','=','belum dikunjungi')->where('acc','=',1)->orWhere('petugas',Auth::user()->name)->where('status','=','Tutup')->where('acc','=',1)->get();
    
    // public function assigment(){
    // $kerja = Donatur::where('petugas',Auth::user()->name)->where('status','!=','Ditarik')->where('acc','=',1)->get();
    // return fractal($kerja, new AssisTransformer())->toArray();
    // }
    
    public function assigment(){
    // $kerja = Donatur::where('petugas',Auth::user()->name)->where('status','=','belum dikunjungi')->where('acc','=',1)->orWhere('petugas',Auth::user()->name)->where('status','=','Tutup')->where('acc','=',1)->get();
    
    $kerja = Auth::user()->kolektor != null && Auth::user()->aktif == 1 ? 
    Donatur::where('petugas',Auth::user()->name)->where('status','!=','Ditarik')->where('acc','=',1)->get() : 
        Donatur::where('petugas', Str::random(60))->get();
    return fractal($kerja, new AssissTransformer())->toArray();
    }
    
    public function assignment(){
    // $kerja = Donatur::where('petugas',Auth::user()->name)->where('status','=','belum dikunjungi')->where('acc','=',1)->orWhere('petugas',Auth::user()->name)->where('status','=','Tutup')->where('acc','=',1)->get();
    
    $kerja = Auth::user()->kolektor != null && Auth::user()->aktif == 1 ? 
    Donatur::where('petugas',Auth::user()->name)->where('status','!=','Ditarik')->where('acc','=',1)->get() : 
        Donatur::where('petugas', Str::random(60))->get();
    return fractal($kerja, new AssisTransformer())->toArray();
    }
    
    public function assignment2(){
        
        $kerja = Auth::user()->kolektor != null && Auth::user()->aktif == 1 ? 
        Donatur::where('petugas',Auth::user()->name)->where('status','!=','Ditarik')->where('acc',1)->get() : 
            Donatur::where('petugas', Str::random(60))->get();
        return $kerja;
        
        $datak = [];
        
        foreach($kerja as $user){
            $ha = unserialize($user->program);
            $datay = [];
            if(count($ha) > 0){
                foreach($ha as $h){
                    $prog = Prog::where('id_program', $h)->first();
                    $datay[] = $prog->program;
                }
            }
            
            $datak[] =  [
                'id' => $user->id,
                'id_temp' => $user->id_temp,
                'id_transaksi' => $user->id_transaksi,
                'tanggal' => $user->tanggal,
                'petugas' => $user->petugas,
                'nama' => $user->nama,
                'email' => $user->email,
                'np' => $user->np,
                'status' => $user->status,
                'jalur' => $user->jalur,
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
                'alamat' => $user->alamat,
                'tgl_kolek' => $user->tgl_kolek,
                'pembayaran' => $user->pembayaran,
                'id_program' => unserialize($user->program),
                'program' => $datay,
                'no_hp' => $user->no_hp,
                'nohap' => $user->nohap,
                'orng_dihubungi' => $user->orng_dihubungi,
                'jabatan' => $user->jabatan,
                'setoran' => $user->setoran,
                'kota' => $user->kota,
                'bukti' => $user->bukti,
                'acc' => $user->acc,
                'dikolek' => $user->dikolek,
                'gambar_donatur' => $user->gambar_donatur,
                'registered' => $user->created_at->format('d M Y'),
                'updated' => $user->updated_at->format('d M Y'),
                'date' => $user->created_at->format('Y-m-d'),
                'warning' => $user->warning,
                'ket' => $user->ket,
                'jenis_donatur' => $user->jenis_donatur,
                'gambar' => $user->gambar_donatur,
                'tgl_fol' => $user->tgl_fol,
                'id_sumdan' => $user->id_sumdan,
            ];
        }
        
        // return $datak;
        
    }
    

    public function riwayat(){
    $rwy = Auth::user()->kolektor != null && Auth::user()->aktif == 1 ? 
    Donatur::where('status','!=','belum dikunjungi')->where('status','!=','Tutup')
            ->whereIn('id', function($query){
                        $query->select('id_donatur')->from('transaksi')->where('id_koleks', Auth::user()->id)->where('via_input', 'transaksi')
                                ->whereDate('created_at','=', date('Y-m-d'));
                    })
            ->orderBy('updated_at','desc')->get() :
        Donatur::where('petugas', Str::random(60))->get();
    return fractal($rwy, new RwyTransformer())->toArray();
    }
    
    public function target_kunjungan(){
    // $rwy = Auth::user()->kolektor != null && Auth::user()->aktif == 1 ? 
    // Donatur::where('petugas',Auth::user()->name)
    //         ->whereIn('id', function($query){
    //             $query->select('id_donatur')->from('transaksi')->where('id_koleks', Auth::user()->id)->where('via_input', 'transaksi')
    //                     ->whereDate('created_at','=', date('Y-m-d'));
    //         })
    //         ->get():
    //     Donatur::where('petugas', Str::random(60))->get();
    // return fractal($rwy, new RwyTransformer())->toArray();
    
        $don = Donatur::selectRaw("donatur.*, t.id_transaksi, t.tanggal")
                ->join('transaksi as t','t.id_donatur','=','donatur.id')
                ->where('petugas',Auth::user()->name)
                ->where('t.id_koleks', Auth::user()->id)
                ->where('via_input', 'transaksi')
                ->whereDate('t.created_at','=', date('Y-m-d'))
                  ->get();
    
        $dataa = [];
        
        foreach($don as $user){
            $dataa[] = [
                'id' => $user->id,
                'id_kantor' => $user->id_kantor,
                'id_transaksi' => $user->id_transaksi,
                'tanggal' => $user->tanggal,
                'nama' => $user->nama,
                'status' => $user->status,
                'jalur' => $user->jalur,
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
                'alamat' => $user->alamat,
                'tgl_kolek' => $user->tgl_kolek,
                'pembayaran' => $user->pembayaran,
                'program' => $user->program,
                'no_hp' => $user->no_hp,
                'setoran' => $user->setoran,
                'kota' => $user->kota,
                'bukti' => $user->bukti,
                'acc' => $user->acc,
                'dikolek' => $user->dikolek,
                'gambar_donatur' => $user->gambar_donatur,
                'registered' => $user->created_at->format('d M Y'),
                'updated' => $user->updated_at->format('d M Y'),
                'date' => $user->created_at->format('Y-m-d'),
                'up' => $user->updated_at,
                'approval' => $user->approval,
                
            ];
        }
        
        
        return response()->json(
            [
                // "status"=>"sukses",
                "data"=> $dataa
            ]
        );
    
    }
    
    public function program(){
    $prog = Program::orderBy('program','asc')->get();
    return fractal($prog, new ProgramTransformer())->toArray();
    }
    
    public function tambahan(){
    $tbh = Tambahan::get();
    return fractal($tbh, new TbhTransformer())->toArray();
    }
 
 
//  public function tambahan ($unit)
// {
//     $donatur = Tambahan::find($unit);
//     $fractal = fractal()
//     ->item($donatur)
//     ->transformWith(new TbhTransformer())
//     ->toArray();
//     return response()->json($fractal);
// }

public function editdonatur ($id)
{
    $donatur = Donatur::find($id);
    $fractal = fractal()
    ->item($donatur)
    ->transformWith(new AssisTransformer())
    ->toArray();
    return response()->json($fractal);
}

public function updatedonatur(Request $request, $id)
{
    $user = Donatur::find($id);
    $user->tesum = $request->get('setoran',$user->tesum);

$id_trans = \DB::select("SELECT id FROM donatur WHERE status = 'Tutup' AND dikolek = '$request->dikolek' AND id = '$id'");

// if (count($id_trans) >= 1 && $request->status == 'Tutup 2x') {
//     $user->status = 'Tutup';
//     $user->acc = 1;
// } else {
    $user->status = $request->get('status',$user->status);
    $user->acc = $request->get('acc',$user->acc);
// } 

    // $user->status = $request->get('status',$user->status);
    // $user->acc = $request->get('acc',$user->acc);
    $user->dikolek = $request->get('dikolek',$user->dikolek);
    $user->user_trans = Auth::user()->id;
    // $user->bukti = $request->file('bukti')->getClientOriginalName();
    $user->update();

    $fractal = fractal()
    ->item($user)
    ->transformWith(new AssisTransformer())
    ->toArray();
    return response()->json([
        "data"=>$fractal,
        "status"=>"sukses"
        ]);
}

// public function add (Request $request, Kinerja $kerja)
// {
//     $this->validate($request,[
//         'kdonasi' => 'required'
//     ]);

//   $kerja = $kerja->create([
//         'id_koleks' => Auth::user()->id_koleks,
//         'kdonasi' => $request->kdonasi,
//     ]);

//     $response = fractal()
//     ->item($kerja)
//     ->transformWith(new LaporanTransformer)
//     ->toArray();

//     return response()->json($response, 201);
// }

public function laporankuy(Request $request)
{
$data = new Kinerja;
$data->id_koleks = Auth::user()->id;
$data->kdonasi = $request->kdonasi;
$data->ktdonasi = $request->ktdonasi;
$data->ktutup = $request->ktutup;
$data->ktutup3x = $request->ktutupx;
$data->kditarik = $request->kditarik;
$data->khilang = $request->khilang;
$data->tdonasi = $request->tdonasi;
$data->tdkdonasi = $request->tdkdonasi;
$data->toff = $request->toff;
$data->save();

return response()->json(
    [
        "status"=>"sukses",
        "data"=>$data
    ]
    );
}

public function transaksi(Request $request)
{
$don = Donatur::find($request->id_donatur);
$data = new Transaksi;
$data->id_koleks = Auth::user()->id;
$data->id_donatur = $request->id_donatur;
$data->id_transaksi = $request->id_transaksi;
$data->tanggal = date('Y-m-d');
$data->donatur = $request->donatur;
$data->pembayaran = $request->pembayaran;
$data->kolektor = Auth::user()->name;
$data->alamat = $request->alamat;
$data->program = $request->program;
$data->subprogram = $request->subprogram;
$data->keterangan = $request->keterangan;
$data->id_kantor = $don->id_kantor;
$data->via_input = 'transaksi';


// $id_trans = \DB::select("SELECT id_transaksi FROM transaksi WHERE status = 'Tutup' AND id_transaksi = '$request->id_transaksi'");

// if (count($id_trans) >= 1 && $request->status == 'Tutup 2x') {
//     $data->status = 'Tutup';
// } else {
    $data->status = $request->status;
// } 

$data->jumlah = preg_replace("/[^0-9]/", "", $request->jumlah);
$data->kota = $request->kota;

//   if ($request->hasFile('bukti')) {
//                 // Ambil file gambar asli
//         $image = $request->file('bukti');
//         $imageName = $image->getClientOriginalName(); // Nama file disimpan
    
//         // Tentukan path untuk menyimpan gambar
//         $destinationPath = 'gambarUpload'; // Path relatif
    
//         // Pastikan direktori tujuan ada
//         if (!file_exists($destinationPath)) {
//             mkdir($destinationPath, 0755, true); // Buat direktori jika belum ada
//         }
    
//         // Dapatkan tipe gambar untuk menentukan cara kompresi
//         $imageType = $image->getClientOriginalExtension();
        
//         // Buat resource gambar berdasarkan tipe file
//         if ($imageType == 'jpeg' || $imageType == 'jpg') {
//             $source = imagecreatefromjpeg($image->getPathname());
//             // Coba gunakan kualitas 50 untuk lebih banyak kompresi
//             imagejpeg($source, $destinationPath . '/' . $imageName, 10); // Kompresi kualitas 50 untuk JPEG
//         } elseif ($imageType == 'png') {
//             $source = imagecreatefrompng($image->getPathname());
//             // Coba kompresi level 9 (maksimum) untuk PNG
//             imagepng($source, $destinationPath . '/' . $imageName, 9); // Kompresi level 9 untuk PNG
//         } else {
//           $request->file('bukti')->move('gambarUpload',$request->file('bukti')->getClientOriginalName());
//         }
    
//         // Hapus resource gambar dari memori
//         imagedestroy($source);
    
//     }

if($request->hasFile('bukti')){
    $data->bukti = $request->file('bukti')->getClientOriginalName();
    $request->file('bukti')->move('gambarUpload',$data->bukti);
}
// $filename    = $data->bukti->getClientOriginalName();
// $image_resize = Image::make($data->bukti->getRealPath());              
// $image_resize->resize(300, 300);
// $image_resize->save(public_path('gambarUpload/' .$filename));

$data->save();

return response()->json(
    [
        "status"=>"sukses",
        "data"=>$data
    ]
    );
}

public function transaksi2(Request $request)
{
$don = Donatur::find($request->id_donatur);
$data = new Transaksi;
$data->id_koleks = Auth::user()->id;
$data->id_donatur = $request->id_donatur;
$data->id_transaksi = $request->id_transaksi;
$data->tanggal = date('Y-m-d');
$data->donatur = $request->donatur;
$data->pembayaran = $request->pembayaran;
$data->kolektor = $request->kolektor;
$data->alamat = $request->alamat;
$data->program = $request->program;
$data->subprogram = $request->subprogram;
$data->keterangan = $request->keterangan;
$data->status = $request->status;
$data->jumlah = preg_replace("/[^0-9]/", "", $request->jumlah);
$data->kota = $request->kota;
$data->id_kantor = $don->id_kantor;
$data->via_input = 'transaksi';
// $data->bukti = $request->file('bukti')->getClientOriginalName();
// $request->file('bukti')->move('gambarUpload',$data->bukti);

$data->save();

return response()->json(
    [
        "status"=>"sukses",
        "data"=>$data
    ]
    );
}


public function getrf ()
{
    $trf = Transaksi::get();
    return fractal($trf, new TransaksiTransformer())->toArray();
    //  $trf = Transaksi::get();
    // $fractal = fractal()
    // ->item($trf)
    // ->transformWith(new TransaksiTransformer())
    // ->toArray();
//     return response()->json(
//   ["data"=>$trf]
//     );
}

public function transkol ($id)
{
    $day = Carbon::now()->format('d');
    $month = Carbon::now()->format('m');
    $year = Carbon::now()->format('Y');

    $trf = Transaksi::where('id_koleks',$id)->whereMonth('created_at', $month)->whereYear('created_at', $year)->where('via_input', 'transaksi')->get();
    return fractal($trf, new TransaksiTransformer())->toArray();
}


public function transdon ($id)
{
    $day = Carbon::now()->format('d');
    $month = Carbon::now()->format('m');
    $year = Carbon::now()->format('Y');

    $trf = Transaksi::where('id_donatur',$id)->whereMonth('created_at',$month)->whereYear('created_at',$year)->where('via_input', 'transaksi')->get();
    return fractal($trf, new TransaksiTransformer())->toArray();
}



public function updateuser(Request $request, $id)
{
    $user = Kolektors::find($id);
    $user->name = $request->get('name',$user->name);
    $user->email = $request->get('email',$user->email);
    $user->update();

    $fractal = fractal()
    ->item($user)
    ->transformWith(new KolekTransformer())
    ->toArray();
    return response()->json([
        "data"=>$fractal,
        "status"=>"sukses"
        ]);
}

public function updatelokasi2(Request $request, $id)
{
    $user = Donatur::find($id);
    $user->no_hp = $request->get('no_hp',$user->no_hp);
    $user->latitude = $request->get('latitude',$user->latitude);
    $user->longitude = $request->get('longitude',$user->longitude);
    
    if($request->gambar != ''){
        $user->gambar_donatur = $request->file('gambar')->getClientOriginalName();
        $request->file('gambar')->move('gambarDonatur',$user->gambar_donatur);
    }
    $user->update();

    // $fractal = fractal()
    // ->item($user)
    // ->transformWith(new AssisTransformer())
    // ->toArray();
    return response()->json([
        // "data"=>$fractal,
        "status"=>"sukses"
        ]);
}

public function uploadpdf(Request $request)
{
$request->file('file')->move('gambarUpload',$request->file('file')->getClientOriginalfileName());
}

public function getdonatur($jalur, $status){
    // $kerja = Donatur::where('petugas',Auth::user()->name)->where('status','=','belum dikunjungi')->where('acc','=',1)->orWhere('petugas',Auth::user()->name)->where('status','=','Tutup')->where('acc','=',1)->get();
    $jal = $jalur == "jalur" ? "jalur != 'jalur'" : "jalur = '$jalur'";
    $stat = $status == "status" ? "status != 'status'" : "status = '$status'";
    $kerja = Auth::user()->kolektor != null && Auth::user()->aktif == 1 ? 
        Donatur::where('id_kantor', Auth::user()->id_kantor)
            ->where('status','!=','Ditarik')->where('status','!=','Off')
            // ->where(function($query) {
            //     $query->where('petugas', 'User Tester')->orWhere('petugas', 'Maman Sukmana');
            // })
            ->whereRaw("$jal AND $stat")
            ->orderBy('petugas', 'asc')
            ->get()
        : Donatur::where('status', Str::random(60))->get();
    return fractal($kerja, new DonTransformer(['kon' => 'Don']))->toArray();
}

public function listassign($kan, $jal, $status, $acc){
    $awal = date('Y-m-01', strtotime(date('Y-m-d')));
    $akhir = date('Y-m-t', strtotime(date('Y-m-d')));
    $kerja = Donatur::join('tambahan', 'tambahan.id', '=', 'donatur.id_kantor')
            ->select('donatur.*', 'tambahan.kantor_induk', 'tambahan.unit')
            ->where(function($query) use ($kan) {
                if($kan == 'all'){
                    $query->where('donatur.id_kantor', Auth::user()->id_kantor)->orWhere('tambahan.kantor_induk', Auth::user()->id_kantor);
                }else{
                    $query->where('donatur.id_kantor', $kan);
                }
            })
            ->where(function($query) use ($jal) {
                if($jal != 'all'){
                    $query->where('donatur.id_jalur', $jal);
                }
            })
            ->where(function($query) use ($status) {
                if($status != 'all'){
                    $query->where('donatur.status', $status);
                }
            })
            ->where(function($query) use ($jal) {
                if($jal == 1){
                    $query->where('donatur.acc', 0);
                }
            })
            ->where('donatur.status','!=', 'Ditarik')->where('donatur.status','!=', 'Off')
            ->whereDate('donatur.created_at','<=', $awal)
            ->orderBy('donatur.nama', 'asc')
            // ->limit(2)
            ->get();
    // $jal = $jalur == "jalur" ? "jalur != 'jalur'" : "jalur = '$jalur'";
    // $stat = $status == "status" ? "status != 'status'" : "status = '$status'";
    // $kerja = Auth::user()->kolektor != null && Auth::user()->aktif == 1 ? 
    //     Donatur::where('id_kantor', $unit)
    //         ->where('status','!=','Ditarik')->where('status','!=','Off')
    //         ->where(function($query) use ($acc) {
    //             if($acc == 1){
    //                 $query->where('acc', 1);
    //             }
    //         })
    //         ->whereRaw("$jal AND $stat")
    //         ->orderBy('petugas', 'asc')
    //         ->get()
    //     : Donatur::where('status', Str::random(60))->get();
    return fractal($kerja, new DonTransformer(['kon' => 'Don']))->toArray();
}

public function listassignnow($kan, $jal, $status, $acc){
    $awal = date('Y-m-01', strtotime(date('Y-m-d')));
    $akhir = date('Y-m-t', strtotime(date('Y-m-d')));
    $kerja = Donatur::join('tambahan', 'tambahan.id', '=', 'donatur.id_kantor')
            ->select('donatur.*', 'tambahan.kantor_induk', 'tambahan.unit')
            ->where(function($query) use ($kan) {
                if($kan == 'all'){
                    $query->where('donatur.id_kantor', Auth::user()->id_kantor)->orWhere('tambahan.kantor_induk', Auth::user()->id_kantor);
                }else{
                    $query->where('donatur.id_kantor', $kan);
                }
            })
            ->where(function($query) use ($jal) {
                if($jal != 'all'){
                    $query->where('donatur.id_jalur', $jal);
                }
            })
            ->where(function($query) use ($status) {
                if($status != 'all'){
                    $query->where('donatur.status', $status);
                }
            })
            ->where(function($query) use ($jal) {
                if($jal == 1){
                    $query->where('donatur.acc', 0);
                }
            })
            ->where('donatur.status','!=', 'Ditarik')->where('donatur.status','!=', 'Off')
            ->whereDate('donatur.created_at','>=', $awal)
            ->orderBy('donatur.nama', 'asc')
            ->get();
    return fractal($kerja, new DonTransformer(['kon' => 'Don']))->toArray();
}

public function getmapdon($jalur, $status){

        
        $jal = $jalur == "jalur" ? "jalur = ''" : "jalur = '$jalur'";
        if($status == 'aktif'){
        $kerja = Donatur::where('id_kantor', Auth::user()->id_kantor)
                ->where('status','!=','Ditarik')->where('status','!=','Off')
                ->whereRaw("$jal")
                ->orderBy('petugas', 'asc')
                ->get();
        }else if($status == 'nonaktif'){
        $kerja = Donatur::where('id_kantor', Auth::user()->id_kantor)
                ->where(function($query) {
                    $query->where('status', 'Ditarik')->orWhere('status', 'Off');
                })
                ->whereRaw("$jal")
                ->orderBy('petugas', 'asc')
                ->get();
        }else{
        $kerja = Donatur::where('id_kantor', Auth::user()->id_kantor)
                ->whereRaw("$jal")
                ->orderBy('petugas', 'asc')
                ->get();
    }
    
    return fractal($kerja, new DonTransformer(['kon' => 'Don']))->toArray();
}

    public function changestatus($id)
    {
        $data = Donatur::where('id', $id)->first();
        // return $data;
        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }
    
        $bayar = $data->pembayaran;
        $status_sekarang = $data->status;
        $registered = $data->tgl_nonaktif;
    
        // Cek apakah registered sudah lebih dari atau tepat 3 bulan dan status_sekarang dalam kondisi tertentu
        $threeMonthsAgo = Carbon::now()->subMonths(3);
        if (Carbon::parse($registered)->lessThanOrEqualTo($threeMonthsAgo) && 
            in_array($status_sekarang, ['Off', 'Ditarik', 'Kotak Hilang'])) {
            // Update registered menjadi tanggal saat ini
            Donatur::where('id', $id)->update([
                'created_at' =>  Carbon::now(),
                'tgl_aktif' =>  Carbon::now(),
                // 'tgl_nonaktif' =>  null,
            ]);
        }
    
        // Tentukan status baru berdasarkan kondisi
        if (in_array($status_sekarang, ['Ditarik', 'Off', 'Kotak Hilang'])) {
            $status_baru = 'belum dikunjungi';
            $tgl_field = 'tgl_aktif';
        } else {
            $status_baru = ($bayar == 'transfer') ? 'Off' : 'Ditarik';
            $tgl_field = 'tgl_nonaktif';
        }
        // Perbarui data donatur
        Donatur::where('id', $id)->update([
            'status' => $status_baru,
            $tgl_field => Carbon::now(),
            'user_update' => Auth::user()->id,
            'id_koleks' => Auth::user()->id,
            'petugas' => Auth::user()->name
        ]);
    
        // Tambahkan log aktivitas
        $log_message = Auth::user()->name . ' ' . ($status_baru == 'belum dikunjungi' ? 'Mengaktifkan' : 'Menonaktifkan') . ' Data Donatur ' . $data->nama . ' Dengan ID ' . $id;
        \LogActivity::addToLoghfm($log_message, 'kosong', 'donatur', 'update', $id);
    
        return response()->json(['success' => $data]);
    }

public function assigndon(Request $request, $id)
{
    $don = Donatur::find($id);
    $petugas = User::where('id', $don->id_koleks)->first();
    $trans = Tunjangan::first();
    
    if($don->warning == 1 && $petugas->id_jabatan == $trans->kolektor && Auth::user()->kolektor == 'spv kolektor' && $petugas->status_kerja != 'Magang'){
        $don = [];
        
    return response()->json([
        "status"=>"gagal"
        ]);
    }else{
        $don->acc = $request->acc;
        $don->tgl_kolek = $request->acc == 1 ? date('Y-m-d H:i:s') : NULL;
        $don->update();
        
    return response()->json([
        "status"=>"sukses"
        ]);
    }
        
    // $user->acc = $request->acc;
    // $user->update();

}
public function assignall(Request $request, $jalur, $status)
{
    $trans = Tunjangan::first();
    $kol = $trans->kolektor;
    if(Auth::user()->kolektor == 'spv kolektor'){
        Donatur::where('id_jalur', $jalur)->where('id_kantor', Auth::user()->id_kantor)->where('status', $status)
                // ->where('warning', '!=', 1)
                // ->whereNotIn('id_koleks', function($query) use ($kol){
                //             $query->select('id')->from('users')->where('id_jabatan', $kol);
                //         })
                ->whereIn('id', function($query){
                            $query->select('id_don')->from('prosp')->where('ket', 'closing');
                        })        
                ->update([
                  'acc' => $request->acc,
                  'tgl_kolek' => $request->acc == 1 ? date('Y-m-d H:i:s') : NULL
                ]);
    }else{
        Donatur::where('id_jalur', $jalur)->where('id_kantor', Auth::user()->id_kantor)->where('status', $status)
                // ->where('warning', '!=', 1)
                ->whereIn('id', function($query){
                            $query->select('id_don')->from('prosp')->where('ket', 'closing');
                        }) 
                ->update([
                  'acc' => $request->acc,
                  'tgl_kolek' => $request->acc == 1 ? date('Y-m-d H:i:s') : NULL
                ]);
    }
    return response()->json([
        "status"=>"sukses"
        ]);
}
public function updon(Request $request, $id)
{
    $user = Donatur::find($id);
    $user->petugas = $request->petugas;
    $user->pembayaran = $request->pembayaran;
    $user->update();

    return response()->json([
        "status"=>"sukses"
        ]);
}

public function updonass(Request $request, $id)
{
                
    $user = UserKolek::find($request->petugas);
    $trans = Tunjangan::first();
    $don = Donatur::find($id);
    
    if($don->warning == 1 && $user->id_jabatan == $trans->kolektor && Auth::user()->kolektor == 'spv kolektor' && $petugas->status_kerja != 'Magang'){
        $don = [];
    
    return response()->json([
        "status"=>"gagal"
        ]);
    }else{
        $don->id_koleks = $user->id;
        $don->petugas = $user->name;
        $don->pembayaran = $request->pembayaran;
        $don->update();
        
    return response()->json([
        "status"=>"sukses"
        ]);
    }

}
public function getpetugas(){
    // $kota = Auth::user()->kota;
    $user = UserKolek::where('aktif', 1)->where('kolektor', '!=', null)->where('id_kantor', Auth::user()->id_kantor)->get();
    $data = [];
    if(count($user) > 0){
    foreach($user as $x => $v){
        $data['data'][] = [
            'id' => $v->id,
            'name' => $v->name,
        ];
    }
        }else{
          $data['data'] = []; 
        }
    return($data);
}

public function getjalur(){
    // $kota = Auth::user()->kota;
    if(Auth::user()->kolektor == 'agenjalur'){
        $jalur = Jalur::where('id_agen', Auth::user()->id)->get();
    }else{
        $jalur = Jalur::where('id_kantor', Auth::user()->id_kantor)->get();
    }
    $data = [];
    if(count($jalur) > 0){
    foreach($jalur as $x => $v){
        $data['data'][] = [
            'id_jalur' => $v->id_jalur,
            'nama_jalur' => $v->nama_jalur,
            'kota' => $v->kota,
        ];
    }
        }else{
          $data['data'] = []; 
        }
    return($data);
}

    public function testo(){
        $tj = Tunjangan::select('mindon','jumbul')->first();
        $now = '2023-11-01';
        $bulan_now = date('Y-m-t', strtotime('-1 month', strtotime($now)));
        $interval = date('Y-m-01', strtotime('-'.$tj->jumbul.' month', strtotime($now)));
        // BETWEEN '$interval' AND '$bulan_now'
        // SUM(IF(donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(tanggal) BETWEEN '$bulan_now' AND '$interval', jumlah, 0 )) as ju,
        // COUNT(IF(DATE_FORMAT(transaksi.tanggal, '%m-%Y') >= '$interval' AND DATE_FORMAT(transaksi.tanggal, '%m-%Y') <= '$bulan_now' AND transaksi.jumlah >= '$tj->mindon' AND donatur.status != 'Ditarik' AND donatur.status != 'Off', 1, 0)) AS ngitung,
        $datas = Donatur::selectRaw("DATE_FORMAT(transaksi.tanggal, '%Y-%m') as bulan, id_donatur, transaksi.id_kantor,
                    SUM(IF(donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now' AND donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83)), jumlah, 0 )) as ju
                    ")
                ->join('transaksi','donatur.id','=','transaksi.id_donatur')
                    ->whereIn('donatur.id', function($q){
                    $q->select('id_don')->from('prosp')->where('ket','closing');
                })
                
                // ->whereRaw("donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83)")
                
                ->whereRaw("donatur.id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83)) AND donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now'")
                ->groupBy('id_donatur','bulan')
                ->get();
                
                
        // $targetAmount = $tj->mindon;
        $targetAmount = 6000;
        $targetAmount2 = 4000;
        $jumbul = $tj->jumbul;
         
        $result = [];
        $count = 0;
        $sepong = [];
        $coy = [];
        $result2 = [];
        $result3 = [];
        
        $groupedData = collect($datas)->groupBy('id_donatur')->toArray();
        $tt = [];
        
        foreach ($groupedData as $donatur => $donaturData) {
            
            $kon = count(array_column($donaturData, 'bulan'));
            $hasil = count(array_filter($donaturData, function ($item) use ($targetAmount) {
                    return $item['ju'] <  $targetAmount;
            }));
            
            $hasil2 = count(array_filter($donaturData, function ($item) use ($targetAmount2) {
                    return $item['ju'] <  $targetAmount2;
            }));
            
            // $result[] = [
            //     "bulan" => array_column($donaturData, 'bulan'),
            //     "kantor" => $donaturData[0]['id_kantor'],
            //     "id_donatur" => $donaturData[0]['id_donatur'],
            //     // "kolektor" => $donaturData[0]['kolektor'],
            //     "jumlah" => array_column($donaturData, 'ju'),
            //     "count_bulan" => $kon,
            //     'donasi_lebih_dari_'.$targetAmount =>  $hasil
                
            // ];
            
            if($kon == $jumbul){
                if($hasil == $jumbul){
                    
                    $result2[] = [
                        "id_donatur" => $donaturData[0]['id_donatur'],
                        'donasi_lebih_dari_'.$targetAmount =>  $hasil,
                        'kantor' => $donaturData[0]['id_kantor']
                    ];
                    
                    //  Donatur::find($donaturData[0]['id_donatur'])->update(['warning' => 1]);
                }
                
                if($hasil2 == $jumbul){
                    
                    $result3[] = [
                        "id_donatur" => $donaturData[0]['id_donatur'],
                        'donasi_lebih_dari_'.$targetAmount2 =>  $hasil2,
                        'kantor' => $donaturData[0]['id_kantor']
                    ];
                    
                    //  Donatur::find($donaturData[0]['id_donatur'])->update(['warning' => 1]);
                }
            }
            
        }
        
        $tt2 = [];
        
        $ehe = collect($result2)->groupBy('kantor')->toArray();
        
        $eho = collect($result3)->groupBy('kantor')->toArray();
        
        foreach ($ehe as $do => $don) {
            $sek = count(array_column($don, 'donasi_lebih_dari_6000'));
            $sek2 = count(array_column($don, 'donasi_lebih_dari_4000'));
            $tt[] = [
                'kantor' => Kantor::find($do)->unit,
                'donasi_kurang_dari_6000_'.$jumbul.'_bulan_berturut_turut' => $sek,
            ];
        }
        
        foreach ($eho as $di => $din) {
            $sek2 = count(array_column($din, 'donasi_lebih_dari_4000'));
            $tt2[] = [
                'kantor' => Kantor::find($di)->unit,
                'donasi_kurang_dari_4000_'.$jumbul.'_bulan_berturut_turut' => $sek2,
            ];
        }
        
        return ([$tt, $tt2]);
        
    }

public function postdon(Request $request)
{
    
$prog = $request->program != '%5B%5D' ?  json_decode (urldecode($request->program)) : '';
$sum = $request->sumber != '%5B%5D' ?  json_decode (urldecode($request->sumber)) : '';
$stat = $request->stat_prog != '%5B%5D' ?  json_decode (urldecode($request->stat_prog)) : '';
$idpeg = $request->id_peg != '%5B%5D' ?  json_decode (urldecode($request->id_peg)) : '';
$idpros = $request->id_pros != '%5B%5D' ?  json_decode (urldecode($request->id_pros)) : '';
$ket = $request->ket != '%5B%5D' ?  json_decode (urldecode($request->ket)) : '';
$tglfol = $request->tgl_fol != '%5B%5D' ?  json_decode (urldecode($request->tgl_fol)) : '';

// $progs = ['program' => $prog, 'sumdan' => $sum, 'status' => $stat];
    $program = $prog != '' ? serialize($prog) : null;
    $sumdan = $sum != '' ? serialize($sum) : null;
    $statprog = $stat != '' ? serialize($stat) : null;
    // $id_peg = $idpeg != '' ? serialize($idpeg) : null;
    // $id_prosp = $idpros != '' ? serialize($idpros) : null;
    // $ket = $kt != '' ? serialize($kt) : null;
    
if($request->id != ''){
    $data = Donatur::find($request->id);
    // $data->petugas = Auth::user()->name;
    $data->id_so = Auth::user()->id_karyawan;
    $data->id_kantor = Auth::user()->id_kantor;
    $data->nama = $request->nama;
    $data->email = $request->email;
    $data->jk = $request->jk;
    $data->tahun_lahir = $request->tahun_lahir;
    $data->pekerjaan = $request->pekerjaan;
    $data->latitude = $request->latitude;
    $data->longitude = $request->longitude;
    $data->alamat = $request->alamat;
    $data->no_hp = $request->no_hp;
    $data->pembayaran = $request->pembayaran == '' ? $data->pembayaran : $request->pembayaran;
    $data->jalur = $request->jalur;
    $data->provinsi = $request->provinsi;
    $data->kota = $request->kota;
    $data->status = 'belum dikunjungi';
    // $data->tgl_fol = $request->ket == 'open' ? $request->tgl_follow : date('Y-m-d');
    // $data->ket = $request->keterangan;
    $data->jenis_donatur = $request->jenis_donatur;
    $data->orng_dihubungi = $request->orng_dihubungi;
    $data->jabatan = $request->jabatan;
    $data->nohap = $request->nohap;
    $data->statprog = $statprog;
    $data->program = $program;
    $data->id_sumdan = $sumdan;
    // $data->id_peg = $id_peg;
    $data->token = $request->token;
    
    if($request->gambar != ''){
    $data->gambar_donatur = $request->file('gambar')->getClientOriginalName();
    $request->file('gambar')->move('gambarDonatur',$data->gambar_donatur);
    }
    $data->update();
    
    // $don = Donatur::where('token', $request->token)->first();
    
     for($i = 0; $i<count($prog); $i++){
        if($idpros[$i] == 0){
            $data = new Prosp;
            $data->id_peg = $idpeg[$i];
            $data->id_don = $request->id;
            $data->id_prog = $prog[$i];
            $data->id_sumdan = $sum[$i];
            $data->ket = $ket[$i];
            $data->status = $stat[$i];
            $data->tgl_fol = $ket[$i] == 'open' ? $tglfol[$i] : date('Y-m-d');
            $data->save();
        }else{
            $data = Prosp::find($idpros[$i]);
            if($data->status != $stat[$i]){
            $data->id_peg = Auth::user()->id;
            $data->id_don = $request->id;
            $data->id_prog = $prog[$i];
            $data->id_sumdan = $sum[$i];
            $data->ket = $ket[$i];
            $data->status = $stat[$i];
            if($ket[$i] == 'open'){
                $data->tgl_fol = $tglfol[$i];
            }else if($data->ket == $ket[$i]){
                $data->tgl_fol = $data->tgl_fol;
            }else{
                $data->tgl_fol = date('Y-m-d');
            }
            $data->update();
            }
        }
    }
}else{
    $data = new Donatur;
    $data->petugas = Auth::user()->name;
    $data->id_so = Auth::user()->id_karyawan;
    $data->id_kantor = Auth::user()->id_kantor;
    $data->id_koleks = Auth::user()->id;
    $data->nama = $request->nama;
    $data->email = $request->email;
    $data->jk = $request->jk;
    $data->tahun_lahir = $request->tahun_lahir;
    $data->pekerjaan = $request->pekerjaan;
    $data->latitude = $request->latitude;
    $data->longitude = $request->longitude;
    $data->alamat = $request->alamat;
    $data->no_hp = $request->no_hp;
    $data->pembayaran = $request->pembayaran == '' ? '-' : $request->pembayaran;
    $data->jalur = $request->jalur;
    $data->provinsi = $request->provinsi;
    $data->kota = $request->kota;
    $data->status = 'belum dikunjungi';
    // $data->tgl_fol = $request->ket == 'open' ? $request->tgl_follow : date('Y-m-d');
    // $data->ket = $request->keterangan;
    $data->jenis_donatur = $request->jenis_donatur;
    $data->orng_dihubungi = $request->orng_dihubungi;
    $data->jabatan = $request->jabatan;
    $data->nohap = $request->nohap;
    $data->program = $program;
    $data->id_sumdan = $sumdan;
    $data->statprog = $statprog;
    // $data->id_peg = $idpeg;
    $data->token = $request->token;
    
    if($request->gambar != ''){
    $data->gambar_donatur = $request->file('gambar')->getClientOriginalName();
    $request->file('gambar')->move('gambarDonatur',$data->gambar_donatur);
    }
    $data->save();
    
    $don = Donatur::where('token', $request->token)->first();
    
    for($i = 0; $i<count($prog); $i++){
            $data = new Prosp;
            $data->id_peg = $idpeg[$i];
            $data->id_don = $don->id;
            $data->id_prog = $prog[$i];
            $data->id_sumdan = $sum[$i];
            $data->ket = $ket[$i];
            $data->status = $stat[$i];
            $data->tgl_fol = $ket[$i] == 'open' ? $tglfol[$i] : date('Y-m-d');
            
            $data->save();
        }
    // $pros = new Prospek;
    // $pros->nama = $don->nama;
    // $pros->id_don = $don->id;
    // $pros->old_program = null;
    // $pros->old_id_sumdan = null;
    // $pros->old_statprog = null;
    // $pros->program = $program;
    // $pros->id_sumdan = $sumdan;
    // $pros->statprog = $statprog;
    // $pros->petugas = Auth::user()->name;
    // $pros->id_so = Auth::user()->id_karyawan;
    
    // $pros->save();
}
return response()->json(
    [
        "status"=>"sukses",
        "data"=>$data
    ]
    );
}

public function kondon($id, Request $request){
    
// Donatur::find($id)->update(['acc' => $request->kondisi]);

$konprog = $request->konprog != '%5B%5D' ?  json_decode (urldecode($request->konprog)) : '';
$idpros = $request->id_pros != '%5B%5D' ?  json_decode (urldecode($request->id_pros)) : '';
$ket = $request->ket != '%5B%5D' ?  json_decode (urldecode($request->ket)) : '';
$tglfol = $request->tgl_fol != '%5B%5D' ?  json_decode (urldecode($request->tgl_fol)) : '';

    for($i = 0; $i<count($idpros); $i++){
        // if($idpros[$i] == 0){
            Prosp::find($idpros[$i])->update(['konprog' => $konprog[$i]]);
        // }
    }
        // else{
        //     $data = Prosp::find($idpros[$i]);
        //     $data->id_peg = $idpeg[$i];
        //     $data->id_don = $request->id;
        //     $data->id_prog = $prog[$i];
        //     $data->id_sumdan = $sum[$i];
        //     $data->ket = $ket[$i];
        //     $data->status = $stat[$i];
        //     if($ket[$i] == 'open'){
        //         $data->tgl_fol = $tglfol[$i];
        //     }else if($data->ket == $ket[$i]){
        //         $data->tgl_fol = $data->tgl_fol;
        //     }else{
        //         $data->tgl_fol = date('Y-m-d');
        //     }
        //     $data->update();
        // }
// $don = Donatur::find($id);

// dd($don);
// if($request->kondisi == 1){
//     $data = new Donatur;
//     $data->petugas = $don->petugas;
//     $data->id_so = $don->id_so;
//     $data->id_kantor = $don->id_kantor;
//     $data->nama = $don->nama;
//     $data->email = $don->email;
//     $data->jk = $don->jk;
//     $data->tahun_lahir = $don->tahun_lahir;
//     $data->pekerjaan = $don->pekerjaan;
//     $data->latitude = $don->latitude;
//     $data->longitude = $don->longitude;
//     $data->alamat = $don->alamat;
//     $data->no_hp = $don->no_hp;
//     $data->pembayaran = $don->pembayaran;
//     $data->jalur = $don->jalur;
//     $data->kota = $don->kota;
//     $data->status = 'belum dikunjungi';
//     $data->tgl_fol = $don->tgl_fol;
//     $data->ket = $don->keterangan;
//     $data->jenis_donatur = $don->jenis_donatur;
//     $data->orng_dihubungi = $don->orng_dihubungi;
//     $data->jabatan = $don->jabatan;
//     $data->nohap = $don->nohap;
//     $data->program = $don->program;
//     $data->id_sumdan = $don->id_sumdan;
//     $data->gambar_donatur = $don->gambar_donatur;
//     $data->id_temp = $don->id;
//     $data->save();
// }

$laphub = new LapHub;
$laphub->id_karyawan = Auth::user()->id_karyawan;
// $laphub->pembayaran = $request->pembayaran;
// $laphub->tgl_fol = $request->ket == 'open' ? $request->tgl_fol : date('Y-m-d');
$laphub->ket = $request->ket;
$laphub->deskripsi = $request->deskripsi;
// $laphub->jalur = $request->jalur;
// $laphub->program = $program;
// $laphub->id_sumdan = $sumdan;
$laphub->id_don = $id;

if($request->bukti != null){
$laphub->bukti = $request->file('bukti')->getClientOriginalName();
$request->file('bukti')->move('lampiranLaporan',$laphub->bukti);
}
$laphub->save();

    return response()->json(
        [
            "status"=>"sukses",
            // "data"=>$data
        ]
    );
}

public function updonatur(Request $request, $id)
{
    $data = Donatur::find($id);
    $data->nama = $request->nama;
    $data->latitude = $request->latitude;
    $data->longitude = $request->longitude;
    $data->alamat = $request->alamat;
    $data->no_hp = $request->no_hp;
    $data->pembayaran = $request->pembayaran;
    $data->jalur = $request->jalur;
    $data->kota = $request->kota;
    $data->status = 'belum dikunjungi';
    $data->tgl_fol = $request->tgl_follow;
    $data->ket = $request->keterangan;
    $data->nohap = $request->nohap;
    $data->orng_dihubungi = $request->orng_dihubungi;
    $data->jabatan = $request->jabatan;
    $data->email = $request->email;
    
    if($request->gambar != ''){
    $data->gambar_donatur = $request->file('gambar')->getClientOriginalName();
    $request->file('gambar')->move('gambarDonatur',$data->gambar_donatur);
    }
    
    $data->update();

    return response()->json([
        "status"=>"sukses"
        ]);
}

public function dontempnow(){
    $kerja = Donatur::whereIn('id', function($query){
                        $query->select('id_don')->from('prosp')->where('id_peg', Auth::user()->id)
                                ->whereDate('created_at','=', date('Y-m-d'));
                    })->get();
    return fractal($kerja, new DonIdTransformer(['kon' => 'Id', 'id' => Auth::user()->id]))->toArray();
}
public function donopen(){
    $kerja = Donatur::where('ket','open')->where('id_so', Auth::user()->id_karyawan)->get();
    return fractal($kerja, new AssisoTransformer())->toArray();
}
public function donalliso(){
    $kerja = Donatur::where('id_so', Auth::user()->id_karyawan)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get();
    return fractal($kerja, new DonTransformer(['kon' => 'Don']))->toArray();
}
public function rejectdon($tgl1,$tgl2){
    $kerja = Donatur::join('laphub', 'laphub.id_don', '=', 'donatur.id')->select('donatur.*', 'laphub.deskripsi', 'laphub.id AS id_laphub')->where('donatur.id_so', Auth::user()->id_karyawan)->where('donatur.acc', 2)->whereDate('donatur.created_at','>=', $tgl1)->whereDate('donatur.created_at','<=', $tgl2)->get();
    return fractal($kerja, new DonTransformer(['kon' => 'Don']))->toArray();
}

public function donrangeisoall($stat, $tanggal1, $tanggal2){
    if($stat == 0){
        $ket = 0;
    }else if($stat == 1){
        $ket = 'open';
    }else if($stat == 2){
        $ket = 'closing';
    }else if($stat == 3){
        $ket = 'cancel';
    }
    
    if($ket == 0){
        $kerja = Donatur::whereIn('id', function($query) use ($ket, $tanggal1, $tanggal2){
                            $query->select('id_don')->from('prosp')->where('id_peg', Auth::user()->id)
                                    ->whereDate('created_at','>=', $tanggal1)->whereDate('created_at','<=', $tanggal2);
                        })
                ->get();
        return fractal($kerja, new DonIdTransformer(['id' => Auth::user()->id]))->toArray();
    }else{
        $kerja = Donatur::whereIn('id', function($query) use ($ket, $tanggal1, $tanggal2){
                            $query->select('id_don')->from('prosp')->where('id_peg', Auth::user()->id)->where('ket', $ket)
                                    ->whereDate('tgl_fol','>=', $tanggal1)->whereDate('tgl_fol','<=', $tanggal2);
                        })
                ->get();
        return fractal($kerja, new DonaturTransformer(['kon' => 'Donatur', 'ket' => $ket, 'id' => Auth::user()->id]))->toArray();
    }
    // $kerja = Donatur::where('id_so', 1912919)
    //         ->where(function($query) use ($stat, $tanggal1, $tanggal2){
    //             if($stat == 0){
    //                 $query->whereDate('created_at','>=', $tanggal1)->whereDate('created_at','<=', $tanggal2);
    //             }else if($stat == 1){
    //                 $query->whereDate('tgl_fol','>=', $tanggal1)->whereDate('tgl_fol','<=', $tanggal2)->where('ket', 'open');
    //             }else if($stat == 2){
    //                 $query->whereDate('tgl_fol','>=', $tanggal1)->whereDate('tgl_fol','<=', $tanggal2)->where('ket', 'closing');
    //             }else if($stat == 3){
    //                 $query->whereDate('tgl_fol','>=', $tanggal1)->whereDate('tgl_fol','<=', $tanggal2)->where('ket', 'cancel');
    //             }
    //         })
    //         ->get();
    // return fractal($kerja, new AssisoTransformer())->toArray();
}
public function getdontok($tok){
    $kerja = Donatur::where('token',$tok)->get();
    return fractal($kerja, new DonaturTransformer(['kon' => 'Donatur', 'ket' => 'closing', 'id' => Auth::user()->id]))->toArray();
}
public function warfolup(){
    $kerja = Donatur::whereDate('tgl_fol',date('Y-m-d'))->where('id_so', Auth::user()->id_karyawan)->where('ket','open')->get();
    return fractal($kerja, new DonTransformer(['kon' => 'Don']))->toArray();
}
public function getdonid($id, $ket){
    $kerja = Donatur::where('donatur.id', $id)->get();
    
    // $program = $user->program != null ? unserialize($user->program) : [];
    // $sumdan = $user->id_sumdan != null ? unserialize($user->id_sumdan) : [];
    
    // $nama_prog = [];
    // foreach($program as $key => $val){
    //     $y = Prog::where('id_program', $val)->first();
    //     $nama_prog[] = 
    //         $y->program;
    // }
    
    // // dd($nama_prog);
    
    // $data['data'][] = [
    //             'id' => $user->id,
    //             'id_transaksi' => $user->id_transaksi,
    //             'tanggal' => $user->tanggal,
    //             'petugas' => $user->petugas,
    //             'nama' => $user->nama,
    //             'email' => $user->email,
    //             'np' => $user->np,
    //             'status' => $user->status,
    //             'jalur' => $user->jalur,
    //             'latitude' => $user->latitude,
    //             'longitude' => $user->longitude,
    //             'alamat' => $user->alamat,
    //             'tgl_kolek' => $user->tgl_kolek,
    //             'pembayaran' => $user->pembayaran,
    //             'program' => $program,
    //             'nama_program' => $nama_prog,
    //             'id_sumdan' => $sumdan,
    //             'no_hp' => $user->no_hp,
    //             'nohap' => $user->nohap,
    //             'orng_dihubungi' => $user->orng_dihubungi,
    //             'jabatan' => $user->jabatan,
    //             'setoran' => $user->setoran,
    //             'kota' => $user->kota,
    //             'bukti' => $user->bukti,
    //             'acc' => $user->acc,
    //             'dikolek' => $user->dikolek,
    //             'gambar_donatur' => $user->gambar_donatur,
    //             'registered' => $user->created_at->format('d M Y'),
    //             'updated' => $user->updated_at->format('d M Y'),
    //             'date' => $user->created_at->format('Y-m-d'),
    //             'warning' => $user->warning,
    //             'ket' => $user->ket,
    //             'jenis_donatur' => $user->jenis_donatur,
    //             'gambar' => $user->gambar_donatur,
    //             'tgl_fol' => $user->tgl_fol,
    //         ];
            
    // return($data);
    return fractal($kerja, new DonaturTransformer(['kon' => 'Donatur', 'ket' => $ket, 'id' => Auth::user()->id]))->toArray();
}

public function donid($id){
    $kerja = Donatur::where('donatur.id', $id)->get();
    
    return fractal($kerja, new DonIdTransformer(['kon' => 'Id', 'id' => Auth::user()->id]))->toArray();
}
// public function donspv($ket, $acc){
//     $kerja = Donatur::where('ket', $ket)->where('acc', $acc)->get();
//     return fractal($kerja, new AssisoTransformer())->toArray();
// }

public function listlapfol($id, $tanggal1, $tanggal2){
    $lapfol = LapFol::join('users', 'lap_folup.id_karyawan', '=', 'users.id_karyawan')->join('donatur', 'lap_folup.id_don', '=', 'donatur.id')
            ->select('lap_folup.*', 'users.name', 'donatur.nama')
            ->whereDate('lap_folup.created_at','>=', $tanggal1)->whereDate('lap_folup.created_at','<=', $tanggal2)
            ->where('users.id_spv', $id)
            ->get();
    return fractal($lapfol, new LapFolTransformer())->toArray();
}

public function postlapfol(Request $request)
{
    
// $prog = $request->program != '%5B%5D' ?  json_decode (urldecode($request->program)) : '';
// $sum = $request->id_sumdan != '%5B%5D' ?  json_decode (urldecode($request->id_sumdan)) : '';

$prog = $request->program != '%5B%5D' ?  json_decode (urldecode($request->program)) : '';
$sum = $request->id_sumdan != '%5B%5D' ?  json_decode (urldecode($request->id_sumdan)) : '';
$stat = $request->stat_prog != '%5B%5D' ?  json_decode (urldecode($request->stat_prog)) : '';
$idpeg = $request->id_peg != '%5B%5D' ?  json_decode (urldecode($request->id_peg)) : '';
$idpros = $request->id_pros != '%5B%5D' ?  json_decode (urldecode($request->id_pros)) : '';
$ket = $request->ket != '%5B%5D' ?  json_decode (urldecode($request->ket)) : '';
$tglfol = $request->tgl_fol != '%5B%5D' ?  json_decode (urldecode($request->tgl_fol)) : '';
    $program = $prog != '' ? serialize($prog) : null;
    $sumdan = $sum != '' ? serialize($sum) : null;
    $keterangan = $ket != '' ? serialize($ket) : null;
    $statprog = $stat != '' ? serialize($stat) : null;

    // $don = Donatur::find($request->id);
    // $don->pembayaran = $request->pembayaran;
    // $don->jalur = $request->jalur;
    // $don->program = $program;
    // $don->id_sumdan = $sumdan;
    // $don->tgl_fol = $request->ket == 'open' ? $request->tgl_fol : $don->tgl_fol;
    // $don->tgl_closing = $request->ket == 'closing' ? date('Y-m-d') : null;
    // $don->ket = $request->ket;
    // $don->update();
     
    Donatur::find($request->id)->update([
        'pembayaran' => $request->pembayaran,
        'jalur' => $request->jalur,
        'program' => $program,
        'id_sumdan' => $sumdan,
        'tgl_fol' => $request->ket == 'open' ? $request->tgl_fol : date('Y-m-d'),
        // 'tgl_closing' => $request->ket == 'closing' ? date('Y-m-d') : null,
        'ket' => $request->ket
        ]);
        
        for($i = 0; $i<count($prog); $i++){
        if($idpros[$i] == 0){
            $data = new Prosp;
            $data->id_peg = $idpeg[$i];
            $data->id_don = $request->id;
            $data->id_prog = $prog[$i];
            $data->id_sumdan = $sum[$i];
            $data->ket = $ket[$i];
            $data->status = $stat[$i];
            $data->tgl_fol = $ket[$i] == 'open' ? $tglfol[$i] : date('Y-m-d');
            $data->save();
        }else if($idpros[$i] != 0 && $prog[$i] == 0){
            $data = Prosp::find($idpros[$i]);
            $data->delete();
        }else{
            $data = Prosp::find($idpros[$i]);
            $data->id_peg = $idpeg[$i];
            $data->id_don = $request->id;
            $data->id_prog = $prog[$i];
            $data->id_sumdan = $sum[$i];
            $data->ket = $ket[$i];
            $data->status = $stat[$i];
            if($ket[$i] == 'open'){
                $data->tgl_fol = $tglfol[$i];
            }else if($ket[$i] == 'closing' | $ket[$i] == 'cancel'){
                $data->tgl_fol = date('Y-m-d');
            }else if($data->ket == $ket[$i]){
                $data->tgl_fol = $data->tgl_fol;
            }else{
                $data->tgl_fol = date('Y-m-d');
            }
            $data->update();
        }
        }
        
$data = new LapFol;
$data->id_karyawan = Auth::user()->id_karyawan;
$data->pembayaran = $request->pembayaran;
$data->tgl_fol = $request->ket == 'open' ? $request->tgl_fol : date('Y-m-d');
$data->ket = $keterangan;
$data->deskripsi = $request->deskripsi;
$data->jalur = $request->jalur;
$data->program = $program;
$data->id_sumdan = $sumdan;
$data->status = $statprog;
$data->id_don = $request->id;

if($request->bukti != null){
$data->bukti = $request->file('bukti')->getClientOriginalName();
$request->file('bukti')->move('lampiranLaporan',$data->bukti);
}

$data->save();

return response()->json(
    [
        "status"=>"sukses",
        "data"=>$data
    ]
    );
}

public function getprog(){
$prog = Prog::where('parent', 'n')->orderBy('program','asc')->get();
return fractal($prog, new ProgTransformer())->toArray();
}


public function getsumdan(){
    $user = SumberDana::where('active', 'y')->get();
    $data = [];
    if(count($user) > 0){
    foreach($user as $x => $v){
        $data['data'][] = [
            'id' => $v->id_sumber_dana,
            'sumber_dana' => $v->sumber_dana,
        ];
    }
        }else{
          $data['data'] = []; 
        }
    return($data);
}

public function getkota(){
    $kota = Kota::all();
    return fractal($kota, new KotaTransformer())->toArray();
}

public function getkotprov(){
    $kota = Kota::join('provinces', 'provinces.province_id', '=', 'cities.province_id')
            ->select('cities.*', 'provinces.name as province')
            ->orderBy('province')
            ->get();
    return fractal($kota, new KotaTransformer())->toArray();
}

public function getprov(){
    $prov = Provinsi::all();
    return fractal($prov, new PropinsiTransformer())->toArray();
}

public function posttrans(Request $request)
{
    $id_trans = $request->id_donatur.date('dmY').Auth::user()->id;
    
    $program = $request->program != '%5B%5D' ?  json_decode (urldecode($request->program)) : [];
        // $progser = $prog != '' ? serialize($prog) : null;
        //     $program = $progser != null ? unserialize($progser) : [];
    $sumdan = $request->id_sumdan != '%5B%5D' ?  json_decode (urldecode($request->id_sumdan)) : [];
        // $sumdser = $sumd != '' ? serialize($sumd) : null;
        //     $sumdan = $sumdser != null ? unserialize($sumdser) : [];
    $status = $request->status != '%5B%5D' ?  json_decode (urldecode($request->status)) : [];
        // $statser = $stat != '' ? serialize($stat) : null;
        //     $status = $statser != null ? unserialize($statser) : [];
    $jumlah = $request->jumlah != '%5B%5D' ?  json_decode (urldecode($request->jumlah)) : [];
        // $jumser = $jum != '' ? serialize($jum) : null;
        //     $jumlah = $jumser != null ? unserialize($jumser) : [];
    $id_pros = $request->id_pros != '%5B%5D' ?  json_decode (urldecode($request->id_pros)) : [];
        // $prosser = $idpros != '' ? serialize($idpros) : null;
        //     $id_pros = $prosser != null ? unserialize($prosser) : [];
    $keterangan = $request->keterangan != '%5B%5D' ?  json_decode (urldecode($request->keterangan)) : [];
    
    // if ($request->hasFile('bukti')) {
    //             // Ambil file gambar asli
    //     $image = $request->file('bukti');
    //     $imageName = $image->getClientOriginalName(); // Nama file disimpan
    //     $namgam = $image->getClientOriginalName(); // Nama file disimpan
    
    //     // Tentukan path untuk menyimpan gambar
    //     $destinationPath = 'gambarUpload'; // Path relatif
    
    //     // Pastikan direktori tujuan ada
    //     if (!file_exists($destinationPath)) {
    //         mkdir($destinationPath, 0755, true); // Buat direktori jika belum ada
    //     }
    
    //     // Dapatkan tipe gambar untuk menentukan cara kompresi
    //     $imageType = $image->getClientOriginalExtension();
        
    //     // Buat resource gambar berdasarkan tipe file
    //     if ($imageType == 'jpeg' || $imageType == 'jpg') {
    //         $source = imagecreatefromjpeg($image->getPathname());
    //         // Coba gunakan kualitas 50 untuk lebih banyak kompresi
    //         imagejpeg($source, $destinationPath . '/' . $imageName, 10); // Kompresi kualitas 50 untuk JPEG
    //     } elseif ($imageType == 'png') {
    //         $source = imagecreatefrompng($image->getPathname());
    //         // Coba kompresi level 9 (maksimum) untuk PNG
    //         imagepng($source, $destinationPath . '/' . $imageName, 9); // Kompresi level 9 untuk PNG
    //     } else {
    //       $request->file('bukti')->move('gambarUpload',$request->file('bukti')->getClientOriginalName());
    //     }
    
    //     // Hapus resource gambar dari memori
    //     imagedestroy($source);
    
    // }

            
    if($request->hasFile('bukti')){
        $namgam = $request->file('bukti')->getClientOriginalName();
        $request->file('bukti')->move('gambarUpload',$namgam);
    }
       
    $coadeb = $request->pembayaran == 'transfer' ? Bank::where('id_bank', $request->bank)->first() : Kantor::where('id', $request->id_kantor)->first();  
    
    if(in_array('Donasi', $status)){
        $don = Transaksi::where('id_transaksi', $id_trans)->get();
        $user = Donatur::find($request->id_donatur);
        if(count($don) >= 1){
            $user->tesum = $user->tesum + array_sum(preg_replace("/[^0-9]/", "", $jumlah));
        }else{
            $user->tesum = array_sum(preg_replace("/[^0-9]/", "", $jumlah));
        }
        $user->status = 'Donasi';
        $user->dikolek = date('d/m/Y');
        $user->acc = 0;
        $user->bukti = $request->file('bukti')->getClientOriginalName();
        $user->user_trans = Auth::user()->id;
        $user->update();
        
        for($i = 0; $i<count($jumlah); $i++){
            $pro = Prog::find($program[$i]);
            $data = new Transaksi;
            $data->id_koleks = Auth::user()->id;
            $data->id_donatur = $request->id_donatur;
            $data->id_transaksi = $id_trans;
            $data->tanggal = date('Y-m-d');
            $data->donatur = $request->donatur;
            $data->pembayaran = $request->pembayaran;
            $data->kolektor = Auth::user()->name;
            $data->alamat = $request->alamat;
            $data->id_program = $program[$i];
            $data->id_sumdan = $sumdan[$i];
            $data->id_pros = $id_pros[$i];
            $data->keterangan = $keterangan[$i] == '0' ? NULL : $keterangan[$i];
            $data->subprogram = $pro->program;
            // $data->dp = $pro->dp;
            $data->id_kantor = $request->id_kantor;
            $data->id_bank = $request->bank;
            $data->coa_kredit = $user->jenis_donatur == 'entitas' ? $pro->coa_entitas : $pro->coa_individu;
            $data->coa_debet = $coadeb->id_coa;
            $data->via_input = 'transaksi';
            $data->akun = $pro->program;
            $data->ket_penerimaan = 'an: '.$request->donatur.' | '.$pro->program;
            $data->qty = 1;
            $data->user_insert = Auth::user()->id;
            
            // $id_trans = \DB::select("SELECT id_transaksi FROM transaksi WHERE status = 'Tutup' AND id_transaksi = '$request->id_transaksi'");
            // if (count($id_trans) >= 1 && $status[$i] == 'Tutup 2x') {
            //     $status[$i] = 'Tutup';
            // } else {
                $data->status = $status[$i];
            // } 
            
            $data->jumlah = preg_replace("/[^0-9]/", "", $jumlah[$i]);
            $data->kota = $request->kota;
            
            if($request->bukti != null){
            $data->bukti = $request->file('bukti')->getClientOriginalName();
            }
            
            
            $data->save();
        }
    }else{
        for($i = 0; $i<count($jumlah); $i++){
            $pro = Prog::find($program[$i]);
            $don = Transaksi::where('id_transaksi', $id_trans)->get();
            if($status[$i] == 'Tutup'){
            $trandon = Transaksi::where('id_transaksi','!=', $id_trans)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))
                        ->where('status', 'Tutup')->where('id_donatur', $request->id_donatur)->get();
            }else{
            $trandon = [];
            }
            $user = Donatur::find($request->id_donatur);
            if(count($don) >= 1){
                $user->tesum = $user->tesum + preg_replace("/[^0-9]/", "", $jumlah[$i]);
            }else{
                $user->tesum = preg_replace("/[^0-9]/", "", $jumlah[$i]);
            }
            // $user->status = $status[$i];
            $user->status = $status[$i] == 'Tutup' && count($trandon) >= 1 ? 'Tutup 2x' : $status[$i];
            $user->dikolek = date('d/m/Y');
            if($status[$i] == 'Tutup'){
                $user->acc = count($trandon) >= 1 ? 0 : 1; 
                $user->retup = $request->retup;
                $user->ketup = $request->ketup;
            }else{
                $user->acc = 0;
            }
            
            $user->bukti = $request->file('bukti')->getClientOriginalName();
            $user->user_trans = Auth::user()->id;
            // if($status[$i] == 'Ditarik' || $status[$i] == 'Off'){
            //     $user->tgl_nonaktif = date('Y-m-d');
            //     \LogActivity::addToLoghfm(Auth::user()->name . ' Menonaktifkan Data Donatur ' . $user->nama . ' Dengan ID ' . $request->id_donatur , 'kosong', 'donatur', 'update', $request->id_donatur);
            // }
            
            $user->update();
            
            $data = new Transaksi;
            $data->id_koleks = Auth::user()->id;
            $data->id_donatur = $request->id_donatur;
            $data->id_transaksi = $id_trans;
            $data->tanggal = date('Y-m-d');
            $data->donatur = $request->donatur;
            $data->pembayaran = $request->pembayaran;
            $data->kolektor = Auth::user()->name;
            $data->alamat = $request->alamat;
            $data->id_program = $program[$i];
            $data->id_sumdan = $sumdan[$i];
            $data->id_pros = $id_pros[$i];
            $data->keterangan = $keterangan[$i] == '0' ? NULL : $keterangan[$i];
            $data->subprogram = $pro->program;
            // $data->dp = $pro->dp;
            $data->id_kantor = $request->id_kantor;
            $data->id_bank = $request->bank;
            $data->coa_kredit = $user->jenis_donatur == 'entitas' ? $pro->coa_entitas : $pro->coa_individu;
            $data->coa_debet = $coadeb->id_coa;
            $data->via_input = 'transaksi';
            $data->akun = $pro->program;
            $data->ket_penerimaan = 'an: '.$request->donatur.' | '.$pro->program;
            $data->qty = 1;
            $data->user_insert = Auth::user()->id;
            
            // $id_trans = \DB::select("SELECT id_transaksi FROM transaksi WHERE status = 'Tutup' AND id_transaksi = '$request->id_transaksi'");
            // if (count($id_trans) >= 1 && $status[$i] == 'Tutup 2x') {
            //     $status[$i] = 'Tutup';
            // } else {
                // $data->status = $status[$i];
            // } 
            
            $data->status = $status[$i] == 'Tutup' && count($trandon) >= 1 ? 'Tutup 2x' : $status[$i];
            $data->jumlah = preg_replace("/[^0-9]/", "", $jumlah[$i]);
            $data->kota = $request->kota;
            
            if($request->bukti != null){
            $data->bukti = $request->file('bukti')->getClientOriginalName();
            }
            
            
            $data->save();
        }
    }
    
    $sumtran = 0;
    $sumtran = Transaksi::where('id_transaksi', $id_trans)->sum('jumlah');
    Transaksi::where('id_transaksi', $id_trans)->update([
        'subtot' => $sumtran,
        ]);

    return response()->json(
        [
            "status"=>"sukses"
        ]
    );
    
// $data = new Transaksi;
// $data->id_koleks = Auth::user()->id;
// $data->id_donatur = $request->id_donatur;
// $data->id_transaksi = $request->id_transaksi;
// $data->tanggal = date('Y-m-d');
// $data->donatur = $request->donatur;
// $data->pembayaran = $request->pembayaran;
// $data->kolektor = Auth::user()->name;
// $data->alamat = $request->alamat;
// $data->program = $request->program;
// $data->subprogram = $request->subprogram;
// $data->keterangan = $request->keterangan;

// // $id_trans = \DB::select("SELECT id_transaksi FROM transaksi WHERE status = 'Tutup' AND id_transaksi = '$request->id_transaksi'");

// // if (count($id_trans) >= 1 && $request->status == 'Tutup 2x') {
// //     $data->status = 'Tutup';
// // } else {
//     $data->status = $request->status;
// // } 

// $data->jumlah = preg_replace("/[^0-9]/", "", $request->jumlah);
// $data->kota = $request->kota;

// if($request->bukti != null){
// $data->bukti = $request->file('bukti')->getClientOriginalName();
// $request->file('bukti')->move('gambarUpload',$data->bukti);
// }
// // $filename    = $data->bukti->getClientOriginalName();
// // $image_resize = Image::make($data->bukti->getRealPath());              
// // $image_resize->resize(300, 300);
// // $image_resize->save(public_path('gambarUpload/' .$filename));

// $data->save();

//     return response()->json(
//         [
//             "status"=>"sukses"
//         ]
//     );
}

public function assiso(){
    // $kerja = Donatur::where('petugas',Auth::user()->name)->where('status','=','belum dikunjungi')->where('acc','=',1)->orWhere('petugas',Auth::user()->name)->where('status','=','Tutup')->where('acc','=',1)->get();
    
    // $kerja = Auth::user()->kolektor != null && Auth::user()->aktif == 1 ? 
    if(Auth::user()->kolektor != null && Auth::user()->aktif == 1){
         $kerja = Auth::user()->kolektor == 'agen' || Auth::user()->kolektor == 'spv agen'?
         Donatur::where('petugas', Auth::user()->name)->where('status','!=','Ditarik')->get():
         Donatur::where('petugas', Auth::user()->name)->where('status','!=','Ditarik')->where('acc','=',1)->get();
    }else{
        $kerja = Donatur::where('petugas', Str::random(60))->get();
    }
    // : Donatur::where('petugas', Str::random(60))->get();
    
    return fractal($kerja, new DonNewTransformer(['kon' => 'DonKet', 'ket' => 'closing']))->toArray();
    }
    
    public function dondeket($lat, $long, $rad, $jml){
    
    // $radius = $sat == 'm' ? $rad/1000 : $rad;
         $kerja = Donatur::select(\DB::raw("*, 111.2 * DEGREES(ACOS(COS(RADIANS($lat))
                 * COS(RADIANS(latitude))
                 * COS(RADIANS(longitude) - RADIANS($long))
                 + SIN(RADIANS($lat))
                 * SIN(RADIANS(latitude))))
                 AS jarak_km"))->having('jarak_km','<=', $rad)->orderBy('jarak_km', 'asc')->limit($jml)->get();
    // dd($kerja);
    return fractal($kerja, new DonNewTransformer(['kon' => 'Don']))->toArray();
    }
    
public function upprogdon($id, Request $request)
{
    
$prog = $request->program != '%5B%5D' ?  json_decode (urldecode($request->program)) : '';
$sum = $request->id_sumdan != '%5B%5D' ?  json_decode (urldecode($request->id_sumdan)) : '';
$stat = $request->stat_prog != '%5B%5D' ?  json_decode (urldecode($request->stat_prog)) : '';
$idpeg = $request->id_peg != '%5B%5D' ?  json_decode (urldecode($request->id_peg)) : '';
$idpros = $request->id_pros != '%5B%5D' ?  json_decode (urldecode($request->id_pros)) : '';
$ket = $request->ket != '%5B%5D' ?  json_decode (urldecode($request->ket)) : '';
$tglfol = $request->tgl_fol != '%5B%5D' ?  json_decode (urldecode($request->tgl_fol)) : '';
    $program = $prog != '' ? serialize($prog) : null;
    $sumdan = $sum != '' ? serialize($sum) : null;
    $keterangan = $ket != '' ? serialize($ket) : null;
    $statprog = $stat != '' ? serialize($stat) : null;

    
        
        for($i = 0; $i<count($prog); $i++){
        if($idpros[$i] == 0){
            $data = new Prosp;
            $data->id_peg = $idpeg[$i];
            $data->id_don = $id;
            $data->id_prog = $prog[$i];
            $data->id_sumdan = $sum[$i];
            $data->ket = $ket[$i];
            $data->status = $stat[$i];
            $data->tgl_fol = $ket[$i] == 'open' ? $tglfol[$i] : date('Y-m-d');
            $data->created_at = NULL;
            $data->save();
        }else if($idpros[$i] != 0 && $prog[$i] == 0){
            $data = Prosp::find($idpros[$i]);
            $data->delete();
        }else{
            $data = Prosp::find($idpros[$i]);
            $data->id_peg = $idpeg[$i];
            $data->id_don = $id;
            $data->id_prog = $prog[$i];
            $data->id_sumdan = $sum[$i];
            $data->ket = $ket[$i];
            $data->status = $stat[$i];
            if($ket[$i] == 'open'){
                $data->tgl_fol = $tglfol[$i];
            }else if($ket[$i] == 'closing' | $ket[$i] == 'cancel'){
                $data->tgl_fol = date('Y-m-d');
            }else if($data->ket == $ket[$i]){
                $data->tgl_fol = $data->tgl_fol;
            }else{
                $data->tgl_fol = date('Y-m-d');
            }
            $data->update();
        }
        }

return response()->json(
    [
        "status"=>"sukses"
    ]
    );
}

public function getdoncab($kan, $jal){
    $kerja = Donatur::join('tambahan', 'tambahan.id', '=', 'donatur.id_kantor')
            ->select('donatur.*', 'tambahan.kantor_induk', 'tambahan.unit')
            ->where(function($query) use ($kan) {
                // if(Auth::user()->id_kantor != 4){
                if($kan == 'all'){
                    $query->where('donatur.id_kantor', Auth::user()->id_kantor)->orWhere('tambahan.kantor_induk', Auth::user()->id_kantor);
                }else{
                    $query->where('donatur.id_kantor', $kan);
                }
                // }
            })
            ->where(function($query) use ($jal) {
                if($jal != 'all'){
                    $query->where('donatur.id_jalur', $jal);
                }
            })
            ->where('donatur.status','!=', 'Ditarik')->where('donatur.status','!=', 'Off')
            // ->limit(2)
            ->get();
    return fractal($kerja, new DonTransformer(['kon' => 'Don']))->toArray();
}

public function resenddon($id, $id_laphub, Request $request){
    Donatur::find($id)->update([
        'acc' => 0,
        ]);
        
    LapHub::find($id_laphub)->update([
        'feedback' => $request->deskripsi,
    ]);

return response()->json(
    [
        "status"=>"sukses"
    ]
    );
}

public function izinupdon($id, $up, Request $request){
    $don = Donatur::find($id);
    
    if($up == 'lokasi'){
        $rupdon = new RUpDon;
        $rupdon->id_don = $id;
        $rupdon->nama = $don->nama;
        $rupdon->alamat = $don->alamat;
        $rupdon->latitude = $don->latitude;
        $rupdon->longitude = $don->longitude;
        $rupdon->save();
        
        $don->latitude = NULL;
        $don->latitude = NULL;
        $don->update();
    }else if($up == 'no_hp'){
        $rupdon = new RUpDon;
        $rupdon->id_don = $id;
        $rupdon->nama = $don->nama;
        $rupdon->alamat = $don->alamat;
        $rupdon->no_hp = $don->no_hp;
        $rupdon->save();
        
        $don->no_hp = NULL;
        $don->update();
    }else if($up == 'gambar'){
        $rupdon = new RUpDon;
        $rupdon->id_don = $id;
        $rupdon->nama = $don->nama;
        $rupdon->alamat = $don->alamat;
        $rupdon->gambar_donatur = $don->gambar_donatur;
        $rupdon->save();
        
        $don->gambar_donatur = NULL;
        $don->update();
    }
    

return response()->json(
    [
        "status"=>"sukses"
    ]
    );
}

public function getjalurcab($kan){
    // $kota = Auth::user()->kota;
    $jalur = Jalur::leftjoin('donatur','donatur.id_jalur','=','jalur.id_jalur')->where('jalur.id_kantor', $kan)
            ->select(\DB::raw("jalur.id_jalur, jalur.nama_jalur, COUNT(donatur.id_jalur) AS conjal, jalur.id_spv"))
            ->groupBy('jalur.id_jalur', 'jalur.nama_jalur', 'jalur.id_spv')
            ->get();
   
    // $jalur = Jalur::join('tambahan', 'tambahan.id', '=', 'jalur.id_kantor')->where('jalur.id_kantor', Auth::user()->id_kantor)->orWhere('tambahan.kantor_induk', Auth::user()->id_kantor)->get();
    $data = [];
    if(count($jalur) > 0){
    foreach($jalur as $x => $v){
        $data['data'][] = [
            'id_jalur' => $v->id_jalur,
            'nama_jalur' => $v->nama_jalur,
            'conjal' => $v->conjal,
            'id_spv' => $v->id_spv
        ];
    }
        }else{
          $data['data'] = []; 
        }
    return($data);
}

public function getkantorcab(){
    // $kota = Auth::user()->kota;
    $jalur = Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->get();
    $data = [];
    if(count($jalur) > 0){
    foreach($jalur as $x => $v){
        $data['data'][] = [
            'id_kantor' => $v->id,
            'unit' => $v->unit,
            // 'kota' => $v->kota,
        ];
    }
        }else{
          $data['data'] = []; 
        }
    return($data);
}

public function cekdon($cek, $val){
    $kerja = Donatur::whereRaw("$cek LIKE '%$val%'")
            // ->where(function($query) use ($cek) {
            //     if(Auth::user()->kolektor != so && Auth::user()->id_kantor != 4){
            //     $query->where('donatur.id_kantor', Auth::user()->id_kantor)->orWhere('tambahan.kantor_induk', Auth::user()->id_kantor);
            //     }
            // })
            // ->where('donatur.status','!=', 'Ditarik')->where('donatur.status','!=', 'Off')
            ->get();
    return fractal($kerja, new DonTransformer(['kon' => 'Don']))->toArray();
}


public function getbank(){
    // $kota = Auth::user()->kota;
    $bank = Bank::join('tambahan', 'tambahan.id', '=', 'bank.id_kantor')->select('bank.*', 'tambahan.unit')->get();
    $data = [];
    if(count($bank) > 0){
    foreach($bank as $x => $v){
        $data['data'][] = [
            'id_bank' => $v->id_bank,
            'nama_bank' => $v->no_rek.' ('.$v->nama_bank.' - '.$v->unit.')',
            // 'kota' => $v->kota,
        ];
    }
        }else{
          $data['data'] = []; 
        }
    return($data);
}

public function doncan(){
    $kerja = Donatur::where('petugas', 'User Tester')->get();
    $data = [];
    if(count($kerja) > 0){
    foreach($kerja as $x => $user){
        
        $prog = $user->program === 'b:0;' || @unserialize($user->program) !== false ? unserialize($user->program) : []; 
        // $statprog = $user->statprog === 'b:0;' || @unserialize($user->statprog) !== false ? unserialize($user->statprog) : [];  
            // $sumdan = $user->id_sumdan != null ? unserialize($user->id_sumdan) : [];
            
            if($prog == []){
                $nama_prog = [];
                $program = [];
                $sumdan = [];
            }else{
            foreach($prog as $key => $val){
                // foreach($statprog as $key2 => $val2){
                // if($val2 == 0){
                    $y = Prog::where('id_program', $val)->first();
                    // dd($y[]);
                    if($y != null){
                        $nama_prog[] = $y->program;
                        $sokol[] = $y->sokol;
                        $program[] = $y->id_program;
                        $sumdan[] = $y->id_sumber_dana;
                //     }
                // }
                }
            }
            }
            
            // $kantor = ['id_kantor' => $user->id_kantor, 'unit' => $user->unit];
        $data['data'][] = [
            'id' => $user->id,
            'id_laphub' => $user->id_laphub,
            'id_kantor' => $user->id_kantor,
            // 'kantor' => ['id_kantor' => $user->id_kantor, 'unit' => $user->unit],
            'id_transaksi' => $user->id_transaksi,
            'tanggal' => $user->tanggal,
            'petugas' => $user->petugas,
            'nama' => $user->nama,
            'email' => $user->email,
            'deskripsi' => $user->deskripsi,
            'unit' => $user->unit,
            'np' => $user->np,
            'upno' => $user->no_hp == null ? 1 : 0,
            'uplok' => $user->latitude == null | $user->longitude == null ? 1 : 0,
            'upgam' => $user->gambar_donatur == null ? 1 : 0,
            'status' => $user->status,
            'jalur' => $user->jalur,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'alamat' => $user->alamat,
            'tgl_kolek' => $user->tgl_kolek,
            'pembayaran' => $user->pembayaran,
            'program' => $program,
            'nama_program' => $nama_prog,
            // 'sokol' => $sokol,
            'id_sumdan' => $sumdan,
            'feedback' => $user->feedback,
            'no_hp' => $user->no_hp,
            'nohap' => $user->nohap,
            'orng_dihubungi' => $user->orng_dihubungi,
            'jabatan' => $user->jabatan,
            'setoran' => $user->setoran,
            'provinsi' => $user->provinsi,
            'kota' => $user->kota,
            'bukti' => $user->bukti,
            'acc' => $user->acc,
            'dikolek' => $user->dikolek,
            'gambar_donatur' => $user->gambar_donatur,
            'registered' => $user->created_at->format('d M Y'),
            'updated' => $user->updated_at->format('d M Y'),
            'date' => $user->created_at->format('Y-m-d'),
            'warning' => $user->warning,
            'ket' => $user->ket,
            'jenis_donatur' => $user->jenis_donatur,
            'gambar' => $user->gambar_donatur,
        ];
    }
        }else{
          $data['data'] = []; 
        }
    return($data);
    // return fractal($kerja, new AssisoTransformer())->toArray();
}

public function getdontup($retup){
    $kerja = Donatur::whereIn('id_koleks', function($query) use ($retup){
                        $query->select('id')->from('users')->where('id_spv', Auth::user()->id_karyawan);
                    })
                    // ->where(function($query){
                    //     $query->orWhere('status','Tutup 2x');
                    // })
                    ->where('status','Tutup')
                    ->where('retup', $retup)->get();
    return fractal($kerja, new DonTransformer(['kon' => 'Don']))->toArray();
}

public function dontup(){
    $kerja = Donatur::
            whereIn('id_koleks', function($query){
                if(Auth::user()->kolektor == 'kacab'){
                    $query->select('id')->from('users')->where('id_kantor', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor);
                }else{
                    $query->select('id')->from('users')->where('id_spv', Auth::user()->id_karyawan);
                }
            })
            ->where('status','Tutup')->get();
                    
    return fractal($kerja, new DonTransformer(['kon' => 'Don']))->toArray();
}

public function uptupdon($id, Request $request){
    
Donatur::find($id)->update(['status' => 'Tutup 2x', 'acc' => 0]);

    return response()->json(
        [
            "status"=>"sukses",
        ]
    );
}

public function postjalur(Request $request){
    
    $kan = Kantor::where('id', $request->id_kantor)->first();
    
    $data = new Jalur;
    $data->id_kantor = $request->id_kantor;
    $data->nama_jalur = $request->nama_jalur;
    // $data->id_spv = $request->id_spv;
    $data->kota = $kan->unit;
    $data->save();
    
    return response()->json(
        [
            "status"=>"sukses",
        ]
    );
}


public function upjalur($id, Request $request) {
    
    // $kan = Kantor::where('id', $request->id_kantor)->first();
    
    Jalur::find($id)->update([
        'nama_jalur' => $request->nama_jalur,
        // 'id_spv' => $request->id_spv,
        // 'kota' => $kan->unit
        ]);
    return response()->json(
        [
            "status"=>"sukses",
        ]
    );
}

public function deljalur($id) {
    $jal = Jalur::findOrFail($id);
    $jal->delete();
    return response()->json(
        [
            "status"=>"sukses",
        ]
    );
}

public function donatour(){
    $now = date('Y-m-d');
    $kerja = Donatur::whereIn('donatur.id', function($query) {
                        $query->select('id_don')->from('prosp')->where('id_peg', Auth::user()->id);
            })
            // ->leftjoin('prosp', 'prosp.id_don', '=', 'donatur.id')
            // ->leftjoin('prog', 'prosp.id_prog', '=', 'prog.id_program')
            ->whereDate('created_at', $now)
            ->whereNotIn('status', ['Off', 'Ditarik', 'Kotak Hilang'])
            ->get();
    
    return fractal($kerja, new DonaturTransformer(['kon' => 'Donatur', 'ket' => !null ,'id' => Auth::user()->id]))->toArray();
    //  return response()->json(
    //     [
    //         "data" => $kerja,
    //         "status"=>"sukses",
    //     ]
    // );
    // return $kerja;
}

public function dono($ket,$tgl1,$tgl2){
    $kerja = Donatur::
            // all();
            whereIn('id', function($query) use ($ket,$tgl1,$tgl2){
                        $query->select('id_don')->from('prosp')->where('id_peg', Auth::user()->id)->where('ket', $ket)->whereDate('created_at','>=', $tgl1)->whereDate('created_at','<=', $tgl2);
                    })
            // leftjoin('prosp', 'prosp.id_don', '=', 'donatur.id')
            // ->select('donatur.*', 'prosp.id_don', 'prosp.id_sumdan', 'prosp.id_prog', 'prosp.id_peg', 'prosp.ket', 'prosp.status', 'prosp.durasi')
            // ->where('prosp.id_peg', 44)
            
            ->get();
            
    // $data = [];
    // if(count($kerja) > 0){
    // foreach($kerja as $x => $user){
        
    //     $prosp = Prosp::where('id_don', $user->id)->get();
    //         // dd($prosp);
    //         if(count($prosp) == 0){
    //             $id_pros = [];
    //             $nama_prog = [];
    //             $id_peg = [];
    //             $program = [];
    //             $sumdan = [];
    //         }else{
    //         foreach($prosp as $x => $v){
    //             $y = Prog::where('id_program', $v->id_prog)->first();
                
    //             if($v->id_peg == 44 && $v->id_don == $user->id){
    //             $nama_prog[] = $y->program;
    //             $id_pros[] = $v->id;
    //             $id_peg[] = $v->id_peg;
    //             $program[] = $v->id_prog;
    //             $sumdan[] = $v->id_sumdan;
    //             }
    //         }
    //         }
            
    //         // $kantor = ['id_kantor' => $user->id_kantor, 'unit' => $user->unit];
    //     $data['data'][] = [
    //          'id' => $user->id,
    //         'id_laphub' => $user->id_laphub,
    //         'id_kantor' => $user->id_kantor,
    //         // 'kantor' => ['id_kantor' => $user->id_kantor, 'unit' => $user->unit],
    //         'id_transaksi' => $user->id_transaksi,
    //         'tanggal' => $user->tanggal,
    //         'petugas' => $user->petugas,
    //         'nama' => $user->nama,
    //         'email' => $user->email,
    //         'deskripsi' => $user->deskripsi,
    //         'unit' => $user->unit,
    //         'np' => $user->np,
    //         'upno' => $user->no_hp == null ? 1 : 0,
    //         'uplok' => $user->latitude == null | $user->longitude == null ? 1 : 0,
    //         'upgam' => $user->gambar_donatur == null ? 1 : 0,
    //         'status' => $user->status,
    //         'jalur' => $user->jalur,
    //         'latitude' => $user->latitude,
    //         'longitude' => $user->longitude,
    //         'alamat' => $user->alamat,
    //         'tgl_kolek' => $user->tgl_kolek,
    //         'pembayaran' => $user->pembayaran,
    //         'program' => $program,
    //         // 'statprog' => $statprog,
    //         'nama_program' => $nama_prog,
    //         'id_peg' => $id_peg,
    //         'id_sumdan' => $sumdan,
    //         'id_pros' => $id_pros,
    //         'feedback' => $user->feedback,
    //         'retup' => $user->retup,
    //         'ketup' => $user->ketup,
    //         'no_hp' => $user->no_hp,
    //         'nohap' => $user->nohap,
    //         'orng_dihubungi' => $user->orng_dihubungi,
    //         'jabatan' => $user->jabatan,
    //         'setoran' => $user->setoran,
    //         'provinsi' => $user->provinsi,
    //         'kota' => $user->kota,
    //         'bukti' => $user->bukti,
    //         'acc' => $user->acc,
    //         'dikolek' => $user->dikolek,
    //         'gambar_donatur' => $user->gambar_donatur,
    //         'registered' => $user->created_at->format('d M Y'),
    //         'updated' => $user->updated_at->format('d M Y'),
    //         'date' => $user->created_at->format('Y-m-d'),
    //         'warning' => $user->warning,
    //         'ket' => $user->ket,
    //         'jenis_donatur' => $user->jenis_donatur,
    //         'gambar' => $user->gambar_donatur,
    //         'jk' => $user->jk,
    //         'tahun_lahir' => $user->tahun_lahir,
    //         'pekerjaan' => $user->pekerjaan,
    //         'tgl_fol' => $user->tgl_fol,
    //     ];
    // }
    //     }else{
    //       $data['data'] = []; 
    //     }
    // return($data);
    return fractal($kerja, new DonaturTransformer(['kon' => 'Donatur', 'ket' => $ket, 'id' => Auth::user()->id]))->toArray();
}
 
public function donspv($ket, $acc, $tgl1, $tgl2){
    $kerja = Donatur::whereIn('id', function($query) use ($ket, $acc, $tgl1, $tgl2){
                        $query->select('id_don')->from('prosp')
                            ->whereIn('id_peg', function($quer) use ($ket, $acc){
                                $quer->select('id')->from('users')->where('id_spv', Auth::user()->id_karyawan);
                            })
                            ->where('ket', $ket)->where('konprog', $acc)
                            ->whereDate('created_at', '>=', $tgl1)->whereDate('created_at', '<=', $tgl2);
                        })
            ->get();
    
    return fractal($kerja, new DonSpvTransformer(['kon' => 'Spv', 'konprog' => $acc, 'ket' => $ket, 'id' => Auth::user()->id_karyawan, 'tgl1' => $tgl1, 'tgl2' => $tgl2]))->toArray();
}
 
public function doncab($ket, $acc, $tgl1, $tgl2){
    $kerja = Donatur::whereIn('id', function($query) use ($ket, $acc, $tgl1, $tgl2){
                        $query->select('id_don')->from('prosp')
                            ->whereIn('id_peg', function($quer) use ($ket, $acc){
                                $quer->select('id')->from('users')->where('id_kantor', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor);
                            })
                            ->where('ket', $ket)->where('konprog', $acc)
                            ->whereDate('created_at', '>=', $tgl1)->whereDate('created_at', '<=', $tgl2);
                        })
            ->get();
    
    return fractal($kerja, new DonNewTransformer(['kon' => 'Cab', 'konprog' => $acc, 'ket' => $ket, 'id' => Auth::user()->id_kantor, 'tgl1' => $tgl1, 'tgl2' => $tgl2]))->toArray();
}

public function bonsales($id){
    $kar = Karyawan::where('id_karyawan', $id)->first();
    $tgl_awal = date('Y-m-01', strtotime('-4 month', strtotime($kar->tgl_gaji)));
    $tgl_trans = date('Y-m-01', strtotime($kar->tgl_gaji));
    $tanggal = date('Y-m-t', strtotime($kar->tgl_gaji));
    
    $idprosdon = Prosp::join('users', 'prosp.id_peg','=', 'users.id')
                ->select(\DB::raw("id_don"))
                ->where(function($query) use ($tgl_trans, $tanggal) {
                    $query->where('prosp.ket', '!=', 'open')->whereDate('prosp.tgl_fol', '>=', $tgl_trans)->whereDate('prosp.tgl_fol', '<=', $tanggal)
                          ->orWhere('prosp.ket', 'open')->whereDate('prosp.updated_at', '>=', $tgl_trans)->whereDate('prosp.updated_at', '<=', $tanggal);
                })
                ->whereRaw("prosp.created_at IS NOT NULL")
                ->where('users.id_karyawan',$id)
                ->get();
    
    $id_prosdon = [];
    
    if(count($idprosdon) > 0){
        foreach($idprosdon as $x => $v){
            $id_prosdon[] = $v->id_don;
        }
    }
    // return(count($id_prosdon));            
    $prosdon = Prosp::join('users', 'prosp.id_peg','=', 'users.id')
                ->select(\DB::raw("
                        COUNT(DISTINCT IF(prosp.ket = 'closing' AND prosp.created_at IS NOT NULL AND DATE(prosp.tgl_fol) >= '$tgl_trans' AND DATE(prosp.tgl_fol) <= '$tanggal', id_don, NULL)) AS closing,
                        COUNT(DISTINCT IF(prosp.ket = 'open' AND prosp.created_at IS NOT NULL AND DATE(prosp.created_at) >= '$tgl_trans' AND DATE(prosp.created_at) <= '$tanggal', id_don, NULL)) AS open,
                        COUNT(DISTINCT IF(prosp.ket = 'cancel' AND prosp.created_at IS NOT NULL AND DATE(prosp.tgl_fol) >= '$tgl_trans' AND DATE(prosp.tgl_fol) <= '$tanggal', id_don, NULL)) AS cancel
                    "))
                ->where('users.id_karyawan',$id)
                ->first();
    // return($prosdon);            
    $prosp = Transaksi::
            join('prog', 'transaksi.id_program', '=', 'prog.id_program')->join('prosp', 'transaksi.id_pros','=', 'prosp.id')
                ->select(\DB::raw("
                    transaksi.id_donatur, transaksi.donatur, DATE_FORMAT(transaksi.tanggal, '%Y-%m') AS bulan, 
                    transaksi.id_program, transaksi.subprogram, prosp.tgl_fol,
                    SUM(transaksi.jumlah) AS omset, transaksi.pembayaran
                    "))
                ->whereIn('transaksi.id_pros', function($pr) use ($id, $tgl_awal) {
                $pr->select('id')->from('prosp')->whereIn('id_peg', function($query) use ($id) {
                        $query->select('id')->from('users')->where('id_karyawan', $id);
                    })->whereDate('tgl_fol','>=', $tgl_awal)->where('ket', 'closing')->where('status', 1)->where('created_at','!=',NULL)
                    // ->where('konprog', 0)
                    ;
                })
                ->whereDate('transaksi.tanggal','>=', $tgl_trans)
                ->groupBy('transaksi.id_donatur', 'transaksi.donatur', 'bulan', 'transaksi.subprogram', 'transaksi.id_program', 'prosp.tgl_fol', 'transaksi.pembayaran')
                // ->whereMonth('transaksi.created_at', 4)->whereYear('transaksi.created_at', date('Y'))
                ->get();
                
    
    $totbonpo = 0;
    $tothonpo = 0;
    $totbonset = 0;
    $totpo = 0;
        $poin = 0;
        $honpo = 0;
        $bonpo = 0;
        $bonset = 0;
    $data = [];
    $total = [];
    $id_don = [];
    if(count($prosp) > 0){
        foreach($prosp as $x => $v){
            
            $bln = date_diff(date_create($tanggal), date_create($v->tgl_fol));
            // return($bln->m);
            $b = $bln->m;
            $p = Prog::where('id_program', $v->id_program)->first();
            $omst = $v->pembayaran == 'noncash' ? $v->omset*($p->prenoncash/100) : $v->omset; 
            $prog = $p->tes === 'b:0;' || @unserialize($p->tes) !== false ? unserialize($p->tes) : [];
            $honp = $p->honpo === 'b:0;' || @unserialize($p->honpo) !== false ? unserialize($p->honpo) : [];
            $bonp = $p->bonpo === 'b:0;' || @unserialize($p->bonpo) !== false ? unserialize($p->bonpo) : [];
            $bons = $p->bonset === 'b:0;' || @unserialize($p->bonset) !== false ? unserialize($p->bonset) : [];
            $konb = $p->konbon === 'b:0;' || @unserialize($p->konbon) !== false ? unserialize($p->konbon) : [];
            $inhonp = $p->inhonpo === 'b:0;' || @unserialize($p->inhonpo) !== false ? unserialize($p->inhonpo) : [];
            $inbonp = $p->inbonpo === 'b:0;' || @unserialize($p->inbonpo) !== false ? unserialize($p->inbonpo) : [];
            $inbons = $p->inbonset === 'b:0;' || @unserialize($p->inbonset) !== false ? unserialize($p->inbonset) : [];
            $bons2 = $p->bonset2 === 'b:0;' || @unserialize($p->bonset2) !== false ? unserialize($p->bonset2) : [];
            $minp2 = $p->minpo2 === 'b:0;' || @unserialize($p->minpo2) !== false ? unserialize($p->minpo2) : [];
            // dd($bons);
               
            if($inhonp[$b] == 1){
                $honpo = $omst < $p->minpo ? 0: floatval($honp[$b]);
                $poin = $omst < $p->minpo ? 0 : 1;
            }else if($inhonp[$b] == 2){
                $honpo = round(($omst/$p->minpo)*$honp[$b]);
                $poin = $omst/$p->minpo;
            }else{
                $honpo = 0;
                $poin = 0;
            }
            
            if($inbonp[$b] == 1){
                $bonpo = $omst < $p->minpo ? 0 : floatval($bonp[$b]);
            }else if($inbonp[$b] == 2){
                $bonpo = round(($omst/$p->minpo)*$bonp[$b]);
            }else{
                $bonpo = 0;
            }
            
            if($inbons[$b] == 1){
                $bonset = $omst < $p->minpo ? 0 : floatval($bons[$b]);
            }else if($inbons[$b] == 2){
                $bonset = round($omst*($bons[$b]/100)); 
            }else if($inbons[$b] == 3){
                if($omst >= $p->minpo){
                    $bonset = floatval($bons[$b]);
                }else if($omst < $p->minpo && $omst >= floatval($minp2[$b])){
                    $bonset = floatval($bons2[$b]);
                }else{
                    $bonset = 0;
                }
            }else{
                $bonset = 0;
                // $bonset = $inbons[$b] == 1 ? round($omst*($bons[$b]/100)) : floatval($bons[$b]);
            }
            
            if($poin > 0 | $honpo > 0 | $bonpo > 0 | $bonset > 0){
            $id_don[] = $v->id_donatur;
            $data[] = [
                'b' => $b,
                'bulan' => $v->bulan,
                'id_donatur' => $v->id_donatur,
                'donatur' => $v->donatur,
                'subprogram' => $v->subprogram,
                'tgl_fol' => $v->tgl_fol,
                'omset' => $omst,
                'minpo' => $p->minpo,
                'poin' => $poin,
                'honpo' => $honpo,
                'bonpo' => $bonpo,
                'bonset' => $bonset,
                'totbon' => $honpo + $bonpo + $bonset
                // 'kolektif' => $p->kolektif,
            ];
            }
        }
    }else{
      $data = []; 
    }
    
    $tgl_gaji = $kar->tgl_gaji;
    $month = date("m",strtotime($tgl_gaji));
    $year = date("Y",strtotime($tgl_gaji));
    $tj = Tunjangan::first();
    if($kar->jabatan == $tj->sokotak){
        $kolek = Transaksi::leftjoin('users', 'transaksi.id_koleks', '=', 'users.id')
                ->select(\DB::raw("transaksi.tanggal, users.id, 
                    SUM(jumlah) AS jumlah,
                    COUNT(DISTINCT IF(transaksi.subtot >= users.minimal AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) * users.honor AS honor
                    "))
                ->whereMonth('transaksi.tanggal', $month)->whereYear('transaksi.tanggal', $year)
                ->whereNotIn('transaksi.id_donatur', $id_don)
                ->where('users.id_karyawan',$id)
                ->groupBy('tanggal', 'id')
                ->get();
        
        $omset  = 0;
        $honor  = 0;
        for($i=0; $i<count($kolek); $i++){
        $omset  += $kolek[$i]['jumlah'];
        $honor  += $kolek[$i]['honor'];
        }
        
        // return($omset);        
        if($kar->jabatan == $tj->sokotak){
            $honor = $kolek != null ? $honor : 0;
            // $boncap = $kolek != null ? $kolek->boncap : 0;
            if ($omset <= 10000000){
                $bon = $omset * 4/100;
            }elseif ($omset > 10000000 && $omset <= 20000000){
                $bon = ($omset - 10000000) * 5/100 + 400000; 
            }elseif ($omset > 20000000){
                $bon = ($omset - 20000000) * 6/100 + 900000;
            }else{
                $bon = 0;
            }
        }
    }
    // dd($id_don, $data);
    for($i=0; $i<count($data); $i++){
    $totpo  += $data[$i]['poin'];
    $tothonpo  += $data[$i]['honpo'];
    $totbonpo  += $data[$i]['bonpo'];
    $totbonset  += $data[$i]['bonset'];
    }
    
    if($data != []){
        
        if($kar->jabatan == $tj->sokotak){
            $total = [
                    ['nambon' => 'Total Poin',
                     'nominal'=> round($totpo)],
                    ['nambon' => 'Total Honor Poin',
                     'nominal'=> $tothonpo],
                    ['nambon' => 'Total Bonus Poin',
                     'nominal'=> $totbonpo],
                    ['nambon' => 'Total Bonus Omset',
                     'nominal'=> $totbonset],
                    ['nambon' => 'Bonus Omset Kolekting',
                     'nominal'=> round($bon)],
                    ['nambon' => 'Honor Kolekting',
                     'nominal'=> $honor],
                    ['nambon' => 'Total Bonus',
                     'nominal'=> $tothonpo + $totbonpo + $totbonset + round($bon) + $honor],
                ];
        }else{
            $total = [
                    ['nambon' => 'Total Poin',
                     'nominal'=> round($totpo)],
                    ['nambon' => 'Total Honor Poin',
                     'nominal'=> $tothonpo],
                    ['nambon' => 'Total Bonus Poin',
                     'nominal'=> $totbonpo],
                    ['nambon' => 'Total Bonus Omset',
                     'nominal'=> $totbonset],
                    ['nambon' => 'Total Bonus',
                     'nominal'=> $tothonpo + $totbonpo + $totbonset],
                ];
        }
        
    }else{
      $total = []; 
    }
    
    $datfin = [];
    $datfin['jumlah'] = count($data);
    $datfin['id_don'] = $id_don;
    $datfin['data'] = $data;
    $datfin['total'] = $total;
    $datfin['prosdon'] = [
                            ['nampros'  => 'Donatur Closing',
                             'jumlah'   => $prosdon['closing']],
                            ['nampros'  => 'Donatur Open',
                             'jumlah'   => $prosdon['open']],
                            ['nampros'  => 'Donatur Cancel',
                             'jumlah'   => $prosdon['cancel']],
                            ['nampros'  => 'Total Prospek',
                             'jumlah'   => $prosdon['closing'] + $prosdon['open'] + $prosdon['cancel']],
                        ]; 
    return($datfin);
}

    public function getcoaper()
    {
        $coa = COA::where('grup', 'LIKE', '%5%')->get();
        $data['data'] = $coa;
        return ($data);
    }
    
    public function posttrandon(Request $request){
        // return($request);
    $id_trans = $request->id_donatur.date('dmY').Auth::user()->id;
            

    // // if ($request->hasFile('bukti')) {
    // //             // Ambil file gambar asli
    // //     $image = $request->file('bukti');
    // //     $imageName = $image->getClientOriginalName(); // Nama file disimpan
    
    // //     // Tentukan path untuk menyimpan gambar
    // //     $destinationPath = 'gambarUpload'; // Path relatif
    
    // //     // Pastikan direktori tujuan ada
    // //     if (!file_exists($destinationPath)) {
    // //         mkdir($destinationPath, 0755, true); // Buat direktori jika belum ada
    // //     }
    
    // //     // Dapatkan tipe gambar untuk menentukan cara kompresi
    // //     $imageType = $image->getClientOriginalExtension();
        
    // //     // Buat resource gambar berdasarkan tipe file
    // //     if ($imageType == 'jpeg' || $imageType == 'jpg') {
    // //         $source = imagecreatefromjpeg($image->getPathname());
    // //         // Coba gunakan kualitas 50 untuk lebih banyak kompresi
    // //         imagejpeg($source, $destinationPath . '/' . $imageName, 10); // Kompresi kualitas 50 untuk JPEG
    // //     } elseif ($imageType == 'png') {
    // //         $source = imagecreatefrompng($image->getPathname());
    // //         // Coba kompresi level 9 (maksimum) untuk PNG
    // //         imagepng($source, $destinationPath . '/' . $imageName, 9); // Kompresi level 9 untuk PNG
    // //     } else {
    // //       $request->file('bukti')->move('gambarUpload',$request->file('bukti')->getClientOriginalName());
    // //     }
    
    // //     // Hapus resource gambar dari memori
    // //     imagedestroy($source);
    
    // // }
    
    //      return response()->json(
    //     [
    //         "status"=> $request->file('bukti')->getClientOriginalName(),
    //         "id_trans" => $id_trans
    //     ]
    // );
    
    $coadeb = $request->pembayaran == 'transfer' ? Bank::where('id_bank', $request->bank)->first() : Kantor::where('id', $request->id_kantor)->first();  
    
        
        if($request->status == 'Tutup'){
        $trandon = Transaksi::where('id_transaksi','!=', $id_trans)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))
                    ->where('status', 'Tutup')->where('id_donatur', $request->id_donatur)->get();
        }else{
        $trandon = [];
        }
        
        $user = Donatur::find($request->id_donatur);
        
        $pro = Prog::find($request->id_prog);
        
        $data = new Transaksi;
        $data->id_koleks = Auth::user()->id;
        $data->id_donatur = $request->id_donatur;
        $data->id_transaksi = $id_trans;
        $data->tanggal = date('Y-m-d');
        $data->donatur = $request->donatur;
        $data->pembayaran = $request->pembayaran;
        $data->kolektor = Auth::user()->name;
        $data->alamat = $request->alamat;
        $data->id_program = $request->id_prog;
        $data->dp = $pro->dp;
        $data->id_sumdan = $request->id_sumdan;
        $data->id_pros = $request->id_pros;
        $data->keterangan = $request->keterangan == '' ? NULL : $request->keterangan;
        $data->subprogram = $request->namprog;
        $data->id_kantor = $request->id_kantor;
        $data->id_bank = $request->bank;
        $data->coa_kredit = $request->coa_kre;
        $data->coa_debet = $request->pembayaran == 'noncash' ? $request->coaper : $coadeb->id_coa;
        $data->via_input = 'transaksi';
        $data->akun = $request->namprog;
        $data->ket_penerimaan = 'an: '.$request->donatur.' | '.$request->namprog;
        $data->qty = 1;
        $data->user_insert = Auth::user()->id;
        $data->status = $request->status == 'Tutup' && count($trandon) >= 1 ? 'Tutup 2x' : $request->status;
        $data->jumlah = preg_replace("/[^0-9]/", "", $request->jumlah);
        $data->kota = $request->kota;
        $data->id_camp = $request->id_camp == 0 ? NULL : $request->id_camp;
        
        // if($request->bukti != null){
        //     $data->bukti = $request->file('bukti')->getClientOriginalName();
        // }  
        
        if($request->bukti != null){
            $data->bukti = $request->file('bukti')->getClientOriginalName();
            $request->file('bukti')->move('gambarUpload',$data->bukti);
        }
        
        // if($request->hasFile('bukti')){
        //     $data->bukti = $request->file('bukti')->getClientOriginalName();
        //     $request->file('bukti')->move('gambarUpload',$data->bukti);
        // }
           
        $data->save();
        
        $user->status = $request->status == 'Tutup' && count($trandon) >= 1 ? 'Tutup 2x' : $request->status;
        $user->dikolek = date('d/m/Y');
        if($request->status == 'Tutup'){
            $user->acc = count($trandon) >= 1 ? 0 : 1; 
            $user->retup = $request->retup;
            $user->ketup = $request->retup == 2 ? $request->ketup : NULL;
        }else{
            $user->acc = 0;
        }
        $user->bukti = $request->file('bukti')->getClientOriginalName();
        $user->user_trans = Auth::user()->id;
        // if($status[$i] == 'Ditarik' || $status[$i] == 'Off'){
        //     $user->tgl_nonaktif = date('Y-m-d');
        //     \LogActivity::addToLoghfm(Auth::user()->name . ' Menonaktifkan Data Donatur ' . $user->nama . ' Dengan ID ' . $request->id_donatur , 'kosong', 'donatur', 'update', $request->id_donatur);
        // }
        $user->update();
    
    $sumtran = 0;
    $sumtran = Transaksi::where('id_transaksi', $id_trans)->sum('jumlah');
    Transaksi::where('id_transaksi', $id_trans)->update([
        'subtot' => $sumtran,
        ]);
        
    return response()->json(
        [
            "status"=>"sukses",
            "id_trans" => $id_trans
        ]
    );
    }
    
    public function gettrandon($id)
    {
        $trans = Transaksi::where('id_transaksi', $id)->get();
        $data['data'] = $trans;
        return ($data);
    }
    
    public function rwytrans2(){
        
    $rwy = Auth::user()->kolektor != null && Auth::user()->aktif == 1 ? 
    Donatur::where('status','!=','belum dikunjungi')
            ->whereIn('id', function($query){
                        $query->select('id_donatur')->from('transaksi')->where('id_koleks', Auth::user()->id)->where('via_input', 'transaksi')
                                ->whereDate('created_at','=', date('Y-m-d'));
                    })
            ->orderBy('updated_at','desc')->get() :
        Donatur::where('petugas', Str::random(60))->get();
    return fractal($rwy, new DonTransformer(['kon' => 'Don']))->toArray();
    }
    
    public function rwytrans(){
        
    // $rwy = Donatur::where('status','!=','belum dikunjungi')
    //         ->whereIn('id', function($query){
    //                     $query->select('id_donatur')->from('transaksi')->where('id_koleks', Auth::user()->id)->where('via_input', 'transaksi')
    //                             ->whereDate('created_at','=', date('Y-m-d'));
    //                 })
    //         ->orderBy('updated_at','desc')->get();
    
    $dataa = [];
    
    $don = Donatur::selectRaw("donatur.*, t.id_transaksi, t.tanggal")->join('transaksi as t','t.id_donatur','=','donatur.id')
            ->where('t.id_koleks', Auth::user()->id)
            ->where('via_input', 'transaksi')->whereDate('t.created_at','=', date('Y-m-d'))
            ->where('donatur.status','!=','belum dikunjungi')->groupBy('id')->orderBy('donatur.updated_at','desc')->get();
    // return $don;
    foreach($don as $user){
        
        $prosp = Prosp::where('id_don', $user->id)->get();
            // dd($prosp);
            if(count($prosp) == 0){
                $id_prosp = [];
                $nama_prog = [];
                $id_peg = [];
                $program = [];
                $sumdan = [];
                $ket = [];
                $tglfol = [];
                $statprog = [];
                $hide = [];
                $ket_prog = [];
                $kotak = [];
            }else{
                foreach($prosp as $x => $v){
                    // if($v->ket == 'closing' && $v->status == 1 ){
                    $y = Prog::where('id_program', $v->id_prog)->first();
                    // dd($y[]);
                    
                    $nama_prog[] = $y->program;
                    $id_prosp[] = $v->id;
                    $id_peg[] = $v->id_peg;
                    $program[] = $v->id_prog;
                    $sumdan[] = $v->id_sumdan;
                    $kotak[] = $y->kotak;
                    $ket[] = $v->ket;
                    $tglfol[] = $v->tgl_fol;
                    $statprog[] = $v->status;
                    $hide[] = 0;
                    $ket_prog[] = $y->ket;
                }
            }
            
            // $kantor = ['id_kantor' => $user->id_kantor, 'unit' => $user->unit];
        $dataa[] =  [
            'id' => $user->id,
            'id_laphub' => $user->id_laphub,
            'id_kantor' => $user->id_kantor,
            // 'kantor' => ['id_kantor' => $user->id_kantor, 'unit' => $user->unit],
            'id_transaksi' => $user->id_transaksi,
            'tanggal' => $user->tanggal,
            'petugas' => $user->petugas != NULL ? $user->petugas : 'Kosong',
            'nama' => $user->nama,
            'email' => $user->email,
            'deskripsi' => $user->deskripsi,
            'unit' => $user->unit,
            'np' => $user->np,
            'upno' => $user->no_hp == null ? 1 : 0,
            'uplok' => $user->latitude == null | $user->longitude == null ? 1 : 0,
            'upgam' => $user->gambar_donatur == null ? 1 : 0,
            'status' => $user->status,
            'jalur' => $user->jalur,
            'id_jalur' => $user->id_jalur,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'alamat' => $user->alamat,
            'tgl_kolek' => $user->tgl_kolek,
            'pembayaran' => $user->pembayaran,
            'id_pros' => $id_prosp,
            'program' => $program,
            'statprog' => $statprog,
            'nama_program' => $nama_prog,
            'id_peg' => $id_peg,
            'id_sumdan' => $sumdan,
            'ket' => $ket,
            'tgl_fol' => $tglfol,
            'feedback' => $user->feedback,
            'retup' => $user->retup,
            'ketup' => $user->ketup,
            'no_hp' => $user->no_hp,
            'nohap' => $user->nohap,
            'orng_dihubungi' => $user->orng_dihubungi,
            'jabatan' => $user->jabatan,
            'setoran' => $user->setoran,
            'provinsi' => $user->provinsi,
            'kota' => $user->kota,
            'bukti' => $user->bukti,
            'acc' => $user->acc,
            'dikolek' => $user->dikolek,
            'gambar_donatur' => $user->gambar_donatur,
            'tgl_nonaktif' => $user->tgl_nonaktif,
            'tgl_aktif' => $user->tgl_aktif,
            'registered' => $user->created_at->format('d M Y'),
            'updated' => $user->updated_at->format('d M Y'),
            'date' => $user->created_at->format('Y-m-d'),
            'warning' => $user->warning,
            'progres' => 'closing',
            'jenis_donatur' => $user->jenis_donatur,
            'gambar' => $user->gambar_donatur,
            'jk' => $user->jk,
            'tahun_lahir' => $user->tahun_lahir,
            'pekerjaan' => $user->pekerjaan,
            'hide' => $hide,
            'ket_prog' => $ket_prog,
            'id_koleks' => $user->id_koleks,
            'kotak' => $kotak,
        ];
    }
    
            
    // return $dataa;
    return response()->json(
    [
        // "status"=>"sukses",
        "data"=>$dataa
    ]
    );
    
    }
    
    public function getver()
    {
        $trans = Tunjangan::first();
        // $data['data'] = $trans;
        return ($trans);
    }
    
public function prospdon(Request $request){
    
    if($request->id == ''){
        $data = new Donatur;
        $data->jenis_donatur = $request->jenis_donatur;
        $data->nama = $request->nama;
        $data->no_hp = $request->no_hp;
        $data->email = $request->email;
        
        if($request->jenis_donatur == 'personal'){
            $data->jk = $request->jk;
            $data->tahun_lahir = $request->tahun_lahir;
            $data->pekerjaan = $request->pekerjaan;
        }else{
            $data->orng_dihubungi = $request->orng_dihubungi;
            $data->jabatan = $request->jabatan;
            $data->nohap = $request->nohap;
        }
        
        $data->alamat = $request->alamat;
        $data->provinsi = $request->provinsi;
        $data->kota = $request->kota;
        $data->latitude = $request->latitude;
        $data->longitude = $request->longitude;
        
        $data->petugas = Auth::user()->name;
        $data->id_so = Auth::user()->id_karyawan;
        $data->id_kantor = Auth::user()->id_kantor;
        $data->id_koleks = Auth::user()->id;
        $data->pembayaran = $request->pembayaran == '' ? '-' : $request->pembayaran;
        $data->id_jalur = $request->id_jalur;
        $data->jalur = $request->jalur;
        $data->status = 'belum dikunjungi';
        $data->token = $request->token;
        
        if($request->gambar != ''){
        $data->gambar_donatur = $request->file('gambar')->getClientOriginalName();
        $request->file('gambar')->move('gambarDonatur',$data->gambar_donatur);
        }
        
        $data->save();
    }else{
        $data = Donatur::find($request->id);
        $data->status = $data->status == 'Ditarik' | $data->status == 'Off' ? 'belum dikunjungi' : $data->status;
        $data->token = $request->token;
        $data->update();
    }


    $don        = Donatur::where('token', $request->token)->first();
    $id         = $request->id == '' ? $don->id : $request->id;
    $tokpros    = Str::random(60);
        if($request->id_pros == 0){
            $pros = new Prosp;
            $pros->id_don = $don->id;
            $pros->id_peg = Auth::user()->id;
            $pros->id_sumdan = $request->id_sumdan;
            $pros->id_prog = $request->id_prog;
            $pros->ket = $request->ket_prog;
            $pros->status = $request->stat_prog;
            $pros->tokpros = $tokpros;
            // $pros->carfol = $request->carfol;
            $pros->tgl_fol = $request->ket_prog == 'open' ? $request->tgl_fol : date('Y-m-d');
            $pros->save();
        }else{
            $pros = Prosp::find($request->id_pros);
            $pros->id_peg = Auth::user()->id;
            $pros->ket = $request->ket_prog;
            $pros->status = $request->stat_prog;
            $pros->tokpros = $tokpros;
            // $pros->carfol = $request->carfol;
            if($request->ket_prog == 'open'){
                $pros->tgl_fol =  $request->tgl_fol;
            }else if($pros->ket == $request->ket_prog){
                $pros->tgl_fol = $pros->tgl_fol;
            }else{
                $pros->tgl_fol = date('Y-m-d');
            }
            $pros->update();
        }
       
    $id_tokpros = Prosp::where('tokpros', $tokpros)->first();
    
    $data = new LapFol;
    $data->id_karyawan = Auth::user()->id_karyawan;
    $data->id_don = $don->id;
    $data->id_peg = Auth::user()->id;
    $data->id_sumdan = $request->id_sumdan;
    $data->id_prog = $request->id_prog;
    $data->program = $request->id_prog;
    $data->ket = $request->ket_prog;
    $data->status = $request->stat_prog;
    $data->tgl_fol = $request->ket_prog == 'open' ? $request->tgl_fol : date('Y-m-d');
    $data->pembayaran = $request->pembayaran;
    $data->deskripsi = $request->deskripsi;
    $data->jalur = $request->jalur;
    $data->id_pros = $id_tokpros->id;
    // $data->carfol = $request->carfol;
    $data->jenis = 'pros';

    $data->save();
        
    
return response()->json(
    [
        "status" => "sukses",
        "data" => $data,
        "id_donp" => $id,
        "ket_prog" => $request->ket_prog
    ]
    );
}

public function getprostam($id){
    $kerja = Donatur::where('id', $id)->get();
    return fractal($kerja, new DonTransformer(['kon' => 'Don']))->toArray();
}

public function upprosdon(Request $request){
    
        $data = new DonaturTemp;
        $data->jenis_donatur = $request->jenis_donatur;
        $data->nama = $request->nama;
        $data->no_hp = $request->no_hp;
        $data->email = $request->email;
        
        if($request->jenis_donatur == 'personal'){
            $data->jk = $request->jk;
            $data->tahun_lahir = $request->tahun_lahir;
            $data->pekerjaan = $request->pekerjaan;
        }else{
            $data->orng_dihubungi = $request->orng_dihubungi;
            $data->jabatan = $request->jabatan;
            $data->nohap = $request->nohap;
        }
        
        $data->alamat = $request->alamat;
        $data->provinsi = $request->provinsi;
        $data->kota = $request->kota;
        $data->latitude = $request->latitude;
        $data->longitude = $request->longitude;
        $data->id_don = $request->id;
        
        // $data->petugas = Auth::user()->name;
        // $data->id_so = Auth::user()->id_karyawan;
        // $data->id_kantor = Auth::user()->id_kantor;
        // $data->id_koleks = Auth::user()->id;
        // $data->pembayaran = $request->pembayaran == '' ? '-' : $request->pembayaran;
        // $data->id_jalur = $request->id_jalur;
        // $data->jalur = $request->jalur;
        // $data->status = 'belum dikunjungi';
        // $data->token = $request->token;
        
        if($request->gambar != ''){
        $data->gambar_donatur = $request->file('gambar')->getClientOriginalName();
        $request->file('gambar')->move('gambarDonatur',$data->gambar_donatur);
        }
        
        $data->save();

return response()->json(
    [
        "status"=>"sukses",
        "data"=>$data
    ]
    );
}

public function tamjalur(Request $request){
    
    $data = new Jalur;
    $data->id_kantor = Auth::user()->id_kantor;
    $data->nama_jalur = $request->nama_jalur;
    $data->kota = Auth::user()->kota;
    $data->id_agen = Auth::user()->id;
    $data->save();
    
    return response()->json(
        [
            "status"=>"sukses",
        ]
    );
}

public function getprosdon($ket){
    // $kerja = Donatur::
    //         whereIn('id', function($query) use ($ket){
    //                     $query->select('id_don')->from('prosp')->where('id_peg', 44)->where('ket', $ket);
    //                 })
            
    //         ->get();
   
    // return fractal($kerja, new DonNewTransformer(['kon' => 'Donatura', 'ket' => $ket, 'id' => 44]))->toArray();
    // $response = Http::get('https://berbagibahagia.org/api/getcat');
    // return($response);
    $response = Http::post('https://berbagibahagia.org/api/postprov', [
            'name' => 'Sudiroh Gay',
        ]);
    $kerja = Donatur::whereIn('id', function($query){
                        $query->select('id_don')->from('upnoloc')
                            ->where('acc','=', 2)
                            ->whereIn('id_koleks', function($query2){
                                $query2->select('id')->from('users')
                                    ->where(function($query3) {
                                        // if(Auth::user()->kolektor == 'kacab'){
                                            $query3->where('id_kantor', 2)->orWhere('kantor_induk', 2);
                                        // }else{
                                        //     $query3->where('id_spv', Auth::user()->id_karyawan);
                                        // }
                                    })
                                    ;
                            })
                            ;
                    })
                    ->orderBy('updated_at', 'DESC')->get();
    return fractal($kerja, new DonNewTransformer(['kon' => 'Up', 'acc' => 2]))->toArray();
}

public function postfolup(Request $request){
    
    $tokpros    = Str::random(60);
        if($request->id_pros == 0){
            $pros = new Prosp;
            $pros->id_don = $request->id;
            $pros->id_peg = Auth::user()->id;
            $pros->id_sumdan = $request->id_sumdan;
            $pros->id_prog = $request->id_prog;
            $pros->ket = $request->ket_prog;
            $pros->status = $request->stat_prog;
            $pros->tokpros = $tokpros;
            $pros->tgl_fol = $request->ket_prog == 'open' ? $request->tgl_fol : date('Y-m-d');
            $pros->save();
        }else{
            $pros = Prosp::find($request->id_pros);
            $pros->id_peg = Auth::user()->id;
            $pros->ket = $request->ket_prog;
            $pros->status = $request->stat_prog;
            $pros->tokpros = $tokpros;
            $pros->tgl_fol = $request->ket_prog == 'open' ? $request->tgl_fol : date('Y-m-d');
            // if($request->ket_prog == 'open'){
            //     $pros->tgl_fol =  $request->tgl_fol;
            // }else if($pros->ket == $request->ket_prog){
            //     $pros->tgl_fol = $pros->tgl_fol;
            // }else{
            //     $pros->tgl_fol = date('Y-m-d');
            // }
            $pros->update();
        }
       
    $id_tokpros = Prosp::where('tokpros', $tokpros)->first();
    
    $data = new LapFol;
    $data->id_karyawan = Auth::user()->id_karyawan;
    $data->id_don = $request->id;
    $data->id_peg = Auth::user()->id;
    $data->id_sumdan = $request->id_sumdan;
    $data->id_prog = $request->id_prog;
    $data->program = $request->id_prog;
    $data->ket = $request->ket_prog;
    $data->status = $request->stat_prog;
    $data->tgl_fol = $request->ket_prog == 'open' ? $request->tgl_fol : date('Y-m-d');
    $data->pembayaran = $request->pembayaran;
    $data->deskripsi = $request->deskripsi;
    $data->jalur = $request->jalur;
    $data->id_pros = $id_tokpros->id;
    $data->jenis = $request->id_pros == 0 ? 'pros' : 'fol';
    if($request->bukti != ''){
        $data->bukti = $request->file('bukti')->getClientOriginalName();
        $request->file('bukti')->move('lampiranLaporan',$data->bukti);
    }
    $data->carfol = $request->carfol;
    $data->save();
    
    $don = Donatur::find($request->id);
    if($don->pembayaran != $request->pembayaran){
        $don->pembayaran =  $request->pembayaran;
        
        if($don->id_jalur != $request->id_jalur){
            $don->id_jalur =  $request->id_jalur;
            $don->jalur =  $request->jalur;
        }
    $don->update();
    }


return response()->json(
    [
        "status"=>"sukses",
        "data"=>$data
    ]
    );
}

public function donclos($ket, $tgl1, $tgl2){
    $kerja = Donatur::
            // all();
            whereIn('id', function($query) use ($ket, $tgl1, $tgl2){
                        $query->select('id_don')->from('prosp')->where('id_peg', Auth::user()->id)->where('ket', $ket)->whereDate('created_at','>=', $tgl1)->whereDate('created_at','<=', $tgl2);
                    })
            ->get();
    return fractal($kerja, new DonRangeTransformer(['kon' => 'Range', 'ket' => $ket, 'tgl1' => $tgl1, 'tgl2' => $tgl2, 'id' => Auth::user()->id]))->toArray();
}

public function upnoloc2(Request $request, $id)
{
    $user = Donatur::find($id);
    // $getup = [];
    // if($request->no_hp != ''){
    //     $getup = UpNoLoc::where('id_don', $id)->where('jenis', 'no')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'));
    // }else if($request->latitude != ''){
    //     $getup = UpNoLoc::where('id_don', $id)->where('jenis', 'lok')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'));
    // }else if($request->gambar != ''){
    //     $getup = UpNoLoc::where('id_don', $id)->where('jenis', 'gam')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'));
    // }
    
    // if(count($getup) == 0){
    
    $noloc = new UpNoLoc;
    $noloc->nama = $user->nama;
    $noloc->alamat = $user->alamat;
    $noloc->nama = $user->nama;
    $noloc->petugas = Auth::user()->name;
    $noloc->id_koleks = Auth::user()->id;
    $noloc->user_insert = Auth::user()->id;
    
    if($request->no_hp != ''){
        $noloc->no_hp = $user->no_hp;
        $noloc->jenis = 'no';
    }else if($request->latitude != ''){
        $noloc->latitude = $user->latitude;
        $noloc->longitude = $user->longitude;
        $noloc->jenis = 'lok';
    }else if($request->gambar != ''){
        $noloc->gambar_donatur = $user->gambar_donatur;
        $noloc->jenis = 'gam';
    }
    
    $noloc->acc = 2;
    $noloc->id_don = $id;
    $noloc->save();
    
    $user->no_hp = $request->get('no_hp',$user->no_hp);
    $user->latitude = $request->get('latitude',$user->latitude);
    $user->longitude = $request->get('longitude',$user->longitude);
    
    if($request->gambar != ''){
        $user->gambar_donatur = $request->file('gambar')->getClientOriginalName();
        $request->file('gambar')->move('gambarDonatur',$user->gambar_donatur);
    }
    $user->update();

    return response()->json([
        "status"=>"sukses"
        ]);
    // }else{
    //   return response()->json([
    //     "status"=>"gagal"
    //     ]);  
    // }
}

public function postprospdon2(Request $request){
    // return response()->json(['file' => $request->hasFile('gambar'), 'mek'=>'mememeeeee']);
    // return response()->json($request);
    if($request->id == ''){
        $data = new Donatur;
        $data->jenis_donatur = $request->jenis_donatur;
        $data->nama = $request->nama;
        $data->no_hp = $request->no_hp;
        $data->email = $request->email;
        
        if($request->jenis_donatur == 'personal'){
            $data->jk = $request->jk;
            $data->tahun_lahir = $request->tahun_lahir;
            $data->pekerjaan = $request->pekerjaan;
        }else{
            $data->orng_dihubungi = $request->orng_dihubungi;
            $data->jabatan = $request->jabatan;
            $data->nohap = $request->nohap;
        }
        $data->nik = $request->nik;
        $data->kecamatan = $request->kecamatan;
        $data->desa = $request->desa;
        $data->rtrw = $request->rt . '/' . $request->rw;
        $data->alamat_detail = $request->detLainya;
        $data->alamat = $request->detLainya . ', ' . $request->rt . '/' . $request->rw . ', ' . $request->desa . ', ' . $request->kecamatan . ', ' . $request->kota . ', ' . $request->provinsi;
        // $data->alamat = $request->alamat;
        $data->provinsi = $request->provinsi;
        $data->kota = $request->kota;
        $data->latitude = $request->latitude;
        $data->longitude = $request->longitude;
        
        $data->petugas = Auth::user()->name;
        $data->id_so = Auth::user()->id_karyawan;
        $data->id_kantor = Auth::user()->id_kantor;
        $data->id_koleks = Auth::user()->id;
        $data->pembayaran = $request->pembayaran == '' ? '-' : $request->pembayaran;
        $data->id_jalur = $request->id_jalur;
        $data->jalur = $request->jalur;
        $data->status = 'belum dikunjungi';
        $data->token = $request->token;
        
            // if ($request->hasFile('gambar')) {
            //     // Ambil file gambar asli
            //     $image = $request->file('gambar');
            //     $imageName = $image->getClientOriginalName(); // Nama file disimpan
            
            //     // Tentukan path untuk menyimpan gambar
            //     $destinationPath = 'gambarDonatur'; // Path relatif
            
            //     // Pastikan direktori tujuan ada
            //     if (!file_exists($destinationPath)) {
            //         mkdir($destinationPath, 0755, true); // Buat direktori jika belum ada
            //     }
            
            //     // Dapatkan tipe gambar untuk menentukan cara kompresi
            //     $imageType = $image->getClientOriginalExtension();
                
            //     // Buat resource gambar berdasarkan tipe file
            //     if ($imageType == 'jpeg' || $imageType == 'jpg') {
            //         $source = imagecreatefromjpeg($image->getPathname());
            //         // Coba gunakan kualitas 50 untuk lebih banyak kompresi
            //         imagejpeg($source, $destinationPath . '/' . $imageName, 15); // Kompresi kualitas 50 untuk JPEG
            //     } elseif ($imageType == 'png') {
            //         $source = imagecreatefrompng($image->getPathname());
            //         // Coba kompresi level 9 (maksimum) untuk PNG
            //         imagepng($source, $destinationPath . '/' . $imageName, 9); // Kompresi level 9 untuk PNG
            //     } else {
            //       $request->file('gambar')->move('gambarDonatur',$data->gambar_donatur);
            //     }
            
            //     // Hapus resource gambar dari memori
            //     imagedestroy($source);
            
            //     // Simpan nama gambar ke dalam $data atau database
            //     $data->gambar_donatur = $imageName;
            // }
        if($request->hasFile('gambar')){
            $data->gambar_donatur = $request->file('gambar')->getClientOriginalName();
            $request->file('gambar')->move('gambarDonatur',$data->gambar_donatur);
        }
        
        $data->save();
    }else{
        $data = Donatur::find($request->id);
        $data->status = $data->status == 'Ditarik' | $data->status == 'Off' ? 'belum dikunjungi' : $data->status;
        $data->token = $request->token;
        $data->update();
    }


    $don        = Donatur::where('token', $request->token)->first();
    $id         = $request->id == '' ? $don->id : $request->id;
    $tokpros    = Str::random(60);
        if($request->id_pros == 0){
            $pros = new Prosp;
            $pros->id_don = $don->id;
            $pros->id_peg = Auth::user()->id;
            $pros->id_sumdan = $request->id_sumdan;
            $pros->id_prog = $request->id_prog;
            $pros->ket = $request->ket_prog;
            $pros->status = $request->stat_prog;
            $pros->tokpros = $tokpros;
            $pros->carfol = $request->carfol;
            $pros->tgl_fol = $request->ket_prog == 'open' ? $request->tgl_fol : date('Y-m-d');
            $pros->save();
        }else{
            $pros = Prosp::find($request->id_pros);
            $pros->id_peg = Auth::user()->id;
            $pros->ket = $request->ket_prog;
            $pros->status = $request->stat_prog;
            $pros->tokpros = $tokpros;
            $pros->carfol = $request->carfol;
            if($request->ket_prog == 'open'){
                $pros->tgl_fol =  $request->tgl_fol;
            }else if($pros->ket == $request->ket_prog){
                $pros->tgl_fol = $pros->tgl_fol;
            }else{
                $pros->tgl_fol = date('Y-m-d');
            }
            $pros->update();
        }
       
    $id_tokpros = Prosp::where('tokpros', $tokpros)->first();
    
    $data = new LapFol;
    $data->id_karyawan = Auth::user()->id_karyawan;
    $data->id_don = $don->id;
    $data->id_peg = Auth::user()->id;
    $data->id_sumdan = $request->id_sumdan;
    $data->id_prog = $request->id_prog;
    $data->program = $request->id_prog;
    $data->ket = $request->ket_prog;
    $data->status = $request->stat_prog;
    $data->tgl_fol = $request->ket_prog == 'open' ? $request->tgl_fol : date('Y-m-d');
    $data->pembayaran = $request->pembayaran;
    $data->deskripsi = $request->deskripsi;
    $data->jalur = $request->jalur;
    $data->id_pros = $id_tokpros->id;
    $data->carfol = $request->carfol;
    $data->jenis = 'pros';

    $data->save();
        
    
    return response()->json(
        [
            "status" => "sukses",
            "data" => $data,
            "id_donp" => $id,
            "ket_prog" => $request->ket_prog
        ]
        );
}


public function postprospdon(Request $request){
    
    if($request->id == ''){
        $data = new Donatur;
        $data->jenis_donatur = $request->jenis_donatur;
        $data->nama = $request->nama;
        $data->no_hp = $request->no_hp;
        $data->email = $request->email;
        
        if($request->jenis_donatur == 'personal'){
            $data->jk = $request->jk;
            $data->tahun_lahir = $request->tahun_lahir;
            $data->pekerjaan = $request->pekerjaan;
        }else{
            $data->orng_dihubungi = $request->orng_dihubungi;
            $data->jabatan = $request->jabatan;
            $data->nohap = $request->nohap;
        }
        // $data->nik = $request->nik;
        // $data->kecamatan = $request->kecamatan;
        // $data->desa = $request->desa;
        // $data->rtrw = $request->rt . '/' . $request->rw;
        // $data->alamat_detail = $request->alamat_detail;
        // $data->alamat = $request->alamat_detail . ',' . $request->rt . '/' . $request->rw . ',' . $request->desa . ',' . $request->kecamatan . ',' . $request->kota . ',' . $request->provinsi;
        $data->alamat = $request->alamat;
        $data->provinsi = $request->provinsi;
        $data->kota = $request->kota;
        $data->latitude = $request->latitude;
        $data->longitude = $request->longitude;
        
        $data->petugas = Auth::user()->name;
        $data->id_so = Auth::user()->id_karyawan;
        $data->id_kantor = Auth::user()->id_kantor;
        $data->id_koleks = Auth::user()->id;
        $data->pembayaran = $request->pembayaran == '' ? '-' : $request->pembayaran;
        $data->id_jalur = $request->id_jalur;
        $data->jalur = $request->jalur;
        $data->status = 'belum dikunjungi';
        $data->token = $request->token;
        
            // if ($request->hasFile('gambar')) {
            //     // Ambil file gambar asli
            //     $image = $request->file('gambar');
            //     $imageName = $image->getClientOriginalName(); // Nama file disimpan
            
            //     // Tentukan path untuk menyimpan gambar
            //     $destinationPath = 'gambarDonatur'; // Path relatif
            
            //     // Pastikan direktori tujuan ada
            //     if (!file_exists($destinationPath)) {
            //         mkdir($destinationPath, 0755, true); // Buat direktori jika belum ada
            //     }
            
            //     // Dapatkan tipe gambar untuk menentukan cara kompresi
            //     $imageType = $image->getClientOriginalExtension();
                
            //     // Buat resource gambar berdasarkan tipe file
            //     if ($imageType == 'jpeg' || $imageType == 'jpg') {
            //         $source = imagecreatefromjpeg($image->getPathname());
            //         // Coba gunakan kualitas 50 untuk lebih banyak kompresi
            //         imagejpeg($source, $destinationPath . '/' . $imageName, 25); // Kompresi kualitas 50 untuk JPEG
            //     } elseif ($imageType == 'png') {
            //         $source = imagecreatefrompng($image->getPathname());
            //         // Coba kompresi level 9 (maksimum) untuk PNG
            //         imagepng($source, $destinationPath . '/' . $imageName, 9); // Kompresi level 9 untuk PNG
            //     } else {
            //       $request->file('gambar')->move('gambarDonatur',$data->gambar_donatur);
            //     }
            
            //     // Hapus resource gambar dari memori
            //     imagedestroy($source);
            
            //     // Simpan nama gambar ke dalam $data atau database
            //     $data->gambar_donatur = $imageName;
            // }
        if($request->hasFile('gambar')){
            $data->gambar = $request->file('gambar')->getClientOriginalName();
            $request->file('gambar')->move('gambarDonatur',$data->gambar);
        }
        $data->save();
    }else{
        $data = Donatur::find($request->id);
        $data->status = $data->status == 'Ditarik' | $data->status == 'Off' ? 'belum dikunjungi' : $data->status;
        $data->token = $request->token;
        $data->update();
    }


    $don        = Donatur::where('token', $request->token)->first();
    $id         = $request->id == '' ? $don->id : $request->id;
    $tokpros    = Str::random(60);
        if($request->id_pros == 0){
            $pros = new Prosp;
            $pros->id_don = $don->id;
            $pros->id_peg = Auth::user()->id;
            $pros->id_sumdan = $request->id_sumdan;
            $pros->id_prog = $request->id_prog;
            $pros->ket = $request->ket_prog;
            $pros->status = $request->stat_prog;
            $pros->tokpros = $tokpros;
            $pros->carfol = $request->carfol;
            $pros->tgl_fol = $request->ket_prog == 'open' ? $request->tgl_fol : date('Y-m-d');
            $pros->save();
        }else{
            $pros = Prosp::find($request->id_pros);
            $pros->id_peg = Auth::user()->id;
            $pros->ket = $request->ket_prog;
            $pros->status = $request->stat_prog;
            $pros->tokpros = $tokpros;
            $pros->carfol = $request->carfol;
            if($request->ket_prog == 'open'){
                $pros->tgl_fol =  $request->tgl_fol;
            }else if($pros->ket == $request->ket_prog){
                $pros->tgl_fol = $pros->tgl_fol;
            }else{
                $pros->tgl_fol = date('Y-m-d');
            }
            $pros->update();
        }
       
    $id_tokpros = Prosp::where('tokpros', $tokpros)->first();
    
    $data = new LapFol;
    $data->id_karyawan = Auth::user()->id_karyawan;
    $data->id_don = $don->id;
    $data->id_peg = Auth::user()->id;
    $data->id_sumdan = $request->id_sumdan;
    $data->id_prog = $request->id_prog;
    $data->program = $request->id_prog;
    $data->ket = $request->ket_prog;
    $data->status = $request->stat_prog;
    $data->tgl_fol = $request->ket_prog == 'open' ? $request->tgl_fol : date('Y-m-d');
    $data->pembayaran = $request->pembayaran;
    $data->deskripsi = $request->deskripsi;
    $data->jalur = $request->jalur;
    $data->id_pros = $id_tokpros->id;
    $data->carfol = $request->carfol;
    $data->jenis = 'pros';

    $data->save();
        
    
return response()->json(
    [
        "status" => "sukses",
        "data" => $data,
        "id_donp" => $id,
        "ket_prog" => $request->ket_prog
    ]
    );
}

public function getprosnow(){
    $kerja = Donatur::whereIn('id', function($query){
                        $query->select('id_don')->from('lap_folup')->where('id_peg', Auth::user()->id)
                                ->whereDate('created_at','=', date('Y-m-d'));
                    })->get();
    return fractal($kerja, new DonIdTransformer(['kon' => 'Id', 'id' => Auth::user()->id]))->toArray();
}

public function getprosnow2($id, $ket){
    $kerja = Donatur::where('donatur.id', $id)->get();
    return fractal($kerja, new DonaturTransformer(['kon' => 'Donatur', 'ket' => $ket, 'id' => 44]))->toArray();
    // $kerja = Donatur::whereIn('id', function($query){
    //                     $query->select('id_don')->from('upnoloc')
    //                             ->whereDate('created_at','=', date('2022-08-03'))
    //                             ;
    //                 })->get();
    // return fractal($kerja, new DonUpTransformer(['id' => 97]))->toArray();
}

public function getpenup(){
    $kerja = Donatur::whereIn('id', function($query){
                        $query->select('id_don')->from('upnoloc')
                            ->where('acc','=', 2)
                            ->whereIn('id_koleks', function($query2){
                                $query2->select('id')->from('users')
                                    ->where(function($query3) {
                                        if(Auth::user()->kolektor == 'kacab'){
                                            $query3->where('id_kantor', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor);
                                        }else{
                                            $query3->where('id_spv', Auth::user()->id_karyawan);
                                        }
                                    })
                                    ;
                            })
                            ;
                    })
                    ->orderBy('updated_at', 'DESC')->get();
    return fractal($kerja, new DonNewTransformer(['kon' => 'Up', 'acc' => 2]))->toArray();
}

public function getrekup($acc, $tgl1, $tgl2){
    $kerja = Donatur::whereIn('id', function($query) use ($acc, $tgl1, $tgl2){
                        $query->select('id_don')->from('upnoloc')
                            ->where('acc','=', $acc)->whereDate('tgl_acc','>=', $tgl1)->whereDate('tgl_acc','<=', $tgl2)
                            ->whereIn('id_koleks', function($query2){
                                $query2->select('id')->from('users')
                                    ->where(function($query3) {
                                        if(Auth::user()->kolektor == 'kacab'){
                                            $query3->where('id_kantor', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor);
                                        }else{
                                            $query3->where('id_spv', Auth::user()->id_karyawan);
                                        }
                                    })
                                    ;
                            })
                            ;
                    })
                    ->get();
    return fractal($kerja, new DonNewTransformer(['kon' => 'Up', 'acc' => $acc, 'tgl1' => $tgl1, 'tgl2' => $tgl2]))->toArray();
}

public function accupdon($id, Request $request)
{
    $data = UpNoLoc::find($id);
    
        $don        = Donatur::find($request->id_don);
        $don_no     = $don->no_hp;
        $don_lat    = $don->latitude;
        $don_long   = $don->longitude;
        $don_gam    = $don->gambar_donatur;
        
    if($request->acc == 0 | ($data->acc == 0 && $request->acc == 1)){
        if($data->jenis == 'no'){
            $don->no_hp             = $data->no_hp;
            $data->no_hp            = $don_no;
        }else if($data->jenis == 'lok'){
            $don->latitude          = $data->latitude;
            $don->longitude         = $data->longitude;
            $data->longitude        = $don_long;
            $data->latitude         = $don_lat;
        }else if($data->jenis == 'gam'){
            $don->gambar_donatur    = $data->gambar_donatur;
            $data->gambar_donatur   = $don_gam;
        }
        $don->update();
    }
    
    $data->tgl_acc = date('Y-m-d');
    $data->acc = $request->acc;
    $data->update();
    
    return response()->json([
        "status"=>"sukses"
        ]);
}

public function getspvkol(){
    $tj = Tunjangan::first();
    $kar= Karyawan::where('aktif', 1)->where('jabatan', $tj->spv_kol)
                    ->where(function($query) {
                        $query->where('id_kantor', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor);
                    })
                    ->get();
    // $data['data'] = $kar;
    return ($kar);
}

public function postnewtran(Request $request){
    $id_trans = $request->id_donatur.date('dmY').Auth::user()->id;
            
    if($request->bukti != null){
        $namgam = $request->file('bukti')->getClientOriginalName();
        $request->file('bukti')->move('gambarUpload',$namgam);
        }
       
    $coadeb = $request->pembayaran == 'transfer' ? Bank::where('id_bank', $request->bank)->first() : Kantor::where('id', $request->id_kantor)->first();  
    
        
        if($request->status == 'Tutup'){
        $trandon = Transaksi::where('id_transaksi','!=', $id_trans)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))
                    ->where('status', 'Tutup')->where('id_donatur', $request->id_donatur)->get();
        }else{
        $trandon = [];
        }
        
        $user = Donatur::find($request->id_donatur);
        
        $pro = Prog::find($request->id_prog);
        
        $data = new Transaksi;
        $data->id_koleks = Auth::user()->id;
        $data->id_donatur = $request->id_donatur;
        $data->id_transaksi = $id_trans;
        $data->tanggal = date('Y-m-d');
        $data->donatur = $request->donatur;
        $data->pembayaran = $request->pembayaran;
        $data->kolektor = Auth::user()->name;
        $data->alamat = $request->alamat;
        $data->id_program = $request->id_prog;
        $data->dp = $pro->dp;
        $data->id_sumdan = $request->id_sumdan;
        $data->id_pros = $request->id_pros;
        $data->keterangan = $request->keterangan == '' ? NULL : $request->keterangan;
        $data->subprogram = $request->namprog;
        $data->id_kantor = $request->id_kantor;
        $data->id_bank = $request->bank;
        $data->coa_kredit = $request->coa_kre;
        $data->coa_debet = $request->pembayaran == 'noncash' ? $request->coaper : $coadeb->id_coa;
        $data->via_input = 'transaksi';
        $data->akun = $request->namprog;
        $data->ket_penerimaan = 'an: '.$request->donatur.' | '.$request->namprog;
        $data->qty = 1;
        $data->user_insert = Auth::user()->id;
        $data->status = $request->status == 'Tutup' && count($trandon) >= 1 ? 'Tutup 2x' : $request->status;
        $data->jumlah = preg_replace("/[^0-9]/", "", $request->jumlah);
        $data->kota = $request->kota;
        $data->id_camp = $request->id_camp == 0 ? NULL : $request->id_camp;
        
        if($request->bukti != null){
        $data->bukti = $request->file('bukti')->getClientOriginalName();
        }  
            
        $data->save();
        
        $user->status = $request->status == 'Tutup' && count($trandon) >= 1 ? 'Tutup 2x' : $request->status;
        $user->dikolek = date('d/m/Y');
        if($request->status == 'Tutup'){
            $user->acc = count($trandon) >= 1 ? 0 : 1; 
            $user->retup = $request->retup;
            $user->ketup = $request->retup == 2 ? $request->ketup : NULL;
        }else{
            $user->acc = 0;
        }
        $user->bukti = $request->file('bukti')->getClientOriginalName();
        $user->user_trans = Auth::user()->id;
        // if($status[$i] == 'Ditarik' || $status[$i] == 'Off'){
        //     $user->tgl_nonaktif = date('Y-m-d');
        //     \LogActivity::addToLoghfm(Auth::user()->name . ' Menonaktifkan Data Donatur ' . $user->nama . ' Dengan ID ' . $request->id_donatur , 'kosong', 'donatur', 'update', $request->id_donatur);
        // }
        $user->update();
    
    $sumtran = 0;
    $sumtran = Transaksi::where('id_transaksi', $id_trans)->sum('jumlah');
    Transaksi::where('id_transaksi', $id_trans)->update([
        'subtot' => $sumtran,
        ]);
        
        
    return response()->json(
        [
            "status"=>"sukses",
            "id_trans" => $id_trans
        ]
    );
    }
    
    public function gettj(){
    $tj = Tunjangan::first();
    return ($tj);
    }
    
    public function getwa($id){
    $tran = Transaksi::where('id_transaksi', $id)->where('via_input', 'transaksi')->orderBy('id', 'DESC')->get();
    $t = $tran[0];
    // $t = Transaksi::where('id_transaksi', $id)->where('via_input', 'transaksi')->latest()->first();
    $dt = date('d / m / Y, H:i:s',strtotime($t->created_at));
    $k = Kantor::find($t->id_kantor);
    // $t = Donatur::first();
    // $nama = $t->nama;
    // $tun = Tunjangan::first();
    if($t->pembayaran == 'dijemput'){
    $tj['text'] = 
                'TANDA TERIMA ELEKTRONIK %0A*KILAU - LEMBAGA KEMANUSIAAN*%0A==============================
                %0ADonatur Yth, %0A*' .$t->donatur. '*
                %0A==============================
                %0AId Transaksi : ' .$t->id_transaksi. ' %0ADiterima       : ' .$dt. ' %0APetugas        : ' .$t->kolektor. '
                %0A============================== %0ATotal Donasi : Rp. ' .number_format($t->subtot,0,',','.'). ' %0A==============================
                %0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *' .$t->donatur. '* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                %0A==============================
                %0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ' .$k->unit. '
                %0AKlik link dibawah ini untuk melihat detail kwitansi :
                %0A https://kilauindonesia.org/datakilau/kwitansi/' .$t->id_transaksi. ' %0A============================== %0AKlik link dibawah ini untuk pengaduan dan saran :
                %0A https://kilauindonesia.org/datakilau/formpengaduan/' .$t->id_transaksi. '
                %0A==============================
                %0A Terima Kasih Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A'
                ;    
    }else if($t->status == 'Tutup' || $t->status == 'Tutup 2x'){
    $tj['text'] = 
                'TANDA TERIMA ELEKTRONIK %0A*KILAU - LEMBAGA KEMANUSIAAN*%0A==============================
                %0ADonatur Yth, %0A*' .$t->donatur. '*
                %0A==============================
                %0AId Transaksi : ' .$t->id_transaksi. ' %0ADikunjungi    : ' .$dt. ' %0APetugas        : ' .$t->kolektor. '
                %0A==============================
                %0ABerdasarkan hasil kunjungan petugas, sdr/i *' .$t->donatur. '* dinyatakan tidak sedang berada ditempat dengan kondisi *TUTUP* sehingga petugas tidak dapat menjemput donasi. Insyaa Allah akan dilakukan kunjungan ulang untuk melakukan penjemputan donasi
                %0A============================== %0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ' .$k->unit. '
                %0A==============================
                %0A Terima Kasih atas perhatian Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A'
                ;
    }else{
    $tj['text'] = 
                'TANDA TERIMA ELEKTRONIK %0A*KILAU - LEMBAGA KEMANUSIAAN*%0A==============================
                %0ADonatur Yth, %0A*' .$t->donatur. '*
                %0A==============================
                %0AId Transaksi : ' .$t->id_transaksi. ' %0ADikolek         : ' .$dt. ' %0APetugas        : ' .$t->kolektor. '
                %0A============================== %0ATotal Donasi : Rp. ' .number_format($t->subtot,0,',','.'). ' %0A==============================
                %0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *' .$t->donatur. '* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                %0A==============================
                %0AMohon Bantuan Sahabat Untuk %0A1. Menyaksikan penghitungan uang yang dilakukan petugas kami %0A2. Apabila terdapat kekeliruan, ketidak sesuaian jumlah donasi atau pelanggaran yang dilakukan petugas kami, silahkan untuk melakukan pengaduan %0A3. Bantulah kami agar senantiasa dapat menjaga amanah sahabat
                %0A============================== %0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ' .$k->unit. '
                %0AKlik link dibawah ini untuk melihat detail kwitansi :
                %0A https://kilauindonesia.org/datakilau/kwitansi/' .$t->id_transaksi. ' %0A============================== %0AKlik link dibawah ini untuk pengaduan dan saran :
                %0A https://kilauindonesia.org/datakilau/formpengaduan/' .$t->id_transaksi. '
                %0A==============================
                %0A Terima Kasih Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A'
                ;
    }
    
    $tj['data'] = [];
    if(count($tran) > 0){
    foreach($tran as $x => $user){
        $tj['data'][] = [
            'id' => $user->id,
            'id_donatur' => $user->id_donatur,
            'id_koleks' => $user->id_koleks,
            'id_transaksi' => $user->id_transaksi,
            'pembayaran' => $user->pembayaran,
            'jumlah' => $user->jumlah,
            'status' => $user->status,
            'subprogram' => $user->subprogram,
            'keterangan' => $user->keterangan,
            'approval' => $user->approval,
            'alasan' => $user->alasan,
            'registered' => $user->created_at->format('d M Y'),
            'create' => $user->created_at->format('d/m/Y'),
            'bulan' => $user->created_at->format('m/Y'),
        ];
    }
    }
    
    return ($tj);
    }
    
    
    public function dondeketdup($lat, $long, $mail, $no){
    
         $kerja = Donatur::selectRaw("*, 111.2 * DEGREES(ACOS(COS(RADIANS($lat))
                 * COS(RADIANS(latitude))
                 * COS(RADIANS(longitude) - RADIANS($long))
                 + SIN(RADIANS($lat))
                 * SIN(RADIANS(latitude))))
                 AS jarak_km")
                ->where(function($query) use ($mail, $no) {
                    $query->where('email', 'like', '%'.$mail.'%')->orWhere('no_hp', 'like', '%'.$no.'%');
                })
                ->having('jarak_km','<=', 0.2)->orderBy('jarak_km', 'asc')->limit(15)->get();
    
    return fractal($kerja, new DonNewTransformer(['kon' => 'Don']))->toArray();
    }
    
    public function namdonclos($ket, $nam){
    $kerja = Donatur::where('nama', 'like', '%'.$nam.'%')
            ->whereIn('id', function($query) use ($ket){
                        $query->select('id_don')->from('prosp')->where('id_peg', Auth::user()->id)->where('ket', $ket);
                    })
            ->get();
    return fractal($kerja, new DonNewTransformer(['kon' => 'Donatur', 'ket' => $ket, 'id' => Auth::user()->id]))->toArray();
}

    public function makeParentChildRelations(&$inArray, &$outArray, $currentParentId = 0) {
        if(!is_array($inArray)) {
            return;
        }
    
        if(!is_array($outArray)) {
            return;
        }
    
        foreach($inArray as $key => $tuple) {
            if($tuple['id_parent'] == $currentParentId) {
                $tuple['children'] = array();
                $this->makeParentChildRelations($inArray, $tuple['children'], $tuple['id']);
                $outArray[] = $tuple;   
            }
        }
    }
    
    public function trial_data(Request $request){
            
            $bln = Carbon::now()->format('m-Y');
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
             $query = DB::table('coa as t1')
                    ->select('t1.*', 't1.id as root')
                    ->unionAll(
                        DB::table('b as t0')
                            ->select('t3.*', 't0.root')
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                    );
                
                $tree = DB::table('b as t')
                    ->selectRaw("root, t2.*, SUM(t.saldo_new) as total, 0 AS debit, 0 AS kredit")
                    ->withRecursiveExpression('b', $query)
                    ->join('coa as t2', 't2.id', '=', 't.root')
                    ->groupBy('root')
                    ->get();
                    
           
            // $query = DB::table('coa as t1')
            //         ->select('t1.*', 't1.id as root', 'transaksi.jumlah as jumlah', 'pengeluaran.nominal as nominal')
            //         ->unionAll(
            //             DB::table('b as t0')
            //                 ->select('t3.*', 't0.root', 't3.id as n', 't3.id as n2')
            //                 ->join('coa as t3', 't3.id_parent', '=', 't0.id')
            //         )
                    
            //         ->leftjoin('transaksi',function($join) use ($bulan, $tahun) {
            //             $join->on('transaksi.coa_debet' ,'=', 't1.coa')
            //                  ->whereMonth('transaksi.tanggal', $bulan)
            //                  ->whereYear('transaksi.tanggal', $tahun);
            //             })
            //         ->leftjoin('pengeluaran',function($join) use ($bulan, $tahun) {
            //             $join->on('pengeluaran.coa_kredit' ,'=', 't1.coa')
            //                  ->whereMonth('pengeluaran.tgl', $bulan)
            //                  ->whereYear('pengeluaran.tgl', $tahun);
            //             });
                
            //     $tree = DB::table('b as t')
            //         ->selectRaw("root, t2.*, SUM(t.saldo_new) as total, SUM(t.jumlah) AS debit, SUM(t.nominal) AS kredit")
            //         ->withRecursiveExpression('b', $query)
            //         ->join('coa as t2', 't2.id', '=', 't.root')
            //         ->groupBy('root')
            //         ->get();
                    
            
        $inArray = [];
        foreach($tree as $val){
            
            $saldox = $val->total;
            $debit =  $val->debit == NULL ? 0 : $val->debit;
            $kredit = $val->kredit == NULL ? 0 : $val->kredit;
            
            $saldoc = SaldoAw::whereRaw("coa = '$val->coa' AND MONTH(saldo_awal.created_at) = '$bulan' AND YEAR(saldo_awal.created_at) = '$tahun'")->first();
            
            if($saldoc == null){
                $b = 0;
                $ah = 0;
            }else{
                $ah = $saldoc->closing;
                if($saldoc->closing == 1){
                    $b = ($saldox + $debit) - $kredit;
                }else{
                    $b = 0;
                }
            }
            
            $inArray[]=[
                'id' => $val->id,
                'coa' => $val->coa,
                'nama_coa' => $val->nama_coa,
                'id_parent' => $val->id_parent,
                'coa_parent' => $val->id_parent != 0 ? $val->coa : 0,
                'level' => $val->level,
                'saldo_awal' => $saldox,
                'debit' => $debit,
                'kredit' => $kredit,
                'neraca_saldo' => ($saldox + $debit) - $kredit,
                'closing' => $ah ,
                'closed' => $b,
                'debit_s' => 0,
                'kredit_s' => 0
            ];
        }
        
        
        $outArray = array();
        $this->makeParentChildRelations($inArray, $outArray);
        return(['data' => $outArray]);
        
    }
    
    
 
    public function listkondon($ket, $acc, $tgl1, $tgl2){
      $kerja = Donatur::join('prosp', 'donatur.id', '=', 'prosp.id_don')
            ->whereIn('prosp.id_peg', function($quer) {
                if (Auth::user()->kolektor == 'kacab') {
                    $quer->select('id')->from('users')->where('id_kantor', Auth::user()->id_kantor)
                        ->orWhere('kantor_induk', Auth::user()->id_kantor);
                } else {
                    $quer->select('id')->from('users')->where('id_spv', Auth::user()->id_karyawan);
                }
            })
            ->where('prosp.ket', $ket)
            ->where('prosp.konprog', $acc)
            ->where(function($query) use ($ket, $acc, $tgl1, $tgl2) {
                if (!empty($tgl1) && !empty($tgl2)) {
                    $query->whereDate('prosp.created_at', '>=', $tgl1)
                          ->whereDate('prosp.created_at', '<=', $tgl2);
                } else {
                    $query->whereMonth('prosp.created_at', now()->month)
                          ->whereYear('prosp.created_at', now()->year);
                }
            })
            ->orderBy('prosp.created_at', 'desc') // Order by applied on prosp.created_at
            ->get();


        
        return fractal($kerja, new DonNewTransformer(['kon' => Auth::user()->kolektor == 'kacab' ? 'Cab' : 'Spv', 'konprog' => $acc, 'ket' => $ket, 'id' => Auth::user()->kolektor == 'kacab' ? Auth::user()->id_kantor : Auth::user()->id_karyawan, 'tgl1' => $tgl1, 'tgl2' => $tgl2]))->toArray();
    }
    
    public function gettranpros($arid, $id)
    {
        // $tgl1 = $val['tgl'] == '' ? date('dmY') : date('dmY', strtotime($val['tgl']));
        $pros = $arid != '%5B%5D' ?  json_decode (urldecode($arid)) : '';
        $tran = Transaksi::where('id_donatur', $id)->whereIn('id_pros', $pros)->get();
        return(['data' => $tran]);
    }
    
    public function cekmovedon($lat, $long, $name, $no, $pay){
    
         $kerja = Donatur::selectRaw("*, 111.2 * DEGREES(ACOS(COS(RADIANS($lat))
                 * COS(RADIANS(latitude))
                 * COS(RADIANS(longitude) - RADIANS($long))
                 + SIN(RADIANS($lat))
                 * SIN(RADIANS(latitude))))
                 AS jarak_km")
                ->where(function($query) use ($mail, $no) {
                    $query->where('nama', 'like', '%'.$name.'%')->orWhere('no_hp', 'like', '%'.$no.'%');
                })
                ->where('status','!=','Ditarik')->where('status','!=','Off')
                ->having(function($query) use ($pay) {
                    if($pay == 'dijemput'){
                        $query->having('jarak_km','<=', 0.2);
                    }
                })
                ->orderBy('jarak_km', 'asc')->limit(15)->get();
    
    return fractal($kerja, new DonNewTransformer(['kon' => 'Don']))->toArray();
    }
    
    
public function konfdon($id, Request $request){
    
// Donatur::find($id)->update(['acc' => $request->kondisi]);

$konprog = $request->konprog != '%5B%5D' ?  json_decode (urldecode($request->konprog)) : '';
$idpros = $request->id_pros != '%5B%5D' ?  json_decode (urldecode($request->id_pros)) : '';
$ket = $request->ket != '%5B%5D' ?  json_decode (urldecode($request->ket)) : '';
$tglfol = $request->tgl_fol != '%5B%5D' ?  json_decode (urldecode($request->tgl_fol)) : '';

    for($i = 0; $i<count($idpros); $i++){
        // if($idpros[$i] == 0){
            Prosp::find($idpros[$i])->update(['konprog' => $konprog[$i]]);
        // }
    }
       

$laphub = new LapHub;
$laphub->id_karyawan = Auth::user()->id_karyawan;
// $laphub->pembayaran = $request->pembayaran;
// $laphub->tgl_fol = $request->ket == 'open' ? $request->tgl_fol : date('Y-m-d');
$laphub->ket = $request->ket;
$laphub->deskripsi = $request->deskripsi;
// $laphub->jalur = $request->jalur;
// $laphub->program = $program;
// $laphub->id_sumdan = $sumdan;
$laphub->id_don = $id;

if($request->bukti != null){
$laphub->bukti = $request->file('bukti')->getClientOriginalName();
$request->file('bukti')->move('lampiranLaporan',$laphub->bukti);
}
$laphub->save();

    return response()->json(
        [
            "status"=>"sukses",
            // "data"=>$data
        ]
    );
}

    public function cronwarningdonkil(){
        Donatur::where('warning', 1)->update(['warning' => 0, 'user_warning', NULL]);
        
        $tj = Tunjangan::select('mindon','jumbul')->first();
        $now = date('Y-m-d');
        $bulan_now = date('Y-m-t', strtotime('-1 month', strtotime($now)));
        $interval = date('Y-m-01', strtotime('-'.$tj->jumbul.' month', strtotime($now)));
        $datas = Donatur::selectRaw("DATE_FORMAT(transaksi.tanggal, '%Y-%m') as bulan, id_donatur,
                    SUM(IF(donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now', jumlah, 0 )) as ju
                    ")
                ->join('transaksi','donatur.id','=','transaksi.id_donatur')
                    ->whereIn('donatur.id', function($q){
                    $q->select('id_don')->from('prosp')->where('ket','closing');
                })
                ->whereRaw("donatur.status != 'Ditarik' AND donatur.status != 'Off' AND DATE(transaksi.tanggal) >=  '$interval' AND DATE(transaksi.tanggal) <= '$bulan_now'")
                ->groupBy('id_donatur','bulan')
                ->get();
                
                
        $targetAmount = $tj->mindon;
        $jumbul = $tj->jumbul;
         
        $result = [];
        $count = 0;
        $sepong = [];
        $coy = [];
        $result2 = [];
        
        $groupedData = collect($datas)->groupBy('id_donatur')->toArray();
        
        foreach ($groupedData as $donatur => $donaturData) {
            
            $kon = count(array_column($donaturData, 'bulan'));
            $hasil = count(array_filter($donaturData, function ($item) use ($targetAmount) {
                    return $item['ju'] <  $targetAmount;
            }));
            
            // $result[] = [
            //     "bulan" => array_column($donaturData, 'bulan'),
            //     "id_donatur" => $donaturData[0]['id_donatur'],
            //     "jumlah" => array_column($donaturData, 'ju'),
            //     "count_bulan" => $kon,
            //     'donasi_lebih_dari_'.$targetAmount =>  $hasil
                
            // ];
            
            if($kon == $jumbul){
                if($hasil == $jumbul){
                    
                    // $result2[] = [
                    //     "id_donatur" => $donaturData[0]['id_donatur'],
                    //     'donasi_lebih_dari_'.$targetAmount =>  $hasil
                    // ];
                    
                     Donatur::find($donaturData[0]['id_donatur'])->update(['warning' => 1]);
                }
            }
        }
        return ('SUKSES');
    }
    
    public function cronassignkil(){
        Donatur::whereDate('jadwal_assignment', date('Y-m-d'))->where('status', 'belum dikunjungi')
                    ->update([
                      'acc' => 1,
                      'tgl_kolek' => date('Y-m-d H:i:s')
                    ]);
        return('SUKSES');
    }

}
