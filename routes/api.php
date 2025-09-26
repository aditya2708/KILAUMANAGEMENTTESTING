<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiUserKolektorController;
use App\Http\Controllers\ApiUserKaryawanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\MidtransController;
use App\Http\Controllers\DonaturApiController;
// api bp, klinik, dan bb
use App\Http\Controllers\AllRoundApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('daftar-post', [AuthController::class, 'daftar_post']);


Route::get('coa', [AllRoundApiController::class, 'getCoa']);
Route::get('get-company', [AllRoundApiController::class, 'getCompany']);
Route::post('create-company', [AllRoundApiController::class, 'createCompany']);
Route::post('createUser',[AllRoundApiController::class, 'createUser']);
Route::post('createUserbp',[AllRoundApiController::class, 'createUserBp']);


Route::post('masuk',[AllRoundApiController::class, 'masuk']);
Route::post('updateUser',[AllRoundApiController::class, 'updateUser']);
Route::get('cekTokenUser',[AllRoundApiController::class, 'cekTokenUser']);
Route::get('cari_donatur',[AllRoundApiController::class, 'cari_donatur']);
Route::get('listapp',[AllRoundApiController::class, 'listapp']);
Route::get('apilaporan',[AllRoundApiController::class, 'apilaporan']);
Route::post('login_sso',[AllRoundApiController::class, 'loginsso']);
Route::get('companys', [ApiUserKaryawanController::class, 'companys']);
Route::get('datKar', [AllRoundApiController::class, 'datKar']);
Route::get('donitung', [AllRoundApiController::class, 'count_donatur']);

Route::get('test', function(){
    return response()->json(['heheh' => 'heheh']);
});
Route::post('get_token_midtrans_sb', [ApiUserKaryawanController::class, 'getTokenMidtransSandbox']);
Route::post('get_token_midtrans_kliniq', [ApiUserKaryawanController::class, 'getTokenMidtransKliniq']);
Route::post('midtrans-callback-post', [ApiUserKaryawanController::class, 'handleMidtransCallback']);

Route::get('sukses', function () {
    return view('halaman-gabut.sukses');
});

Route::get('error', function () {
    return view('halaman-gabut.errors');
});

Route::get('maintenance',function () {
    return view('halaman-gabut.mainten');
});

Route::get('not-found',function () {
    return view('halaman-gabut.notfon');
});

Route::get('v1/getkolek','KolektorController@index');
Route::post('v1/postkolektor','KolektorController@store');
Route::delete('kolektor/{kolektor}','KolektorController@destroy');
Route::patch('kolektor/{kolektor}','KolektorController@update');
Route::get('kolektor/{kolektor}/edit','KolektorController@edit');

Route::post('donatur',[ApiUserKolektorController::class, 'donatur']); 
Route::get('cek_donatur',[DonaturApiController::class, 'cek_donatur']); 


