<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Akses;
use App\Models\User;
use App\Models\UserKolek;
use App\Models\Presensi;
use App\Models\Presensi2;
use App\Models\Kantor;
use App\Models\RequestKar;
use App\Models\Laporan;
use App\Models\Karyawan;
use App\Models\KaryawanNew;
use App\Models\Feedback;
use App\Models\Jabatan;
use App\Models\Gapok;
use App\Models\Gaji;
use App\Models\UpGaji;
use App\Models\JamKerja;
use App\Models\Tunjangan;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\HapusTransaksi;
use App\Models\HapusPengeluaran;
use App\Models\Golongan;
use App\Models\Kenaikan;
use App\Models\RekapKeluarga;
use App\Models\RekapJabatan;
use App\Models\Bpjs;
use App\Models\Daerah;
use App\Models\Notiftest;
use App\Models\Hubungi;
use App\Models\Transaksi_Perhari;
use App\Models\Transaksi_Perhari_All;
use App\Models\Profile;
use App\Models\FeedbackUser;
use App\Models\Rinbel;
use App\Models\DonaturTemp;
use App\Models\Donatur;
use App\Models\Prog;
use App\Models\RekapKar;
use App\Models\ToDo;
use App\Models\Prosp;
use App\Models\COA;
use App\Models\SaldoAw;
use App\Models\Bank;
use App\Models\PenAnak;
use App\Models\Company;
use App\Models\PenUser;
use App\Models\LapFol;
use App\Models\Rekdis;
use App\Models\Jenreq;
use App\Models\Pengumuman;
use App\Models\Targets;
use App\Models\Tambahan;
use App\Models\SetUP;
use App\Models\Rencana;
use App\Models\RencanaBln;
use App\Models\RencanaThn;
use App\Models\Generate;
use App\Models\Udev;
use App\Models\ProgPerus;
use App\Models\KomponenGj;
use App\Models\KomponenGaji;
use App\Models\Voting;
use stdClass;
use Auth;
use Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Transformers\UserKarTransformer;
use App\Transformers\PresensiTransformer;
use App\Transformers\StatPresTransformer;
use App\Transformers\KantorTransformer;
use App\Transformers\RequestTransformer;
use App\Transformers\RwyKarTransformer;
use App\Transformers\LaporanTransformer;
use App\Transformers\FeedbackTransformer;
use App\Transformers\FeedbackUserTransformer;
use App\Transformers\JamkerTransformer;
use App\Transformers\GajiTransformer;
use App\Transformers\GolonganTransformer;
use App\Transformers\JabatanTransformer;
use App\Transformers\ToDoTransformer;
use App\Transformers\GajiNewTransformer;
use App\Transformers\GajiKarTransformer;
use Image;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use DB;
// use \App\Http\Controllers\DB;
use App\Midtrans\Midtrans\Config;
use App\Midtrans\Midtrans\Snap;
use App\Midtrans\Midtrans\CoreApi;
use App\Midtrans\Midtrans\Transaction;


class ApiUserKaryawanController extends Controller
{
    //  public function __construct()
    //     {
    //         Config::$serverKey = 'SB-Mid-server-MmVp4ogk83LNPul5WjPiplNd';
    //         Config::$isProduction = false;
    //         // Config::$isSanitized = true;
    //         // Config::$is3ds = true;
    //     }
        
        public function getStatusPayment($orderId)
        {
            Config::$serverKey = 'SB-Mid-server-MmVp4ogk83LNPul5WjPiplNd'; // PAK AKHMAD
            Config::$isProduction = true; //rubah jika pro/dev
            Config::$isSanitized = true;
            Config::$is3ds = true;
            try {
                $status = Transaction::status($orderId);
        
                // Contoh ambil informasi penting
                $transactionStatus = $status->transaction_status;
                $paymentType = $status->payment_type;
                $fraudStatus = $status->fraud_status ?? null;
        
                // Kembalikan status ke frontend atau ke sistem
                return response()->json([
                    'success' => true,
                    'status' => $transactionStatus,
                    'payment_type' => $paymentType,
                    'fraud_status' => $fraudStatus,
                ]);
        
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
        }

    public function handleMidtransCallback(Request $request)
    {
        $serverKey = 'Mid-server-AjhceItc4Bi6b9hxSuO-vDL0';
        $orderId = $request['order_id'];
        $statusCode = $request['status_code'];
        $grossAmount = $request['gross_amount'];
    
        // Buat hashed signature untuk validasi
        $hashed = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if($hashed == $request['signature_key']){
            if($request['transaction_status'] == 'settlement'){
                $transaksi = Transaksi::where('id_transaksi', $orderId)->first();
                $transaksi->update([
                    'payment_type'      => $request['payment_type'],
                    'user_update'      => $transaksi->user_insert,
                    'subtot'      => $transaksi->jumlah,
                    'status'            => 'Donasi',
                    'approval'            => 1,
                ]);
            }
        }
    }
    public function getTokenMidtrans(Request $request){
        Config::$serverKey = 'Mid-server-AjhceItc4Bi6b9hxSuO-vDL0'; // RIZKA
        // Config::$serverKey = 'SB-Mid-server-MmVp4ogk83LNPul5WjPiplNd'; // PAK AKHMAD
        Config::$isProduction = true; //rubah jika pro/dev
        Config::$isSanitized = true;
        Config::$is3ds = true;
        
        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)->first();
        $totalAmount = is_numeric($request->total) ? (int) $request->total : 0;
        
        $existing = Transaksi::where('id_transaksi', '321213' . time())->exists();

        if ($existing) {
            return response()->json(['message' => 'Transaksi sudah ada!'], 400);
        }
        
        Transaksi::create(
            [
                'payment_type'      => null,
                'status'            => 'Belum Dibayar',
                'id_transaksi'      => '321213' . time(),
                'pembayaran'        => 'transfer',
                'subtot'            => $totalAmount,
                'jumlah'            => $totalAmount,
                'qty'               => 1,
                'via_input'         => 'midtrans',
                'id_bank'           => 11,
                'tanggal'           => date('Y-m-d'),
                'donatur'           => $karyawan->nama,
                'alamat'            => $karyawan->alamat,
                'id_sumdan'         => 0,
                'id_program'        => 0,
                'keterangan'        => $request->message ?? null,
                'kota'              => $karyawan->kota,
                'id_kantor'         => 321213,
                'kantor_induk'      => 4,
                'approval'          => 2,
                'user_insert'       => $karyawan->id_karyawan,
                'user_update'       => $karyawan->id_karyawan,
                'id_pros'           => 0,
                'akun'              => 'Infaq Shodaqoh Umum',
                'coa_debet'         => '101.01.002.014',  // Berbagi Teknologi
                'coa_kredit'        => '402.02.001.000', // Infaq Sodaqoh Umum
            ]
        ); 
        
        $params = [
            'transaction_details' => [
                'order_id' => '321213' . time(),
                'gross_amount' => $totalAmount,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name, 
                'last_name' => '',
                'email' => Auth::user()->email ?? 'email_default@example.com',
                'phone' =>  Auth::user()->nomorhp,
                'address'  => $karyawan ->alamat ?? 'Kosong',
                'custom_field1' => Auth::user()->id_karyawan, 
                'custom_field2' => $request->message ?? null, 
            ],
        ];
  
        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getTokenMidtransSandbox(Request $request){
        Config::$serverKey = 'Mid-server-AjhceItc4Bi6b9hxSuO-vDL0'; // server
        // Config::$serverKey = 'SB-Mid-server-MmVp4ogk83LNPul5WjPiplNd'; // sb
        Config::$isProduction = true; //rubah jika pro/dev
        Config::$isSanitized = true;
        Config::$is3ds = true;
        
        $totalAmount = is_numeric($request->total) ? (int) $request->total : 0;
        
        // PENERIMAAN COA 
        // 40 => PENERIMAAN
        // 50 => PENGELUARAN
        
        Transaksi::create(
            [
                'payment_type'      => null,
                'status'            => 'Belum Dibayar',
                'id_transaksi'      => '321213' . time(),
                
                'subtot'            => $totalAmount,
                'jumlah'            => $totalAmount,
                'qty'               => 1,
                'id_bank'           => 11,
                'tanggal'           => date('Y-m-d'),
                // 'donatur'           => $karyawan->nama,
                // 'alamat'            => $karyawan->alamat,
                'id_sumdan'         => 0,
                'id_program'        => 0,
                'keterangan'        => $request->message ?? null,
                // 'kota'              => $karyawan->kota,
                'id_kantor'         => 321213,
                'kantor_induk'      => 4,
                'approval'          => 2,
                // 'user_insert'       => $karyawan->id_karyawan,
                // 'user_update'       => $karyawan->id_karyawan,
                'id_pros'           => 0,
                
                'akun'              => 'Infaq Shodaqoh Umum',
                'pembayaran'        => 'transfer',
                'via_input'         => 'midtrans', // TRANSAKSI // MIDTRANS // MUTASI // PENERIMAAN
                'coa_debet'         => '101.01.002.014',  // Berbagi Teknologi
                'coa_kredit'        => '402.02.001.000', // Infaq Sodaqoh Umum
            ]
        ); 
        
        $params = [
            'transaction_details' => [
                'order_id' => '321213' . time(),
                'gross_amount' => $totalAmount,
            ]
        ];
        
        //  $params = [
        //     'transaction_details' => [
        //         'order_id' => '321213' . time(),
        //         'gross_amount' => $totalAmount,
        //     ],
        //     'customer_details' => [
        //         'first_name' => Auth::user()->name, 
        //         'last_name' => '',
        //         'email' => Auth::user()->email ?? 'email_default@example.com',
        //         'phone' =>  Auth::user()->nomorhp,
        //         'address'  => $karyawan ->alamat ?? 'Kosong',
        //         'custom_field1' => Auth::user()->id_karyawan, 
        //         'custom_field2' => $request->message ?? null, 
        //     ],
        // ];
  
        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getTokenMidtransKliniq() {
         Config::$serverKey = 'Mid-server-AjhceItc4Bi6b9hxSuO-vDL0'; // server
        // Config::$serverKey = 'SB-Mid-server-MmVp4ogk83LNPul5WjPiplNd'; // sb
        Config::$isProduction = true; //rubah jika pro/dev
        Config::$isSanitized = true;
        Config::$is3ds = true;
        
        $totalAmount = is_numeric($request->total) ? (int) $request->total : 0;
        
        // PENERIMAAN COA 
        // 40 => PENERIMAAN
        // 50 => PENGELUARAN
        
        Transaksi::create(
            [
                'payment_type'      => null,
                'status'            => 'Belum Dibayar',
                'id_transaksi'      => '321213' . time(),
                
                'subtot'            => $totalAmount,
                'jumlah'            => $totalAmount,
                'qty'               => 1,
                'id_bank'           => 11,
                'tanggal'           => date('Y-m-d'),
                // 'donatur'           => $karyawan->nama,
                // 'alamat'            => $karyawan->alamat,
                'id_sumdan'         => 0,
                'id_program'        => 0,
                'keterangan'        => $request->message ?? null,
                // 'kota'              => $karyawan->kota,
                'id_kantor'         => 321213,
                'kantor_induk'      => 4,
                'approval'          => 2,
                // 'user_insert'       => $karyawan->id_karyawan,
                // 'user_update'       => $karyawan->id_karyawan,
                'id_pros'           => 0,
                
                'akun'              => 'Bantuan Kesehatan',
                'pembayaran'        => 'transfer',
                'via_input'         => 'midtrans', // TRANSAKSI // MIDTRANS // MUTASI // PENERIMAAN
                'coa_debet'         => '101.01.002.014',  // Berbagi Teknologi
                'coa_kredit'        => '402.01.002.001', // Infaq Sodaqoh Umum
            ]
        ); 
        
        $params = [
            'transaction_details' => [
                'order_id' => '321213' . time(),
                'gross_amount' => $totalAmount,
            ]
        ];
        
        //  $params = [
        //     'transaction_details' => [
        //         'order_id' => '321213' . time(),
        //         'gross_amount' => $totalAmount,
        //     ],
        //     'customer_details' => [
        //         'first_name' => Auth::user()->name, 
        //         'last_name' => '',
        //         'email' => Auth::user()->email ?? 'email_default@example.com',
        //         'phone' =>  Auth::user()->nomorhp,
        //         'address'  => $karyawan ->alamat ?? 'Kosong',
        //         'custom_field1' => Auth::user()->id_karyawan, 
        //         'custom_field2' => $request->message ?? null, 
        //     ],
        // ];
  
        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    
    
    
    public function companys(){
        $data = Company::where('id_com', 1)->first();
        return response()->json(['data' => $data]);
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
                
                // $cil = [];
                // foreach($tuple['children'] as $ke => $tucil) {
                //     $cil[] = [
                        // 'id' => $tucil['id'],
                        // 'coa' => $tucil['coa'],
                        // 'nama_coa' => $tucil['nama_coa'],
                        // 'id_parent' => $tucil['id_parent'],
                        // 'coa_parent' => $tucil['coa_parent'],
                        // 'level' => $tucil['level'],
                        // 'saldo_awal' => $tucil['saldo_awal'],
                        // 'cilong' => count($tucil['children']),
                        // 'children' => count($tuple['children']) > 0 ? $cil : 'kosong tai'
                //         ];
                // }
                $jumcil = 0;
                $jumcrot = 0;
                    for($i=0; $i<count($tuple['children']); $i++){
                    $jumcil +=$tuple['children'][$i]['saldo_awal'];
                    // $jumcrot = count(array($tuple['children'][$i]['children'])) == 1 ? 'haha' : 'hihi';
                    
                    if(count(array($tuple['children'][$i]['children'])) == 1){
                        for($h=0; $h<1; $h++){
                            $jumcrot +=$tuple['children'][$i]['children'][$h]['saldo_awal'];
                        }
                    }
                    // $jumcrot = count((is_countable($tuple['children'][$i]['children'])?$tuple['children'][$i]['children']:[]));
                    // $jumcrot = count(array($tuple['children'][$i]['children']));
                    }
                $outArray[] = [
                    'jumcrot' => $jumcrot,
                    'id' => $tuple['id'],
                    'coa' => $tuple['coa'],
                    'nama_coa' => $tuple['nama_coa'],
                    'id_parent' => $tuple['id_parent'],
                    'coa_parent' => $tuple['coa_parent'],
                    'level' => $tuple['level'],
                    'saldo_awal' => $tuple['saldo_awal'],
                    'cilong' => count($tuple['children']),
                    'cil' => count($tuple['children']) > 0 ? $jumcil : 'kosong tai',
                    'children' => count($tuple['children']) > 0 ? $tuple['children'] : 'kosong tai'
                    ]; 
                // $outArray[] = $tuple;
            }
        }
        
        
    }
    
    
    public function loginsso(Request $request, UserKolek $user){
        if(!Auth::guard('userkolek')->attempt(['email' => $request->email, 'password' => $request->password, 'aktif' => 1])){
            return response()->json(['error' => 'salah bos',401]);
        }else{
            $userk = $user->find(Auth::guard('userkolek')->user()->id);
            if($userk->presensi != null){
            \LogActivity::addToLog(Auth::guard('userkolek')->user()->name.' telah Logging App ');
            return response()->json([
                'berhasil' => $userk,
                'token' => $userk->api_token,
                ]);
            }
            return response()->json(['error' => 'salah bosss' ,401]);
        }
    }

public function notifdev($data, $notif){
    
    define('FCM_AUTH_KEY', 'AAAA5kE6iRc:APA91bF0SlLAo6VU6in0QAXkDsxIr1Cee6DP7HX-HdeR6BmTbUuDR5dRfswhLAFeBPnWe_iFymhDv4hnYrp6N8__5Ns96DQTGLF3t2-r3brXVerHfRIs1oWqtwDz5Ms9fwRyoCjwSKQk');
    
    // $token = $request->token;
    // $pesan = $request->pesan;

    // $data = Notiftest::where('token',$token)->get();
    
   
    foreach($data as $i => $val){
        
    	$postdata = json_encode(
    	    [
    	        'notification' => 
    	        	[
    	        		'title' => $notif['title'],
    	        		'body'  => $notif['body'],
    	       // 		'icon'  => $icon,
    	       // 		'click_action' => $url
    	                'sound' => "default",
    	        	]
    	        ,
    	        'to' => $val->token,
    	        'data' => [
                        'click_action' => 'LIFIYK',
                    ],
    	    ]
    	    
    	);
    
    	$opts = array('http' =>
    	    array(
    	        'method'  => 'POST',
    	        'header'  => 'Content-type: application/json'."\r\n"
    	        			.'Authorization: key='.FCM_AUTH_KEY."\r\n",
    	        'content' => $postdata
    	    )
    	);
    
    	$context  = stream_context_create($opts);
    
    	$result = file_get_contents('https://fcm.googleapis.com/fcm/send', false, $context);
    }
}    
    
public function tesapi(){
    // $kar = Transaksi::where('id', '!=', 0)->get();
    $kar = Udev::all();
    $notif = [
                'title' => 'Diroh Gay',
    	        'body'  => 'Sudiroh adalah ketua gay Indramayu',
            ];
    
    $this->notifdev($kar, $notif);
    
    return 'SUKSES';
}

public function tester(Request $request){
    
    // $data = new notiftest;
    // $data->nama = $request->nama;
    // $data->token = $request->token;
    // $data->save();
    
    // return response()->json(
    //     [
    //         "status"=>"sukses",
    //         // "data"=>$data
    //     ]
    // );
    
    // return('berasil');
     
    // tes rencananow 
        $id     = $request->id_karyawan;
        $u      = User::where('id_karyawan', $id)->first();
        $tgl    = date('Y-m-d');
        $tgl_m  = date('Y-m-01', strtotime($tgl));
        $tgl_a  = date('Y-m-t', strtotime($tgl));
        
        $r_tgs  = Rencana::leftjoin('rencana_bln', 'rencana.id_rb', '=', 'rencana_bln.id')
                            ->select('rencana.*', 'rencana_bln.satuan AS sat_bln', 'rencana_bln.id_hasil AS id_hasil')
                            ->where(function($que) use ($tgl) {
                                $que->where(function($q) use ($tgl) {
                                    $q->whereDate('rencana.tgl_awal', $tgl)->whereDate('rencana.tgl_akhir', '>=', $tgl)
                                        ->orWhereDate('rencana.tgl_akhir', '<', $tgl)->where('rencana.tgl_selesai', null);
                                    })
                                    ->orWhereDate('rencana.tgl_kerja', $tgl)->where('in_tambahan', 1);
                            })
                            ->whereDate('rencana.tgl_awal','>=', $tgl_m)->whereDate('rencana.tgl_akhir', '<=', $tgl_a)
                            ->where('rencana.id_karyawan', $id)->where('rencana.marketing', 0)
                            ->orderBy('rencana_bln.id_hasil')
                            ->orderBy('rencana.id')
                            ;
        
        // $id_h   = $r_tgs->pluck('id_hasil')->unique();
        $tgs    = $r_tgs->get();
        
        $tugas  = [];
        $i      = 0;
        foreach($tgs as $key => $v){
            if($i > 0){
                $i++;
            }
            
            $sat    = RencanaThn::find($v->sat_bln);
            $buk    = [
                      'name'    => '',
                      'size'    => 0,
                      'type'    => '',
                      'uri'     => ''
                    ];
            $tugas['items'][$i]  =   [
                                'id'            => $v->id,
                                'tugas'         => $v->tugas,
                                'deskripsi'     => $v->deskripsi,
                                'catatan'       => $v->catatan,
                                'bukti'         => $buk,
                                'jamMulai'      => $v->jam_awal,
                                'jamAkhir'      => $v->jam_akhir,
                                'durasi'        => $v->durasi,
                                'target'        => $v->target,
                                'tglAwal'       => $v->tgl_awal,
                                'tglAkhir'      => $v->tgl_akhir,
                                'tglKerja'      => $v->tgl_kerja,
                                'inTambahan'    => $v->in_tambahan,
                                'inEdit'        => 1,
                                'jenis'         => 'proses',
                                'tgl_selesai'   => $v->tgl_selesai,
                                'capaian'       => $v->capaian,
                                'satuan'        => $sat != null ? $sat->rumus : null,
                                'aktif'         => $v->aktif,
                                'acc'           => $v->acc,
                                'alasan'        => $v->alasan,
                                'alasan_r'      => $v->alasan_reject,
                                'id_hasil'      => $v->id_hasil,
                                'index'         => $i
                                ];
            if($key+1 < count($tgs) ? $tgs[$key+1]->id_hasil != $v->id_hasil : 1 == 1){
                $i++;
                $h      = RencanaBln::where('id', $v->id_hasil)->first();
                $sat    = RencanaThn::find($h->satuan);
                $tugas['items'][$i]  =   [
                                'id'            => $h->id,
                                'tugas'         => $h->tugas,
                                'deskripsi'     => null,
                                'catatan'       => null,
                                'bukti'         => null,
                                'jamMulai'      => null,
                                'jamAkhir'      => null,
                                'durasi'        => null,
                                'target'        => $h->target,
                                'tglAwal'       => $h->bulan,
                                'tglAkhir'      => date('Y-m-t', strtotime($h->bulan_akhir)),
                                'tglKerja'      => null,
                                'inTambahan'    => 0,
                                'inEdit'        => 1,
                                'jenis'         => 'hasil',
                                'tgl_selesai'   => null,
                                'capaian'       => $h->capaian,
                                'satuan'        => $sat != null ? $sat->rumus : null,
                                'aktif'         => $h->aktif,
                                'acc'           => $h->acc,
                                'alasan'        => $h->alasan,
                                'alasan_r'      => $h->alasan_reject,
                                'id_hasil'      => null,
                                'index'         => $i
                                ];
            }                    
        }
            
    return response()->json(
        [
            "data"  => $tugas
            // "id_h"  => $hasil
        ]
    );
}
public function statpres ($lev, $id, $tanggal1, $tanggal2)
{
    
    $dari = date('Y-m-d', strtotime($tanggal1));
    $sampai = date('Y-m-d', strtotime($tanggal2));
    
    if($lev == 'kacab'){
    $data = Presensi::leftjoin('jabatan', 'presensi.id_jabatan', '=' ,'jabatan.id')
            ->select(\DB::raw("presensi.id_karyawan, presensi.nama, jabatan.jabatan, presensi.id_jabatan, presensi.pr_jabatan, presensi.id_kantor, presensi.kantor_induk, presensi.status, 
                                SUM(IF( DATE(presensi.created_at) >= '$dari' AND DATE(presensi.created_at) <= '$sampai' , presensi.jumlah, 0)) as jumlah"))
            ->where('presensi.id_kantor', $id)->whereDate('presensi.created_at', '>=', $dari)->whereDate('presensi.created_at', '<=', $sampai)
            ->orWhere('presensi.kantor_induk', $id)->whereDate('presensi.created_at', '>=', $dari)->whereDate('presensi.created_at', '<=', $sampai)
            ->groupBy('presensi.id_karyawan', 'presensi.nama', 'presensi.id_jabatan', 'presensi.pr_jabatan', 'presensi.id_kantor', 'presensi.kantor_induk', 'presensi.status')
            ->get();
    }else if($lev == 'admin'){
    $data = Presensi::leftjoin('jabatan', 'presensi.id_jabatan', '=' ,'jabatan.id')
            ->select(\DB::raw("presensi.id_karyawan, presensi.nama, jabatan.jabatan, presensi.id_jabatan, presensi.pr_jabatan, presensi.id_kantor, presensi.kantor_induk, presensi.status, 
                                SUM(IF( DATE(presensi.created_at) >= '$dari' AND DATE(presensi.created_at) <= '$sampai' , presensi.jumlah, 0)) as jumlah"))
            ->whereDate('presensi.created_at', '>=', $dari)->whereDate('presensi.created_at', '<=', $sampai)
            ->groupBy('presensi.id_karyawan', 'presensi.nama', 'presensi.id_jabatan', 'presensi.pr_jabatan', 'presensi.id_kantor', 'presensi.kantor_induk', 'presensi.status')
            ->get();
    }else if($lev == 'karyawan'){
        if(Auth::user()->id_kantor == 4){
        $data = Presensi::leftjoin('jabatan', 'presensi.id_jabatan', '=' ,'jabatan.id')
                ->select(\DB::raw("presensi.id_karyawan, presensi.nama, jabatan.jabatan, presensi.id_jabatan, presensi.pr_jabatan, presensi.id_kantor, presensi.kantor_induk, presensi.status, 
                                    SUM(IF( DATE(presensi.created_at) >= '$dari' AND DATE(presensi.created_at) <= '$sampai' , presensi.jumlah, 0)) as jumlah"))
                ->where('presensi.pr_jabatan', $id)->whereDate('presensi.created_at', '>=', $dari)->whereDate('presensi.created_at', '<=', $sampai)
                ->groupBy('presensi.id_karyawan', 'presensi.nama', 'presensi.id_jabatan', 'presensi.pr_jabatan', 'presensi.id_kantor', 'presensi.kantor_induk', 'presensi.status')
                ->get();
        }else{
        $id_kan = Auth::user()->id_kantor;
        $data = Presensi::leftjoin('jabatan', 'presensi.id_jabatan', '=' ,'jabatan.id')
                ->select(\DB::raw("presensi.nama, jabatan.jabatan, presensi.id_jabatan, presensi.pr_jabatan, presensi.id_kantor, presensi.kantor_induk, presensi.status, 
                                    SUM(IF( DATE(presensi.created_at) >= '$dari' AND DATE(presensi.created_at) <= '$sampai' AND presensi.id_kantor = '$id_kan' , presensi.jumlah, 0)) as jumlah"))
                ->where('presensi.pr_jabatan', $id)->whereDate('presensi.created_at', '>=', $dari)->whereDate('presensi.created_at', '<=', $sampai)->where('presensi.id_kantor', Auth::user()->id_kantor)
                ->groupBy('presensi.id_karyawan', 'presensi.nama', 'presensi.id_jabatan', 'presensi.pr_jabatan', 'presensi.id_kantor', 'presensi.kantor_induk', 'presensi.status')
                ->get();
        }
    }
    return fractal($data, new StatPresTransformer())->toArray();
}

public function stat ($lev, $id, $tanggal1, $tanggal2)
{
    
    $dari = date('Y-m-d', strtotime($tanggal1));
    $sampai = date('Y-m-d', strtotime($tanggal2));
    
    if($lev == 'kacab'){
    $data = Presensi::select(\DB::raw("status, SUM(IF( DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai' AND id_kantor = '$id'
                                        OR DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai' AND kantor_induk = '$id'
                                        , presensi.jumlah, 0)) as jumlah"))
            ->groupBy('status')
            // ->whereDate('created_at','>=',$dari)->whereDate('created_at','<=',$sampai)
            ->get();
    }else if($lev == 'admin'){
    $data = Presensi::select(\DB::raw("status, SUM(IF( DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai'
                                        , presensi.jumlah, 0)) as jumlah"))
            // ->whereDate('created_at','>=',$dari)->whereDate('created_at','<=',$sampai)
            ->groupBy('status')
            ->get();
    }else if($lev == 'karyawan'){
        if(Auth::user()->id_kantor == 4){
        $data = Presensi::select(\DB::raw("status, SUM(IF( DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai' AND pr_jabatan = '$id'
                                            , presensi.jumlah, 0)) as jumlah"))
                ->groupBy('status')
                // ->whereDate('created_at','>=',$dari)->whereDate('created_at','<=',$sampai)
                ->get();
        }else{
        $id_kan = Auth::user()->id_kantor;
        $data = Presensi::select(\DB::raw("status, SUM(IF( DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai' AND pr_jabatan = '$id' AND id_kantor = '$id_kan'
                                            , presensi.jumlah, 0)) as jumlah"))
                ->groupBy('status') 
                // ->whereDate('created_at','>=',$dari)->whereDate('created_at','<=',$sampai)
                ->get();
        }
    }
    return fractal($data, new StatPresTransformer())->toArray();
}

    public function login(Request $request, UserKolek $user){
        if(!Auth::guard('userkolek')->attempt(['email' => $request->email, 'password' => $request->password, 'aktif' => 1])){
            return response()->json(['error' => 'salah bos',401]);
        }else{
            $userk = $user->find(Auth::guard('userkolek')->user()->id);
            if($userk->presensi != null){
            \LogActivity::addToLog(Auth::guard('userkolek')->user()->name.' telah Logging Presensi App ');
            return response()->json([
                'berhasil' => $userk,
                'token' => $userk->api_token,
                ]);
            }
            return response()->json(['error' => 'salah bos',401]);
        }
    }


public function profile(UserKolek $user){
    // if(Auth::user()->aktif == 1 && Auth::user()->presensi != null){
    // $user = UserKolek::find(Auth::user()->id);
    // $fractal = fractal()
    // ->item($user)
    // ->transformWith(new UserKarTransformer())
    // ->toArray();
    // return response()->json($fractal);
    // }
    
    return response()->json([
        "status"=>"gagal"
        ]);
}

public function presnowid ($id)
{
    if(Auth::user()->aktif == 1 && Auth::user()->presensi != null){
        $date = date('Y-m-d');
        
        if(Auth::user()->id_com != null){
            $trf = Presensi::where('id_karyawan',$id)->whereDate('created_at',$date)
                    ->whereIn('id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })
                    ->get();
        }
    
    return fractal($trf, new PresensiTransformer())->toArray();
    }
}

public function presmonthnow ($id)
{
    if(Auth::user()->aktif == 1 && Auth::user()->presensi != null){
        $day = date('d');
        $month = date('m');
        $year = date('Y');
        
        if(Auth::user()->id_com != null){
            $trf = Presensi::where('id_karyawan',$id)->whereMonth('created_at',$month)->whereYear('created_at',$year)
                    ->whereIn('id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })
                    ->orderBy('created_at', 'DESC')->get();
        }
    
    return fractal($trf, new PresensiTransformer())->toArray();
    }
}


public function presrange ($id, $tanggal1, $tanggal2)
{
    if(Auth::user()->aktif == 1 && Auth::user()->presensi != null){
    // $dari = date('Y-m-d', strtotime($request->dari));
    // $sampai = date('Y-m-d', strtotime($request->sampai));
        if(Auth::user()->id_com != null){
            $trf = Presensi::where('id_karyawan',$id)->whereDate('created_at','>=', $tanggal1)->whereDate('created_at','<=', $tanggal2)
                    ->whereIn('id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })
                    ->get();
        }
    return fractal($trf, new PresensiTransformer())->toArray();
    }
}


public function getkantor ()
{
    if(Auth::user()->id_com != null && Auth::user()->aktif == 1 && Auth::user()->presensi != null){
        $trf = Kantor::where('id_com', Auth::user()->id_com)->get();
        return fractal($trf, new KantorTransformer())->toArray();
    }
}

public function postprestest(Request $request)
{
    $user = Auth::user();
    $preskon = Presensi::whereDate('created_at', Carbon::today()) // Ambil data hari ini
    ->where('id_karyawan', Auth::user()->id_karyawan) // Berdasarkan ID karyawan
    ->count();

    // return $preskon;
    // return response()->json(["status" => "gagal", "message" => $preskon]);
    if ($preskon > 0) {
        return response()->json(["status" => "gagal", "message" => "Anda sudah absen!"]);
    }
    if(Auth::user()->id_com != null){
        
            if(Auth::user()->kon_gaji == 1){
            Karyawan::where('id_karyawan', Auth::user()->id_karyawan)->update([
                'tgl_gaji'=> date('Y-m-01')
            ]);
            UserKolek::where('id', Auth::user()->id)->update([
                'kon_gaji' => 0
            ]);
        }
        
        
        $d = date('l');
            $jamker = JamKerja::select('cek_in', 'terlambat')
                ->where('nama_hari', $d)
                ->where('shift', $user->shift)
                ->where('id_com', $user->id_com)
                ->first();
                
        if (!$jamker) {
            return response()->json(['status' => 'gagal', 'message' => 'Jadwal kerja tidak ditemukan'], 404);
        }
        $cek_in = $jamker->cek_in;
        $terlambat = $jamker->terlambat;
        $jam_cek = date('H:i', strtotime($cek_in));
        
        $jam =  $request->cek_in;
        $date = date('Y-m-d');
        
        $jam1 = strtotime(date('Y-m-d H:i'));
        $jam2 = strtotime(date('Y-m-d '.$jam_cek));
        $diff = floor($jam1 - $jam2)/60;
    
    
       $data = [
            'id_karyawan' => Auth::user()->id_karyawan,
            'id_jabatan' => Auth::user()->id_jabatan,
            'pr_jabatan' => Auth::user()->pr_jabatan,
            'id_kantor' => Auth::user()->id_kantor,
            'kantor_induk' => Auth::user()->kantor_induk,
            'nama' => Auth::user()->name,
            'cek_in' => $jam,
            'ket' => $request->keterangan,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'kantor' => $request->kantor,
            'status' => $jam <= $cek_in ? 'Hadir' : 'Terlambat',
            'keterlambatan' => $jam <= $cek_in ? 0 : $diff,
        ];
            if (Auth::user()->id_shift != null) {
                $data['id_shift'] = Auth::user()->id_shift;
                $data['acc_shift'] = Auth::user()->acc_shift;
            }
            
            // // Cek apakah ada file gambar
            // if ($request->hasFile('image')) {
            //     $filename = $request->file('image')->getClientOriginalName();
            //     $request->file('image')->move('gambarKehadiran', $filename);
            //     $data['foto'] = $filename;
            // } else {
            //     return response()->json(["status" => "gagal",  'message' => 'gagal simpan foto']);
            // }
        

// Simpan data ke database
        Presensi::create($data);
            // $data->save();
        return response()->json(
            [
                "status"=>"sukses",
                 'message' => 'Suksen masuk!'
                // "data"=>$data
            ]
        );
    // }
    }else if(Auth::user()->id_com == 3){
        $d = date('l');
        $jamker = JamKerja::where('nama_hari', $d)->where('shift',Auth::user()->shift)->where('id_com', Auth::user()->id_com)->first();
        $cek_in = $jamker->cek_in;
        $terlambat = $jamker->terlambat;
        $jam_cek = date('H:i', strtotime($cek_in));
        
        $jam = date('H:i:s');
        $date = date('Y-m-d');
        
        $jam1 = strtotime(date('Y-m-d H:i'));
        $jam2 = strtotime(date('Y-m-d '.$jam_cek));
        $diff = floor($jam1 - $jam2)/60;
    
        if($diff < -60){
            return response()->json(
                [
                    "status"=>"tofast",
                ]
            );
        }else{
            $data = new Presensi;
            $data->id_karyawan = Auth::user()->id_karyawan;
            $data->id_jabatan = Auth::user()->id_jabatan;
            $data->pr_jabatan = Auth::user()->pr_jabatan;
            $data->id_kantor = Auth::user()->id_kantor;
            $data->kantor_induk = Auth::user()->kantor_induk;
            $data->nama = Auth::user()->name;
            $data->cek_in = $jam;
            $data->ket = $request->keterangan;
            
            if(Auth::user()->id_shift != NULL){
            $data->id_shift = Auth::user()->id_shift;
            $data->acc_shift = Auth::user()->acc_shift;
            }
            
            if($jam <= $cek_in){
                $data->status = 'Hadir';
            }else{
                $data->status = 'Terlambat';
            };
            
            if($jam <= $cek_in){
                $data->keterlambatan = 0;
            }else{
                $data->keterlambatan = $diff;
            }
            
            $data->latitude = $request->latitude;
            $data->longitude = $request->longitude;
            $data->kantor = $request->kantor;
            
            // if($request->hasFile('image')){
            //     $data->foto = $request->file('image')->getClientOriginalName();
            //     $request->file('image')->move('gambarKehadiran',$data->foto);
            // }else{
            //     $data->foto = null;
            // }
            $data->save();
            return response()->json(
                [
                    "status"=>"sukses",
                    // "data"=>$data
                ]
            );
        }
    }
}

public function postpres(Request $request)
{
    $user = Auth::user();
    $preskon = Presensi::whereDate('created_at', Carbon::today()) // Ambil data hari ini
    ->where('id_karyawan', Auth::user()->id_karyawan) // Berdasarkan ID karyawan
    ->count();

    // return $preskon;
    // return response()->json(["status" => "gagal", "message" => $preskon]);
    if ($preskon > 0) {
        return response()->json(["status" => "gagal", "message" => "Anda sudah absen!"]);
    }
    if(Auth::user()->id_com != null){
        
            if(Auth::user()->kon_gaji == 1){
            Karyawan::where('id_karyawan', Auth::user()->id_karyawan)->update([
                'tgl_gaji'=> date('Y-m-01')
            ]);
            UserKolek::where('id', Auth::user()->id)->update([
                'kon_gaji' => 0
            ]);
        }
        
        
        $d = date('l');
            $jamker = JamKerja::select('cek_in', 'terlambat')
                ->where('nama_hari', $d)
                ->where('shift', $user->shift)
                ->where('id_com', $user->id_com)
                ->first();
                
        if (!$jamker) {
            return response()->json(['status' => 'gagal', 'message' => 'Jadwal kerja tidak ditemukan'], 404);
        }
        $cek_in = $jamker->cek_in;
        $terlambat = $jamker->terlambat;
        $jam_cek = date('H:i', strtotime($cek_in));
        
        $jam =  date('H:i:s');
        $date = date('Y-m-d');
        
        $jam1 = strtotime(date('Y-m-d H:i'));
        $jam2 = strtotime(date('Y-m-d '.$jam_cek));
        $diff = floor($jam1 - $jam2)/60;
    
    
       $data = [
            'id_karyawan' => Auth::user()->id_karyawan,
            'id_jabatan' => Auth::user()->id_jabatan,
            'pr_jabatan' => Auth::user()->pr_jabatan,
            'id_kantor' => Auth::user()->id_kantor,
            'kantor_induk' => Auth::user()->kantor_induk,
            'nama' => Auth::user()->name,
            'cek_in' => $jam,
            'ket' => $request->keterangan,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'kantor' => $request->kantor,
            'status' => $jam <= $cek_in ? 'Hadir' : 'Terlambat',
            'keterlambatan' => $jam <= $cek_in ? 0 : $diff,
        ];
            if (Auth::user()->id_shift != null) {
                $data['id_shift'] = Auth::user()->id_shift;
                $data['acc_shift'] = Auth::user()->acc_shift;
            }
            
            // // Cek apakah ada file gambar
            // if ($request->hasFile('image')) {
            //     $filename = $request->file('image')->getClientOriginalName();
            //     $request->file('image')->move('gambarKehadiran', $filename);
            //     $data['foto'] = $filename;
            // } else {
            //     return response()->json(["status" => "gagal",  'message' => 'gagal simpan foto']);
            // }
        

// Simpan data ke database
        Presensi::create($data);
            // $data->save();
        return response()->json(
            [
                "status"=>"sukses",
                 'message' => 'Sukses masuk!'
                // "data"=>$data
            ]
        );
    // }
    }else if(Auth::user()->id_com == 3){
        $d = date('l');
        $jamker = JamKerja::where('nama_hari', $d)->where('shift',Auth::user()->shift)->where('id_com', Auth::user()->id_com)->first();
        $cek_in = $jamker->cek_in;
        $terlambat = $jamker->terlambat;
        $jam_cek = date('H:i', strtotime($cek_in));
        
        $jam = date('H:i:s');
        $date = date('Y-m-d');
        
        $jam1 = strtotime(date('Y-m-d H:i'));
        $jam2 = strtotime(date('Y-m-d '.$jam_cek));
        $diff = floor($jam1 - $jam2)/60;
    
        if($diff < -60){
            return response()->json(
                [
                    "status"=>"tofast",
                ]
            );
        }else{
            $data = new Presensi;
            $data->id_karyawan = Auth::user()->id_karyawan;
            $data->id_jabatan = Auth::user()->id_jabatan;
            $data->pr_jabatan = Auth::user()->pr_jabatan;
            $data->id_kantor = Auth::user()->id_kantor;
            $data->kantor_induk = Auth::user()->kantor_induk;
            $data->nama = Auth::user()->name;
            $data->cek_in = $jam;
            $data->ket = $request->keterangan;
            
            if(Auth::user()->id_shift != NULL){
            $data->id_shift = Auth::user()->id_shift;
            $data->acc_shift = Auth::user()->acc_shift;
            }
            
            if($jam <= $cek_in){
                $data->status = 'Hadir';
            }else{
                $data->status = 'Terlambat';
            };
            
            if($jam <= $cek_in){
                $data->keterlambatan = 0;
            }else{
                $data->keterlambatan = $diff;
            }
            
            $data->latitude = $request->latitude;
            $data->longitude = $request->longitude;
            $data->kantor = $request->kantor;
            
            // if($request->hasFile('image')){
            //     $data->foto = $request->file('image')->getClientOriginalName();
            //     $request->file('image')->move('gambarKehadiran',$data->foto);
            // }else{
            //     $data->foto = null;
            // }
            $data->save();
            return response()->json(
                [
                    "status"=>"sukses",
                    // "data"=>$data
                ]
            );
        }
    }
}


public function updatelokasi(Request $request, $id)
{
    $user = Kantor::find($id);
    $user->latitude = $request->get('latitude',$user->latitude);
    $user->longitude = $request->get('longitude',$user->longitude);
    $user->user_update = Auth::user()->id;
    $user->update();

    $fractal = fractal()
    ->item($user)
    ->transformWith(new KantorTransformer())
    ->toArray();
    
    \LogActivity::addToLog(Auth::user()->name.' Mengupdate Lokasi Presensi App ');
    
    return response()->json([
        "data"=>$fractal,
        "status"=>"sukses"
        ]);
}

public function breakoutin(Request $request, $id)
{
    if(Auth::user()->id_com != null){
        $jam = date('H:i:s');
        $req = $request->status;
        $user = Presensi::find($id);
        
        if($req == 'breakout'){
            $user->break_out = $jam;
        }else if($req == 'breakin'){
            $user->break_in = $jam;
        }
        
        $user->update();
    return response()->json([
        "data"=>$user,
        "status"=>"sukses"
        ]);
    }
    // $fractal = fractal()
    // ->item($user)
    // ->transformWith(new PresensiTransformer())
    // ->toArray();
    return response()->json([
        // "data"=>$fractal,
        "status"=>"gagal"
        ]);
}

public function rincianreq($id){
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $data = RequestKar::find($id);
        return fractal($data, new RequestTransformer())->toArray();
    }
}


public function postreq(Request $request)
{
    
    $jam = date('H:i:s');
    
    $tgl = date('Y-m-d', strtotime($request->tanggal));
    $tanggal = $tgl == '1970-01-01' ? date('Y-m-d') : $tgl;
    $jumlah = $request->jumlah - 1;
    $tg_akhir = date('Y-m-d', strtotime('+'.$jumlah. 'days', strtotime($tanggal)));
    
    
    // $getreq = RequestKar::where('tg_mulai', '>=', $tanggal)->where('tg_akhir', '<=', $tg_akhir)->where('id_karyawan', Auth::user()->id_karyawan)
    //         ->orWhere('tg_mulai', $tg_akhir)->where('id_karyawan', Auth::user()->id_karyawan)
    //         ->orWhere('tg_akhir', $tanggal)->where('id_karyawan', Auth::user()->id_karyawan)
    //         ->get();
    
    // if(count($getreq) > 0){
    //     return response()->json(
    //         [
    //             "status"=>"gagal"
    //         ]
    //     );
    // }else{
    
    if($request->status == 'Pindah Shift'){
        User::findOrFail(Auth::user()->id)->update(['id_shift' => Auth::user()->id.date('Ymd').$request->shift, 'acc_shift' => 2, 'shift' => $request->shift, 'injamker' => 1]);
    }
    
        $data = new RequestKar;
        $data->id_karyawan = Auth::user()->id_karyawan;
        $data->id_jabatan = Auth::user()->id_jabatan;
        $data->pr_jabatan = Auth::user()->pr_jabatan;
        $data->id_kantor = Auth::user()->id_kantor;
        $data->kantor_induk = Auth::user()->kantor_induk;
        $data->nama = Auth::user()->name;
        
        $data->ket = $request->ket;
        $data->status = $request->status;
        $data->jumlah = $request->jumlah;
        $data->tg_mulai = $tanggal;
        $data->tg_akhir = $tg_akhir;
        $data->id_presensi = $request->id_presensi;
        
        if($request->status == 'Pindah Shift'){
            $data->id_shift = Auth::user()->id.date('Ymd').$request->shift;
            $data->shift = $request->shift;
        }
        
        if($request->file != ''){
        $data->lampiran = $request->file('file')->getClientOriginalName();
        $request->file('file')->move('gambarLampiran',$data->lampiran);   
        }
        
        $data->save();
        \LogActivity::addToLog(Auth::user()->name.' Membuat Request '.$request->status.' Presensi App');
        return response()->json(
            [
                "status"=>"sukses",
                "data"=>$data
            ]
        );
        
    // }
}

public function editreq($id, Request $request)
{
    $jam = date('H:i:s');
    
    $tanggal = date('Y-m-d', strtotime($request->tanggal));
    $jumlah = $request->jumlah - 1;
    
    $data = RequestKar::find($id);
    
    $data->ket = $request->ket;
    $data->status = $request->status;
    $data->jumlah = $request->jumlah;
    $data->tg_mulai = $tanggal;
    $data->tg_akhir = date('Y-m-d', strtotime('+'.$jumlah. 'days', strtotime($tanggal)));
    
    if($request->file != ''){
    $data->lampiran = $request->file('file')->getClientOriginalName();
    $request->file('file')->move('gambarLampiran',$data->lampiran);   
    }
    
        if($request->foto != ''){
        $data->foto = $request->file('foto')->getClientOriginalName();
        $request->file('foto')->move('gambarKehadiran',$data->foto);   
        }
    
        $data->latitude = $request->latitude;
        $data->longitude = $request->longitude;
    
    $data->update();
    \LogActivity::addToLog(Auth::user()->name.' Mengupdate Request');
    return response()->json(
        [
            "status"=>"sukses",
            "data"=>$data
        ]
    );
}

public function sisa_req(Request $request){
    // return response()->json($request);
    $id_sta = $request->id_status;
    $id_kar = $request->id_karyawan;
    $tgl    = date('Y-m-d');
    $thn    = date('Y', strtotime($tgl));
    $jen    = Jenreq::find($id_sta);
    
    $per    = $jen->per_limit;
    $limitt    = $jen->jum_limit;
    $jenis  = $jen->jenis;
    
    $get_s  = RequestKar::selectRaw("id_status, status, SUM(jumlah) AS terpakai")
                        ->where('id_karyawan', $id_kar)->whereRaw("(id_status = '$id_sta' OR status = '$jenis')")
                        ->where(function($q) use ($thn){
                            $q->whereYear('tg_mulai', $thn);
                        })
                        ->where('acc', '>', 0)
                        ->groupBy('status')
                        ->first();
                        
    $sisa   = $get_s != null ? $limitt - $get_s->terpakai : $limitt;
    
    return response()->json(
        [
            "data"  => $get_s,
            "sisa"  => $sisa ,
            'limit' => $limitt
        ]
    );
}
// Ganti postreq
public function post_req(Request $request){
  
    // return response()->json(['status' => $request->all()]);
    if($request->hapus){
        RequestKar::find($request->id)->delete();
        return response()->json(['status' => 200]);
    }
    
    $jam        = date('H:i:s');
    $tgl        = date('Y-m-d', strtotime($request->tanggal ?? null));
    $tanggal    = $tgl == '1970-01-01' ? date('Y-m-d') : $tgl;
    $jumlah     = $request->kategori == 'dana' ? 0 : $request->jumlah ?? null - 1;
    $tg_akhir   = date('Y-m-d', strtotime('+'.$jumlah. 'days', strtotime($tanggal)));
    $status     = $request->id_substatus ?? null > 0 ? $request->substatus ?? null : $request->status ?? null;
    $id_status  = $request->id_substatus ?? null > 0 ? $request->id_substatus ?? null : $request->id_status ?? null;
    // $getreq = RequestKar::where('tg_mulai', '>=', $tanggal)->where('tg_akhir', '<=', $tg_akhir)->where('id_karyawan', Auth::user()->id_karyawan)
    //         ->orWhere('tg_mulai', $tg_akhir)->where('id_karyawan', Auth::user()->id_karyawan)
    //         ->orWhere('tg_akhir', $tanggal)->where('id_karyawan', Auth::user()->id_karyawan)
    //         ->get();
    
    // if(count($getreq) > 0){
        // return response()->json(
        //     [
        //         'ket'          => $request->ket ?? NULL,
        // 'jumlah'       => $request->jumlah ?? NULL,
        // 'tg_mulai'     => $tanggal,
        // 'tg_akhir'     => $tg_akhir,
        // 'id_presensi'  => $request->id_presensi ?? NULL,
        // 'status'       => $status,
        // 'id_status'    => $id_status,
        // 'kategori'     => $request->kategori ?? NULL,
        //     ]
        // );
    // }else{
        
       
    if($request->kategori == 'shift'){
        if(Auth::user()->kotug == 'Ungu Laundry') {
            User::findOrFail(Auth::user()->id)->update(['id_shift' => Auth::user()->id.date('Ymd').$request->shift, 'shift' => $request->shift, 'acc_shift' => 2, 'injamker' => 1]);
        }else{
            User::findOrFail(Auth::user()->id)->update(['id_shift' => Auth::user()->id.date('Ymd').$request->shift, 'acc_shift' => 2, 'injamker' => 1]);
        }
        
    }
    
    $data = RequestKar::updateOrCreate(
    ['id_request' => $request->id], // Kondisi pencarian berdasarkan id
    [
        'acc'          => ($request->kategori == 'shift' && Auth::user()->kotug == 'Ungu Laundry') ? 1 : 0,
        'id_karyawan'  => Auth::user()->id_karyawan,
        'id_jabatan'   => Auth::user()->id_jabatan,
        'pr_jabatan'   => Auth::user()->pr_jabatan,
        'id_kantor'    => Auth::user()->id_kantor,
        'id_com'       => Auth::user()->id_com,
        'kantor_induk' => Auth::user()->kantor_induk,
        'nama'         => Auth::user()->name,
        'ket'          => $request->ket ?? NULL,
        'jumlah'       => $request->jumlah ?? NULL,
        'tg_mulai'     => $tanggal,
        'tg_akhir'     => $tg_akhir,
        'id_presensi'  => $request->id_presensi ?? NULL,
        'status'       => $status,
        'id_status'    => $id_status,
        'kategori'     => $request->kategori ?? NULL,
        'jamci'        => $id_status == 0 ? ($request->jamci ?? NULL) : NULL,
        'jambo'        => $id_status == 0 ? ($request->jambo ?? NULL) : NULL,
        'jambi'        => $id_status == 0 ? ($request->jambi ?? NULL) : NULL,
        'jamco'        => $id_status == 0 ? ($request->jamco ?? NULL) : NULL,
        'id_shift'     => $request->kategori == 'shift' ? Auth::user()->id . date('Ymd') . $request->shift : NULL,
        'shift'        => $request->kategori == 'shift' ? $request->shift : NULL,
        'latitude'     => $request->latitude ?? 0,
        'longitude'    => $request->longitude ?? 0,
            ]
        );
        
        // **Handle Upload File jika ada**
        if ($request->hasFile('file')) {
            $fileName = $request->file('file')->getClientOriginalName();
            $request->file('file')->move('gambarLampiran', $fileName);
            $data->update(['lampiran' => $fileName]);
        }
        
        if ($request->hasFile('foto')) {
            $fotoName = $request->file('foto')->getClientOriginalName();
            $request->file('foto')->move('gambarKehadiran', $fotoName);
            $data->update(['foto' => $fotoName]);
        }

    
    \LogActivity::addToLog(Auth::user()->name.' Membuat Request '.$status.' Presensi App');
    return response()->json(
        [
            "status"=>"sukses"
        ]
    );
        
    // }
}

// Ganti editreq
public function edit_req(Request $request){
    $id         = $request->id;
    $jam        = date('H:i:s');
    $tgl        = date('Y-m-d', strtotime($request->tanggal));
    $tanggal    = $tgl == '1970-01-01' ? date('Y-m-d') : $tgl;
    $jumlah     = $request->jumlah - 1;
    $tg_akhir   = date('Y-m-d', strtotime('+'.$jumlah. 'days', strtotime($tanggal)));
    $status     = $request->id_substatus > 0 ? $request->substatus : $request->status;
    $id_status  = $request->id_substatus > 0 ? $request->id_substatus : $request->id_status;
    
    if($request->kategori == 'shift'){
        User::findOrFail(Auth::user()->id)->update(['id_shift' => Auth::user()->id.date('Ymd').$request->shift, 'acc_shift' => 2, 'shift' => $request->shift, 'injamker' => 1]);
    }
    
    $data               = RequestKar::find($id);
    $data->ket          = $request->ket;
    $data->jumlah       = $request->jumlah;
    $data->tg_mulai     = $tanggal;
    $data->tg_akhir     = $tg_akhir;
    $data->id_presensi  = $request->id_presensi;
    $data->status       = $status;
    $data->id_status    = $id_status;
    $data->kategori     = $request->kategori;
    
    if($id_status == 0){
        $data->jamci    = $request->jamci;
        $data->jamco    = $request->jamco;
    }else{
        $data->jamci    = null;
        $data->jamco    = null;
    }
    
    if($request->kategori == 'shift'){
        $data->id_shift = Auth::user()->id.date('Ymd').$request->shift;
        $data->shift    = $request->shift;
    }else{
        $data->id_shift = null;
        $data->shift    = null;
    }
    
    if($request->hasFile('file')){
        $data->lampiran = $request->file('file')->getClientOriginalName();
        $request->file('file')->move('gambarLampiran',$data->lampiran);   
    }
    
    if($request->hasFile('foto')){
        $data->foto     = $request->file('foto')->getClientOriginalName();
        $request->file('foto')->move('gambarKehadiran',$data->foto);   
    }

    $data->latitude     = $request->latitude;
    $data->longitude    = $request->longitude;
        
    $data->update();
    
    \LogActivity::addToLog(Auth::user()->name.' Mengupdate Request');
    return response()->json(
        [
            "status"=>"sukses"
        ]
    );
}


public function post_lap_ren(Request $request)
{
    // return $request->hasFile('file');
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        
        $lapnow = Laporan::where('id_karyawan', Auth::user()->id_karyawan)->whereDate('created_at', date('Y-m-d'))->first();

        // if($lapnow != null){
        //     return response()->json(
        //         [
        //             "status"=>"exist",
        //         ]
        //     );
        // }else{
            $lampiran = null;
            $vn = null;
            if($request->hasFile('file')){
                $lampiran = $request->file('file')->getClientOriginalName();
                $request->file('file')->move('lampiranLaporan', $lampiran);
            }
            
            if($request->vn != ''){
            $vn = $request->file('vn')->getClientOriginalName();
            $request->file('vn')->move('lampiranLaporan', $vn);
            }
            $created = date('Y-m-d H:i:s');
            
            $saveid = Laporan::insertGetId([
                        'id_karyawan'   => Auth::user()->id_karyawan,
                        'id_jabatan'    => Auth::user()->id_jabatan,
                        'pr_jabatan'    => Auth::user()->pr_jabatan,
                        'id_com'     => Auth::user()->id_com,
                        'id_kantor'     => Auth::user()->id_kantor,
                        'kantor_induk'  => Auth::user()->kantor_induk,
                        'nama'          => Auth::user()->name,
                
                        'ket'           => $request->ket,
                        'capaian'       => $request->capaian,
                        'target'        => $request->target == '' ? NULL : $request->target,
                        'sec_vn'        => $request->sec_vn != '' ? $request->sec_vn : 0,
                        'link_lap'      => $request->link_lap != '' ? $request->link_lap : NULL,
                        'lampiran'      => $lampiran,
                        'vn'            => $request->vn != '' ? $vn : NULL,
                        'created_at'    => $created,
                        'updated_at'    => $created,
                    ]);
            
            // $tugas  = $request->tugas != '%5B%5D' ?  json_decode (urldecode($request->tugas)) : [];
            // foreach($tugas as $tug){
            
            $tug = Rencana::where('id', $request->id)->first();
            
                if($tug->id > 0){
                    
                    if($tug->marketing > 0){
                        $upren      = Rencana::where('id', $tug->id)
                                    ->update([
                                                'id_laporan'    => $saveid,
                                            ]);
                    }
                    else{
                        $ren = Rencana::find($tug->id);
                        if($ren->id_range > 0){
                            $uprange    = Rencana::where('id_range', $ren->id_range)
                                        ->update([
                                                    'alasan'        => $request->alasan != null ? $request->alasan : NULL,
                                                    'aktif'         => $request->aktif,
                                                    'tgl_selesai'   => $request->capaian == $request->target ? date('Y-m-d') : NULL,
                                                    'user_aktif'    => $request->aktif > 0 ? NULL : Auth::user()->id,
                                                ]);
                            $bentar = RencanaBln::find($ren->id_rb); 
                            
                            // return $bentar;
                            if($bentar != null){
                                if($bentar->metode == 'kualitatif'){
                                    $caprange   = Rencana::where('id_range', $ren->id_range)
                                                ->whereRaw("tgl_awal > '$ren->tglAwal'")
                                                ->update([
                                                        'capaian'       => $request->capaian,
                                                ]);
                                }
                                
                            }
                        }
                                            
                        $ren->capaian       = $request->capaian;
                        $ren->alasan        = $request->alasan != '' ? $request->alasan : NULL;
                        $ren->aktif         = $request->aktif;
                        $ren->jam_awal      = $request->jamMulai;
                        $ren->jam_akhir     = $request->jamAkhir;
                        $ren->tgl_selesai   = $request->capaian == $request->target ? date('Y-m-d') : NULL;
                        $ren->id_laporan    = $saveid;
                        $ren->user_aktif    = $request->aktif > 0 ? NULL : Auth::user()->id;
                        $ren->update();
                    }
                }
            // }
            
            // $tugnext  = $request->tugas_next != '%5B%5D' ?  json_decode (urldecode($request->tugas_next)) : [];
            // foreach($tugnext as $tug){
            //     if($tug->id > 0){
            //             $ren        = Rencana::find($tug->id);
            //             if($ren->id_range > 0){
            //             $uprange    = Rencana::where('id_range', $ren->id_range)
            //                         ->update([
            //                                     // 'alasan'        => $tug->alasan != '' ? $tug->alasan : NULL,
            //                                     // 'aktif'         => $tug->aktif,
            //                                     'tgl_selesai'   => $tug->capaian == $tug->target ? date('Y-m-d') : NULL,
            //                                     // 'user_aktif'    => $tug->aktif > 0 ? NULL : Auth::user()->id,
            //                                 ]);
                                            
            //             $caprange   = Rencana::where('id_range', $ren->id_range)->whereDate('tgl_awal', '>', $ren->tgl_awal)
            //                         ->update([
            //                                     'capaian'       => $tug->capaian,
            //                                 ]);
            //             }
                                            
            //             $ren->capaian       = $tug->capaian;
            //             // $ren->alasan        = $tug->alasan != '' ? $tug->alasan : NULL;
            //             // $ren->aktif         = $tug->aktif;
            //             $ren->tgl_selesai   = $tug->capaian == $tug->target ? date('Y-m-d') : NULL;
            //             $ren->id_laporan    = $saveid;
            //             // $ren->user_aktif    = $tug->aktif > 0 ? NULL : Auth::user()->id;
            //             $ren->update();
            //     }
            // }
            
            return response()->json(
                [
                    "status"=>"sukses",
                ]
            );
        // }
    }
}

public function postlap(Request $request)
{
    // return $request->all();
    // $lapfol = LapFol::where('id_peg', Auth::user()->id)->whereDate('created_at', date('Y-m-d'))->get();
    // if(count($lapfol) >= 5){
  if(Auth::user()->id_com != null && Auth::user()->presensi != null){
    $hariIni = now()->toDateString(); // Mendapatkan tanggal hari ini dalam format 'YYYY-MM-DD'

    // Cek apakah user sudah membuat laporan hari ini
    // $cekLaporan = Laporan::where('id_karyawan', Auth::user()->id_karyawan)
    //     ->whereDate('created_at', $hariIni)
    //     ->exists();

    // if ($cekLaporan) {
    //     return response()->json([
    //         "status" => "gagal",
    //         "message" => "Anda sudah membuat laporan hari ini. Tidak dapat membuat laporan lagi."
    //     ]);
    // }

    $data = new Laporan;
    $data->id_karyawan = Auth::user()->id_karyawan;
    $data->id_jabatan = Auth::user()->id_jabatan;
    $data->pr_jabatan = Auth::user()->pr_jabatan;
    $data->id_kantor = Auth::user()->id_kantor;
    $data->kantor_induk = Auth::user()->kantor_induk;
    $data->nama = Auth::user()->name;

    $data->ket = $request->ket;
    $data->capaian = $request->capaian;
    $data->target = $request->target;
    $data->sec_vn = $request->sec_vn ?: 0;
    $data->link_lap = $request->link_lap ?: NULL;

    if ($request->hasFile('file')) {
        $data->lampiran = $request->file('file')->getClientOriginalName();
        $request->file('file')->move('lampiranLaporan', $data->lampiran);
    }

    if ($request->hasFile('vn')) {
        $data->vn = $request->file('vn')->getClientOriginalName();
        $request->file('vn')->move('lampiranLaporan', $data->vn);
    }

    $data->save();

    return response()->json([
        "status" => "sukses",
        "message" => "Berhasil laporan!",
        "data" => $data
    ]);
}

    // }else{
    //     return response()->json(
    //     [
    //         "status"=>"gagal",
    //         "data"=>$data
    //     ]
    // );
    // }
}

public function postlap2(Request $request)
{
    // dd('tes');
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        if($request->file != ''){
        $lampiran = $request->file('file')->getClientOriginalName();
        $request->file('file')->move('lampiranLaporan', $lampiran);
        }
        
        if($request->vn != ''){
        $vn = $request->file('vn')->getClientOriginalName();
        $request->file('vn')->move('lampiranLaporan', $vn);
        }
        $created = date('Y-m-d H:i:s');
        
        //Insert Laporan
        $saveid = Laporan::insertGetId([
                    'id_karyawan'   => Auth::user()->id_karyawan,
                    'id_jabatan'    => Auth::user()->id_jabatan,
                    'pr_jabatan'    => Auth::user()->pr_jabatan,
                    'id_kantor'     => Auth::user()->id_kantor,
                    'kantor_induk'  => Auth::user()->kantor_induk,
                    'nama'          => Auth::user()->name,
            
                    // 'ket'           => $request->ket,
                    'capaian'       => $request->capaian,
                    'target'        => $request->target,
                    'sec_vn'        => $request->sec_vn != '' ? $request->sec_vn : 0,
                    'link_lap'      => $request->link_lap != '' ? $request->link_lap : NULL,
                    'lampiran'      => $request->file != '' ? $lampiran : NULL,
                    'vn'            => $request->vn != '' ? $vn : NULL,
                    'created_at'    => $created,
                    'updated_at'    => $created,
                ]);
        
        //insert rencana
        $tugas  = $request->tugas != '%5B%5D' ?  json_decode (urldecode($request->tugas)) : [];
        foreach($tugas as $tug){
            if($tug->id > 0){
                if($tug->marketing > 0){
                    $upren      = Rencana::where('id', $tug->id)
                                ->update([
                                            'id_laporan'    => $saveid,
                                        ]);
                }else{
                    $ren        = Rencana::find($tug->id);
                    
                    if($ren->id_range > 0){
                    $uprange    = Rencana::where('id_range', $ren->id_range)
                                ->update([
                                            'alasan'        => $tug->alasan != '' ? $tug->alasan : NULL,
                                            'aktif'         => $tug->aktif,
                                            'tgl_selesai'   => $tug->capaian == $tug->target ? date('Y-m-d') : NULL,
                                            'user_aktif'    => $tug->aktif > 0 ? NULL : Auth::user()->id,
                                        ]);
                                        
                    $caprange   = Rencana::where('id_range', $ren->id_range)->whereDate('tgl_awal', '>', $ren->tgl_awal)
                                ->update([
                                            'capaian'       => $tug->capaian,
                                        ]);
                    }
                                        
                    $ren->capaian       = $tug->capaian;
                    $ren->alasan        = $tug->alasan != '' ? $tug->alasan : NULL;
                    $ren->aktif         = $tug->aktif;
                    $ren->tgl_selesai   = $tug->capaian == $tug->target ? date('Y-m-d') : NULL;
                    $ren->id_laporan    = $saveid;
                    $ren->user_aktif    = $tug->aktif > 0 ? NULL : Auth::user()->id;
                    $ren->update();
                }
            }
        }
        
        $tugnext  = $request->tugas_next != '%5B%5D' ?  json_decode (urldecode($request->tugas_next)) : [];
        foreach($tugnext as $tug){
            if($tug->id > 0){
                    $ren        = Rencana::find($tug->id);
                    if($ren->id_range > 0){
                    $uprange    = Rencana::where('id_range', $ren->id_range)
                                ->update([
                                            // 'alasan'        => $tug->alasan != '' ? $tug->alasan : NULL,
                                            // 'aktif'         => $tug->aktif,
                                            'tgl_selesai'   => $tug->capaian == $tug->target ? date('Y-m-d') : NULL,
                                            // 'user_aktif'    => $tug->aktif > 0 ? NULL : Auth::user()->id,
                                        ]);
                                        
                    $caprange   = Rencana::where('id_range', $ren->id_range)->whereDate('tgl_awal', '>', $ren->tgl_awal)
                                ->update([
                                            'capaian'       => $tug->capaian,
                                        ]);
                    }
                                        
                    $ren->capaian       = $tug->capaian;
                    // $ren->alasan        = $tug->alasan != '' ? $tug->alasan : NULL;
                    // $ren->aktif         = $tug->aktif;
                    $ren->tgl_selesai   = $tug->capaian == $tug->target ? date('Y-m-d') : NULL;
                    $ren->id_laporan    = $saveid;
                    // $ren->user_aktif    = $tug->aktif > 0 ? NULL : Auth::user()->id;
                    $ren->update();
            }
        }
        
        $task  = $request->task != '%5B%5D' ?  json_decode (urldecode($request->task)) : [];
        foreach($task as $tam){
            
            $dari = date('Y-m-d');
            $sampai = date('Y-m-d', strtotime($tam->datepeng2)) <= date('Y-m-d') ? date('Y-m-d') : date('Y-m-d', strtotime($tam->datepeng2));
            
            $durasi = $dari == $sampai ? 'daily' : 'range';
            
            $tanggalAwal = new DateTime($dari);
            $tanggalAkhir = new DateTime($sampai);
            
            $selisihHari = 0;
            $dateList = [];
            
            while ($tanggalAwal <= $tanggalAkhir) {
                $selisihHari++;
                $dateList[] = $tanggalAwal->format('Y-m-d');
                $tanggalAwal->modify('+1 day');
            }
            
            $data = [];
            
            $last = 0;
            
            for($i = 0; $i < $selisihHari; $i++){
                
            // $created = date('Y-m-d H:i:s');
            
            // if($i > 0){    
            //     $last = Rencana::where('created_at', $created)->where('id_karyawan', Auth::user()->id_karyawan)->first()->id;
            //     if($i == 1){
            //         $upren = Rencana::where('id', $last)->update(['id_range' => $last]);
            //     }
            // }
            
                if($durasi == 'range' && $dateList[$i] == date('Y-m-d')){
                    $get_idr = Rencana::insertGetId([
                                    'id_karyawan'   => Auth::user()->id_karyawan,
                                    'tugas'         => $tam->namatugas,
                                    'durasi'        => $durasi,
                                    'tgl_awal'      => $dateList[$i],
                                    'tgl_akhir'     => $sampai,
                                    'capaian'       => $tam->capaian,
                                    'target'        => $tam->targetnya,
                                    'user_insert'   => Auth::user()->id,
                                    'marketing'     => 0,
                                    'id_range'      => null,
                                    'deskripsi'    => $request->ket,
                                    'id_laporan'    => $saveid,
                                    'created_at'    => date('Y-m-d H:i:s'),
                                    'updated_at'    => date('Y-m-d H:i:s'),
                                ]);
                    $last = $get_idr;
                }else{
                    if($durasi == 'range' && $i == 1){
                        Rencana::where('id', $last)->update(['id_range' => $last]);
                    }
                    Rencana::create([
                                    'id_karyawan'   => Auth::user()->id_karyawan,
                                    'tugas'         => $tam->namatugas,
                                    'durasi'        => $durasi,
                                    'tgl_awal'      => $dateList[$i],
                                    'tgl_akhir'     => $sampai,
                                    'deskripsi'    => $request->ket,
                                    'capaian'       => $tam->capaian,
                                    'target'        => $tam->targetnya,
                                    'user_insert'   => Auth::user()->id,
                                    'marketing'     => 0,
                                    'id_range'      => $durasi == 'daily' ? null : $last,
                                    'id_laporan'    => $durasi == 'daily' ? $saveid : null,
                                ]);
                }
            }
            
            \LogActivity::addToLog(Auth::user()->name . ' Membuat Rencana untuk ID' . Auth::user()->id_karyawan);
        }
        
        return response()->json(
            [
                "status"=>"sukses",
                "data"=>$data
            ]
        );
    
    }
}



public function postpresout($id, Request $request)
{
    
    // $day = date('D');
    //     $dayList = array(
    //         'Sun' => 'Minggu',
    //         'Mon' => 'Senin',
    //         'Tue' => 'Selasa',
    //         'Wed' => 'Rabu',
    //         'Thu' => 'Kamis',
    //         'Fri' => 'Jumat',
    //         'Sat' => 'Sabtu'
    //     );
    
    // $jamker = JamKerja::where('nama_hari', $dayList[$day])->first();
    
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $d = date('l');
        $jamker = JamKerja::where('nama_hari', $d)->where('shift',Auth::user()->shift)->where('id_com', Auth::user()->id_com)->first();
        $cek_out = $jamker->cek_out;
        $jam = date('H:i:s');
        // $jam = $request->cek_out;
        
        $data = Presensi::find($id);
        
        $data->cek_out = $jam;
        
        if($jam >= $cek_out){
            $data->acc = 1;
        }else{
            $data->acc = 0;
        };
        
        $data->update();
        
        return response()->json(
            [
                "status"=>"sukses",
                "message" => "Berhasil absen pulang!"
            ]);
    }
}

public function reqnowid ($id)
{
    $date = date('Y-m-d');
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $trf = RequestKar::where('id_karyawan',$id)->whereDate('created_at','<=',$date)->whereDate('tg_akhir','>=',$date)
                ->whereIn('id_karyawan', function($query) {
                        $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                })
                ->get();
        return fractal($trf, new RequestTransformer())->toArray();
    }
}

public function reqnowjab ($id)
{
    $date = date('Y-m-d');
    if(Auth::user()->id_kantor == 4){
       $trf = RequestKar::leftjoin('jabatan', 'request.id_jabatan', '=' ,'jabatan.id')
              ->leftjoin('karyawan', 'request.id_karyawan', '=', 'karyawan.id_karyawan')
              ->where(function($quer) use ($date){
                  $quer->whereDate('request.created_at','<=',$date)->whereDate('request.tg_akhir','>=',$date)
                        ->orWhere(function($query) {
                          $query->where('request.acc',0)->orWhere('request.acc',2);
                        })
                        ->whereMonth('request.created_at', date('m'))->whereYear('request.created_at', date('Y'));
                })
              ->where('request.pr_jabatan', $id)
            //   ->where('request.pr_jabatan', $id)->whereDate('request.created_at','<=',$date)->whereDate('request.tg_akhir','>=',$date)
              ->select('request.*', 'jabatan.jabatan', 'karyawan.nomerhp')
              ->orderBy('request.created_at', 'desc')
              ->get();
    }else{
       $trf = RequestKar::leftjoin('jabatan', 'request.id_jabatan', '=' ,'jabatan.id')
              ->leftjoin('karyawan', 'request.id_karyawan', '=', 'karyawan.id_karyawan')
              ->where(function($quer) use ($date){
                  $quer->whereDate('request.created_at','<=',$date)->whereDate('request.tg_akhir','>=',$date)
                        ->orWhere(function($query) {
                          $query->where('request.acc',0)->orWhere('request.acc',2);
                        })
                        ->whereMonth('request.created_at', date('m'))->whereYear('request.created_at', date('Y'));
                })
              ->where('request.pr_jabatan', $id)->where('request.id_kantor', Auth::user()->id_kantor)
            //   ->where('request.pr_jabatan', $id)->whereDate('request.created_at','<=',$date)->whereDate('request.tg_akhir','>=',$date)->where('request.id_kantor', Auth::user()->id_kantor)
              ->select('request.*', 'jabatan.jabatan', 'karyawan.nomerhp')
              ->orderBy('request.created_at', 'desc')
              ->get();
    }
    return fractal($trf, new RequestTransformer())->toArray();
}

public function reqnowkan ($id)
{
    $date = date('Y-m-d');

    $trf = RequestKar::leftjoin('jabatan', 'request.id_jabatan', '=' ,'jabatan.id')
           ->leftjoin('karyawan', 'request.id_karyawan', '=', 'karyawan.id_karyawan')
        //   ->where(function($quer) use ($date){
        //       $quer->whereDate('request.created_at','<=',$date)->whereDate('request.tg_akhir','>=',$date)
        //             ->orWhere(function($query) {
        //               $query->where('request.acc',0)->orWhere('request.acc',2);
        //             })
        //             ->whereMonth('request.created_at', date('m'));
        //   })
        //   ->where(function($query) use ($id) {
        //       $query->where('request.id_kantor',$id)->orWhere('request.kantor_induk',$id);
        //   })
        //   ->where('request.id_karyawan', '!=', Auth::user()->id_karyawan)
           
          ->where('request.id_kantor', $id)->whereDate('request.created_at','<=',$date)->whereDate('request.tg_akhir','>=',$date)->where('request.id_karyawan', '!=', Auth::user()->id_karyawan)
          ->orWhere('request.kantor_induk', $id)->whereDate('request.created_at','<=',$date)->whereDate('request.tg_akhir','>=',$date)->where('request.id_karyawan', '!=', Auth::user()->id_karyawan)
          ->whereMonth('request.created_at', date('m'))->whereYear('request.created_at', date('Y'))
           ->select('request.*', 'jabatan.jabatan', 'karyawan.nomerhp')
           ->orderBy('request.created_at', 'desc')
           ->get();
    return fractal($trf, new RequestTransformer())->toArray();
}

public function reqnowall ()
{
    $date = date('Y-m-d');

    $trf = RequestKar::leftjoin('jabatan', 'request.id_jabatan', '=' ,'jabatan.id')
           ->leftjoin('karyawan', 'request.id_karyawan', '=', 'karyawan.id_karyawan')
           ->where(function($quer) use ($date){
               $quer->whereDate('request.created_at','<=',$date)->whereDate('request.tg_akhir','>=',$date)
                    ->orWhere(function($query) {
                      $query->where('request.acc',0)->orWhere('request.acc',2);
                    })
                    ->whereMonth('request.created_at', date('m'))->whereYear('request.created_at', date('Y'));
           })
           ->where(function($query) {
               $query->where('request.id_kantor',Auth::user()->id_kantor)->orWhere('request.pr_jabatan',Auth::user()->id_jabatan);
           })
           ->select('request.*', 'jabatan.jabatan', 'karyawan.nomerhp')
           ->orderBy('request.created_at', 'desc')
           ->get();
    return fractal($trf, new RequestTransformer())->toArray();
}

public function daftareq ($id)
{
    $date = date('Y-m-d');
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $trf = RequestKar::select('request.*')->where('id_karyawan', $id)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))
                ->whereIn('id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })
               ->orderBy('created_at', 'desc')
               ->get();
        return fractal($trf, new RequestTransformer())->toArray();
    }
}

public function presatasan($id){
    
    if(Auth::user()->id_kantor == 4){
        $kar = Karyawan::leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                ->selectRaw("karyawan.*, jabatan.id, jabatan.jabatan, IF(MONTH(karyawan.tgl_gaji) = MONTH(NOW()) AND YEAR(karyawan.tgl_gaji) = YEAR(NOW()),1,0) AS kondisi")
                ->where('karyawan.id_com', Auth::user()->id_com)->where('karyawan.pr_jabatan', $id)->where('karyawan.aktif', 1)->get();
    }else{
        $kar = Karyawan::leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                ->select(\DB::raw("karyawan.*, jabatan.id, jabatan.jabatan, IF(MONTH(karyawan.tgl_gaji) = MONTH(NOW()) AND YEAR(karyawan.tgl_gaji) = YEAR(NOW()),1,0) AS kondisi"))
                ->where('karyawan.aktif', 1)
                ->where('karyawan.pr_jabatan', $id)
                ->where('karyawan.id_com', Auth::user()->id_com)
                ->where('karyawan.id_kantor', Auth::user()->id_kantor)->get();
    }
         return fractal($kar, new RwyKarTransformer())->toArray();
}

public function presatasankacab($id){
        $kar = Karyawan::leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
        ->select(\DB::raw("karyawan.*, jabatan.id, jabatan.jabatan, IF(MONTH(karyawan.tgl_gaji) = MONTH(NOW()) AND YEAR(karyawan.tgl_gaji) = YEAR(NOW()),1,0) AS kondisi"))
        ->where('karyawan.id_kantor', $id)->where('karyawan.aktif', 1)
        ->orWhere('karyawan.kantor_induk', $id)->where('karyawan.aktif', 1)
        ->where('karyawan.id_com', Auth::user()->id_com)
        ->get();
        return fractal($kar, new RwyKarTransformer())->toArray();
}

public function presatasanadm(){
        $kar = Karyawan::leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                ->select(\DB::raw("karyawan.*, jabatan.id, jabatan.jabatan, IF(MONTH(karyawan.tgl_gaji) = MONTH(NOW()) AND YEAR(karyawan.tgl_gaji) = YEAR(NOW()),1,0) AS kondisi"))
                ->where('karyawan.aktif', 1)
                ->orderBy('war_naik', 'desc')
                ->where('karyawan.id_com', Auth::user()->id_com)
                ->get();
        return fractal($kar, new RwyKarTransformer())->toArray();
}
   
public function accreq($id, Request $request)
{
    // $req = RequestKar::where('id_request', $id)->first();
    $datareq = RequestKar::find($id);
    
    $pres = Presensi::where('id_reqbolos', $id)->get();
    if(count($pres) > 0){
        $up_pres = Presensi::where('id_reqbolos', $id)->update(['ket' => $datareq->ket, 'status' => $datareq->status, 'id_req' => $id]);
    }
    
    $datareq->acc = 1;
    $datareq->alasan = null;
    $datareq->user_update = Auth::user()->id;
    $datareq->update();

    
    $tanggal = date('Y-m-d', strtotime($request->tanggal));    
    $jam = date('H:i:s', strtotime($datareq->created_at));
    
    if($request->status == 'Pulang Awal'){
        $data = Presensi::find($request->id_presensi);
        $data->id_request = $id;
        $data->cek_out = $jam;
        $data->acc = 1;
    
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Menyetujui Request '.$request->nama.' Pulang Awal');
    }else if($request->status == 'Dinas Luar'){
        $data = Presensi::find($request->id_presensi);
        $data->id_request = $id;
    
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Menyetujui Request '.$request->nama.' Dinas Luar');
    }else if($request->status == 'Perdin' && $request->id_presensi != ''){
        $data = Presensi::find($request->id_presensi);
        $data->id_request = $id;
        $data->cek_out = $jam;
        $data->acc = 1;
    
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Menyetujui Request '.$request->nama.' Perdin');
    }else if($request->status == 'Pindah Shift'){
        
        User::where('id_shift', $datareq->id_shift)->update(['id_shift' => NULL, 'acc_shift' => 1]);
        Presensi::where('id_shift', $datareq->id_shift)->update(['acc_shift' => 1]);
        
        \LogActivity::addToLog(Auth::user()->name.' Menyetujui Request '.$request->nama.' Pindah Shift');
    }else{
        if(count($pres) == 0){
        $data = new Presensi;
        $data->id_karyawan = $request->id_karyawan;
        $data->id_jabatan = $request->id_jabatan;
        $data->pr_jabatan = $request->pr_jabatan;
        $data->id_kantor = $request->id_kantor;
        $data->kantor_induk = $request->kantor_induk;
        $data->nama = $request->nama;
        // $data->cek_in = $jam;
        $data->ket = $request->ket;
        $data->status = $request->status;
        $data->jumlah = $request->jumlah;
        $data->created_at = $tanggal . ' ' . $jam ;
        $data->lampiran = $request->lampiran;
        $data->id_req = $id;
        $data->acc = 1;
        
        if($request->status == 'Perdin'){
            $data->foto = $request->foto;
            $data->latitude = $request->latitude;
            $data->longitude = $request->longitude;
        }
        
        $data->save();
        \LogActivity::addToLog(Auth::user()->name.' Menyetujui Request '.$request->nama.' '.$request->status);
        }
    }
    return response()->json(
        [
            "status"=>"sukses",
            // "data"=>$data
        ]
    );
}

public function rejectreq($id, Request $request)
{
    $datareq = RequestKar::find($id);
    
    $pres = Presensi::where('id_reqbolos', $id)->get();
    if(count($pres) > 0){
        $up_pres = Presensi::where('id_reqbolos', $id)->update(['ket' => '(Request '.$datareq->status.' Ditolak Dengan Alasan : '.$datareq->alasan.') | '.$datareq->ket, 'status' => 'Bolos']);
    }
    
    if($datareq->acc = 1){
    if($request->status == 'Pulang Awal'){
        $data = Presensi::find($request->id_presensi);
        $data->id_request = null;
        $data->cek_out = null;
        $data->acc = 0;
    
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Menolak Request '.$request->nama.' Pulang Awal');
        
    }else if($request->status == 'Dinas Luar'){
        $data = Presensi::find($request->id_presensi);
        $data->id_request = null;
    
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Menolak Request '.$request->nama.' Dinas Luar');
    }else if($request->status == 'Pindah Shift'){
        
        User::where('id_karyawan', $datareq->id_karyawan)->update(['id_shift' => $datareq->id_shift, 'acc_shift' => 2]);
        Presensi::where('id_shift', $datareq->id_shift)->update(['acc_shift' => 2]);
        
        \LogActivity::addToLog(Auth::user()->name.' Menolak Request '.$request->nama.' Pindah Shift');
    }else if($request->status == 'Perdin' && $request->id_presensi != ''){
        $data = Presensi::find($request->id_presensi);
        $data->id_request = null;
        $data->cek_out = null;
        $data->acc = 0;
    
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Menolak Request '.$request->nama.' Perdin');
    }else{
        $data = Presensi::where('id_req', $id)->update(['ket' => '(Request '.$datareq->status.' Ditolak Dengan Alasan : '.$datareq->alasan.') | '.$datareq->ket, 'status' => 'Bolos']);
    }
    }
    
    $datareq->alasan = $request->alasan;
    $datareq->acc = 2;
    $datareq->user_update = Auth::user()->id;
    $datareq->update();
    
    return response()->json(
        [
            "status"=>"sukses"
        ]
    );
}

public function lapnowid ($id)
{
    $date = date('Y-m-d');
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $trf = Laporan::where('id_karyawan',$id)->whereDate('created_at',$date)
                        ->whereIn('id_karyawan', function($query) {
                                $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })
                        ->get();
        return fractal($trf, new LaporanTransformer())->toArray();
    }
}

public function getlapid ($id)
{
    $date = date('Y-m-d');
    $month = date('m');
    $year = date('Y');
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){

        $trf = Laporan::where('id_karyawan',$id)->whereMonth('created_at', $month)->whereYear('created_at', $year)
                        ->whereIn('id_karyawan', function($query) {
                                $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })
                        ->orderBy('created_at', 'DESC')->get();
        return fractal($trf, new LaporanTransformer())->toArray();
    }
}

//Ganti Laporan saya
public function get_lapid(Request $req)
{
    // return($req->all());
    $id_karyawan = $req->id_kar;
    $tgl1 = $req->tgl1 == '' ? date('Y-m-01') : $req->tgl1;
    $tgl2 = $req->tgl2 == '' ? date('Y-m-d') : $req->tgl2;
    
    if($req->tab == 'tab1'){
         $trf = Laporan::select('laporan.*', DB::raw('COUNT(rencana.id_laporan) as total_tugas, DATE(laporan.created_at) as tanggal'))
            ->leftJoin('rencana', 'laporan.id_laporan', '=', 'rencana.id_laporan')
            ->where('laporan.id_karyawan', $id_karyawan)
            ->whereRaw("DATE_FORMAT(laporan.created_at, '%Y-%m-%d') >= '$tgl1'")
            ->whereRaw("DATE_FORMAT(laporan.created_at, '%Y-%m-%d') <= '$tgl2'")
            // ->whereMonth('laporan.created_at', $month)
            // ->whereYear('laporan.created_at', $year)
            ->groupBy('laporan.tanggal')
            // ->groupBy('laporan.id')
            ->orderBy('laporan.created_at', 'DESC')
            ->get();
            
        return $trf;
    }

    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
      // Laporan yang memiliki rencana (Dikelompokkan berdasarkan tanggal)
$queryWithRencana = Laporan::select(
        'laporan.*', 
        DB::raw('COUNT(rencana.id_laporan) as total_tugas'),
        DB::raw("DATE_FORMAT(laporan.created_at, '%Y-%m-%d') as created_date")
    )
    ->leftJoin('rencana', 'laporan.id_laporan', '=', 'rencana.id_laporan')
    ->where('laporan.id_karyawan', $id_karyawan)
    ->whereRaw("DATE(laporan.created_at) BETWEEN ? AND ?", [$tgl1, $tgl2])
    ->groupBy('created_date');

// Laporan yang tidak memiliki rencana (Langsung tampil tanpa gruping)
$queryWithoutRencana = Laporan::select(
        'laporan.*', 
        DB::raw('0 as total_tugas'),
        DB::raw("DATE_FORMAT(laporan.created_at, '%Y-%m-%d') as created_date")
    )
    ->where('laporan.id_karyawan', $id_karyawan)
    ->whereRaw("DATE(laporan.created_at) BETWEEN ? AND ?", [$tgl1, $tgl2])
    ->whereNotExists(function ($query) {
        $query->select(DB::raw(1))
              ->from('rencana')
              ->whereRaw('rencana.id_laporan = laporan.id_laporan');
    });

// Gabungkan hasil query
$trf = $queryWithRencana->union($queryWithoutRencana)
    ->orderBy('created_date', 'DESC')
    ->get();

return $trf;


     
    }
}
public function getlaprange ($id, $tanggal1, $tanggal2)
{
    if(Auth::user()->id_com != null && Auth::user()->aktif == 1 && Auth::user()->presensi != null){
        $trf = Laporan::where('id_karyawan',$id)->whereDate('created_at','>=', $tanggal1)->whereDate('created_at','<=', $tanggal2)
                ->whereIn('id_karyawan', function($query) {
                    $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                })
                ->orderBy('created_at', 'DESC')->get();
        return fractal($trf, new LaporanTransformer())->toArray();
    }
}

public function feedback(Request $request){
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $data = new Feedback;
        $data->id_karyawan = Auth::user()->id_karyawan;
        $data->id_jabatan = Auth::user()->id_jabatan;
        $data->pr_jabatan = Auth::user()->pr_jabatan;
        $data->id_kantor = Auth::user()->id_kantor;
        $data->kantor_induk = Auth::user()->kantor_induk;
        $data->nama_atasan = Auth::user()->name;
        
        $data->id_laporan = $request->id_laporan;
        $data->feedback = $request->feedback;
        
        if($request->sec_vn != ''){
            $data->sec_vn = $request->sec_vn;
        }else{
            $data->sec_vn = 0;
        }
        
        
        if($request->vn != ''){
        $data->vn = $request->file('vn')->getClientOriginalName();
        $request->file('vn')->move('lampiranLaporan',$data->vn);
        }
        
        $data->save();
        
        $datalap = Laporan::find($request->id_laporan);
        $datalap->stat_feed = 1;
        
        $datalap->update();
        
         return response()->json(
            [
                "status"=>"sukses",
                "data"=>$data
            ]
        );
    }
    
}

// public function getlapjab ($id)
// {
//     $date = date('Y-m-d');
//     $month = date('m');
//     $year = date('Y');

//   $trf = Laporan::join('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
//           ->join('karyawan', 'laporan.id_karyawan', '=', 'karyawan.id_karyawan')
//           ->where('laporan.pr_jabatan', $id)->whereMonth('laporan.created_at', $month)->whereYear('laporan.created_at', $year)
//           ->select('laporan.*', 'jabatan.jabatan', 'karyawan.nomerhp')
//           ->get();
//     return fractal($trf, new LaporanTransformer())->toArray();
// }

// public function getlapkan ($id)
// {
//     $date = date('Y-m-d');
//     $month = date('m');
//     $year = date('Y');

//     $trf = Laporan::join('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
//           ->join('karyawan', 'laporan.id_karyawan', '=', 'karyawan.id_karyawan')
//           ->where('laporan.id_kantor', $id)->whereMonth('laporan.created_at', $month)->whereYear('laporan.created_at', $year)->orWhere('laporan.kantor_induk', $id)->whereMonth('laporan.created_at', $month)->whereYear('laporan.created_at', $year)
//           ->select('laporan.*', 'jabatan.jabatan', 'karyawan.nomerhp')
//           ->get();
//     return fractal($trf, new LaporanTransformer())->toArray();
// }

// public function getlapdir ()
// {
//     $date = date('Y-m-d');
//     $month = date('m');
//     $year = date('Y');

//     $trf = Laporan::join('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
//           ->join('karyawan', 'laporan.id_karyawan', '=', 'karyawan.id_karyawan')
//           ->whereMonth('laporan.created_at', $month)->whereYear('laporan.created_at', $year)
//           ->select('laporan.*', 'jabatan.jabatan', 'karyawan.nomerhp')
//           ->get();
//     return fractal($trf, new LaporanTransformer())->toArray();
// }



public function lapnowjab ($id)
{
    $date = date('Y-m-d');
    if(Auth::user()->id_kantor == 4){
        $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
              ->select('laporan.*', 'jabatan.jabatan') 
              ->where('laporan.pr_jabatan',$id)->whereDate('laporan.created_at',$date)
              ->whereIn('laporan.id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                    })
              ->get();
    }else{
        $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
              ->select('laporan.*', 'jabatan.jabatan') 
              ->where('laporan.pr_jabatan',$id)->whereDate('laporan.created_at',$date)->where('laporan.id_kantor', Auth::user()->id_kantor)
              ->whereIn('laporan.id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                    })
              ->get();
    }
    return fractal($trf, new LaporanTransformer())->toArray();
}

public function lapnowkan ($id)
{
    $date = date('Y-m-d');

    $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
          ->select('laporan.*', 'jabatan.jabatan') 
          ->where('laporan.kantor_induk', $id)->whereDate('laporan.created_at',$date)->orWhere('laporan.id_kantor', $id)->whereDate('laporan.created_at',$date)->get();
    return fractal($trf, new LaporanTransformer())->toArray();
}

public function lapnowall ()
{
    $date = date('Y-m-d');

    $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
          ->select('laporan.*', 'jabatan.jabatan') 
          ->whereDate('laporan.created_at',$date)
          ->whereIn('laporan.id_karyawan', function($query) {
                    $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
            })
          ->get();
    return fractal($trf, new LaporanTransformer())->toArray();
}

public function listfeedback($id){
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $data = Feedback::where('id_laporan',$id)->get();
        return fractal($data, new FeedbackTransformer())->toArray();
    }
}

public function listfeedbackid($id){
    $date = date('Y-m-d');
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $data = Feedback::where('id_karyawan',$id)->whereDate('created_at',$date)->get();
        return fractal($data, new FeedbackTransformer())->toArray();
    }
}

public function updatepass($id, Request $request)
{
    
    $user = User::findOrFail($id);
    // Cek password lama
    if (!\Hash::check($request->password, $user->password)) {
        return response()->json(["status" => "gagal", "message" => "Password lama salah"]);
    }

    // Cek password baru & konfirmasi
    if ($request->newpassword !== $request->konpassword) {
        return response()->json(["status" => "gagal", "message" => "Password baru dan konfirmasi password beda"]);
    }

    // Update password
    $user->update([
        'password' => \Hash::make($request->newpassword),
    ]);

    \LogActivity::addToLog(Auth::user()->name . ' Merubah Password Presensi App ');

    return response()->json(["status" => "sukses", "message" => "Berhasil ganti password"]);
}


public function getjamker ($hari)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $d = date('l');
        $trf = JamKerja::where('nama_hari',$d)->where('shift',Auth::user()->shift)->where('id_com', Auth::user()->id_com)->get();
        return fractal($trf, new JamkerTransformer())->toArray();
    }
}

public function jamker ($shift)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $trf = JamKerja::where('shift',$shift)->where('id_com', Auth::user()->id_com)->get();
        
        User::findOrFail(Auth::user()->id)->update(['injamker' => 0]);  
        return fractal($trf, new JamkerTransformer())->toArray();
    }
}

public function editjamker($id, Request $request)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $data = JamKerja::find($id);
        
        $data->cek_in = $request->cek_in;
        $data->terlambat = $request->terlambat;
        $data->break_out = $request->break_out;
        $data->break_in = $request->break_in;
        $data->cek_out = $request->cek_out;
        $data->status = $request->status;
        $data->user_update = Auth::user()->id;
        
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Jam Kerja');
        return response()->json(
            [
                "status"=>"sukses",
                "data"=>$data
            ]
        );
    }
}


public function getgaji($id){
    
    $today = new DateTime("today");
    
    $day    = date('d'); 
    $tg     = date('Y-m-01', strtotime(date('Y-m-d')));
    // $kar = Karyawan::selectRaw("karyawan.*, IF(($day >= 20 AND karyawan.tgl_gaji >= $tg AND MONTH(karyawan.tgl_gaji) <= MONTH(NOW()) AND YEAR(karyawan.tgl_gaji) <= YEAR(NOW())) OR (karyawan.tgl_gaji < $tg), 0, 1) AS kondisi")->where('id_karyawan', $id)->where('id_com', Auth::user()->id_com)->first();
    $kar = Karyawan::selectRaw("karyawan.*, 
                                IF('$day' >= 25, 
                                    IF(karyawan.tgl_gaji >= DATE(NOW()),1,0), 
                                    IF(MONTH(karyawan.tgl_gaji) = MONTH(NOW()) AND YEAR(karyawan.tgl_gaji) = YEAR(NOW()),1,0)
                                    ) AS kondisi
                                ")
            ->where('id_karyawan', $id)->where('id_com', Auth::user()->id_com)->first();
    // dd($kar->nm_pasangan);
    $tot_pasangan = 0;
    if($kar->status_nikah == 'Menikah' && $kar->nm_pasangan != null){
        $istri = unserialize($kar->nm_pasangan);
        foreach($istri as $key => $value){
            $tot_pasangan += 1;
        }
    }
    
    $anak = unserialize($kar->nm_anak); 
    $tgl = unserialize($kar->tgl_lahir_anak);
    $sts = unserialize($kar->status_anak);
    
    $tot_anak = 0;
    if($kar->nm_anak != null){
    foreach($anak as $key => $value){
        $tt = new DateTime($tgl[$key]);
        if($today->diff($tt)->y <= 21 && $sts[$key] == 'Belum Menikah'){
            $tot_anak += 1;
        }
    }
    }
    $tgl_gaji = $kar->tgl_gaji;
    
    // return $tgl_gaji;
    $month = date("m",strtotime($tgl_gaji));
    $year = date("Y",strtotime($tgl_gaji));
    $tj = Tunjangan::where('id_com', Auth::user()->id_com)->first();
    $drh = Daerah::where('id_daerah', $kar->id_daerah)->first();
    
    // return $id;
    
    if($kar->jabatan == $tj->kolektor || $kar->jabatan == $tj->spv_kol || $kar->jabatan == $tj->sokotak){
        $kolek = Transaksi_Perhari::leftjoin('users', 'transaksi_perhari.id', '=', 'users.id')
                ->select(\DB::raw("
                    SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.jumlah, 0)) AS Omset,
                    SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.honor, 0)) AS honor,
                    SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.bonus_cap, 0)) AS boncap
                    "))
                ->where('users.id_karyawan',$id)->first();
                
        // return $kolek;
        
        if($month == date('m') && $year == date('Y')){
            $datkol = User::leftjoin('donatur', 'donatur.petugas', '=', 'users.name')
                ->select(\DB::raw("
                COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput' 
                        AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS totkun,
                COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput'
                        AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS totup"))
                ->where('users.id_karyawan',$id)->first();
        }else{
            $datkol = Rinbel::select('totkun', 'totup')
                ->where('id_karyawan',$id)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->first();
        // return($datkol);
        }  
        
                
        if($kar->jabatan == $tj->kolektor){
            $honor = $kolek != null ? $kolek->honor : 0;
            $boncap = $kolek != null ? $kolek->boncap : 0;
            if ($kolek->Omset <= 10000000){
                $bon = $kolek->Omset * 4/100;
            }elseif ($kolek->Omset > 10000000 && $kolek->Omset <= 20000000){
                $bon = ($kolek->Omset - 10000000) * 5/100 + 400000; 
            }elseif ($kolek->Omset > 20000000){
                $bon = ($kolek->Omset - 20000000) * 6/100 + 900000;
            }else{
                $bon = 0;
            }
        // $rinbon= [
        //             'bonus' => ['Bonus Capaian Target', 'Bonus Omset'],
        //             'jumlah' => [$boncap, $bon]
        //          ];
        // return response()->json(['data', $tj]);
        if($kar->status_kerja == 'Magang'){
            $rinbon= [
                        ['bonus' => 'Omset',
                         'jumlah' => round($bon)],
                        ['bonus'=> 'Capaian Target',
                         'jumlah' => round($boncap)],
                     ];
                 
            $potongan = [
                            ['nampot' => 'Potongan',
                             'jumlah' => 0],
                        ];
            $bonkol = $boncap + $bon;
        }else{   
            $rinbon= [
                        ['bonus' => 'Omset',
                         'jumlah' => round($bon)],
                        ['bonus'=> 'Capaian Target',
                         'jumlah' => round($boncap)],
                     ];     
                     
            $potongan = [
                            ['nampot' => 'Belum Dikunjungi',
                             'jumlah' => is_object($datkol)  ? $datkol->totkun * $tj->potongan : 0 ] ,
                            ['nampot'=> 'Tutup 1x',
                             'jumlah' => is_object($datkol) ? $datkol->totup * $tj->potongan : 0],
                        ];
            $bonkol = $boncap + $bon;
        }
        $totpot = is_object($datkol) ? ($datkol->totkun * $tj->potongan) + ($datkol->totup * $tj->potongan) : 0;
        
        }elseif($kar->jabatan == $tj->spv_kol){
            $kol = Transaksi::
                select(\DB::raw("
                    SUM(IF( MONTH(created_at) = MONTH('$tgl_gaji') AND YEAR(created_at) = YEAR('$tgl_gaji') AND via_input = 'transaksi' 
                        AND id_koleks IN (SELECT id FROM users WHERE id_jabatan = $tj->kolektor AND id_spv = $id), jumlah, 0)) AS Omtim
                    "))->first();
        if($month == date('m') && $year == date('Y')){
            $datkolspv = User::leftjoin('donatur', 'donatur.petugas', '=', 'users.name')
                ->select(\DB::raw("
                COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput'
                        AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS belkun,
                COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput'
                        AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS beltup"))
                // ->where('users.id_karyawan',$id)
                ->whereIn('users.id_karyawan', function($query) use ($id) {
                    $query->select('id_karyawan')->from('users')->where('id_spv', $id);
                })->first();
        }else{
            $datkolspv = Rinbel::select(\DB::raw("
                SUM(IF(MONTH(tanggal) = '$month' AND YEAR(tanggal) = '$year' AND id_karyawan IN (SELECT id_karyawan FROM users WHERE id_spv = '$id'), belkun, 0)) AS belkun,
                SUM(IF(MONTH(tanggal) = '$month' AND YEAR(tanggal) = '$year' AND id_karyawan IN (SELECT id_karyawan FROM users WHERE id_spv = '$id'), beltup, 0)) AS beltup"))
                ->first();
        }
                // dd($datkolspv);
            // $datkol = User::leftjoin('donatur', 'donatur.petugas', '=', 'users.name')
            //     ->select(\DB::raw("
            //     COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totkun,
            //     COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totup"))
            //     ->where('users.id_karyawan',$id)->first();
        // $honor = $kolek != null ? $kolek->honor : 0;
        $bontim = $kol != null ? $kol->Omtim * 1/100 : 0; 
        $bon = $kolek != null ? $kolek->Omset * 1/100 : 0;
        $rinbon= [
                    // ['bonus' => 'Omset',
                    //  'jumlah' => round($bon)],
                    ['bonus'=> 'Capaian Tim',
                     'jumlah' => round($bontim) + round($bon)],
                 ];
        $bonkol = $bontim + $bon;
        $potongan = [
                        ['nampot' => 'Potongan',
                         'jumlah' => $datkolspv->belkun * $tj->potongan],
                        ['nampot'=> 'Tutup 1x',
                         'jumlah' => ($datkolspv->beltup * $tj->potongan) + ($datkol->totup * $tj->potongan)],
                        ['nampot' => 'Belum Dikunjungi',
                         'jumlah' => $datkol->totkun * $tj->potongan],
                        // ['nampot'=> 'Tutup 1x',
                        //  'jumlah' => $datkol->totup * $tj->potongan],
                    ];
        // dd($bon, $bontim);
        $totpot = ($datkolspv->belkun * $tj->potongan) + ($datkolspv->beltup * $tj->potongan) + ($datkol->totup * $tj->potongan) + ($datkol->totkun * $tj->potongan);
        // ini bonus sales
        }elseif($kar->jabatan == $tj->sokotak){
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
                $poin = round($omst/$p->minpo);
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
                // $bonset = $inbons[$b] == 1 ? round($v->omset*($bons[$b]/100)) : floatval($bons[$b]);
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
    $tj = Tunjangan::where('id_com', Auth::user()->id_com)->first();
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
                     'nominal'=> $totpo],
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
                     'nominal'=> $totpo],
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
    
    // $datfin = [];
    // $datfin['jumlah'] = count($data);
    // $datfin['id_don'] = $id_don;
    // $datfin['data'] = $data;
    // $datfin['total'] = $total;
    // $datfin['prosdon'] = [
    //                         ['nampros'  => 'Donatur Closing',
    //                          'jumlah'   => $prosdon['closing']],
    //                         ['nampros'  => 'Donatur Open',
    //                          'jumlah'   => $prosdon['open']],
    //                         ['nampros'  => 'Donatur Cancel',
    //                          'jumlah'   => $prosdon['cancel']],
    //                         ['nampros'  => 'Total Prospek',
    //                          'jumlah'   => $prosdon['closing'] + $prosdon['open'] + $prosdon['cancel']],
    //                     ]; 
        $rinbon= [
                //   ['bonus' => 'Total Poin',
                //      'jumlah'=> $totpo],
                    ['bonus' => 'Total Honor Poin',
                     'jumlah'=> $tothonpo],
                    ['bonus' => 'Total Bonus Poin',
                     'jumlah'=> $totbonpo],
                    ['bonus' => 'Total Bonus Omset',
                     'jumlah'=> $totbonset],
                    ['bonus' => 'Bonus Omset Kolekting',
                     'jumlah'=> round($bon)],
                    ['bonus' => 'Total Bonus',
                     'jumlah'=> $tothonpo + $totbonpo + $totbonset + round($bon)],
                 ];
        $bonkol = $tothonpo + $totbonpo + $totbonset;
        $potongan = [
                        ['nampot' => 'Potongan',
                         'jumlah' => 0],
                    ];
        $totpot = 0;
        }
    }else{
        $honor = 0;
        $rinbon= [
                    ['bonus' => 'Omset',
                     'jumlah' => 0],
                 ];
                 
        $potongan = [
                        ['nampot' => 'Potongan',
                         'jumlah' => 0],
                    ];
        $totpot = 0;
    }
    // dd($honor);
    // $transport = $tj->potongan0;
    $gol = $kar->status_kerja == 'Magang' ? 'IA' : $kar->golongan;
    $datass = Karyawan::leftjoin('gapok', 'karyawan.masa_kerja', '=', 'gapok.th')->leftjoin('jabatan', 'karyawan.jabatan', '=', 'jabatan.id')
            ->leftjoin('daerah', 'karyawan.id_daerah', '=', 'daerah.id_daerah')
            ->leftjoin('presensi', 'karyawan.id_karyawan', '=', 'presensi.id_karyawan')
            ->leftjoin("set_terlambat",function($join) use ($id){
                        $join->on("presensi.keterlambatan",">=","set_terlambat.awal")
                             ->on("presensi.keterlambatan","<=","set_terlambat.akhir")
                             ->where('presensi.id_karyawan', $id);
                        })
            ->select(\DB::raw("karyawan.nama, gapok.$gol, jabatan.tj_fungsional, jabatan.tj_jabatan , daerah.tj_daerah, jabatan.tj_training, jabatan.kon_tj_plt, jabatan.tj_plt,
                            SUM(IF(presensi.status = 'Hadir' AND MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji') 
                            OR presensi.status = 'Terlambat' AND MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji') 
                            , presensi.jumlah,0)) AS jumlah,
                            SUM(IF(MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji'), set_terlambat.potongan, 0)) AS potongan"))
            ->groupBy('karyawan.nama','gapok.'.$gol, 'jabatan.tj_jabatan', 'jabatan.tj_training', 'daerah.tj_daerah', 'jabatan.tj_fungsional', 'jabatan.tj_training', 'jabatan.kon_tj_plt', 'jabatan.tj_plt')
            ->where('karyawan.id_karyawan',$id)->get();
    // dd($datass);
    $total = $tot_anak + $tot_pasangan + 1;
    // dd($potongbpjs);
    $jkk = Bpjs::where('nama_jenis', 'JKK')->first();
    $jkm = Bpjs::where('nama_jenis', 'JKM')->first();
    $jht = Bpjs::where('nama_jenis', 'JHT')->first();
    $jpn = Bpjs::where('nama_jenis', 'JPN')->first();
    $sht = Bpjs::where('nama_jenis', 'KESEHATAN')->first();
    
    $profil = Profile::where('id_com', Auth::user()->id_com)->first();
    
    $pjkk = $profil->jkk == 1 && $kar->jkk == 1 ? $jkk->perusahaan : 0;
    $pjkm = $profil->jkm == 1 && $kar->jkm == 1 ? $jkm->perusahaan : 0;
    $pjht = $profil->jht == 1 && $kar->jht == 1 ? $jht->perusahaan : 0;
    $pjpn = $profil->jpn == 1 && $kar->jpn == 1 ? $jpn->perusahaan : 0;
    $psht = $profil->kesehatan == 1 && $kar->kesehatan == 1 ? $sht->perusahaan : 0;
    
    $kjkk = $kar->jkk == 1 ? $jkk->karyawan : 0;
    $kjkm = $kar->jkm == 1 ? $jkm->karyawan : 0;
    $kjht = $kar->jht == 1 ? $jht->karyawan : 0;
    $kjpn = $kar->jpn == 1 ? $jpn->karyawan : 0;
    $ksht = $kar->kesehatan == 1 ? $sht->karyawan : 0;
    
    $potongbpjs = $tj->umr * (($kjkk + $kjkm + $kjht + $kjpn + $ksht)/100);
    
    $databpjs = [
        'jkk' => number_format($tj->umr * ($pjkk/100),0, ',' , '.'),
        'jkm' => number_format($tj->umr * ($pjkm/100),0, ',' , '.'),
        'jht' => number_format($tj->umr * ($pjht/100),0, ',' , '.'),
        'jpn' => number_format($tj->umr * ($pjpn/100),0, ',' , '.'),
        // 'sht' => number_format($tj->umr * ($psht/100),0, ',' , '.'),
        // 'total' => number_format($tj->umr * (($jkk->perusahaan + $jkm->perusahaan + $jht->perusahaan + $jpn->perusahaan)/100),0, ',' , '.'),
        ];
        
    // return($databpjs);
    
    $rekdis = Rekdis::selectRaw("id_karyawan, COUNT(IF(stat = 1, tanggal, NULL)) AS lap, COUNT(IF(stat = 2, tanggal, NULL)) AS pul, COUNT(IF(stat = 3, tanggal, NULL)) AS lappul")
            ->where('id_karyawan', $kar->id_karyawan)
            ->groupBy('id_karyawan')->first();
            
    if($rekdis == null){        
    $rekdis = [
                'id_karyawan'   => $kar->id_karyawan,
                'lap'           => 0,
                'pul'           => 0,
                'lappul'         => 0
            ];
    }
    
    $potpul = ($rekdis['lap'] * $tj->pul) + ($rekdis['pul'] * $tj->pul);
    $potlappul = $rekdis['lappul'] * $tj->lappul;
    
    $data = [];
    foreach($datass as $x => $v){
        
    $potlaptab = [
                ['nampot' => 'Keterlambatan', 'jumlah'  => round($v->potongan)],
                ['nampot' => 'Tidak Laporan atau Presensi Pulang', 'jumlah'  => $potpul],
                ['nampot' => 'Tidak Laporan dan Presensi Pulang', 'jumlah'  => $potlappul]
            ];
    
    // dd($rekdis, $potlaptab);
    
        // if($id == 8911041807102){
        //     $tjjab = ($kar->plt == 1 ? $tj->tj_plt/100 * $v->tj_jabatan : $v->tj_jabatan) + $v->tj_fungsional + 400000;
        // }else{
            $tjjab = ($kar->plt == 1 ? $v->tj_jabatan * ($v->tj_plt/100) : $v->tj_jabatan) + $v->tj_fungsional;
        // }
        
        // if($kar->plt == 1){
        //     $tjjab = ($v->kon_tj_plt == 'p' ? $v->tj_plt/100 * $v->tj_jabatan : $v->tj_plt) + $v->tj_fungsional;
        //     $tjjabtrain = $v->tj_training == 1 ? ($v->kon_tj_plt == 'p' ? $v->tj_plt/100 * $v->tj_jabatan : $v->tj_plt) : 0;
        // }else{
        //     $tjjab = $v->tj_jabatan + $v->tj_fungsional;
        //     $tjjabtrain = $v->tj_training == 1 ? $v->tj_jabatan : 0;
        // }
        
            
        $tjjabtrain = $v->tj_training == 1 ? $v->tj_jabatan : 0;
        
        if($kar->status_kerja == 'Contract'){
            $data['data'][] = [
                'kondisi' => $kar->kondisi,
                'nama' => $v->nama,
                'no_rek' => $kar->no_rek,
                'nik' => $kar->nik,
                'status_kerja' => $kar->status_kerja,
                'masa_kerja' => $kar->masa_kerja,
                'golongan' => $kar->golongan,
                'id_jabatan' => $kar->jabatan,
                'id_kantor' => $kar->id_kantor,
                // 'id_daerah' => $kar->id_daerah,
                'gapok' => number_format($v->$gol,0, ',' , '.'),
                'tj_jabatan' => number_format($tjjab,0, ',' , '.'),
                'tj_daerah' => number_format($v->tj_daerah,0, ',' , '.'),
                'tj_p_daerah' => number_format($kar->jab_daerah == 1 ? $drh->tj_jab_daerah : 0,0, ',' , '.'),
                'jml_hari' => $v->jumlah,
                'tj_anak' => number_format($kar->tj_pas == 1 ? 0 : $tot_anak * ($tj->tj_anak/100 * $v->$gol),0, ',' , '.'),
                'tj_pasangan' => number_format($kar->tj_pas == 1 ? 0 : $tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol),0, ',' , '.'),
                'tj_beras' => number_format($kar->tj_pas == 1 ? 0 : $tj->tj_beras*$tj->jml_beras*$total,0, ',' , '.'),
                'transport' => number_format($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor :  $v->jumlah * $tj->tj_transport,0, ',' , '.'),
                'total' => number_format($v->$gol + $tjjab + $v->tj_daerah + ($kar->tj_pas == 1 ? 0 : $tot_anak * ($tj->tj_anak/100 * $v->$gol)) + 
                            ($kar->tj_pas == 1 ? 0 : $tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol)) + ($kar->tj_pas == 1 ? 0 : $tj->tj_beras*$tj->jml_beras*$total) + 
                            ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport),0, ',' , '.'),
                'thp' => round(($v->$gol + $tjjab + $v->tj_daerah + ($kar->tj_pas == 1 ? 0 : $tot_anak * ($tj->tj_anak/100 * $v->$gol)) + 
                            ($kar->tj_pas == 1 ? 0 : $tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol)) + ($kar->tj_pas == 1 ? 0 : $tj->tj_beras*$tj->jml_beras*$total) + 
                            ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport))),
                // 'thp' => number_format(($v->$gol + $tjjab + $v->tj_daerah + ($tot_anak * ($tj->tj_anak/100 * $v->$gol)) + 
                //             ($tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol)) + ($tj->tj_beras*$tj->jml_beras*$total) + 
                //             ($kar->jabatan == $tj->kolektor ? $honor : $v->jumlah * $tj->tj_transport)) - $potongbpjs ,0, ',' , '.'),
                'tgl_gaji' => $tgl_gaji,
                'bpjs' => round($potongbpjs),
                'tot_tj_bpjs' => number_format($tj->umr * (($pjkk + $pjkm + $pjht + $pjpn)/100),0, ',' , '.'),
                'bpjs_sehat' => number_format($tj->umr * ($psht/100),0, ',' , '.'),
                'tj_bpjs' => $databpjs,
                'bonus' => round($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->spv_kol | $kar->jabatan == $tj->sokotak ? $bonkol : 0),
                'rinbon' => $rinbon,
                'potlaptab' => $potlaptab,
                'potab' => round($v->potongan),
                'potdis' => '0',
                'totpot' => $totpot,
                'potongan' => $potongan,
                'bokin' => '0'
            ];
        }else if($kar->status_kerja == 'Training'){
            // $gapok = $kar->id_karyawan == '9812062405101' ? $v->$gol * (50/100) : $v->$gol * (80/100);
            $gapok = $v->$gol * (80/100);
            $data['data'][] = [
                'kondisi' => $kar->kondisi,
                'nama' => $v->nama,
                'no_rek' => $kar->no_rek,
                'nik' => $kar->nik,
                'status_kerja' => $kar->status_kerja,
                'masa_kerja' => $kar->masa_kerja,
                'golongan' => $kar->golongan,
                'id_jabatan' => $kar->jabatan,
                'id_kantor' => $kar->id_kantor,
                // 'id_daerah' => $kar->id_daerah,
                'gapok' => number_format($gapok,0, ',' , '.'),
                'tj_jabatan' => number_format($tjjabtrain,0, ',' , '.'),
                'tj_daerah' => '0',
                'tj_p_daerah' => '0',
                'jml_hari' => $v->jumlah,
                'tj_anak' => '0',
                'tj_pasangan' => '0',
                'tj_beras' => '0',
                'transport' => number_format($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport,0, ',' , '.'),
                'total' => number_format(($gapok) + $tjjabtrain +
                            ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport),0, ',' , '.'),
                'thp' => round(($gapok) + $tjjabtrain +
                            ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport)),
                'tgl_gaji' => $tgl_gaji,
                'bpjs' => 0,
                'tot_tj_bpjs' => '0',
                'bpjs_sehat' => '0',
                'tj_bpjs' => ['jkk' => '0', 'jkm' => '0', 'jht' => '0', 'jpn' => '0'],
                'bonus' => round($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->spv_kol | $kar->jabatan == $tj->sokotak ? $bonkol : 0),
                'rinbon' => $rinbon,
                'potab' => round($v->potongan),
                'potlaptab' => $potlaptab,
                'potdis' => '0',
                'totpot' => $totpot,
                'potongan' => $potongan,
                'bokin' => '0'
            ];
        }else{
            $tjmag = $tj->tj_magang_mhs;
            $data['data'][] = [
                'kondisi' => $kar->kondisi,
                'nama' => $v->nama,
                'no_rek' => $kar->no_rek,
                'nik' => $kar->nik,
                'status_kerja' => $kar->status_kerja,
                'masa_kerja' => $kar->masa_kerja,
                'golongan' => $kar->golongan,
                'id_jabatan' => $kar->jabatan,
                'id_kantor' => $kar->id_kantor,
                // 'id_daerah' => $kar->id_daerah,
                'gapok' => 0,
                'tj_jabatan' => 0,
                'tj_daerah' => '0',
                'tj_p_daerah' => '0',
                'jml_hari' => $v->jumlah,
                'tj_anak' => '0',
                'tj_pasangan' => '0',
                'tj_beras' => '0',
                'transport' => number_format($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tjmag,0, ',' , '.'),
                'total' => number_format(
                            ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tjmag),0, ',' , '.'),
                'thp' => round(
                            ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tjmag)),
                'tgl_gaji' => $tgl_gaji,
                'bpjs' => 0,
                'tot_tj_bpjs' => '0',
                'bpjs_sehat' => '0',
                'tj_bpjs' => ['jkk' => '0', 'jkm' => '0', 'jht' => '0', 'jpn' => '0'],
                'bonus' => round($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->spv_kol | $kar->jabatan == $tj->sokotak ? $bonkol : 0),
                'rinbon' => $rinbon,
                'potab' => 0,
                'potdis' => '0',
                'totpot' => 0,
                'potongan' => $potongan,
                'bokin' => '0'
            ];
        }
    }
    return($data);
}


public function accgaji($id, Request $request)
{

        $data = new Gaji;
        $data->id_karyawan = $id;
        $data->nama = $request->nama;
        $data->nik = $request->nik;
        $data->no_rek = $request->no_rek;
        $data->status_kerja = $request->status_kerja;
        $data->masa_kerja = $request->masa_kerja;
        $data->golongan = $request->golongan;
        $data->id_jabatan = $request->id_jabatan;
        $data->id_kantor = $request->id_kantor;
        $data->gapok = preg_replace("/[^0-9]/", "", $request->gapok);
        $data->tj_jabatan = preg_replace("/[^0-9]/", "", $request->tj_jabatan);
        $data->tj_daerah = preg_replace("/[^0-9]/", "", $request->tj_daerah);
        $data->tj_p_daerah = preg_replace("/[^0-9]/", "", $request->tj_p_daerah);
        $data->tj_anak = preg_replace("/[^0-9]/", "", $request->tj_anak);
        $data->tj_pasangan = preg_replace("/[^0-9]/", "", $request->tj_pasangan);
        $data->tj_beras = preg_replace("/[^0-9]/", "", $request->tj_beras);
        $data->transport = preg_replace("/[^0-9]/", "", $request->transport);
        $data->jml_hari = $request->jml_hari;
        $data->total = preg_replace("/[^0-9]/", "", $request->total);
        $data->thp = preg_replace("/[^0-9]/", "", $request->thp);
        $data->bpjs = preg_replace("/[^0-9]/", "", $request->bpjs);
        $data->potab = preg_replace("/[^0-9]/", "", $request->potab);
        $data->potongan = preg_replace("/[^0-9]/", "", $request->potongan);
        $data->ketenagakerjaan = preg_replace("/[^0-9]/", "", $request->ketenagakerjaan);
        $data->kesehatan = preg_replace("/[^0-9]/", "", $request->kesehatan);
        $data->bonus = preg_replace("/[^0-9]/", "", $request->bonus);
        $data->bokin = preg_replace("/[^0-9]/", "", $request->bokin);
        $data->potdis = preg_replace("/[^0-9]/", "", $request->potdis);
        $data->potdll = preg_replace("/[^0-9]/", "", $request->potdll);
        $data->created_at = $request->tgl_gaji;
        if($id == Auth::user()->id_karyawan && Auth::user()->presensi == 'kacab'){
            $data->status = 'req';
        }else{
            $data->status = $request->status;
        }
        $data->created = date('Y-m-d H:i:s');
        $data->user_insert = Auth::user()->id;
        
        $data->save();
    // $datkar = Karyawan::where('id_karyawan',$id)->where('id_com', Auth::user()->id_com)->first();
    // $datkar->tgl_gaji = date('Y-m-d');
    // $datkar->update();
    $datkar = Karyawan::where('id_karyawan',$id)->where('id_com', Auth::user()->id_com)->update(['tgl_gaji' => date('Y-m-d')]);
        \LogActivity::addToLog(Auth::user()->name.' Menyetujui Gaji '.$request->nama);
    return response()->json(
        [
            "status"=>"sukses",
            // "data"=>$data
        ]
    );
}

public function rejectgaji($id, Request $request)
{
    $last   = Gaji::where('id_karyawan', $request->id_kar)->orderBy('id_gaji', 'DESC')->first();
    $gaji   = Gaji::findOrFail($id);
    
    $namakar = $gaji->nama;
    $tg_gaji = date('Y-m-01', strtotime($gaji->created_at));
    
    if($id == $last->id_gaji){
        $datkar = Karyawan::where('id_karyawan', $request->id_kar)->where('id_com', Auth::user()->id_com)->update(['tgl_gaji' => $tg_gaji]);
        
        $gaji->delete();
        Rekapkar::where('id_gaji', $id)->delete();
        \LogActivity::addToLog(Auth::user()->name.' Membatalkan Data Gaji'.$namakar.' Bulan '.$tg_gaji);
      
        return response()->json([
            "status"=>"sukses"
            ]);
    }else{
        return response()->json([
            "status"=>"gagal"
            ]);
    }
}

public function accreqgaji($id, Request $request)
{
    Gaji::findOrFail($id)->update(['status' => $request->status, 'user_update' => Auth::user()->id]);
    if($request->status == 'trf'){
    $kar = Karyawan::where('id_karyawan', $request->id_kar)->where('id_com', Auth::user()->id_com)->first();
        $data = new RekapKar;
        $data->id_gaji = $id;
        $data->nama = $kar->nama;
        $data->pendidikan = $kar->pendidikan;
        $data->status_nikah = $kar->status_nikah;
        $data->id_gol = $kar->id_gol;
        $data->masa_kerja = $kar->masa_kerja;
        $data->golongan = $kar->golongan;
        $data->id_pasangan = $kar->id_pasangan;
        $data->nm_pasangan = $kar->nm_pasangan;
        $data->tgl_lahir = $kar->tgl_lahir;
        $data->tgl_nikah = $kar->tgl_nikah;
        $data->nm_anak = $kar->nm_anak;
        $data->tgl_lahir_anak = $kar->tgl_lahir_anak;
        $data->status_anak = $kar->status_anak;
        $data->tgl_kerja = $kar->tgl_kerja;
        $data->id_jabatan = $kar->id_jabatan;
        $data->id_karyawan = $kar->id_karyawan;
        $data->id_kantor = $kar->id_kantor;
        $data->tgl_gaji = $kar->tgl_gaji;
        $data->id_daerah = $kar->id_daerah;
        $data->tgl_mk = $kar->tgl_mk;
        $data->tgl_gol = $kar->tgl_gol;
        $data->status_kerja = $kar->status_kerja;
        $data->tj_pas = $kar->tj_pas;
        $data->jab_daerah = $kar->jab_daerah;
        $data->plt = $kar->plt;
        $data->user_insert = Auth::user()->id;
        $data->save();
    }
    
    // \LogActivity::addToLog(Auth::user()->name.' Menghapus Data Gaji');
    return response()->json([
        "status"=>"sukses"
        ]);
}

public function gajikar ($m, $Y)
{
    $gaji = Gaji::join('tambahan', 'gaji.id_kantor', '=', 'tambahan.id')
            ->select(\DB::raw("gaji.id_kantor, tambahan.unit, DATE_FORMAT(gaji.created_at, '%Y-%m') AS tgl, SUM(IF(MONTH(gaji.created_at) = '$m' AND YEAR(gaji.created_at) = '$Y', gaji.thp, 0)) AS thp
                "))
            ->whereMonth('gaji.created_at', $m)->whereYear('gaji.created_at', $Y)
            ->groupBy('id_kantor','unit', 'tgl')->get();
    $data = [];
    if(count($gaji) > 0){
        foreach($gaji as $x => $v){
            $data['data'][] = [
                'id_kantor' => $v->id_kantor,
                'unit' => $v->unit,
                'thp' => $v->thp,
                'tgl' => $v->tgl.'-01'
            ];
        }
    }else{
      $data['data'] = []; 
    }
    return($data);
}

// Ganti rwygajiid
public function rwy_gajiid(Request $req){
    $id_kar = $req->id_karyawan;
    $tahun  = $req->tahun > 0 ? $req->tahun : date('Y');
    $gaji   = Gaji::where('id_karyawan',$id_kar)->whereYear('created_at', $tahun)->orderBy('created','desc')->get();
    return fractal($gaji, new GajiKarTransformer())->toArray();
}

// Ganti getgajiid
public function get_gajiid(Request $req){
    $id     = $req->id_gaji;
    $gaji   = Gaji::where('id_gaji',$id)->get();
    return fractal($gaji, new GajiKarTransformer())->toArray();
}

// Ganti listreqgaji, listaccgaji, listreqgajicab, listaccgajicab
public function list_gaji(Request $req){
    $day    = date('d');
    if($day >= 25){
        $tgs = date('Y-04-01');
    }else{
        $tgs = date('Y-m-01', strtotime('-1 month', strtotime(date('Y-m-d'))));
    }
    // return($tgs);
    $id_kan = Auth::user()->id_kantor;
    $id_kcp = Kantor::where('kantor_induk', $id_kan)->pluck('id');
    
    $gaji   = Gaji::join('jabatan', 'gaji.id_jabatan', '=', 'jabatan.id')
            ->select('gaji.*', 'jabatan.jabatan')
            ->where('gaji.status', $req->status)
            ->whereDate('gaji.created_at', $tgs)
            ->where(function($q) use ($id_kan, $id_kcp) {
                        if(Auth::user()->presensi == 'kacab' | Auth::user()->presensi == 'keuangan cabang'){
                            $q->where('gaji.id_kantor', $id_kan)->orWhereIn('gaji.id_kantor', $id_kcp);
                        }else if(Auth::user()->presensi == 'karyawan'){
                            $q->where('gaji.id_kantor', 0);
                        }
                    })
            ->get();
            
    return fractal($gaji, new GajiKarTransformer())->toArray();
}

// Ganti listgajikar, rwygajikar
public function list_gajikar(Request $req){
    $id     = $req->id;
    $m      = $req->bln > 0 ? $req->bln : date('m');
    $Y      = $req->thn > 0 ? $req->thn : date('Y');
    $jen    = $req->jenis;
    
    $gaji   = Gaji::join('jabatan', 'gaji.id_jabatan', '=', 'jabatan.id')
            ->select('gaji.*', 'jabatan.jabatan')
            ->where(function($q) use ($id, $jen) {
                if($jen == 'kan'){
                    $q->where('gaji.id_kantor', $id);
                }else{
                    $q->where('gaji.id_karyawan', $id);
                }
            })
            ->whereMonth('gaji.created_at', $m)->whereYear('gaji.created_at', $Y)
            ->get();
    return fractal($gaji, new GajiKarTransformer())->toArray();
}

public function rwygajiid ($id, $year)
{
    $gaji = Gaji::where('id_karyawan',$id)->whereYear('created_at',$year)->orderBy('created','desc')->get();
    return fractal($gaji, new GajiNewTransformer())->toArray();
}

public function getgajiid ($id)
{
    $gaji = Gaji::where('id_gaji',$id)->get();
    return fractal($gaji, new GajiNewTransformer())->toArray();
}

public function listreqgaji ()
{
    $day = date('d');
    if($day >= 25){
        $tgs = date('Y-m-01');
    }else{
        $tgs = date('Y-m-01', strtotime('-1 month', strtotime(date('Y-m-d'))));
    }
    $gaji = Gaji::join('jabatan', 'gaji.id_jabatan', '=', 'jabatan.id')
            ->select('gaji.*', 'jabatan.jabatan')
            ->where('gaji.status', 'req')
            ->whereDate('gaji.created_at', $tgs)
            // ->whereMonth('gaji.created', date('m'))->whereYear('gaji.created', date('Y'))
            // ->whereMonth('gaji.created_at', $m)->whereYear('gaji.created_at', $Y)
            ->get();
    return fractal($gaji, new GajiNewTransformer())->toArray();
}
public function listaccgaji ()
{
    $day = date('d');
    if($day >= 25){
        $tgs = date('Y-m-01');
    }else{
        $tgs = date('Y-m-01', strtotime('-1 month', strtotime(date('Y-m-d'))));
    }
    
    $gaji = Gaji::join('jabatan', 'gaji.id_jabatan', '=', 'jabatan.id')
            ->select('gaji.*', 'jabatan.jabatan')
            ->where('gaji.status', '!=', 'req')
            ->whereDate('gaji.created_at', $tgs)
            // ->whereMonth('gaji.created', date('m'))->whereYear('gaji.created', date('Y'))
            // ->whereMonth('gaji.created_at', $m)->whereYear('gaji.created_at', $Y)
            ->get();
    return fractal($gaji, new GajiNewTransformer())->toArray();
}

public function listreqgajicab ($id)
{
    $day = date('d');
    if($day >= 25){
        $tgs = date('Y-m-01');
    }else{
        $tgs = date('Y-m-01', strtotime('-1 month', strtotime(date('Y-m-d'))));
    }
    $gaji = Gaji::join('jabatan', 'gaji.id_jabatan', '=', 'jabatan.id')
            ->select('gaji.*', 'jabatan.jabatan')
            ->where('gaji.status', 'req')
            ->where('gaji.id_kantor', $id)
            ->whereDate('gaji.created_at', $tgs)
            // ->whereMonth('gaji.created', date('m'))->whereYear('gaji.created', date('Y'))
            // ->whereMonth('gaji.created_at', $m)->whereYear('gaji.created_at', $Y)
            ->get();
    return fractal($gaji, new GajiNewTransformer())->toArray();
}
public function listaccgajicab ($id)
{
    $day = date('d');
    if($day >= 25){
        $tgs = date('Y-m-01');
    }else{
        $tgs = date('Y-m-01', strtotime('-1 month', strtotime(date('Y-m-d'))));
    }
    $gaji = Gaji::join('jabatan', 'gaji.id_jabatan', '=', 'jabatan.id')
            ->select('gaji.*', 'jabatan.jabatan')
            ->where('gaji.status', '!=', 'req')
            ->where('gaji.id_kantor', $id)
            ->whereDate('gaji.created_at', $tgs)
            // ->whereMonth('gaji.created', date('m'))->whereYear('gaji.created', date('Y'))
            // ->whereMonth('gaji.created_at', $m)->whereYear('gaji.created_at', $Y)
            ->get();
    return fractal($gaji, new GajiNewTransformer())->toArray();
}

public function listgajikar ($id, $m, $Y)
{
    $gaji = Gaji::join('jabatan', 'gaji.id_jabatan', '=', 'jabatan.id')
            ->select('gaji.*', 'jabatan.jabatan')
            ->where('gaji.id_kantor', $id)->whereMonth('gaji.created_at', $m)->whereYear('gaji.created_at', $Y)
            ->get();
    return fractal($gaji, new GajiNewTransformer())->toArray();
}

public function rwygajikar ($id, $m, $Y)
{
    $gaji = Gaji::join('jabatan', 'gaji.id_jabatan', '=', 'jabatan.id')
            ->select('gaji.*', 'jabatan.jabatan')
            ->where('gaji.id_karyawan', $id)->whereMonth('gaji.created_at', $m)->whereYear('gaji.created_at', $Y)
            ->get();
    return fractal($gaji, new GajiNewTransformer())->toArray();
}

public function getrekapkar ($id)
{
    $gaji = RekapKar::where('id_gaji', $id)->get();
    $data = [];
    if(count($gaji) > 0){
    foreach($gaji as $x => $v){
        $nm_pas = $v->nm_pasangan != null ? unserialize($v->nm_pasangan) : [];
        $tgl_lahir = $v->tgl_lahir != null ? unserialize($v->tgl_lahir) : [];
        $tgl_nikah = $v->tgl_nikah != null ? unserialize($v->tgl_nikah) : [];
        $anak = $v->nm_anak != null ? unserialize($v->nm_anak) : []; 
        $tgl_anak = $v->tgl_lahir_anak != null ? unserialize($v->tgl_lahir_anak) : [];
        $sts_anak = $v->status_anak != null ? unserialize($v->status_anak) : [];
        
        $data['data'][] = [
            'id_gaji' => $id,
            'nama' => $v->nama,
            'pendidikan' => $v->pendidikan,
            'status_nikah' => $v->status_nikah,
            'id_gol' => $v->id_gol,
            'masa_kerja' => $v->masa_kerja,
            'golongan' => $v->golongan,
            'id_pasangan' => $v->id_pasangan,
            'nm_pasangan' => $nm_pas,
            'tgl_lahir' => $tgl_lahir,
            'tgl_nikah' => $tgl_nikah,
            'nm_anak' => $anak,
            'tgl_lahir_anak' => $tgl_anak,
            'status_anak' => $sts_anak,
            'tgl_kerja' => $v->tgl_kerja,
            'id_jabatan' => $v->id_jabatan,
            'id_karyawan' => $v->id_karyawan,
            'id_kantor' => $v->id_kantor,
            'tgl_gaji' => $v->tgl_gaji,
            'id_daerah' => $v->id_daerah,
            'tgl_mk' => $v->tgl_mk,
            'tgl_gol' => $v->tgl_gol,
            'status_kerja' => $v->status_kerja,
            'tj_pas' => $v->tj_pas,
            'jab_daerah' => $v->jab_daerah,
            'plt' => $v->plt
        ];
    }
        }else{
          $data['data'] = []; 
        }
    return($data);
}

public function tahungaji ()
{
    $datass = Gaji::select(\DB::raw("DISTINCT YEAR(created_at) AS tahun"))->get();
    foreach($datass as $x => $v){
        $data['data'][] = [
            'tahun' => $v->tahun,
        ];
    }
    return($data);
}

public function getgolongan(){
    $data = Golongan::all();
    return fractal($data, new GolonganTransformer())->toArray();
}

public function getkeluarga($id){
    $datkar = Karyawan::where('id_karyawan',$id)->first();
    $nm_pas = $datkar->nm_pasangan != null ? unserialize($datkar->nm_pasangan) : [];
    $tgl_lahir = $datkar->tgl_lahir != null ? unserialize($datkar->tgl_lahir) : [];
    $tgl_nikah = $datkar->tgl_nikah != null ? unserialize($datkar->tgl_nikah) : [];
    $anak = $datkar->nm_anak != null ? unserialize($datkar->nm_anak) : []; 
    $tgl_anak = $datkar->tgl_lahir_anak != null ? unserialize($datkar->tgl_lahir_anak) : [];
    $sts_anak = $datkar->status_anak != null ? unserialize($datkar->status_anak) : [];
        
    // $nm_pas = unserialize($datkar->nm_pasangan);
    // $tgl_lahir = unserialize($datkar->tgl_lahir);
    // $tgl_nikah = unserialize($datkar->tgl_nikah);
    // $anak = unserialize($datkar->nm_anak); 
    // $tgl_anak = unserialize($datkar->tgl_lahir_anak);
    // $sts_anak = unserialize($datkar->status_anak);
    
            $data['data'][] = [
                'id_karyawan' => $datkar->id_karyawan,
                'nama' => $datkar->nama,
                'tj_pas' => $datkar->tj_pas,
                'status_nikah' => $datkar->status_nikah,
                'scan_kk' => $datkar->scan_kk,
                'no_kk' => $datkar->no_kk,
                'nm_pasangan' => $nm_pas,
                'tgl_lahir' => $tgl_lahir,
                'tgl_nikah' => $tgl_nikah,
                'nm_anak' => $anak,
                'tgl_anak' => $tgl_anak,
                'status_anak' => $sts_anak,
                'jml_pas' => $nm_pas != null ? count($nm_pas) : 0,
                'jml_anak' => $anak != null ? count($anak) : 0
            ];
            
    return($data);
}

public function upkenaikan(Request $request,$id){
    // dd($request->id_gol);
    $tanggal = date('Y-m-d', strtotime($request->tgl_sk));
    $gol = Golongan::where('id_gol', $request->id_gol)->first();
    $kar = Karyawan::where('id_karyawan', $id)->first();
    $date = date('Y-m-d');
    
    $con = Kenaikan::where('id_karyawan', $id)->whereDate('created_at', $date)->first();
    // dd($con);
    $tgl_mk = $request->masa != $kar->masa_kerja ? $tanggal : $kar->tgl_mk;
    $tgl_gol = $request->id_gol != $kar->id_gol ? $tanggal : $kar->tgl_gol;
    
    if($request->upload_sk != ''){
        $file_sk = $request->file('upload_sk')->getClientOriginalName();
    }else{
        $file_sk = null;
    }
    
    // // dd($tanggal);
    Karyawan::where('id_karyawan', $id)->update([
      'masa_kerja' => $request->masa_kerja,
      'id_gol' => $request->id_gol,
      'golongan' => $gol->golongan,
      'tgl_mk' => $tgl_mk,
      'tgl_gol' => $tgl_gol,
      'file_sk' => $file_sk,
      'status_kerja' => $request->status_kerja,
      'no_rek' => $request->no_rek,
      'user_update' => Auth::user()->id
    ]);
    
   
    if($con){
        $data = Kenaikan::findOrFail($con->id_naik);
        $data->nama = $kar->nama;
        $data->masa_kerja = $request->masa_kerja;
        $data->golongan = $gol->golongan;
        $data->tgl_mk = $tgl_mk;
        $data->tgl_gol = $tgl_gol;
        $data->status_kerja = $request->status_kerja;
        $data->user_insert = Auth::user()->id;
        
        if($request->upload_sk != ''){
        if($request->hasFile('upload_sk')){
            $image = $request->file('upload_sk');
    
            if($image->isValid()){
                $image_name = $image->getClientOriginalName();
                $upload_path = 'fileSK';
                $image->move($upload_path, $image_name);
                $data->file_sk = $image_name;
            }
        }
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
        
        if($request->upload_sk != ''){
        if($request->hasFile('upload_sk')){
            $image = $request->file('upload_sk');
    
            if($image->isValid()){
                $image_name = $image->getClientOriginalName();
                $upload_path = 'fileSK';
                $image->move($upload_path, $image_name);
                $data->file_sk = $image_name;
            }
        }
        }
        
        $data->save();
    }
    \LogActivity::addToLog(Auth::user()->name.' Mengupdate Kenaikan Pangkat '.$request->nama);
    return response()->json(
        [
            "status"=>"sukses",
            "data"=>$data
        ]
    );
}

public function upkeluarga(Request $request,$id){
    
    $date = date('Y-m-d');
    $con = RekapKeluarga::where('id_karyawan', $id)->whereDate('created_at', $date)->first();
    $peg = Karyawan::where('id_karyawan', $id)->first();
    
    $id_pas = $request->id_pasangan != '%5B%5D' ?  json_decode (urldecode($request->id_pasangan)) : [];
        $id_pasangan = $id_pas != [] ? serialize($id_pas) : null;
        
    $nm_pas = $request->nm_pasangan != '%5B%5D' ?  json_decode (urldecode($request->nm_pasangan)) : [];
        $nm_pasangan = $nm_pas != [] ? serialize($nm_pas) : null;
    $tgl_pas = $request->tgl_lahir != '%5B%5D' ?  json_decode (urldecode($request->tgl_lahir)) : [];
        $tgl_lahir = $tgl_pas != [] ? serialize($tgl_pas) : null;
    $tgl_nkh = $request->tgl_nikah != '%5B%5D' ?  json_decode (urldecode($request->tgl_nikah)) : [];
        $tgl_nikah = $tgl_nkh != [] ? serialize($tgl_nkh) : null;
    
    $nm_ank = $request->nm_anak != '%5B%5D' ? json_decode (urldecode($request->nm_anak)) : '';
        $nm_anak = $nm_ank != '' ? serialize($nm_ank) : null;
    $tgl_ank = $request->tgl_anak != '%5B%5D' ? json_decode (urldecode($request->tgl_anak)) : '';
        $tgl_lahir_anak = $tgl_ank != '' ? serialize($tgl_ank) : null;
    $stat_ank = $request->status_anak != '%5B%5D' ? json_decode (urldecode($request->status_anak)) : '';
        $status_anak = $stat_ank != '' ? serialize($stat_ank) : null;
    
    
    $data = Karyawan::findOrFail($peg->id_karyawans);
    $data->status_nikah = $request->status_nikah;
    $data->no_kk = $request->no_kk;
    $data->tj_pas = $request->tj_pas;
    
    if($request->scan_kk != ''){
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
    }
    
    
    $data->id_pasangan = $id_pasangan;
    $data->nm_pasangan = $nm_pasangan;
    $data->tgl_lahir = $tgl_lahir;
    $data->tgl_nikah = $tgl_nikah;
    $data->nm_anak = $nm_anak;
    $data->tgl_lahir_anak = $tgl_lahir_anak;
    $data->status_anak = $status_anak;
    $data->user_update = Auth::user()->id;
    
    $data->update();
    
    if($request->tj_pas == 1){
        if($request->scan_kk != ''){
            $scan_kk = $request->file('scan_kk')->getClientOriginalName();
            // $data->scan_kk = $image->getClientOriginalName();
        }else{
            $scan_kk = null;
        }
        Karyawan::whereIn('id_karyawan', $id_pas)->update([
            'id_pasangan' => serialize(array($peg->id_karyawan)),
            'status_nikah' => 'Menikah',
            'no_kk' => $request->no_kk,
            'scan_kk' => $scan_kk,
            'nm_pasangan' => serialize(array($peg->nama)),
            'tgl_lahir' => serialize(array($peg->ttl)),
            'tgl_nikah' => $tgl_nikah,
            'nm_anak' => $nm_anak,
            'tgl_lahir_anak' => $tgl_lahir_anak,
            'status_anak' => $status_anak,
            'user_update' => Auth::user()->id
        ]);
    }
    
    if($con){
        $data = RekapKeluarga::findOrFail($con->id_rekkel);
        $data->id_karyawan = $id;
        $data->nama = $peg->nama;
        $data->status_nikah = $request->status_nikah;
        $data->no_kk = $request->no_kk;
        
        if($request->scan_kk != ''){
        if($request->hasFile('scan_kk')){
            $image = $request->file('scan_kk');
            $data->scan_kk = $image->getClientOriginalName();
        }
        }
        
        $data->id_pasangan = $id_pasangan;
        $data->nm_pasangan = $nm_pasangan;
        $data->tgl_lahir = $tgl_lahir;
        $data->tgl_nikah = $tgl_nikah;
        $data->nm_anak = $nm_anak;
        $data->tgl_lahir_anak = $tgl_lahir_anak;
        $data->status_anak = $status_anak;
        $data->user_insert = Auth::user()->id;
        
        $data->update();
        
    } else{
        
        
        $data = new RekapKeluarga;
        $data->id_karyawan = $id;
        $data->nama = $peg->nama;
        $data->status_nikah = $request->status_nikah;
        $data->no_kk = $request->no_kk;
        
        if($request->scan_kk != ''){
        if($request->hasFile('scan_kk')){
            $image = $request->file('scan_kk');
            $data->scan_kk = $image->getClientOriginalName();
        }
        }
        
        $data->id_pasangan = $id_pasangan;
        $data->nm_pasangan = $nm_pasangan;
        $data->tgl_lahir = $tgl_lahir;
        $data->tgl_nikah = $tgl_nikah;
        $data->nm_anak = $nm_anak;
        $data->tgl_lahir_anak = $tgl_lahir_anak;
        $data->status_anak = $status_anak;
        $data->user_insert = Auth::user()->id;
        
        $data->save();
    }
    \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Keluarga '.$peg->nama);
    return response()->json(
        [
            "status"=>"sukses",
            "data"=>$data
        ]
    );
}

public function upjabatan(Request $request,$id){
    $tj = Tunjangan::where('id_com', Auth::user()->id_com)->first(); 
    $tanggal = date('Y-m-d', strtotime($request->tgl_jab));
    $date = date('Y-m-d');
    $con = RekapJabatan::where('id_karyawan', $id)->whereDate('created_at', $date)->first();
    $jab = Jabatan::where('id', $request->id_jabatan)->first();
    $kar = Karyawan::where('id_karyawan', $id)->first();
    Karyawan::where('id_karyawan', $id)->update([
      'jabatan' => $request->id_jabatan,
      'id_spv' => $request->id_jabatan == $tj->kolektor | $request->id_jabatan == $tj->so ? $request->id_spv : NULL,
      'plt' => $request->plt,
      'jab_daerah' => $request->jab_daerah,
      'pr_jabatan' => $jab->pr_jabatan,
      'user_update' => Auth::user()->id
    ]);
    
    if($con){
        $data = RekapJabatan::findOrFail($con->id_rekjab);
        $data->id_karyawan = $id;
        $data->nama = $kar->nama;
        $data->id_jabatan = $request->id_jabatan;
        $data->tgl_jab = $tanggal;
        $data->user_insert = Auth::user()->id;
        
        if($request->upload_file != ''){
        if($request->hasFile('upload_file')){
            $image = $request->file('upload_file');
    
            if($image->isValid()){
                $image_name = $image->getClientOriginalName();
                $upload_path = 'fileSK';
                $image->move($upload_path, $image_name);
                $data->file = $image_name;
            }
        }
        }
        
        $data->update();
        
    } else{
        $data = new RekapJabatan;
        $data->id_karyawan = $id;
        $data->nama = $kar->nama;
        $data->id_jabatan = $request->id_jabatan;
        $data->tgl_jab = $tanggal;
        $data->user_insert = Auth::user()->id;
        
        if($request->upload_file != ''){
        if($request->hasFile('upload_file')){
            $image = $request->file('upload_file');
    
            if($image->isValid()){
                $image_name = $image->getClientOriginalName();
                $upload_path = 'fileSK';
                $image->move($upload_path, $image_name);
                $data->file = $image_name;
            }
        }
        }
        
        $data->save();
    }
    \LogActivity::addToLog(Auth::user()->name.' Mengupdate Kenaikan Jabatan '.$kar->nama);
    return response()->json(
        [
            "status"=>"sukses",
            "data"=>$data
        ]
    );
}

public function getjabatan(){
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $data = Jabatan::where('id_com', Auth::user()->id_com)->get();
        return fractal($data, new JabatanTransformer())->toArray();
    }
}

public function statgaji ($id){
    $kar = Karyawan::where('id_karyawan', $id)->first();
    $datkar = [];
    if($kar != null){
    $gaji = Gaji::where('id_karyawan', $id)->whereDate('created', $kar->tgl_gaji)->get();
        if(count($gaji) > 0){
            foreach($gaji as $k => $v){
            $datkar['data'][] = [
                'nama' => $v->nama,
            ];
            }
        }else{
          $datkar['data'] = [];
        }
    }else{
          $datkar['data'] = [];
    }
    return($datkar);
}


public function belap ($id_jab){
    
    if(Auth::user()->id_kantor == 4){
    $laporan = Presensi::leftjoin('karyawan', 'presensi.id_karyawan', '=', 'karyawan.id_karyawan')->leftjoin('jabatan', 'presensi.id_jabatan', '=', 'jabatan.id')
            ->select('presensi.*','karyawan.nomerhp','jabatan.jabatan')
            ->whereDate('presensi.created_at', date('Y-m-d'))->where('presensi.pr_jabatan', $id_jab)
            ->whereNotIn('presensi.id_karyawan', function($query) {$query->select('id_karyawan')->from('laporan')->whereDate('created_at', date('Y-m-d'));})
            ->whereNotIn('presensi.id_karyawan', function($query) {$query->select('id_karyawan')->from('hubungi')->whereDate('created_at', date('Y-m-d'));})
            ->where(function($query) {$query->where('presensi.status', 'Hadir')->orWhere('presensi.status', 'Telambat')->orWhere('presensi.status', 'Bolos');})
            ->get();
    }else{
    $laporan = Presensi::leftjoin('karyawan', 'presensi.id_karyawan', '=', 'karyawan.id_karyawan')->leftjoin('jabatan', 'presensi.id_jabatan', '=', 'jabatan.id')
            ->select('presensi.*','karyawan.nomerhp','jabatan.jabatan')
            ->whereDate('presensi.created_at', date('Y-m-d'))->where('presensi.pr_jabatan', $id_jab)
            ->whereNotIn('presensi.id_karyawan', function($query) {$query->select('id_karyawan')->from('laporan')->whereDate('created_at', date('Y-m-d'));})
            ->whereNotIn('presensi.id_karyawan', function($query) {$query->select('id_karyawan')->from('hubungi')->whereDate('created_at', date('Y-m-d'));})
            ->where(function($query) {$query->where('presensi.status', 'Hadir')->orWhere('presensi.status', 'Telambat')->orWhere('presensi.status', 'Bolos');})
            ->where(function($query) {
                if(Auth::user()->presensi == 'kacab'){
                    $query->where('presensi.id_kantor', Auth::user()->id_kantor)->orWhere('presensi.kantor_induk', Auth::user()->id_kantor);
                }else{
                    $query->where('presensi.id_kantor', Auth::user()->id_kantor);
                }
            })
            ->get();    
    }
      $data = [];
      if(count($laporan) > 0){
        foreach($laporan as $x => $v){
        $query = Hubungi::whereDate('created_at', date('Y-m-d'))->where('id_karyawan', $v->id_karyawan)->get();
            $data['data'][] = [
                'id_karyawan' => $v->id_karyawan,
                'nama' => $v->nama,
                'no_hp' => $v->nomerhp,
                'jabatan' => $v->jabatan,
                'hub' => count($query) > 0 ? 'Sudah Dihubungi' : 'Hubungi'
                ];
        }
      }else{
          $data['data'] = [];
      }
        return($data);
}

public function belfeed ($id, $id_jab){
    
    $feed = Feedback::select('id_laporan')->where('id_karyawan', $id)->whereDate('created_at', date('Y-m-d'));
    $hub = Hubungi::select('id_karyawan')->where('id_atasan', $id)->whereDate('created_at', date('Y-m-d'));
    if(Auth::user()->id_kantor == 4){
    $feedback = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=', 'jabatan.id')->select('laporan.*','jabatan.jabatan')
            ->whereDate('laporan.created_at', date('Y-m-d'))->where('laporan.pr_jabatan', $id_jab)
            ->whereNotIn('laporan.id_laporan', $feed)
            ->whereNotIn('laporan.id_karyawan', $hub)
            ->get();
    }else{
    $feedback = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=', 'jabatan.id')->select('laporan.*','jabatan.jabatan')
            ->whereDate('laporan.created_at', date('Y-m-d'))->where('laporan.pr_jabatan', $id_jab)->where('laporan.id_kantor', Auth::user()->id_kantor)
            ->whereNotIn('laporan.id_laporan', $feed)
            ->whereNotIn('laporan.id_karyawan', $hub)
            ->where(function($query) {
                if(Auth::user()->presensi == 'kacab'){
                    $query->where('laporan.id_kantor', Auth::user()->id_kantor)->orWhere('laporan.kantor_induk', Auth::user()->id_kantor);
                }else{
                    $query->where('laporan.id_kantor', Auth::user()->id_kantor);
                }
            })
            ->get();
    }
    //   $data = [];
    //   if(count($feedback) > 0){
    //     foreach($feedback as $x => $v){
    //         $data['data'][] = [
    //             'id_laporan' => $v->id_laporan,
    //             'id_karyawan' => $v->id_karyawan,
    //             'nama' => $v->nama,
    //             'ket' => $v->ket,
    //             'lampiran' => $v->lampiran,
    //             'jabatan' => $v->jabatan,
    //             ];
    //     }
    //   }else{
    //       $data['data'] = [];
    //   }
    //     return($data);
    return fractal($feedback, new LaporanTransformer())->toArray();
}

public function posthub(Request $request)
{
   
    // $jamker = JamKerja::where('nama_hari', $dayList[$day])->first();
    
    $data = new Hubungi;
    $data->id_karyawan = $request->id_karyawan;
    $data->id_atasan = Auth::user()->id_karyawan;
    // $data->id_jabatan = $request->id_jabatan;
    // $data->pr_jabatan = $request->pr_jabatan;
    // $data->id_kantor = $request->id_kantor;
    // $data->kantor_induk = $request->kantor_induk;
    $data->pesan = $request->pesan;
    
    $data->save();
    
    return response()->json(
        [
            "status"=>"sukses",
            "data"=>$data
        ]
    );
}

public function getwar_naik()
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $datass = Karyawan::where('war_naik','!=', null)->where('aktif',1)->where('id_com', Auth::user()->id_com)->get();   
        $data = [];
        if(count($datass) > 0){
        foreach($datass as $x => $v){
            $data['data'][] = [
                'nama' => $v->nama,
                'war_naik' => $v->war_naik
            ];
        }
            }else{
              $data['data'] = []; 
            }
        return($data);
    }
}

public function feeduser(Request $request){
    $data = new FeedbackUser;
    $data->id_karyawan = Auth::user()->id_karyawan;
    $data->nama = Auth::user()->name;
    
    $data->feedback = $request->feedback;
    
    $data->save();
    
     return response()->json(
        [
            "status"=>"sukses",
            "data"=>$data
        ]
    );
    
}

public function statpresid ($id)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $datass = Presensi::select(\DB::raw("id_karyawan, 
                    SUM(IF(status = 'Hadir',jumlah, 0)) AS Hadir, 
                    SUM(IF(status = 'Terlambat',jumlah, 0)) AS Terlambat, 
                    SUM(IF(status = 'Bolos',jumlah, 0)) AS Bolos, 
                    SUM(IF(status = 'Sakit',jumlah, 0)) AS Sakit, 
                    SUM(IF(status = 'Perdin',jumlah, 0)) AS Perdin, 
                    SUM(IF(status = 'Cuti',jumlah, 0)) AS Cuti, 
                    SUM(IF(status = 'Cuti Penting',jumlah, 0)) AS Cuti_Penting
                    "))
                ->where('id_karyawan', $id)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))
                ->whereIn('id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })
                ->groupBy('id_karyawan')
                ->get();   
        $data = [];
        if(count($datass) > 0){
        foreach($datass as $x => $v){
            $data['data'][] = [
                'id_karyawan' => $v->id_karyawan,
                'Hadir' => $v->Hadir,
                'Terlambat' => $v->Terlambat,
                'Bolos' => $v->Bolos,
                'Sakit' => $v->Sakit,
                'Perdin' => $v->Perdin,
                'Cuti' => $v->Cuti,
                'Cuti_Penting' => $v->Cuti_Penting
            ];
        }
            }else{
              $data['data'] = []; 
            }
        return($data);
    }
}

public function uptema($id, Request $request)
{
    // return $request->tema;
    $data = User::findOrFail($id);
    
    // if($request->mode === 'mode'){
    //     $data->tema = $request->tema;
    // }else{
    //     $data->color = $request->color;
    // }
    
    $data->theme = $request->theme;
    
    $data->update();
    
    return response()->json(
        [
            "status"=>"sukses",
            "data"=>$data
        ]
    );
}

public function getjabid()
{
    $tj = Tunjangan::where('id_com', Auth::user()->id_com)->first();  
    $data = [];
    
        $data['data'][] = [
            'kolektor' => $tj->kolektor,
            'spv_kol' => $tj->spv_kol,
            'so' => $tj->so,
            'spv_so' => $tj->spv_so,
        ];
    
    return($data);
}

public function getuser($act){
    // if(Auth::user()->aktif == 1 && Auth::user()->presensi != null){
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $user = UserKolek::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')->select('users.*', 'jabatan.jabatan')
                ->where(function($query) use ($act){
                    if($act =='aktif'){
                        $query->where('users.aktif', '1');
                    }else if($act =='nonaktif'){
                        $query->where('users.aktif', '0');
                    }
                })
                ->where('users.id_com', Auth::user()->id_com)
                ->get();
        return fractal($user, new UserKarTransformer())->toArray();
    }
    // }
}

public function getkarid($id){
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $kar = Karyawan::leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')->where('karyawan.id_karyawan', $id)->where('karyawan.id_com', Auth::user()->id_com)->get();
        return fractal($kar, new RwyKarTransformer())->toArray();
    }
}

public function getkar(){
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $kar = Karyawan::leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                ->whereNotIn('karyawan.email', function($query) {$query->select('email')->from('users');})
                ->where('karyawan.aktif', 1)
                ->where('karyawan.id_com', Auth::user()->id_com)
                ->get();
        return fractal($kar, new RwyKarTransformer())->toArray();
    }
}

public function getakses()
{
    $lev = Akses::where('level','!=', null)->where('id_com', Auth::user()->id_com)->get();
    $peng = Akses::where('pengaturan','!=', null)->where('id_com', Auth::user()->id_com)->get(); 
    $peg = Akses::where('kepegawaian','!=', null)->where('id_com', Auth::user()->id_com)->get(); 
    $keu = Akses::where('keuangan','!=', null)->where('id_com', Auth::user()->id_com)->get(); 
    $koting = Akses::where('kolekting','!=', null)->where('id_com', Auth::user()->id_com)->get(); 
    $pres = Akses::where('presensi','!=', null)->where('id_com', Auth::user()->id_com)->get(); 
    $kolek = Akses::where('kolektor','!=', null)->where('id_com', Auth::user()->id_com)->get(); 
    $data['data'] = [];
    $data['data']['level'] = [];
    $data['data']['pengaturan'] = [];
    $data['data']['kepegawaian'] = [];
    $data['data']['keuangan'] = [];
    $data['data']['kolekting'] = [];
    $data['data']['presensi'] = [];
    $data['data']['kolektor'] = [];
    foreach($lev as $x => $v){
        $data['data']['level'][] = [
            'val' => $v->level
            ];
    }
    foreach($peng as $x => $v){
        $data['data']['pengaturan'][] = [
            'val' => $v->pengaturan
            ];
    }
    foreach($peg as $x => $v){
        $data['data']['kepegawaian'][] = [
            'val' => $v->kepegawaian
            ];
    }
    foreach($keu as $x => $v){
        $data['data']['keuangan'][] = [
            'val' => $v->keuangan
            ];
    }
    foreach($koting as $x => $v){
        $data['data']['kolekting'][] = [
            'val' => $v->kolekting
            ];
    }
    foreach($pres as $x => $v){
        $data['data']['presensi'][] = [
            'val' => $v->presensi
            ];
    }
    foreach($kolek as $x => $v){
        $data['data']['kolektor'][] = [
            'val' => $v->kolektor
            ];
    }
    return($data);
}

public function regakses(Request $request){
        //  dd($request->all());
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $input = $request->all();
        $kar = Karyawan::where('id_karyawan', $request->id_karyawan)->where('id_com', Auth::user()->id_com)->first();
        $name = $kar->nama;
        
            $input['name'] = $name;
            $input['id_kantor'] = $kar->id_kantor;
            $input['kantor_induk'] = $kar->kantor_induk;
            $input['id_jabatan'] = $kar->jabatan;
            $input['pr_jabatan'] = $kar->pr_jabatan;
            $input['password']  = bcrypt($request->password);
            $input['api_token'] = Str::random(60);
            
            $input['kunjungan'] = $request->kunjungan != '' ? $request->kunjungan : 0;
            $input['qty']       = $request->qty != '' ? $request->qty : 0;      
            $input['minimal']   = $request->minimal != '' ? preg_replace("/[^0-9]/", "", $request->minimal) : 0;
            $input['target']    = $request->target != '' ? preg_replace("/[^0-9]/", "", $request->target) : 0;
            $input['honor']     = $request->honor != '' ? preg_replace("/[^0-9]/", "", $request->honor) : 0;
            $input['bonus']     = $request->bonus != '' ? preg_replace("/[^0-9]/", "", $request->bonus) : 0;
            
            $input['diluar'] = $request->jenis == 'lapangan' ? 1 : 0; 
            $input['id_com'] = $kar->id_com;
            
             \LogActivity::addToLog(Auth::user()->name.' Membuat Data User '.$name);
            
        UserKolek::create($input);
        return response()->json(
            [
                "status"=>"sukses",
            ]
        );
    }
}

public function getuserid($id){
    // if(Auth::user()->aktif == 1 && Auth::user()->presensi != null){
    $user = UserKolek::find($id);
    return fractal($user, new UserKarTransformer())->toArray();
    // }
}
public function upuser($id, Request $request){
    $input = $request->except(['password']);
        
   if($request->password == 'reset') {
        $input['password']  = \Hash::make('12345678');
    }
        
    $input['minimal'] = $request->minimal != '' ? preg_replace("/[^0-9]/", "", $request->minimal) : 0;
    $input['target']  = $request->target != '' ? preg_replace("/[^0-9]/", "", $request->target) : 0;
    $input['honor']   = $request->honor != '' ? preg_replace("/[^0-9]/", "", $request->honor) : 0;
    $input['bonus']   = $request->bonus != '' ? preg_replace("/[^0-9]/", "", $request->bonus) : 0;
    $input['diluar']  = $request->jenis == 'lapangan' ? 1 : 0; 
    UserKolek::where('id', $id)->update($input);
    
    return response()->json(
        [
            "status"=>"sukses",
        ]
    );
}

public function onuser(Request $request, $id)
{
    $user = UserKolek::find($id);
    $user->aktif = $request->aktif;
    $user->update();
    
    $ketlog = $request->aktif == 1 ? ' Mengaktifkan Data User ' : ' Menonaktifkan Data User ';
    \LogActivity::addToLog(Auth::user()->name.$ketlog.$user->name);

    return response()->json([
        "status"=>"sukses"
        ]);
}

public function deluser($id)
{
  $user = UserKolek::findOrFail($id);
  $user->delete();
  \LogActivity::addToLog(Auth::user()->name.' Menghapus Data User '.$user->name);
  
    return response()->json([
        "status"=>"sukses"
        ]);
}

public function readfeed($id)
{
    Feedback::where('id_laporan', $id)->where('id_karyawan', '!=', Auth::user()->id_karyawan)->update([
      'baca' => 1,
    ]);
    
    return response()->json([
        "status"=>"sukses"
        ]);
}

public function feedin(){
    $data = Feedback::where('id_karyawan', '!=', Auth::user()->id_karyawan)->where('baca', '!=', 1)
            ->whereIn('id_laporan', function($query) {
                $query->select('id_laporan')->from('laporan')->where('id_karyawan', Auth::user()->id_karyawan);
            })
            ->get();
    return fractal($data, new FeedbackTransformer())->toArray();
}

// public function laprangejab ($id, $tanggal1, $tanggal2)
// {
//     if(Auth::user()->id_com != null && Auth::user()->presensi != null){
//         $date = date('Y-m-d');
//         if(Auth::user()->id_kantor == 4){
//             $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
//                   ->select('laporan.*', 'jabatan.jabatan') 
//                 //   ->where('laporan.pr_jabatan',$id)
//                   ->where(function($query) use ($id) {
//                       $query->where('laporan.pr_jabatan',$id)
//                             ->orWhereIn('laporan.id_karyawan', function($query) {
//                                 $query->select('id_karyawan')->from('karyawan')->where('id_mentor', Auth::user()->id_karyawan);
//                             });
//                     })
//                   ->whereIn('laporan.id_karyawan', function($query) {
//                             $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
//                     })
//                   ->whereDate('laporan.created_at','>=', $tanggal1)->whereDate('laporan.created_at','<=', $tanggal2)->get();
//         }else{
//             $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
//                   ->select('laporan.*', 'jabatan.jabatan') 
//                 //   ->where('laporan.pr_jabatan',$id)
//                 ->where(function($query) use ($id) {
//                       $query->where('laporan.pr_jabatan',$id)
//                             ->orWhereIn('laporan.id_karyawan', function($query) {
//                                 $query->select('id_karyawan')->from('karyawan')->where('id_mentor', Auth::user()->id_karyawan);
//                             });
//                     })
//                   ->whereIn('laporan.id_karyawan', function($query) {
//                             $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
//                     })    
//                   ->whereDate('laporan.created_at','>=', $tanggal1)->whereDate('laporan.created_at','<=', $tanggal2)->where('laporan.id_kantor', Auth::user()->id_kantor)->get();
//         }
//         return fractal($trf, new LaporanTransformer())->toArray();
//     }
// }

public function laprangejab ($id, $tanggal1, $tanggal2)
{
    $id_kan = Auth::user()->id_kantor;
    $id_jab = Auth::user()->id_jabatan;
    
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
                ->select('laporan.*', 'jabatan.jabatan') 
                ->whereDate('laporan.created_at','>=', $tanggal1)->whereDate('laporan.created_at','<=', $tanggal2)
                ->whereIn('laporan.id_karyawan', function($query) {
                        $query->select('id_karyawan')->from('.karyawan')->where('id_com', Auth::user()->id_com);
                    }) 
                ->where(function($q) use ($id_kan) {
                        if(Auth::user()->presensi == 'kacab'){
                            $q->where('laporan.id_kantor', $id_kan)->orWhere('laporan.kantor_induk', $id_kan);
                        }
                        // else if(Auth::user()->presensi != 'admin' && Auth::user()->id_kantor != 4){
                        //     $q->where('laporan.id_kantor', $id_kan);
                        // }
                        else if(Auth::user()->presensi == 'admin' ){
                            $q->where('laporan.id_com', Auth::user()->id_com);
                        }
                    })
                ->where(function($query) use ($id_jab) {
                    if(Auth::user()->presensi != '' ){
                    $query->where('laporan.pr_jabatan',$id_jab)
                        ->orWhereIn('laporan.id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_mentor', Auth::user()->id_karyawan);
                        });
                    }
                })
                // ->orderBy('laporan.nama', 'esc')
                ->groupBy('laporan.nama')
                ->get();
        return fractal($trf, new LaporanTransformer())->toArray();
    }
}

public function cekup(Request $request, $id)
{
    $user = UserKolek::find($id);
    $user->up_shift = $request->up_shift;
    $user->update();
    \LogActivity::addToLog(Auth::user()->name.' cek up_shift '.$user->name);
    return response()->json([
        "status"=>"sukses"
        ]);
}

public function uncekup(Request $request)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        UserKolek::where('up_shift', 1)
                    ->where('id_com', Auth::user()->id_com)
                    ->update([
                      'up_shift' => 0
                    ]);
        
        return response()->json([
            "status"=>"sukses"
            ]);
    }
}

public function upshift(Request $request)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        UserKolek::where('up_shift', 1)->where('aktif', 1)->where('id_com', Auth::user()->id_com)->update([
          'shift' => $request->shift,
          'up_shift' => 0
        ]);
         \LogActivity::addToLog(Auth::user()->name.' upshift ');
        return response()->json([
            "status"=>"sukses"
            ]);
    }
}

public function shiftuser($shift, $kerja){
    // if(Auth::user()->aktif == 1 && Auth::user()->presensi != null){
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        if($kerja == 'all'){
            $user = UserKolek::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')->select('users.*', 'jabatan.jabatan')->where('users.shift', $shift)->where('users.aktif', 1)->where('users.id_com', Auth::user()->id_com)->get();
        }else{
            $user = UserKolek::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')->select('users.*', 'jabatan.jabatan')->where('users.shift', $shift)->where('users.aktif', 1)->where('users.id_com', Auth::user()->id_com)->where('users.id_kantor', $kerja)->get();
        }
        return fractal($user, new UserKarTransformer())->toArray();
    }
    // }
}

public function uncekshift($shift, $kerja, Request $request)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        if($kerja == 'all'){
            UserKolek::where('shift', $shift)->where('users.aktif', 1)->where('id_com', Auth::user()->id_com)->update(['up_shift' => 0]);
        }else{
            UserKolek::where('shift', $shift)->where('users.aktif', 1)->where('id_kantor', $kerja)->where('id_com', Auth::user()->id_com)->update(['up_shift' => 0]);
        }
        return response()->json([
            "status"=>"sukses"
            ]);
    }
}

public function cekshift($shift, $kerja, Request $request)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        if($kerja == 'all'){
            UserKolek::where('shift', $shift)->where('users.aktif', 1)->where('id_com', Auth::user()->id_com)->update(['up_shift' => 1]);
        }else{
            UserKolek::where('shift', $shift)->where('users.aktif', 1)->where('id_kantor', $kerja)->where('id_com', Auth::user()->id_com)->update(['up_shift' => 1]);
        }
        return response()->json([
            "status"=>"sukses"
            ]);
    }
}

public function lapkol ()
{
    
    $datass = Transaksi_Perhari_All::where('id', Auth::user()->id)->whereDate('Tanggal', date('Y-m-d'))->get();   
    $data = [];
    if(count($datass) > 0){
        foreach($datass as $x => $v){
            $data['data'][] = [
                'id' => $v->id,
                'name' => $v->name,
                'donasi' => $v->donasi,
                't_donasi' => $v->t_donasi,
                'tutup' => $v->tutup,
                'tutup_x' => $v->tutup_x,
                'ditarik' => $v->ditarik,
                'k_hilang' => $v->k_hilang,
                'tf_donasi' => $v->tf_donasi,
                'tf_t_donasi' => $v->tf_t_donasi,
                'tf_off' => $v->tf_off,
                'capaian' => ($v->donasi + $v->t_donasi + $v->tutup + $v->tutup_x + $v->ditarik + $v->k_hilang)/Auth::user()->kunjungan * 100
            ];
        }
    }else{
      $data['data'] = []; 
    }
    return($data);
}

public function lap_mar(Request $req)
{
    $id     = $req->id_karyawan;
    $u      = User::where('id_karyawan', $id)->first();
    $tgl    = $req->tgl;
    $v      = Transaksi_Perhari_All::where('id', $u->id)->whereDate('Tanggal', $tgl)->first(); 
    $w      = Prosp::selectRaw("COUNT(IF(ket = 'closing' AND tgl_fol = '$tgl', id, NULL)) AS closing, 
                                COUNT(IF(ket = 'cancel' AND tgl_fol = '$tgl', id, NULL)) AS cancel, 
                                COUNT(IF(ket = 'open' AND DATE(created_at) = '$tgl', id, NULL)) AS open ")
                    ->where(function($q) use ($tgl) {
                        $q->whereDate('tgl_fol', $tgl)->where('ket', '!=', 'open')
                            ->orWhereDate('created_at', $tgl)->where('ket', 'open');
                    })
                    ->where('id_peg', $u->id)
                    ->first()
                    ;
    $data = [];
    if($v != null){
            $data['data'] = [
                'id'            => $v->id,
                'name'          => $v->name,
                'donasi'        => $v->donasi,
                't_donasi'      => $v->t_donasi,
                'tutup'         => $v->tutup,
                'tutup_x'       => $v->tutup_x,
                'ditarik'       => $v->ditarik,
                'k_hilang'      => $v->k_hilang,
                'kunjungan'     => $v->k_hilang + $v->ditarik + $v->tutup_x + $v->tutup + $v->t_donasi + $v->donasi,
                'tf_donasi'     => $v->tf_donasi,
                'tf_t_donasi'   => $v->tf_t_donasi,
                'tf_off'        => $v->tf_off,
                'tf'            => $v->tf_donasi + $v->tf_t_donasi + $v->tf_off,
                'closing'       => $w->closing,
                'open'          => $w->open,
                'cancel'        => $w->cancel,
                'prospek'       => $w->closing + $w->open + $w->cancel,
                'capaian'       => 0,
                // 'capaian'       => ($v->donasi + $v->t_donasi + $v->tutup + $v->tutup_x + $v->ditarik + $v->k_hilang)/Auth::user()->kunjungan * 100
            ];
    }else{
      $data['data'] = [
                'id'            => 0,
                'name'          => $u->name,
                'donasi'        => 0,
                't_donasi'      => 0,
                'tutup'         => 0,
                'tutup_x'       => 0,
                'ditarik'       => 0,
                'k_hilang'      => 0,
                'kunjungan'     => 0,
                'tf_donasi'     => 0,
                'tf_t_donasi'   => 0,
                'tf_off'        => 0,
                'tf'            => 0,
                'closing'       => 0,
                'open'          => 0,
                'cancel'        => 0,
                'prospek'       => 0,
                'capaian'       => 0,
            ]; 
    }
    return($data);
}

public function copyjamker($id, $shift, Request $request)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $data = JamKerja::find($id);
        $senin = $request->Senin != '' ? "nama_hari = 'Monday'" : "nama_hari = '0'";
        $selasa = $request->Selasa != '' ? "nama_hari = 'Tuesday'" : "nama_hari = '0'";
        $rabu = $request->Rabu != '' ? "nama_hari = 'Wednesday'" : "nama_hari = '0'";
        $kamis = $request->Kamis != '' ? "nama_hari = 'Thursday'" : "nama_hari = '0'";
        $jumat = $request->Jumat != '' ? "nama_hari = 'Friday'" : "nama_hari = '0'";
        $sabtu = $request->Sabtu != '' ? "nama_hari = 'Saturday'" : "nama_hari = '0'";
        $minggu = $request->Minggu != '' ? "nama_hari = 'Sunday'" : "nama_hari = '0'";
        // dd($data->cek_in);
        
        JamKerja::where('shift', $shift)
                ->where(function($query) use ($senin, $selasa, $rabu, $kamis, $jumat, $sabtu, $minggu) {
                            $query->whereRaw("$senin OR $selasa OR $rabu OR $kamis OR $jumat OR $sabtu OR $minggu");
                        })
                ->where('id_com', Auth::user()->id_com)
        ->update([
            'cek_in' => $data->cek_in,
            'terlambat' => $data->terlambat,
            'break_out' => $data->break_out,
            'break_in' => $data->break_in,
            'cek_out' => $data->cek_out,
            'status' => $data->status,
            'user_update' => Auth::user()->id
            ]);
        
        
        // $data->update();
        // \LogActivity::addToLog(Auth::user()->name.' Mengupdate Jam Kerja');
        return response()->json(
            [
                "status"=>"sukses",
                // "data"=>$data
            ]
        );
    }
}

public function izinup(Request $request, $id)
{
    Kantor::find($id)->update([
        'acc_up' => $request->acc_up,
        'user_update' => Auth::user()->id
        ]);
    
    \LogActivity::addToLog(Auth::user()->name.' Mengubah izin update lokasi');
    
    return response()->json([
        "status"=>"sukses"
        ]);
}

public function getshift ()
{
    // $d = date('l');
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $trf = JamKerja::select('shift')->where('id_com', Auth::user()->id_com)->distinct()->get();
        $data = [];
        if(count($trf) > 0){
        foreach($trf as $x => $v){
            $data['data'][] = [
                'shift' => $v->shift
            ];
        }
            }else{
              $data['data'] = []; 
            }
        return($data);
    }
}

public function upgaji($id, Request $request){
    
    $ket = $request->keterangan != '%5B%5D' ?  json_decode (urldecode($request->keterangan)) : [];
    $nom = $request->nominal != '%5B%5D' ?  json_decode (urldecode($request->nominal)) : [];
    $stat = $request->status != '%5B%5D' ?  json_decode (urldecode($request->status)) : [];
    
    for($i = 0; $i < count($ket); $i++){
    $nominal[$i] = $nom[$i] != '' ? preg_replace("/[^0-9]/", "", $nom[$i]) : 0;
    $gaji = Gaji::find($id);
    $data = new UpGaji;
    $data->id_gaji = $id;
    $data->keterangan = $ket[$i];
    $data->nominal = $nominal[$i];
    $data->status = $stat[$i];
    $data->tmt = $request->tmt;
    $data->id_karyawan = $request->id_karyawan;
    $data->tanggal = $gaji->created_at->format('Y-m-d');
    $data->tgl_gaji = $gaji->created_at->format('m');
    $data->user_insert = Auth::user()->id;
    
    $data->save();
    
        if($ket[$i] == 'Gaji Pokok'){
        $tj = Tunjangan::where('id_com', Auth::user()->id_com)->first();
        
        $kar = RekapKar::where('id_gaji', $id)->first();
        $nm_pas = $kar->nm_pasangan != null ? unserialize($kar->nm_pasangan) : [];
        $nm_ank = $kar->nm_anak != null ? unserialize($kar->nm_anak) : [];
        
        $tj_pas = count($nm_pas) * ($tj->tj_pasangan/100 * ($nominal[$i] + $gaji->gapok)) - $gaji->tj_pasangan;
        $tj_ank = count($nm_ank) * ($tj->tj_anak/100 * ($nominal[$i] + $gaji->gapok)) - $gaji->tj_anak;
        
        $ketgapok = ['Tunjangan Istri / Suami', 'Tunjangan Anak'];
        $nomgapok = [$tj_pas, $tj_ank];
        
            for($j = 0; $j < 2; $j++){
                $data = new UpGaji;
                $data->id_gaji = $id;
                $data->keterangan = $ketgapok[$j];
                $data->nominal = $nomgapok[$j];
                $data->status = $stat[$i];
                $data->tmt = $request->tmt;
                $data->id_karyawan = $request->id_karyawan;
                $data->tanggal = $gaji->created_at->format('Y-m-d');
                $data->tgl_gaji = $gaji->created_at->format('m');
                $data->user_insert = Auth::user()->id;
                $data->save();
            }
        }
    }
    // if($request->status == 'tambah'){
    //     $gaji = Gaji::find($id);
    //     $gaji->thp = $gaji->thp + $nominal;
    // }else{
    //     $gaji = Gaji::find($id);
    //     $gaji->thp = $gaji->thp - $nominal;
    // }
    
    // $gaji->update();
    
     return response()->json(
        [
            "status"=>"sukses"
        ]
    );
}

public function upgajirapel(Request $request){
    
    $ket = $request->keterangan != '%5B%5D' ?  json_decode (urldecode($request->keterangan)) : [];
    $nom = $request->nominal != '%5B%5D' ?  json_decode (urldecode($request->nominal)) : [];
    $stat = $request->status != '%5B%5D' ?  json_decode (urldecode($request->status)) : [];
    $id_gaji = $request->id_gaji != '%5B%5D' ?  json_decode (urldecode($request->id_gaji)) : [];
    
    for($h = 0; $h < count($id_gaji); $h++){    
        for($i = 0; $i < count($ket); $i++){
        $nominal[$i] = $nom[$i] != '' ? preg_replace("/[^0-9]/", "", $nom[$i]) : 0;
        $gaji = Gaji::find($id_gaji[$h]);
        
        $data = new UpGaji;
        $data->id_gaji = $id_gaji[$h];
        $data->keterangan = $ket[$i];
        $data->nominal = $nominal[$i];
        $data->status = $stat[$i];
        $data->tmt = $request->tmt;
        $data->id_karyawan = $request->id_karyawan;
        $data->tanggal = $gaji->created_at->format('Y-m-d');
        $data->tgl_gaji = $gaji->created_at->format('m');
        $data->user_insert = Auth::user()->id;
        $data->save();
        
            if($ket[$i] == 'Gaji Pokok'){
            $tj = Tunjangan::where('id_com', Auth::user()->id_com)->first();
            
            $kar = RekapKar::where('id_gaji', $id_gaji[$h])->first();
            $nm_pas = $kar->nm_pasangan != null ? unserialize($kar->nm_pasangan) : [];
            $nm_ank = $kar->nm_anak != null ? unserialize($kar->nm_anak) : [];
            
            $tj_pas = count($nm_pas) * ($tj->tj_pasangan/100 * ($nominal[$i] + $gaji->gapok)) - $gaji->tj_pasangan;
            $tj_ank = count($nm_ank) * ($tj->tj_anak/100 * ($nominal[$i] + $gaji->gapok)) - $gaji->tj_anak;
            
            $ketgapok = ['Tunjangan Istri / Suami', 'Tunjangan Anak'];
            $nomgapok = [$tj_pas, $tj_ank];
            
                for($j = 0; $j < 2; $j++){
                    $data = new UpGaji;
                    $data->id_gaji = $id_gaji[$h];
                    $data->keterangan = $ketgapok[$j];
                    $data->nominal = $nomgapok[$j];
                    $data->status = $stat[$i];
                    $data->tmt = $request->tmt;
                    $data->id_karyawan = $request->id_karyawan;
                    $data->tanggal = $gaji->created_at->format('Y-m-d');
                    $data->tgl_gaji = $gaji->created_at->format('m');
                    $data->user_insert = Auth::user()->id;
                    
                    $data->save();
                }
            }
        }
    }
    
    // if($request->status == 'tambah'){
    //     $gaji = Gaji::find($id);
    //     $gaji->thp = $gaji->thp + $nominal;
    // }else{
    //     $gaji = Gaji::find($id);
    //     $gaji->thp = $gaji->thp - $nominal;
    // }
    
    // $gaji->update();
    
     return response()->json(
        [
            "status"=>"sukses"
        ]
    );
}

public function getupgaji ($id)
{
    $upgaji = UpGaji::select(\DB::raw("id_gaji, keterangan, tgl_bayar, SUM(nominal) AS nominal, status"))->where('id_gaji', $id)->groupBy('id_gaji', 'tgl_bayar', 'keterangan', 'status')->get();
    $data = [];
    if(count($upgaji) > 0){
    foreach($upgaji as $x => $v){
        $data['data'][] = [
            'tgl_bayar' => $v->tgl_bayar,
            'keterangan' => $v->keterangan,
            'nominal' => number_format($v->nominal,0, ',' , '.'),
            'status' => $v->status
        ];
    }
        }else{
          $data['data'] = []; 
        }
    return($data);
}

public function listbayarupgaji ($stat)
{
    $upgaji = UpGaji::join('karyawan', 'up_gaji.id_karyawan', '=', 'karyawan.id_karyawan')->join('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                ->select(\DB::raw("up_gaji.tmt, karyawan.id_karyawan, karyawan.nama, jabatan.jabatan, SUM(up_gaji.nominal) AS nominal, up_gaji.status"))
                ->whereDate('up_gaji.tmt', '<=', date('Y-m-d'))->where('up_gaji.bayar', 0)->where('up_gaji.status', $stat)->groupBy('up_gaji.tmt', 'karyawan.id_karyawan','karyawan.nama','jabatan.jabatan','up_gaji.status')->get();
    $data = [];
    if(count($upgaji) > 0){
    foreach($upgaji as $x => $v){
        $data['data'][] = [
            'tmt' => $v->tmt,
            'id_karyawan' => $v->id_karyawan,
            'nama' => $v->nama,
            'jabatan' => $v->jabatan,
            'nominal' => number_format($v->nominal,0, ',' , '.'),
            'status' => $v->status
        ];
    }
        }else{
          $data['data'] = []; 
        }
    return($data);
}

public function bayarupgaji ($id, $stat)
{
    $upgaji = UpGaji::select(\DB::raw("tanggal, tgl_gaji, keterangan, SUM(nominal) AS nominal, status"))->where('id_karyawan', $id)->where('status', $stat)->where('bayar', 0)->groupBy('tanggal','tgl_gaji','keterangan', 'status')->get();
    $data = [];
    if(count($upgaji) > 0){
    foreach($upgaji as $x => $v){
        $data['data'][] = [
            'tanggal' => $v->tanggal,
            'tgl_gaji' => $v->tgl_gaji,
            'keterangan' => $v->keterangan,
            'nominal' => number_format($v->nominal,0, ',' , '.'),
            'status' => $v->status,
        ];
    }
        }else{
          $data['data'] = []; 
        }
    return($data);
}

public function upbayarupgaji($id, $tmt, $stat, Request $request)
{
    UpGaji::where('id_karyawan',$id)->whereDate('tmt', $tmt)->where('status', $stat)->update(['tgl_bayar' => date('Y-m-d'), 'bayar' => 1]);
    
    return response()->json([
        "status"=>"sukses"
        ]);
}
public function lapbawahan ($pres, $jab, $kan, $tgl)
{
    $date = $tgl;
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
    if($pres == 'kacab'){
        $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
          ->select('laporan.*', 'jabatan.jabatan') 
          ->where('laporan.kantor_induk', $kan)->whereDate('laporan.created_at',$date)->orWhere('laporan.id_kantor', $kan)->whereDate('laporan.created_at',$date)
          ->whereIn('laporan.id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                    })
          ->get();
    }else{
        if($kan == 4){
            $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
                  ->select('laporan.*', 'jabatan.jabatan') 
                  ->where('laporan.pr_jabatan',$jab)->whereDate('laporan.created_at',$date)
                  ->whereIn('laporan.id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                    })
                  ->get();
        }else{
            $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
                  ->select('laporan.*', 'jabatan.jabatan') 
                  ->where('laporan.pr_jabatan',$jab)->whereDate('laporan.created_at',$date)->where('laporan.id_kantor', $kan)
                  ->whereIn('laporan.id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                    })
                  ->get();
        }    
    }
    return fractal($trf, new LaporanTransformer())->toArray();
    }
}

public function lapbawahankar ($id, $tgl)
{
    $kary = UserKolek::where('id_karyawan', $id)->first();
    $pres = $kary->presensi;
    $jab = $kary->id_jabatan;
    $kan = $kary->id_kantor;
    
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
    $date = $tgl;
    if($pres == 'kacab'){
        $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
          ->select('laporan.*', 'jabatan.jabatan') 
          ->where('laporan.kantor_induk', $kan)->whereDate('laporan.created_at',$date)->orWhere('laporan.id_kantor', $kan)->whereDate('laporan.created_at',$date)
          ->whereIn('laporan.id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                    })
          ->get();
    }else{
        if($kan == 4){
            $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
                  ->select('laporan.*', 'jabatan.jabatan') 
                  ->where('laporan.pr_jabatan',$jab)->whereDate('laporan.created_at',$date)
                  ->whereIn('laporan.id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                    })
                  ->get();
        }else{
            $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
                  ->select('laporan.*', 'jabatan.jabatan') 
                  ->where('laporan.pr_jabatan',$jab)->whereDate('laporan.created_at',$date)->where('laporan.id_kantor', $kan)
                  ->whereIn('laporan.id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                    })
                  ->get();
        }    
    }
    return fractal($trf, new LaporanTransformer())->toArray();
    }
}

public function delap($id){
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $datalap = Laporan::find($id);
        $datalap->delete();
        return response()->json([
            "status"=>"sukses"
            ]);
    }
}


// update data pertugas
public function uplaper_tug(Request $request)
{
    $id = $request->id_tugas;
    $data = Rencana::find($id);
    $data->deskripsi = $request->deskripsi;
    $data->capaian = $request->capaian;
    $data->jam_awal = $request->jam_awal;
    $data->jam_akhir = $request->jam_akhir;

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move('lampiranLaporan', $fileName); // Simpan file ke lokasi penyimpanan yang diinginkan
        $data->bukti = $fileName;
    }

    $data->update();

    return response()->json(
        [
            "status" => "sukses",
            "data" => $data,
            "request_files" => $request->hasFile('file'),
            "data.bukti" => $request->file,
        ]
    );
}


public function uplap($id, Request $request)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){    
        $data = Laporan::find($id);
        // $data->id_karyawan = Auth::user()->id_karyawan;
        // $data->id_jabatan = Auth::user()->id_jabatan;
        // $data->pr_jabatan = Auth::user()->pr_jabatan;
        // $data->id_kantor = Auth::user()->id_kantor;
        // $data->kantor_induk = Auth::user()->kantor_induk;
        // $data->nama = Auth::user()->name;
        
        $data->ket = $request->ket;
        $data->capaian = $request->capaian;
        $data->target = $request->target;
        if($request->sec_vn != ''){
            $data->sec_vn = $request->sec_vn;
        }else{
            $data->sec_vn = 0;
        }
        if($request->link_lap != ''){
            $data->link_lap = $request->link_lap;
        }else{
            $data->link_lap = NULL;
        }
        
        if($request->file != $data->lampiran && $request->file != ''){
        $data->lampiran = $request->file('file')->getClientOriginalName();
        $request->file('file')->move('lampiranLaporan',$data->lampiran);
        }
        
        if($request->vn != $data->vn && $request->vn != ''){
        $data->vn = $request->file('vn')->getClientOriginalName();
        $request->file('vn')->move('lampiranLaporan',$data->vn);
        }
        
        $data->update();
        
        return response()->json(
            [
                "status"=>"sukses",
                // "data"=>$data
            ]
        );
    }
}

public function getaccu()
{
    $v = Tunjangan::where('id_com', Auth::user()->id_com)->first();   
    
         return response()->json(['data' => $v->accu]);
}

public function upaccu(Request $request)
{  
    $datkar = Tunjangan::where('id_com', Auth::user()->id_com)->first()->update(['accu' => $request->accu]);
    
    return response()->json(
        [
            "status"=>"sukses"
        ]
    );
}

public function uppres($id, Request $request)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi == 'admin'){
        $data = Presensi::where('id_presensi', $id)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->first();
        // $data = Presensi::find($id);
        
        $data->cek_in = $request->cek_in;
        $data->keterlambatan = $request->keterlambatan;
        $data->ket = $request->ket == 'null' ? NULL : $request->ket;
        $data->break_out = $request->break_out;
        $data->break_in = $request->break_in;
        $data->cek_out = $request->cek_out;
        $data->status = $request->status;
        $data->acc = $request->acc;
        $data->user_update = Auth::user()->id;
        
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate presensi'.$id);
        return response()->json(
            [
                "status"=>"sukses"
            ]
        );
    }
}

public function delpres($id, Request $request)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $data = Presensi::findOrFail($id);
        $data->delete();
      
        return response()->json([
            "status"=>"sukses"
            ]);
    }
}


public function posttodo(Request $request, $id, $tgl){
    
    $date = date('Y-m-d');
    $con = ToDo::where('id_karyawan', $id)->whereDate('created_at', $tgl)->get();
    
    
    $idr = $request->id != '%5B%5D' ?  json_decode (urldecode($request->id)) : [];
        $idrows = $idr != [] ? serialize($idr) : null;
    $nm = $request->name != '%5B%5D' ?  json_decode (urldecode($request->name)) : [];
        $name = $nm != [] ? serialize($nm) : null;
    $dc = $request->description != '%5B%5D' ?  json_decode (urldecode($request->description)) : [];
        $desc = $dc != [] ? serialize($dc) : null;
    $st = $request->status != '%5B%5D' ?  json_decode (urldecode($request->status)) : [];
        $stat = $st != [] ? serialize($st) : null;
    
    if(count($con) == 0){
        $data = new ToDo;
        $data->id_karyawan = $id; 
        $data->id_rows = $idrows;
        $data->name_rows = $name;
        $data->desc_rows = $desc;
        $data->stat_rows = $stat;
        $data->created_at = $tgl;
        $data->save();
    }else{
        $data = ToDo::where('id_karyawan',$id)->whereDate('created_at', $tgl)
                ->update([
                    'id_rows' => $idrows,
                    'name_rows' => $name,
                    'desc_rows' => $desc,
                    'stat_rows' => $stat
                    ]);
    }
    // \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Keluarga '.$peg->nama);
    return response()->json(
        [
            "status"=>"sukses",
            // "data"=>$data
        ]
    );
}

public function gettodo($id, $tgl){
    
    $tod = ToDo::where('id_karyawan', $id)->whereDate('created_at', $tgl)->get();
    $data = [];
    if(count($tod) > 0){
        foreach($tod as $x => $v){
            $id_rows = $v->id_rows != null ? unserialize($v->id_rows) : [];
            $name_rows = $v->name_rows != null ? unserialize($v->name_rows) : [];
            $desc_rows = $v->desc_rows != null ? unserialize($v->desc_rows) : [];
            $stat_rows = $v->stat_rows != null ? unserialize($v->stat_rows) : []; 
            $todo = [];
            $progs = [];
            $done = [];
            $tods = [];
            foreach($id_rows as $c => $d){
                $tods[] = [
                        'description'=> $desc_rows[$c], 
                        'status'=> $stat_rows[$c]
                        ];
                if($stat_rows[$c] == 'To Do'){
                $todo[] = [ 
                        'id'=> $id_rows[$c],
                        'name'=> $name_rows[$c], 
                        'description'=> $desc_rows[$c] 
                        ];
                }else if($stat_rows[$c] == 'Progress'){
                $progs[] = [ 
                        'id'=> $id_rows[$c],
                        'name'=> $name_rows[$c], 
                        'description'=> $desc_rows[$c] 
                        ];
                }else{
                $done[] = [ 
                        'id'=> $id_rows[$c],
                        'name'=> $name_rows[$c], 
                        'description'=> $desc_rows[$c] 
                        ];
                }
            }
            for($i = 0; $i < 3; $i++){
              if($i == 0){
                $rows[] = [
                        'id_rows'=> $id_rows,
                        'name_rows'=> $name_rows, 
                        'desc_rows'=> $desc_rows,
                        'id_todo' => $v->id_todo,
                        'id' => 1,
                        'name' => 'TO DO',
                        'rows' => $todo,
                        ];
                }else if($i == 1){
                $rows[] = [
                        'id_rows'=> $id_rows,
                        'name_rows'=> $name_rows, 
                        'desc_rows'=> $desc_rows,
                        'id_todo' => $v->id_todo,
                        'id' => 2,
                        'name' => 'IN PROGRESS',
                        'rows' => $progs,
                        ];
                }else{
                $rows[] = [ 
                        'id_rows'=> $id_rows,
                        'name_rows'=> $name_rows, 
                        'desc_rows'=> $desc_rows,
                        'id_todo' => $v->id_todo,
                        'id' => 3,
                        'name' => 'DONE',
                        'rows' => $done,
                        ];
                } 
            }
            
            // $data['data'] = $tods;
            $data['data'][] = [ 
                        'id'=> $id_rows,
                        'description'=> $desc_rows,
                        'status'=> $stat_rows
                    ];
        }
    }else{
      $data['data'] = []; 
    }
    return($data);
}

public function loginpen(Request $request, PenUser $user){
    if(!Auth::guard('userpendik')->attempt(['username' => $request->email, 'password' => $request->password])){
        return response()->json(['error' => 'salah bos',401]);
    }else{
        $userk = $user->find(Auth::guard('userpendik')->user()->id_users);
        // if($userk->presensi != null){
        // \LogActivity::addToLog(Auth::guard('userkolek')->user()->name.' telah Logging Presensi App ');
        return response()->json([
            'berhasil' => $userk,
            'token' => $userk->token,
            ]);
        // }
        return response()->json(['error' => 'salah bos',401]);
    }
    }


public function profilepen(PenUser $user){
    // if(Auth::user()->aktif == 1 && Auth::user()->presensi != null){
    $user = PenUser::find(Auth::user()->id_users);
    $fractal = fractal()
    ->item($user)
    ->transformWith(new UserPenTransformer())
    ->toArray();
    return response()->json($fractal);
    // }
}

public function accgaji2($id, Request $request)
{
    $p = $request->potlaptab != '%5B%5D' ?  json_decode (urldecode($request->potlaptab)) : [];
    $potlaptab = $p != [] ? serialize($p) : null;
    $p1 = $request->arpotkol != '%5B%5D' ?  json_decode (urldecode($request->arpotkol)) : [];
    $arpotkol = $p1 != [] ? serialize($p1) : null;
    // $p2 = $request->arrinbon != '%5B%5D' ?  json_decode (urldecode($request->arrinbon)) : [];
    // $arrinbon = $p2 != [] ? serialize($p2) : null;
    
        $data = new Gaji;
        $data->id_karyawan = $id;
        $data->nama = $request->nama;
        $data->nik = $request->nik;
        $data->no_rek = $request->no_rek;
        $data->status_kerja = $request->status_kerja;
        $data->masa_kerja = $request->masa_kerja;
        $data->golongan = $request->golongan;
        $data->id_jabatan = $request->id_jabatan;
        $data->id_kantor = $request->id_kantor;
        $data->gapok = preg_replace("/[^0-9]/", "", $request->gapok);
        $data->tj_jabatan = preg_replace("/[^0-9]/", "", $request->tj_jabatan);
        $data->tj_daerah = preg_replace("/[^0-9]/", "", $request->tj_daerah);
        $data->tj_p_daerah = preg_replace("/[^0-9]/", "", $request->tj_p_daerah);
        $data->tj_anak = preg_replace("/[^0-9]/", "", $request->tj_anak);
        $data->tj_pasangan = preg_replace("/[^0-9]/", "", $request->tj_pasangan);
        $data->tj_beras = preg_replace("/[^0-9]/", "", $request->tj_beras);
        $data->transport = preg_replace("/[^0-9]/", "", $request->transport);
        $data->jml_hari = $request->jml_hari;
        $data->total = preg_replace("/[^0-9]/", "", $request->total);
        $data->thp = preg_replace("/[^0-9]/", "", $request->thp);
        $data->bpjs = preg_replace("/[^0-9]/", "", $request->bpjs);
        $data->potab = preg_replace("/[^0-9]/", "", $request->potab);
        $data->potongan = preg_replace("/[^0-9]/", "", $request->potongan);
        $data->ketenagakerjaan = preg_replace("/[^0-9]/", "", $request->ketenagakerjaan);
        $data->kesehatan = preg_replace("/[^0-9]/", "", $request->kesehatan);
        $data->bonus = preg_replace("/[^0-9]/", "", $request->bonus);
        $data->bokin = preg_replace("/[^0-9]/", "", $request->bokin);
        $data->potdis = preg_replace("/[^0-9]/", "", $request->potdis);
        $data->potdll = preg_replace("/[^0-9]/", "", $request->potdll);
        $data->created_at = $request->tgl_gaji;
        $data->potlaptab = $potlaptab;
        $data->arpotkol = $arpotkol;
        // $data->arrinbon = $arrinbon;
        if($id == Auth::user()->id_karyawan && Auth::user()->presensi == 'kacab'){
            $data->status = 'req';
        }else{
            $data->status = $request->status;
        }
        $data->created = date('Y-m-d H:i:s');
        $data->user_insert = Auth::user()->id;
        
        $data->save();
        
        
    $tg_gaji = date('Y-m-01', strtotime('+1 month', strtotime($request->tgl_gaji)));
    
    $datkar = Karyawan::where('id_karyawan',$id)->where('id_com', Auth::user()->id_com)->update(['tgl_gaji' => $tg_gaji]);
        \LogActivity::addToLog(Auth::user()->name.' Menyetujui Gaji '.$request->nama);
    return response()->json(
        [
            "status"=>"sukses",
            "data"=> []
        ]
    );
}

public function getgajiid2 ($id)
{
    $gaji = Gaji::where('id_gaji',$id)->get();
    return fractal($gaji, new GajiNewTransformer())->toArray();
}

public function profilekar(UserKolek $user){
      $user = UserKolek::join('company', 'users.id_com', '=', 'company.id_com')
        ->join('karyawan', 'users.id_karyawan', '=', 'karyawan.id_karyawan') // Join ke tabel karyawan
        ->select('users.*', 'company.gaji', 'karyawan.*','company.client') // Ambil kolom foto dari karyawan
        ->where('users.id', Auth::user()->id)
        ->first();

    $fractal = fractal()
        ->item($user)
        ->transformWith(new UserKarTransformer())
        ->toArray();

    return response()->json($fractal);
}

public function stat_pres($tanggal1, $tanggal2)
{
    
    $dari = date('Y-m-d', strtotime($tanggal1));
    $sampai = date('Y-m-d', strtotime($tanggal2));
    $id_kan = Auth::user()->id_kantor;
    $id_jab = Auth::user()->id_jabatan;
    
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
    $data = Presensi::select('status')
                    ->selectRaw("SUM(jumlah) as jumlah")
                    ->where(function($q) use ($id_kan, $id_jab, $dari, $sampai) {
                        if(Auth::user()->presensi == 'kacab'){
                            $q->where('id_kantor', $id_kan)->orWhere('kantor_induk', $id_kan);
                        }else if(Auth::user()->presensi != 'admin'){
                            $q->where('pr_jabatan', $id_jab)->where('id_kantor', $id_kan);
                        }
                    })
                    ->whereDate('created_at','>=',$dari)->whereDate('created_at','<=',$sampai)
                    ->whereIn('id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })
                    ->groupBy('status')
                    ->get();
    }   
    // ->select(function($q) use ($dari, $sampai) {
    //                     if(Auth::user()->presensi == 'kacab'){
    //                         $q->selectRaw("SUM(IF( DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai' AND id_kantor = '$id_kan'
    //                                         OR DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai' AND kantor_induk = '$id_kan'
    //                                         , presensi.jumlah, 0)) as jumlah");
    //                     }else if(Auth::user()->presensi == 'admin'){
    //                         $q->selectRaw("SUM(IF( DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai'
    //                                         , presensi.jumlah, 0)) as jumlah");
    //                     }else{
    //                         $q->selectRaw("SUM(IF( DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai' AND pr_jabatan = '$id_jab' AND id_kantor = '$id_kan'
    //                                         , presensi.jumlah, 0)) as jumlah");
    //                     }
    //                 })
    
    // if($lev == 'kacab'){
    // $data = Presensi::select(\DB::raw("status, SUM(IF( DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai' AND id_kantor = '$id'
    //                                     OR DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai' AND kantor_induk = '$id'
    //                                     , presensi.jumlah, 0)) as jumlah"))
    //         ->groupBy('status')
    //         // ->whereDate('created_at','>=',$dari)->whereDate('created_at','<=',$sampai)
    //         ->get();
    // }else if($lev == 'admin'){
    // $data = Presensi::select(\DB::raw("status, SUM(IF( DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai'
    //                                     , presensi.jumlah, 0)) as jumlah"))
    //         // ->whereDate('created_at','>=',$dari)->whereDate('created_at','<=',$sampai)
    //         ->groupBy('status')
    //         ->get();
    // }else if($lev == 'karyawan'){
    //     if(Auth::user()->id_kantor == 4){
    //     $data = Presensi::select(\DB::raw("status, SUM(IF( DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai' AND pr_jabatan = '$id'
    //                                         , presensi.jumlah, 0)) as jumlah"))
    //             ->groupBy('status')
    //             // ->whereDate('created_at','>=',$dari)->whereDate('created_at','<=',$sampai)
    //             ->get();
    //     }else{
    //     $id_kan = Auth::user()->id_kantor;
    //     $data = Presensi::select(\DB::raw("status, SUM(IF( DATE(created_at) >= '$dari' AND DATE(created_at) <= '$sampai' AND pr_jabatan = '$id' AND id_kantor = '$id_kan'
    //                                         , presensi.jumlah, 0)) as jumlah"))
    //             ->groupBy('status')
    //             // ->whereDate('created_at','>=',$dari)->whereDate('created_at','<=',$sampai)
    //             ->get();
    //     }
    // }
    return fractal($data, new StatPresTransformer())->toArray();
}

public function stat_presrin($tanggal1, $tanggal2)
{
    
    $dari = date('Y-m-d', strtotime($tanggal1));
    $sampai = date('Y-m-d', strtotime($tanggal2));
    $id_kan = Auth::user()->id_kantor;
    $id_jab = Auth::user()->id_jabatan;
    if(Auth::user()->id_com != null && Auth::user()->presensi != null ){
    $data = Presensi::leftjoin('jabatan', 'presensi.id_jabatan', '=' ,'jabatan.id')
                    ->select('presensi.id_karyawan', 'presensi.nama', 'jabatan.jabatan', 'presensi.id_jabatan', 'presensi.pr_jabatan', 'presensi.id_kantor', 'presensi.kantor_induk', 'presensi.status')
                    ->selectRaw("SUM(presensi.jumlah) as jumlah")
                    ->where(function($q) use ($id_kan, $id_jab, $dari, $sampai) {
                        if(Auth::user()->presensi == 'kacab'){
                            $q->where('presensi.id_kantor', $id_kan)->orWhere('presensi.kantor_induk', $id_kan);
                        }else if(Auth::user()->presensi != 'admin'){
                            $q->where('presensi.pr_jabatan', $id_jab)->where('presensi.id_kantor', $id_kan);
                        }
                    })
                    ->whereDate('presensi.created_at', '>=', $dari)->whereDate('presensi.created_at', '<=', $sampai)
                    ->whereIn('presensi.id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                        })
                    ->groupBy('presensi.id_karyawan', 'presensi.nama', 'presensi.id_jabatan', 'presensi.pr_jabatan', 'presensi.id_kantor', 'presensi.kantor_induk', 'presensi.status')
                    ->get();
    }
    // if($lev == 'kacab'){
    // $data = Presensi::leftjoin('jabatan', 'presensi.id_jabatan', '=' ,'jabatan.id')
    //         ->select(\DB::raw("presensi.id_karyawan, presensi.nama, jabatan.jabatan, presensi.id_jabatan, presensi.pr_jabatan, presensi.id_kantor, presensi.kantor_induk, presensi.status, 
    //                             SUM(IF( DATE(presensi.created_at) >= '$dari' AND DATE(presensi.created_at) <= '$sampai' , presensi.jumlah, 0)) as jumlah"))
    //         ->where('presensi.id_kantor', $id)->whereDate('presensi.created_at', '>=', $dari)->whereDate('presensi.created_at', '<=', $sampai)
    //         ->orWhere('presensi.kantor_induk', $id)->whereDate('presensi.created_at', '>=', $dari)->whereDate('presensi.created_at', '<=', $sampai)
    //         ->groupBy('presensi.id_karyawan', 'presensi.nama', 'presensi.id_jabatan', 'presensi.pr_jabatan', 'presensi.id_kantor', 'presensi.kantor_induk', 'presensi.status')
    //         ->get();
    // }else if($lev == 'admin'){
    // $data = Presensi::leftjoin('jabatan', 'presensi.id_jabatan', '=' ,'jabatan.id')
    //         ->select(\DB::raw("presensi.id_karyawan, presensi.nama, jabatan.jabatan, presensi.id_jabatan, presensi.pr_jabatan, presensi.id_kantor, presensi.kantor_induk, presensi.status, 
    //                             SUM(IF( DATE(presensi.created_at) >= '$dari' AND DATE(presensi.created_at) <= '$sampai' , presensi.jumlah, 0)) as jumlah"))
    //         ->whereDate('presensi.created_at', '>=', $dari)->whereDate('presensi.created_at', '<=', $sampai)
    //         ->groupBy('presensi.id_karyawan', 'presensi.nama', 'presensi.id_jabatan', 'presensi.pr_jabatan', 'presensi.id_kantor', 'presensi.kantor_induk', 'presensi.status')
    //         ->get();
    // }else if($lev == 'karyawan'){
    //     if(Auth::user()->id_kantor == 4){
    //     $data = Presensi::leftjoin('jabatan', 'presensi.id_jabatan', '=' ,'jabatan.id')
    //             ->select(\DB::raw("presensi.id_karyawan, presensi.nama, jabatan.jabatan, presensi.id_jabatan, presensi.pr_jabatan, presensi.id_kantor, presensi.kantor_induk, presensi.status, 
    //                                 SUM(IF( DATE(presensi.created_at) >= '$dari' AND DATE(presensi.created_at) <= '$sampai' , presensi.jumlah, 0)) as jumlah"))
    //             ->where('presensi.pr_jabatan', $id)->whereDate('presensi.created_at', '>=', $dari)->whereDate('presensi.created_at', '<=', $sampai)
    //             ->groupBy('presensi.id_karyawan', 'presensi.nama', 'presensi.id_jabatan', 'presensi.pr_jabatan', 'presensi.id_kantor', 'presensi.kantor_induk', 'presensi.status')
    //             ->get();
    //     }else{
    //     $id_kan = Auth::user()->id_kantor;
    //     $data = Presensi::leftjoin('jabatan', 'presensi.id_jabatan', '=' ,'jabatan.id')
    //             ->select(\DB::raw("presensi.nama, jabatan.jabatan, presensi.id_jabatan, presensi.pr_jabatan, presensi.id_kantor, presensi.kantor_induk, presensi.status, 
    //                                 SUM(IF( DATE(presensi.created_at) >= '$dari' AND DATE(presensi.created_at) <= '$sampai' AND presensi.id_kantor = '$id_kan' , presensi.jumlah, 0)) as jumlah"))
    //             ->where('presensi.pr_jabatan', $id)->whereDate('presensi.created_at', '>=', $dari)->whereDate('presensi.created_at', '<=', $sampai)->where('presensi.id_kantor', Auth::user()->id_kantor)
    //             ->groupBy('presensi.id_karyawan', 'presensi.nama', 'presensi.id_jabatan', 'presensi.pr_jabatan', 'presensi.id_kantor', 'presensi.kantor_induk', 'presensi.status')
    //             ->get();
    //     }
    // }
    return fractal($data, new StatPresTransformer())->toArray();
}

public function pres_atasan(){
    
    $id_kan = Auth::user()->id_kantor;
    $id_jab = Auth::user()->id_jabatan;
    $day    = date('d');
    $tg     = date('Y-m-01', strtotime(date('Y-m-d')));
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $kar = Karyawan::leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                // ->select('karyawan.*', 'jabatan.id', 'jabatan.jabatan')
                ->selectRaw("karyawan.*, jabatan.id, jabatan.jabatan, 
                            IF('$day' >= 25, 
                                IF(karyawan.tgl_gaji >= DATE(NOW()),1,0), 
                                IF(MONTH(karyawan.tgl_gaji) = MONTH(NOW()) AND YEAR(karyawan.tgl_gaji) = YEAR(NOW()),1,0)
                                ) AS kondisi
                            ")
                // ->selectRaw("karyawan.*, jabatan.id, jabatan.jabatan, IF(($day >= 20 AND karyawan.tgl_gaji >= $tg) OR (karyawan.tgl_gaji < $tg), 0, 1) AS kondisi")
                ->where('karyawan.id_com', Auth::user()->id_com)
                ->where(function($q) use ($id_kan, $id_jab) {
                        if(Auth::user()->presensi == 'admin' | Auth::user()->presensi == 'keuangan pusat'){
                            $q->where('karyawan.id_kantor', '!=', 'hahaha');
                        }else if(Auth::user()->presensi == 'kacab' | Auth::user()->presensi == 'keuangan cabang'){
                            $q->where('karyawan.id_kantor', $id_kan)->orWhere('karyawan.kantor_induk', $id_kan);
                        }else{
                            $q->where('karyawan.pr_jabatan', $id_jab)->where('karyawan.id_kantor', $id_kan);
                        }
                    })
                ->where('karyawan.aktif', 1)
                ->get();
    }    
    return fractal($kar, new RwyKarTransformer())->toArray();
}

public function pres_atasan_gaji(){
    
    $id_kan = Auth::user()->id_kantor;
    $id_jab = Auth::user()->id_jabatan;
    $day    = date('d');
    $tg     = date('Y-m-01');
    $tg_hir = date('Y-m-t');
    $tgs    = date('Y-m-01', strtotime('-1 month', strtotime($tg)));
    
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $kar = Karyawan::leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                // ->select('karyawan.*', 'jabatan.id', 'jabatan.jabatan') 
                ->selectRaw("karyawan.*, jabatan.id, jabatan.jabatan, 
                            IF('$day' >= 25, 
                                IF(karyawan.tgl_gaji >= DATE(NOW()),1,0), 
                                IF(MONTH(karyawan.tgl_gaji) = MONTH(NOW()) AND YEAR(karyawan.tgl_gaji) = YEAR(NOW()),1,0)
                                ) AS kondisi
                            ")
                // ->selectRaw("karyawan.*, jabatan.id, jabatan.jabatan, IF(karyawan.tgl_gaji = $tgs, 0, 1) AS kondisi")
                ->where('karyawan.id_com', Auth::user()->id_com)
                ->where(function($q) use ($id_kan, $id_jab) {
                        if(Auth::user()->presensi == 'admin' | Auth::user()->presensi == 'keuangan pusat'){
                            $q->where('karyawan.id_kantor', '!=', 'hahaha');
                        }else if(Auth::user()->presensi == 'kacab' | Auth::user()->presensi == 'keuangan cabang'){
                            $q->where('karyawan.id_kantor', $id_kan)->orWhere('karyawan.kantor_induk', $id_kan);
                        }else{
                            $q->where('karyawan.pr_jabatan', $id_jab)->where('karyawan.id_kantor', $id_kan);
                        }
                    })
                ->where('karyawan.aktif', 1)
                ->get();
    
    foreach($kar as $user){
        $nm_pas = $user->nm_pasangan != null ? unserialize($user->nm_pasangan) : [];
        $tgl_lahir = $user->tgl_lahir != null ? unserialize($user->tgl_lahir) : [];
        $tgl_nikah = $user->tgl_nikah != null ? unserialize($user->tgl_nikah) : [];
        $anak = $user->nm_anak != null ? unserialize($user->nm_anak) : []; 
        $tgl_anak = $user->tgl_lahir_anak != null ? unserialize($user->tgl_lahir_anak) : [];
        $sts_anak = $user->status_anak != null ? unserialize($user->status_anak) : [];
        
        $data['data'][] = [
                        'id_karyawan' => $user->id_karyawan,
                        'nama' => ucwords($user->nama),
                        'id_jabatan' => $user->id,
                        'id_spv' => $user->id_spv,
                        'plt' => $user->plt,
                        'ttl' => $user->ttl,
                        'tj_pas' => $user->tj_pas,
                        'jab_daerah' => $user->jab_daerah,
                        'unit_kerja' => $user->unit_kerja,
                        'email' => $user->email,
                        'id_kantor' => $user->id_kantor,
                        'jabatan' => $user->jabatan,
                        'tgl_gaji' => $user->tgl_gaji,
                        'status_kerja' => $user->status_kerja,
                        'masa_kerja' => $user->masa_kerja,
                        'id_gol' => $user->id_gol,
                        'no_rek' => $user->no_rek,
                        // 'presensi' => [
                        //     'hadir' => $pres == null ? 0 : $pres->hadir,
                        //     'sakit' => $pres == null ? 0 : $pres->sakit,
                        //     'bolos' => $pres == null ? 0 : $pres->bolos,
                        //     'cuti' => $pres == null ? 0 : $pres->cuti,
                        //     'perdin' => $pres == null ? 0 : $pres->perdin,
                        //     'terlambat' => $pres == null ? 0 : $pres->terlambat,
                        // ],
                        'nm_pasangan' => $nm_pas,
                        'tgl_lahir' => $tgl_lahir,
                        'tgl_nikah' => $tgl_nikah,
                        'nm_anak' => $anak,
                        'tgl_anak' => $tgl_anak,
                        'status_anak' => $sts_anak,
                        'status_nikah' => $user->status_nikah,
                        'scan_kk' => $user->scan_kk,
                        'no_kk' => $user->no_kk,
                        'jml_pas' => $nm_pas != null ? count($nm_pas) : 0,
                        'jml_anak' => $anak != null ? count($anak) : 0,
                        'tgl_mk' => $user->tgl_mk,
                        'tgl_gol' => $user->tgl_gol,
                        'file_sk' => $user->file_sk,
                        'war_naik' => $user->war_naik,
                        'kondisi' => $user->kondisi,
                        'id_com' => $user->id_com,
                    ];
    }
    }
    return $data;
}

public function req_atasan(){
    $date = date('Y-m-d');
    $id_kan = Auth::user()->id_kantor;
    $id_jab = Auth::user()->id_jabatan;
    
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $trf = RequestKar::leftjoin('jabatan', 'request.id_jabatan', '=' ,'jabatan.id')
                ->leftjoin('karyawan', 'request.id_karyawan', '=', 'karyawan.id_karyawan')
                ->select('request.*', 'jabatan.jabatan', 'karyawan.nomerhp')
                ->where(function($q) use ($id_jab, $id_kan){
                        if(Auth::user()->presensi != 'admin'){
                            $q->where('request.id_karyawan', '!=', Auth::user()->id_karyawan);
                        }
                })
                ->where(function($que) use ($id_jab, $id_kan){
                        if(Auth::user()->presensi == 'kacab'){
                            $que->where('request.id_kantor', $id_kan)->orWhere('request.kantor_induk', $id_kan);
                        }else if(Auth::user()->presensi == 'admin'){
                            $que->where('request.id_com', Auth::user()->id_com)->orWhere('request.id_kantor', $id_kan);
                        }else if(Auth::user()->presensi != 'admin'){
                            $que->where('request.pr_jabatan', $id_jab)->where('request.id_kantor', $id_kan);
                        }
                })
                ->where(function($quer) use ($date){
                        $quer->whereDate('request.created_at','<=',$date)->whereDate('request.tg_akhir','>=',$date)
                                ->orWhere(function($query) {
                                  $query->where('request.acc',0)->orWhere('request.acc',2);
                                })
                                ->whereMonth('request.created_at', date('m'))->whereYear('request.created_at', date('Y'));
                })
                ->whereIn('request.id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                })
                ->orderBy('request.created_at', 'desc')
                ->get();
    
    return fractal($trf, new RequestTransformer())->toArray();
    }
}

public function lapnow_atasan (){
    $date = date('Y-m-d');
    $id_kan = Auth::user()->id_kantor;
    $id_jab = Auth::user()->id_jabatan;
    
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
              ->select('laporan.*', 'jabatan.jabatan') 
              ->where(function($q) use ($id_kan, $id_jab) {
                        if(Auth::user()->presensi == 'kacab'){
                            $q->where('laporan.id_kantor', $id_kan)->orWhere('laporan.kantor_induk', $id_kan);
                        }else if(Auth::user()->presensi != 'admin'){
                            $q->where('laporan.id_kantor', $id_kan);
                        }
                    })
              ->where('laporan.pr_jabatan', $id_jab)
              ->whereDate('laporan.created_at',$date)
              ->whereIn('laporan.id_karyawan', function($query) {
                            $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
                    })
              ->get();
              
        // $trf = Laporan::leftjoin('jabatan', 'laporan.id_jabatan', '=' ,'jabatan.id')
        //       ->select('laporan.*', 'jabatan.jabatan') 
        //       ->where(function($q) use ($id_kan, $id_jab) {
        //                 if(Auth::user()->presensi == 'kacab'){
        //                     $q->where('laporan.id_kantor', $id_kan)->orWhere('laporan.kantor_induk', $id_kan);
        //                 }else if(Auth::user()->presensi == 'admin'){
        //                     $q->where('laporan.pr_jabatan', $id_jab)->orWhere('laporan.id_kantor', $id_kan);
        //                 }else if(Auth::user()->presensi != 'admin'){
        //                     $q->where('laporan.pr_jabatan',$id_jab)->where('laporan.id_kantor', $id_kan);
        //                 }
        //             })
        //       ->whereDate('laporan.created_at',$date)
        //       ->whereIn('laporan.id_karyawan', function($query) {
        //                     $query->select('id_karyawan')->from('karyawan')->where('id_com', Auth::user()->id_com);
        //             })
        //       ->get();
    }
    return fractal($trf, new LaporanTransformer())->toArray();
}

public function regakses_new(){
    
        $karyawan = Karyawan::where('id_com', 5)->whereNotIn('email', function($query) {$query->select('email')->from('users');})->get();
        // return($karyawan);
        return(count($karyawan));
        foreach($karyawan as $kar){
            $input['id_karyawan'] = $kar->id_karyawan;
            $input['email'] = $kar->email;
            $input['name'] = $kar->nama;
            $input['id_kantor'] = $kar->id_kantor;
            $input['kantor_induk'] = $kar->kantor_induk;
            $input['id_jabatan'] = $kar->jabatan;
            $input['pr_jabatan'] = $kar->pr_jabatan;
            $input['password']  = bcrypt($kar->nomerhp);
            $input['api_token'] = Str::random(60);
            $input['presensi'] = 'karyawan';
            $input['shift'] = 1;
            
            // $input['kunjungan'] = $request->kunjungan != '' ? $request->kunjungan : 0;
            // $input['qty']       = $request->qty != '' ? $request->qty : 0;      
            // $input['minimal']   = $request->minimal != '' ? preg_replace("/[^0-9]/", "", $request->minimal) : 0;
            // $input['target']    = $request->target != '' ? preg_replace("/[^0-9]/", "", $request->target) : 0;
            // $input['honor']     = $request->honor != '' ? preg_replace("/[^0-9]/", "", $request->honor) : 0;
            // $input['bonus']     = $request->bonus != '' ? preg_replace("/[^0-9]/", "", $request->bonus) : 0;
            
            $input['diluar'] = 1; 
            $input['id_com'] = $kar->id_com;
            
            
        UserKolek::create($input);
        }
        return response()->json(
            [
                "status"=>"sukses",
                "jum"=>count($karyawan)
            ]
        );
}

public function getaks($id)
{
    // $lev = Akses::where(function($query) use ($id) {
    //                         if(Auth::user()->presensi == 'super admin'){
    //                             $query->where(function($quer) use ($id) {
    //                                 $quer->where('id_com', $id)
    //                                         ->orWhere('holding', 1)->whereIn('id_com', function($q) {
    //                                                 $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_hc);
    //                                             });
    //                             });
    //                         }else{
    //                             $query->where('id_com', Auth::user()->id_com)->where('holding', 0);
    //                         }
    //                     })
    //                 ->distinct()->get();
    
    $lev = Akses::where(function($query) use ($id) {
                            if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                $query->where('id_com', $id);
                            }else{
                                $query->where('id_com', Auth::user()->id_com);
                            }
                        })
                    ->distinct()->get();
                    
    $data['data'] = [];
    $data['data']['level'] = [];
    $data['data']['pengaturan'] = [];
    $data['data']['kepegawaian'] = [];
    $data['data']['keuangan'] = [];
    $data['data']['kolekting'] = [];
    $data['data']['presensi'] = [];
    $data['data']['kolektor'] = [];
    
    foreach($lev as $x => $v){
        if($v->level != null){
            $data['data']['level'][] = [
                'val' => $v->level
                ];
        }
        if($v->pengaturan != null){
            $data['data']['pengaturan'][] = [
                'val' => $v->pengaturan
                ];
        }
        if($v->kepegawaian != null){
            $data['data']['kepegawaian'][] = [
                'val' => $v->kepegawaian
                ];
        }
        if($v->keuangan != null){
            $data['data']['keuangan'][] = [
                'val' => $v->keuangan
                ];
        }
        if($v->kolekting != null){
            $data['data']['kolekting'][] = [
                'val' => $v->kolekting
                ];
        }
        if($v->presensi != null){
            $data['data']['presensi'][] = [
                'val' => $v->presensi
                ];
        }
        if($v->kolektor != null){
            $data['data']['kolektor'][] = [
                'val' => $v->kolektor
                ];
        }
    }
    
    return($data);
}

public function getcom(){
    $prof = Profile::where(function($query) {
                        if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                            $query->where('id_hc', Auth::user()->id_com)->orWhere('id_com', Auth::user()->id_com);
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })
                    ->get();
    return($prof);
}

public function getusers($act, $id_com){
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $user = UserKolek::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')->select('users.*', 'jabatan.jabatan')
                ->where(function($query) use ($act){
                    if($act =='aktif'){
                        $query->where('users.aktif', '1');
                    }else if($act =='nonaktif'){
                        $query->where('users.aktif', '0');
                    }
                })
                ->where(function($query) use ($id_com){
                    if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                        if($id_com > 0){
                            $query->where('users.id_com', $id_com);
                        }else{
                            $query->whereIn('users.id_com', function($q) {
                                        $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                    });
                        }
                    }else{
                        $query->where('users.id_com', Auth::user()->id_com);
                    }
                })
                // ->where('users.id_com', Auth::user()->id_com)
                ->orderBy('users.id_com')->orderBy('users.name')->get();
        return fractal($user, new UserKarTransformer())->toArray();
    }
}

public function getkars($id_com){
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $kar = Karyawan::leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                ->whereNotIn('karyawan.email', function($query) {$query->select('email')->from('users');})
                ->where('karyawan.aktif', 1)
                ->where(function($query) use ($id_com){
                    if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                        if($id_com > 0){
                            $query->where('karyawan.id_com', $id_com);
                        }else{
                            $query->whereIn('karyawan.id_com', function($q) {
                                        $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                    });
                        }
                    }else{
                        $query->where('karyawan.id_com', Auth::user()->id_com);
                    }
                })
                // ->where('karyawan.id_com', Auth::user()->id_com)
                ->get();
        return fractal($kar, new RwyKarTransformer())->toArray();
    }
}

public function pres_atasans($id_com){
    
    $id_kan = Auth::user()->id_kantor;
    $id_jab = Auth::user()->id_jabatan;
    
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $kar = Karyawan::leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                ->select('karyawan.*', 'jabatan.id', 'jabatan.jabatan')
                ->selectRaw("IF(MONTH(karyawan.tgl_gaji) = MONTH(NOW()) AND YEAR(karyawan.tgl_gaji) = YEAR(NOW()),1,0) AS kondisi")
                ->where(function($q) use ($id_kan, $id_jab) {
                        if(Auth::user()->presensi == 'admin' | Auth::user()->presensi == 'keuangan pusat'){
                            $q->where('karyawan.id_kantor', '!=', 'hahaha');
                        }else if(Auth::user()->presensi == 'kacab' | Auth::user()->presensi == 'keuangan cabang'){
                            $q->where('karyawan.id_kantor', $id_kan)->orWhere('karyawan.kantor_induk', $id_kan);
                        }else{
                            $q->where('karyawan.pr_jabatan', $id_jab)->where('karyawan.id_kantor', $id_kan);
                        }
                    })
                ->where('karyawan.aktif', 1)
                ->where(function($query) use ($id_com){
                    if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                        if($id_com > 0){
                            $query->where('karyawan.id_com', $id_com);
                        }else{
                            $query->whereIn('karyawan.id_com', function($q) {
                                        $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                    });
                        }
                    }else{
                        $query->where('karyawan.id_com', Auth::user()->id_com);
                    }
                })
                // ->where('karyawan.id_com', Auth::user()->id_com)
                ->get();
    }    
    return fractal($kar, new RwyKarTransformer())->toArray();
}

public function jamkers ($shift, $id_com)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $trf = JamKerja::where('shift',$shift)
                ->where(function($query) use ($id_com){
                    if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                        if($id_com > 0){
                            $query->where('id_com', $id_com);
                        }else{
                            $query->whereIn('id_com', function($q) {
                                        $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                    });
                        }
                    }else{
                        $query->where('id_com', Auth::user()->id_com);
                    }
                })
                // ->where('id_com', Auth::user()->id_com)
                ->get();
        return fractal($trf, new JamkerTransformer())->toArray();
    }
}

public function getshifts ($id_com)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $trf = JamKerja::select('shift', 'cek_in', 'cek_out')
                ->where(function($query) use ($id_com){
                    if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                        if($id_com > 0){
                            $query->where('id_com', $id_com);
                        }else{
                            $query->whereIn('id_com', function($q) {
                                        $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                    });
                        }
                    }else{
                        $query->where('id_com', Auth::user()->id_com);
                    }
                })
                // ->where('id_com', Auth::user()->id_com)
                ->distinct()->get();
        $data = [];
        if(count($trf) > 0){
        foreach($trf as $x => $v){
            $data['data'][] = [
                'shift' => $v->shift,
                'shift' => $v->shift,
                'cek_in' => $v->cek_in,
                'cek_out' => $v->cek_out
            ];
        }
            }else{
              $data['data'] = []; 
            }
        return($data);
    }
}

public function shiftusers($shift, $kerja, $id_com){
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $user = UserKolek::leftjoin('jabatan', 'jabatan.id', '=', 'users.id_jabatan')
                ->select('users.*', 'jabatan.jabatan')->where('users.shift', $shift)
                ->where(function($query) use ($kerja){
                    if($kerja != 'all'){
                        $query->where('users.id_kantor', $kerja);
                    }
                })
                ->where(function($query) use ($id_com){
                    if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                        if($id_com > 0){
                            $query->where('users.id_com', $id_com);
                        }else{
                            $query->whereIn('users.id_com', function($q) {
                                        $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                    });
                        }
                    }else{
                        $query->where('users.id_com', Auth::user()->id_com);
                    }
                })
                // ->where('users.id_com', Auth::user()->id_com)
                ->where('users.aktif', 1)
                ->get();
        
        return fractal($user, new UserKarTransformer())->toArray();
    }
}

public function uncekshifts($shift, $kerja, $id_com, Request $request)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        UserKolek::where(function($query) use ($id_com){
                        if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                            if($id_com > 0){
                                $query->where('id_com', $id_com);
                            }else{
                                $query->whereIn('id_com', function($q) {
                                            $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                        });
                            }
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })
                    ->where(function($query) use ($kerja){
                        if($kerja != 'all'){
                            $query->where('id_kantor', $kerja);
                        }
                    })
                    ->where('shift', $shift)
                    ->where('users.aktif', 1)
                    ->update(['up_shift' => 0]);
        
        return response()->json([
            "status"=>"sukses"
            ]);
    }
}

public function cekshifts($shift, $kerja, $id_com, Request $request)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
            UserKolek::where(function($query) use ($id_com){
                            if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                if($id_com > 0){
                                    $query->where('id_com', $id_com);
                                }else{
                                    $query->whereIn('id_com', function($q) {
                                                $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                            });
                                }
                            }else{
                                $query->where('id_com', Auth::user()->id_com);
                            }
                        })
                        ->where(function($query) use ($kerja){
                            if($kerja != 'all'){
                                $query->where('id_kantor', $kerja);
                            }
                        })
                        ->where('shift', $shift)
                        ->where('users.aktif', 1)
                        ->update(['up_shift' => 1]);
                    
        // if($kerja == 'all'){
        //     UserKolek::where('shift', $shift)->where('users.aktif', 1)->where('id_com', Auth::user()->id_com)->update(['up_shift' => 1]);
        // }else{
        //     UserKolek::where('shift', $shift)->where('users.aktif', 1)->where('id_kantor', $kerja)->where('id_com', Auth::user()->id_com)->update(['up_shift' => 1]);
        // }
        return response()->json([
            "status"=>"sukses"
            ]);
    }
}

public function stats_pres($tanggal1, $tanggal2, $id_com)
{
    
    $dari = date('Y-m-d', strtotime($tanggal1));
    $sampai = date('Y-m-d', strtotime($tanggal2));
    $id_kan = Auth::user()->id_kantor;
    $id_jab = Auth::user()->id_jabatan;
    
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
    $data = Presensi::select('status')
                    ->selectRaw("SUM(jumlah) as jumlah")
                    ->where(function($q) use ($id_kan, $id_jab, $dari, $sampai) {
                        if(Auth::user()->presensi == 'kacab'){
                            $q->where('id_kantor', $id_kan)->orWhere('kantor_induk', $id_kan);
                        }else if(Auth::user()->presensi != 'admin'){
                            $q->where('pr_jabatan', $id_jab)->where('id_kantor', $id_kan);
                        }
                    })
                    ->whereDate('created_at','>=',$dari)->whereDate('created_at','<=',$sampai)
                    ->whereIn('id_karyawan', function($query) use ($id_com) {
                            $query->select('id_karyawan')
                                    ->from('karyawan')
                                    ->where(function($query) use ($id_com){
                                        if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                            if($id_com > 0){
                                                $query->where('id_com', $id_com);
                                            }else{
                                                $query->whereIn('id_com', function($q) {
                                                            $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                                        });
                                            }
                                        }else{
                                            $query->where('id_com', Auth::user()->id_com);
                                        }
                                    });
                        })
                    ->groupBy('status')
                    ->get();
    }   
    return fractal($data, new StatPresTransformer())->toArray();
}

public function upshifts($id_com, Request $request)
{
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        UserKolek::where('up_shift', 1)->where('aktif', 1)
                    ->where(function($query) use ($id_com){
                        if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                            if($id_com > 0){
                                $query->where('id_com', $id_com);
                            }else{
                                $query->whereIn('id_com', function($q) {
                                            $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                        });
                            }
                        }else{
                            $query->where('id_com', Auth::user()->id_com);
                        }
                    })
                    ->where('id_com', Auth::user()->id_com)
                    ->update([
                      'shift' => $request->shift,
                      'up_shift' => 0
                    ]);
         \LogActivity::addToLog(Auth::user()->name.' upshift ');
        return response()->json([
            "status"=>"sukses"
            ]);
    }
}

public function getkantors ($id_com)
{
    if(Auth::user()->id_com != null && Auth::user()->aktif == 1 && Auth::user()->presensi != null){
        $trf = Kantor::where(function($query) use ($id_com){
                            if(Auth::user()->presensi == 'admin' && Auth::user()->level_hc == 1){
                                if($id_com > 0){
                                    $query->where('id_com', $id_com);
                                }else{
                                    $query->whereIn('id_com', function($q) {
                                                $q->select('id_com')->from('company')->where('id_hc', Auth::user()->id_com);
                                            });
                                }
                            }else{
                                $query->where('id_com', Auth::user()->id_com);
                            }
                        })->get();
        return fractal($trf, new KantorTransformer())->toArray();
    }
}

public function jenreq()
{
    $v = Jenreq::where('id_com', Auth::user()->id_com)->get(); 
    
    User::findOrFail(Auth::user()->id)->update(['injenreq' => 0]);  
        
    return($v);
}

public function katjenreq()
{
    $v = Jenreq::where('id_com', '!=', null)->distinct('kategori')->get(); 
    
    // User::findOrFail(Auth::user()->id)->update(['injenreq' => 0]);  
        
    return($v);
}

public function getpengumuman(){
    $today = Carbon::today();

    $peng = Pengumuman::where('jenis', '!=', null)
        ->where('id_com', Auth::user()->id_com)
        // ->where('tgl_awal', '<=', $today)
        ->where('tgl_akhir', '>=', $today)
        ->get();
        
    $data = [];
    $id = [];
    $kon = [];
        if(count($peng) > 0){
            foreach($peng as $x => $v){
                $kon[$x] = 0;
                $unkan = unserialize($v->id_kantor);
                $unkar = unserialize($v->id_user);
                
                if($v->peruntukan == 1){
                    if(in_array(Auth::user()->id_kantor, $unkan)){
                        $kon[$x] = 1;
                    }
                }else if($v->peruntukan == 2){
                    if(in_array(Auth::user()->id, $unkar)){
                        $kon[$x] = 1;
                    }
                }
                
                    
                    // $kan = Kantor::find($d);
                    
                    // if(Auth::user()->presensi == 'admin'){
                    //     $kondisi = !in_array($v->id, $id);
                    // }else if(Auth::user()->presensi == 'kacab'){
                    //     $kondisi = ($d == Auth::user()->id_kantor || $kan->kantor_induk == Auth::user()->id_kantor) && !in_array($v->id, $id);
                    // }else{
                    //     $kondisi = $d == Auth::user()->id_kantor && !in_array($v->id, $id);
                    // }
                    
                if($kon[$x] == 1){
                    $id[] = $v->id;
                    $data[] = [
                        'id' => $v->id,
                        'isi' => $v->isi,
                        'jenis' => $v->jenis,
                        'tgl_awal' => $v->tgl_awal,
                        'tgl_akhir' => $v->tgl_akhir,
                        'id_kantor' => unserialize($v->id_kantor),
                        'id_com' => $v->id_com,
                    ];
                }
                
            }
        }
     
    return($data);
}

public function cronbolos(){
    $kar = UserKolek::where('users.id_karyawan', '!=', null)
                        ->where('users.aktif', 1)
                        ->where('users.status_kerja', '!=', 'Agen')
                        ->join('jam_kerja',function($join){
                            $join->on('users.id_com', 'jam_kerja.id_com')
                                 ->on('users.shift', 'jam_kerja.shift')
                                 ->where('jam_kerja.nama_hari', date('l'));
                                //  ->where('jam_kerja.status', 'kerja');
                            })
                        ->whereNotIn('users.id_karyawan', function($que) {
                            $que->select('id_karyawan')->from('presensi')
                                ->whereDate('created_at', date('Y-m-d'));
                            })
                        ->whereNotIn('users.id_karyawan', function($que) {
                            $que->select('id_karyawan')->from('request')
                                ->whereDate('tg_akhir', '>=', date('Y-m-d'))
                                ->where('acc', 1);
                            })
                        // ->where('users.id_com', 1)    
                        ->get();
    $dat = [];
    $kon = []; 
    $lem = [];
    foreach($kar as $x => $v){
    $kon[$x] = 0;  
    $lem[$x] = 0;  
    
    $req = RequestKar::where('id_karyawan', $v->id_karyawan)
                        ->whereDate('tg_akhir', '>=', date('Y-m-d'))
                        ->where('acc','!=', 1)->first();
    
    $accreq = $req != '' ? ( $req->acc == 0 ? '(Request '.$req->status.' Pending) | ' : '(Request '.$req->status.' Ditolak Dengan Alasan : '.$req->alasan.') | ' ) : '';  
    $ketreq = $req != '' ? $req->ket : 'Tidak Melakukan Presensi atau Request';
    $idreq  = $req != '' ? $req->id_request : NULL;
    // return($ketreq);
    $peng = Pengumuman::where('jenis','!=', 'Info')
                        ->whereDate('tgl_awal', '<=', date('Y-m-d'))
                        ->whereDate('tgl_akhir', '>=', date('Y-m-d'))
                        ->where('id_com', $v->id_com)
                        ->get();
        
        if(count($peng) > 0){
            foreach($peng as $a => $b){
                
                $unkan = unserialize($b->id_kantor);
                $unkar = unserialize($b->id_user);
                
                if($b->peruntukan == 1){
                    if(in_array($v->id_kantor, $unkan)){
                        if($b->jenis == 'Lembur'){
                            $lem[$x] = 1;
                        }else{
                            $kon[$x] = 1;    
                        }
                    }
                }else if($b->peruntukan == 2){
                    if(in_array($v->id, $unkar)){
                        if($b->jenis == 'Lembur'){
                            $lem[$x] = 1;
                        }else{
                            $kon[$x] = 1;    
                        }
                    }
                }
            }
        }
        
        if($lem[$x] == 1 || ($kon[$x] == 0 && $v->status == 'Kerja')){
            $data = new Presensi;
            $data->id_karyawan = $v->id_karyawan;
            $data->id_jabatan = $v->id_jabatan;
            $data->pr_jabatan = $v->pr_jabatan;
            $data->id_kantor = $v->id_kantor;
            $data->kantor_induk = $v->kantor_induk;
            $data->nama = $v->name;
            $data->cek_in = date('H:i:s');
            $data->cek_out = date('H:i:s');
            $data->ket = $accreq.$ketreq;
            $data->status = 'Bolos';
            $data->id_reqbolos = $idreq;
            $data->kantor = 0;
            $data->keterlambatan = 0;
            $data->jumlah = 1;
            $data->acc = 1;
            $data->save();
        
        // $dat[] = [
        //     'id' => $v->id,
        //     'id_karyawan' => $v->id_karyawan,
        //     'name' => $v->name,
        //     'id_kantor' => $v->id_kantor,
        //     'id_com' => $v->id_com,
        //     'shift' => $v->shift,
        //     'status' => $v->status,
        //     'id_reqbolos' => $idreq,
        //     'ketreq' => $accreq.$ketreq,
        //     'kon'  => $kon[$x],
        //     'index' => $x
        // ];
        } 
        
    }
    
    return('SUKSES');
}

public function cronbolos2(){
    $pres = Presensi::selectRaw("DISTINCT DATE(created_at) AS tgl")
            // ->whereDate('created_at','>=', '2023-08-17')
            ->whereIn('id_karyawan', function($que) {
                    $que->select('id_karyawan')->from('karyawan')
                        ->where('id_com', 5)->where('aktif', 1);
                    })
            ->get();
    // return($pres);
    foreach($pres as $z => $y){
    $namhar = date('l', strtotime($y->tgl));
    // dd($namhar);
        $kar = UserKolek::where('users.id_karyawan', '!=', null)
                            ->where('users.aktif', 1)
                            // ->where('users.status_kerja', '!=', 'Agen')
                            ->join('jam_kerja',function($join) use($namhar){
                                $join->on('users.id_com', 'jam_kerja.id_com')
                                     ->on('users.shift', 'jam_kerja.shift')
                                     ->where('jam_kerja.nama_hari', $namhar);
                                    //  ->where('jam_kerja.status', 'kerja');
                                })
                            ->whereNotIn('users.id_karyawan', function($que) use($y) {
                                $que->select('id_karyawan')->from('presensi')
                                    ->whereDate('created_at', $y->tgl);
                                })
                            ->whereNotIn('users.id_karyawan', function($que) use($y) {
                                $que->select('id_karyawan')->from('request')
                                    ->whereDate('tg_akhir', '>=', $y->tgl)
                                    ->where('acc', 1);
                                })
                            ->whereDate('users.created_at', '<=', $y->tgl)
                            ->where('users.id_com', 5)    
                            ->get();
        $dat = [];
        $kon = []; 
        $lem = [];
        // return(COUNT($kar));
    // dd($y->tgl);
        foreach($kar as $x => $v){
        $kon[$x] = 0;  
        $lem[$x] = 0;  
        
        $req = RequestKar::where('id_karyawan', $v->id_karyawan)
                            ->whereDate('tg_akhir', '>=', $y->tgl)
                            ->where('acc','!=', 1)->first();
        
        $accreq = $req != '' ? ( $req->acc == 0 ? '(Request '.$req->status.' Pending) | ' : '(Request '.$req->status.' Ditolak Dengan Alasan : '.$req->alasan.') | ' ) : '';  
        $ketreq = $req != '' ? $req->ket : 'Tidak Melakukan Presensi atau Request';
        $idreq  = $req != '' ? $req->id_request : NULL;
        // return($ketreq);
        $peng = Pengumuman::where('jenis','!=', 'Info')
                            ->whereDate('tgl_awal', '<=', $y->tgl)
                            ->whereDate('tgl_akhir', '>=', $y->tgl)
                            ->where('id_com', $v->id_com)
                            ->get();
            
            if(count($peng) > 0){
                foreach($peng as $a => $b){
                    
                    $unkan = unserialize($b->id_kantor);
                    $unkar = unserialize($b->id_user);
                    
                    if($b->peruntukan == 1){
                        if(in_array($v->id_kantor, $unkan)){
                            if($b->jenis == 'Lembur'){
                                $lem[$x] = 1;
                            }else{
                                $kon[$x] = 1;    
                            }
                        }
                    }else if($b->peruntukan == 2){
                        if(in_array($v->id, $unkar)){
                            if($b->jenis == 'Lembur'){
                                $lem[$x] = 1;
                            }else{
                                $kon[$x] = 1;    
                            }
                        }
                    }
                }
            }
            
            if($lem[$x] == 1 || ($kon[$x] == 0 && $v->status == 'Kerja')){
                $data = new Presensi;
                $data->id_karyawan = $v->id_karyawan;
                $data->id_jabatan = $v->id_jabatan;
                $data->pr_jabatan = $v->pr_jabatan;
                $data->id_kantor = $v->id_kantor;
                $data->kantor_induk = $v->kantor_induk;
                $data->nama = $v->name;
                $data->cek_in = '23:23:23';
                $data->cek_out = '23:23:23';
                $data->ket = $accreq.$ketreq.'_';
                $data->status = 'Bolos';
                $data->id_reqbolos = $idreq;
                $data->kantor = 0;
                $data->keterlambatan = 0;
                $data->jumlah = 1;
                $data->acc = 1;
                $data->created_at = $y->tgl;
                $data->save();
            
            // $dat[] = [
            //     'id' => $v->id,
            //     'id_karyawan' => $v->id_karyawan,
            //     'name' => $v->name,
            //     'id_kantor' => $v->id_kantor,
            //     'id_com' => $v->id_com,
            //     'shift' => $v->shift,
            //     'status' => $v->status,
            //     'id_reqbolos' => $idreq,
            //     'ketreq' => $accreq.$ketreq,
            //     'kon'  => $kon[$x],
            //     'index' => $x
            // ];
            } 
            
        }
    }
    
    return('SUKSES');
}

public function accreq2($id, Request $request)
{
    $datareq = RequestKar::find($id);
    $datareq->acc = 1;
    $datareq->alasan = null;
    $datareq->user_update = Auth::user()->id;
    
    if($request->old_status != ''){
        $datareq->old_status = $request->old_status;
        $datareq->up_cuti = $request->up_cuti;
        $datareq->status = 'Cuti';
    }
    
    $datareq->update();

    
    $tanggal = date('Y-m-d', strtotime($request->tanggal));    
    $jam = date('H:i:s', strtotime($datareq->created_at));
    
    if($request->status == 'Pulang Awal'){
        $data = Presensi::find($request->id_presensi);
        $data->id_request = $id;
        $data->cek_out = $jam;
        $data->acc = 1;
    
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Menyetujui Request '.$request->nama.' Pulang Awal');
    }else if($request->status == 'Dinas Luar'){
        $data = Presensi::find($request->id_presensi);
        $data->id_request = $id;
    
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Menyetujui Request '.$request->nama.' Dinas Luar');
    }else if($request->status == 'Perdin' && $request->id_presensi != ''){
        $data = Presensi::find($request->id_presensi);
        $data->id_request = $id;
        $data->cek_out = $jam;
        $data->acc = 1;
    
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Menyetujui Request '.$request->nama.' Perdin');
    }else if($request->status == 'Pindah Shift'){
        
        User::where('id_shift', $datareq->id_shift)->update(['id_shift' => NULL, 'acc_shift' => 1]);
        Presensi::where('id_shift', $datareq->id_shift)->update(['acc_shift' => 1]);
        
        \LogActivity::addToLog(Auth::user()->name.' Menyetujui Request '.$request->nama.' Pindah Shift');
    }else{
        $data = new Presensi;
        $data->id_karyawan = $request->id_karyawan;
        $data->id_jabatan = $request->id_jabatan;
        $data->pr_jabatan = $request->pr_jabatan;
        $data->id_kantor = $request->id_kantor;
        $data->kantor_induk = $request->kantor_induk;
        $data->nama = $request->nama;
        // $data->cek_in = $jam;
        $data->ket = $request->ket;
        $data->status = $request->status;
        $data->jumlah = $request->jumlah;
        $data->created_at = $tanggal . ' ' . $jam ;
        $data->lampiran = $request->lampiran;
        $data->id_req = $id;
        $data->acc = 1;
        
        if($request->status == 'Perdin'){
            $data->foto = $request->foto;
            $data->latitude = $request->latitude;
            $data->longitude = $request->longitude;
        }
        
        $data->save();
        \LogActivity::addToLog(Auth::user()->name.' Menyetujui Request '.$request->nama.' '.$request->status);
    }
    return response()->json(
        [
            "status"=>"sukses",
            // "data"=>$data
        ]
    );
}

public function rekap_target(){
    // date('m', strtotime($tg))
    $tg = date('Y-m-01', strtotime('-1 month', strtotime(date('Y-m-d'))));
    $target = Targets::whereDate('tanggal', $tg)->where('periode','!=', 'tahun')->get();
    
    foreach($target as $x => $v){
    $tg_now = date('Y-m-01');
    
    $tar = Targets::whereDate('tanggal', $tg_now)
            ->where('id_jenis', $v->id_jenis)
            ->where('jenis_target', $v->jenis_target)
            ->where('id_kantor', $v->id_kantor)
            ->where('periode','!=', 'tahun')
            ->get();
        
        
        if(count($tar) == 0){
            $data = new Targets;
            $data->target = $v->target;
            $data->kunjungan = $v->kunjungan;
            $data->transaksi = $v->transaksi;
            $data->minimal = $v->minimal;
            $data->bonus = $v->bonus;
            $data->honor = $v->honor;
            $data->id_jenis = $v->id_jenis;
            // $data->tanggal = $tg_now;
            $data->tanggal = date('Y-m-d');
            $data->status = 2;
            $data->id_kantor = $v->id_kantor;
            $data->jenis_target = $v->jenis_target;
            $data->save();
        } 
        
    }
    return('SUKSES');
}

    public function cronclosing(){
        
        $bln = date("m-Y", strtotime("-1 month", strtotime(date('Y-m-d'))));
        $bulan = date('m', strtotime('01-'.$bln));
        $tahun = date('Y', strtotime('01-'.$bln));;
        
        $bln2 = date("m-Y", strtotime("-1 month", strtotime('01-'.$bln)));
        $bulan2 = date('m', strtotime('01-'.$bln2));
        $tahun2 = date('Y', strtotime('01-'.$bln2));
        
        $inbulan = date("Y-m-t", strtotime('01-'.$bln));
        
        dd($bln, $bln2);
        $cek = SaldoAw::whereMonth('bulan', $bulan)
                            ->whereYear('bulan', $tahun)
                            ->first();
                            
        $union = DB::table('transaksi')
                        ->selectRaw("coa_debet, coa_kredit, jumlah, 0 as nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tanggal, approval AS acc")
                        ->unionAll(DB::table('pengeluaran')
                                        ->selectRaw("coa_kredit, coa_debet, 0 as jumlah, nominal, 0 as nominal_debit, 0 as nominal_kredit, via_input, tgl, acc"))
                        ->unionAll(DB::table('jurnal')
                                ->selectRaw("coa_use, 0 as coa_kredit, 0 as jumlah, 0 as nominal, nominal_debit, nominal_kredit, 0 as via_input, tanggal, acc")
                        );
                
            $query = DB::table('coa as t1')
                    ->selectRaw("t1.*, t1.id as root")
                    
                    ->unionAll(
                        DB::table('b as t0')
                            ->selectRaw("t3.*, t0.root")
                            ->join('coa as t3', 't3.id_parent', '=', 't0.id')
                                
                    );
                     
                
                $saldo = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t.parent,  SUM(sub.jumlah) as debit, SUM(sub.nominal) as kredit, SUM(sub.nominal_kredit) as kredit_s, SUM(sub.nominal_debit) as debit_s")
                            
                        ->withRecursiveExpression('b', $query)
                            
                        ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($bulan, $tahun) {
                            $join->on('sub.coa_debet' ,'=', 't.coa')
                                ->whereMonth('sub.tanggal', $bulan)
                                ->whereYear('sub.tanggal', $tahun)
                                ->where('sub.acc', 1);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                        
                $saldo2 = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t2.saldo_awal, t.parent")
                            
                        ->withRecursiveExpression('b', $query)
                        
                        ->leftjoin('saldo_awal as t2',function($join) use ($bulan2, $tahun2) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                ->whereMonth('t2.bulan', $bulan2)
                                ->whereYear('t2.bulan', $tahun2);
                        })
                            
                        ->groupBy('root')
                        ->get(); 
                
                if($cek != null){
                    $saldo3 = DB::table('b as t')
                            ->selectRaw("root, t.coa, t.grup, t.id_parent, t.coa_parent, t.id, t.level ,t.nama_coa, t2.saldo_awal, t.parent, t2.id AS id_sal, t2.closing")
                                
                            ->withRecursiveExpression('b', $query)
                            
                            ->leftjoin('saldo_awal as t2',function($join) use ($bulan, $tahun) {
                                $join->on('t2.coa' ,'=', 't.coa')
                                    ->whereMonth('t2.bulan', $bulan)
                                    ->whereYear('t2.bulan', $tahun);
                            })
                                
                            ->groupBy('root')
                            ->get(); 
                }        
                        
            $saldoak = [];
            foreach($saldo as $i => $val){
            
                if($saldo2[$i]->coa == $val->coa){
                    $a = $saldo2[$i]->saldo_awal;
                }else{
                    $a = 0;
                }
                
                $id_sal = 0;
                if($cek != null){
                    if($saldo3[$i]->coa == $val->coa){
                        $id_sal = $saldo3[$i]->id_sal > 0 ? $saldo3[$i]->id_sal : 0;
                        $closing = $saldo3[$i]->closing > 0 ? $saldo3[$i]->closing : 0;
                    }else{
                        $id_sal = 0;
                        $closing = 0;
                    }
                }
                
                if($val->parent == 'n'){
                    $saldoak = $a + $val->debit - $val->kredit + $val->debit_s - $val->kredit_s;
                }else{
                    $saldoak = 0;
                }
                
                $input['coa'] = $val->coa;
                $input['saldo_awal'] = $saldoak;
                $input['closing'] = $closing;
                $input['canclos'] = 0;
                $input['tgl_closing'] = date('Y-m-d');
                $input['bulan'] = $inbulan;
                    
            // dd($bln, $bln2, $inbulan);
            
                if($cek != null && $id_sal > 0) {
                    SaldoAw::where('id',$id_sal)->update($input);
                }else{
                    SaldoAw::create($input);
                }
                
            }
            
        return ('SUKSES');
        
    }
    
    public function cronbatalclosing(){
        
        $now    = date('Y-m-d');
        $salnow = SaldoAw::whereDate('bulan', date('Y-m-t'))->first();
        
        $tran   = Transaksi::selectRaw("id, 'tran' AS tab, 'Transaksi' AS ket_tab, tanggal AS tgl, jumlah AS nominal, approval AS acc, via_input, coa_debet, coa_kredit, DATE(created_at) AS dibuat, DATE(updated_at) AS diupdate")
                            ->where(function($q) use ($now){
                                $q->whereDate('created_at', $now)->orWhereDate('updated_at', $now);
                                })
                            ->whereRaw("DATE(tanggal) != DATE(created_at)")
                            ->where(function($q) use ($salnow){
                                if($salnow == null)
                                    $q->whereMonth('tanggal', '!=', date('m'));
                                })
                            ->where('approval', 1);
        $peng   = Pengeluaran::selectRaw("id, 'peng' AS tab, 'Pengeluaran' AS ket_tab, tgl, nominal, acc, via_input, coa_debet, coa_kredit, DATE(created_at) AS dibuat, DATE(updated_at) AS diupdate")
                            ->where(function($q) use ($now){
                                $q->whereDate('created_at', $now)->orWhereDate('updated_at', $now);
                                })
                            ->whereRaw("DATE(tgl) != DATE(created_at)")
                            ->where(function($q) use ($salnow){
                                if($salnow == null)
                                    $q->whereMonth('tgl', '!=', date('m'));
                                })
                            ->where('acc', 1);
        $h_tran = HapusTransaksi::selectRaw("id, 'h_tran' AS tab, 'Hapus Transaksi' AS ket_tab, tanggal AS tgl, jumlah AS nominal, approval AS acc, via_input, coa_debet, coa_kredit, DATE(created_at) AS dibuat, DATE(updated_at) AS diupdate")
                            ->where(function($q) use ($now){
                                $q->whereDate('created_at', $now)->orWhereDate('updated_at', $now);
                                })
                            ->whereRaw("DATE(tanggal) != DATE(created_at)")
                            ->where(function($q) use ($salnow){
                                if($salnow == null)
                                    $q->whereMonth('tanggal', '!=', date('m'));
                                })
                            ->where('approval', 1);
        $h_peng = HapusPengeluaran::selectRaw("id, 'h_peng' AS tab, 'Hapus Pengeluaran' AS ket_tab, tgl, nominal, acc, via_input, coa_debet, coa_kredit, DATE(created_at) AS dibuat, DATE(updated_at) AS diupdate")
                            ->where(function($q) use ($now){
                                $q->whereDate('created_at', $now)->orWhereDate('updated_at', $now);
                                })
                            ->whereRaw("DATE(tgl) != DATE(created_at)")
                            ->where(function($q) use ($salnow){
                                if($salnow == null)
                                    $q->whereMonth('tgl', '!=', date('m'));
                                })
                            ->where('acc', 1); 
                            
        $listup = $tran->unionAll($peng)->unionAll($h_tran)->unionAll($h_peng)->orderByRaw('tgl')->get();
        // return($listup);
        $dat = [];
        if(count($listup) > 0){
            foreach ($listup as $i => $v) {
                $tgl = date('Y-m-t', strtotime($v->tgl));
                
                $upsal = SaldoAw::whereDate('bulan', $tgl)
                                ->where(function($q) use ($v){
                                    $q->where('coa', $v->coa_debet)->orWhere('coa', $v->coa_kredit);
                                    })
                                ->where('closing', 1)
                                // ->get();
                                ->update([ 
                                            'closing' => 0,
                                            'canclos' => 1,
                                        ]);
                                
                // if(count($upsal) > 0){
                //      $dat[] = $upsal;
                // }                
            }
        }
        
        return('SUKSES');
        // return($dat);
    }
    
    public function renlap(Request $request){

        $id_lap = $request->id_lap > 0 ? $request->id_lap : 0;
        $lap    = Laporan::find($id_lap);
        
        if($lap != null){
            $r_umum = Rencana::leftjoin('rencana_bln', 'rencana.id_rb', '=', 'rencana_bln.id')
                    ->select('rencana.*', 'rencana_bln.satuan AS sat_bln')
                    ->where('rencana.id_laporan', $id_lap)->where('rencana.marketing', 0)
                    ->get(); 
            
            $tugas  = [];
            
            foreach($r_umum as $key => $v){
            $sat    = RencanaThn::find($v->sat_bln);
            
                $tugas[]  =   [
                                'id'            => $v->id,
                                'tugas'         => $v->tugas,
                                'deskripsi'     => $v->deskripsi,
                                'bukti'         => $v->bukti,
                                'jam_awal'      => $v->jam_awal,
                                'jam_akhir'     => $v->jam_akhir,
                                'durasi'        => $v->durasi,
                                'target'        => $v->target,
                                'tgl_awal'      => $v->tgl_awal,
                                'tgl_akhir'     => $v->tgl_akhir,
                                'tgl_selesai'   => $v->tgl_selesai,
                                'capaian'       => $v->capaian,
                                'satuan'        => $sat != null ? $sat->rumus : null,
                                'aktif'         => $v->aktif,
                                'acc'           => $v->acc,
                                'alasan'        => $v->alasan,
                                'alasan_r'      => $v->alasan_reject,
                                ];
            }
            
            
            return($tugas);
        }
        
    } 
    
    public function count_tugas(Request $request){
        $id = $request->id_karyawan;
        
        $u = User::where('id_karyawan', $id)->first();
        
        $tgl = date('Y-m-d');
        $tgl_awal = date('Y-m-01', strtotime($tgl));
        $tgl_akhir = date('Y-m-t', strtotime($tgl));
        
        $r_tgs = Rencana::selectRaw("COUNT(IF(DATE(tgl_awal) < '$tgl' AND rencana.id_laporan IS NULL AND (rencana.tgl_tampil < '$tgl' OR rencana.tgl_tampil IS NULL), rencana.id, NULL)) AS lewat, 
                    COUNT(IF(DATE(tgl_awal) >= '$tgl' AND rencana.id_laporan IS NULL, rencana.id, NULL)) AS lanjut")
                // ->join('rencana_bln','rencana.id','=','rencana.id_rb')
                ->whereDate('rencana.tgl_awal', '>=', $tgl_awal)
                ->whereDate('rencana.tgl_awal', '<=', $tgl_akhir)
                ->where('rencana.id_karyawan', $id)
                ->where('rencana.marketing', 0)
                ->orderBy('rencana.id', 'desc')
                ->get();
            
        return $r_tgs;
    }
    
    public function lap_renlap(Request $request){
        // return response()->json($request->all());
        $id = $request->id_karyawan;
        $filt = $request->filt;
        
        $u = User::where('id_karyawan', $id)->first();
        
        $tgl = date('Y-m-d');
        $tgl_m = date('Y-01-01', strtotime($tgl));
        $tgl_a = date('Y-m-t', strtotime($tgl));
        
        $kodisi = '=';
        
         if($filt == 'now') $kondisi = '=';
         if($filt == 'late') $kondisi = '<';
         if($filt == 'next') $kondisi = '>';
         
        // Melakukan join antara tabel rencana dan rencana_bln untuk mendapatkan data yang dibutuhkan
    
       $r_tgs = Rencana::leftJoin('rencana_bln', 'rencana.id_rb', '=', 'rencana_bln.id')
            ->select('rencana.*', 'rencana_bln.satuan AS sat_bln', 'rencana_bln.id_hasil AS id_hasil')
            // ->where(function($que) use ($tgl, $kondisi) {
                // $que->where(function($q) use ($tgl, $kondisi) {
                //     $q->whereDate('rencana.tgl_awal', $kondisi,  $tgl)
                //         // ->whereDate('rencana.tgl_akhir', '>=', $tgl)
                //         // ->orWhereDate('rencana.tgl_akhir', '<', $tgl)
                //         // ->orWhereDate('rencana.tgl_tampil', '=', $tgl)
                //         ->where('rencana.tgl_selesai', null);
                //         // ->where('rencana.id_laporan', null);
                // })
            //     $que->orWhereDate('rencana.tgl_kerja', $tgl)
            //     ->where('in_tambahan', 1);
            // })
            
            ->where(function($que) use ($tgl, $filt, $kondisi) {
                if($filt == 'now'){
                    $que->whereDate('rencana.tgl_awal', $kondisi,  $tgl)
                        ->whereDate('rencana.tgl_akhir', '>=', $tgl)
                        ->where('rencana.id_laporan', null)
                        ->orWhere('rencana.tgl_tampil', $tgl);
                }else if($filt == 'late'){
                        $que->whereDate('rencana.tgl_awal', $kondisi,  $tgl)
                        ->whereDate('rencana.tgl_akhir', '>=', $tgl)
                        ->orWhereDate('rencana.tgl_akhir', '<', $tgl)
                        ->WhereRaw("rencana.tgl_tampil < '$tgl' OR rencana.tgl_tampil IS NULL")
                        ->where('rencana.id_laporan', null);
                }else{
                    $que->whereDate('rencana.tgl_awal', $kondisi,  $tgl)
                    ->whereDate('rencana.tgl_akhir', '>=', $tgl)
                    ->where('rencana.id_laporan',  null);
                }
            })
            
            ->whereColumn('rencana.capaian', '<', 'rencana.target')
            ->whereDate('rencana.tgl_awal', '>=', $tgl_m)
            ->whereDate('rencana.tgl_akhir', '<=', $tgl_a)
            ->where('rencana.id_karyawan', $id)
            ->where('rencana.marketing', 0)
            // ->where('rencana.id_laporan', null)
            ->orderBy('rencana_bln.id_hasil')
            ->orderBy('rencana.id', 'desc');
     
        
        $tgs = $r_tgs->get();
        
        $tugas = [];
        $i = 0;
        
        foreach ($tgs as $key => $v) {
            // Menentukan satuan
            $sat = RencanaThn::find($v->sat_bln);
            
            $buk = [
                'name' => '',
                'size' => 0,
                'type' => '',
                'uri' => ''
            ];
        
            // Mengambil hasil dari RencanaBln berdasarkan id_hasil
            $hResults = RencanaBln::where('id', $v->id_hasil)->get();
        
            // Menyusun data tugas
            $tugas['items'][$i] = [
                'id' => $v->id,
                'tugas' => $v->tugas,
                'deskripsi' => $v->deskripsi,
                'catatan' => $v->catatan,
                'bukti' => $buk,
                'jamMulai' => $v->jam_awal,
                'jamAkhir' => $v->jam_akhir,
                'durasi' => $v->durasi,
                'target' => $v->target,
                'tglTampil' => $v->tgl_tampil,
                'tglAwal' => $v->tgl_awal,
                'tglAkhir' => $v->tgl_akhir,
                'id_laporan' => $v->id_laporan,
                'tglKerja' => $v->tgl_kerja,
                'inTambahan' => $v->in_tambahan,
                'inEdit' => true,
                'jenis' => 'proses',
                'tgl_selesai' => $v->tgl_selesai,
                'capaian' => $v->capaian,
                'satuan' => $sat != null ? $sat->rumus : null,
                'aktif' => $v->aktif,
                'acc' => $v->acc,
                'alasan' => $v->alasan,
                'alasan_r' => $v->alasan_reject,
                'id_hasil' => $v->id_hasil,
                'index' => $i,
            ];
        
            // Menyusun hasil tugas jika ada
            if ($hResults->isNotEmpty()) {
                $tugas['items'][$i]['tugas_hasil'] = $hResults->pluck('tugas')->toArray();
                $tugas['items'][$i]['capaian_hasil'] = $hResults->pluck('capaian')->toArray();
                $tugas['items'][$i]['target_hasil'] = $hResults->pluck('target')->toArray();
            } else {
                $tugas['items'][$i]['tugas_hasil'] = [];
                $tugas['items'][$i]['capaian_hasil'] = [];
                $tugas['items'][$i]['target_hasil'] = [];
            }
        
            $i++;
        }
        
        return $tugas;

    }
    
    public function lap_renlap_save(Request $request){
        
        return response()->json(['test' => $request->deskripsi]);
        $ren    = Rencana::findOrFail($request->id);
        
        $file = $request->file('bukti');

        if($file->isValid()){
            $fileName = $file->getClientOriginalName();
            $upload_path = 'fileSK';
            $file->move($upload_path, $fileName);
            $ren->bukti = $fileName;
        }
        
        $ren->capaian   = $request->capaian;
        $ren->jam_awal   = $request->jamMulai;
        $ren->jam_akhir   = $request->jamSelesai;
        $ren->deskripsi   = $request->deskripsi;
        $ren->link   = $request->link;
        $ren->acc = 1;
        $ren->update();

    }
    
    public function rencananext(Request $req){
        $id     = $req->id_karyawan;
        $u      = User::where('id_karyawan', $id)->first();
        $tgl    = date('Y-m-d');
        $tgl_a  = date('Y-m-t', strtotime($tgl));
        $bln    = date('m', strtotime($tgl));
        $thn    = date('Y', strtotime($tgl));
        
        $r_umum = Rencana::leftjoin('rencana_bln', 'rencana.id_rb', '=', 'rencana_bln.id')
                            ->select('rencana.*', 'rencana_bln.satuan AS sat_bln')
                            ->whereDate('rencana.tgl_awal','>', $tgl)->whereDate('rencana.tgl_awal', '<=', $tgl_a)
                            ->where('in_tambahan', '!=', 1)
                            ->where('rencana.id_karyawan', $id)->where('rencana.marketing', 0) 
                            ->get();
        
        $tugas = [];
        foreach($r_umum as $key => $v){
            $sat    = RencanaThn::find($v->sat_bln);
            $i      = $key;
            if($v->durasi == 'daily' || ($v->durasi == 'range' && $v->id_range == $v->id)){
                $buk = [
                          'name'    => '',
                          'size'    => 0,
                          'type'    => '',
                          'uri'     => ''
                        ];
                $tugas[$i]  =   [
                                'id'            => $v->id,
                                'tugas'         => $v->tugas,
                                'deskripsi'     => $v->deskripsi,
                                'catatan'       => $v->catatan,
                                'bukti'         => $buk,
                                'jamMulai'      => $v->jam_awal,
                                'jamAkhir'      => $v->jam_akhir,
                                'durasi'        => $v->durasi,
                                'target'        => $v->target,
                                'tglAwal'       => $v->tgl_awal,
                                'tglAkhir'      => $v->tgl_akhir,
                                'tglKerja'      => $v->tgl_kerja,
                                'inTambahan'    => $v->in_tambahan,
                                'tgl_selesai'   => $v->tgl_selesai,
                                'capaian'       => $v->capaian,
                                'satuan'        => $sat != null ? $sat->rumus : null,
                                'aktif'         => $v->aktif,
                                'acc'           => $v->acc,
                                'alasan'        => $v->alasan,
                                'alasan_r'      => $v->alasan_reject,
                                ];
            }
        }
        return($tugas);
    }
    
    public function up_nextren(Request $req){
        if(Auth::user()->id_com != null && Auth::user()->presensi != null){
            
            $id = $req->id;
            $ren = Rencana::whereIn('id', $id)->update([
                'tampil' => 'now',
                'tgl_tampil' => date('Y-m-d')
            ]);
            
            // $in_tam = $req->indikator;
            // $tgl_k  = $in_tam != 1 ? null : date('Y-m-d');
            
            // if($in_tam == 0){
            //     $id_ren = $req->id > 0 ? $req->id : 0;
            //     $ren    = Rencana::findOrFail($id_ren);
                
            //     $ren->in_tambahan   = $in_tam;
            //     $ren->tgl_kerja     = $tgl_k;
            //     $ren->update();
            // }else{
            //     // $ar_ren = $req->items != '%5B%5D' ?  json_decode (urldecode($req->items)) : [];
            //     $ar_ren = $req->items;
            //     $uprange    = Rencana::whereIn('id', $ar_ren)
            //                         ->update([
            //                                     'in_tambahan'   => $in_tam,
            //                                     'tgl_kerja'     => $tgl_k,
            //                                 ]);
                // foreach($ar_ren as $i => $v){
                    
                //     $id_ren = $v->id > 0 ? $v->id : 0;
                //     $ren    = Rencana::findOrFail($id_ren);
                //     // if($ren->durasi == 'range'){
                //     //     $uprange    = Rencana::where('id_range', $ren->id_range)
                //     //                 ->update([
                //     //                             'in_tambahan'   => $in_tam,
                //     //                             'tgl_kerja'     => $tgl_k,
                //     //                         ]);
                //     // }else{
                //         $ren->in_tambahan   = $in_tam;
                //         $ren->tgl_kerja     = $tgl_k;
                //         $ren->update();
                //     // }
                // }
            // }
            
            return response()->json(
                [
                    "status"=> "sukses",
                ]
            );
        }
    }  
    public function rencananow(){
        $id     = '0107132202101';
        $u      = User::where('id_karyawan', $id)->first();
        $tgl    = date('Y-m-d');
        $tgl_d  = date('Y-m-01', strtotime($tgl));
        $bln    = date('m', strtotime($tgl));
        $thn    = date('Y', strtotime($tgl));
         
        // $pprog  = Prosp::selectRaw("id_prog, COUNT(IF(ket != 'closing' AND DATE(created_at) = '$tgl', id, NULL)) AS pnw, COUNT(IF(ket = 'closing' AND tgl_fol = '$tgl', id, NULL)) AS cls")
        //                 ->where(function($q) use ($tgl) {
        //                     $q->whereDate('tgl_fol', $tgl)->where('ket', 'closing')
        //                         ->orWhereDate('created_at', $tgl)->where('ket', '!=', 'closing');
        //                 })
        //                 ->where('id_peg', $u->id)
        //                 ->groupBy('id_prog');
        // $idprog = $pprog->pluck('id_prog');
        // return($pprog->get());
                        
        // $tprog  = ProgPerus::where('id_kar', $id)->whereDate('tanggal', $tgl_d)->get();
         
        
        $r_late = Rencana::whereDate('tgl_akhir', '<', $tgl)->where('tgl_selesai', null)
                            ->where('id_karyawan', $id)->where('marketing', 0)
                            ->get();
        $r_umum = Rencana::whereDate('tgl_awal', $tgl)->whereDate('tgl_akhir', '>=', $tgl)
                            ->where('id_karyawan', $id)->where('marketing', 0)
                            ->get();                    
        $r_mrkt = Rencana::whereDate('tgl_awal', $tgl)->whereDate('tgl_akhir', $tgl)
                            ->where('id_karyawan', $id)->where('marketing', 1)
                            ->first();
         
        $r_dana = Targets::whereDate('tanggal', $tgl_d)->where('id_jenis', $id)->where('jenis_target', 'kar')->first();
        
        $min    = $r_dana != null ? $r_dana->minimal : 0;
        $omset  = Transaksi::whereMonth('tanggal', $bln)->whereYear('tanggal', $thn)->where('id_koleks', $u->id)->where('approval', '>', 0)->sum('jumlah');
        $tran   = Transaksi::selectRaw("COUNT(id_donatur) AS knj, COUNT(IF(subtot > $min, id_donatur, NULL)) AS tdm")->whereDate('tanggal', $tgl)->where('id_koleks', $u->id)->where('approval', '>', 0)->first();
        $prosp  = Prosp::selectRaw("COUNT(IF(ket != 'closing' AND DATE(created_at) = '$tgl', id, NULL)) AS pnw, COUNT(IF(ket = 'closing' AND tgl_fol = '$tgl', id, NULL)) AS cls")
                        ->where(function($q) use ($tgl) {
                            $q->whereDate('tgl_fol', $tgl)->orWhereDate('created_at', $tgl);
                        })
                        ->where('id_peg', $u->id)->first();
        $tugas  = [];
        if($r_dana != null){
            $v = $r_dana;
            $tugas[0]   =  [
                            'id'        => 0,
                            'tugas'     => 'Target Dana',
                            'durasi'    => 'daily',
                            'target'    => $v->target,
                            'akhir'     => date('Y-m-t'),
                            'capaian'   => $omset,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 1,
                            'telat'     => 0,
                            'alasan'    => '',
                            ];
        }
        // return($r_late);
        foreach($r_late as $key => $v){
            $i = $r_dana != null ? $key + 1 : $key;
            $tugas[$i]  =   [
                            'id'        => $v->id,
                            'tugas'     => $v->tugas,
                            'durasi'    => $v->durasi,
                            'target'    => $v->target,
                            'akhir'     => $v->tgl_akhir,
                            'capaian'   => $v->capaian,
                            'marketing' => 0,
                            'aktif'     => $v->aktif,
                            'dana'      => 0,
                            'telat'     => 1,
                            'alasan'    => $v->alasan,
                            ];
        }
        
        foreach($r_umum as $key => $v){
            $i = $r_dana != null ? $key + count($r_late) + 1 : $key + count($r_late);
            $tugas[$i]  =   [
                            'id'        => $v->id,
                            'tugas'     => $v->tugas,
                            'durasi'    => $v->durasi,
                            'target'    => $v->target,
                            'akhir'     => $v->tgl_akhir,
                            'capaian'   => $v->capaian,
                            'marketing' => 0,
                            'aktif'     => $v->aktif,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => $v->alasan,
                            ];
        }
        
        if($r_mrkt != null | $tran != null | $prosp != null){
            $v = $r_mrkt;
            $i = $r_dana != null ? count($r_umum) + count($r_late) + 1 : count($r_umum) + count($r_late);
            $tugas[$i] =  [
                            'id'        => $r_mrkt != null ? $v->id : 0,
                            'tugas'     => 'Total Kunjungan',
                            'durasi'    => 'daily',
                            'target'    => $r_mrkt != null ? $v->kunjungan : 0,
                            'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                            'capaian'   => $tran != null ? $tran->knj : 0,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => '',
                            ];
            $tugas[$i+1] = [
                            'id'        => $r_mrkt != null ? $v->id : 0,
                            'tugas'     => 'Transaksi diatas minimal',
                            'durasi'    => 'daily',
                            'target'    => $r_mrkt != null ? $v->transaksi : 0,
                            'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                            'capaian'   => $tran != null ? $tran->tdm : 0,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => '',
                            ];
            $tugas[$i+2] = [
                            'id'        => $r_mrkt != null ? $v->id : 0,
                            'tugas'     => 'Total Penawaran',
                            'durasi'    => 'daily',
                            'target'    => $r_mrkt != null ? $v->penawaran : 0,
                            'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                            'capaian'   => $prosp != null ? $prosp->pnw + $prosp->cls : 0,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => '',
                            ];
            $tugas[$i+3] = [
                            'id'        => $r_mrkt != null ? $v->id : 0,
                            'tugas'     => 'Prospek Closing',
                            'durasi'    => 'daily',
                            'target'    => $r_mrkt != null ? $v->closing : 0,
                            'akhir'     => $r_mrkt != null ? $v->tgl_akhir : date('Y-m-d'),
                            'capaian'   => $prosp != null ? $prosp->cls : 0,
                            'marketing' => 1,
                            'aktif'     => 1,
                            'dana'      => 0,
                            'telat'     => 0,
                            'alasan'    => '',
                            ];
        }
        
        return($tugas);
    }
    
    
    public function getsurat(){
        $surat = Generate::where('id_com', Auth::user()->id_com)->get();
        return($surat);
    }
    
    public function postgol(Request $request,$id){
        $gol = Golongan::where('id_gol', $request->id_gol)->first();
        $kar = Karyawan::where('id_karyawan', $id)->first();
        $date = date('Y-m-d');
    
        $tgl_mk = $request->masa != $kar->masa_kerja ? $request->tgl_sk : $kar->tgl_mk;
        $tgl_gol = $request->id_gol != $kar->id_gol ? $request->tgl_sk : $kar->tgl_gol;
        
        if($request->hasFile('upload_sk')){
            $file_sk = $request->file('upload_sk')->getClientOriginalName();
        }else{
            $file_sk = null;
        }
        if($request->action == 'pangkat'){
          
             if($request->level == 'kacab'){
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
             
            \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Kenaikan Pangkat '.$kar->nama);
        }else if($request->action == 'jabatan'){
            
        if($request->level == 'kacab'){
            $con = RekapJabatan::where('id_karyawan', $id)->whereDate('created_at', $date)->first();
            $jab = Jabatan::where('id', $request->jabatan)->first();
            
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
              'pr_jabatan' => $jab->pr_jabatan,
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
                $data->id_spv = $request->id_spv;
                $data->user_insert = Auth::user()->id;
                $data->user_approve = Auth::user()->id;
                $data->plt = $request->plt == 'on' ? "1" : "0";
                $data->jab_daerah = $request->jab_daerah == 'on' ? "1" : "0";
                $data->acc = 1;
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
            
        }
            \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Kenaikan Jabatan '.$kar->nama);
        }else if($request->action == 'keluarga'){
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
            
            if($request->level == 'kacab'){
                if($con){
                    $data = RekapKeluarga::findOrFail($con->id_rekkel);
                    $data->id_karyawan = $id;
                    $data->nama = $kar->nama;
                    $data->status_nikah = $request->status_nikah;
                    $data->no_kk = $request->no_kk;
                    $data->acc = 2;
                    $data->user_approve = null;
                    if($request->hasFile('scan_kk')){
                        
                        $image = $request->file('scan_kk');
                        $data->scan_kk = $image->getClientOriginalName();
                    }
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
                    }
                    
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
                    if($request->hasFile('scan_kk')){
                        
                        $image = $request->file('scan_kk');
                        $data->scan_kk = $image->getClientOriginalName();
                    }
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
                    }
                    
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
    
    public function perubahankaryawan(Request $request)
   {
       
            $tgl_awal = $request->dari;
            $tgl_akhir = $request->sampai != '' ? $request->sampai : date('Y-m-d');
            $perubahan = $request->perubahan != '' ? $request->perubahan : null;
            $status = $request->status != '' ? $request->status : null;
            
            
                $id_com = $request->com;
                // $id_com = 0;
                $datas = DB::table('rekap_jabatan')
                    ->selectRaw("DATE_FORMAT(rekap_jabatan.created_at,'%Y-%m-%d') as tanggal_buat,karyawan.nama,rekap_jabatan.id_rekjab as id ,rekap_jabatan.user_approve as user_approve,'jabatan' as nama_tabel,rekap_jabatan.acc as acc,rekap_jabatan.id_karyawan as id_karyawan")
                    ->whereIn('rekap_jabatan.id_karyawan', function($query) use ($id_com){
                            $query->select('id_karyawan')
                                    ->from('karyawan')
                                    // ->where('karyawan.id_com', Auth::user()->id_com);
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
                                    // ->where('karyawan.id_com', Auth::user()->id_com);
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
                                    // ->where('karyawan.id_com', Auth::user()->id_com);
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
                                    // ->where('karyawan.id_com', Auth::user()->id_com);
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
            
                // $datas = $data;
            
                if ($status !== null) {
                    $datas = $datas->where('acc', '=', $status);
                }
            
                if ($perubahan !== null) {
                    $datas = $datas->where('nama_tabel', '=', $perubahan);
                }
            
                $datas = $datas
                    ->where('tanggal_buat', '>=', $tgl_awal)
                    ->where('tanggal_buat', '<=', $tgl_akhir);
                    
                // $data = $datas->all();    
                
                $Results = [];
                foreach($datas as $d){
                    $Results[] = [
                                    'tanggal_buat' => $d->tanggal_buat,
                                    'nama' => $d->nama,
                                    'id' => $d->id,
                                    'user_approve' => $d->user_approve,
                                    'nama_tabel' => $d->nama_tabel,
                                    'acc' => $d->acc,
                                    'id_karyawan' => $d->id_karyawan,
                                    'userap' => $d->user_approve != '' ? User::select('users.name')->where('id',$d->user_approve )->first()->name : ''
                                ];
                }
            
            return($Results);
   }
   
    public function list_tim(Request $request){
        $id_kan = Auth::user()->id_kantor;
        $id_jab = Auth::user()->id_jabatan;
        $fil_kan    = $request->id_kan;    
        $tgl_awal   = $request->bln != '' ? date('Y-m-01', strtotime($request->bln.'-01')) : date('Y-m-01');
        $tgl_akhir  = date('Y-m-t', strtotime($tgl_awal));
        
        if(Auth::user()->id_com != null && Auth::user()->presensi != null){
            $kar = Karyawan::leftjoin('jabatan', 'jabatan.id', '=', 'karyawan.jabatan')
                    ->selectRaw("karyawan.*, jabatan.id, jabatan.jabatan")
                    ->where('karyawan.id_com', Auth::user()->id_com)
                    ->where(function($q) use ($id_kan, $id_jab, $fil_kan) {
                            if(Auth::user()->presensi == 'admin' | Auth::user()->presensi == 'keuangan pusat'){
                                if($fil_kan > 0){
                                    $q->where('karyawan.id_kantor', $fil_kan);
                                }else{
                                    $q->where('karyawan.id_kantor', '!=', 'hahaha');
                                }
                            }else if(Auth::user()->presensi == 'kacab' | Auth::user()->presensi == 'keuangan cabang'){
                                if($fil_kan > 0){
                                    $q->where('karyawan.id_kantor', $fil_kan);
                                }else{
                                    $q->where('karyawan.id_kantor', $id_kan)->orWhere('karyawan.kantor_induk', $id_kan);
                                }
                            }else{
                                $q->where('karyawan.pr_jabatan', $id_jab)->where('karyawan.id_kantor', $id_kan);
                            }
                        })
                    ->where('karyawan.aktif', 1)
                    ->get();
        
        foreach($kar as $user){
            $ren = Rencana::selectRaw("COUNT(DISTINCT IF(marketing = 0, tgl_awal, NULL)) AS tugmum, COUNT(DISTINCT IF(marketing = 1, tgl_awal, NULL)) AS tugmar")
                    ->whereDate('tgl_awal', '>=', $tgl_awal)->whereDate('tgl_awal', '<=', $tgl_akhir)
                    ->where('id_karyawan', $user->id_karyawan)->first();
            $data['data'][] = [
                            'id_karyawan' => $user->id_karyawan,
                            'nama' => ucwords($user->nama),
                            'token_notif' => $user->token,
                            'id_jabatan' => $user->id,
                            'id_spv' => $user->id_spv,
                            'plt' => $user->plt,
                            'ttl' => $user->ttl,
                            'tj_pas' => $user->tj_pas,
                            'jab_daerah' => $user->jab_daerah,
                            'unit_kerja' => $user->unit_kerja,
                            'email' => $user->email,
                            'id_kantor' => $user->id_kantor,
                            'jabatan' => $user->jabatan,
                            'tgl_gaji' => $user->tgl_gaji,
                            'status_kerja' => $user->status_kerja,
                            'masa_kerja' => $user->masa_kerja,
                            'id_gol' => $user->id_gol,
                            'no_rek' => $user->no_rek,
                            'status_nikah' => $user->status_nikah,
                            'scan_kk' => $user->scan_kk,
                            'no_kk' => $user->no_kk,
                            'tgl_mk' => $user->tgl_mk,
                            'tgl_gol' => $user->tgl_gol,
                            'file_sk' => $user->file_sk,
                            'war_naik' => $user->war_naik,
                            'id_com' => $user->id_com,
                            'tugmum' => $ren != NULL ? $ren->tugmum : 0,
                            'tugmar' => $ren != NULL ? $ren->tugmar : 0,
                            'foto' => 'https://www.kilauindonesia.org/kilau/upload/'.$user->gambar_identitas
                        ];
        }
        }
        return $data;
    }
    
    public function rencana_pertanggal(Request $request){
        $id_kan = Auth::user()->id_kantor;
        $id_jab = Auth::user()->id_jabatan;
        $id_kar     = $request->id_kar != '' ? $request->id_kar : 0;
        $tgl_awal   = $request->bln != '' ? date('Y-m-01', strtotime($request->bln.'-01')) : date('Y-m-01');
        $tgl_akhir  = date('Y-m-t', strtotime($tgl_awal));
        // return($tgl_akhir);
        if(Auth::user()->id_com != null && Auth::user()->presensi != null){
           
        $kar = Rencana::selectRaw("tgl_awal, COUNT(id) AS total, COUNT(IF(durasi = 'daily', id, NULL)) AS daily, COUNT(IF(durasi = 'range', id, NULL)) AS ranges")
                ->whereDate('tgl_awal', '>=', $tgl_awal)->whereDate('tgl_awal', '<=', $tgl_akhir)
                ->where('id_karyawan', $id_kar)->where('marketing', 0)
                ->groupBy('tgl_awal')->orderBy('tgl_awal', 'asc')->get();
        $data = [];
            foreach($kar as $user){
                $renc   = Rencana::selectRaw("rencana.*, IF(rencana.durasi = 'range', (SELECT ren.tgl_awal FROM rencana AS ren WHERE ren.id = rencana.id_range), rencana.tgl_awal) AS awal_range ")
                                    ->where('tgl_awal', $user->tgl_awal)->where('id_karyawan', $id_kar)->where('marketing', 0)->orderBy('tgl_awal', 'asc')->get();
                $detren = [];
                
                $data['data'][] = [
                                'tanggal'   => $user->tgl_awal,
                                'total'     => $user->total != null ? $user->total : 0,
                                'daily'     => $user->daily != null ? $user->daily : 0,
                                'range'     => $user->ranges != null ? $user->ranges : 0,
                                'detren'    => $renc
                            ];
            }
        return $data;
        }
    }
    
    public function rencana_perkar(Request $request){
        $id_kan = Auth::user()->id_kantor;
        $id_jab = Auth::user()->id_jabatan;
        $id_kar     = $request->id_kar != '' ? $request->id_kar : 0;
        $tgl_awal   = $request->bln != '' ? date('Y-m-01', strtotime($request->bln.'-01')) : date('Y-m-01');
        $tgl_akhir  = date('Y-m-t', strtotime($tgl_awal));
        
        if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        $renc   = Rencana::selectRaw("rencana.*, IF(rencana.durasi = 'range', (SELECT ren.tgl_awal FROM rencana AS ren WHERE ren.id = rencana.id_range), rencana.tgl_awal) AS awal_range ")
                            ->whereDate('tgl_awal', '>=', $tgl_awal)->whereDate('tgl_awal', '<=', $tgl_akhir)
                            ->where('id_karyawan', $id_kar)
                            ->where('marketing', 0)
                            ->orderBy('tgl_awal', 'asc')
                            ->get();
        $data = [];
        
        foreach($renc as $v){
            $tgl = $v->tgl_awal;
            
            if (!isset($data[$tgl])) {
            $data[$tgl] = [
                            'periods' => [],
                            'dots' => [['color' => '#51C9C2']],
                        ];
            }
            
            $data[$tgl]['periods'][] = [
                                        'color'         => '#51C9C2',
                                        'text'          => $v->tugas,
                                        'id'            => $v->id,
                                        'durasi'        => $v->durasi,
                                        'id_rb'         => $v->id_rb,
                                        'tgl_awal'      => $v->tgl_awal,
                                        'tgl_akhir'     => $v->tgl_akhir,
                                        'target'        => $v->target,
                                        'aktif'         => $v->aktif,
                                        'capaian'       => $v->capaian,
                                        'tgl_selesai'   => $v->tgl_selesai,
                                        'acc'           => $v->acc,
                                        'awal_range'    => $v->awal_range,
                                        'catatan'       => $v->catatan,
                                    ];
        }
            
        return $data;
        }
    }
    
    public function rencana_marketing(Request $request){
        $id_kan = Auth::user()->id_kantor;
        $id_jab = Auth::user()->id_jabatan;
        $id_kar     = $request->id_kar != '' ? $request->id_kar : 0;
        $tgl_awal   = $request->bln != '' ? date('Y-m-01', strtotime($request->bln.'-01')) : date('Y-m-01');
        $tgl_akhir  = date('Y-m-t', strtotime($tgl_awal));
        
        if(Auth::user()->id_com != null && Auth::user()->presensi != null){
           
        $kar = Rencana::whereDate('tgl_awal', '>=', $tgl_awal)->whereDate('tgl_awal', '<=', $tgl_akhir)
                        ->where('id_karyawan', $id_kar)->where('marketing', 1)
                        ->get();
        // $data = [];
        // foreach($kar as $user){
        //     $data['data'][] = [
        //                     'tanggal'   => $user->tgl_awal,
        //                     'total'     => $user->total != null ? $user->total : 0,
        //                     'daily'     => $user->daily != null ? $user->daily : 0,
        //                     'range'     => $user->ranges != null ? $user->ranges : 0,
        //                     'detren'    => $renc
        //                 ];
        // }
        return $kar;
        }
    }
    
    public function list_kan(Request $req)
    {
        if(Auth::user()->id_com != null && Auth::user()->aktif == 1 && Auth::user()->presensi != null){
            $id_kan = Auth::user()->id_kantor;
            $bln    = date('Y-m-01', strtotime($req->bln.'-01'));
            
            if(!$req->bln){
                $kan = Kantor::where('id_com', Auth::user()->id_com)
                            ->where(function($q) use ($id_kan) {
                                if(Auth::user()->presensi == 'admin' | Auth::user()->presensi == 'keuangan pusat'){
                                    $q->where('tambahan.id', '!=', 'hahaha');
                                }else if(Auth::user()->presensi == 'kacab' | Auth::user()->presensi == 'keuangan cabang'){
                                    $q->where('tambahan.id', $id_kan)->orWhere('tambahan.kantor_induk', $id_kan);
                                }else{
                                    $q->where('tambahan.id', $id_kan);
                                }
                            })
                            ->get();
            }else{
                $kan = Kantor::leftjoin('rencana_bln', function($join) use ($bln) {
                                    $join->on('tambahan.id', '=', 'rencana_bln.id_kantor') 
                                        ->whereDate('rencana_bln.bulan_akhir', '>=', $bln)
                                        ->whereDate('rencana_bln.bulan', '<=', $bln) 
                                        ->where('rencana_bln.aktif', 1);
                                  })
                                ->selectRaw("tambahan.*, COUNT(IF(rencana_bln.bulan <= '$bln' AND rencana_bln.bulan_akhir >= '$bln', rencana_bln.id, NULL)) AS jumtug")
                                ->where('tambahan.id_com', Auth::user()->id_com)
                                ->where(function($q) use ($id_kan) {
                                    if(Auth::user()->presensi == 'admin' | Auth::user()->presensi == 'keuangan pusat'){
                                        $q->where('tambahan.id', '!=', 'hahaha');
                                    }else if(Auth::user()->presensi == 'kacab' | Auth::user()->presensi == 'keuangan cabang'){
                                        $q->where('tambahan.id', $id_kan)->orWhere('tambahan.kantor_induk', $id_kan);
                                    }else{
                                        $q->where('tambahan.id', $id_kan);
                                    }
                                })
                                ->groupBy('tambahan.id')
                                ->get();
            }
            
            $data = [];                
            // foreach($kan as $user){
            if(!$req->bln && (Auth::user()->presensi == 'admin' | Auth::user()->presensi == 'keuangan pusat')){
                $data['data'][0] = [
                                'id'            => 0,
                                'unit'          => 'Umum',
                                'no_hp'         => 0,
                                'kantor_induk'  => 0,
                                'latitude'      => 0,
                                'longitude'     => 0,
                                'acc_up'        => 0,
                                'jumtug'        => 0,
                                'bln'           => 0
                            ];
            }
            
            foreach($kan as $k => $user){
                $i = !$req->bln && (Auth::user()->presensi == 'admin' | Auth::user()->presensi == 'keuangan pusat') ? $k + 1 : $k;
                
                $data['data'][$i] = [
                                'id'            => $user->id,
                                'unit'          => $user->unit,
                                'no_hp'         => $user->no_hp,
                                'kantor_induk'  => $user->kantor_induk,
                                'latitude'      => $user->latitude,
                                'longitude'     => $user->longitude,
                                'acc_up'        => $user->acc_up,
                                'jumtug'        => !$req->bln ? 0 : $user->jumtug,
                                'bln'           => $req->bln
                            ];
            }
            return $data;
        }
    }
    
    public function lap_rencana(Request $request){
        $id_lap = $request->id_lap > 0 ? $request->id_lap : 0;
        $lap    = Laporan::find($id_lap);
        
        if($lap != null){
            $u      = User::where('id_karyawan', $lap->id_karyawan)->first();
            $tgl    = date('Y-m-d', strtotime($lap->created_at));
            $tgl_d  = date('Y-m-01', strtotime($tgl));
            
            $r_umum = Rencana::where('id_laporan', $id_lap)->where('marketing', 0)->get(); 
            $r_mrkt = Rencana::where('id_laporan', $id_lap)->where('marketing', 1)->whereDate('tgl_awal', $tgl)->first();; 
            $r_dana = Targets::whereDate('tanggal', $tgl_d)->where('id_jenis', $lap->id_karyawan)->where('jenis_target', 'kar')->first();
            
            $min    = $r_dana != null ? $r_dana->minimal : 0;
            $omset  = Transaksi::whereDate('tanggal', '>=', $tgl_d)->whereDate('tanggal', '<=', $tgl)->where('id_koleks', $u->id)->where('approval', '>', 0)->sum('jumlah');
            $tran   = Transaksi::selectRaw("COUNT(id_donatur) AS knj, COUNT(IF(subtot > $min, id_donatur, NULL)) AS tdm")->whereDate('tanggal', $tgl)->where('id_koleks', $u->id)->where('approval', '>', 0)->first();
            $prosp  = Prosp::selectRaw("COUNT(IF(ket != 'closing' AND DATE(created_at) = '$tgl', id, NULL)) AS pnw, COUNT(IF(ket = 'closing' AND tgl_fol = '$tgl', id, NULL)) AS cls")
                            ->where(function($q) use ($tgl) {
                                $q->whereDate('tgl_fol', $tgl)->orWhereDate('created_at', $tgl);
                            })
                            ->where('id_peg', $u->id)->first();
        
            $tugas  = [];
            if($r_dana != null){
                $v = $r_dana;
                $tugas[0]   =  [
                                'id'        => 0,
                                'tugas'     => 'Target Dana',
                                'durasi'    => 'daily',
                                'target'    => $v->target,
                                'awal'      => date('Y-m-t', strtotime($tgl)),
                                'akhir'     => date('Y-m-t', strtotime($tgl)),
                                'selesai'   => date('Y-m-t', strtotime($tgl)),
                                'capaian'   => $omset,
                                'marketing' => 1,
                                'aktif'     => 1,
                                'acc'       => 1,
                                'dana'      => 1,
                                'telat'     => 0,
                                'alasan'    => '',
                                'alasan_r'  => '',
                                ];
            }
            
            foreach($r_umum as $key => $v){
                $i = $r_dana != null ? $key + 1 : $key;
                $tugas[$i]  =   [
                                'id'        => $v->id,
                                'tugas'     => $v->tugas,
                                'durasi'    => $v->durasi,
                                'target'    => $v->target,
                                'awal'      => $v->tgl_awal,
                                'akhir'     => $v->tgl_akhir,
                                'selesai'   => $v->tgl_selesai,
                                'capaian'   => $v->capaian,
                                'jam_mulai' => $v->jam_awal,
                                'jam_selesai'  => $v->jam_akhir,
                                'deskripsi' => $v->deskripsi,
                                'bukti'     => $v->bukti,
                                'marketing' => 0,
                                'aktif'     => $v->aktif,
                                'acc'       => $v->acc,
                                'dana'      => 0,
                                'telat'     => 0,
                                'alasan'    => $v->alasan,
                                'alasan_r'  => $v->alasan_reject,
                                ];
            }
            
            if($r_mrkt != null | $tran != null | $prosp != null){
                $v = $r_mrkt;
                $i = $r_dana != null ? count($r_umum) + 1 : count($r_umum);
                $tugas[$i] =  [
                                'id'        => $r_mrkt != null ? $v->id : 0,
                                'tugas'     => 'Total Kunjungan',
                                'durasi'    => 'daily',
                                'target'    => $r_mrkt != null ? $v->kunjungan : 0,
                                'awal'      => $r_mrkt != null ? $v->tgl_awal : $tgl,
                                'akhir'     => $r_mrkt != null ? $v->tgl_akhir : $tgl,
                                'selesai'   => $r_mrkt != null ? $v->tgl_selesai : $tgl,
                                'capaian'   => $tran != null ? $tran->knj : 0,
                                'marketing' => 1,
                                'aktif'     => 1,
                                'acc'       => 1,
                                'dana'      => 0,
                                'telat'     => 0,
                                'alasan'    => '',
                                'alasan_r'  => '',
                                ];
                $tugas[$i+1] = [
                                'id'        => $r_mrkt != null ? $v->id : 0,
                                'tugas'     => 'Transaksi diatas minimal',
                                'durasi'    => 'daily',
                                'target'    => $r_mrkt != null ? $v->transaksi : 0,
                                'awal'      => $r_mrkt != null ? $v->tgl_awal : $tgl,
                                'akhir'     => $r_mrkt != null ? $v->tgl_akhir : $tgl,
                                'selesai'   => $r_mrkt != null ? $v->tgl_selesai : $tgl,
                                'capaian'   => $tran != null ? $tran->tdm : 0,
                                'marketing' => 1,
                                'aktif'     => 1,
                                'acc'       => 1,
                                'dana'      => 0,
                                'telat'     => 0,
                                'alasan'    => '',
                                'alasan_r'  => '',
                                ];
                $tugas[$i+2] = [
                                'id'        => $r_mrkt != null ? $v->id : 0,
                                'tugas'     => 'Total Penawaran',
                                'durasi'    => 'daily',
                                'target'    => $r_mrkt != null ? $v->penawaran : 0,
                                'awal'      => $r_mrkt != null ? $v->tgl_awal : $tgl,
                                'akhir'     => $r_mrkt != null ? $v->tgl_akhir : $tgl,
                                'selesai'   => $r_mrkt != null ? $v->tgl_selesai : $tgl,
                                'capaian'   => $prosp != null ? $prosp->pnw + $prosp->cls : 0,
                                'marketing' => 1,
                                'aktif'     => 1,
                                'acc'       => 1,
                                'dana'      => 0,
                                'telat'     => 0,
                                'alasan'    => '',
                                'alasan_r'  => '',
                                ];
                $tugas[$i+3] = [
                                'id'        => $r_mrkt != null ? $v->id : 0,
                                'tugas'     => 'Prospek Closing',
                                'durasi'    => 'daily',
                                'target'    => $r_mrkt != null ? $v->closing : 0,
                                'awal'      => $r_mrkt != null ? $v->tgl_awal : $tgl,
                                'akhir'     => $r_mrkt != null ? $v->tgl_akhir : $tgl,
                                'selesai'   => $r_mrkt != null ? $v->tgl_selesai : $tgl,
                                'capaian'   => $prosp != null ? $prosp->cls : 0,
                                'marketing' => 1,
                                'aktif'     => 1,
                                'acc'       => 1,
                                'dana'      => 0,
                                'telat'     => 0,
                                'alasan'    => '',
                                'alasan_r'  => '',
                                ];
            }
            
            return($tugas);
        }
        
    }
    
    public function kon_rencana(Request $req){
        if(Auth::user()->id_com != null && Auth::user()->presensi != null){
            $id_ren = $req->id > 0 ? $req->id : 0;
            $ren    = Rencana::findOrFail($id_ren);
            
            if($ren->id_karyawan == Auth::user()->id_karyawan && Auth::user()->presensi != 'admin'){
                return response()->json(
                    [
                        "status"=> "gagal",
                        "data"  => $ren
                    ]
                );
            }else{
                if($req->jenis == 'status'){
                    $alasan = $req->aktif != 0 ? null : $req->alasan;
                    $user   = $req->aktif == 0 ? Auth::user()->id : null;
                    
                    if($ren->durasi == 'range'){
                        $uprange    = Rencana::where('id_range', $ren->id_range)
                                    ->update([
                                                'aktif'         => $req->aktif,
                                                'alasan'        => $alasan,
                                                'user_aktif'    => $user,
                                            ]);
                    }else{
                        $ren->aktif         = $req->aktif;
                        $ren->alasan        = $alasan;
                        $ren->user_aktif    = $user;
                        $ren->update();
                    }
                }else if($req->jenis == 'konfirmasi'){
                    $alasan = $req->acc != 0 ?  null:  $req->alasan_r;
                    $user   = $req->acc == 0 ? Auth::user()->id : null;
                    if($ren->durasi == 'range'){
                        $uprange    = Rencana::where('id_range', $ren->id_range)
                                    ->update([
                                                'acc'           => $req->acc,
                                                'alasan_reject' => $alasan,
                                                'user_acc'      => $user,
                                            ]);
                    }else{
                        $ren->acc           = $req->acc;
                        $ren->alasan_reject = $alasan;
                        $ren->user_acc      = $user;
                        $ren->update();
                    }
                }
                
                
                return response()->json(
                    [
                        "status"=> "sukses",
                        "data"  => $ren
                    ]
                );
            }
        }
    }
    
    public function up_rencana(Request $req)
    {
        if(Auth::user()->id_com != null && Auth::user()->presensi != null){
            $id_ren = $req->id > 0 ? $req->id : 0;
            $ren    = Rencana::findOrFail($id_ren);
            $user   = Auth::user()->id;
            
            if($ren->durasi == 'range'){
                $ren_range  = Rencana::selectRaw("COUNT(IF(id_laporan > 0, id, NULL)) AS id_lap")->where('id_range', $ren->id_range)->first();
            }
            
            $id_lap = $ren->durasi == 'range' ? $ren_range->id_lap : $ren->id_laporan; 
            
            if($id_lap > 0){
                return response()->json(
                    [
                        "status"=> "gagal",
                        "data"  => $ren
                    ]
                );
            }else{
                if($ren->marketing > 0){
                        $ren->kunjungan     = $req->kunjungan;
                        $ren->transaksi     = $req->transaksi;
                        $ren->penawaran     = $req->penawaran;
                        $ren->closing       = $req->closing;
                        $ren->user_update   = $user;
                        $ren->update();
                        
                    \LogActivity::addToLog(Auth::user()->name.' Mengubah rencana '.$ren->id.' untuk '.$ren->id_karyawan); 
                    
                    return response()->json(
                        [
                            "status"=> "sukses",
                            "data"  => $ren
                        ]
                    );
                    
                }else{
                    if($ren->durasi == 'range'){
                        $uprange    = Rencana::where('id_range', $ren->id_range)
                                    ->update([
                                                'tugas'         => $req->tugas,
                                                'user_update'   => $user,
                                            ]);
                    }else{
                        $ren->tugas         = $req->tugas;
                        $ren->user_update   = $user;
                        $ren->update();
                    }
                    
                    \LogActivity::addToLog(Auth::user()->name.' Mengubah rencana '.$ren->id.' untuk '.$ren->id_karyawan); 
                
                    return response()->json(
                        [
                            "status"=> "sukses",
                            "data"  => $ren
                        ]
                    );
                }
            }
        }
    }
     
    public function del_rencana(Request $req)
    {
        if(Auth::user()->id_com != null && Auth::user()->presensi != null){
            $id_ren = $req->id > 0 ? $req->id : 0;
            $ren    = Rencana::findOrFail($id_ren);
            
            if($ren->durasi == 'range'){
                $ren_range  = Rencana::selectRaw("COUNT(IF(id_laporan > 0, id, NULL)) AS id_lap")->where('id_range', $ren->id_range)->first();
            }
            
            $id_lap = $ren->durasi == 'range' ? $ren_range->id_lap : $ren->id_laporan; 
            
            if($id_lap > 0){
                return response()->json(
                    [
                        "status"=> "gagal",
                        "data"  => $ren
                    ]
                );
            }else{
                \LogActivity::addToLog(Auth::user()->name.' Menghapus rencana '.$ren->tugas.' untuk '.$ren->id_karyawan);
                if($ren->durasi == 'range'){
                    $del_range  = Rencana::where('id_range', $ren->id_range)->delete();
                }else{
                    $ren->delete();
                }
                
                return response()->json(
                    [
                        "status"=> "sukses",
                        "data"  => $ren
                    ]
                );
            }
        }
    }
    
    public function logindev(Request $request, UserKolek $user){
        // $user = UserKolek::where('email', trim(stripslashes($request->email), '"'))->where('aktif', 1)->first();
        // // return response()->json(['email' => $user]);
    
        // if (!$user) {
        //     return response()->json(['error' => 'User tidak ditemukan'], 404);
        // }
    
        // // Login user tanpa verifikasi password
        // Auth::guard('userkolek')->login($user);
        // // $userk = $user->find($user->id);
        // return response()->json([
        //   'berhasil' => $user,
        //   'token' => $user->api_token,
        // ]);

        if(!Auth::guard('userkolek')->attempt(['email' => trim(stripslashes($request->email), '"'), 'password' => trim(stripslashes($request->password), '"'), 'aktif' => 1])){
            return response()->json(['error' => 'Terjadi kesalahan']);
        }else{
            $userk = $user->find(Auth::guard('userkolek')->user()->id);
            if($userk->presensi != null){
                
                $cekdev = Udev::where('token', $request->token)->where('app_name', 'timKita')->first();   
                
                if($cekdev != null){
                    $data = Udev::where('token', $request->token)->where('app_name', 'timKita')->update(['id_user' => $userk->id, 'tgl_in' => date('Y-m-d H:i:s')]);
                }else{
                    $data           = new Udev;
                    $data->id_user  = $userk->id;
                    $data->app_name = 'timKita';
                    $data->token    = $request->token ?? '';
                    $data->tgl_in   = date('Y-m-d H:i:s');
                    $data->save();
                }
                
                \LogActivity::addToLog(Auth::guard('userkolek')->user()->name.' telah Logging Presensi App ');
                return response()->json([
                    'berhasil' => $userk,
                    'token' => $userk->api_token,
                    ]);
            }
            return response()->json(['error' => 'Gagal!']);
        }
    }
    
    public function logoutdev(Request $request){
        if(Auth::user()->id_com != null && Auth::user()->presensi != null){
            $deldev = Udev::where('token', $request->tokdev)->where('app_name', 'timKita')->where('id_user', Auth::user()->id)->delete();   
            
            \LogActivity::addToLog(Auth::user()->name.' telah Logoutg Presensi App ');
            return response()->json([
                'status' => 'SUKSES',
                ]);
            }
    }
    
    public function setbangsat(Request $request){
        $tugas  = $request->bangsat != '%5B%5D' ?  json_decode (urldecode($request->bangsat)) : [];
        $tug    = collect($tugas)->where('id_kantor', $request->id_kantor)->where('aksi','!=','tetap');
        
        if($request->tipe == 'bagian'){
            foreach($tug as $r){
                if($r->aksi == 'hapus'){
                    $cek = RencanaBln::where('id_rt', $r->id)->first();
                    if($cek == null){
                        RencanaThn::findOrFail($r->id)->delete();
                    }
                }else if($r->aksi == 'edit'){
                    RencanaThn::findOrFail($r->id)
                                ->update([
                                            'tugas'         => $r->tugas,
                                            'user_update'   => Auth::user()->id,
                                        ]);
                }else if($r->aksi == 'tambah'){
                    $data = new RencanaThn;
                    $data->tipe         = 'bagian';
                    $data->id_kantor    = $r->id_kantor;
                    $data->tugas        = $r->tugas;
                    $data->user_insert  = Auth::user()->id;
                    $data->id_com       = Auth::user()->id_com;
                    $data->save(); 
                }
            }
        }else{    
            foreach($tug as $r){
                if($r->aksi == 'hapus'){
                    $cek = RencanaBln::where('satuan', $r->id)->first();
                    if($cek == null){
                        RencanaThn::findOrFail($r->id)->delete();
                    }
                }else if($r->aksi == 'edit'){
                    RencanaThn::findOrFail($r->id)
                                ->update([
                                            'tugas'         => $r->tugas,
                                            'rumus'         => $r->rumus,
                                            'jenis_target'  => $r->jenis_target,
                                            'user_update'   => Auth::user()->id,
                                        ]);
                }else if($r->aksi == 'tambah'){
                    $data = new RencanaThn;
                    $data->tipe         = 'satuan';
                    $data->id_kantor    = $r->id_kantor;
                    $data->tugas        = $r->tugas;
                    $data->rumus        = $r->rumus;
                    $data->jenis_target = $r->jenis_target;
                    $data->user_insert  = Auth::user()->id;
                    $data->id_com       = Auth::user()->id_com;
                    $data->save();   
                }
            }
        }
        
        return response()->json([
                'status' => 'SUKSES',
                ]);
    }
    
    public function getbangsat(Request $req){
        $id_kan = $req->id_kantor;
        $tipe   = $req->tipe;
        $jenis  = $req->jenis_target;
        $renkan = RencanaThn::where(function($q) use($id_kan) {
                                    $q->where('id_kantor', $id_kan)
                                        ->orWhere('id_kantor', '0');
                            })
                            ->where(function($q) use($tipe, $jenis) {
                                if($tipe != 'all'){
                                    if($tipe == 'satuan' && $jenis != 'all'){
                                        $q->where('tipe', $tipe)->where('jenis_target', $jenis);
                                    }else{
                                        $q->where('tipe', $tipe);   
                                    }
                                }else{
                                    $q->where('tipe', 'satuan')->where(function($que) use($tipe, $jenis){
                                                                    if($jenis != 'all'){
                                                                        $que->where('jenis_target', $jenis);
                                                                    }
                                                                })
                                        ->orWhere('tipe', 'bagian');
                                } 
                            })
                            ->where('id_com', Auth::user()->id_com)
                            ->get();
        
        return $renkan;
    }
     
    public function getrencanabln(Request $req){
        $jenis  = $req->jenis_target;
        $id_kan = $req->id_kantor;
        $bln    = date('Y-m-01', strtotime($req->bln.'-01'));
        
        $sel    = $jenis == 'proses' ? "rencana_bln.*, (SELECT tugas FROM rencana_thn WHERE rencana_thn.id = rencana_bln.id_rt) AS bagian, (SELECT tugas FROM rencana_thn WHERE rencana_thn.id = rencana_bln.satuan) AS sat, (SELECT tugas FROM rencana_bln AS hasil WHERE hasil.id = rencana_bln.id_hasil) AS thasil" 
                                        : "rencana_bln.*, (SELECT tugas FROM rencana_thn WHERE rencana_thn.id = rencana_bln.id_rt) AS bagian, (SELECT tugas FROM rencana_thn WHERE rencana_thn.id = rencana_bln.satuan) AS sat";
        $data = RencanaBln::selectRaw($sel)
                            ->whereDate('rencana_bln.bulan_akhir', '>=', $bln)
                            ->whereDate('rencana_bln.bulan', '<=', $bln) 
                            ->where('rencana_bln.jenis_target', $jenis)
                            ->where('rencana_bln.aktif', 1)
                            ->where('rencana_bln.id_kantor', $id_kan)
                            ->get();
        
        return $data;
    } 
    
    public function rencanabln_on(Request $req){
        $jenis  = $req->jenis_target;
        $id_kan = $req->id_kantor;
        $bln    = date('Y-m-01', strtotime($req->bln.'-01'));
        
        $sel    = $jenis == 'proses' ? "rencana_bln.*,
                                        (SELECT tugas FROM rencana_thn WHERE rencana_thn.id = rencana_bln.id_rt) AS bagian, 
                                        (SELECT tugas FROM rencana_bln AS hasil WHERE hasil.id = rencana_bln.id_hasil) AS thasil" 
                                        : "rencana_bln.*, (SELECT tugas FROM rencana_thn WHERE rencana_thn.id = rencana_bln.id_rt) AS bagian";
        $getren = RencanaBln::selectRaw($sel)
                            ->whereDate('rencana_bln.bulan_akhir', '>=', $bln)
                            ->whereDate('rencana_bln.bulan', '<=', $bln) 
                            ->where('rencana_bln.jenis_target', $jenis)
                            ->where('rencana_bln.aktif', 1)
                            ->where('rencana_bln.id_kantor', $id_kan)
                            ->where('tgl_selesai', NULL)
                            ->get();
        
        $data = [];
        foreach($getren as $v){
            $totar  = Rencana::whereRaw("rencana.id_rb = $v->id AND rencana.tgl_awal = rencana.tgl_akhir AND rencana.aktif = 1")->sum('target');
            $sat    = RencanaThn::where('rencana_thn.id', $v->satuan)->first();
            if($v->target - $totar > 0){
                $data[] = [
                            'id'            => $v->id,
                            'id_rt'         => $v->id_rt,
                            'id_hasil'      => $v->id_hasil,
                            'bulan_hasil'   => $v->bulan_hasil,
                            'id_kantor'     => $v->id_kantor,
                            'tugas'         => $v->tugas,
                            'bulan'         => $v->bulan,
                            'bulan_akhir'   => $v->bulan_akhir,
                            'target'        => $v->target,
                            'satuan'        => $v->satuan,
                            'jenis_target'  => $v->jenis_target,
                            'aktif'         => $v->aktif,
                            'capaian'       => $v->capaian,
                            'tgl_selesai'   => $v->tgl_selesai,
                            'acc'           => $v->acc,
                            'marketing'     => $v->marketing,
                            'alasan'        => $v->alasan,
                            'alasan_reject' => $v->alasan_reject,
                            'sisa_target'   => $v->target - $totar,
                            'bagian'        => $v->bagian,
                            'sat'           => $sat->tugas,
                            'rumus'         => $sat->rumus,
                            'thasil'        => $v->thasil,
                        ];
            }
        }
        return $data;
    }
    
    
    public function setrencanatgl(Request $req){
        
        if($req->aksi == 'hapus'){
            $id_ren = $req->id > 0 ? $req->id : 0;
            $ren    = Rencana::findOrFail($id_ren);
            
            if($ren->durasi == 'range'){
                $ren_range  = Rencana::selectRaw("COUNT(IF(id_laporan > 0, id, NULL)) AS id_lap")->where('id_range', $ren->id_range)->first();
            }
            
            $id_lap = $ren->durasi == 'range' ? $ren_range->id_lap : $ren->id_laporan; 
            
            if($id_lap > 0){
                return response()->json([ "status"=> "GAGAL" ]);
            }else{
                \LogActivity::addToLog(Auth::user()->name.' Menghapus rencana '.$ren->tugas.' untuk '.$ren->id_karyawan);
                if($ren->durasi == 'range'){
                    $del_range  = Rencana::where('id_range', $ren->id_range)->delete();
                }else{
                    $ren->delete();
                }
                
                return response()->json([ "status"=> "SUKSES" ]);
            }
        }else if($req->aksi == 'edit'){
            $id_ren = $req->id > 0 ? $req->id : 0;
            $ren    = Rencana::findOrFail($id_ren);
            $user   = Auth::user()->id;
            
            if($ren->durasi == 'range'){
                $ren_range  = Rencana::selectRaw("COUNT(IF(id_laporan > 0, id, NULL)) AS id_lap")->where('id_range', $ren->id_range)->first();
            }
            
            $id_lap = $ren->durasi == 'range' ? $ren_range->id_lap : $ren->id_laporan; 
            
            if($id_lap > 0){
                return response()->json(
                    [
                        "status"=> "GAGAL",
                    ]
                );
            }else{
                $dari = date('Y-m-d', strtotime($req->tgl_awal));
                
                if($req->durasi == 'daily'){
                    $sampai = $dari;
                }else{
                    $sampai = date('Y-m-d', strtotime($req->tgl_akhir)) <= $dari ? $dari : date('Y-m-d', strtotime($req->tgl_akhir));
                }
                
                // $durasi = $dari == $sampai ? 'daily' : 'range';
                
                $tanggalAwal = new DateTime($dari);
                $tanggalAkhir = new DateTime($sampai);
                
                $selisihHari = 0;
                $dateList = [];
                
                while ($tanggalAwal <= $tanggalAkhir) {
                    $selisihHari++;
                    $dateList[] = $tanggalAwal->format('Y-m-d');
                    $tanggalAwal->modify('+1 day'); 
                }
                
                $last = 0;
                
                if($req->durasi == 'range'){
                    if($ren->durasi == 'range'){
                        $range = Rencana::where('id_range', $ren->id_range)->get();
                    }else{
                        $range = Rencana::where('id', $ren->id)->get();
                    }
                    
                    if(count($range) > $selisihHari){
                        Rencana::where('id_range', $ren->id_range)->where('id', '>', $range[$selisihHari-1]['id'])->delete();
                    }
                    
                    for($i = 0; $i < $selisihHari; $i++){
                        if(count($range)-1 >= $i){
                            $upren  = Rencana::findOrFail($range[$i]['id'])
                                    ->update([
                                                'id_rb'         => $req->id_proses,
                                                'tugas'         => $req->nama,
                                                'durasi'        => $req->durasi,
                                                'catatan'       => $req->deskripsi,
                                                'tgl_awal'      => $dateList[$i],
                                                'tgl_akhir'     => $sampai,
                                                'target'        => $req->target,
                                                'user_update'   => Auth::user()->id,
                                                'id_range'      => $range[0]['id'],
                                            ]);
                            
                        }else if($i > (count($range)-1)){
                            Rencana::create([
                                            'id_karyawan'   => $req->id_kar,
                                            'id_rb'         => $req->id_proses,
                                            'tugas'         => $req->nama,
                                            'durasi'        => $req->durasi,
                                            'catatan'       => $req->deskripsi,
                                            'tgl_awal'      => $dateList[$i],
                                            'tgl_akhir'     => $sampai,
                                            'target'        => $req->target,
                                            'user_insert'   => Auth::user()->id,
                                            'marketing'     => 0,
                                            'id_range'      => $range[0]['id'],
                                        ]);
                        }
                    }
                    
                }else{
                    if($ren->durasi == 'range'){
                        $range = Rencana::where('id_range', $ren->id_range)->first();
                        
                        Rencana::where('id_range', $ren->id_range)->where('id', '>', $range->id)->delete();
                        
                        $upren  = Rencana::findOrFail($range->id)
                                ->update([
                                            'id_rb'         => $req->id_proses,
                                            'tugas'         => $req->nama,
                                            'durasi'        => $req->durasi,
                                            'catatan'       => $req->deskripsi,
                                            'tgl_awal'      => $dari,
                                            'tgl_akhir'     => $sampai,
                                            'target'        => $req->target,
                                            'user_update'   => Auth::user()->id,
                                            'id_range'      => NULL,
                                        ]);
                    }else{
                        $upren  = Rencana::findOrFail($id_ren)
                                ->update([
                                            'id_rb'         => $req->id_proses,
                                            'tugas'         => $req->nama,
                                            'durasi'        => $req->durasi,
                                            'catatan'       => $req->deskripsi,
                                            'tgl_awal'      => $dari,
                                            'tgl_akhir'     => $sampai,
                                            'target'        => $req->target,
                                            'user_update'   => Auth::user()->id,
                                        ]);
                    }
                }
                
                \LogActivity::addToLog(Auth::user()->name.' Mengubah rencana '.$ren->id.' untuk '.$ren->id_karyawan); 
            
                return response()->json(
                    [
                        "status"=> "SUKSES",
                    ]
                );
            }
            
            return response()->json([
                'status' => 'SUKSES',
                ]); 
        }else if($req->aksi == 'tambah'){
            $dari = date('Y-m-d', strtotime($req->tgl_awal));
            $sampai = date('Y-m-d', strtotime($req->tgl_akhir)) <= $dari ? $dari : date('Y-m-d', strtotime($req->tgl_akhir));
            
            $durasi = $req->durasi;
            
            $tanggalAwal = new DateTime($dari);
            $tanggalAkhir = new DateTime($sampai);
            
            $selisihHari = 0;
            $dateList = [];
            
            while ($tanggalAwal <= $tanggalAkhir) {
                $selisihHari++;
                $dateList[] = $tanggalAwal->format('Y-m-d');
                $tanggalAwal->modify('+1 day'); 
            }
            
            $last = 0; 
            
            for($i = 0; $i < $selisihHari; $i++){
                if($durasi == 'range' && $dateList[$i] == $dari){
                    $get_idr = Rencana::insertGetId([
                                    'id_karyawan'   => $req->id_kar,
                                    'id_rb'         => $req->id_proses,
                                    'tugas'         => $req->nama,
                                    'durasi'        => $durasi,
                                    'catatan'       => $req->deskripsi,
                                    'tgl_awal'      => $dateList[$i],
                                    'tgl_akhir'     => $sampai,
                                    'target'        => $req->target,
                                    'user_insert'   => Auth::user()->id,
                                    'marketing'     => 0,
                                    'id_range'      => null,
                                ]);
                    $last = $get_idr;
                }else{
                    if($durasi == 'range' && $i == 1){
                        Rencana::where('id', $last)->update(['id_range' => $last]);
                    }
                    Rencana::create([
                                    'id_karyawan'   => $req->id_kar,
                                    'id_rb'         => $req->id_proses,
                                    'tugas'         => $req->nama,
                                    'durasi'        => $durasi,
                                    'catatan'       => $req->deskripsi,
                                    'tgl_awal'      => $dateList[$i],
                                    'tgl_akhir'     => $sampai,
                                    'target'        => $req->target,
                                    'user_insert'   => Auth::user()->id,
                                    'marketing'     => 0,
                                    'id_range'      => $durasi == 'daily' ? null : $last,
                                ]);
                }
            }
            
            \LogActivity::addToLog(Auth::user()->name . ' Membuat Rencana untuk ID' . $req->id_kar);
            
            return response()->json([
                'status' => 'SUKSES',
                ]);
        }else{
            return response()->json([
                'status' => 'GAGAL',
                'data'  => $req,
                'aksi'  => $req->aksi
                ]);
        }
        
        
    }
    
    public function setrencanabln(Request $req){
        
        if($req->aksi == 'hapus'){
            $cek = NULL;
            if($req->jenis_target == 'proses'){
                $cek = Rencana::where('id_rb', $req->id)->first();
            }else{
                $cek = RencanaBln::where('id_hasil', $req->id)->first();
            }
            
            if($cek != NULL){
                return response()->json([
                    'status' => 'ADA',
                    ]); 
            }else{
                RencanaBln::findOrFail($req->id)->delete();
                return response()->json([
                    'status' => 'SUKSES',
                    ]);
            }
        }else if($req->aksi == 'edit'){
            $cek = NULL;
            if($req->jenis_target == 'proses'){
                $cek = Rencana::where('id_rb', $req->id)->first();
            }else{
                $cek = RencanaBln::where('id_hasil', $req->id)->first();
            }
            
            if($cek != NULL){
                return response()->json([
                    'status' => 'ADA',
                    ]); 
            }else{
                RencanaBln::findOrFail($req->id)
                            ->update([
                                        'tugas'         => $req->tugas,
                                        'id_rt'         => $req->id_rt,
                                        'bulan'         => date('Y-m-01', strtotime($req->bulan)),
                                        'bulan_akhir'   => date('Y-m-01', strtotime($req->bulan_akhir)),
                                        'target'        => $req->target,
                                        'satuan'        => $req->satuan,
                                        'id_hasil'      => $req->jenis_target == 'proses' ? $req->id_hasil : NULL,
                                        'bulan_hasil'   => $req->jenis_target == 'proses' ? date('Y-m-01', strtotime($req->bulan_hasil)) : NULL,
                                        'user_update'   => Auth::user()->id,
                                    ]);
                return response()->json([
                    'status' => 'SUKSES',
                    ]); 
            }
        }else if($req->aksi == 'tambah'){
            $tug    = $req->data != '%5B%5D' ?  json_decode (urldecode($req->data)) : [];
            foreach($tug as $v){
                $data = new RencanaBln;
                $data->jenis_target = $req->jenis_target;
                $data->id_kantor    = $v->id_kantor;
                $data->tugas        = $v->tugas;
                $data->id_rt        = $v->id_rt;
                $data->bulan        = date('Y-m-01', strtotime($v->bulan));
                $data->bulan_akhir  = date('Y-m-01', strtotime($v->bulan_akhir));
                $data->target       = $v->target;
                $data->satuan       = $v->satuan;
                
                if($req->jenis_target == 'proses'){
                    $data->id_hasil     = $v->id_hasil; 
                    $data->bulan_hasil  = date('Y-m-01', strtotime($v->bulan_hasil));  
                }
                
                $data->user_insert  = Auth::user()->id;
                $data->save(); 
            }
            
            return response()->json([
                'status' => 'SUKSES',
                ]);
        }else{
            return response()->json([
                'status' => 'GAGAL',
                'data'  => $req,
                'aksi'  => $req->aksi
                ]);
        }
        
        
    }
    
    public function cap_omset(Request $req){
        // $id     = '9511232105102';
        $id = $req->id_karyawan;
        $u      = User::where('id_karyawan', $id)->first();
        // $u = Auth::user();
        $tgl    = date('Y-m-d');
        $tgl_d  = date('Y-m-01', strtotime($tgl));
        $tgl_a  = date('Y-m-t', strtotime($tgl));
        
        $omprog = Prog::leftjoin("transaksi",function($join) use ($u, $tgl_d, $tgl_a){
                        $join->on('prog.id_program', '=', 'transaksi.id_program')
                            ->whereDate('transaksi.tanggal', '>=', $tgl_d)
                            ->whereDate('transaksi.tanggal', '<=', $tgl_a)
                            ->where('transaksi.approval', '>', 0)
                            ->where('transaksi.id_koleks', $u->id);
                        })
                        ->selectRaw("prog.id_program, prog.program, SUM(transaksi.jumlah) AS capbulan, SUM(IF(transaksi.tanggal = '$tgl', jumlah, 0)) AS caphari")
                        ->where('parent', 'n')
                        ->where('aktif', 'y')
                        ->groupBy('prog.id_program', 'prog.program')
                        ->get()
                        ;
                        
        $data       = [];  
        $i          = 0;
        $totbulan   = 0;
        $tothari    = 0;
        foreach($omprog as $v){
            $tar    = ProgPerus::whereDate('tanggal', $tgl_d)->where('id_kar', $id)->where('id_program', $v->id_program)->first();
            if($tar != null || $v->capbulan > 0){
            $i          += 1;
            $totbulan   += $v->capbulan;
            $tothari    += $v->caphari;
            $data[]     = [
                            'id_program'    => $v->id_program,
                            'program'       => $v->program,
                            'target'        => $tar != null ? number_format($tar->target, 0, ',' , '.') : 0,
                            'capbulan'      => number_format($v->capbulan, 0, ',' , '.'),
                            'caphari'       => number_format($v->caphari, 0, ',' , '.'),
                            'kontot'        => 0
                            ];
            }
        }   
            $tardan     = Targets::whereDate('tanggal', $tgl_d)->where('id_jenis', $id)->where('jenis_target', 'kar')->first();
            $data[$i]   = [
                            'id_program'    => 0,
                            'program'       => 'Total',
                            'target'        => $tardan != null ? number_format($tardan->target, 0, ',' , '.') : 0,
                            'capbulan'      => number_format($totbulan, 0, ',' , '.'),
                            'caphari'       => number_format($tothari, 0, ',' , '.'),
                            'kontot'        => 1
                            ]; 
        return($data);
    }

    public function cap_closing(Request $req){
        // $id     = '7307292002202';
        $id = $req->id_karyawan;
        $u      = User::where('id_karyawan', $id)->first();
        // $u = Auth::user();
        $tgl    = date('Y-m-d');
        $tgl_d  = date('Y-m-01', strtotime($tgl));
        $tgl_a  = date('Y-m-t', strtotime($tgl));
        
        // $pprog  = Prosp::selectRaw("id_prog, COUNT(IF(ket != 'closing' AND DATE(created_at) = '$tgl', id, NULL)) AS pnw, COUNT(IF(ket = 'closing' AND tgl_fol = '$tgl', id, NULL)) AS cls")
        //                 ->where(function($q) use ($tgl) {
        //                     $q->whereDate('tgl_fol', $tgl)->where('ket', 'closing')
        //                         ->orWhereDate('created_at', $tgl)->where('ket', '!=', 'closing');
        //                 })
        //                 ->where('id_peg', $u->id)
        //                 ->groupBy('id_prog')
        //                 ;
        // $idprog = $pprog->pluck('id_prog');
        // $tprog  = ProgPerus::where('id_kar', $id)->whereIn('id_program', $idprog)->whereDate('tanggal', $tgl_d)->get();
        
        $omprog = Prog::leftjoin("prosp",function($join) use ($u, $tgl_d, $tgl_a){
                        $join->on('prog.id_program', '=', 'prosp.id_prog')
                            ->whereDate('prosp.tgl_fol', '>=', $tgl_d)
                            ->whereDate('prosp.tgl_fol', '<=', $tgl_a)
                            ->where('prosp.ket', 'closing')
                            ->where('prosp.id_peg', $u->id);
                        })
                        ->selectRaw("prog.id_program, prog.program, COUNT(prosp.id) AS capbulan, COUNT(IF(prosp.tgl_fol = '$tgl', id, NULL)) AS caphari")
                        ->where('prog.parent', 'n')
                        ->where('prog.aktif', 'y')
                        ->groupBy('prog.id_program', 'prog.program')
                        ->get()
                        ;
                        
        $data       = [];  
        $i          = 0;
        $totbulan   = 0;
        $tothari    = 0;
        $tottar     = 0;
        foreach($omprog as $v){
            $tar    = ProgPerus::whereDate('tanggal', $tgl_d)->where('id_kar', $id)->where('id_program', $v->id_program)->first();
            if($tar != null || $v->capbulan > 0){
            $i          += 1;
            $totbulan   += $v->capbulan;
            $tothari    += $v->caphari;
            $tottar     += $tar != null ? $tar->closing : 0;
            $data[]     = [
                            'id_program'    => $v->id_program,
                            'program'       => $v->program,
                            'target'        => $tar != null ? $tar->closing : 0,
                            'capbulan'      => $v->capbulan,
                            'caphari'       => $v->caphari,
                            'kontot'        => 0
                            ];
            }
        }   
            $data[$i]   = [
                            'id_program'    => 0,
                            'program'       => 'Total',
                            'target'        => $tottar,
                            'capbulan'      => $totbulan,
                            'caphari'       => $tothari,
                            'kontot'        => 1
                            ]; 
        return($data);
    }
    

    public function get_target(Request $request){
        $kntr = Auth::user()->id_kantor;
        
        if(Auth::user()->level == 'admin' || Auth::user()->level == 'keuangan pusat'){
            $kota = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else if(Auth::user()->level == 'kacab'){
            $kota = Kantor::where('id', $kntr)->orWhere('kantor_induk', $kntr)->get();
        }
        
        // if ($request->ajax()) {
            $id_com = Auth::user()->id_com;
            $kntr   = Auth::user()->id_kantor;
            $lev    = Auth::user()->pengaturan;
            $r_bln  = $request->bln != '' ? $request->bln : date('Y-m');
            $t_bln  = date('Y-m-01', strtotime($r_bln));
            $r_thn  = $request->thn != '' ? $request->thn : date('Y');
            $r_unit = $request->unit;
            $w_prd  = $request->periode == 'tahun' ? "targets.periode = 'tahun'" : "targets.periode = 'bulan'";
            $w_tgl  = $request->periode == 'tahun' ? "YEAR(targets.tanggal) = '$r_thn'" : "DATE(targets.tanggal) = '$t_bln'";
            $w_jns  = $request->jenis == '' ? "targets.jenis_target IS NOT NULL" : "targets.jenis_target = '$request->jenis'";
            $d_tgl  = $request->periode == 'tahun' ? $r_thn.'-01-01' : $t_bln;
            
            $datay   = [];
            
            if($request->jenis == 'kan'){
                $data   = Kantor::selectRaw("'kan' as jenisnyaa ,tambahan.id as idnya, tambahan.unit as jenisnya, targets.*, 0 as pimpinan")
                                ->leftJoin('targets',function($join) use ($w_tgl, $w_prd){
                                            $join->on('targets.id_jenis', 'tambahan.id')
                                                ->whereRaw("$w_tgl AND $w_prd")
                                                ->where('targets.jenis_target', 'kan');
                                            })
                                ->where(function ($query) use ($kntr, $lev) {
                                    if($lev != 'admin' || $lev != 'keuangan pusat') {
                                        $query->where('tambahan.id', $kntr)->orWhere('tambahan.kantor_induk', $kntr);
                                    }
                                })
                                ->where('tambahan.id_com', $id_com)
                                ->groupBy('idnya')
                                ->get();
            }else if($request->jenis == 'kar'){
                $data   = User::selectRaw("'kar' as jenisnyaa, users.id_karyawan as idnya, users.name as jenisnya, targets.*, tambahan.id_pimpinan as pimpinan")
                                ->leftJoin('targets',function($join) use ($w_tgl, $w_prd){
                                            $join->on('targets.id_jenis', 'users.id_karyawan')
                                                ->whereRaw("$w_tgl AND $w_prd")
                                                ->where('targets.jenis_target', 'kar');
                                            })
                                ->join('tambahan','tambahan.id','=','users.id_kantor')
                                ->whereRaw("users.id_com = '$id_com' AND id_karyawan IS NOT NULL AND aktif = '1' AND status_kerja != 'Magang'")
                                ->where(function ($query) use ($kntr, $lev, $r_unit) {
                                    if($r_unit == ''){
                                        if($lev != 'admin' || $lev != 'keuangan pusat') {
                                            $query->where('users.id_kantor', $kntr)->orWhere('users.kantor_induk', $kntr);
                                        }
                                    }else{
                                        $query->where('users.id_kantor', $r_unit);
                                    }
                                })
                                ->groupBy('idnya')
                                ->get();
            }else if($request->jenis == 'prog'){
                $query  = DB::table('prog as t1')
                                ->selectRaw("t1.*, t1.id_program as root")
                                
                                ->unionAll(
                                    DB::table('b as t0')
                                        ->selectRaw("t3.*, t0.root")
                                        ->join('prog as t3', 't3.id_program_parent', '=', 't0.id_program')
                                            
                                );
                $data   = DB::table('b as t')
                                ->selectRaw("'prog' as jenisnyaa, t.parent, t.id_program as idnya, t.program as jenisnya, targets.alasan_tolak, targets.tanggal, 
                                            targets.kunjungan, targets.transaksi,  targets.minimal, targets.honor, targets.bonus, targets.id, SUM(targets.target) as target, 
                                            targets.user_approve, targets.id_kantor, targets.status, 0 as pimpinan")
                                    
                                ->withRecursiveExpression('b', $query)
                                    
                                ->leftJoin('targets',function($join) use ($w_tgl, $w_prd, $r_unit, $lev, $kntr){
                                            $join->on('targets.id_jenis', 't.id_program')
                                                ->whereRaw("$w_tgl AND $w_prd")
                                                ->where('targets.jenis_target', 'prog')
                                                ->where(function ($que) use ($r_unit, $lev, $kntr) {
                                                    if($r_unit == ''){
                                                        if($lev != 'admin' || $lev != 'keuangan pusat') {
                                                            $liskan = Kantor::where('kantor_induk', $kntr)->pluck('id');
                                                            $que->where('targets.id_kantor', $kntr)->orWhereIn('targets.id_kantor', $liskan);
                                                        }
                                                    }else{
                                                        $que->where('targets.id_kantor', $r_unit);
                                                    }
                                                });
                                            })
                                ->groupBy('root')
                                ->get();
            }
            
            $tottar = 0;
            foreach($data as $do){
                $c = User::where('id',$do->user_approve);
                if(count($c->get()) > 0){
                    $ap = $c->first()->name;
                }else{
                    $ap = '';
                }
                
                if($request->jenis == 'prog'){
                    $ppp = $do->parent;
                }else{
                    $ppp = '';
                }
                
                $haha = $do->target == null ? 0 : $do->target;
                
                if($ppp != 'y'){
                    $tottar += $haha;
                }
                // if($request->periode == 'bulan'){
                //     $hehe = $y ==  date('Y-m', strtotime($do->tanggal)) ? $do->status : 3;
                // }else{
                //     $hehe = $yy == date('Y', strtotime($do->tanggal)) ? $do->status : 3;
                // }
                
                
                // if($request->periode == 'bulan'){
                //     $exey = $request->thn == date('Y-m', strtotime($do->tanggal)) ? date('Y-m', strtotime($do->tanggal)) : $y;
                // }else{
                //     $exey = $request->tahun == date('Y', strtotime($do->tanggal)) ? date('Y', strtotime($do->tanggal)) : date('Y');
                // }
                if($do->id > 0){
                    $datay[] = [
                        'id_jenis'      => $do->idnya,
                        'nama'          => $do->jenisnya,
                        'tgl'           => $do->tanggal,
                        'target'        => $haha,
                        
                        'kunjungan'     => $do->kunjungan,
                        'transaksi'     => $do->transaksi,
                        'minimal'       => $do->minimal,
                        'honor'         => $do->honor,
                        'bonus'         => $do->bonus,
                        
                        'status'        => $do->status,
                        'id_targetnya'  => $do->id != null ? $do->id : 0,
                        'alasan'        => $do->alasan_tolak,
                        'jenisnya'      => $do->jenisnyaa,
                        // 'user_approve'  => $ap,
                        'parent'        => $ppp,
                        'pimpinan'      => $do->pimpinan,
                        // 'id_spesial'    =>  $do->idnya,
                        'kontot'        => 0
                    ];
                }else{
                    $datay[] = [
                        'id_jenis'      => $do->idnya,
                        'nama'          => $do->jenisnya,
                        'tgl'           => $d_tgl,
                        'target'        => 0,
                        
                        'kunjungan'     => 0,
                        'transaksi'     => 0,
                        'minimal'       => 0,
                        'honor'         => 0,
                        'bonus'         => 0,
                        
                        'status'        => 2,
                        'id_targetnya'  => 0,
                        'alasan'        => null,
                        'jenisnya'      => $do->jenisnyaa,
                        'parent'        => $ppp,
                        'pimpinan'      => $do->pimpinan,
                        'kontot'        => 0
                    ];
                }
            }
            
            $datay[count($data)] = [
                    // 'idnya'         => 0,
                    'id_jenis'      => 0,
                    'nama'          => 'Total',
                    'tgl'           => $d_tgl,
                    'target'        => $tottar,
                    
                    'kunjungan'     => $do->kunjungan,
                    'transaksi'     => $do->transaksi,
                    'minimal'       => $do->minimal,
                    'honor'         => $do->honor,
                    'bonus'         => $do->bonus,
                        
                    // 'kunjungan' => $y ==  date('Y-m', strtotime($do->tanggal)) ? $do->kunjungan : 0,
                    // 'transaksi' => $y ==  date('Y-m', strtotime($do->tanggal)) ? $do->transaksi : 0,
                    // 'minimal' => $y ==  date('Y-m', strtotime($do->tanggal)) ? $do->minimal : 0,
                    // 'honor' => $y ==  date('Y-m', strtotime($do->tanggal)) ? $do->honor : 0,
                    // 'bonus' => $y ==  date('Y-m', strtotime($do->tanggal)) ? $do->bonus : 0,
                    // 'stts' => $hehe,
                    
                    'status'        => 1,
                    'id_targetnya'  => 0,
                    'alasan'        => '',
                    'jenisnya'      => '',
                    'user_approve'  => '',
                    'parent'        => $ppp,
                    'pimpinan'      => '',
                    'id_spesial'    => '',
                    'kontot'        => 1
                ];
            
            
            return($datay);
    }
    
    public function get_tarperbulan(Request $request){
        $tahun  = $request->thn == '' ? date('Y') : $request->thn;
        $find   = Targets::whereRaw("YEAR(tanggal) = '$tahun' AND periode = 'bulan' AND id_jenis = '$request->id_jenis' AND jenis_target = '$request->jenis'")->get();
        $thn    = Targets::whereRaw("YEAR(tanggal) = '$tahun' AND periode = 'tahun' AND id_jenis = '$request->id_jenis' AND jenis_target = '$request->jenis'")->first(); 
        $parah  = [];
        
        if($thn != null){
            $datahun    = [
                            'id_targetnya' => $thn->id,
                            'tgl'       => date('Y-01-01', strtotime($thn->tanggal)),
                            'target'    => strval($thn->target),
                            'id_jenis'  => $request->id_jenis,
                            'jenisnya'  => $thn->jenis_target,
                            ];
        }else{
            $datahun    = [
                            'id_targetnya'  => 0,
                            'tgl'       => $tahun.'-01-01',
                            'target'    => 0,
                            'id_jenis'  => $request->id_jenis,
                            'jenisnya'  => $request->jenis,
                            ];
        }
        for ($i = 0; $i < 12; $i++) {
            $b      = $i+1;
            $bulan  = $b < 10 ? '0'.$b : $b;
            $parah[$i] = [
                            'id'        => 0,
                            'bulan'     => $bulan,
                            'tahbul'    => $tahun.'-'.$bulan,
                            'target'    => 0
                        ];
        }
        
        foreach ($find as $target) {
            $bln    = date('n', strtotime($target->tanggal));
            // $b      = $bln;
            $bulan  = $bln < 10 ? '0'.$bln : $bln;
            
            $parah[$bln - 1] = [
                                'id'        => $target->id,
                                'bulan'     => $bulan,
                                'tahbul'    => $tahun.'-'.$bulan,
                                'target'    => $target->target
                                ];
        }
        
        // return $parah;
        return response()->json([
                                'tahun' => $datahun,
                                'bulan' => $parah,
                            ]);
    }
    
    public function acc_target(Request $request){
        $tolak = $request->acc == 0 ? $request->alasan : null;
        Targets::where('id', $request->id)->update(['status' => $request->acc, 'alasan_tolak' => $tolak, 'user_update' => Auth::user()->id, 'user_approve' => Auth::user()->id]);
        return response()->json([
                                'status' => 'sukses',
                                ]);
    }
    
    public function get_progser(Request $request){
        $u      = User::where('id_karyawan', $request->id_karyawan)->first();
        $r_bln  = $request->bln != '' ? $request->bln : date('Y-m');
        $t_bln  = date('Y-m-01', strtotime($r_bln));
        
        $d_prog = Prog::selectRaw("prog.id_program, prog.program, targets.*")
                        ->where('parent','!=', 'y')
                        ->leftjoin('targets', function($join) use ($t_bln, $u) {
                            $join->on('prog.id_program', '=', 'targets.id_jenis')
                                ->whereDate('targets.tanggal', $t_bln)
                                ->where('targets.id_kantor', $u->id_kantor)
                                ->where('targets.jenis_target', 'prog');
                        })
                        ->orderBy('prog.id_program','asc')
                        ->get();
        
        $data   = [];
        $tottar = 0;
        $t_star = 0;
        $t_tgt  = 0;
        $t_pnw  = 0;
        $t_flw  = 0;
        $t_cls  = 0;
        foreach($d_prog as $i => $v){
            $p_tar  = ProgPerus::selectRaw("SUM(target) as jumlah")
                                ->whereDate('tanggal', $t_bln)
                                ->where('id_program', $v->id_program)
                                ->where('id_kantor', $u->id_kantor)
                                ->first();
            $u_tar  = ProgPerus::where('id_kar', $u->id_karyawan)
                                ->whereDate('tanggal', $t_bln)
                                ->where('id_program', $v->id_program)
                                ->where('id_kantor', $u->id_kantor)
                                ->first();
                                
            $s_tar  = $v->target - ($p_tar != null ? $p_tar->jumlah : 0); 
            if($u_tar == null){
                $tgt    = 0;
                $pnw    = 0;
                $flw    = 0;
                $cls    = 0;
            }else{
                $tgt    = $u_tar->target;
                $pnw    = $u_tar->penawaran;
                $flw    = $u_tar->followup;
                $cls    = $u_tar->closing;
            }
            
            $data[] = [
                        'id_program'    => $v->id_program,
                        'id_target'     => $v->id != null ? $v->id : 0,
                        'id_jenis'      => $v->id_jenis != null ? $v->id_jenis : $v->id_program,
                        'tanggal'       => $v->tanggal != null ? $v->tanggal : $t_bln,
                        'program'       => $v->program,
                        'total_target'  => $v->target > 0 ? $v->target : 0,
                        'sisa_target'   => $s_tar,
                        'target'        => $tgt > 0 ? $tgt : 0,
                        'penawaran'     => $pnw > 0 ? $pnw : 0,
                        'followup'      => $flw > 0 ? $flw : 0,
                        'closing'       => $cls > 0 ? $cls : 0,
                        'kontot'        => 0,
                        'koncul'        => $tgt > 0 ? 1 : 0,
                        ];
            
            $tottar += $v->target;
            $t_star += $s_tar;
            $t_tgt  += $tgt;
            $t_pnw  += $pnw;
            $t_flw  += $flw;
            $t_cls  += $cls;
        }
        $data[count($d_prog)] = [
                        'id_program'    => 0,
                        'id_target'     => 0,
                        'id_jenis'      => 0,
                        'tanggal'       => $t_bln,
                        'program'       => 'Total',
                        'total_target'  => $tottar > 0 ? $tottar : 0,
                        'sisa_target'   => $t_star,
                        'target'        => $t_tgt > 0 ? $t_tgt : 0,
                        'penawaran'     => $t_pnw > 0 ? $t_pnw : 0,
                        'followup'      => $t_flw > 0 ? $t_flw : 0,
                        'closing'       => $t_cls > 0 ? $t_cls : 0,
                        'kontot'        => 1,
                        'koncul'        => 1,
                        ];
        
        return $data;
    }
    
    public function set_target(Request $request){
        
        $dat  = $request->datagabungan != '%5B%5D' ?  json_decode (urldecode($request->datagabungan)) : [];
        
        // Tahun
        $c_tar  = null;
        if($dat->id_targetnya <= 0){
            $c_tar  = Targets::where('id_jenis', $dat->id_jenis)
                                ->where('periode', 'tahun')
                                ->whereDate('tanggal', $dat->tgl)
                                ->where('jenis_target', $dat->jenisnya)
                                ->first();
        }
        
        if($dat->id_targetnya > 0 || $c_tar != null){
            $id_tar             = $dat->id_targetnya > 0 ? $dat->id_targetnya : $c_tar->id;
            $tar                = Targets::find($id_tar);
            $tar->target        = $dat->target;
            $tar->user_update   = Auth::user()->id;
            $tar->update();
        }else{
            $tar                = new Targets;
            $tar->periode       = 'tahun';
            $tar->tanggal       = $dat->tgl;
            $tar->id_jenis      = $dat->id_jenis;
            $tar->target        = $dat->target;
            $tar->status        = 2;
            $tar->jenis_target  = $dat->jenisnya;
            $tar->id_kantor     = $dat->id_jenis;
            $tar->user_insert   = Auth::user()->id;
            $tar->save();
        }
        
        // Bulan
        foreach($dat->bulan as $i => $v){
            $c_tar  = null;
            if($v->id <= 0){
                $c_tar  = Targets::where('id_jenis', $dat->id_jenis)
                                    ->where('periode', 'bulan')
                                    ->whereDate('tanggal', $v->tahbul.'-01')
                                    ->where('jenis_target', $dat->jenisnya)
                                    ->first();
            }
            
            if($v->id > 0 || $c_tar != null){
                $id_tar             = $v->id > 0 ? $v->id : $c_tar->id;
                $tar                = Targets::find($id_tar);
                $tar->target        = $v->target;
                $tar->user_update   = Auth::user()->id;
                $tar->update();
            }else{
                $tar                = new Targets;
                $tar->periode       = 'bulan';
                $tar->tanggal       = $v->tahbul.'-01';
                $tar->id_jenis      = $dat->id_jenis;
                $tar->target        = $v->target;
                $tar->status        = 2;
                $tar->jenis_target  = $dat->jenisnya;
                $tar->id_kantor     = $dat->id_jenis;
                $tar->user_insert   = Auth::user()->id;
                $tar->save();
            }
        }
        
        return response()->json([
                                'status'    => 'sukses',
                                ]);
    }
    
    public function set_tarprog(Request $request){
        $dat    = $request;
        $unit   = $dat->unit == '' ? Auth::user()->id_kantor : $dat->unit;
        $c_tar  = null;
            
        if($dat->id_targetnya <= 0){
            $c_tar  = Targets::where('id_jenis', $dat->id_jenis)
                                ->whereDate('tanggal', $dat->tgl)
                                ->where('id_kantor', $unit)
                                ->first();
        }
        
        if($dat->id_targetnya > 0 || $c_tar != null){
            $id_tar             = $dat->id_targetnya > 0 ? $dat->id_targetnya : $c_tar->id;
            $tar                = Targets::find($id_tar);
            $tar->target        = $dat->target;
            $tar->user_update   = Auth::user()->id;
            $tar->update();
        }else{
            $tar                = new Targets;
            $tar->periode       = 'bulan';
            $tar->tanggal       = $dat->tgl;
            $tar->id_jenis      = $dat->id_jenis;
            $tar->target        = $dat->target;
            $tar->status        = 2;
            $tar->jenis_target  = $dat->jenisnya;
            $tar->id_kantor     = $unit;
            $tar->user_insert   = Auth::user()->id;
            $tar->save();
        }
        
        return response()->json([
                                'status'    => 'sukses',
                                ]);
    }
    
    public function set_tarkar(Request $request){
        $r_bln  = $request->tgl != '' ? $request->tgl : date('Y-m-01');
        $t_bln  = date('Y-m-01', strtotime($r_bln));
        $u      = User::where('id_karyawan', $request->id_jenis)->first();
        // return $request;
        $dat    = $request;
        $c_tar  = null;
            
        if($dat->id_targetnya <= 0){
            $c_tar  = Targets::where('id_jenis', $u->id_karyawan)
                                ->whereDate('tanggal', $t_bln)
                                ->first();
        }
            
        if($dat->id_targetnya > 0 || $c_tar != null){
            $id_tar             = $dat->id_targetnya > 0 ? $dat->id_targetnya : $c_tar->id;
            $tar                = Targets::find($id_tar);
            $tar->target        = $dat->target;
            $tar->kunjungan     = $dat->kunjungan;
            $tar->transaksi     = $dat->transaksi;
            $tar->minimal       = $dat->minimal;
            $tar->honor         = $dat->honor;
            $tar->bonus         = $dat->bonus;
            $tar->user_update   = Auth::user()->id;
            $tar->update();
        }else{
            $tar                = new Targets;
            $tar->periode       = 'bulan';
            $tar->tanggal       = $t_bln;
            $tar->id_jenis      = $u->id_karyawan;
            $tar->target        = $dat->target;
            $tar->kunjungan     = $dat->kunjungan;
            $tar->transaksi     = $dat->transaksi;
            $tar->minimal       = $dat->minimal;
            $tar->honor         = $dat->honor;
            $tar->bonus         = $dat->bonus;
            $tar->status        = 2;
            $tar->jenis_target  = 'kar';
            $tar->id_kantor     = $u->id_kantor;
            $tar->user_insert   = Auth::user()->id;
            $tar->save();
        }
        
        return response()->json([
                                'status'    => 'sukses',
                                ]);
    }

    public function set_progser(Request $request){
        $dat    = $request->dataprog != '%5B%5D' ?  json_decode (urldecode($request->dataprog)) : [];
        $u      = User::where('id_karyawan', $request->id_karyawan)->first();
        
                                
        foreach($dat as $i => $v){
            $c_tar  = null;
            
            if($v->id_target <= 0){
                $c_tar  = ProgPerus::where('id_kar', $request->id_karyawan)
                                    ->whereDate('tanggal', $v->tanggal)
                                    ->where('id_program', $v->id_program)
                                    ->first();
            }
            
            if($v->id_target > 0 || $c_tar != null){
                $id_tar             = $v->id_target > 0 ? $v->id_target : $c_tar->id;
                $tar                = ProgPerus::find($id_tar);
                $tar->target        = $v->target;
                $tar->penawaran     = $v->penawaran;
                $tar->followup      = $v->followup;
                $tar->closing       = $v->closing;
                $tar->user_update   = Auth::user()->id;
                $tar->update();
            }else{
                $tar                = new ProgPerus;
                $tar->id_target     = $request->id_targetnya;
                $tar->id_kar        = $request->id_karyawan;
                $tar->id_program    = $v->id_program;
                $tar->id_kantor     = $u->id_kantor;
                $tar->target        = $v->target;
                $tar->penawaran     = $v->penawaran;
                $tar->followup      = $v->followup;
                $tar->closing       = $v->closing;
                $tar->tanggal       = $v->tanggal;
                $tar->user_insert   = Auth::user()->id;
                $tar->save();
            }
        }
        
        return response()->json([
                                'status'    => 'sukses',
                                ]);
    }
    
public function get_gaji(Request $req){
    // new
    $id     = $req->id_kar;
    $today  = new DateTime("today");
    $day    = date('d'); 
    $tg     = date('Y-m-01', strtotime(date('Y-m-d')));
    $tj     = Tunjangan::where('id_com', Auth::user()->id_com)->first();
    $kar    = Karyawan::selectRaw("karyawan.*, 
                                IF('$day' >= 25, 
                                    IF(karyawan.tgl_gaji >= DATE(NOW()),1,0), 
                                    IF(MONTH(karyawan.tgl_gaji) = MONTH(NOW()) AND YEAR(karyawan.tgl_gaji) = YEAR(NOW()),1,0)
                                    ) AS kondisi
                                ")
                        ->where('id_karyawan', $id)->where('id_com', Auth::user()->id_com)->first();
                        
    $ske    = User::where('id_karyawan', $id)->where('id_com', Auth::user()->id_com)->first();
    
    $kon_skema   = $ske->skema_gaji == 0 ? 1 : $ske->skema_gaji;
    
    $skema  = !$req->id_skema ? $kon_skema : $req->id_skema;

    $komp   = KomponenGaji::join('komponen_gj', 'komponen_gaji.id_komponen', '=', 'komponen_gj.id')
                            ->select('komponen_gaji.*', 'komponen_gj.nama', 'komponen_gj.modal', 'komponen_gj.grup')
                            ->where('komponen_gaji.id_skema', $skema)
                            ->where('komponen_gaji.aktif', 1)
                            ->orderBy('komponen_gj.urutan');
                            
    $arkom  = $komp->pluck('modal')->toArray();
    
    // data_utama
    $tgl_gaji   = $kar->tgl_gaji;
    $month = date("m",strtotime($tgl_gaji));
    $year = date("Y",strtotime($tgl_gaji));
    $gol        = $kar->status_kerja == 'Magang' ? 'IA' : $kar->golongan;
    $datkar     = Karyawan::leftjoin('gapok', 'karyawan.masa_kerja', '=', 'gapok.th')->leftjoin('jabatan', 'karyawan.jabatan', '=', 'jabatan.id')
                ->leftjoin('daerah', 'karyawan.id_daerah', '=', 'daerah.id_daerah')
                ->leftjoin('presensi', 'karyawan.id_karyawan', '=', 'presensi.id_karyawan')
                ->leftjoin("set_terlambat",function($join) use ($id){
                            $join->on("presensi.keterlambatan",">=","set_terlambat.awal")
                                 ->on("presensi.keterlambatan","<=","set_terlambat.akhir")
                                 ->where('presensi.id_karyawan', $id);
                            })
                ->select(\DB::raw("karyawan.nama, gapok.$gol, jabatan.tj_fungsional, jabatan.tj_jabatan, daerah.tj_daerah, daerah.umk, jabatan.tj_training, jabatan.kon_tj_plt, jabatan.tj_plt,
                                SUM(IF(presensi.status = 'Hadir' AND MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji') 
                                OR presensi.status = 'Terlambat' AND MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji') 
                                , presensi.jumlah,0)) AS jumlah,
                                SUM(IF(MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji'), set_terlambat.potongan, 0)) AS potongan"))
                ->groupBy('karyawan.nama','gapok.'.$gol, 'jabatan.tj_jabatan', 'jabatan.tj_training', 'daerah.tj_daerah', 'jabatan.tj_fungsional', 'jabatan.tj_training', 'jabatan.kon_tj_plt', 'jabatan.tj_plt')
                ->where('karyawan.id_karyawan',$id)->first();
    
    // return $datkar;
    
    // keluarga
    $tot_pasangan   = 0;
    $tot_anak       = 0;
    if($kar->tj_pas != 1){
        if($kar->status_nikah == 'Menikah' && $kar->nm_pasangan != null){
            $istri = unserialize($kar->nm_pasangan);
            foreach($istri as $key => $value){
                $tot_pasangan += 1;
            }
        }
        
        if($kar->nm_anak != null){
            $anak   = unserialize($kar->nm_anak); 
            $tgl    = unserialize($kar->tgl_lahir_anak);
            $sts    = unserialize($kar->status_anak);
            foreach($anak as $key => $value){
                $tt = new DateTime($tgl[$key]);
                if($today->diff($tt)->y <= 21 && $sts[$key] == 'Belum Menikah'){
                    $tot_anak += 1;
                }
            }
        }
    }
    
    $totkel = $tot_anak + $tot_pasangan + 1;
    
    // fungsional
    $tjfung = ($kar->plt == 1 ? $datkar->tj_jabatan * ($datkar->tj_plt/100) : $datkar->tj_jabatan) + $datkar->tj_fungsional;
    // transport
    $honor  = 0;
    $trans  = $kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor :  $datkar->jumlah * $tj->tj_transport;
    
    // potongan rekdis
    $rekdis = Rekdis::selectRaw("id_karyawan, COUNT(IF(stat = 1, tanggal, NULL)) AS lap, COUNT(IF(stat = 2, tanggal, NULL)) AS pul, COUNT(IF(stat = 3, tanggal, NULL)) AS lappul")
            ->where('id_karyawan', $kar->id_karyawan)
            ->groupBy('id_karyawan')->first();
            
    if($rekdis == null){        
    $rekdis = [
                'id_karyawan'   => $kar->id_karyawan,
                'lap'           => 0,
                'pul'           => 0,
                'lappul'        => 0
            ];
    }
    
    $potpul     = ($rekdis['lap'] * $tj->pul) + ($rekdis['pul'] * $tj->pul);
    $potlappul  = $rekdis['lappul'] * $tj->lappul;
    
    // BPJS
    $jkk = Bpjs::where('nama_jenis', 'JKK')->where('id_com', $kar->id_com)->first();
    $jkm = Bpjs::where('nama_jenis', 'JKM')->where('id_com', $kar->id_com)->first();
    $jht = Bpjs::where('nama_jenis', 'JHT')->where('id_com', $kar->id_com)->first();
    $jpn = Bpjs::where('nama_jenis', 'JPN')->where('id_com', $kar->id_com)->first();
    $sht = Bpjs::where('nama_jenis', 'KESEHATAN')->where('id_com', $kar->id_com)->first();
    
    $profil = Profile::where('id_com', $kar->id_com)->first();
    
    $pjkk = $profil->jkk == 1 && $kar->jkk == 1 ? $jkk->perusahaan : 0;
    $pjkm = $profil->jkm == 1 && $kar->jkm == 1 ? $jkm->perusahaan : 0;
    $pjht = $profil->jht == 1 && $kar->jht == 1 ? $jht->perusahaan : 0;
    $pjpn = $profil->jpn == 1 && $kar->jpn == 1 ? $jpn->perusahaan : 0;
    $psht = $profil->kesehatan == 1 && $kar->kesehatan == 1 ? $sht->perusahaan : 0;
    
    $kjkk = $kar->jkk == 1 ? $jkk->karyawan : 0;
    $kjkm = $kar->jkm == 1 ? $jkm->karyawan : 0;
    $kjht = $kar->jht == 1 ? $jht->karyawan : 0;
    $kjpn = $kar->jpn == 1 ? $jpn->karyawan : 0;
    $ksht = $kar->kesehatan == 1 ? $sht->karyawan : 0;
    
    $potongbpjs = $tj->umr * (($kjkk + $kjkm + $kjht + $kjpn + $ksht)/100);
    
    // bonus_otomatis
    if($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->spv_kol | $kar->jabatan == $tj->sokotak){
    // if(1 == 0){ // kondisi error sengaja
        $kolek = Transaksi_Perhari::leftjoin('users', 'transaksi_perhari.id', '=', 'users.id')
                ->select(\DB::raw("
                    SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.jumlah, 0)) AS Omset,
                    SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.honor, 0)) AS honor,
                    SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.bonus_cap, 0)) AS boncap
                    "))
                ->where('users.id_karyawan',$id)->first();
        
        if($month == date('m') && $year == date('Y')){
            $datkol = User::leftjoin('donatur', 'donatur.petugas', '=', 'users.name')
                ->select(\DB::raw("
                COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput' 
                        AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS totkun,
                COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput'
                        AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS totup"))
                ->where('users.id_karyawan',$id)->first();
        }else{
            $datkol = Rinbel::select('totkun', 'totup')
                ->where('id_karyawan',$id)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->first();
        }  
                
        if($kar->jabatan == $tj->kolektor){
            $honor = $kolek != null ? $kolek->honor : 0;
            $boncap = $kolek != null ? $kolek->boncap : 0;
            if ($kolek->Omset <= 10000000){
                $bon = $kolek->Omset * 4/100;
            }elseif ($kolek->Omset > 10000000 && $kolek->Omset <= 20000000){
                $bon = ($kolek->Omset - 10000000) * 5/100 + 400000; 
            }elseif ($kolek->Omset > 20000000){
                $bon = ($kolek->Omset - 20000000) * 6/100 + 900000;
            }else{
                $bon = 0;
            }
        // $rinbon= [
        //             'bonus' => ['Bonus Capaian Target', 'Bonus Omset'],
        //             'jumlah' => [$boncap, $bon]
        //          ];
        
        if($kar->status_kerja == 'Magang'){
            $rinbon= [
                        ['bonus' => 'Omset',
                         'jumlah' => round($bon)],
                        ['bonus'=> 'Capaian Target',
                         'jumlah' => round($boncap)],
                     ];
                 
            $potongan = [
                            ['nampot' => 'Potongan',
                             'jumlah' => 0],
                        ];
            $bonkol = $boncap + $bon;
        }else{   
            $rinbon= [
                        ['bonus' => 'Omset',
                         'jumlah' => round($bon)],
                        ['bonus'=> 'Capaian Target',
                         'jumlah' => round($boncap)],
                     ];     
                     
            $potongan = [
                            ['nampot' => 'Belum Dikunjungi',
                             'jumlah' => $datkol->totkun * $tj->potongan],
                            ['nampot'=> 'Tutup 1x',
                             'jumlah' => $datkol->totup * $tj->potongan],
                        ];
            $bonkol = $boncap + $bon;
        }
        $totpot = ($datkol->totkun * $tj->potongan) + ($datkol->totup * $tj->potongan);
        
        }elseif($kar->jabatan == $tj->spv_kol){
            $kol = Transaksi::
                select(\DB::raw("
                    SUM(IF( MONTH(created_at) = MONTH('$tgl_gaji') AND YEAR(created_at) = YEAR('$tgl_gaji') AND via_input = 'transaksi' 
                        AND id_koleks IN (SELECT id FROM users WHERE id_jabatan = $tj->kolektor AND id_spv = $id), jumlah, 0)) AS Omtim
                    "))->first();
        if($month == date('m') && $year == date('Y')){
            $datkolspv = User::leftjoin('donatur', 'donatur.petugas', '=', 'users.name')
                ->select(\DB::raw("
                COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput'
                        AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS belkun,
                COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput'
                        AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS beltup"))
                // ->where('users.id_karyawan',$id)
                ->whereIn('users.id_karyawan', function($query) use ($id) {
                    $query->select('id_karyawan')->from('users')->where('id_spv', $id);
                })->first();
        }else{
            $datkolspv = Rinbel::select(\DB::raw("
                SUM(IF(MONTH(tanggal) = '$month' AND YEAR(tanggal) = '$year' AND id_karyawan IN (SELECT id_karyawan FROM users WHERE id_spv = '$id'), belkun, 0)) AS belkun,
                SUM(IF(MONTH(tanggal) = '$month' AND YEAR(tanggal) = '$year' AND id_karyawan IN (SELECT id_karyawan FROM users WHERE id_spv = '$id'), beltup, 0)) AS beltup"))
                ->first();
        }
                // dd($datkolspv);
            // $datkol = User::leftjoin('donatur', 'donatur.petugas', '=', 'users.name')
            //     ->select(\DB::raw("
            //     COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totkun,
            //     COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totup"))
            //     ->where('users.id_karyawan',$id)->first();
        // $honor = $kolek != null ? $kolek->honor : 0;
        $bontim = $kol != null ? $kol->Omtim * 1/100 : 0; 
        $bon = $kolek != null ? $kolek->Omset * 1/100 : 0;
        $rinbon= [
                    // ['bonus' => 'Omset',
                    //  'jumlah' => round($bon)],
                    ['bonus'=> 'Capaian Tim',
                     'jumlah' => round($bontim) + round($bon)],
                 ];
        $bonkol = $bontim + $bon;
        $potongan = [
                        ['nampot' => 'Potongan',
                         'jumlah' => $datkolspv->belkun * $tj->potongan],
                        ['nampot'=> 'Tutup 1x',
                         'jumlah' => ($datkolspv->beltup * $tj->potongan) + ($datkol->totup * $tj->potongan)],
                        ['nampot' => 'Belum Dikunjungi',
                         'jumlah' => $datkol->totkun * $tj->potongan],
                        // ['nampot'=> 'Tutup 1x',
                        //  'jumlah' => $datkol->totup * $tj->potongan],
                    ];
        // dd($bon, $bontim);
        $totpot = ($datkolspv->belkun * $tj->potongan) + ($datkolspv->beltup * $tj->potongan) + ($datkol->totup * $tj->potongan) + ($datkol->totkun * $tj->potongan);
        // ini bonus sales
        }elseif($kar->jabatan == $tj->sokotak){
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
                $poin = round($omst/$p->minpo);
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
                // $bonset = $inbons[$b] == 1 ? round($v->omset*($bons[$b]/100)) : floatval($bons[$b]);
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
    $tj = Tunjangan::where('id_com', Auth::user()->id_com)->first();
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
                     'nominal'=> $totpo],
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
                     'nominal'=> $totpo],
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
    
    // $datfin = [];
    // $datfin['jumlah'] = count($data);
    // $datfin['id_don'] = $id_don;
    // $datfin['data'] = $data;
    // $datfin['total'] = $total;
    // $datfin['prosdon'] = [
    //                         ['nampros'  => 'Donatur Closing',
    //                          'jumlah'   => $prosdon['closing']],
    //                         ['nampros'  => 'Donatur Open',
    //                          'jumlah'   => $prosdon['open']],
    //                         ['nampros'  => 'Donatur Cancel',
    //                          'jumlah'   => $prosdon['cancel']],
    //                         ['nampros'  => 'Total Prospek',
    //                          'jumlah'   => $prosdon['closing'] + $prosdon['open'] + $prosdon['cancel']],
    //                     ]; 
        $rinbon = [
                //   ['bonus' => 'Total Poin',
                //      'jumlah'=> $totpo],
                    ['bonus' => 'Total Honor Poin',
                     'jumlah'=> $tothonpo],
                    ['bonus' => 'Total Bonus Poin',
                     'jumlah'=> $totbonpo],
                    ['bonus' => 'Total Bonus Omset',
                     'jumlah'=> $totbonset],
                    ['bonus' => 'Bonus Omset Kolekting',
                     'jumlah'=> round($bon)],
                    ['bonus' => 'Total Bonus',
                     'jumlah'=> $tothonpo + $totbonpo + $totbonset + round($bon)],
                 ];
        $bonkol = $tothonpo + $totbonpo + $totbonset;
        $potongan = [
                        ['nampot' => 'Potongan',
                         'jumlah' => 0],
                    ];
        $totpot = 0;
        }
    }else{
        $honor = 0;
        $rinbon= [
                    ['bonus' => 'Omset',
                     'jumlah' => 0],
                 ];
                 
        $potongan = [
                        ['nampot' => 'Potongan',
                         'jumlah' => 0],
                    ];
        $totpot = 0;
    }
    // end bonus_otomatis
    
    // return $komp->get();
    
    // for data
    $data       = [];
    $datgaji    = [];
    
    foreach($komp->get() as $v){
        $mod    = $v->modal;
        $nom    = '0';
        
        if ($kar->status_kerja == 'Magang') {
            if ($mod == 'harikerja') {
                $nom = number_format($datkar->jumlah, 0, ',', '.'); // Hanya jumlah hari kerja
            } else {
                $nom = '0'; // Komponen lainnya tidak diperhitungkan
            }
        } else {
        
            if($mod == 'umk'){
                $nom = number_format($datkar->umk,0, ',' , '.');
            }else if($mod == 'gajipokok'){
                $nom = number_format($datkar->$gol,0, ',' , '.');
            }else if($mod == 'tunjanganberas'){
                $nom = number_format($kar->tj_pas == 1 ? 0 : $tj->tj_beras*$tj->jml_beras*$totkel,0, ',' , '.');
            }else if($mod == 'tunjanganpasangan'){
                $nom = number_format($tot_pasangan * ($tj->tj_pasangan/100 * $datkar->$gol),0, ',' , '.');
            }else if($mod == 'tunjangananak'){
                $nom = number_format($tot_anak * ($tj->tj_anak/100 * $datkar->$gol),0, ',' , '.');
            }else if($mod == 'tunjangandaerah'){
                $nom = number_format($datkar->tj_daerah,0, ',' , '.');
            }else if($mod == 'tunjanganfungsional'){
                $nom = number_format($tjfung,0, ',' , '.');
            }else if($mod == 'harikerja'){
                $nom = number_format($datkar->jumlah,0, ',' , '.');
            }else if($mod == 'uangtransport'){
                $nom = number_format($trans,0, ',' , '.');
            }else if($mod == 'keterlambatan'){
                $nom = number_format(round($datkar->potongan),0, ',' , '.');
            }else if($mod == 'tidaklaporanataupresensipulang'){
                $nom = number_format($potpul,0, ',' , '.');
            }else if($mod == 'tidaklaporandanpresensipulang'){
                $nom = number_format($potlappul,0, ',' , '.');
            }else if($mod == 'ketenagakerjaan'){
                $nom = number_format($tj->umr * (($pjkk + $pjkm + $pjht + $pjpn)/100),0, ',' , '.');
            }else if($mod == 'kesehatan'){
                $nom = number_format($tj->umr * ($psht/100),0, ',' , '.');
            }else if($mod == 'bpjs'){
                $nom = number_format(round($potongbpjs),0, ',' , '.');
            }else if($mod == 'omset'){
                $omsetValue = 0;
                foreach ($rinbon as $item) {
                    if ($item['bonus'] === 'Omset') {
                        $omsetValue = $item['jumlah'];
                        break;
                    }
                }
        
                $nom = number_format($omsetValue, 0, ',', '.');
            }else if($mod == ''){
                $nom = 0;
            }
        }
        
        $kon_if = !$req->id_skema ? $v->grup == 'utama' || $nom > 0 || $v->bisa_edit == 1 : 1 == 1;
        // $kon_if = !$req->id_skema ? $v->grup == 'utama' || $v->bisa_edit == 1 : 1 == 1;
        
        if($kon_if && $nom > 0 || $v->bisa_edit == 1){
            $datgaji[] = [
                        'id'    => $v->id,
                        'nama'  => $v->nama,
                        'nilai' => $nom,
                        'koned' => $v->bisa_edit,
                        'grup'  => $v->grup,
                        'modal' => $v->modal,
                    ];
        }
    }
    
    $data = [
        'id_karyawan'   => $kar->id_karyawan,
        'kondisi'       => $kar->kondisi,
        'nama'          => $kar->nama,
        'no_rek'        => $kar->no_rek,
        'nik'           => $kar->nik,
        'status_kerja'  => $kar->status_kerja,
        'masa_kerja'    => $kar->masa_kerja,
        'golongan'      => $kar->golongan,
        'id_jabatan'    => $kar->jabatan,
        'id_kantor'     => $kar->id_kantor,
        'tgl_gaji'      => $tgl_gaji,
        'datgaji'       => $datgaji
    ];
    
    return response()->json([
                            'status'    => 'sukses',
                            'data'      => $data,
                            'hihih'     => 'hihihi,'
                            // 'status'    => in_array('tunjanganberas', $arkom) ? 'sukses' : 'gagal',
                            // 'arkom'     => $arkom,
                            ]);
    
    // end_new
if($ske->skema_gaji == 1){
    $tot_pasangan = 0;
    if($kar->status_nikah == 'Menikah' && $kar->nm_pasangan != null){
        $istri = unserialize($kar->nm_pasangan);
        foreach($istri as $key => $value){
            $tot_pasangan += 1;
        }
    }
    
    $anak = unserialize($kar->nm_anak); 
    $tgl = unserialize($kar->tgl_lahir_anak);
    $sts = unserialize($kar->status_anak);
    
    $tot_anak = 0;
    if($kar->nm_anak != null){
    foreach($anak as $key => $value){
        $tt = new DateTime($tgl[$key]);
        if($today->diff($tt)->y <= 21 && $sts[$key] == 'Belum Menikah'){
            $tot_anak += 1;
        }
    }
    }
    $tgl_gaji = $kar->tgl_gaji;
    $month = date("m",strtotime($tgl_gaji));
    $year = date("Y",strtotime($tgl_gaji));
    $tj = Tunjangan::where('id_com', Auth::user() ->id_com)->first();
    $drh = Daerah::where('id_daerah', $kar->id_daerah)->first();
    if($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->spv_kol | $kar->jabatan == $tj->sokotak){
        $kolek = Transaksi_Perhari::leftjoin('users', 'transaksi_perhari.id', '=', 'users.id')
                ->select(\DB::raw("
                    SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.jumlah, 0)) AS Omset,
                    SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.honor, 0)) AS honor,
                    SUM(IF( MONTH(transaksi_perhari.tanggal) = MONTH('$tgl_gaji') AND YEAR(transaksi_perhari.tanggal) = YEAR('$tgl_gaji') , transaksi_perhari.bonus_cap, 0)) AS boncap
                    "))
                ->where('users.id_karyawan',$id)->first();
        
        if($month == date('m') && $year == date('Y')){
            $datkol = User::leftjoin('donatur', 'donatur.petugas', '=', 'users.name')
                ->select(\DB::raw("
                COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput' 
                        AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS totkun,
                COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput'
                        AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS totup"))
                ->where('users.id_karyawan',$id)->first();
        }else{
            $datkol = Rinbel::select('totkun', 'totup')
                ->where('id_karyawan',$id)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->first();
        }  
                
        if($kar->jabatan == $tj->kolektor){
            $honor = $kolek != null ? $kolek->honor : 0;
            $boncap = $kolek != null ? $kolek->boncap : 0;
            if ($kolek->Omset <= 10000000){
                $bon = $kolek->Omset * 4/100;
            }elseif ($kolek->Omset > 10000000 && $kolek->Omset <= 20000000){
                $bon = ($kolek->Omset - 10000000) * 5/100 + 400000; 
            }elseif ($kolek->Omset > 20000000){
                $bon = ($kolek->Omset - 20000000) * 6/100 + 900000;
            }else{
                $bon = 0;
            }
        // $rinbon= [
        //             'bonus' => ['Bonus Capaian Target', 'Bonus Omset'],
        //             'jumlah' => [$boncap, $bon]
        //          ];
        
        if($kar->status_kerja == 'Magang'){
            $rinbon= [
                        ['bonus' => 'Omset',
                         'jumlah' => round($bon)],
                        ['bonus'=> 'Capaian Target',
                         'jumlah' => round($boncap)],
                     ];
                 
            $potongan = [
                            ['nampot' => 'Potongan',
                             'jumlah' => 0],
                        ];
            $bonkol = $boncap + $bon;
        }else{   
            $rinbon= [
                        ['bonus' => 'Omset',
                         'jumlah' => round($bon)],
                        ['bonus'=> 'Capaian Target',
                         'jumlah' => round($boncap)],
                     ];     
                     
            $potongan = [
                            ['nampot' => 'Belum Dikunjungi',
                             'jumlah' => $datkol->totkun * $tj->potongan],
                            ['nampot'=> 'Tutup 1x',
                             'jumlah' => $datkol->totup * $tj->potongan],
                        ];
            $bonkol = $boncap + $bon;
        }
        $totpot = ($datkol->totkun * $tj->potongan) + ($datkol->totup * $tj->potongan);
        
        }elseif($kar->jabatan == $tj->spv_kol){
            $kol = Transaksi::
                select(\DB::raw("
                    SUM(IF( MONTH(created_at) = MONTH('$tgl_gaji') AND YEAR(created_at) = YEAR('$tgl_gaji') AND via_input = 'transaksi' 
                        AND id_koleks IN (SELECT id FROM users WHERE id_jabatan = $tj->kolektor AND id_spv = $id), jumlah, 0)) AS Omtim
                    "))->first();
        if($month == date('m') && $year == date('Y')){
            $datkolspv = User::leftjoin('donatur', 'donatur.petugas', '=', 'users.name')
                ->select(\DB::raw("
                COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput'
                        AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS belkun,
                COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput'
                        AND DATE_FORMAT(donatur.created_at, '%Y-%m') <> DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%Y-%m'), donatur.id, NULL)) AS beltup"))
                // ->where('users.id_karyawan',$id)
                ->whereIn('users.id_karyawan', function($query) use ($id) {
                    $query->select('id_karyawan')->from('users')->where('id_spv', $id);
                })->first();
        }else{
            $datkolspv = Rinbel::select(\DB::raw("
                SUM(IF(MONTH(tanggal) = '$month' AND YEAR(tanggal) = '$year' AND id_karyawan IN (SELECT id_karyawan FROM users WHERE id_spv = '$id'), belkun, 0)) AS belkun,
                SUM(IF(MONTH(tanggal) = '$month' AND YEAR(tanggal) = '$year' AND id_karyawan IN (SELECT id_karyawan FROM users WHERE id_spv = '$id'), beltup, 0)) AS beltup"))
                ->first();
        }
                // dd($datkolspv);
            // $datkol = User::leftjoin('donatur', 'donatur.petugas', '=', 'users.name')
            //     ->select(\DB::raw("
            //     COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totkun,
            //     COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totup"))
            //     ->where('users.id_karyawan',$id)->first();
        // $honor = $kolek != null ? $kolek->honor : 0;
        $bontim = $kol != null ? $kol->Omtim * 1/100 : 0; 
        $bon = $kolek != null ? $kolek->Omset * 1/100 : 0;
        $rinbon= [
                    // ['bonus' => 'Omset',
                    //  'jumlah' => round($bon)],
                    ['bonus'=> 'Capaian Tim',
                     'jumlah' => round($bontim) + round($bon)],
                 ];
        $bonkol = $bontim + $bon;
        $potongan = [
                        ['nampot' => 'Potongan',
                         'jumlah' => $datkolspv->belkun * $tj->potongan],
                        ['nampot'=> 'Tutup 1x',
                         'jumlah' => ($datkolspv->beltup * $tj->potongan) + ($datkol->totup * $tj->potongan)],
                        ['nampot' => 'Belum Dikunjungi',
                         'jumlah' => $datkol->totkun * $tj->potongan],
                        // ['nampot'=> 'Tutup 1x',
                        //  'jumlah' => $datkol->totup * $tj->potongan],
                    ];
        // dd($bon, $bontim);
        $totpot = ($datkolspv->belkun * $tj->potongan) + ($datkolspv->beltup * $tj->potongan) + ($datkol->totup * $tj->potongan) + ($datkol->totkun * $tj->potongan);
        // ini bonus sales
        }elseif($kar->jabatan == $tj->sokotak){
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
                $poin = round($omst/$p->minpo);
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
                // $bonset = $inbons[$b] == 1 ? round($v->omset*($bons[$b]/100)) : floatval($bons[$b]);
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
    $tj = Tunjangan::where('id_com', Auth::user()->id_com)->first();
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
                     'nominal'=> $totpo],
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
                     'nominal'=> $totpo],
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
    
    // $datfin = [];
    // $datfin['jumlah'] = count($data);
    // $datfin['id_don'] = $id_don;
    // $datfin['data'] = $data;
    // $datfin['total'] = $total;
    // $datfin['prosdon'] = [
    //                         ['nampros'  => 'Donatur Closing',
    //                          'jumlah'   => $prosdon['closing']],
    //                         ['nampros'  => 'Donatur Open',
    //                          'jumlah'   => $prosdon['open']],
    //                         ['nampros'  => 'Donatur Cancel',
    //                          'jumlah'   => $prosdon['cancel']],
    //                         ['nampros'  => 'Total Prospek',
    //                          'jumlah'   => $prosdon['closing'] + $prosdon['open'] + $prosdon['cancel']],
    //                     ]; 
        $rinbon= [
                //   ['bonus' => 'Total Poin',
                //      'jumlah'=> $totpo],
                    ['bonus' => 'Total Honor Poin',
                     'jumlah'=> $tothonpo],
                    ['bonus' => 'Total Bonus Poin',
                     'jumlah'=> $totbonpo],
                    ['bonus' => 'Total Bonus Omset',
                     'jumlah'=> $totbonset],
                    ['bonus' => 'Bonus Omset Kolekting',
                     'jumlah'=> round($bon)],
                    ['bonus' => 'Total Bonus',
                     'jumlah'=> $tothonpo + $totbonpo + $totbonset + round($bon)],
                 ];
        $bonkol = $tothonpo + $totbonpo + $totbonset;
        $potongan = [
                        ['nampot' => 'Potongan',
                         'jumlah' => 0],
                    ];
        $totpot = 0;
        }
    }else{
        $honor = 0;
        $rinbon= [
                    ['bonus' => 'Omset',
                     'jumlah' => 0],
                 ];
                 
        $potongan = [
                        ['nampot' => 'Potongan',
                         'jumlah' => 0],
                    ];
        $totpot = 0;
    }
    // dd($honor);
    // $transport = $tj->potongan0;
    $gol = $kar->status_kerja == 'Magang' ? 'IA' : $kar->golongan;
    $datass = Karyawan::leftjoin('gapok', 'karyawan.masa_kerja', '=', 'gapok.th')->leftjoin('jabatan', 'karyawan.jabatan', '=', 'jabatan.id')
            ->leftjoin('daerah', 'karyawan.id_daerah', '=', 'daerah.id_daerah')
            ->leftjoin('presensi', 'karyawan.id_karyawan', '=', 'presensi.id_karyawan')
            ->leftjoin("set_terlambat",function($join) use ($id){
                        $join->on("presensi.keterlambatan",">=","set_terlambat.awal")
                             ->on("presensi.keterlambatan","<=","set_terlambat.akhir")
                             ->where('presensi.id_karyawan', $id);
                        })
            ->select(\DB::raw("karyawan.nama, gapok.$gol, jabatan.tj_fungsional, jabatan.tj_jabatan , daerah.tj_daerah, jabatan.tj_training, jabatan.kon_tj_plt, jabatan.tj_plt,
                            SUM(IF(presensi.status = 'Hadir' AND MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji') 
                            OR presensi.status = 'Terlambat' AND MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji') 
                            , presensi.jumlah,0)) AS jumlah,
                            SUM(IF(MONTH(presensi.created_at) = MONTH('$tgl_gaji') AND YEAR(presensi.created_at) = YEAR('$tgl_gaji'), set_terlambat.potongan, 0)) AS potongan"))
            ->groupBy('karyawan.nama','gapok.'.$gol, 'jabatan.tj_jabatan', 'jabatan.tj_training', 'daerah.tj_daerah', 'jabatan.tj_fungsional', 'jabatan.tj_training', 'jabatan.kon_tj_plt', 'jabatan.tj_plt')
            ->where('karyawan.id_karyawan',$id)->get();
    // dd($datass);
    $total = $tot_anak + $tot_pasangan + 1;
    // dd($potongbpjs);
    $jkk = Bpjs::where('nama_jenis', 'JKK')->first();
    $jkm = Bpjs::where('nama_jenis', 'JKM')->first();
    $jht = Bpjs::where('nama_jenis', 'JHT')->first();
    $jpn = Bpjs::where('nama_jenis', 'JPN')->first();
    $sht = Bpjs::where('nama_jenis', 'KESEHATAN')->first();
    
    $profil = Profile::where('id_com', Auth::user()->id_com)->first();
    
    $pjkk = $profil->jkk == 1 && $kar->jkk == 1 ? $jkk->perusahaan : 0;
    $pjkm = $profil->jkm == 1 && $kar->jkm == 1 ? $jkm->perusahaan : 0;
    $pjht = $profil->jht == 1 && $kar->jht == 1 ? $jht->perusahaan : 0;
    $pjpn = $profil->jpn == 1 && $kar->jpn == 1 ? $jpn->perusahaan : 0;
    $psht = $profil->kesehatan == 1 && $kar->kesehatan == 1 ? $sht->perusahaan : 0;
    
    $kjkk = $kar->jkk == 1 ? $jkk->karyawan : 0;
    $kjkm = $kar->jkm == 1 ? $jkm->karyawan : 0;
    $kjht = $kar->jht == 1 ? $jht->karyawan : 0;
    $kjpn = $kar->jpn == 1 ? $jpn->karyawan : 0;
    $ksht = $kar->kesehatan == 1 ? $sht->karyawan : 0;
    
    $potongbpjs = $tj->umr * (($kjkk + $kjkm + $kjht + $kjpn + $ksht)/100);
    
    $databpjs = [
        'jkk' => number_format($tj->umr * ($pjkk/100),0, ',' , '.'),
        'jkm' => number_format($tj->umr * ($pjkm/100),0, ',' , '.'),
        'jht' => number_format($tj->umr * ($pjht/100),0, ',' , '.'),
        'jpn' => number_format($tj->umr * ($pjpn/100),0, ',' , '.'),
        // 'sht' => number_format($tj->umr * ($psht/100),0, ',' , '.'),
        // 'total' => number_format($tj->umr * (($jkk->perusahaan + $jkm->perusahaan + $jht->perusahaan + $jpn->perusahaan)/100),0, ',' , '.'),
        ];
        
    // return($databpjs);
    
    $rekdis = Rekdis::selectRaw("id_karyawan, COUNT(IF(stat = 1, tanggal, NULL)) AS lap, COUNT(IF(stat = 2, tanggal, NULL)) AS pul, COUNT(IF(stat = 3, tanggal, NULL)) AS lappul")
            ->where('id_karyawan', $kar->id_karyawan)
            ->groupBy('id_karyawan')->first();
            
    if($rekdis == null){        
    $rekdis = [
                'id_karyawan'   => $kar->id_karyawan,
                'lap'           => 0,
                'pul'           => 0,
                'lappul'         => 0
            ];
    }
    
    $potpul = ($rekdis['lap'] * $tj->pul) + ($rekdis['pul'] * $tj->pul);
    $potlappul = $rekdis['lappul'] * $tj->lappul;
    
    $data = [];
    foreach($datass as $x => $v){
        
    $potlaptab = [
                ['nampot' => 'Keterlambatan', 'jumlah'  => round($v->potongan)],
                ['nampot' => 'Tidak Laporan atau Presensi Pulang', 'jumlah'  => $potpul],
                ['nampot' => 'Tidak Laporan dan Presensi Pulang', 'jumlah'  => $potlappul]
            ];
    
    // dd($rekdis, $potlaptab);
    
        // if($id == 8911041807102){
        //     $tjjab = ($kar->plt == 1 ? $tj->tj_plt/100 * $v->tj_jabatan : $v->tj_jabatan) + $v->tj_fungsional + 400000;
        // }else{
            $tjjab = ($kar->plt == 1 ? $v->tj_jabatan * ($v->tj_plt/100) : $v->tj_jabatan) + $v->tj_fungsional;
        // }
        
        // if($kar->plt == 1){
        //     $tjjab = ($v->kon_tj_plt == 'p' ? $v->tj_plt/100 * $v->tj_jabatan : $v->tj_plt) + $v->tj_fungsional;
        //     $tjjabtrain = $v->tj_training == 1 ? ($v->kon_tj_plt == 'p' ? $v->tj_plt/100 * $v->tj_jabatan : $v->tj_plt) : 0;
        // }else{
        //     $tjjab = $v->tj_jabatan + $v->tj_fungsional;
        //     $tjjabtrain = $v->tj_training == 1 ? $v->tj_jabatan : 0;
        // }
        
            
        $tjjabtrain = $v->tj_training == 1 ? $v->tj_jabatan : 0;
        
        if($kar->status_kerja == 'Contract'){
            $data['data'][] = [
                'kondisi' => $kar->kondisi,
                'nama' => $v->nama,
                'no_rek' => $kar->no_rek,
                'nik' => $kar->nik,
                'status_kerja' => $kar->status_kerja,
                'masa_kerja' => $kar->masa_kerja,
                'golongan' => $kar->golongan,
                'id_jabatan' => $kar->jabatan,
                'id_kantor' => $kar->id_kantor,
                // 'id_daerah' => $kar->id_daerah,
                'gapok' => number_format($v->$gol,0, ',' , '.'),
                'tj_jabatan' => number_format($tjjab,0, ',' , '.'),
                'tj_daerah' => number_format($v->tj_daerah,0, ',' , '.'),
                'tj_p_daerah' => number_format($kar->jab_daerah == 1 ? $drh->tj_jab_daerah : 0,0, ',' , '.'),
                'jml_hari' => $v->jumlah,
                'tj_anak' => number_format($kar->tj_pas == 1 ? 0 : $tot_anak * ($tj->tj_anak/100 * $v->$gol),0, ',' , '.'),
                'tj_pasangan' => number_format($kar->tj_pas == 1 ? 0 : $tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol),0, ',' , '.'),
                'tj_beras' => number_format($kar->tj_pas == 1 ? 0 : $tj->tj_beras*$tj->jml_beras*$total,0, ',' , '.'),
                'transport' => number_format($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor :  $v->jumlah * $tj->tj_transport,0, ',' , '.'),
                'total' => number_format($v->$gol + $tjjab + $v->tj_daerah + ($kar->tj_pas == 1 ? 0 : $tot_anak * ($tj->tj_anak/100 * $v->$gol)) + 
                            ($kar->tj_pas == 1 ? 0 : $tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol)) + ($kar->tj_pas == 1 ? 0 : $tj->tj_beras*$tj->jml_beras*$total) + 
                            ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport),0, ',' , '.'),
                'thp' => round(($v->$gol + $tjjab + $v->tj_daerah + ($kar->tj_pas == 1 ? 0 : $tot_anak * ($tj->tj_anak/100 * $v->$gol)) + 
                            ($kar->tj_pas == 1 ? 0 : $tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol)) + ($kar->tj_pas == 1 ? 0 : $tj->tj_beras*$tj->jml_beras*$total) + 
                            ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport))),
                // 'thp' => number_format(($v->$gol + $tjjab + $v->tj_daerah + ($tot_anak * ($tj->tj_anak/100 * $v->$gol)) + 
                //             ($tot_pasangan * ($tj->tj_pasangan/100 * $v->$gol)) + ($tj->tj_beras*$tj->jml_beras*$total) + 
                //             ($kar->jabatan == $tj->kolektor ? $honor : $v->jumlah * $tj->tj_transport)) - $potongbpjs ,0, ',' , '.'),
                'tgl_gaji' => $tgl_gaji,
                'bpjs' => round($potongbpjs),
                'tot_tj_bpjs' => number_format($tj->umr * (($pjkk + $pjkm + $pjht + $pjpn)/100),0, ',' , '.'),
                'bpjs_sehat' => number_format($tj->umr * ($psht/100),0, ',' , '.'),
                'tj_bpjs' => $databpjs,
                'bonus' => round($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->spv_kol | $kar->jabatan == $tj->sokotak ? $bonkol : 0),
                'rinbon' => $rinbon,
                'potlaptab' => $potlaptab,
                'potab' => round($v->potongan),
                'potdis' => '0',
                'totpot' => $totpot,
                'potongan' => $potongan,
                'bokin' => '0'
            ];
        }else if($kar->status_kerja == 'Training'){
            $data['data'][] = [
                'kondisi' => $kar->kondisi,
                'nama' => $v->nama,
                'no_rek' => $kar->no_rek,
                'nik' => $kar->nik,
                'status_kerja' => $kar->status_kerja,
                'masa_kerja' => $kar->masa_kerja,
                'golongan' => $kar->golongan,
                'id_jabatan' => $kar->jabatan,
                'id_kantor' => $kar->id_kantor,
                // 'id_daerah' => $kar->id_daerah,
                'gapok' => number_format($v->$gol * (80/100),0, ',' , '.'),
                'tj_jabatan' => number_format($tjjabtrain,0, ',' , '.'),
                'tj_daerah' => '0',
                'tj_p_daerah' => '0',
                'jml_hari' => $v->jumlah,
                'tj_anak' => '0',
                'tj_pasangan' => '0',
                'tj_beras' => '0',
                'transport' => number_format($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport,0, ',' , '.'),
                'total' => number_format(($v->$gol * (80/100)) + $tjjabtrain +
                            ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport),0, ',' , '.'),
                'thp' => round(($v->$gol * (80/100)) + $tjjabtrain +
                            ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tj->tj_transport)),
                'tgl_gaji' => $tgl_gaji,
                'bpjs' => 0,
                'tot_tj_bpjs' => '0',
                'bpjs_sehat' => '0',
                'tj_bpjs' => ['jkk' => '0', 'jkm' => '0', 'jht' => '0', 'jpn' => '0'],
                'bonus' => round($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->spv_kol | $kar->jabatan == $tj->sokotak ? $bonkol : 0),
                'rinbon' => $rinbon,
                'potab' => round($v->potongan),
                'potlaptab' => $potlaptab,
                'potdis' => '0',
                'totpot' => $totpot,
                'potongan' => $potongan,
                'bokin' => '0'
            ];
        }else{
            $tjmag = $tj->tj_magang_mhs;
            $data['data'][] = [
                'kondisi' => $kar->kondisi,
                'nama' => $v->nama,
                'no_rek' => $kar->no_rek,
                'nik' => $kar->nik,
                'status_kerja' => $kar->status_kerja,
                'masa_kerja' => $kar->masa_kerja,
                'golongan' => $kar->golongan,
                'id_jabatan' => $kar->jabatan,
                'id_kantor' => $kar->id_kantor,
                // 'id_daerah' => $kar->id_daerah,
                'gapok' => 0,
                'tj_jabatan' => 0,
                'tj_daerah' => '0',
                'tj_p_daerah' => '0',
                'jml_hari' => $v->jumlah,
                'tj_anak' => '0',
                'tj_pasangan' => '0',
                'tj_beras' => '0',
                'transport' => number_format($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tjmag,0, ',' , '.'),
                'total' => number_format(
                            ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tjmag),0, ',' , '.'),
                'thp' => round(
                            ($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->so | $kar->jabatan == $tj->sokotak ? $honor : $v->jumlah * $tjmag)),
                'tgl_gaji' => $tgl_gaji,
                'bpjs' => 0,
                'tot_tj_bpjs' => '0',
                'bpjs_sehat' => '0',
                'tj_bpjs' => ['jkk' => '0', 'jkm' => '0', 'jht' => '0', 'jpn' => '0'],
                'bonus' => round($kar->jabatan == $tj->kolektor | $kar->jabatan == $tj->spv_kol | $kar->jabatan == $tj->sokotak ? $bonkol : 0),
                'rinbon' => $rinbon,
                'potab' => 0,
                'potdis' => '0',
                'totpot' => 0,
                'potongan' => $potongan,
                'bokin' => '0'
            ];
        }
    }
    return($data);
}else{
    return('skema 2');
}
}

// public function post_lap(Request $request)
// {
//     // return response()->json([
//     //     'fileNames' => $request->file()
//     // ]);
    
//     $data = $request->items;
//     $fileNames = [];
    
//     foreach ($data as $i => $val) {
//         // Memeriksa apakah file tidak kosong dan properti 'bukti' ada
//         if (isset($val['bukti'])) {
//             // Mengambil file dari request
//             $file = $request->file('items')[$i]['bukti'];
            
//             // Mengambil nama asli (original name) dari file jika ada
//             if ($file && is_object($file)) {
                
//                 $originalName = $file->getClientOriginalName();
//                 $fileNames[] = $originalName;
//             } else {
//                 $fileNames[] = 'No file uploaded';
//             }
//         }
//     }

//     // Cek apakah request memiliki voice note yang dikirim sebagai string path
//     if ($request->hasFile('voice')) {
//             // Proses file lebih lanjut, misalnya menyimpan informasi ke database
//             return response()->json([
//                 'status' => 'success',
//                 'voiceName' => $request->file('voice')->getClientOriginalName(),
//                 'fileNames' => $fileNames
//             ]);
        
//     }

//     return response()->json([
//         'status' => 'error',
//         'message' => 'No valid file found in items or no voice note found in the request'
//     ]);
// }

public function post_lap(Request $request){
    
    if(Auth::user()->id_com != null && Auth::user()->presensi != null){
        
    
        
        //conver detik
        $sec_vn = $request->sec_vn != '' ? $request->sec_vn : 0;
            list($minutes, $seconds) = explode(':', $sec_vn);
        $total_seconds = ($minutes * 60) + $seconds;
            
        $lapnow = Laporan::where('id_karyawan', Auth::user()->id_karyawan)->whereDate('created_at', date('Y-m-d'))->first();
        
        if($lapnow != null){
            return response()->json(
                [
                    "status"=>"exist",
                    // "data"=>$data
                ]
            );
        }else{
         
            if($request->hasFile('suara')){
                $vn = $request->file('suara')->getClientOriginalName();
                $request->file('suara')->move('lampiranLaporan', $vn);
            } else{
                $vn = NULL;
            }
            // $created = date('Y-m-d H:i:s');
            $saveid = 0;
            $saveid = Laporan::insertGetId([
                        'id_karyawan'   => Auth::user()->id_karyawan,
                        'id_jabatan'    => Auth::user()->id_jabatan,
                        'pr_jabatan'    => Auth::user()->pr_jabatan,
                        'id_kantor'     => Auth::user()->id_kantor,
                        'kantor_induk'  => Auth::user()->kantor_induk,
                        'nama'          => Auth::user()->name,
                        
                        'ket'           => $request->deskripsiTambahan,
                        'capaian'       => $request->capaianTotal,
                        'target'        => $request->targetTotal,
                        'sec_vn'        => $total_seconds,
                        'link_lap'      => $request->link != '' ? $request->link : NULL,
                        // 'lampiran'      => $request->file != '' ? $lampiran : NULL,
                        'vn'            => $request->suara != '' ? $vn : NULL,
                        // 'created_at'    => $created,
                        // 'updated_at'    => $created,
                    ]);
            
            // // $tugas  = $request->items != '%5B%5D' ?  json_decode (urldecode($request->items)) : [];
            // $tugas  = $request->items ?? [];
          
            // foreach($tugas as $i => $tug){
            //     if($tug['jenis'] == 'proses' && $tug['id'] > 0){
            //       if ($request->hasFile("items.$i.bukti")) {
            //           $file = $request->file("items.$i.bukti");
            //           if ($file && is_object($file)) {
            //                 $originalName = $file->getClientOriginalName();
            //                 $fileNames[] = $originalName;
            //                 $file->move('lampiranLaporan', $originalName);
            //             }else {
            //                 $fileNames[] = 'No file uploaded';
            //             }
            //         }
                    
            //         $ren        = Rencana::find($tug['id']);
                    
            //         if($ren->id_range > 0){
            //         $uprange    = Rencana::where('id_range', $ren->id_range)
            //                     ->update([
            //                                 'aktif'         => $tug['aktif'],
            //                                 'alasan'        => $tug['aktif'] > 0 ? NULL : $tug['alasan'],
            //                                 'user_aktif'    => $tug['aktif'] > 0 ? NULL : Auth::user()->id,
            //                                 'tgl_selesai'   => $tug['capaian'] == $ren->target ? date('Y-m-d') : NULL,
            //                                 'acc'           => 1
            //                             ]);
                                        
            //         $caprange   = Rencana::where('id_range', $ren->id_range)->whereDate('tgl_awal', '>', $ren->tgl_awal)
            //                     ->update([
            //                                 'capaian'       => $tug['capaian'],
            //                                 'acc'           => 1
            //                             ]);
            //         }
                    
            //         $ren->in_tambahan   = 0;                    
            //         $ren->capaian       = $tug['capaian'];
            //         $ren->deskripsi     = $tug['deskripsi'];
            //         $ren->bukti         = $tug['bukti'] != '' ? $originalName : NULL;
            //         $ren->jam_awal      = $tug['jamMulai'];
            //         $ren->jam_akhir     = $tug['jamAkhir'];
            //         $ren->aktif         = $tug['aktif'];
            //         $ren->acc           = 1;
            //         $ren->alasan        = $tug['aktif'] > 0 ? NULL : $tug['alasan'];
            //         $ren->user_aktif    = $tug['aktif'] > 0 ? NULL : Auth::user()->id;
            //         $ren->tgl_selesai   = $tug['capaian'] == $ren->target ? date('Y-m-d') : NULL;
            //         $ren->id_laporan    = $saveid;
            //         $ren->update();
                    
                    
        

            //     }else if($tug['jenis'] == 'hasil' && $tug['inEdit'] == true && $tug['id'] > 0){
            //         $uphasil   = Rencana::where('id', $tug['id'])
            //                     ->update([
            //                                 'capaian'       => $tug['capaian'],
            //                                 'acc'           => 1
            //                             ]);
            //     }
            // }
            
            
            return response()->json(
                [
                    "sukses"    => "sukses",
                    "req" =>$request->all()
                ]
            );
        }
    }
}

public function acc_gaji(Request $req){
    
    $p = $req->datgaji != '%5B%5D' ?  json_decode (urldecode($req->datgaji)) : [];
    $get = collect($p);
    $datpot = [];
    $datbon = [];
    $data = new Gaji;
    
    foreach($get as $v){
        $nilai  = preg_replace("/[^0-9]/", "", $v->nilai);
        $mod    = $v->modal;
        if($mod == 'gajipokok'){
            $data->gapok            = $nilai;
        }else if($mod == 'tunjanganberas'){
            $data->tj_beras         = $nilai;
        }else if($mod == 'tunjanganfungsional'){
            $data->tj_jabatan       = $nilai;
        }else if($mod == 'tunjanganpasangan'){
            $data-> tj_pasangan     = $nilai;
        }else if($mod == 'tunjangananak'){
            $data->tj_anak          = $nilai;
        }else if($mod == 'tunjangandaerah'){
            $data->tj_daerah        = $nilai;
        }else if($mod == 'uangtransport'){
            $data->transport        = $nilai;
        }else if($mod == 'ketenagakerjaan'){
            $data->ketenagakerjaan  = $nilai;
        }else if($mod == 'kesehatan'){
            $data->kesehatan        = $nilai;
        }else if($mod == 'tunjanganpejabatadaerah'){
            $data->tj_p_daerah      = $nilai;
        }
    }
    
    foreach($get->where('grup', 'potongan') as $v){
        $obj = new stdClass();
        $obj->nampot    = $v->nama;
        $obj->jumlah    = $v->nilai;
        $datpot[]       = $obj;
    }
    
    
    foreach($get->where('grup', 'bonus') as $v){
        $obj = new stdClass();
        $obj->nampot    = $v->nama;
        $obj->jumlah    = $v->nilai;
        $datbon[]       = $obj;
    }
    $serpot = $datpot != [] ? serialize($datpot) : null;
    $serbon = $datbon != [] ? serialize($datbon) : null;
    
    $data->potlaptab    = $serpot;
    $data->arrinbon     = $serbon;
    $data->thp          = $req->thp;
    
    $data->id_karyawan  = $req->id_karyawan;
    $data->nama         = $req->nama;
    $data->nik          = $req->nik;
    // $data->no_rek       = $req->no_rek;
    $data->status_kerja = $req->status_kerja;
    $data->masa_kerja   = $req->masa_kerja;
    $data->golongan     = $req->golongan;
    $data->id_jabatan   = $req->id_jabatan;
    $data->id_kantor    = $req->id_kantor;
    $data->created_at   = $req->tgl_gaji;
    $data->created      = date('Y-m-d H:i:s');
    $data->user_insert  = Auth::user()->id;
    
    if($req->id_karyawan == Auth::user()->id_karyawan && Auth::user()->presensi == 'kacab'){
        $data->status   = 'req';
    }else{
        $data->status   = $req->status;
    }
    
    $tg_gaji = date('Y-m-01', strtotime('+1 month', strtotime($req->tgl_gaji)));
    
    $datkar = Karyawan::where('id_karyawan',$req->id_karyawan)->where('id_com', Auth::user()->id_com)->update(['tgl_gaji' => $tg_gaji]);
    \LogActivity::addToLog(Auth::user()->name.' Menyetujui Gaji '.$req->id_karyawan);
        
    $data->save();
    return response()->json(
        [
            "status"    => "sukses",
        ]
    );
}

public function get_gajiid2(Request $req){
    $gaji = Gaji::whereDate('created', $req->tgl)->get();
    return fractal($gaji, new GajiKarTransformer())->toArray();
}


// public function notif_hp(Request $request)
// {
//     define('FCM_AUTH_KEY', 'AAAA5kE6iRc:APA91bF0SlLAo6VU6in0QAXkDsxIr1Cee6DP7HX-HdeR6BmTbUuDR5dRfswhLAFeBPnWe_iFymhDv4hnYrp6N8__5Ns96DQTGLF3t2-r3brXVerHfRIs1oWqtwDz5Ms9fwRyoCjwSKQk');
    
//     $id_karyawan = $request->id_karyawan;
//     $kar = user::leftjoin('udev', 'udev.id_user', '=', 'users.id')
//                 // ->leftjoin('udev', 'udev.id_user', '=', 'users.id')
//                 ->where('users.id_karyawan', $id_karyawan)
//               ->get();
               
//     $tokens = [];
//     $title = 'Hallo';

//     foreach ($kar as $val) {
//         $tokens[] = $val->token;
//         $title = 'Hallo ' . $val->nama;
//     }

//   if (!empty($tokens)) {
//         $postdata = json_encode(
//             [
//                 'notification' => [
//                     'title' => $title,
//                     'body' => 'Ada Tugas Tugas baru Untuk anda',
//                     'sound' => "default",
//                 ],
//                 'registration_ids' => $tokens,
//                 'data' => [
//                     'click_action' => 'LIFIYK',
//                 ],
//             ]
//         );

//         $opts = array(
//             'http' => array(
//                 'method'  => 'POST',
//                 'header'  => 'Content-type: application/json' . "\r\n" .
//                             'Authorization: key=' . FCM_AUTH_KEY . "\r\n",
//                 'content' => $postdata
//             )
//         );

//         $context  = stream_context_create($opts);

//         $result = file_get_contents('https://fcm.googleapis.com/fcm/send', false, $context);
        
//         return response()->json(['status' => 'success', 'result' => $result]);
//     } else {
//         return response()->json(['status' => 'error', 'message' => 'No tokens found.']);
//     }
// }


public function notif_hp(Request $request)
{
    define('FCM_AUTH_KEY', 'AAAA5kE6iRc:APA91bF0SlLAo6VU6in0QAXkDsxIr1Cee6DP7HX-HdeR6BmTbUuDR5dRfswhLAFeBPnWe_iFymhDv4hnYrp6N8__5Ns96DQTGLF3t2-r3brXVerHfRIs1oWqtwDz5Ms9fwRyoCjwSKQk');
    
    $id_karyawan = $request->id_karyawan;
    $stat = $request->stat;
    $id_kan = $request->id_kan;

    $cek = Tambahan::where('tambahan.id',$id_kan)->first();
         if($stat == 'laporan' || $stat == 'req'){
             // ini untuk notif direktur
            if (Auth::user()->presensi == 'kacab'){
                  $kar = user::leftjoin('udev', 'udev.id_user', '=', 'users.id')
                    ->where('users.id_karyawan', '8210021201101')
                      //  8210021201101 id pak akhmad
                    ->get();
                    
                // ini untuk pimpinan    
            }else if($cek && $cek->id_pimpinan != null){
                 $kar = user::leftjoin('udev', 'udev.id_user', '=', 'tambahan.id_karyawan')
                    ->get();
            }else{
                // ini untuk notif SPV
                $kar = user::leftjoin('udev', 'udev.id_user', '=', 'users.id')
                    ->where('users.id_karyawan', '8210021201101')
                    //  8210021201101 id pak akhmad
                    ->get();
            }
          }else{
                  $kar = user::leftjoin('udev', 'udev.id_user', '=', 'users.id')
                        ->where('users.id_karyawan', $id_karyawan)
                        ->get();
          }
           
           
  

     if($stat == 'laporan'){
         $body = 'Ada laporan baru yang masuk' ;
     }else if($stat == 'req'){
          $body = 'Ada request baru yang masuk';
     }else{
        $body = 'Ada tugas baru untuk anda silahkan cek list tugas laporan ';
     }
   
    $tokens = [];
    $title = [];
  
    foreach ($kar as $val) {
        $tokens = [$val->token];
        $title = 'Hallo ' . $val->name;
        
        $postdata = json_encode([
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => "default",
                'color' => "#51C9C2"
            ],
            'registration_ids' => $tokens,
            'data' => [
                'click_action' => 'LIFIYK',
            ],
        ]);
    
        $opts = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/json' . "\r\n" .
                            'Authorization: key=' . FCM_AUTH_KEY . "\r\n",
                'content' => $postdata
            )
        );
    
        $context  = stream_context_create($opts);
        $result = file_get_contents('https://fcm.googleapis.com/fcm/send', false, $context);
    }
     return response()->json(['status' => 'success', 'result' => $result,'karyawan'=>$kar]);
}


public function notif_konfir(Request $request)
{
    define('FCM_AUTH_KEY', 'AAAA5kE6iRc:APA91bF0SlLAo6VU6in0QAXkDsxIr1Cee6DP7HX-HdeR6BmTbUuDR5dRfswhLAFeBPnWe_iFymhDv4hnYrp6N8__5Ns96DQTGLF3t2-r3brXVerHfRIs1oWqtwDz5Ms9fwRyoCjwSKQk');
    
    $id_karyawan = $request->id_karyawan;
    $stat = $request->stat;
    $id_kan = $request->id_kan;
    $konf = $request->konfir;

    $cek = Tambahan::where('tambahan.id',$id_kan)->first();
    //  return response()->json(['request' => $request->all(), 'cek' => $cek]);

             if($stat == 'laporan' || $stat == 'req'|| $stat == 'laporaned'){
            if ($cek && $cek->id_pimpinan != null){
                
                //   $kar = user::leftjoin('udev', 'udev.id_user', '=', 'users.id')
                //     ->where('users.id_karyawan', '9902242203103')
                //     ->get();
                    
                $kar = user::leftjoin('udev', 'udev.id_user', '=', 'tambahan.id_karyawan')
                    ->get();
            }else{
                // ini untuk notif 
                $kar = user::leftjoin('udev', 'udev.id_user', '=', 'users.id')
                    ->where('users.id_karyawan', '8210021201101')
                    //  8210021201101 id pak akhmad
                    ->get();
            }
          }else{
                  $kar = user::leftjoin('udev', 'udev.id_user', '=', 'users.id')
                        ->where('users.id_karyawan', $id_karyawan)
                        ->get();
          }

          foreach ($kar as $val) {
                $gtnama = $val->name;
          }
     
     if($stat == 'laporan'){
         if($konf == 1){
            $body = 'Laporan anda telah di Acc oleh atasan' ;
         }else{
            $body = 'Laporan anda ada yang harus di perbaiki' ;
         }
         }else if($stat == 'laporaned'){
               $body = 'Ada laporan yang di edit silahkan Cek laporan'. $gtnama ;
         }else{
              if($konf == 1){
                $body = 'Request anda telah di ACC' ;
             }else{
                $body = 'Request anda telah di Tolak' ;
             }
         }
   
    $tokens = [];
    $title = [];
  
    foreach ($kar as $val) {
        $tokens = [$val->token];
        $title = 'Hallo ' . $val->name;
        
        $postdata = json_encode([
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => "default",
                'color' => "#51C9C2"
            ],
            'registration_ids' => $tokens,
            'data' => [
                'click_action' => 'LIFIYK',
            ],
        ]);
    
        $opts = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/json' . "\r\n" .
                            'Authorization: key=' . FCM_AUTH_KEY . "\r\n",
                'content' => $postdata
            )
        );
    
        $context  = stream_context_create($opts);
        $result = file_get_contents('https://fcm.googleapis.com/fcm/send', false, $context);
    }
     return response()->json(['status' => 'success', 'result' => $result ,'nama'=>$gtnama ,'kar' => $kar]);
}

public function acc_req(Request $request){
    // $old_req    = $request->oldData != '%5B%5D' ?  json_decode (urldecode($request->oldData)) : [];
    // $new_req    = $request->newData != '%5B%5D' ?  json_decode (urldecode($request->newData)) : [];

 
    // return $request->input('newData');
        // return response()->json(['message' => $request->all()]);
  $new_req = json_decode($request->input('newData'), true);
    // return $new_req['id_request'];
  $oldData = json_decode($request->input('oldData'), true);
  $ubahStatus = $request->input('ubahStatus');
  $use_req    = $request->ubahStatus == false ? (object) $oldData :  (object) $new_req;
    // foreach($use_req as $req){
        // return($use_req->id_request);
        // return($use_req['id_presensi']);
        $id         = $use_req->id_request;
        $datareq    = RequestKar::find($id);
        // return $datareq;
        
        $tanggal    = date('Y-m-d', strtotime($use_req->tanggal ?? date('Y-m-d')));    
        $jam        = date('H:i:s', strtotime($datareq->created_at));
        $jen        = Jenreq::find($datareq->id_status);
        
        $pres       = Presensi::whereDate('created_at', $tanggal)->where('id_karyawan', $datareq->id_karyawan)->first();
        $lap        = Laporan::whereDate('created_at', $tanggal)->where('id_karyawan', $datareq->id_karyawan)->first();
        $id_pres    = $pres != null ? $pres->id_presensi : 0;

        // return response()->json(['status' => $datareq->kategori, 'message' => $request->hasFile('lampiran')]);
        if($datareq->kategori == 'dana'){
                // **Handle Upload File jika ada**
            if ($request->hasFile('lampiran')) {
                $fileName = $request->file('lampiran')->getClientOriginalName();
                $request->file('lampiran')->move('gambarLampiran', $fileName);
                $datareq->update(['bukti_from_admin' => $fileName]);
            }else{
                return response()->json(['status' => 'gagal', 'message' => 'file tidak ditemukan']);
            }
            
            $datareq->acc            = 1;
            $datareq->user_update    = Auth::user()->id;
            $datareq->update();
            
            return response()->json(['status' => 'sukses', 'message' => 'Berhasil acc reimburse']);
            
            
        
        }


        if($datareq->kategori == 'shift'){
            User::where('id_shift', $datareq->id_shift)->update(['id_shift' => $datareq->id_shift, 'shift'=> $datareq->shift, 'acc_shift' => 1]);
            Presensi::where('id_shift', $datareq->id_shift)->update(['acc_shift' => 1]);
        }else if($datareq->kategori == 'perbaikan'){ 
            
            // return response()->json(['status' => $datareq->jamci, 'messa' => $datareq->jamco, 'ee' =>  $datareq->jamco == "null" ]);
            $data = Presensi::find($id_pres);
            $data->id_request   = $id;
            $data->ket          = $datareq->ket;
            $data->jumlah       = $datareq->jumlah;
            $data->status       = $datareq->status;
            if($datareq->id_status == 0){
                $data->cek_in   = $datareq->jamci == "null" ? null : $datareq->jamci ;
                $data->break_out  =  $datareq->jambo == "null" ?  null : $datareq->jambo;
                $data->break_in  =  $datareq->jambi == "null" ? null :$datareq->jambi ;
                $data->cek_out  = $datareq->jamco == "null" ?   null : $datareq->jamco;
            }
            $data->acc          = $datareq->jamco == "null" ? 0 : 1;
            $data->update();
        }else if(optional($jen)->statpres == 'dengan' || (optional($jen)->statpres == 'opsional' && $id_pres > 0)){
            $data = Presensi::find($id_pres);
            $data->id_request   = $id;
            $data->cek_out      = $lap != null && $data->cek_out == null ? $jam : $data->cek_out;
            $data->acc          = 1;
            $data->update();
        }else if((optional($jen)->statpres == 'tanpa' || optional($jen)->statpres == 'opsional') && $id_pres == 0){
            $data = new Presensi;
            $data->id_karyawan  = $datareq->id_karyawan;
            $data->id_jabatan   = $datareq->id_jabatan;
            $data->pr_jabatan   = $datareq->pr_jabatan;
            $data->id_kantor    = $datareq->id_kantor;
            $data->kantor_induk = $datareq->kantor_induk;
            $data->nama         = $datareq->nama;
            $data->ket          = $datareq->ket;
            $data->status       = $datareq->status;
            $data->jumlah       = $datareq->jumlah;
            $data->created_at   = $tanggal . ' ' . $jam ;
            $data->id_req       = $id;
            $data->acc          = 1;
            $data->lampiran     = $datareq->lampiran;
            $data->foto         = $datareq->foto;
            $data->latitude     = $datareq->latitude;
            $data->longitude    = $datareq->longitude;
            $data->save();
        }
        if($request->ubahStatus == 'true'){
            $datareq->status         = $use_req->status;
            $datareq->id_status      = $use_req->id_jenreq;     
        }
        $datareq->acc            = 1;
        $datareq->alasan         = null;
        $datareq->user_update    = Auth::user()->id;
        $datareq->update();
    // }
    return response()->json(
        [
            "status"=>"sukses",
        ]
    );
}

public function reject_req( Request $request)
{
    
    $id = $request->id_request;
    $datareq = RequestKar::find($id);
    
    
    //   return response()->json(
    //     [
    //         "status"=>"sukses",
    //         "datareq" =>$datareq,
    //         "all" =>$request->all()
    //     ]
    // );
    
    $pres = Presensi::where('id_reqbolos', $id)->get();
    if(count($pres) > 0){
        $up_pres = Presensi::where('id_reqbolos', $id)->update(['ket' => '(Request '.$datareq->status.' Ditolak Dengan Alasan : '.$datareq->alasan.') | '.$datareq->ket, 'status' => 'Bolos']);
    }
    
    if($datareq->acc = 1){
    if($request->status == 'Pulang Awal'){
        $data = Presensi::find($request->id_presensi);
        $data->id_request = null;
        $data->cek_out = null;
        $data->acc = 0;
    
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Menolak Request '.$request->nama.' Pulang Awal');
        
    }else if($request->status == 'Dinas Luar'){
        $data = Presensi::find($request->id_presensi);
        $data->id_request = null;
    
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Menolak Request '.$request->nama.' Dinas Luar');
    }else if($request->status == 'Pindah Shift'){
        
        User::where('id_karyawan', $datareq->id_karyawan)->update(['id_shift' => $datareq->id_shift, 'acc_shift' => 2]);
        Presensi::where('id_shift', $datareq->id_shift)->update(['acc_shift' => 2]);
        
        \LogActivity::addToLog(Auth::user()->name.' Menolak Request '.$request->nama.' Pindah Shift');
    }else if($request->status == 'Perdin' && $request->id_presensi != ''){
        $data = Presensi::find($request->id_presensi);
        $data->id_request = null;
        $data->cek_out = null;
        $data->acc = 0;
    
        $data->update();
        \LogActivity::addToLog(Auth::user()->name.' Menolak Request '.$request->nama.' Perdin');
    }else if($request->status == 'perbaikan'){
        
    //   \LogActivity::addToLog(Auth::user()->name.' Menolak Request '.$request->nama.' Perbaikan');
    }else{
        $data = Presensi::where('id_req', $id)->update(['ket' => '(Request '.$datareq->status.' Ditolak Dengan Alasan : '.$datareq->alasan.') | '.$datareq->ket, 'status' => 'Bolos']);
    }
    
    }
    
    $datareq->alasan = $request->alasan;
    $datareq->acc = 2;
    $datareq->user_update = Auth::user()->id;
    $datareq->update();
    
    return response()->json(
        [
            "status"=>"sukses"
        ]
    );
}

public function voting( Request $request)
{
        $id_kantor = $request->id_kantor;
        $id_com = $request->id_com;
        $id_jabatan = $request->id_jab;
        $today = Carbon::today();
        // return($tgl);
        $datavot = Voting::where('id_com', $id_com)->where('aktif', 1)
         ->whereDate('tgl_awal', '<=', $today)
         ->whereDate('tgl_akhir', '>=', $today)->get();
        
        $data = [];
        foreach ($datavot as $v) {
            $ditunjukan = unserialize($v->ditunjukan);
            $id_kantor_arr = unserialize($v->id_kantor);
            if (is_array($ditunjukan) && is_array($id_kantor_arr)) {
                if (in_array($id_jabatan, $ditunjukan) && in_array($id_kantor, $id_kantor_arr)) {
                    $jumlah_voting = unserialize($v->jumlah_voting);
                    $voting = unserialize($v->voting);
                    $user = unserialize($v->user_vote);
        
                    $data[] = [
                        'id' => $v->id,
                        'judul' => $v->judul,
                        'ditunjukan' => $ditunjukan,
                        'id_kantor' => $id_kantor_arr,
                        'jumlah_voting' => $jumlah_voting,
                        'voting' => $voting,
                        'user_vote' => $user,
                        'aktif' => $v->aktif,
                        'id_com' => $v->id_com,
                    ];
                }
            }
        }
        // return response()->json($data);
        return ($data);
       
    // $result = $data->filter(function ($item) use ($id_kantor, $id_com, $id_jabatan) {
    //     $item->ditunjukan = unserialize($item->ditunjukan);
    //     $item->id_kantor = unserialize($item->id_kantor);
    //     $item->jumlah_voting = unserialize($item->jumlah_voting);
    //     $item->voting = unserialize($item->voting);
        
    //   return $item->id_com == $id_com &&
    //           in_array($id_kantor, $item->id_kantor) &&
    //           in_array($id_jabatan, $item->ditunjukan);
    // });

    // return response()->json($result);

  
}
public function post_vote( Request $request)
{
        $jumlah_vote = json_decode($request->jumlah_voting);
        $user_vote = json_decode($request->user_vote);
      
        $id = $request->id;
        
        $data                    = Voting::find($id);
        $data->jumlah_voting     = serialize($jumlah_vote);
        $data->user_vote         = serialize($user_vote);
        $data->update();
       
        return response()->json(
        [
            "status"=>"sukses",
            "data" =>$data,
            "data_jumlah_vote"=> serialize($jumlah_vote),
            "data_user_vote"=> serialize($user_vote) 
        ]
    );
   
  
}
}


