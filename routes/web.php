<?php
      
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonaturController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChartofaccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\JalurController;
use App\Http\Controllers\AssigmentController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\GolonganController;
use App\Http\Controllers\GajipokokController;
use App\Http\Controllers\GajikaryawanController;
use App\Http\Controllers\TunjanganController;
use App\Http\Controllers\KantorController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\PenerimamanfaatController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PenyaluranController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AkuntasiController;
use App\Http\Controllers\FinsController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\UangPersediaanController;
use App\Http\Controllers\SettingLaporanController;
use App\Http\Controllers\BuktiSetorController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\TesController;
use App\Http\Controllers\RiwayatPerubahanController;
use App\Http\Controllers\PerencanaanController;
use App\Http\Controllers\JamKerjaController;
use App\Http\Controllers\GenerateController;
use App\Http\Controllers\NotifController;
use App\Http\Controllers\SkemaController;
use App\Http\Controllers\VotingController;
use App\Http\Controllers\KPIController;
use App\Http\Controllers\BerbagiTeknologiController;
use App\Http\Controllers\ApiUserKaryawanController;
use Illuminate\Support\Facades\Mail;


// use Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('tes-email', function () {
    Mail::raw('Ini adalah email percobaan dari server production.', function ($message) {
        $message->to('rizka.parhan@gmail.com') // ubah ke email kamu
                ->subject('Tes Email dari Laravel Production');
    });

    return 'Email test dikirim!';
});

Route::get('/', function () {
    return view('auth.login');
});
Route::get('/fcm',[FcmController::class, 'index']);

Route::post('masuk', [AuthController::class, 'masuk']);
Route::get('buat', [AuthController::class, 'create']);
Route::post('create', [AuthController::class, 'buat']);
Route::get('logout', [AuthController::class, 'logout']);

Route::get('registrasi', [AuthController::class, 'daftar_perusahaan']);
Route::get('privacy-police', function(){
        return view('privasi.index');
    }
);
Route::post('daftar-post', [AuthController::class, 'daftar_post']);

Route::get('kwitansi/{donatur}', [TransaksiController::class, 'test']);

// Route::get('cekdataa', [NotifController::class, 'cekdataa']);

Route::get('add-karyawan', [KaryawanController::class, 'createx']);
Route::post('karyawannew', [KaryawanController::class, 'postx']);
Route::get('terimakasih', [KaryawanController::class, 'terimakasih']);


Route::group(['prefix' => 'berbagi-teknologi', 'middleware' => ['auth:user']], function () {
    // START Berbagi Teknologi
    Route::get('home', [BerbagiTeknologiController::class, 'index']);
    Route::get('dashboard', [BerbagiTeknologiController::class, 'dashboard']);
    // apps
    Route::get('apps', [BerbagiTeknologiController::class, 'apps']);
    Route::get('getapps', [BerbagiTeknologiController::class, 'getapps']);
    Route::post('postapps', [BerbagiTeknologiController::class, 'postapps']);
    Route::get('editapps/{id}', [BerbagiTeknologiController::class, 'editapps']);
    Route::post('delapps/{id}', [BerbagiTeknologiController::class, 'delapps']);
    
    // company
    Route::get('company', [BerbagiTeknologiController::class, 'company']);
    Route::get('getcompany', [BerbagiTeknologiController::class, 'getcompany']);
    Route::post('postcompany', [BerbagiTeknologiController::class, 'postcompany']);
    Route::post('delcompany/{id}', [BerbagiTeknologiController::class, 'delcompany']);
    Route::post('AktifCompany', [BerbagiTeknologiController::class, 'AktifCompany']);
    Route::post('auto-aktivasi', [BerbagiTeknologiController::class, 'autoaktivasi']);
    
    // user_management
    Route::get('user-management', [BerbagiTeknologiController::class, 'user_manage']);
    Route::get('getusers', [BerbagiTeknologiController::class, 'getusers']);
    Route::get('getUserById/{id}', [BerbagiTeknologiController::class, 'getUserById']);
    Route::post('postUserAkses', [BerbagiTeknologiController::class, 'postUserAkses']);
    
    // notif daftar perusahaan
    Route::get('notify', [BerbagiTeknologiController::class, 'notify']);
    Route::post('hapusNotif', [BerbagiTeknologiController::class, 'hapusNotif']);
    
    // Akses
    Route::get('akses', [BerbagiTeknologiController::class, 'akses']);
    Route::get('level-company', [BerbagiTeknologiController::class, 'levelcompany']);
    Route::post('entry-level-post', [BerbagiTeknologiController::class, 'entrylevelprice'])->name('entry-level');
    Route::get('getakses', [BerbagiTeknologiController::class, 'getakses']);
    // Route::post('postapps', [BerbagiTeknologiController::class, 'postapps']);
    // Route::get('editapps/{id}', [BerbagiTeknologiController::class, 'editapps']);
    // Route::post('delapps/{id}', [BerbagiTeknologiController::class, 'delapps']);
    // END Berbagi Teknologi
});