Route::post('charge',[PaymentController::class, 'charge']);
Route::get('v1/getdonatur','DonaturApiController@index');
Route::post('v1/getlogin',[ApiUserKolektorController::class, 'getlogin']);
Route::post('v1/login',[ApiUserKolektorController::class, 'login']);
Route::get('v1/getuser',[ApiUserKolektorController::class, 'index']);
Route::get('v1/getkolektors',[ApiUserKolektorController::class, 'loginuser']);
Route::get('getprofile',[ApiUserKolektorController::class, 'profile'])->middleware('auth:userkol');
Route::get('assigment',[ApiUserKolektorController::class, 'assigment'])->middleware('auth:userkol');
Route::get('assignment',[ApiUserKolektorController::class, 'assignment'])->middleware('auth:userkol');
Route::get('assignment2',[ApiUserKolektorController::class, 'assignment2'])->middleware('auth:userkol');
Route::get('riwayat',[ApiUserKolektorController::class, 'riwayat'])->middleware('auth:userkol');
Route::get('target',[ApiUserKolektorController::class, 'target_kunjungan'])->middleware('auth:userkol');
Route::put('updates',[ApiUserKolektorController::class, 'updates'])->middleware('auth:userkol');
Route::post('laporan',[ApiUserKolektorController::class, 'add'])->middleware('auth:userkol');
Route::put('updates',[ApiUserKolektorController::class, 'updates'])->middleware('auth:userkol');
Route::get('edites/{id}',[ApiUserKolektorController::class, 'editdonatur'])->middleware('auth:userkol');
Route::post('edites/{id}',[ApiUserKolektorController::class, 'updatedonatur'])->middleware('auth:userkol');
Route::post('lapor',[ApiUserKolektorController::class, 'laporankuy'])->middleware('auth:userkol');
Route::post('transaksi',[ApiUserKolektorController::class, 'transaksi'])->middleware('auth:userkol');
Route::post('transaksi2',[ApiUserKolektorController::class, 'transaksi'])->middleware('auth:userkol');
Route::get('getransaksi',[ApiUserKolektorController::class, 'getrf']);
Route::get('transkol/{id}',[ApiUserKolektorController::class, 'transkol']);
Route::get('transdon/{id}',[ApiUserKolektorController::class, 'transdon']);
Route::post('updateuser/{id}',[ApiUserKolektorController::class, 'updateuser'])->middleware('auth:userkol');
Route::get('tambahan',[ApiUserKolektorController::class, 'tambahan']);
Route::post('uploc/{id}',[ApiUserKolektorController::class, 'updatelokasi'])->middleware('auth:userkol');
Route::get('program',[ApiUserKolektorController::class, 'program']);
Route::get('getdonatur/{jalur}/{status}',[ApiUserKolektorController::class, 'getdonatur'])->middleware('auth:userkol');
Route::get('listassign/{kan}/{jal}/{status}/{acc}',[ApiUserKolektorController::class, 'listassign'])->middleware('auth:userkol');
Route::get('listassignnow/{kan}/{jal}/{status}/{acc}',[ApiUserKolektorController::class, 'listassignnow'])->middleware('auth:userkol');
Route::post('uploadpdf',[ApiUserKolektorController::class, 'uploadpdf']);
Route::post('assigndon/{id}',[ApiUserKolektorController::class, 'assigndon'])->middleware('auth:userkol');
Route::get('getpetugas',[ApiUserKolektorController::class, 'getpetugas'])->middleware('auth:userkol');
Route::post('updon/{id}',[ApiUserKolektorController::class, 'updon'])->middleware('auth:userkol');
Route::post('updonass/{id}',[ApiUserKolektorController::class, 'updonass'])->middleware('auth:userkol');
Route::get('getjalur',[ApiUserKolektorController::class, 'getjalur'])->middleware('auth:userkol');
Route::post('assignall/{jalur}/{status}',[ApiUserKolektorController::class, 'assignall'])->middleware('auth:userkol');
Route::post('postdon',[ApiUserKolektorController::class, 'postdon'])->middleware('auth:userkol');
Route::get('getmapdon/{jalur}/{status}',[ApiUserKolektorController::class, 'getmapdon'])->middleware('auth:userkol');
Route::post('changestatusdon/{id}',[ApiUserKolektorController::class, 'changestatus'])->middleware('auth:userkol');
Route::get('dontempnow',[ApiUserKolektorController::class, 'dontempnow'])->middleware('auth:userkol');
Route::post('updonatur/{id}',[ApiUserKolektorController::class, 'updonatur'])->middleware('auth:userkol');
Route::get('getdonid/{id}/{ket}',[ApiUserKolektorController::class, 'getdonid'])->middleware('auth:userkol');
Route::post('lapfol',[ApiUserKolektorController::class, 'postlapfol'])->middleware('auth:userkol');
Route::get('donopen',[ApiUserKolektorController::class, 'donopen'])->middleware('auth:userkol');
Route::get('donalliso',[ApiUserKolektorController::class, 'donalliso'])->middleware('auth:userkol');
Route::get('donrangeisoall/{stat}/{tanggal1}/{tanggal2}',[ApiUserKolektorController::class, 'donrangeisoall'])->middleware('auth:userkol');
Route::get('getprog',[ApiUserKolektorController::class, 'getprog'])->middleware('auth:userkol');
Route::get('getsumdan',[ApiUserKolektorController::class, 'getsumdan'])->middleware('auth:userkol');
Route::get('donspv/{ket}/{acc}/{tgl1}/{tgl2}',[ApiUserKolektorController::class, 'donspv'])->middleware('auth:userkol');
Route::get('doncab/{ket}/{acc}/{tgl1}/{tgl2}',[ApiUserKolektorController::class, 'doncab'])->middleware('auth:userkol');
Route::get('getprov',[ApiUserKolektorController::class, 'getprov'])->middleware('auth:userkol');
Route::get('getkota',[ApiUserKolektorController::class, 'getkota'])->middleware('auth:userkol');
Route::post('kondon/{id}',[ApiUserKolektorController::class, 'kondon'])->middleware('auth:userkol');
Route::get('assiso',[ApiUserKolektorController::class, 'assiso'])->middleware('auth:userkol');
Route::get('testo',[ApiUserKolektorController::class, 'testo']);
Route::post('posttrans',[ApiUserKolektorController::class, 'posttrans'])->middleware('auth:userkol');
Route::get('dondeket/{lat}/{long}/{rad}/{jml}',[ApiUserKolektorController::class, 'dondeket'])->middleware('auth:userkol');
Route::post('upprogdon/{id}',[ApiUserKolektorController::class, 'upprogdon'])->middleware('auth:userkol');
Route::get('warfolup',[ApiUserKolektorController::class, 'warfolup'])->middleware('auth:userkol');
Route::get('listlapfol/{id}/{tanggal1}/{tanggal2}',[ApiUserKolektorController::class, 'listlapfol'])->middleware('auth:userkol');
Route::get('getdoncab/{kan}/{jal}',[ApiUserKolektorController::class, 'getdoncab'])->middleware('auth:userkol');
Route::get('rejectdon/{tgl1}/{tgl2}',[ApiUserKolektorController::class, 'rejectdon'])->middleware('auth:userkol');
Route::post('resenddon/{id}/{id_laphub}',[ApiUserKolektorController::class, 'resenddon'])->middleware('auth:userkol');
Route::get('cekdon/{cek}/{val}',[ApiUserKolektorController::class, 'cekdon'])->middleware('auth:userkol');
Route::get('getkantorcab',[ApiUserKolektorController::class, 'getkantorcab'])->middleware('auth:userkol');
Route::get('getjalurcab/{kan}',[ApiUserKolektorController::class, 'getjalurcab'])->middleware('auth:userkol');
Route::post('izinupdon/{id}/{up}',[ApiUserKolektorController::class, 'izinupdon'])->middleware('auth:userkol');
Route::get('getbank',[ApiUserKolektorController::class, 'getbank'])->middleware('auth:userkol');
Route::get('getdontok/{tok}',[ApiUserKolektorController::class, 'getdontok'])->middleware('auth:userkol');
Route::get('getdontup/{retup}',[ApiUserKolektorController::class, 'getdontup'])->middleware('auth:userkol');
Route::post('uptupdon/{id}',[ApiUserKolektorController::class, 'uptupdon'])->middleware('auth:userkol');
Route::get('doncan',[ApiUserKolektorController::class, 'doncan']);
Route::post('postjalur',[ApiUserKolektorController::class, 'postjalur'])->middleware('auth:userkol');
Route::post('upjalur/{id}',[ApiUserKolektorController::class, 'upjalur'])->middleware('auth:userkol');
Route::post('deljalur/{id}',[ApiUserKolektorController::class, 'deljalur'])->middleware('auth:userkol');
Route::get('dono/{ket}/{tgl1}/{tgl2}',[ApiUserKolektorController::class, 'dono'])->middleware('auth:userkol');
Route::get('donatour',[ApiUserKolektorController::class, 'donatour'])->middleware('auth:userkol');
Route::get('donid/{id}',[ApiUserKolektorController::class, 'donid'])->middleware('auth:userkol');
Route::get('bonsales/{id}',[ApiUserKolektorController::class, 'bonsales']);
Route::get('getcoaper',[ApiUserKolektorController::class, 'getcoaper']);
Route::post('posttrandon',[ApiUserKolektorController::class, 'posttrandon'])->middleware('auth:userkol');
Route::get('gettrandon/{id}',[ApiUserKolektorController::class, 'gettrandon']);
Route::get('rwytrans',[ApiUserKolektorController::class, 'rwytrans'])->middleware('auth:userkol');
Route::get('rwytrans2',[ApiUserKolektorController::class, 'rwytrans2'])->middleware('auth:userkol');
Route::get('dontup',[ApiUserKolektorController::class, 'dontup'])->middleware('auth:userkol');
Route::get('getver',[ApiUserKolektorController::class, 'getver']);
Route::get('getprostam/{id}',[ApiUserKolektorController::class, 'getprostam']);
Route::post('prospdon',[ApiUserKolektorController::class, 'prospdon'])->middleware('auth:userkol');
Route::post('upprosdon',[ApiUserKolektorController::class, 'upprosdon'])->middleware('auth:userkol');
Route::get('getkotprov',[ApiUserKolektorController::class, 'getkotprov']);
Route::get('getprosdon/{ket}',[ApiUserKolektorController::class, 'getprosdon']);
Route::post('tamjalur',[ApiUserKolektorController::class, 'tamjalur'])->middleware('auth:userkol');
Route::post('postfolup',[ApiUserKolektorController::class, 'postfolup'])->middleware('auth:userkol');
Route::get('donclos/{ket}/{tgl1}/{tgl2}',[ApiUserKolektorController::class, 'donclos'])->middleware('auth:userkol');
Route::post('upnoloc/{id}',[ApiUserKolektorController::class, 'upnoloc'])->middleware('auth:userkol');
Route::post('postprospdon',[ApiUserKolektorController::class, 'postprospdon'])->middleware('auth:userkol');
Route::post('postprospdon2',[ApiUserKolektorController::class, 'postprospdon2'])->middleware('auth:userkol');
Route::get('getprosnow',[ApiUserKolektorController::class, 'getprosnow'])->middleware('auth:userkol');
Route::get('getprosnow2/{id}/{ket}',[ApiUserKolektorController::class, 'getprosnow2']);
Route::get('getpenup',[ApiUserKolektorController::class, 'getpenup'])->middleware('auth:userkol');
Route::get('getrekup/{acc}/{tgl1}/{tgl2}',[ApiUserKolektorController::class, 'getrekup'])->middleware('auth:userkol');
Route::get('getspvkol',[ApiUserKolektorController::class, 'getspvkol'])->middleware('auth:userkol');
Route::post('accupdon/{id}',[ApiUserKolektorController::class, 'accupdon'])->middleware('auth:userkol');
// Route::get('gettj',[ApiUserKolektorController::class, 'gettj');
Route::get('gettj',[ApiUserKolektorController::class, 'gettj']);
Route::get('getwa/{id}',[ApiUserKolektorController::class, 'getwa']);
Route::get('profilekol',[ApiUserKolektorController::class, 'profilekol'])->middleware('auth:userkol');
Route::get('dondeketdup/{lat}/{long}/{mail}/{no}',[ApiUserKolektorController::class, 'dondeketdup']);
Route::get('namdonclos/{ket}/{nam}',[ApiUserKolektorController::class, 'namdonclos'])->middleware('auth:userkol');
Route::get('td',[ApiUserKolektorController::class, 'trial_data']);
Route::get('listkondon/{ket}/{acc}/{tgl1}/{tgl2}',[ApiUserKolektorController::class, 'listkondon'])->middleware('auth:userkol');
Route::get('gettranpros/{arid}/{id}',[ApiUserKolektorController::class, 'gettranpros']);
Route::get('cekmovedon/{lat}/{long}/{name}/{no}/{pay}',[ApiUserKolektorController::class, 'cekmovedon']);
Route::get('cronwarningdonkil', [ApiUserKolektorController::class, 'cronwarningdonkil']);
Route::get('cronassignkil', [ApiUserKolektorController::class, 'cronassignkil']);
 
