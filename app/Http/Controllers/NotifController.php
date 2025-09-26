<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kolektors;
use App\Models\Karyawan;
use App\Models\Kinerja;
use App\Models\Donatur;
use App\Models\Transaksi;
use App\Models\Transaksi_Perhari;
use App\Models\Kantor;
use App\Models\User;
use App\Models\Udev;
use App\Models\Tunjangan;
use App\Models\Notiftest;
use Carbon\Carbon;
use Auth;
use DB;
use App\Exports\AnalisisExport;
use App\Exports\AnalisisDetailExport;
use App\Exports\DetailAnalisisDonaturExport;
use Excel;
use PDF;
use Illuminate\Support\Facades\Http;

use DataTables;

class NotifController extends Controller
{
    
    public function cekdataa(){
        
        $response = Http::get('https://berbagipendidikan.org/sim/api/getuser');

        // // Periksa apakah respons berhasil
        // if ($response->successful()) {
        //     // Dapatkan data dari respons
        //     $data = $response->json();

        //     // Tampilkan data ke view atau kembalikan sebagai JSON
        //     return response()->json([
        //         'status' => 'success',
        //         'data' => $response->body()
        //     ]);
        // }

        // // Tangani jika respons gagal
        // return response()->json([
        //     'status' => 'error',
        //     'message' => 'Failed to fetch data from API',
        // ], $response->status());
        
        return $response->body();
        
    }
    
    public function notif(Request $request){
        
         $data = Udev::leftJoin('users', 'udev.id_user', '=', 'users.id')
                ->select('udev.token', 'users.name')
                ->get();

          if($request->ajax())
        {
   
                    
            // return $datas;
        }
        
              
        return view('notif.index',compact('data'));  

    }
    
    
    
    // public function testing_saja(Request $request){
        
    //     define('FCM_AUTH_KEY', 'AAAA5kE6iRc:APA91bF0SlLAo6VU6in0QAXkDsxIr1Cee6DP7HX-HdeR6BmTbUuDR5dRfswhLAFeBPnWe_iFymhDv4hnYrp6N8__5Ns96DQTGLF3t2-r3brXVerHfRIs1oWqtwDz5Ms9fwRyoCjwSKQk');
    //     $token = $request->token;
    //     $pesan = $request->pesan;

    //     $data = Notiftest::where('token',$token)->get();
        
    //   $title = 'Default Title';
    //     foreach($data as $i => $val){
    //          $h1[] = [
    //             "nama" => $val->nama,
    //         ];
            
    //     $title = 'Hallo ' . $val->nama ;
    //     }
    
    // 	$postdata = json_encode(
    // 	    [
    // 	        'notification' => 
    // 	        	[
    // 	        		'title' => $title ,
    // 	        		'body' => $pesan,
    // 	       // 		'icon' => $icon,
    // 	       // 		'click_action' => $url
    // 	                'sound' => "default",
    // 	        	]
    // 	        ,
    // 	        'to' => $token,
    // 	        'data' => [
    //                     'click_action' => 'LIFIYK',
    //                 ],
    // 	    ]
    	    
    // 	);
    
    // 	$opts = array('http' =>
    // 	    array(
    // 	        'method'  => 'POST',
    // 	        'header'  => 'Content-type: application/json'."\r\n"
    // 	        			.'Authorization: key='.FCM_AUTH_KEY."\r\n",
    // 	        'content' => $postdata
    // 	    )
    // 	);
    
    // 	$context  = stream_context_create($opts);
    
    // 	$result = file_get_contents('https://fcm.googleapis.com/fcm/send', false, $context);
    	

    // }
    
    
  public function notif_hp(Request $request)
{
    define('FCM_AUTH_KEY', 'AAAA5kE6iRc:APA91bF0SlLAo6VU6in0QAXkDsxIr1Cee6DP7HX-HdeR6BmTbUuDR5dRfswhLAFeBPnWe_iFymhDv4hnYrp6N8__5Ns96DQTGLF3t2-r3brXVerHfRIs1oWqtwDz5Ms9fwRyoCjwSKQk');
    
    $id_karyawan = $request->id_karyawan;
    // $pesan = $request->pesan;
    $kar = user::leftjoin('udev', 'udev.id_user', '=', 'users.id')
                // ->leftjoin('udev', 'udev.id_user', '=', 'users.id')
                ->where('users.id_karyawan', $id_karyawan)
               ->get();
               
    $tokens = [];
    $title = 'Hallo';

    foreach ($data as $val) {
        $tokens[] = $val->token;
        $title = 'Hallo ' . $val->nama;
    }

   if (!empty($tokens)) {
        $postdata = json_encode(
            [
                'notification' => [
                    'title' => $title,
                    'body' => 'Ada Tugas Tugas baru Untuk anda',
                    'sound' => "default",
                ],
                'registration_ids' => $tokens,
                'data' => [
                    'click_action' => 'LIFIYK',
                ],
            ]
        );

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
        
        return response()->json(['status' => 'success', 'result' => $result]);
    } else {
        return response()->json(['status' => 'error', 'message' => 'No tokens found.']);
    }
}


 public function testing_saja(Request $request)
{
    define('FCM_AUTH_KEY', 'AAAA5kE6iRc:APA91bF0SlLAo6VU6in0QAXkDsxIr1Cee6DP7HX-HdeR6BmTbUuDR5dRfswhLAFeBPnWe_iFymhDv4hnYrp6N8__5Ns96DQTGLF3t2-r3brXVerHfRIs1oWqtwDz5Ms9fwRyoCjwSKQk');
    
    $token = $request->token;
    $pesan = $request->pesan;

    $data = Notiftest::where('token', $token)->get();
   

    foreach ($data as $val) {
        $h1[] = [
            "nama" => $val->nama,
        ];
        $title = 'Hallo ' . $val->nama;
    }

    $postdata = json_encode(
        [
            'notification' => [
                // 'title' => $title,
                'body' => $pesan,
                'sound' => "default",
            ],
            'to' => $token,
            'data' => [
                'click_action' => 'LIFIYK',
            ],
        ]
    );

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

}