Route::group(['middleware' => ['auth:user']], function () {
    
    Route::get('ceklogin', [AuthController::class, 'ceklogin']);
    
    
    // Route::get('non-aktif-company', [ProfileController::class, 'getcompanyNonAktive']);
    Route::get('search-sidebar', [AuthController::class, 'searchSidebar']);
    // Tab
    Route::get('diroh_handsome', [DashboardController::class, 'handsome']);
    
    Route::get('dashboard_tab', [DashboardController::class, 'dashboard_tab']);
    Route::get('donatur_tab', [DonaturController::class, 'donatur_tab']);
    Route::get('transaksi_tab', [TransaksiController::class, 'transaksi_tab']);
    // badge_sidebar
    Route::get('badge_doang', [DashboardController::class, 'badge_doang']);
    
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::post('dashboard/ubahaja', [DashboardController::class, 'ubahaja']);
    Route::get('targetong', [DashboardController::class, 'target']);
    Route::get('target_by_id', [DashboardController::class, 'targetId']);

    // controller dibawah digunakan management user dan tambah karyawan
    Route::get('getspv', [AuthController::class, 'getspv']);
    Route::get('getjab', [AuthController::class, 'getjab']);
    Route::get('getkan', [AuthController::class, 'getkan']);
    Route::get('getmentor', [AuthController::class, 'getmentor']);
    Route::get('namekaryawan', [AuthController::class, 'namekaryawan']);
    // Route::get('getjandk', [AuthController::class, 'getjandk']);
    Route::get('getpj', [AuthController::class, 'getpj']);
    Route::get('management-user', [AuthController::class, 'akses']);
    Route::get('changeaktifakses', [AuthController::class, 'changeaktifakses']);
    Route::get('getkar', [AuthController::class, 'getkar']);
    Route::post('cobakan', [AuthController::class, 'store']);
    Route::post('upkar/{id}',[AuthController::class, 'upkar']);
    Route::post('user', [AuthController::class, 'patch']);
    Route::get('edkar/{id}', [AuthController::class, 'edkar']);
    Route::get('user/getedit/{id}', [AuthController::class, 'geteditkar']);
    Route::get('user/getdata/{id}', [AuthController::class, 'getkarid']);
    Route::get('offuser/{id}', [AuthController::class, 'offuser']);
    Route::get('user/{id}', [AuthController::class, 'destroy']);
    Route::get('change-account', [AuthController::class, 'changeAccount']);
    Route::get('countusers', [AuthController::class, 'countUsersLogin']);
    Route::get('company-layout', [AuthController::class, 'companyLayout']);

    // aktivasi perusahaan
    
    Route::post('aktivasi-perusahaan', [AuthController::class, 'storeUserPerusahaan']);

    // karyawan controller
    
    Route::get('limit-kar', [KaryawanController::class, 'limitkar']);
    Route::get('karyawan', [KaryawanController::class, 'index']);
    Route::post('karyawan', [KaryawanController::class, 'store']);
    Route::get('karyawan/detail/{id}', [KaryawanController::class, 'show']);
    Route::get('detailkaryawan/{id}', [KaryawanController::class, 'detailkaryawan']);
    Route::get('changesttsaktif', [KaryawanController::class, 'changesttsaktif']);
    Route::get('getkaryawan', [KaryawanController::class, 'getkaryawan']);
    Route::get('karyawan/hapus/{id}', [KaryawanController::class, 'destroyy']);
    Route::get('karyawan/edit/{id}', [KaryawanController::class, 'edit']);
    Route::post('karyawan/{id}', [KaryawanController::class, 'update']);
    Route::post('karyawanpen/{id}', [KaryawanController::class, 'updatepen']);
    Route::get('karyawan/aktifkan/{id}', [KaryawanController::class, 'aktifken']);
    Route::get('getgol/{id}', [KaryawanController::class, 'getgol']);
    Route::post('postgol/{id}', [KaryawanController::class, 'postgol']);
    Route::get('getkaryawanbyid/{id}', [KaryawanController::class, 'getKaryawanById']);
    Route::get('bpjskar/{id}', [KaryawanController::class, 'getbpjskar']);
    Route::post('upbpjskar/{id}', [KaryawanController::class, 'postbpjskar']);
    Route::post('mutasi-karyawan/{id}', [KaryawanController::class, 'mutasi_karyawan']);
    Route::get('karyawan/create', [KaryawanController::class, 'create']);
    Route::get('riwayatmutasi', [KaryawanController::class, 'riwayatmutasi']);
    Route::get('riwayatkeluarga', [KaryawanController::class, 'riwayatkeluarga']);
    Route::get('riwayatkenaikan', [KaryawanController::class, 'riwayat']);
    Route::get('riwayatjabatan', [KaryawanController::class, 'riwayatjabatan']);
    Route::get('karyawan/exports', [KaryawanController::class, 'exports']);
    Route::get('karyawan/cekcompany', [KaryawanController::class, 'cekcompany']);
    Route::get('getjandk', [PresensiController::class, 'getjandk']);
    Route::get('karyawan-option', [KaryawanController::class, 'select_kar']);
    Route::get('karyawan-export', [KaryawanController::class, 'karyawanExport']);
    Route::get('pengajuan-perubahan', [KaryawanController::class, 'perubahankaryawan']);
    Route::get('perbkarBy/{id}', [KaryawanController::class, 'perbkarBy']);
    Route::post('acc_perubahan/{id}', [KaryawanController::class, 'acc_perubahan']);
    Route::get('akseshc', [KaryawanController::class, 'getakseshc']);
    Route::get('jamhc', [KaryawanController::class, 'getjamhc']);
    Route::get('levelhc', [KaryawanController::class, 'getlevelhc']);
    Route::get('itungkar', [KaryawanController::class, 'itungkar']);
    
// Route::post('/upload-pdf', [YourControllerName::class, 'uploadPdf']);


    // Transaksi Controller
    Route::get('transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::post('transaksi', [TransaksiController::class, 'index']);
    Route::get('transaksi/getdata/{id}', [TransaksiController::class, 'ambilkirim']);
    // Route::get('transaksi/edit/{id}',[TransaksiController::class, 'ambil']);
    Route::get('transaksi/total', [TransaksiController::class, 'total']);
    Route::get('getdon', [TransaksiController::class, 'getdon']);
    Route::get('kolektorr', [TransaksiController::class, 'cek_kolektor']);
    Route::post('app', [TransaksiController::class, 'appr']);
    Route::get('transaksi/delete/{id}', [TransaksiController::class, 'destroyy']);
    Route::get('aproves/{id}', [TransaksiController::class, 'aproves']);
    Route::get('aprove_all', [TransaksiController::class, 'aprove_all']);
    Route::get('add-transaksi', [TransaksiController::class, 'add_tr']);
    Route::get('prog_prog_prog/{prog}', [TransaksiController::class, 'get_prog_prog']);
    Route::get('getprosp/{id}/{prog}', [TransaksiController::class, 'getprosp']);
    Route::get('getinfodonatur/{id}', [TransaksiController::class, 'getinfodon']);
    Route::get('getsave', [TransaksiController::class, 'getsave']);
    Route::post('post_trans', [TransaksiController::class, 'post_trans']);
    Route::get('notifya', [TransaksiController::class, 'notifya'])->name('notifya');
    Route::get('getPengTransBy/{id}', [TransaksiController::class, 'getPengTransBy']);
    Route::post('changenotif/{id}', [TransaksiController::class, 'changenotif']);
    Route::get('getbbjudul/{id}', [TransaksiController::class, 'getbbjudul']);
    
    
    //Bukti Setor Zakat
    Route::get('bukti-setor-zakat', [BuktiSetorController::class, 'buktisetor_zakat']);
    Route::get('buktiBy/{id}', [BuktiSetorController::class, 'buktiBy']);
    Route::get('pdfbuktisetor', [BuktiSetorController::class, 'eksbukti']);
    Route::get('bukti-setor-zakat/export', [BuktiSetorController::class, 'export']);


    Route::get('petugasso', [DonaturController::class, 'petugas_so']);
    Route::get('get_riwayat_t/{id}', [DonaturController::class, 'get_riw']);
    Route::get('riwayatdonasi/{donatur}', [TransaksiController::class, 'riwayat']);
    Route::post('updatedon', [TransaksiController::class, 'updatedon']);

    Route::get('cek_aja_nih', [DonaturController::class, 'cek_aja_nih']);
    Route::get('detail/{id}', [TransaksiController::class, 'detail']);

    Route::get('getcoanoncash', [TransaksiController::class, 'getcoanoncash']);
    Route::post('edittransaksi', [TransaksiController::class, 'edittransaksi']);
    Route::get('transaksi/edit/{id}', [TransaksiController::class, 'edit']);
    Route::get('nm_donaturedit', [TransaksiController::class, 'get_nm1']);

    Route::get('transaksi-rutin', [TransaksiController::class, 'transaksi_rutin']);
    Route::get('transaksi-rutin/ekspor', [TransaksiController::class, 'transaksi_rutin_ekspor']);
    Route::get('transaksi-rutin-detail', [TransaksiController::class, 'transaksi_rutin_detail']);
    // export
    Route::get('transaksi-export', [TransaksiController::class, 'transaksi_export']);

    Route::get('getDntr', [DonaturController::class, 'getDntr']);
    Route::get('list_donat', [DonaturController::class, 'list_donat']);
    Route::get('map_donatur', [DonaturController::class, 'map_donatur']);
    Route::get('lokdon_detail', [DonaturController::class, 'lokdon_detail']);

    // Donatur Controller
    Route::get('donatur', [DonaturController::class, 'index']);
    Route::get('nm_donatur', [DonaturController::class, 'get_nm']);
    Route::get('cek_don/{name}/{id}', [DonaturController::class, 'cek_email']);
    Route::get('offdon/{donatur}', [DonaturController::class, 'offdon']);
    Route::get('donatur/delete/{donatur}', [DonaturController::class, 'destroy']);
    Route::get('changeoffdon', [DonaturController::class, 'changeoffdon']);
    Route::get('donatur/export', [DonaturController::class, 'export']);
    Route::get('adddonatur', [DonaturController::class, 'add']);
    Route::post('adddonatur', [DonaturController::class, 'add_don']);
    Route::get('provinces', [DonaturController::class, 'provinces']);
    Route::get('cities/{id}', [DonaturController::class, 'cities']);
    Route::get('donatur/edit/{id}', [DonaturController::class, 'edit_don']);
    Route::post('donatur/edit/{id}', [DonaturController::class, 'update_don']);
    Route::post('hfmdonatur/edit/{id}', [DonaturController::class, 'update_donhfm']);
    
    Route::get('add-donatur', [DonaturController::class, 'add_new']);
    Route::post('add-donatur', [DonaturController::class, 'add_don_new']);

    Route::get('getsumberdana', [ProgramController::class, 'get_sumberdana']);
    Route::get('getid_program/{id}', [ProgramController::class, 'getid_program']);
    
     // Digunakan di program
    Route::get('program', [ProgramController::class, 'getProgram']);
    Route::get('program/sumberdana_edit/{id}', [ProgramController::class, 'sumdit']);
    Route::post('program/sumberdana', [ProgramController::class, 'add_sumberdana']);
    Route::get('program_penerimaan', [ProgramController::class, 'program_penerimaan']);
    Route::get('program_penyaluran', [ProgramController::class, 'program_penyaluran']);
    Route::post('program', [ProgramController::class, 'add_program']);
    Route::post('set_bon', [ProgramController::class, 'set_bon']);
    Route::get('getprograms', [ProgramController::class, 'getprogramparent']);
    Route::get('getprogramsalur', [ProgramController::class, 'getprogramparentsalur']);
    Route::get('program/{id}', [ProgramController::class, 'destroy_program']);
    Route::post('program/update', [ProgramController::class, 'update_program']);
    Route::get('program/edit/{id}', [ProgramController::class, 'edit_program']);
    Route::get('program/getProgs/{id}', [ProgramController::class, 'getProgs']);
    Route::get('getcamp/{id}', [ProgramController::class, 'getcamp']);
    Route::get('ekspor_program_penerimaan', [ProgramController::class, 'ekspor_program_penerimaan']);
    Route::post('add-program-penyaluran', [ProgramController::class, 'add_program_penyaluran']);
    Route::get('edit-program-penyaluran/{id}', [ProgramController::class, 'edit_program_penyaluran']);
    Route::post('update-program-penyaluran', [ProgramController::class, 'update_program_penyaluran']);
    Route::post('delete-program-penyaluran/{id}', [ProgramController::class, 'destroy_program_penyaluran']);
    
    // Digunakan di Rincian DOnasi
    Route::get('riwayat-donasi/{donatur}', [TransaksiController::class, 'detaildon']);
    Route::get('riwayat-kunjungan/{donatur}', [TransaksiController::class, 'riwayat']);
    Route::get('getdata_riwkun/{id}', [TransaksiController::class, 'ambilkirim']);
    Route::get('riwayat-kunjungan/delete/{id}', [TransaksiController::class, 'destroyy']);
    
    // Capaian  Controller
    Route::get('capaian-omset', [HomeController::class, 'capaianomset']);
    Route::get('export_dulu', [HomeController::class, 'export_dulu']);
    
    // Buku Harian
    Route::get('buku-harian', [AkuntasiController::class, 'bukuharian']);
    Route::get('caribuku/{id}', [AkuntasiController::class, 'caribuku']);
    Route::get('buku-harian-dash', [AkuntasiController::class, 'bukuhariandash']);
    Route::get('buku-harian-dash-awal', [AkuntasiController::class, 'bukuhariandashsawal']);
    Route::get('buku_harian_by', [AkuntasiController::class, 'buku_harian_by']);
    Route::post('buku_harian_hapus', [AkuntasiController::class, 'buku_harian_hapus']);
    Route::post('buku_harian_acc', [AkuntasiController::class, 'buku_harian_acc']);
    Route::post('buku_harian_edit', [AkuntasiController::class, 'buku_harian_edit']);
    Route::post('buku_harian_edit_hfm', [AkuntasiController::class, 'buku_harian_edit_hfm']);
    Route::get('getnamcoa', [AkuntasiController::class, 'getnamcoa']);
    Route::get('getnamcoadet', [AkuntasiController::class, 'getnamcoadet']);
    Route::get('buku_harian_export', [AkuntasiController::class, 'buku_harian_export']);
    
    // Buku Besar
    Route::get('buku-besar', [AkuntasiController::class, 'bukubesar']);
    Route::get('buku-besar-export', [AkuntasiController::class, 'bukubesarexport']);
    
    // Rekap Jurnal
    Route::get('rekap-jurnal', [AkuntasiController::class, 'rekapjurnal']);
    Route::post('post_jurnal', [AkuntasiController::class, 'postjurnal']);
    Route::get('export-jurnal', [AkuntasiController::class, 'exportJurnal']);
    
    // Skema Gaji
    Route::get('skema-gaji', [SkemaController::class, 'index']);
    Route::post('postSkema', [SkemaController::class, 'add']);
    Route::get('getSkema', [SkemaController::class, 'getSkema']);
    Route::post('postKom', [SkemaController::class, 'add_kom']);
    Route::get('getKom', [SkemaController::class, 'getKom']);
    Route::post('setKomponen', [SkemaController::class, 'setKomponen']);
    Route::post('setEdit', [SkemaController::class, 'setEdit']);
    Route::POST('ubahPosisi',  [SkemaController::class, 'ubahPosisi']);
    Route::get('getkomp', [SkemaController::class, 'getkomp']);
    Route::POST('postPersentase',  [SkemaController::class, 'postPersentase']);
    Route::get('getPers', [SkemaController::class, 'getPers']);
    
    // Laporan DI Fins
    Route::get('laporan-bulanan', [LaporanKeuanganController::class, 'laporan_bulanan']);
    Route::get('laporan-bulanan-tabel', [LaporanKeuanganController::class, 'laporan_bulanan_tabel']);
    Route::get('laporan-keuangan', [LaporanKeuanganController::class, 'laporan_keuangan']);
    Route::get('laporan-keuangan/ekspor', [LaporanKeuanganController::class, 'ekspor']);
    Route::get('laporan-bulanan/ekspor', [LaporanKeuanganController::class, 'eksporbulanan']);
    Route::get('detail-keuangan', [LaporanKeuanganController::class, 'detail_keuangan']);
    Route::get('detail-debet', [LaporanKeuanganController::class, 'detail_debet']);
    Route::get('detail-kredit', [LaporanKeuanganController::class, 'detail_kredit']);
    Route::get('detail-keuangan2', [LaporanKeuanganController::class, 'detail_keuangan2']);
    Route::get('detail-dsebelum', [LaporanKeuanganController::class, 'detail_debet_sebelumnya']);
    Route::get('detail-ksebelum', [LaporanKeuanganController::class, 'detail_kredit_sebelumnya']);
    Route::get('detail-laporan-keuangan/ekspor', [LaporanKeuanganController::class, 'detail_ekspor']);
    
    // Home Controller
    Route::get('capaian-kolekting', [HomeController::class, 'index']);
    
    Route::get('testing', [HomeController::class, 'testing']);
    Route::get('detailcapdon', [HomeController::class, 'detailcapdon']);
    
    Route::get('testt', [HomeController::class, 'test']);
    Route::get('chart', [HomeController::class, 'chart']);
    Route::get('kota', [HomeController::class, 'kota']);
    Route::get('assign_tot', [HomeController::class, 'assign_tot']);
    Route::get('totdontran', [HomeController::class, 'totdontran']);
    Route::get('test/{id}', [HomeController::class, 'getid']);
    Route::get('datranmod/{id}', [HomeController::class, 'datranmod_getid']);
    Route::get('datdon/{id}', [HomeController::class, 'datdon_getid']);
    
    Route::get('targetgetcab', [HomeController::class, 'targetgetcab']);
    Route::post('targetkacc', [HomeController::class, 'targetkacc']);

    // Digunakan di Data Jalur
    Route::get('getjalur', [JalurController::class, 'getjalur']);
    Route::get('jalur', [JalurController::class, 'index']);
    Route::post('jalur', [JalurController::class, 'add_jalur']);
    Route::post('jalur/update', [JalurController::class, 'update_jalur']);
    Route::post('jalur/updatejalur', [JalurController::class, 'updatejalur']);
    Route::get('jalur/edit/{id}', [JalurController::class, 'edit_jalur']);
    Route::get('jalur/{id}', [JalurController::class, 'delete']);
    Route::get('getspvid', [JalurController::class, 'getspvid']);
    Route::get('getjalurspv', [JalurController::class, 'getjalurspv']);
    Route::get('adajalur', [JalurController::class, 'adajalur']);

    Route::get('assignment', [AssigmentController::class, 'assigmentadmin']);
    Route::get('assign_all', [AssigmentController::class, 'assign_all']);
    Route::get('jalurah', [AssigmentController::class, 'cek_jlr']);
    Route::post('set_warning', [AssigmentController::class, 'set_warning']);
    Route::get('getjumbul', [AssigmentController::class, 'getjumbul']);
    Route::post('actionsadm', [AssigmentController::class, 'actionsadm']);
    Route::get('changeStatusadm', [AssigmentController::class, 'changeStatusadm']);
    Route::get('getjlr_multiple', [AssigmentController::class, 'getjlr_multiple']);
    Route::get('jadwalkan', [AssigmentController::class, 'jadwalkan']);
    Route::get('jadwalkan_all', [AssigmentController::class, 'jadwalkan_all']);
    Route::get('ganti_petugas_bang', [AssigmentController::class, 'ganti_petugas_bang']);
    

    Route::get('data-bonus-kolekting', [BonusController::class, 'index']);
    Route::get('coa-bank/{id}', [BankController::class, 'coa_bank']);
    
    Route::get('coa', [ChartofaccountController::class, 'index']);
    Route::get('coa_coa', [ChartofaccountController::class, 'coa_kun']);
    Route::get('getcoba', [ChartofaccountController::class, 'coba']);
    Route::post('coa',[ChartofaccountController::class, 'store']);
    Route::get('coa/edit/{id}',[ChartofaccountController::class, 'edit']);
    Route::post('coa/update',[ChartofaccountController::class, 'update']);
    Route::get('coa/{id}',[ChartofaccountController::class, 'destroy']);
    Route::get('update_coa/{id}',[ChartofaccountController::class, 'update_kantor']);
    Route::get('getcoapndp', [ChartofaccountController::class, 'getcoapndp']);
    Route::get('getcoapngdp', [ChartofaccountController::class, 'getcoapngdp']);
    Route::get('getcoa', [ChartofaccountController::class, 'getcoa']);
    Route::get('getcoapenyaluran', [ChartofaccountController::class, 'getcoapenyaluran']);
    Route::get('getcoakondisi', [ChartofaccountController::class, 'getcoakondisi']);
    Route::get('getcoalagi', [ChartofaccountController::class, 'getcoalagi']);

    Route::get('getcoapersediaan', [ChartofaccountController::class, 'getcoapersediaan']);
    Route::get('getcoapenerimaan', [ChartofaccountController::class, 'getcoapenerimaan']);
    Route::get('getcoaamil', [ChartofaccountController::class, 'getcoaamil']);
    Route::get('getcoaapbn', [ChartofaccountController::class, 'getcoaapbn']);
    Route::get('getcoahibah', [ChartofaccountController::class, 'getcoahibah']);
    Route::get('getcoainfaqt', [ChartofaccountController::class, 'getcoainfaqt']);
    Route::get('getcoainfaqtd', [ChartofaccountController::class, 'getcoainfaqtd']);
    Route::get('getcoawakaf', [ChartofaccountController::class, 'getcoawakaf']);
    Route::get('getcoadilarang', [ChartofaccountController::class, 'getcoadilarang']);
    Route::get('getcoazkt', [ChartofaccountController::class, 'getcoazkt']);
    Route::get('coapengirimmutasi', [ChartofaccountController::class, 'coapengirimmutasi']);
    Route::get('getcoamutasipengirim', [ChartofaccountController::class, 'getcoamutasipengirim']);
    Route::get('getcoamutasipenerima', [ChartofaccountController::class, 'getcoamutasipenerima']);
    Route::get('getcoasumberdana', [ChartofaccountController::class, 'getcoasumberdana']);
    Route::get('cari_saldo', [ChartofaccountController::class, 'cari_saldo']);
    Route::get('data_anggaran', [ChartofaccountController::class, 'data_anggaran']);

    Route::get('cari_saldox', [ChartofaccountController::class, 'cari_saldox']);
    Route::get('cari_saldonya', [ChartofaccountController::class, 'cari_saldonya']);
    Route::get('cari_saldo2', [ChartofaccountController::class, 'cari_saldo2']);

    Route::get('getcoajasa', [ChartofaccountController::class, 'getcoajasa']);
    
    Route::get('coa-exp', [ChartofaccountController::class, 'coaExport']);

    // Golongan Controller
    Route::get('golongan', [GolonganController::class, 'index']);
    Route::get('golongan/edit/{id}', [GolonganController::class, 'edit']);
    Route::post('golongan/update', [GolonganController::class, 'update']);
    
    // Digunakan di Jabatan
    Route::get('jabatan', [JabatanController::class, 'index']);
    Route::post('jabatan', [JabatanController::class, 'store']);
    Route::get('jabatan/{jabatan}', [JabatanController::class, 'destroy']);
    Route::post('jabatan/{jabatan}', [JabatanController::class, 'update']);
    Route::get('jabatan/edit/{id}', [JabatanController::class, 'edit']);

    // Gaji pokok Controller
    Route::get('gaji-pokok', [GajipokokController::class, 'index']);
    Route::post('intahun', [GajipokokController::class, 'intahun']);
    Route::get('getgapok', [GajipokokController::class, 'getgapok']);
    Route::post('upgapok', [GajipokokController::class, 'upgapok']);
    Route::post('naikper', [GajipokokController::class, 'naikper']);
    Route::post('turunper', [GajipokokController::class, 'turunper']);

    // Gaji Controller
    Route::get('gaji-karyawan', [GajikaryawanController::class, 'index']);
    Route::get('eksportdata', [GajikaryawanController::class, 'eksdata']);
    Route::get('eksportpay', [GajikaryawanController::class, 'ekspay']);
    Route::get('eksportbpjs', [GajikaryawanController::class, 'eksbpjs']);
    Route::get('cekdata', [GajikaryawanController::class, 'cekdata']);

  // notif Controller
    Route::get('notif', [NotifController::class, 'notif']);
    Route::post('testing_saja', [NotifController::class, 'testing_saja']);



    // presensi controller
    Route::get('kehadiran', [PresensiController::class, 'index']);
    Route::get('kehadiran/exportk', [PresensiController::class, 'exportk']);
    Route::get('kehadiran/exportrekapk', [PresensiController::class, 'exportrekapk']);
    Route::get('kehadiran/{id}', [PresensiController::class, 'ambilkirimdong']);
     Route::get('get_detail_presensi/{id}', [PresensiController::class, 'detailPresensi']);
     Route::get('get_kardinamis', [PresensiController::class, 'kardinamis']);
    
	Route::get('pdf', [PresensiController::class, 'pdf']);
    Route::get('get-kar', [PresensiController::class, 'kar']);
    Route::get('nama_karyawan', [PresensiController::class, 'get_karyawan_nih']);
    Route::get('laporan-karyawan', [PresensiController::class, 'laporan']);
    Route::get('exportlk', [PresensiController::class, 'exportlk']);
    Route::get('getlapkar/{id}', [PresensiController::class, 'ambilkirim']);
    Route::get('lapo_mar', [PresensiController::class, 'lapo_mar']);
    Route::get('getCapaianSet', [PresensiController::class, 'getCapaianSet']);
   
    Route::get('reqDet/{id}', [PresensiController::class, 'reqDet']);
    Route::get('daftar-request', [PresensiController::class, 'daftar_req']);
    Route::get('daftar-request/exportdr', [PresensiController::class, 'exportdr']);
    Route::get('daftar-request/rinreq/{id}', [PresensiController::class, 'get_rinreq']);
    Route::get('daftar-request/konfirm/{id}', [PresensiController::class, 'konfirm']);
    Route::get('daftar-request/tolak/{id}', [PresensiController::class, 'tolak']);
    Route::post('laporan-karyawan/post_feedback', [PresensiController::class, 'post_feedback']);
    Route::get('accall', [PresensiController::class, 'accall']);
    Route::get('accmilih', [PresensiController::class, 'accmilih']);
    Route::get('rejectall', [PresensiController::class, 'rejectall']);
    Route::get('rejectmilih', [PresensiController::class, 'rejectmilih']);
    
    // Pengumuman
    Route::get('pengumuman', [PresensiController::class, 'pengumuman']);
    Route::get('daftar-pengumuman', [PresensiController::class, 'daftar_pengumuman']);
    Route::post('entry-pengumuman', [PresensiController::class, 'entry_pengumuman']);
    Route::get('detail-pengumuman/{id}', [PresensiController::class, 'detail_pengumuman']);
    Route::get('delete-pengumuman/delete/{id}', [PresensiController::class, 'delete_pengumuman']);
    Route::post('edit-pengumuman/{id}', [PresensiController::class, 'edit_pengumuman']);
    Route::get('kar-pengumuman/{id}', [PresensiController::class, 'kar_pengumuman']);
    Route::get('notif-pengumuman', [PresensiController::class, 'notif_pengumuman']);
    Route::post('on_link_param', [PresensiController::class, 'on_link_param']);
    Route::get('off_link_param', [PresensiController::class, 'off_link_param']);
    
    // Setting Request
    Route::get('setting-request', [PresensiController::class, 'setting_request']);
    Route::get('edit-setting-request/{id}', [PresensiController::class, 'edit_setting_request']);
    Route::post('save-setting-request', [PresensiController::class, 'save_setting_request']);
    Route::get('parent-request', [PresensiController::class, 'parentRequest']);
    Route::get('hapus-setting-req/{id}', [PresensiController::class, 'hapus']);


    // Controller untuk Management Gaji
    Route::get('management-gaji', [TunjanganController::class, 'index']);
    Route::get('getdaerah', [TunjanganController::class, 'getdaerah']);
    Route::get('getterlambat', [TunjanganController::class, 'getterlambat']);
    Route::post('management-gaji/update', [TunjanganController::class, 'update']);
    Route::post('updatembl', [TunjanganController::class, 'updatembl']);
    Route::post('setterlambat', [TunjanganController::class, 'setterlambat']);
    Route::get('management-gaji/{id}', [TunjanganController::class, 'edit']);
    Route::get('gettunjangan', [TunjanganController::class, 'gettunjangan']);
    Route::get('getjabatan', [TunjanganController::class, 'getjabatan']);
    Route::post('updatebpjs', [TunjanganController::class, 'updatebpjs']);
    Route::post('update_bpjs', [TunjanganController::class, 'update_bpjs']);
    Route::post('updatetj', [TunjanganController::class, 'updatetj']);
    Route::post('management-gaji/tambahh', [TunjanganController::class, 'tambahh']);
    Route::get('management-gaji/delete/{id}', [TunjanganController::class, 'hapus']);
    Route::get('terlambat-delete/{id}', [TunjanganController::class, 'hapusterlambat']);
    Route::get('gethukuman', [TunjanganController::class, 'gethukuman']);
    Route::get('getbpjs', [TunjanganController::class, 'getbpjs']);
    Route::get('listJi', [TunjanganController::class, 'listJi']);
    Route::POST('ubahSkemaGaji', [TunjanganController::class, 'ubahSkemaGaji']);
    Route::get('getSkemaIdkar', [TunjanganController::class, 'getSkemaIdkar']);
    Route::post('set_terlambat', [TunjanganController::class, 'set_terlambat']);
    

    // Controller untuk setting kantor
    Route::get('kantor', [KantorController::class, 'index']);
    Route::get('coa-coa-kntr', [KantorController::class, 'coa_coa_kntr']);
    Route::post('kantor', [KantorController::class, 'store']);
    Route::get('kantor/{id}', [KantorController::class, 'destroy']);
    Route::post('kantor/update', [KantorController::class, 'update']);
    Route::get('updatekantor', [KantorController::class, 'updatekantor']);
    Route::get('kantor/edit/{id}', [KantorController::class, 'edit']);
    
    // Controller di setting target
    Route::get('setting-target', [SettingController::class, 'set_target']);
    Route::get('getKolektor', [SettingController::class, 'getKolektor']);
    Route::get('getTargetKantor', [SettingController::class, 'getTargetKantor']);
    Route::post('update_target', [SettingController::class, 'update_target']);
    Route::post('acc_target', [SettingController::class, 'acc_target']);
    Route::get('getProgSer', [SettingController::class, 'getProgSer']);
    Route::POST('postProgSer', [SettingController::class, 'postProgSer']);
    
    Route::post('setTahunan', [SettingController::class, 'setTahunan']);
    Route::post('setTargetNew', [SettingController::class, 'setTargetNew']);
    Route::get('getTargetPertahun', [SettingController::class, 'getTargetPertahun']);
    
    Route::GET('setTargetPimpinan', [SettingController::class, 'setTargetPimpinan']);
    
    Route::get('setwarning', [SettingController::class, 'setwarning']);
    Route::post('postwarning', [SettingController::class, 'updatewarning']);
    
    Route::get('getPenerimaanSD', [SettingController::class, 'getPenerimaanSD']);
    Route::get('getPengeluaranSD', [SettingController::class, 'getPengeluaranSD']);
    
    // Digunakan pada halaman Setting Saldo Dana
    Route::get('saldo-dana', [SettingController::class, 'saldo_dana']);
    Route::post('post_saldo_dana', [SettingController::class, 'post_saldo_dana']);

    // Digunakan pada halaman Setting Bukti Setor
    Route::get('bukti-setor', [SettingController::class, 'bukti_setor']);
    Route::post('post_bsz', [SettingController::class, 'post_bsz']);
    Route::get('getbsz', [SettingController::class, 'listbsz']);
    Route::get('bszBy/{id}', [SettingController::class, 'bszBy']);
    Route::post('edbsz_stat', [SettingController::class, 'edbsz_stat']);
    Route::get('progBy/{id}', [SettingController::class, 'progBy']);
    Route::post('bsz_pasang', [SettingController::class, 'bsz_pasang']);
    
    // Digunakan untuk USER SSO
    Route::post('cekdataa', [SettingController::class, 'cekdataa']);


    // Controller Penerima Manfaat
    Route::get('penerima-manfaat', [PenerimamanfaatController::class, 'index_pm']);
    Route::post('post_add', [PenerimamanfaatController::class, 'add_penerimaan']);
    Route::get('add-pm', [PenerimamanfaatController::class, 'add_pm']);
    Route::post('post_pm', [PenerimamanfaatController::class, 'post_pm']);
    Route::get('nama_pm', [PenerimamanfaatController::class, 'nama_pm']);
    Route::get('get_info_pm/{id}', [PenerimamanfaatController::class, 'get_info_pm']);
    Route::post('aksipenerimaan', [PenerimamanfaatController::class, 'aksipenerimaan']);
    Route::get('acc_semua_penerimaan', [PenerimamanfaatController::class, 'acc_semua_penerimaan']);
    Route::get('edit-pm/{id}', [PenerimamanfaatController::class, 'infor_pm']);
    Route::get('ubahstat', [PenerimamanfaatController::class, 'editstat']);
    Route::get('hapuspm/{id}', [PenerimamanfaatController::class, 'hapuspm']);
    Route::get('pm/export', [PenerimamanfaatController::class, 'pmexport']);
    Route::post('edtpm', [PenerimamanfaatController::class, 'editpm']);
    Route::get('gethistori/{id}', [PenerimamanfaatController::class, 'get_salur']);



    // Controller ini digunakan di Penerimaan
    Route::get('penerimaan', [PenerimamanfaatController::class, 'index']);
    Route::get('penerimaanBy/{id}', [PenerimamanfaatController::class, 'penerimaanBy']);
    Route::get('penerimaan/ekspor', [PenerimamanfaatController::class, 'ekspor']);
    
    // Controller ini digunakan di voting
    Route::get('voting', [VotingController::class, 'index']);
    Route::post('post-voting', [VotingController::class, 'post_voting']);
    // Route::get('penerimaanBy/{id}', [PenerimamanfaatController::class, 'penerimaanBy']);
    // Route::get('penerimaan/ekspor', [PenerimamanfaatController::class, 'ekspor']);

    Route::get('pengeluaran1', [PengeluaranController::class, 'index1']);

    // Controller Pengeluaran
    Route::get('pengeluaran', [PengeluaranController::class, 'index']);
    Route::post('post_pengeluaran', [PengeluaranController::class, 'post_pengeluaran']);
    Route::post('post_mutasi', [PengeluaranController::class, 'post_mutasi']);
    Route::post('hapus_pengeluaran', [PengeluaranController::class, 'hapus_pengeluaran']);
    Route::get('getcoapengeluaranbank', [PengeluaranController::class, 'getcoapengeluaranbank']);
    Route::get('get_saldo_pengeluaran', [PengeluaranController::class, 'get_saldo_pengeluaran']);
    Route::get('get_saldox_pengeluaran', [PengeluaranController::class, 'get_saldox_pengeluaran']);
    Route::get('get_saldo_pengirim', [PengeluaranController::class, 'get_saldo_pengirim']);
    Route::get('get_saldo_penerima', [PengeluaranController::class, 'get_saldo_penerima']);
    Route::get('pengeluaranBy/{id}', [PengeluaranController::class, 'pengeluaranby']);
    Route::get('pengEdBy/{id}', [PengeluaranController::class, 'pengEdBy']);
    Route::post('aksipeng', [PengeluaranController::class, 'aksipeng']);
    Route::post('editspeng', [PengeluaranController::class, 'editspeng']);
    Route::get('acc_semua', [PengeluaranController::class, 'acc_semua']);
    Route::get('pengeluaran/export', [PengeluaranController::class, 'Pexport']);


    // Controller Penyaluran
    Route::get('penyaluran', [PenyaluranController::class, 'index']);
    Route::get('get_program_penyaluran', [PenyaluranController::class, 'get_program_penyaluran']);
    Route::get('aksi-button', [PenyaluranController::class, 'button']);
    Route::get('edit-penyaluran/{id}', [PenyaluranController::class, 'edit_penyaluran']);
    
    Route::post('post_penyaluran', [PenyaluranController::class, 'post_penyaluran']);
    Route::get('edit-post-penyaluran/{id}', [PenyaluranController::class, 'editPostPenyaluran']);
    Route::get('sales-export', [SalesController::class, 'salesExport']);

    Route::get('capaian-sales', [SalesController::class, 'index']);
    Route::get('get_lap_id/{id}', [SalesController::class, 'get_lap_id']);
    Route::get('get_data_id', [SalesController::class, 'get_data_id']);
    
    Route::get('get_sales_data', [SalesController::class, 'get_sales_data']);
    Route::get('transaksi_langsung/{id}', [SalesController::class, 'transaksi_langsung']);
    Route::get('laporan_folup/{id}', [SalesController::class, 'laporan_folup']);

    Route::get('data-bonus-sales', [BonusController::class, 'bonus_sales']);
    Route::get('bonus_sales_nih/{id}', [BonusController::class, 'bonus_sales_nih']);
    
    Route::get('setting-pembayaran', [BankController::class, 'pembayaran']);
    
    // Digunakan di Halaman Bank
    Route::get('bank', [BankController::class, 'index']);
    Route::post('bank',[BankController::class, 'store']);
    Route::get('coa-bank/{id}', [BankController::class, 'coa_bank']);
    Route::post('bank/update',[BankController::class, 'update']);
    Route::get('bank/edit/{id}',[BankController::class, 'edit']);
    Route::get('bank/{id}',[BankController::class, 'destroy']);
    
    
      // Digunakan di Report Perubahan Transaksi
    Route::get('riwayat-perubahan', [RiwayatPerubahanController::class, 'riwayat_perubahan']);
    Route::get('detail-perubahan', [RiwayatPerubahanController::class, 'detail_perubahan']);
    Route::get('perubahan-donatur', [RiwayatPerubahanController::class, 'perubahan_donatur']);
    Route::get('perubahan-detdonatur', [RiwayatPerubahanController::class, 'detail_perbdon']);

    
    
    // Digunakan di analisis Transaksi
    Route::get('analisis-transaksi', [ReportController::class, 'analis_transaksi']);
    Route::get('getPetugas', [ReportController::class, 'getPetugas']);
    Route::get('transaksi_chart', [ReportController::class, 'chart_transaksi']);
    Route::get('analisis_don', [ReportController::class, 'analis_don']);
    Route::get('analis_kunjungan', [ReportController::class, 'analis_kunjungan']);
    Route::get('get_rincian_transaksi/{id}', [ReportController::class, 'get_data_id']);
    Route::get('kunjungan_by_id', [ReportController::class, 'kunjungan_by_id']);
    
    Route::get('lokasi-donatur', [ReportController::class, 'lokdon']);
    Route::get('get_jalur_lokdon', [ProfileController::class, 'get_jalur_lokdon']);
    
    Route::get('analisis-transaksi/ekspor', [ReportController::class, 'export_an']);
    
    // Digunakan di analisis Donatur
    Route::get('analisis-donatur', [ReportController::class, 'analis_donatur']);
    Route::get('chart_donatur', [ReportController::class, 'chart_donatur']);
    Route::get('donatur_detail', [ReportController::class, 'donatur_det']);
    Route::get('donatur/ekspor', [ReportController::class, 'ekspor']);
    Route::get('detail-analisis-donatur/ekspor', [ReportController::class, 'detail_donatur']);
    // Digunakan di Transaksi Funnel
    Route::get('transaksi-funnel', [ReportController::class, 'transaksi_funnel']);
    Route::get('detailFunnel', [ReportController::class, 'detail_funnel']);
    Route::get('detailFunnelOff', [ReportController::class, 'detail_funnel_off']);
    
    // Digunakan di Profil
    Route::get('profile',[ProfileController::class, 'index'])->name('aktivasi');
    Route::post('profile/edit',[ProfileController::class, 'ubah']);
    Route::post('profileamd',[ProfileController::class, 'profileamd']);
    Route::patch('profile/{profile}',[ProfileController::class, 'update']);
    Route::patch('profile/{profile}',[ProfileController::class, 'update']);
    Route::get('profile/jabatan/{id}',[ProfileController::class, 'nama']);
    Route::get('profile/jab',[ProfileController::class, 'jab']);
    Route::post('tambahcom', [ProfileController::class, 'tambahcom']);
    Route::get('entry-company', [ProfileController::class, 'createperus']);
    Route::get('comby', [ProfileController::class, 'comby']);
    Route::get('karyawanhc', [ProfileController::class, 'nama']);
    Route::get('jabatanhc', [ProfileController::class, 'jab']);

    // Digunakan di Saldo Awal
    Route::get('saldo-awal', [AkuntasiController::class, 'saldo_awal']);
    Route::get('saldo-awal-data', [AkuntasiController::class, 'saldo_awal_ah']);
    Route::get('saldo_first', [AkuntasiController::class, 'saldo_first']);
    Route::get('trial-balance', [TesController::class, 'trial_balance']);
    Route::post('trial-balance', [TesController::class, 'trial_balance']);
    Route::get('trial_data', [AkuntasiController::class, 'trial_data']);
    Route::get('trial_footer', [TesController::class, 'trial_footer']);
    Route::post('postClosing', [TesController::class, 'postClosing']);
    Route::get('detail-batal-closing/{id}', [TesController::class, 'detailBatclos']);
    Route::post('batalClosing', [AkuntasiController::class, 'batalClosing']);
    Route::get('getsaldoaw', [AkuntasiController::class, 'getsaldoaw']);
    Route::post('update_saldo', [AkuntasiController::class, 'update_saldo']);
    Route::get('tombol_closing', [AkuntasiController::class, 'tombol_closing']);
    Route::get('saldo_awal_export', [AkuntasiController::class, 'saldo_awal_export']);
    Route::get('trial_balance_export', [TesController::class, 'trial_balance_export']);
    Route::get('trial-balance-detail', [TesController::class, 'detailRow']);
    Route::get('trial-balance-detail-export', [TesController::class, 'detail_export']);
    Route::get('detail-batal-closing-export', [TesController::class, 'batalClosingExport']);
    Route::get('rin_canclos', [TesController::class, 'rin_canclos']);
    Route::get('/download/{filename}', [TesController::class, 'download'])->name('download');

    // DIgunakan di Penutupan
    Route::get('penutupan', [AkuntasiController::class, 'penutupan']);
    Route::get('caribank/{id}', [AkuntasiController::class, 'caribank']);
    Route::get('carikantor/{id}', [AkuntasiController::class, 'carikantor']);
    Route::get('penutupan_ekspor', [AkuntasiController::class, 'penutupan_ekspor']);
    Route::get('cari_akun_penutupan', [AkuntasiController::class, 'cari_akun_penutupan']);
    Route::post('updatepen', [AkuntasiController::class, 'updatepen']);
    Route::post('tutupin', [AkuntasiController::class, 'tutupin']);
    
    
    //digunakan di fins-baru
    
    //digunakan di fins-home
    Route::get('kas-bank', [FinsController::class, 'kas_bank']);
    Route::get('kas-bank/export', [FinsController::class, 'KBexport']);
    Route::get('kas-bank-total', [FinsController::class, 'total']);
    Route::get('saldo_dana', [FinsController::class, 'saldo_dana']);
    Route::get('kas-bank-cash', [FinsController::class, 'totalcash']);
    Route::get('kas-bank-bank', [FinsController::class, 'totalbank']);
    Route::get('tot-pengajuan', [FinsController::class, 'totalpengajuan']);
    Route::get('tot-pengeluaran', [FinsController::class, 'totalpengeluaran']);
    Route::get('tot-penerimaan', [FinsController::class, 'totalpenerimaan']);
    
    //digunakan di fins-budget
    Route::get('pengajuan-ca', [FinsController::class, 'pengajuan_ca']);
    Route::post('post_pengajuan', [FinsController::class, 'post_pengajuan']);
    Route::get('pengajuanBy/{id}', [FinsController::class, 'pengajuanby']);
    Route::get('pengajuan-anggaran/export', [FinsController::class, 'PAexport']);
    Route::get('downloadformat/export', [FinsController::class, 'downloadformat']);
    Route::post('pengajuananggaran/import', [FinsController::class, 'import']);
    Route::post('aksipengajuan', [FinsController::class, 'aksipengajuan']);
    Route::post('editspengajuan', [FinsController::class, 'editspengajuan']);
    Route::get('acc_all', [FinsController::class, 'acc_all']);
    Route::get('approve-anggaran', [FinsController::class, 'approve_anggaran']);
    Route::post('editaggaran', [FinsController::class, 'editaggaran']);
    Route::get('getreal', [FinsController::class, 'getreal']);
    Route::get('getjumrealisasi', [FinsController::class, 'getjumrealisasi']);
    Route::post('editdp', [FinsController::class, 'editdptabelprogdp']);
    Route::post('editdpprog', [FinsController::class, 'editdp']);
    Route::get('getsemuaprogram', [FinsController::class, 'getsemuaprogram']);
    Route::get('getsemuajumtrans', [FinsController::class, 'jumtrans']);


    Route::get('min_waktu', [FinsController::class, 'min_waktu_pengajuan']);
    Route::post('edit_waktu', [FinsController::class, 'update_waktupemgajuan']);
    
    Route::get('getsemuacoa', [FinsController::class, 'getsemuacoa']);
    Route::post('post_anggaran', [FinsController::class, 'post_anggaran']);
    Route::get('getcoauntukrelokasi', [FinsController::class, 'getcoauntukrelokasi']);

    Route::get('resume-dana-pengelola', [FinsController::class, 'resumedanapengelola']);
    Route::get('tot-dana-pengelola', [FinsController::class, 'resumedanapengelola']);
    Route::get('resumeBy', [FinsController::class, 'resumeBy']);
    Route::get('transaksiBy/{id}', [FinsController::class, 'transaksiBy']);
    Route::get('resume-dana-pengelola/export', [FinsController::class, 'danapengelola_export']);

   
    Route::get('resume-anggaran', [FinsController::class, 'resume_anggaran']);
    Route::get('resume-anggaran/export', [FinsController::class, 'raexport']);

    // digunakan untuk setting baru
     Route::get('uang-persediaan', [UangPersediaanController::class, 'uang_persediaan']);
     Route::get('cek-persediaan', [UangPersediaanController::class, 'cek_persediaan']);
     Route::post('post_up', [UangPersediaanController::class, 'post_up']);
     
     Route::get('jenis-laporan', [SettingLaporanController::class, 'jenis_laporan']);
     Route::post('post_laporan', [SettingLaporanController::class, 'post_laporan']);
     Route::get('list-laporan', [SettingLaporanController::class, 'list_laporan']);
     Route::post('post_rumus', [SettingLaporanController::class, 'post_rumus']);
     Route::post('edit_rumus_stts', [SettingLaporanController::class, 'edit_rumus_stts']);
     Route::post('edurut_rumus', [SettingLaporanController::class, 'edurut_rumus']);
     Route::get('lapBy/{id}', [SettingLaporanController::class, 'lapBy']);
     Route::post('edlap_stat', [SettingLaporanController::class, 'edlap_stat']);
     Route::get('rumBy/{id}', [SettingLaporanController::class, 'rumBy']);
     Route::post('edrum', [SettingLaporanController::class, 'edrum']);
     Route::get('jenis-laporan/export', [SettingLaporanController::class, 'export']);
     Route::get('jenis-laporan/hapus/{id}', [SettingLaporanController::class, 'destroyy']);
     Route::get('parentcoa', [SettingLaporanController::class, 'parentcoa']);
        
    //Route::get('laporan-keuangan/ekspor', [LaporanKeuanganController::class, 'ekspor']);

    //  Route::get('setting-target', [FinsController::class, 'setting_target']);
    
    // Perencanaan Controller
    Route::get('perencanaan', [PerencanaanController::class, 'index']);
    Route::get('perencanaan/detail', [PerencanaanController::class, 'form']);
    Route::post('perencanaan/add', [PerencanaanController::class, 'tambah_rencana']);
    Route::get('get_marketing', [PerencanaanController::class, 'get_marketing']);
    Route::post('edit_marketing', [PerencanaanController::class, 'edit_marketing']);
    Route::post('tambah_marketing', [PerencanaanController::class, 'tambah_marketing']);
    Route::get('edit_get_marketing', [PerencanaanController::class, 'edit_get_marketing']);
    Route::get('getBytanggal', [PerencanaanController::class, 'getBytanggal']);
    Route::get('getDetail', [PerencanaanController::class, 'getDetail']);
    Route::post('tambah_rencana', [PerencanaanController::class, 'tambah_rencana']);
    Route::post('edit_rencana', [PerencanaanController::class, 'edit_rencana']);
    Route::get('ubah_aktif_rencana', [PerencanaanController::class, 'ubah_aktif_rencana']);
    Route::get('get_rencana_id', [PerencanaanController::class, 'get_rencana_id']);
    Route::get('hapus_rencana', [PerencanaanController::class, 'hapus_rencana']);
    Route::get('laporanBy', [PerencanaanController::class, 'laporanBy']);
    Route::post('konfirmasi_rencana', [PerencanaanController::class, 'konfirmasi_rencana']);
    Route::get('getajasih', [PerencanaanController::class, 'getajasih']);
    Route::get('getBaganHasil', [PerencanaanController::class, 'getBaganHasil']);
    Route::get('rencana_id_modal', [PerencanaanController::class, 'rencana_id_modal']);
    
    Route::get('exportRencana', [PerencanaanController::class, 'exportRencana']);
    
    Route::POST('addRencanaT', [PerencanaanController::class, 'addRencanaT']);
    Route::POST('addRencanaM', [PerencanaanController::class, 'addRencanaM']);
    Route::POST('addRencanaTP', [PerencanaanController::class, 'addRencanaTP']);
    Route::POST('addRencanaS', [PerencanaanController::class, 'addRencanaS']);
    Route::get('getRencanaThn', [PerencanaanController::class, 'getRencanaThn']);
    Route::get('getRencanaBln', [PerencanaanController::class, 'getRencanaBln']);
    
    // KPIController
    Route::get('kpi', [KPIController::class, 'index']);
    Route::get('kpi_kar', [KPIController::class, 'kpi_kar']);
    Route::get('kpi_det', [KPIController::class, 'kpi_det']);
    Route::get('kpidetail', [KPIController::class, 'detail']);
    Route::get('getrendetbul', [KPIController::class, 'getrendetbul']);
    Route::post('postkpii', [KPIController::class, 'postkpii']);
    
    // Jam Kerja Contorller
    Route::get('jam-kerja', [JamKerjaController::class, 'index']);
    Route::post('jamker/{id}', [JamKerjaController::class, 'update']);
    Route::post('entry-jamker', [JamKerjaController::class, 'store']);
    
    // Generate Controller
    Route::get('setting-file', [GenerateController::class, 'index']);
    Route::get('generate-pdf', [GenerateController::class, 'generatePdf']);
    Route::post('save-summernote-content', [GenerateController::class, 'upload']);
    Route::get('save-summernote-show', [GenerateController::class, 'show']);
    Route::get('delete-surat-summernote', [GenerateController::class, 'destroy']);
    Route::get('simpan-tipe-surat', [GenerateController::class, 'simpanTipeSurat']);
    Route::get('upload-pdf/{pdfpath}', [GenerateController::class, 'uploadPdf']);
    Route::post('get-token-midtrans', [ApiUserKaryawanController::class, 'getTokenMidtrans']);

});


$data = DB::table('users')->select('perus')->whereRaw("perus IS NOT NULL")->distinct()->pluck('perus')->toArray();

// $locales = [
//     'kilau',
//     'bsi',
//     'bps'
// ];

if(in_array(Request::segment(1), $data)){
Route::group(['prefix' => Request::segment(1), 'middleware' => ['auth:user']], function () {
    
    // Route::get('ceklogin', [AuthController::class, 'ceklogin']);
    
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::post('dashboard/ubahaja', [DashboardController::class, 'ubahaja']);
    Route::get('targetong', [DashboardController::class, 'target']);
    Route::get('target_by_id', [DashboardController::class, 'targetId']);

    // controller dibawah digunakan management user dan tambah karyawan
    Route::get('getspv', [AuthController::class, 'getspv']);
    Route::get('getjab', [AuthController::class, 'getjab']);
    Route::get('getkan', [AuthController::class, 'getkan']);
    Route::get('getmentor', [AuthController::class, 'getmentor']);
    Route::get('namekaryawan', [AuthController::class, 'namekaryawan']);
    // Route::get('getjandk', [AuthController::class, 'getjandk']);
    Route::get('getpj', [AuthController::class, 'getpj']);
    Route::get('management-user', [AuthController::class, 'akses']);
    Route::get('changeaktifakses', [AuthController::class, 'changeaktifakses']);
    Route::get('getkar', [AuthController::class, 'getkar']);
    Route::post('cobakan', [AuthController::class, 'store']);
    Route::post('aktivasi-perusahaan', [AuthController::class, 'storeUserPerusahaan']);
    Route::post('upkar/{id}',[AuthController::class, 'upkar']);
    Route::post('user', [AuthController::class, 'patch']);
    Route::get('edkar/{id}', [AuthController::class, 'edkar']);
    Route::get('user/getedit/{id}', [AuthController::class, 'geteditkar']);
    Route::get('user/getdata/{id}', [AuthController::class, 'getkarid']);
    Route::get('offuser/{id}', [AuthController::class, 'offuser']);
    Route::get('user/{id}', [AuthController::class, 'destroy']);
    Route::get('change-account', [AuthController::class, 'changeAccount']);
    Route::get('countusers', [AuthController::class, 'countUsersLogin']);
    Route::get('company-layout', [AuthController::class, 'companyLayout']);

    // karyawan controller
    
    
    Route::get('karyawan', [KaryawanController::class, 'index']);
    Route::post('karyawan', [KaryawanController::class, 'store']);
    Route::get('karyawan/detail/{id}', [KaryawanController::class, 'show']);
    Route::get('detailkaryawan/{id}', [KaryawanController::class, 'detailkaryawan']);
    Route::get('changesttsaktif', [KaryawanController::class, 'changesttsaktif']);
    Route::get('getkaryawan', [KaryawanController::class, 'getkaryawan']);
    Route::get('karyawan/hapus/{id}', [KaryawanController::class, 'destroyy']);
    Route::get('karyawan/edit/{id}', [KaryawanController::class, 'edit']);
    Route::post('karyawan/{id}', [KaryawanController::class, 'update']);
    Route::post('karyawanpen/{id}', [KaryawanController::class, 'updatepen']);
    Route::get('karyawan/aktifkan/{id}', [KaryawanController::class, 'aktifken']);
    Route::get('getgol/{id}', [KaryawanController::class, 'getgol']);
    Route::post('postgol/{id}', [KaryawanController::class, 'postgol']);
    Route::get('getkaryawanbyid/{id}', [KaryawanController::class, 'getKaryawanById']);
    Route::get('bpjskar/{id}', [KaryawanController::class, 'getbpjskar']);
    Route::post('upbpjskar/{id}', [KaryawanController::class, 'postbpjskar']);
    Route::post('mutasi-karyawan/{id}', [KaryawanController::class, 'mutasi_karyawan']);
    Route::get('karyawan/create', [KaryawanController::class, 'create']);
    Route::get('riwayatmutasi', [KaryawanController::class, 'riwayatmutasi']);
    Route::get('riwayatkeluarga', [KaryawanController::class, 'riwayatkeluarga']);
    Route::get('riwayatkenaikan', [KaryawanController::class, 'riwayat']);
    Route::get('riwayatjabatan', [KaryawanController::class, 'riwayatjabatan']);
    Route::get('karyawan/exports', [KaryawanController::class, 'exports']);
    Route::get('karyawan/cekcompany', [KaryawanController::class, 'cekcompany']);
    Route::get('getjandk', [PresensiController::class, 'getjandk']);
    Route::get('karyawan-option', [KaryawanController::class, 'select_kar']);
    Route::get('karyawan-export', [KaryawanController::class, 'karyawanExport']);
    Route::get('pengajuan-perubahan', [KaryawanController::class, 'perubahankaryawan']);
    Route::get('perbkarBy/{id}', [KaryawanController::class, 'perbkarBy']);
    Route::post('acc_perubahan/{id}', [KaryawanController::class, 'acc_perubahan']);
    Route::get('akseshc', [KaryawanController::class, 'getakseshc']);
    Route::get('jamhc', [KaryawanController::class, 'getjamhc']);
    Route::get('levelhc', [KaryawanController::class, 'getlevelhc']);
    Route::get('itungkar', [KaryawanController::class, 'itungkar']);
    
// Route::post('/upload-pdf', [YourControllerName::class, 'uploadPdf']);


    // Transaksi Controller
    Route::get('transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::post('transaksi', [TransaksiController::class, 'index']);
    Route::get('transaksi/getdata/{id}', [TransaksiController::class, 'ambilkirim']);
    // Route::get('transaksi/edit/{id}',[TransaksiController::class, 'ambil']);
    Route::get('transaksi/total', [TransaksiController::class, 'total']);
    Route::get('getdon', [TransaksiController::class, 'getdon']);
    Route::get('kolektorr', [TransaksiController::class, 'cek_kolektor']);
    Route::post('app', [TransaksiController::class, 'appr']);
    Route::get('transaksi/delete/{id}', [TransaksiController::class, 'destroyy']);
    Route::get('aproves/{id}', [TransaksiController::class, 'aproves']);
    Route::get('aprove_all', [TransaksiController::class, 'aprove_all']);
    Route::get('add-transaksi', [TransaksiController::class, 'add_tr']);
    Route::get('prog_prog_prog/{prog}', [TransaksiController::class, 'get_prog_prog']);
    Route::get('getprosp/{id}/{prog}', [TransaksiController::class, 'getprosp']);
    Route::get('getinfodonatur/{id}', [TransaksiController::class, 'getinfodon']);
    Route::get('getsave', [TransaksiController::class, 'getsave']);
    Route::post('post_trans', [TransaksiController::class, 'post_trans']);
    Route::get('notifya', [TransaksiController::class, 'notifya'])->name('notifya');
    Route::get('getPengTransBy/{id}', [TransaksiController::class, 'getPengTransBy']);
    Route::post('changenotif/{id}', [TransaksiController::class, 'changenotif']);
    Route::get('getbbjudul/{id}', [TransaksiController::class, 'getbbjudul']);
    
    
    //Bukti Setor Zakat
    Route::get('bukti-setor-zakat', [BuktiSetorController::class, 'buktisetor_zakat']);
    Route::get('buktiBy/{id}', [BuktiSetorController::class, 'buktiBy']);
    Route::get('pdfbuktisetor', [BuktiSetorController::class, 'eksbukti']);
    Route::get('bukti-setor-zakat/export', [BuktiSetorController::class, 'export']);


    Route::get('petugasso', [DonaturController::class, 'petugas_so']);
    Route::get('get_riwayat_t/{id}', [DonaturController::class, 'get_riw']);
    Route::get('riwayatdonasi/{donatur}', [TransaksiController::class, 'riwayat']);
    Route::post('updatedon', [TransaksiController::class, 'updatedon']);

    Route::get('cek_aja_nih', [DonaturController::class, 'cek_aja_nih']);
    Route::get('detail/{id}', [TransaksiController::class, 'detail']);

    Route::get('getcoanoncash', [TransaksiController::class, 'getcoanoncash']);
    Route::post('edittransaksi', [TransaksiController::class, 'edittransaksi']);
    Route::get('transaksi/edit/{id}', [TransaksiController::class, 'edit']);
    Route::get('nm_donaturedit', [TransaksiController::class, 'get_nm1']);

    Route::get('transaksi-rutin', [TransaksiController::class, 'transaksi_rutin']);
    Route::get('transaksi-rutin/ekspor', [TransaksiController::class, 'transaksi_rutin_ekspor']);
    Route::get('transaksi-rutin-detail', [TransaksiController::class, 'transaksi_rutin_detail']);
    // export
    Route::get('transaksi-export', [TransaksiController::class, 'transaksi_export']);

    Route::get('getDntr', [DonaturController::class, 'getDntr']);
    Route::get('list_donat', [DonaturController::class, 'list_donat']);
    Route::get('map_donatur', [DonaturController::class, 'map_donatur']);
    Route::get('lokdon_detail', [DonaturController::class, 'lokdon_detail']);

    // Donatur Controller
    Route::get('donatur', [DonaturController::class, 'index']);
    Route::get('nm_donatur', [DonaturController::class, 'get_nm']);
    Route::get('cek_don/{name}/{id}', [DonaturController::class, 'cek_email']);
    Route::get('offdon/{donatur}', [DonaturController::class, 'offdon']);
    Route::get('donatur/delete/{donatur}', [DonaturController::class, 'destroy']);
    Route::get('changeoffdon', [DonaturController::class, 'changeoffdon']);
    Route::get('donatur/export', [DonaturController::class, 'export']);
    Route::get('adddonatur', [DonaturController::class, 'add']);
    Route::post('adddonatur', [DonaturController::class, 'add_don']);
    Route::get('provinces', [DonaturController::class, 'provinces']);
    Route::get('cities/{id}', [DonaturController::class, 'cities']);
    Route::get('donatur/edit/{id}', [DonaturController::class, 'edit_don']);
    Route::post('donatur/edit/{id}', [DonaturController::class, 'update_don']);
    Route::post('hfmdonatur/edit/{id}', [DonaturController::class, 'update_donhfm']);

    
    
    Route::get('add-donatur', [DonaturController::class, 'add_new']);
    Route::post('add-donatur', [DonaturController::class, 'add_don_new']);

    Route::get('getsumberdana', [ProgramController::class, 'get_sumberdana']);
    Route::get('getid_program/{id}', [ProgramController::class, 'getid_program']);
    
     // Digunakan di program
    Route::get('program', [ProgramController::class, 'getProgram']);
    Route::get('program/sumberdana_edit/{id}', [ProgramController::class, 'sumdit']);
    Route::post('program/sumberdana', [ProgramController::class, 'add_sumberdana']);
    Route::get('program_penerimaan', [ProgramController::class, 'program_penerimaan']);
    Route::get('program_penyaluran', [ProgramController::class, 'program_penyaluran']);
    Route::post('program', [ProgramController::class, 'add_program']);
    Route::post('set_bon', [ProgramController::class, 'set_bon']);
    Route::get('getprograms', [ProgramController::class, 'getprogramparent']);
    Route::get('getprogramsalur', [ProgramController::class, 'getprogramparentsalur']);
    Route::get('program/{id}', [ProgramController::class, 'destroy_program']);
    Route::post('program/update', [ProgramController::class, 'update_program']);
    Route::get('program/edit/{id}', [ProgramController::class, 'edit_program']);
    Route::get('program/getProgs/{id}', [ProgramController::class, 'getProgs']);
    Route::get('getcamp/{id}', [ProgramController::class, 'getcamp']);
    Route::get('ekspor_program_penerimaan', [ProgramController::class, 'ekspor_program_penerimaan']);
    Route::post('add-program-penyaluran', [ProgramController::class, 'add_program_penyaluran']);
    Route::get('edit-program-penyaluran/{id}', [ProgramController::class, 'edit_program_penyaluran']);
    Route::post('update-program-penyaluran', [ProgramController::class, 'update_program_penyaluran']);
    Route::post('delete-program-penyaluran/{id}', [ProgramController::class, 'destroy_program_penyaluran']);
    
    // Digunakan di Rincian DOnasi
    Route::get('riwayat-donasi/{donatur}', [TransaksiController::class, 'detaildon']);
    Route::get('riwayat-kunjungan/{donatur}', [TransaksiController::class, 'riwayat']);
    Route::get('getdata_riwkun/{id}', [TransaksiController::class, 'ambilkirim']);
    Route::get('riwayat-kunjungan/delete/{id}', [TransaksiController::class, 'destroyy']);
    
    // Capaian  Controller
    Route::get('capaian-omset', [HomeController::class, 'capaianomset']);
    Route::get('export_dulu', [HomeController::class, 'export_dulu']);
    
    // Buku Harian
    Route::get('buku-harian', [AkuntasiController::class, 'bukuharian']);
    Route::get('caribuku/{id}', [AkuntasiController::class, 'caribuku']);
    Route::get('buku-harian-dash', [AkuntasiController::class, 'bukuhariandash']);
    Route::get('buku-harian-dash-awal', [AkuntasiController::class, 'bukuhariandashsawal']);
    Route::get('buku_harian_by', [AkuntasiController::class, 'buku_harian_by']);
    Route::post('buku_harian_hapus', [AkuntasiController::class, 'buku_harian_hapus']);
    Route::post('buku_harian_acc', [AkuntasiController::class, 'buku_harian_acc']);
    Route::post('buku_harian_edit', [AkuntasiController::class, 'buku_harian_edit']);
    Route::post('buku_harian_edit_hfm', [AkuntasiController::class, 'buku_harian_edit_hfm']);
    Route::get('getnamcoa', [AkuntasiController::class, 'getnamcoa']);
    Route::get('getnamcoadet', [AkuntasiController::class, 'getnamcoadet']);
    Route::get('buku_harian_export', [AkuntasiController::class, 'buku_harian_export']);
    
    // Buku Besar
    Route::get('buku-besar', [AkuntasiController::class, 'bukubesar']);
    Route::get('buku-besar-export', [AkuntasiController::class, 'bukubesarexport']);
    
    // Rekap Jurnal
    Route::get('rekap-jurnal', [AkuntasiController::class, 'rekapjurnal']);
    Route::post('post_jurnal', [AkuntasiController::class, 'postjurnal']);
    Route::get('export-jurnal', [AkuntasiController::class, 'exportJurnal']);
    
    // Laporan DI Fins
    Route::get('laporan-bulanan', [LaporanKeuanganController::class, 'laporan_bulanan']);
    Route::get('laporan-bulanan-tabel', [LaporanKeuanganController::class, 'laporan_bulanan_tabel']);
    Route::get('laporan-keuangan', [LaporanKeuanganController::class, 'laporan_keuangan']);
    Route::get('laporan-keuangan/ekspor', [LaporanKeuanganController::class, 'ekspor']);
    Route::get('laporan-bulanan/ekspor', [LaporanKeuanganController::class, 'eksporbulanan']);
    Route::get('detail-keuangan', [LaporanKeuanganController::class, 'detail_keuangan']);
    Route::get('detail-debet', [LaporanKeuanganController::class, 'detail_debet']);
    Route::get('detail-kredit', [LaporanKeuanganController::class, 'detail_kredit']);
    Route::get('detail-keuangan2', [LaporanKeuanganController::class, 'detail_keuangan2']);
    Route::get('detail-dsebelum', [LaporanKeuanganController::class, 'detail_debet_sebelumnya']);
    Route::get('detail-ksebelum', [LaporanKeuanganController::class, 'detail_kredit_sebelumnya']);
    Route::get('detail-laporan-keuangan/ekspor', [LaporanKeuanganController::class, 'detail_ekspor']);
    
    // Home Controller
    Route::get('capaian-kolekting', [HomeController::class, 'index']);
    
    Route::get('testing', [HomeController::class, 'testing']);
    Route::get('detailcapdon', [HomeController::class, 'detailcapdon']);
    
    Route::get('testt', [HomeController::class, 'test']);
    Route::get('chart', [HomeController::class, 'chart']);
    Route::get('kota', [HomeController::class, 'kota']);
    Route::get('assign_tot', [HomeController::class, 'assign_tot']);
    Route::get('totdontran', [HomeController::class, 'totdontran']);
    Route::get('test/{id}', [HomeController::class, 'getid']);
    Route::get('datranmod/{id}', [HomeController::class, 'datranmod_getid']);
    Route::get('datdon/{id}', [HomeController::class, 'datdon_getid']);
    
    Route::get('targetgetcab', [HomeController::class, 'targetgetcab']);
    Route::post('targetkacc', [HomeController::class, 'targetkacc']);

    // Digunakan di Data Jalur
    Route::get('getjalur', [JalurController::class, 'getjalur']);
    Route::get('jalur', [JalurController::class, 'index']);
    Route::post('jalur', [JalurController::class, 'add_jalur']);
    Route::post('jalur/update', [JalurController::class, 'update_jalur']);
    Route::post('jalur/updatejalur', [JalurController::class, 'updatejalur']);
    Route::get('jalur/edit/{id}', [JalurController::class, 'edit_jalur']);
    Route::get('jalur/{id}', [JalurController::class, 'delete']);
    Route::get('getspvid', [JalurController::class, 'getspvid']);
    Route::get('getjalurspv', [JalurController::class, 'getjalurspv']);
    Route::get('adajalur', [JalurController::class, 'adajalur']);

    Route::get('assignment', [AssigmentController::class, 'assigmentadmin']);
    Route::get('assign_all', [AssigmentController::class, 'assign_all']);
    Route::get('jalurah', [AssigmentController::class, 'cek_jlr']);
    Route::post('set_warning', [AssigmentController::class, 'set_warning']);
    Route::get('getjumbul', [AssigmentController::class, 'getjumbul']);
    Route::post('actionsadm', [AssigmentController::class, 'actionsadm']);
    Route::get('changeStatusadm', [AssigmentController::class, 'changeStatusadm']);
    Route::get('getjlr_multiple', [AssigmentController::class, 'getjlr_multiple']);
    Route::get('jadwalkan', [AssigmentController::class, 'jadwalkan']);
    Route::get('jadwalkan_all', [AssigmentController::class, 'jadwalkan_all']);
    Route::get('ganti_petugas_bang', [AssigmentController::class, 'ganti_petugas_bang']);
    

    Route::get('data-bonus-kolekting', [BonusController::class, 'index']);
    Route::get('coa-bank/{id}', [BankController::class, 'coa_bank']);
    
    Route::get('coa', [ChartofaccountController::class, 'index']);
    Route::get('coa_coa', [ChartofaccountController::class, 'coa_kun']);
    Route::get('getcoba', [ChartofaccountController::class, 'coba']);
    Route::post('coa',[ChartofaccountController::class, 'store']);
    Route::get('coa/edit/{id}',[ChartofaccountController::class, 'edit']);
    Route::post('coa/update',[ChartofaccountController::class, 'update']);
    Route::get('coa/{id}',[ChartofaccountController::class, 'destroy']);
    Route::get('update_coa/{id}',[ChartofaccountController::class, 'update_kantor']);
    Route::get('getcoapndp', [ChartofaccountController::class, 'getcoapndp']);
    Route::get('getcoapngdp', [ChartofaccountController::class, 'getcoapngdp']);
    Route::get('getcoa', [ChartofaccountController::class, 'getcoa']);
    Route::get('getcoapenyaluran', [ChartofaccountController::class, 'getcoapenyaluran']);
    Route::get('getcoakondisi', [ChartofaccountController::class, 'getcoakondisi']);
    Route::get('getcoalagi', [ChartofaccountController::class, 'getcoalagi']);

    Route::get('getcoapersediaan', [ChartofaccountController::class, 'getcoapersediaan']);
    Route::get('getcoapenerimaan', [ChartofaccountController::class, 'getcoapenerimaan']);
    Route::get('getcoaamil', [ChartofaccountController::class, 'getcoaamil']);
    Route::get('getcoaapbn', [ChartofaccountController::class, 'getcoaapbn']);
    Route::get('getcoahibah', [ChartofaccountController::class, 'getcoahibah']);
    Route::get('getcoainfaqt', [ChartofaccountController::class, 'getcoainfaqt']);
    Route::get('getcoainfaqtd', [ChartofaccountController::class, 'getcoainfaqtd']);
    Route::get('getcoawakaf', [ChartofaccountController::class, 'getcoawakaf']);
    Route::get('getcoadilarang', [ChartofaccountController::class, 'getcoadilarang']);
    Route::get('getcoazkt', [ChartofaccountController::class, 'getcoazkt']);
    Route::get('coapengirimmutasi', [ChartofaccountController::class, 'coapengirimmutasi']);
    Route::get('getcoamutasipengirim', [ChartofaccountController::class, 'getcoamutasipengirim']);
    Route::get('getcoamutasipenerima', [ChartofaccountController::class, 'getcoamutasipenerima']);
    Route::get('getcoasumberdana', [ChartofaccountController::class, 'getcoasumberdana']);
    Route::get('cari_saldo', [ChartofaccountController::class, 'cari_saldo']);
    Route::get('data_anggaran', [ChartofaccountController::class, 'data_anggaran']);

    Route::get('cari_saldox', [ChartofaccountController::class, 'cari_saldox']);
    Route::get('cari_saldonya', [ChartofaccountController::class, 'cari_saldonya']);
    Route::get('cari_saldo2', [ChartofaccountController::class, 'cari_saldo2']);

    Route::get('getcoajasa', [ChartofaccountController::class, 'getcoajasa']);
    
    Route::get('coa-exp', [ChartofaccountController::class, 'coaExport']);

    // Golongan Controller
    Route::get('golongan', [GolonganController::class, 'index']);
    Route::get('golongan/edit/{id}', [GolonganController::class, 'edit']);
    Route::post('golongan/update', [GolonganController::class, 'update']);
    
    // Digunakan di Jabatan
    Route::get('jabatan', [JabatanController::class, 'index']);
    Route::post('jabatan', [JabatanController::class, 'store']);
    Route::get('jabatan/{jabatan}', [JabatanController::class, 'destroy']);
    Route::post('jabatan/{jabatan}', [JabatanController::class, 'update']);
    Route::get('jabatan/edit/{id}', [JabatanController::class, 'edit']);

    // Gaji pokok Controller
    Route::get('gaji-pokok', [GajipokokController::class, 'index']);
    Route::post('intahun', [GajipokokController::class, 'intahun']);
    Route::get('getgapok', [GajipokokController::class, 'getgapok']);
    Route::post('upgapok', [GajipokokController::class, 'upgapok']);
    Route::post('naikper', [GajipokokController::class, 'naikper']);
    Route::post('turunper', [GajipokokController::class, 'turunper']);

    // Gaji Controller
    Route::get('gaji-karyawan', [GajikaryawanController::class, 'index']);
    Route::get('eksportdata', [GajikaryawanController::class, 'eksdata']);
    Route::get('eksportpay', [GajikaryawanController::class, 'ekspay']);
    Route::get('eksportbpjs', [GajikaryawanController::class, 'eksbpjs']);
    Route::get('cekdata', [GajikaryawanController::class, 'cekdata']);

    // presensi controller
    Route::get('kehadiran', [PresensiController::class, 'index']);
    Route::get('kehadiran/exportk', [PresensiController::class, 'exportk']);
    Route::get('kehadiran/exportrekapk', [PresensiController::class, 'exportrekapk']);
    Route::get('kehadiran/{id}', [PresensiController::class, 'ambilkirimdong']);
     Route::get('get_detail_presensi/{id}', [PresensiController::class, 'detailPresensi']);
     Route::get('get_kardinamis', [PresensiController::class, 'kardinamis']);
    
	Route::get('pdf', [PresensiController::class, 'pdf']);
    Route::get('get-kar', [PresensiController::class, 'kar']);
    Route::get('nama_karyawan', [PresensiController::class, 'get_karyawan_nih']);
    Route::get('laporan-karyawan', [PresensiController::class, 'laporan']);
    Route::get('exportlk', [PresensiController::class, 'exportlk']);
    Route::get('getlapkar/{id}', [PresensiController::class, 'ambilkirim']);
   
    Route::get('daftar-request', [PresensiController::class, 'daftar_req']);
    Route::get('daftar-request/exportdr', [PresensiController::class, 'exportdr']);
    Route::get('daftar-request/rinreq/{id}', [PresensiController::class, 'get_rinreq']);
    Route::get('daftar-request/konfirm/{id}', [PresensiController::class, 'konfirm']);
    Route::get('daftar-request/tolak/{id}', [PresensiController::class, 'tolak']);
    Route::post('laporan-karyawan/post_feedback', [PresensiController::class, 'post_feedback']);
    Route::get('accall', [PresensiController::class, 'accall']);
    Route::get('accmilih', [PresensiController::class, 'accmilih']);
    Route::get('rejectall', [PresensiController::class, 'rejectall']);
    Route::get('rejectmilih', [PresensiController::class, 'rejectmilih']);
    
    // Pengumuman
    Route::get('pengumuman', [PresensiController::class, 'pengumuman']);
    Route::get('daftar-pengumuman', [PresensiController::class, 'daftar_pengumuman']);
    Route::post('entry-pengumuman', [PresensiController::class, 'entry_pengumuman']);
    Route::get('detail-pengumuman/{id}', [PresensiController::class, 'detail_pengumuman']);
    Route::get('delete-pengumuman/delete/{id}', [PresensiController::class, 'delete_pengumuman']);
    Route::post('edit-pengumuman/{id}', [PresensiController::class, 'edit_pengumuman']);
    Route::get('kar-pengumuman/{id}', [PresensiController::class, 'kar_pengumuman']);
    Route::get('notif-pengumuman', [PresensiController::class, 'notif_pengumuman']);
    Route::post('on_link_param', [PresensiController::class, 'on_link_param']);
    Route::get('off_link_param', [PresensiController::class, 'off_link_param']);
    
    // Setting Request
    Route::get('setting-request', [PresensiController::class, 'setting_request']);
    Route::get('edit-setting-request/{id}', [PresensiController::class, 'edit_setting_request']);
    Route::post('save-setting-request', [PresensiController::class, 'save_setting_request']);
    Route::get('parent-request', [PresensiController::class, 'parentRequest']);
    Route::get('hapus-setting-req/{id}', [PresensiController::class, 'hapus']);


    // Controller untuk Management Gaji
    Route::get('management-gaji', [TunjanganController::class, 'index']);
    Route::get('getdaerah', [TunjanganController::class, 'getdaerah']);
    Route::get('getterlambat', [TunjanganController::class, 'getterlambat']);
    Route::post('management-gaji/update', [TunjanganController::class, 'update']);
    Route::post('updatembl', [TunjanganController::class, 'updatembl']);
    Route::post('setterlambat', [TunjanganController::class, 'setterlambat']);
    Route::get('management-gaji/{id}', [TunjanganController::class, 'edit']);
    Route::get('gettunjangan', [TunjanganController::class, 'gettunjangan']);
    Route::get('getjabatan', [TunjanganController::class, 'getjabatan']);
    Route::post('updatebpjs', [TunjanganController::class, 'updatebpjs']);
    Route::post('updatetj', [TunjanganController::class, 'updatetj']);
    Route::post('management-gaji/tambahh', [TunjanganController::class, 'tambahh']);
    Route::get('management-gaji/delete/{id}', [TunjanganController::class, 'hapus']);
    Route::get('terlambat-delete/{id}', [TunjanganController::class, 'hapusterlambat']);
    Route::get('gethukuman', [TunjanganController::class, 'gethukuman']);
    Route::get('getbpjs', [TunjanganController::class, 'getbpjs']);
    Route::post('set_terlambat', [TunjanganController::class, 'set_terlambat']);

    // Controller untuk setting kantor
    Route::get('kantor', [KantorController::class, 'index']);
    Route::get('coa-coa-kntr', [KantorController::class, 'coa_coa_kntr']);
    Route::post('kantor', [KantorController::class, 'store']);
    Route::get('kantor/{id}', [KantorController::class, 'destroy']);
    Route::post('kantor/update', [KantorController::class, 'update']);
    Route::get('updatekantor', [KantorController::class, 'updatekantor']);
    Route::get('kantor/edit/{id}', [KantorController::class, 'edit']);
    
    // Controller di setting target
    Route::get('setting-target', [SettingController::class, 'set_target']);
    Route::get('getKolektor', [SettingController::class, 'getKolektor']);
    Route::get('getTargetKantor', [SettingController::class, 'getTargetKantor']);
    Route::post('update_target', [SettingController::class, 'update_target']);
    Route::post('acc_target', [SettingController::class, 'acc_target']);
    Route::post('setTahunan', [SettingController::class, 'setTahunan']);
    
    Route::GET('setTargetPimpinan', [SettingController::class, 'setTargetPimpinan']);
    
    Route::get('setwarning', [SettingController::class, 'setwarning']);
    Route::post('postwarning', [SettingController::class, 'updatewarning']);
    
    Route::get('getPenerimaanSD', [SettingController::class, 'getPenerimaanSD']);
    Route::get('getPengeluaranSD', [SettingController::class, 'getPengeluaranSD']);
    
    // Digunakan pada halaman Setting Saldo Dana
    Route::get('saldo-dana', [SettingController::class, 'saldo_dana']);
    Route::post('post_saldo_dana', [SettingController::class, 'post_saldo_dana']);

    // Digunakan pada halaman Setting Bukti Setor
    Route::get('bukti-setor', [SettingController::class, 'bukti_setor']);
    Route::post('post_bsz', [SettingController::class, 'post_bsz']);
    Route::get('getbsz', [SettingController::class, 'listbsz']);
    Route::get('bszBy/{id}', [SettingController::class, 'bszBy']);
    Route::post('edbsz_stat', [SettingController::class, 'edbsz_stat']);
    Route::get('progBy/{id}', [SettingController::class, 'progBy']);
    Route::post('bsz_pasang', [SettingController::class, 'bsz_pasang']);


    // Controller Penerima Manfaat
    Route::get('penerima-manfaat', [PenerimamanfaatController::class, 'index_pm']);
    Route::post('post_add', [PenerimamanfaatController::class, 'add_penerimaan']);
    Route::get('add-pm', [PenerimamanfaatController::class, 'add_pm']);
    Route::post('post_pm', [PenerimamanfaatController::class, 'post_pm']);
    Route::get('nama_pm', [PenerimamanfaatController::class, 'nama_pm']);
    Route::get('get_info_pm/{id}', [PenerimamanfaatController::class, 'get_info_pm']);
    Route::post('aksipenerimaan', [PenerimamanfaatController::class, 'aksipenerimaan']);
    Route::get('acc_semua_penerimaan', [PenerimamanfaatController::class, 'acc_semua_penerimaan']);
    Route::get('edit-pm/{id}', [PenerimamanfaatController::class, 'infor_pm']);
    Route::get('ubahstat', [PenerimamanfaatController::class, 'editstat']);
    Route::get('hapuspm/{id}', [PenerimamanfaatController::class, 'hapuspm']);
    Route::get('pm/export', [PenerimamanfaatController::class, 'pmexport']);
    Route::post('edtpm', [PenerimamanfaatController::class, 'editpm']);
    Route::get('gethistori/{id}', [PenerimamanfaatController::class, 'get_salur']);



    // Controller ini digunakan di Penerimaan
    Route::get('penerimaan', [PenerimamanfaatController::class, 'index']);
    Route::get('penerimaanBy/{id}', [PenerimamanfaatController::class, 'penerimaanBy']);
    Route::get('penerimaan/ekspor', [PenerimamanfaatController::class, 'ekspor']);

    // Controller Pengeluaran
    Route::get('pengeluaran', [PengeluaranController::class, 'index']);
    Route::post('post_pengeluaran', [PengeluaranController::class, 'post_pengeluaran']);
    Route::post('post_mutasi', [PengeluaranController::class, 'post_mutasi']);
    Route::post('hapus_pengeluaran', [PengeluaranController::class, 'hapus_pengeluaran']);
    Route::get('getcoapengeluaranbank', [PengeluaranController::class, 'getcoapengeluaranbank']);
    Route::get('get_saldo_pengeluaran', [PengeluaranController::class, 'get_saldo_pengeluaran']);
    Route::get('get_saldox_pengeluaran', [PengeluaranController::class, 'get_saldox_pengeluaran']);
    Route::get('get_saldo_pengirim', [PengeluaranController::class, 'get_saldo_pengirim']);
    Route::get('get_saldo_penerima', [PengeluaranController::class, 'get_saldo_penerima']);
    Route::get('pengeluaranBy/{id}', [PengeluaranController::class, 'pengeluaranby']);
    Route::get('pengEdBy/{id}', [PengeluaranController::class, 'pengEdBy']);
    Route::post('aksipeng', [PengeluaranController::class, 'aksipeng']);
    Route::post('editspeng', [PengeluaranController::class, 'editspeng']);
    Route::get('acc_semua', [PengeluaranController::class, 'acc_semua']);
    Route::get('pengeluaran/export', [PengeluaranController::class, 'Pexport']);


    // Controller Penyaluran
    Route::get('penyaluran', [PenyaluranController::class, 'index']);
    Route::get('get_program_penyaluran', [PenyaluranController::class, 'get_program_penyaluran']);
    Route::get('aksi-button', [PenyaluranController::class, 'button']);
    Route::get('edit-penyaluran/{id}', [PenyaluranController::class, 'edit_penyaluran']);
    
    Route::post('post_penyaluran', [PenyaluranController::class, 'post_penyaluran']);
    Route::get('edit-post-penyaluran/{id}', [PenyaluranController::class, 'editPostPenyaluran']);
    Route::get('sales-export', [SalesController::class, 'salesExport']);

    Route::get('capaian-sales', [SalesController::class, 'index']);
    Route::get('get_lap_id/{id}', [SalesController::class, 'get_lap_id']);
    Route::get('get_data_id', [SalesController::class, 'get_data_id']);
    
    Route::get('get_sales_data', [SalesController::class, 'get_sales_data']);
    Route::get('transaksi_langsung/{id}', [SalesController::class, 'transaksi_langsung']);
    Route::get('laporan_folup/{id}', [SalesController::class, 'laporan_folup']);

    Route::get('data-bonus-sales', [BonusController::class, 'bonus_sales']);
    Route::get('bonus_sales_nih/{id}', [BonusController::class, 'bonus_sales_nih']);
    
    // Digunakan di Halaman Bank
    Route::get('bank', [BankController::class, 'index']);
    Route::post('bank',[BankController::class, 'store']);
    Route::get('coa-bank/{id}', [BankController::class, 'coa_bank']);
    Route::post('bank/update',[BankController::class, 'update']);
    Route::get('bank/edit/{id}',[BankController::class, 'edit']);
    Route::get('bank/{id}',[BankController::class, 'destroy']);
    
    Route::get('setting-pembayaran', [BankController::class, 'pembayaran']);
    
      // Digunakan di Report Perubahan Transaksi
    Route::get('riwayat-perubahan', [RiwayatPerubahanController::class, 'riwayat_perubahan']);
    Route::get('detail-perubahan', [RiwayatPerubahanController::class, 'detail_perubahan']);
    Route::get('perubahan-donatur', [RiwayatPerubahanController::class, 'perubahan_donatur']);
    Route::get('perubahan-detdonatur', [RiwayatPerubahanController::class, 'detail_perbdon']);
    
    // Digunakan di analisis Transaksi
    Route::get('analisis-transaksi', [ReportController::class, 'analis_transaksi']);
    Route::get('getPetugas', [ReportController::class, 'getPetugas']);
    Route::get('transaksi_chart', [ReportController::class, 'chart_transaksi']);
    Route::get('analisis_don', [ReportController::class, 'analis_don']);
    Route::get('analis_kunjungan', [ReportController::class, 'analis_kunjungan']);
    Route::get('get_rincian_transaksi/{id}', [ReportController::class, 'get_data_id']);
    Route::get('kunjungan_by_id', [ReportController::class, 'kunjungan_by_id']);
    
    Route::get('lokasi-donatur', [ReportController::class, 'lokdon']);
    Route::get('get_jalur_lokdon', [ProfileController::class, 'get_jalur_lokdon']);
    
    Route::get('analisis-transaksi/ekspor', [ReportController::class, 'eksporanalis']);
    
    // Digunakan di analisis Donatur
    Route::get('analisis-donatur', [ReportController::class, 'analis_donatur']);
    Route::get('chart_donatur', [ReportController::class, 'chart_donatur']);
    Route::get('donatur_detail', [ReportController::class, 'donatur_det']);
    Route::get('donatur/ekspor', [ReportController::class, 'ekspor']);
    Route::get('detail-analisis-donatur/ekspor', [ReportController::class, 'detail_donatur']);
    // Digunakan di Transaksi Funnel
    Route::get('transaksi-funnel', [ReportController::class, 'transaksi_funnel']);
    Route::get('detailFunnel', [ReportController::class, 'detail_funnel']);
    Route::get('detailFunnelOff', [ReportController::class, 'detail_funnel_off']);
    
    // Digunakan di Profil
    Route::get('profile',[ProfileController::class, 'index'])->name('aktivasi');
    Route::post('profile/edit',[ProfileController::class, 'ubah']);
    Route::post('profileamd',[ProfileController::class, 'profileamd']);
    Route::patch('profile/{profile}',[ProfileController::class, 'update']);
    Route::patch('profile/{profile}',[ProfileController::class, 'update']);
    Route::get('profile/jabatan/{id}',[ProfileController::class, 'nama']);
    Route::get('profile/jab',[ProfileController::class, 'jab']);
    Route::post('tambahcom', [ProfileController::class, 'tambahcom']);

    // Digunakan di Saldo Awal
    Route::get('saldo-awal', [AkuntasiController::class, 'saldo_awal']);
    Route::get('saldo-awal-data', [AkuntasiController::class, 'saldo_awal_ah']);
    Route::get('saldo_first', [AkuntasiController::class, 'saldo_first']);
    Route::get('trial-balance', [TesController::class, 'trial_balance']);
    Route::post('trial-balance', [TesController::class, 'trial_balance']);
    Route::get('trial_data', [AkuntasiController::class, 'trial_data']);
    Route::get('trial_footer', [TesController::class, 'trial_footer']);
    Route::post('postClosing', [TesController::class, 'postClosing']);
    Route::get('detail-batal-closing/{id}', [TesController::class, 'detailBatclos']);
    Route::post('batalClosing', [AkuntasiController::class, 'batalClosing']);
    Route::get('getsaldoaw', [AkuntasiController::class, 'getsaldoaw']);
    Route::post('update_saldo', [AkuntasiController::class, 'update_saldo']);
    Route::get('tombol_closing', [AkuntasiController::class, 'tombol_closing']);
    Route::get('saldo_awal_export', [AkuntasiController::class, 'saldo_awal_export']);
    Route::get('trial_balance_export', [TesController::class, 'trial_balance_export']);
    Route::get('trial-balance-detail', [TesController::class, 'detailRow']);
    Route::get('trial-balance-detail-export', [TesController::class, 'detail_export']);
    Route::get('detail-batal-closing-export', [TesController::class, 'batalClosingExport']);
    Route::get('rin_canclos', [TesController::class, 'rin_canclos']);
    Route::get('/download/{filename}', [TesController::class, 'download'])->name('download');

    // DIgunakan di Penutupan
    Route::get('penutupan', [AkuntasiController::class, 'penutupan']);
    Route::get('caribank/{id}', [AkuntasiController::class, 'caribank']);
    Route::get('carikantor/{id}', [AkuntasiController::class, 'carikantor']);
    Route::get('penutupan_ekspor', [AkuntasiController::class, 'penutupan_ekspor']);
    Route::get('cari_akun_penutupan', [AkuntasiController::class, 'cari_akun_penutupan']);
    Route::post('updatepen', [AkuntasiController::class, 'updatepen']);
    Route::post('tutupin', [AkuntasiController::class, 'tutupin']);
    
    
    //digunakan di fins-baru
    
    //digunakan di fins-home
    Route::get('kas-bank', [FinsController::class, 'kas_bank']);
    Route::get('kas-bank/export', [FinsController::class, 'KBexport']);
    Route::get('kas-bank-total', [FinsController::class, 'total']);
    Route::get('saldo_dana', [FinsController::class, 'saldo_dana']);
    Route::get('kas-bank-cash', [FinsController::class, 'totalcash']);
    Route::get('kas-bank-bank', [FinsController::class, 'totalbank']);
    Route::get('tot-pengajuan', [FinsController::class, 'totalpengajuan']);
    Route::get('tot-pengeluaran', [FinsController::class, 'totalpengeluaran']);
    Route::get('tot-penerimaan', [FinsController::class, 'totalpenerimaan']);
    
    //digunakan di fins-budget
    Route::get('pengajuan-ca', [FinsController::class, 'pengajuan_ca']);
    Route::post('post_pengajuan', [FinsController::class, 'post_pengajuan']);
    Route::get('pengajuanBy/{id}', [FinsController::class, 'pengajuanby']);
    Route::get('pengajuan-anggaran/export', [FinsController::class, 'PAexport']);
    Route::get('downloadformat/export', [FinsController::class, 'downloadformat']);
    Route::post('pengajuananggaran/import', [FinsController::class, 'import']);
    Route::post('aksipengajuan', [FinsController::class, 'aksipengajuan']);
    Route::post('editspengajuan', [FinsController::class, 'editspengajuan']);
    Route::get('acc_all', [FinsController::class, 'acc_all']);
    Route::get('approve-anggaran', [FinsController::class, 'approve_anggaran']);
    Route::post('editaggaran', [FinsController::class, 'editaggaran']);
    Route::get('getreal', [FinsController::class, 'getreal']);
    Route::get('getjumrealisasi', [FinsController::class, 'getjumrealisasi']);
    Route::post('editdp', [FinsController::class, 'editdptabelprogdp']);
    Route::post('editdpprog', [FinsController::class, 'editdp']);
    Route::get('getsemuaprogram', [FinsController::class, 'getsemuaprogram']);
    Route::get('getsemuajumtrans', [FinsController::class, 'jumtrans']);


    Route::get('min_waktu', [FinsController::class, 'min_waktu_pengajuan']);
    Route::post('edit_waktu', [FinsController::class, 'update_waktupemgajuan']);
    
    Route::get('getsemuacoa', [FinsController::class, 'getsemuacoa']);
    Route::post('post_anggaran', [FinsController::class, 'post_anggaran']);
    Route::get('getcoauntukrelokasi', [FinsController::class, 'getcoauntukrelokasi']);

    Route::get('resume-dana-pengelola', [FinsController::class, 'resumedanapengelola']);
    Route::get('tot-dana-pengelola', [FinsController::class, 'resumedanapengelola']);
    Route::get('resumeBy', [FinsController::class, 'resumeBy']);
    Route::get('transaksiBy/{id}', [FinsController::class, 'transaksiBy']);
    Route::get('resume-dana-pengelola/export', [FinsController::class, 'danapengelola_export']);

   
    Route::get('resume-anggaran', [FinsController::class, 'resume_anggaran']);
    Route::get('resume-anggaran/export', [FinsController::class, 'raexport']);

    // digunakan untuk setting baru
     Route::get('uang-persediaan', [UangPersediaanController::class, 'uang_persediaan']);
     Route::get('cek-persediaan', [UangPersediaanController::class, 'cek_persediaan']);
     Route::post('post_up', [UangPersediaanController::class, 'post_up']);
     
     Route::get('jenis-laporan', [SettingLaporanController::class, 'jenis_laporan']);
     Route::post('post_laporan', [SettingLaporanController::class, 'post_laporan']);
     Route::get('list-laporan', [SettingLaporanController::class, 'list_laporan']);
     Route::post('post_rumus', [SettingLaporanController::class, 'post_rumus']);
     Route::post('edit_rumus_stts', [SettingLaporanController::class, 'edit_rumus_stts']);
     Route::post('edurut_rumus', [SettingLaporanController::class, 'edurut_rumus']);
     Route::get('lapBy/{id}', [SettingLaporanController::class, 'lapBy']);
     Route::post('edlap_stat', [SettingLaporanController::class, 'edlap_stat']);
     Route::get('rumBy/{id}', [SettingLaporanController::class, 'rumBy']);
     Route::post('edrum', [SettingLaporanController::class, 'edrum']);
     Route::get('jenis-laporan/export', [SettingLaporanController::class, 'export']);
     Route::get('jenis-laporan/hapus/{id}', [SettingLaporanController::class, 'destroyy']);
     Route::get('parentcoa', [SettingLaporanController::class, 'parentcoa']);
        
    //Route::get('laporan-keuangan/ekspor', [LaporanKeuanganController::class, 'ekspor']);

    //  Route::get('setting-target', [FinsController::class, 'setting_target']);
    
    // Perencanaan Controller
    Route::get('perencanaan', [PerencanaanController::class, 'index']);
    Route::get('perencanaan/detail', [PerencanaanController::class, 'form']);
    Route::post('perencanaan/add', [PerencanaanController::class, 'tambah_rencana']);
    Route::get('get_marketing', [PerencanaanController::class, 'get_marketing']);
    Route::post('edit_marketing', [PerencanaanController::class, 'edit_marketing']);
    Route::post('tambah_marketing', [PerencanaanController::class, 'tambah_marketing']);
    Route::get('edit_get_marketing', [PerencanaanController::class, 'edit_get_marketing']);
    Route::get('getBytanggal', [PerencanaanController::class, 'getBytanggal']);
    Route::post('tambah_rencana', [PerencanaanController::class, 'tambah_rencana']);
    Route::post('edit_rencana', [PerencanaanController::class, 'edit_rencana']);
    Route::get('ubah_aktif_rencana', [PerencanaanController::class, 'ubah_aktif_rencana']);
    Route::get('get_rencana_id', [PerencanaanController::class, 'get_rencana_id']);
    Route::get('hapus_rencana', [PerencanaanController::class, 'hapus_rencana']);
    Route::get('laporanBy', [PerencanaanController::class, 'laporanBy']);
    Route::post('konfirmasi_rencana', [PerencanaanController::class, 'konfirmasi_rencana']);
    Route::get('getajasih', [PerencanaanController::class, 'getajasih']);
    
    Route::get('exportRencana', [PerencanaanController::class, 'exportRencana']);
    
    // Jam Kerja Contorller
    Route::get('jam-kerja', [JamKerjaController::class, 'index']);
    Route::post('jamker/{id}', [JamKerjaController::class, 'update']);
    Route::post('entry-jamker', [JamKerjaController::class, 'store']);
    
    // Generate Controller
    Route::get('setting-file', [GenerateController::class, 'index']);
    Route::get('generate-pdf', [GenerateController::class, 'generatePdf']);
    Route::post('save-summernote-content', [GenerateController::class, 'upload']);
    Route::get('save-summernote-show', [GenerateController::class, 'show']);
    Route::get('delete-surat-summernote', [GenerateController::class, 'destroy']);
    Route::get('simpan-tipe-surat', [GenerateController::class, 'simpanTipeSurat']);
    Route::get('upload-pdf/{pdfpath}', [GenerateController::class, 'uploadPdf']);

    
    
});
}
Route::get('kwitansi/{donatur}', [TransaksiController::class, 'test']);
    

Auth::routes();