Route::post('v1/loginkaryawan','KaryawanController@karyawan');


// Route::post('v1/login',[ApiUserKolektorController::class, 'login');
// Route::get('v1/getuser',[ApiUserKolektorController::class, 'index');
// Route::get('v1/getkolektors',[ApiUserKolektorController::class, 'loginuser');
// Route::get('getprofile',[ApiUserKolektorController::class, 'profile')->middleware('auth:userkol');
Route::post('loginkar', [ApiUserKaryawanController::class, 'login']);
Route::post('loginsso', [ApiUserKaryawanController::class, 'loginsso']);
Route::post('logindevkar', [ApiUserKaryawanController::class, 'logindev']);
Route::post('logoutdevkar', [ApiUserKaryawanController::class, 'logoutdev'])->middleware('auth:userkol');
Route::get('getprofilekar', [ApiUserKaryawanController::class, 'profile'])->middleware('auth:userkol');
Route::get('profilekar', [ApiUserKaryawanController::class, 'profilekar'])->middleware('auth:userkol');
Route::get('presnowid/{id}', [ApiUserKaryawanController::class, 'presnowid'])->middleware('auth:userkol');
Route::get('presmonthnow/{id}', [ApiUserKaryawanController::class, 'presmonthnow'])->middleware('auth:userkol');
Route::get('getkantor', [ApiUserKaryawanController::class, 'getkantor'])->middleware('auth:userkol'); //hapus_ini
Route::get('getkantors/{id}', [ApiUserKaryawanController::class, 'getkantors'])->middleware('auth:userkol');
Route::post('postpres', [ApiUserKaryawanController::class, 'postpres'])->middleware('auth:userkol');
Route::post('postpresout/{id}', [ApiUserKaryawanController::class, 'postpresout'])->middleware('auth:userkol');
Route::post('uplockan/{id}', [ApiUserKaryawanController::class, 'updatelokasi'])->middleware('auth:userkol');
Route::post('postreq', [ApiUserKaryawanController::class, 'postreq'])->middleware('auth:userkol');
Route::get('tester', [ApiUserKaryawanController::class, 'tester']);
Route::get('tesapi', [ApiUserKaryawanController::class, 'tesapi']);
Route::post('postlap',  [ApiUserKaryawanController::class, 'postlap'])->middleware('auth:userkol');
Route::post('postlap2',  [ApiUserKaryawanController::class, 'postlap2'])->middleware('auth:userkol');
Route::post('post_lap_ren',  [ApiUserKaryawanController::class, 'post_lap_ren'])->middleware('auth:userkol');
Route::get('reqnowid/{id}', [ApiUserKaryawanController::class, 'reqnowid'])->middleware('auth:userkol');
Route::get('reqnowjab/{id}', [ApiUserKaryawanController::class, 'reqnowjab'])->middleware('auth:userkol');
Route::get('reqnowkan/{id}', [ApiUserKaryawanController::class, 'reqnowkan'])->middleware('auth:userkol');
Route::get('reqnowall', [ApiUserKaryawanController::class, 'reqnowall'])->middleware('auth:userkol');
Route::get('req_atasan',  [ApiUserKaryawanController::class, 'req_atasan'])->middleware('auth:userkol');
Route::get('presatasan/{id}',  [ApiUserKaryawanController::class, 'presatasan'])->middleware('auth:userkol');
Route::get('presatasankacab/{id}',  [ApiUserKaryawanController::class, 'presatasankacab'])->middleware('auth:userkol');
Route::get('presatasanadm',  [ApiUserKaryawanController::class, 'presatasanadm'])->middleware('auth:userkol');
Route::get('pres_atasan_gaji',  [ApiUserKaryawanController::class, 'pres_atasan_gaji'])->middleware('auth:userkol');
Route::get('pres_atasan',  [ApiUserKaryawanController::class, 'pres_atasan'])->middleware('auth:userkol'); //hapus_ini
Route::get('pres_atasans/{id_com}',  [ApiUserKaryawanController::class, 'pres_atasans'])->middleware('auth:userkol');
Route::post('breakoutin/{id}', [ApiUserKaryawanController::class, 'breakoutin'])->middleware('auth:userkol');
Route::post('accreq/{id}', [ApiUserKaryawanController::class, 'accreq'])->middleware('auth:userkol');
Route::get('lapnowid/{id}', [ApiUserKaryawanController::class, 'lapnowid'])->middleware('auth:userkol');
Route::get('getlapid/{id}', [ApiUserKaryawanController::class, 'getlapid'])->middleware('auth:userkol');
Route::get('get_lapid', [ApiUserKaryawanController::class, 'get_lapid'])->middleware('auth:userkol');
Route::get('getlapjab/{id}', [ApiUserKaryawanController::class, 'getlapjab'])->middleware('auth:userkol');
Route::get('getlapkan/{id}', [ApiUserKaryawanController::class, 'getlapkan'])->middleware('auth:userkol');
Route::get('getlapdir', [ApiUserKaryawanController::class, 'getlapdir'])->middleware('auth:userkol');
Route::post('editreq/{id}', [ApiUserKaryawanController::class, 'editreq'])->middleware('auth:userkol');
Route::get('lapnowjab/{id}', [ApiUserKaryawanController::class, 'lapnowjab'])->middleware('auth:userkol');
Route::get('lapnowkan/{id}', [ApiUserKaryawanController::class, 'lapnowkan'])->middleware('auth:userkol');
Route::get('lapnowall', [ApiUserKaryawanController::class, 'lapnowall'])->middleware('auth:userkol');
Route::get('lapnow_atasan', [ApiUserKaryawanController::class, 'lapnow_atasan'])->middleware('auth:userkol');
Route::post('feedback', [ApiUserKaryawanController::class, 'feedback'])->middleware('auth:userkol');
Route::post('rejectreq/{id}', [ApiUserKaryawanController::class, 'rejectreq'])->middleware('auth:userkol');
Route::get('rincianreq/{id}', [ApiUserKaryawanController::class, 'rincianreq'])->middleware('auth:userkol');
Route::get('listfeedback/{id}', [ApiUserKaryawanController::class, 'listfeedback'])->middleware('auth:userkol');
Route::get('statpres/{lev}/{id}/{tanggal1}/{tanggal2}', [ApiUserKaryawanController::class, 'statpres'])->middleware('auth:userkol');
Route::get('stat/{lev}/{id}/{tanggal1}/{tanggal2}', [ApiUserKaryawanController::class, 'stat'])->middleware('auth:userkol');
Route::get('stat_pres/{tanggal1}/{tanggal2}', [ApiUserKaryawanController::class, 'stat_pres'])->middleware('auth:userkol');
Route::get('stat_presrin/{tanggal1}/{tanggal2}', [ApiUserKaryawanController::class, 'stat_presrin'])->middleware('auth:userkol');
Route::post('updatepass/{id}', [ApiUserKaryawanController::class, 'updatepass'])->middleware('auth:userkol');
Route::get('listfeedbackid/{id}', [ApiUserKaryawanController::class, 'listfeedbackid'])->middleware('auth:userkol');
Route::get('getjamker/{hari}', [ApiUserKaryawanController::class, 'getjamker'])->middleware('auth:userkol');
Route::get('jamker/{shift}', [ApiUserKaryawanController::class, 'jamker'])->middleware('auth:userkol'); //hapus_ini
Route::get('jamkers/{shift}/{id_com}', [ApiUserKaryawanController::class, 'jamkers'])->middleware('auth:userkol');
Route::post('editjamker/{id}', [ApiUserKaryawanController::class, 'editjamker'])->middleware('auth:userkol');
Route::get('getgaji/{id}', [ApiUserKaryawanController::class, 'getgaji'])->middleware('auth:userkol');
Route::get('getgajiid/{id}', [ApiUserKaryawanController::class, 'getgajiid'])->middleware('auth:userkol');
Route::post('accgaji/{id}', [ApiUserKaryawanController::class, 'accgaji'])->middleware('auth:userkol');
Route::post('accgaji2/{id}', [ApiUserKaryawanController::class, 'accgaji2'])->middleware('auth:userkol');
Route::get('rwygajiid/{id}/{year}', [ApiUserKaryawanController::class, 'rwygajiid'])->middleware('auth:userkol');
Route::get('tahungaji', [ApiUserKaryawanController::class, 'tahungaji'])->middleware('auth:userkol');
Route::get('getgolongan', [ApiUserKaryawanController::class, 'getgolongan'])->middleware('auth:userkol');
Route::post('upkenaikan/{id}', [ApiUserKaryawanController::class, 'upkenaikan'])->middleware('auth:userkol');
Route::get('getkeluarga/{id}', [ApiUserKaryawanController::class, 'getkeluarga'])->middleware('auth:userkol');
Route::post('upkeluarga/{id}', [ApiUserKaryawanController::class, 'upkeluarga'])->middleware('auth:userkol');
Route::post('upjabatan/{id}', [ApiUserKaryawanController::class, 'upjabatan'])->middleware('auth:userkol');
Route::get('getjabatan', [ApiUserKaryawanController::class, 'getjabatan'])->middleware('auth:userkol');
Route::get('belap/{id_jab}', [ApiUserKaryawanController::class, 'belap'])->middleware('auth:userkol');
Route::get('belfeed/{id}/{id_jab}', [ApiUserKaryawanController::class, 'belfeed'])->middleware('auth:userkol');
Route::get('statgaji/{id}', [ApiUserKaryawanController::class, 'statgaji'])->middleware('auth:userkol');
Route::post('posthub', [ApiUserKaryawanController::class, 'posthub'])->middleware('auth:userkol');
Route::get('presrange/{id}/{tanggal1}/{tanggal2}', [ApiUserKaryawanController::class, 'presrange'])->middleware('auth:userkol');
Route::get('getlaprange/{id}/{tanggal1}/{tanggal2}', [ApiUserKaryawanController::class, 'getlaprange'])->middleware('auth:userkol');
Route::get('getwar_naik', [ApiUserKaryawanController::class, 'getwar_naik'])->middleware('auth:userkol');
Route::post('feeduser', [ApiUserKaryawanController::class, 'feeduser'])->middleware('auth:userkol');
Route::get('statpresid/{id}', [ApiUserKaryawanController::class, 'statpresid'])->middleware('auth:userkol');
Route::post('uptema/{id}', [ApiUserKaryawanController::class, 'uptema'])->middleware('auth:userkol');
Route::get('daftareq/{id}', [ApiUserKaryawanController::class, 'daftareq'])->middleware('auth:userkol');
Route::get('getjabid', [ApiUserKaryawanController::class, 'getjabid'])->middleware('auth:userkol');
Route::get('getuser/{act}', [ApiUserKaryawanController::class, 'getuser'])->middleware('auth:userkol'); //hapus_ini
Route::get('getusers/{act}/{id_com}', [ApiUserKaryawanController::class, 'getusers'])->middleware('auth:userkol'); 
Route::get('getkarid/{id}', [ApiUserKaryawanController::class, 'getkarid'])->middleware('auth:userkol');
Route::get('getakses', [ApiUserKaryawanController::class, 'getakses'])->middleware('auth:userkol');
Route::get('getaks/{id}', [ApiUserKaryawanController::class, 'getaks'])->middleware('auth:userkol');
Route::get('getkar', [ApiUserKaryawanController::class, 'getkar'])->middleware('auth:userkol'); //hapus_ini
Route::get('getkars/{id_com}', [ApiUserKaryawanController::class, 'getkars'])->middleware('auth:userkol');
Route::post('regakses', [ApiUserKaryawanController::class, 'regakses'])->middleware('auth:userkol');
Route::get('getuserid/{id}', [ApiUserKaryawanController::class, 'getuserid'])->middleware('auth:userkol');
Route::post('upuser/{id}', [ApiUserKaryawanController::class, 'upuser'])->middleware('auth:userkol');
Route::post('onuser/{id}', [ApiUserKaryawanController::class, 'onuser'])->middleware('auth:userkol');
Route::post('deluser/{id}', [ApiUserKaryawanController::class, 'deluser'])->middleware('auth:userkol');
Route::post('readfeed/{id}', [ApiUserKaryawanController::class, 'readfeed'])->middleware('auth:userkol');
Route::get('feedin', [ApiUserKaryawanController::class, 'feedin'])->middleware('auth:userkol');
Route::get('laprangejab/{id}/{tanggal1}/{tanggal2}', [ApiUserKaryawanController::class, 'laprangejab'])->middleware('auth:userkol');
Route::post('cekup/{id}', [ApiUserKaryawanController::class, 'cekup'])->middleware('auth:userkol');
Route::post('upshift', [ApiUserKaryawanController::class, 'upshift'])->middleware('auth:userkol');
Route::post('uncekup', [ApiUserKaryawanController::class, 'uncekup'])->middleware('auth:userkol');
Route::get('shiftuser/{shift}/{kerja}', [ApiUserKaryawanController::class, 'shiftuser'])->middleware('auth:userkol'); //hapus_ini
Route::get('shiftusers/{shift}/{kerja}/{id_com}', [ApiUserKaryawanController::class, 'shiftusers'])->middleware('auth:userkol');
Route::post('cekshift/{shift}/{kerja}', [ApiUserKaryawanController::class, 'cekshift'])->middleware('auth:userkol'); //hapus_ini
Route::post('uncekshift/{shift}/{kerja}', [ApiUserKaryawanController::class, 'uncekshift'])->middleware('auth:userkol'); //hapus_ini
Route::post('cekshifts/{shift}/{kerja}/{id_com}', [ApiUserKaryawanController::class, 'cekshifts'])->middleware('auth:userkol');
Route::post('uncekshifts/{shift}/{kerja}/{id_com}', [ApiUserKaryawanController::class, 'uncekshifts'])->middleware('auth:userkol');
Route::get('lapkol', [ApiUserKaryawanController::class, 'lapkol'])->middleware('auth:userkol');
Route::post('rejectgaji/{id}', [ApiUserKaryawanController::class, 'rejectgaji'])->middleware('auth:userkol');
Route::post('copyjamker/{id}/{shift}', [ApiUserKaryawanController::class, 'copyjamker'])->middleware('auth:userkol');
Route::get('getshift', [ApiUserKaryawanController::class, 'getshift'])->middleware('auth:userkol'); //hapus_ini
Route::get('getshifts/{id_com}', [ApiUserKaryawanController::class, 'getshifts'])->middleware('auth:userkol');
Route::get('gajikar/{m}/{Y}', [ApiUserKaryawanController::class, 'gajikar'])->middleware('auth:userkol');
Route::get('listgajikar/{id}/{m}/{Y}', [ApiUserKaryawanController::class, 'listgajikar'])->middleware('auth:userkol');
Route::post('izinup/{id}', [ApiUserKaryawanController::class, 'izinup'])->middleware('auth:userkol');
Route::get('listreqgaji', [ApiUserKaryawanController::class, 'listreqgaji'])->middleware('auth:userkol');
Route::get('listaccgaji', [ApiUserKaryawanController::class, 'listaccgaji'])->middleware('auth:userkol');
Route::get('listreqgajicab/{id}', [ApiUserKaryawanController::class, 'listreqgajicab'])->middleware('auth:userkol');
Route::get('listaccgajicab/{id}', [ApiUserKaryawanController::class, 'listaccgajicab'])->middleware('auth:userkol');
Route::post('accreqgaji/{id}', [ApiUserKaryawanController::class, 'accreqgaji'])->middleware('auth:userkol');
Route::post('upgaji/{id}', [ApiUserKaryawanController::class, 'upgaji'])->middleware('auth:userkol');
Route::post('upgajirapel', [ApiUserKaryawanController::class, 'upgajirapel'])->middleware('auth:userkol');
Route::get('getupgaji/{id}', [ApiUserKaryawanController::class, 'getupgaji'])->middleware('auth:userkol');
Route::get('lapbawahan/{pres}/{jab}/{kan}/{tgl}', [ApiUserKaryawanController::class, 'lapbawahan'])->middleware('auth:userkol');
Route::get('lapbawahankar/{id}/{tgl}', [ApiUserKaryawanController::class, 'lapbawahankar'])->middleware('auth:userkol');
Route::get('rwygajikar/{id}/{m}/{Y}', [ApiUserKaryawanController::class, 'rwygajikar'])->middleware('auth:userkol');
Route::get('getrekapkar/{id}', [ApiUserKaryawanController::class, 'getrekapkar'])->middleware('auth:userkol');
Route::get('listbayarupgaji/{stat}', [ApiUserKaryawanController::class, 'listbayarupgaji'])->middleware('auth:userkol');
Route::get('bayarupgaji/{id}/{stat}', [ApiUserKaryawanController::class, 'bayarupgaji'])->middleware('auth:userkol');
Route::post('upbayarupgaji/{id}/{tmt}/{stat}', [ApiUserKaryawanController::class, 'upbayarupgaji'])->middleware('auth:userkol');
Route::post('delap/{id}', [ApiUserKaryawanController::class, 'delap'])->middleware('auth:userkol');
Route::post('uplap/{id}', [ApiUserKaryawanController::class, 'uplap'])->middleware('auth:userkol');
Route::post('uplaper_tug', [ApiUserKaryawanController::class, 'uplaper_tug'])->middleware('auth:userkol');
Route::get('getaccu', [ApiUserKaryawanController::class, 'getaccu'])->middleware('auth:userkol');
Route::post('upaccu', [ApiUserKaryawanController::class, 'upaccu'])->middleware('auth:userkol');
Route::post('uppres/{id}', [ApiUserKaryawanController::class, 'uppres'])->middleware('auth:userkol');
Route::post('delpres/{id}', [ApiUserKaryawanController::class, 'delpres'])->middleware('auth:userkol');
Route::post('posttodo/{id}/{tgl}', [ApiUserKaryawanController::class, 'posttodo'])->middleware('auth:userkol');
Route::get('gettodo/{id}/{tgl}', [ApiUserKaryawanController::class, 'gettodo']);
Route::get('getgajiid2/{id}', [ApiUserKaryawanController::class, 'getgajiid2']);
Route::get('regakses_new', [ApiUserKaryawanController::class, 'regakses_new']);
Route::get('getcom', [ApiUserKaryawanController::class, 'getcom'])->middleware('auth:userkol');
Route::get('jenreq', [ApiUserKaryawanController::class, 'jenreq'])->middleware('auth:userkol');
Route::get('getpengumuman', [ApiUserKaryawanController::class, 'getpengumuman'])->middleware('auth:userkol');
Route::get('rekap_target', [ApiUserKaryawanController::class, 'rekap_target']);
Route::get('cronboloskil', [ApiUserKaryawanController::class, 'cronbolos']);
Route::get('cronclosingkil', [ApiUserKaryawanController::class, 'cronclosing']);
Route::get('cronbatalclosingkil', [ApiUserKaryawanController::class, 'cronbatalclosing']);
Route::get('boloskil', [ApiUserKaryawanController::class, 'cronbolos2']);
Route::get('rencananow', [ApiUserKaryawanController::class, 'rencananow']);
Route::get('rencananext', [ApiUserKaryawanController::class, 'rencananext'])->middleware('auth:userkol');
Route::get('getsurat', [ApiUserKaryawanController::class, 'getsurat'])->middleware('auth:userkol');
Route::get('perubahankaryawan', [ApiUserKaryawanController::class, 'perubahankaryawan'])->middleware('auth:userkol');
Route::get('list_tim', [ApiUserKaryawanController::class, 'list_tim'])->middleware('auth:userkol');
Route::get('list_kan', [ApiUserKaryawanController::class, 'list_kan'])->middleware('auth:userkol');
Route::get('rencana_pertanggal', [ApiUserKaryawanController::class, 'rencana_pertanggal'])->middleware('auth:userkol');
Route::get('rencana_perkar', [ApiUserKaryawanController::class, 'rencana_perkar'])->middleware('auth:userkol');
Route::get('rencana_marketing', [ApiUserKaryawanController::class, 'rencana_marketing'])->middleware('auth:userkol');
Route::get('lap_rencana', [ApiUserKaryawanController::class, 'lap_rencana'])->middleware('auth:userkol');
Route::post('kon_rencana', [ApiUserKaryawanController::class, 'kon_rencana'])->middleware('auth:userkol');
Route::post('up_rencana', [ApiUserKaryawanController::class, 'up_rencana'])->middleware('auth:userkol');
Route::post('del_rencana', [ApiUserKaryawanController::class, 'del_rencana'])->middleware('auth:userkol');
Route::post('setbangsat', [ApiUserKaryawanController::class, 'setbangsat'])->middleware('auth:userkol');
Route::get('getbangsat', [ApiUserKaryawanController::class, 'getbangsat'])->middleware('auth:userkol');
Route::get('getrencanabln', [ApiUserKaryawanController::class, 'getrencanabln'])->middleware('auth:userkol');
Route::post('setrencanabln', [ApiUserKaryawanController::class, 'setrencanabln'])->middleware('auth:userkol');
Route::get('rencanabln_on', [ApiUserKaryawanController::class, 'rencanabln_on'])->middleware('auth:userkol');
Route::post('setrencanatgl', [ApiUserKaryawanController::class, 'setrencanatgl'])->middleware('auth:userkol');
Route::get('cap_omset', [ApiUserKaryawanController::class, 'cap_omset'])->middleware('auth:userkol');
Route::get('cap_closing', [ApiUserKaryawanController::class, 'cap_closing'])->middleware('auth:userkol');
Route::get('lap_mar', [ApiUserKaryawanController::class, 'lap_mar'])->middleware('auth:userkol');
Route::get('renlap', [ApiUserKaryawanController::class, 'renlap'])->middleware('auth:userkol');
Route::get('count_tugas', [ApiUserKaryawanController::class, 'count_tugas'])->middleware('auth:userkol');
Route::get('lap_renlap', [ApiUserKaryawanController::class, 'lap_renlap'])->middleware('auth:userkol');
Route::post('lap_renlap_save', [ApiUserKaryawanController::class, 'lap_renlap_save'])->middleware('auth:userkol');
Route::get('get_tarperbulan', [ApiUserKaryawanController::class, 'get_tarperbulan'])->middleware('auth:userkol');
Route::get('get_target', [ApiUserKaryawanController::class, 'get_target'])->middleware('auth:userkol');
Route::get('get_progser', [ApiUserKaryawanController::class, 'get_progser'])->middleware('auth:userkol');
Route::post('acc_target', [ApiUserKaryawanController::class, 'acc_target'])->middleware('auth:userkol');
Route::post('set_target', [ApiUserKaryawanController::class, 'set_target'])->middleware('auth:userkol');
Route::post('set_tarprog', [ApiUserKaryawanController::class, 'set_tarprog'])->middleware('auth:userkol');
Route::post('set_tarkar', [ApiUserKaryawanController::class, 'set_tarkar'])->middleware('auth:userkol');
Route::post('set_progser', [ApiUserKaryawanController::class, 'set_progser'])->middleware('auth:userkol');
Route::get('get_gaji', [ApiUserKaryawanController::class, 'get_gaji'])->middleware('auth:userkol');
Route::post('acc_gaji', [ApiUserKaryawanController::class, 'acc_gaji'])->middleware('auth:userkol');
Route::get('list_gaji', [ApiUserKaryawanController::class, 'list_gaji'])->middleware('auth:userkol');
Route::get('get_gajiid', [ApiUserKaryawanController::class, 'get_gajiid'])->middleware('auth:userkol');
Route::get('rwy_gajiid', [ApiUserKaryawanController::class, 'rwy_gajiid'])->middleware('auth:userkol');
Route::get('list_gajikar', [ApiUserKaryawanController::class, 'list_gajikar'])->middleware('auth:userkol');
Route::post('post_lap', [ApiUserKaryawanController::class, 'post_lap'])->middleware('auth:userkol');
Route::post('up_nextren', [ApiUserKaryawanController::class, 'up_nextren'])->middleware('auth:userkol');
Route::post('post_req', [ApiUserKaryawanController::class, 'post_req'])->middleware('auth:userkol');
Route::post('edit_req', [ApiUserKaryawanController::class, 'edit_req'])->middleware('auth:userkol');
Route::get('sisa_req', [ApiUserKaryawanController::class, 'sisa_req'])->middleware('auth:userkol');
Route::get('notif_hp', [ApiUserKaryawanController::class, 'notif_hp'])->middleware('auth:userkol');
Route::get('notif_konfir', [ApiUserKaryawanController::class, 'notif_konfir'])->middleware('auth:userkol');
Route::post('acc_req', [ApiUserKaryawanController::class, 'acc_req'])->middleware('auth:userkol');
Route::post('reject_req', [ApiUserKaryawanController::class, 'reject_req'])->middleware('auth:userkol');
Route::get('voting', [ApiUserKaryawanController::class, 'voting'])->middleware('auth:userkol');
Route::post('post_vote', [ApiUserKaryawanController::class, 'post_vote'])->middleware('auth:userkol');
Route::post('get_token_midtrans', [ApiUserKaryawanController::class, 'getTokenMidtrans'])->middleware('auth:userkol');




Route::post('loginpen', [ApiUserKaryawanController::class, 'loginpen']);
Route::get('getprofilepen', [ApiUserKaryawanController::class, 'profilepen'])->middleware('auth:userpen');



Route::get('getanak','ApiUserPendidikanController@getanak');
Route::get('joindataanak','ApiUserPendidikanController@joindataanak');
Route::get('PenKeuangan','ApiUserPendidikanController@PenKeuangan');
Route::get('detailanakrapot/{id}','ApiUserPendidikanController@detailanakrapot');
Route::get('detailanakprestasi/{id}','ApiUserPendidikanController@detailanakprestasi');
Route::get('detailanaksurat/{id}','ApiUserPendidikanController@detailanaksurat');
Route::get('detailanakdetail/{id}','ApiUserPendidikanController@detailanakdetail');
Route::get('detailanakriwayat/{id}','ApiUserPendidikanController@detailanakriwayat');
Route::get('joindatapend','ApiUserPendidikanController@joindatapend');
Route::get('absenanak/{id}','ApiUserPendidikanController@absenanak');
Route::get('keuangananak/{id}','ApiUserPendidikanController@keuangananak');
Route::get('pembayaran/{id}','ApiUserPendidikanController@pembayaran');
Route::get('kelompok','ApiUserPendidikanController@kelompok');
Route::get('level','ApiUserPendidikanController@level');
Route::get('materi','ApiUserPendidikanController@materi');
Route::get('shelter/{id}','ApiUserPendidikanController@shelter');
Route::get('wilbin/{id}','ApiUserPendidikanController@wilbin');
Route::get('kacab','ApiUserPendidikanController@kacab');
Route::get('getprovinsi','ApiUserPendidikanController@getprovinsi');
Route::get('getkab/{id}','ApiUserPendidikanController@getkab');
Route::get('getkec/{id}','ApiUserPendidikanController@getkec');
Route::get('getkel/{id}','ApiUserPendidikanController@getkel');
Route::get('tutor','ApiUserPendidikanController@tutor');
Route::get('tutor2','ApiUserPendidikanController@tutor2');
Route::get('pengetkeluarga','ApiUserPendidikanController@pengetkeluarga');
Route::get('getkooranak/{id}','ApiUserPendidikanController@getkooranak');
Route::get('firebase','FirebaseController@index');
Route::get('getpengelola','ApiUserPendidikanController@getpengelola');
Route::get('getsurvey','ApiUserPendidikanController@getsurvey');
Route::get('getanakortu/{id}','ApiUserPendidikanController@getanakortu');
Route::get('getwali/{id}','ApiUserPendidikanController@getwali');
Route::get('getibu/{id}','ApiUserPendidikanController@getibu');
Route::get('getayah/{id}','ApiUserPendidikanController@getayah');
Route::get('getanakkel/{id}','ApiUserPendidikanController@getanakkel');
Route::get('getbankpen','ApiUserPendidikanController@getbankpen');
Route::get('wilbinfil','ApiUserPendidikanController@wilbinfil');
Route::get('shelterfil','ApiUserPendidikanController@shelterfil');
Route::get('getbiaya/{biaya}','ApiUserPendidikanController@getbiaya');
Route::get('getlaporananak/{id}','ApiUserPendidikanController@getlaporananak');
Route::get('getlaporandonatur/{id}','ApiUserPendidikanController@getlaporandonatur');
Route::get('AnakDona/{id}','ApiUserPendidikanController@AnakDona');



