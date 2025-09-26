<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserKolek;
use Auth;
use App\Models\Target;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Transaksi;
use App\Models\Transaksi_Perhari;
use App\Models\Transaksi_Perhari_Pending;
use App\Models\Transaksi_Perhari_All;
use App\Models\User;
use App\Models\Donatur;
use App\Models\Kantor;
use App\Models\Tunjangan;
use App\Models\Program;
use App\Models\Prog;
use Carbon\Carbon;
use DataTables;
use DB;

use Excel;
use App\Exports\HomeExport;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
        $sampai = $request->sampaii == '' ? Carbon::now()->toDateString() : $request->sampaii;
        $dari2 = $request->dari2 == '' ? Carbon::now()->toDateString() : $request->dari2;
        $sampai2 = $request->sampai2 == '' ? Carbon::now()->toDateString() : $request->sampai2;
        $field = $request->field;
        $cit = $request->kotas;

        // dd($field);
        $kota = Auth::user()->id_kantor;
        $sum = UserKolek::get();

        if (request()->ajax()) {

            $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
            $sampai = $request->sampaii == '' ? $dari : $request->sampaii;
            $dari2 = $request->dari2 == '' ? Carbon::now()->toDateString() : $request->dari2;
            $sampai2 = $request->sampai2 == '' ? $dari2 : $request->sampai2;
            $bln = $request->bln == '' ? Carbon::now()->format('m-Y') : $request->bln;
            $bln2 = $request->bln2 == '' ? Carbon::now()->format('m-Y') : $request->bln2;

            $field = $request->field;
            
            $kot = $request->kotas == "" ? "id_kantor != ''" : "id_kantor = '$request->kotas'";

            $rkot = $request->kotas;
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
            
            $kunit = $k != null ? $k->id : 'asdfghqwerty';
            $kota = Auth::user()->id_kantor;
            $lev = Auth::user()->kolekting;
            $sum = UserKolek::get();

            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            $bulan1 = Carbon::createFromFormat('m-Y', $bln2)->format('m');
            $tahun1 = Carbon::createFromFormat('m-Y', $bln2)->format('Y');

            $approve = $request->approve == '' ? "approval IS NOT NULL" : "approval = '$request->approve'";

            $dat = [];
            $dat['target'] = Target::where('kota', Auth::user()->kota)->orderBy('created_at', 'desc')->first();
            $dat['sum'] = Transaksi::where('id_kantor', $kota)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('via_input', 'transaksi')->where('approval', '!=', 0)->sum('jumlah');
            $dat['count'] = \DB::select("SELECT count(id) as jumlah from donatur where id_kantor = '$kota' and status = 'belum dikunjungi' and acc = 1 and pembayaran = 'dijemput' or status = 'Tutup' and id_kantor = '$kota' ");
            $dat['countnot'] = \DB::select("SELECT count(id) as jumlah from donatur where id_kantor = '$kota' and status = 'belum dikunjungi' and acc = 0 and pembayaran = 'dijemput' ");
            $dat['datakacab'] = UserKolek::join('transaksi', 'transaksi.id_koleks', '=', 'users.id')
                ->select(\DB::raw("users.name,
                                    SUM(IF(MONTH(transaksi.tanggal) = MONTH(now()) AND YEAR(transaksi.tanggal) = YEAR(now()), transaksi.jumlah, 0 )) AS jumlah"))
                ->groupBy('users.name')
                ->where('users.kolektor', 'kolektor')
                ->where('users.aktif', 1)
                ->where('users.id_kantor', $kota)
                ->get();
            $ahh = Auth::user()->id_kantor;
            // $dat['kntr'] = DB::table('targets')->whereRaw("MONTH(tanggal) = MONTH(now()) AND YEAR(tanggal) = YEAR(now()) AND id_jenis = '$ahh' AND jenis_target = 'kan' ")->first()->target;
            $jil = DB::table('targets')->whereRaw("MONTH(tanggal) = MONTH(now()) AND YEAR(tanggal) = YEAR(now()) AND id_jenis = '$ahh' AND jenis_target = 'kan' ");
            if(count($jil->get()) > 0){
                $dat['kntr'] = $jil->first()->target;
            }else{
                 $dat['kntr'] = 0;
            }
            
            
                
            $dat['belum'] = User::join('donatur', 'donatur.petugas', '=', 'users.name')
                ->select(\DB::raw("users.name, users.id_kantor, 
                        COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totkun,
                        COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totup"))
                ->groupBy('users.name', 'users.id_kantor', 'users.kolektor', 'users.aktif')
                ->where('users.id_kantor', $kota)
                ->where('users.kolektor', 'kolektor')
                ->where('users.aktif', 1)
                ->get();
            $dat['belummas'] = User::join('donatur', 'donatur.petugas', '=', 'users.name')
                ->select(\DB::raw("users.name, users.id_kantor,
                            COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totkun"))
                ->groupBy('users.name', 'users.id_kantor', 'users.kolektor', 'users.aktif')
                ->where('users.id_kantor', $kota)
                ->where('users.kolektor', 'kolektor')
                ->where('users.aktif', 1)
                ->get();
            $dat['totset'] = Transaksi::select(\DB::raw("SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND via_input = 'transaksi', jumlah, 0)) AS Totset, 
                         SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' AND via_input = 'transaksi' , jumlah, 0)) AS Totset2"))->where('via_input', 'transaksi')
                ->get();
            $tj = Tunjangan::first();
            if ($request->tab == 'tab1') {
                if ($request->approve == 1) {
                    $ppp = 'App\Models\Transaksi_Perhari';
                } else if ($request->approve == 2) {
                    $ppp = 'App\Models\Transaksi_Perhari_Pending';
                } else if ($request->approve == 0) {
                    $ppp = 'App\Models\Transaksi_Perhari_All';
                }else if ($request->approve == 3){
                    $ppp = 'App\Models\Transaksi_Perhari_Reject';
                    $oi = "transaksi_perhari0.id_kantor";
                }

                if (Auth::user()->kolekting =='kacab' | Auth::user()->kolekting =='spv') {
                    if ($request->plhtgl == 0) {
                        $data = \App::make($ppp)->select(\DB::raw("name, id, id_jabatan,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) AS Omset,
                        SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0)) AS Omset2,
                        ((SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) - SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0))) / SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0)) * 100) as growth,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tdm, 0)) AS tdm,
                        SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , tdm, 0)) AS tdm2, 
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), honor, 0)) AS honor,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), bonus_cap, 0)) AS totcap,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , donasi, 0)) AS donasi,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , t_donasi, 0)) AS t_donasi,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup, 0)) AS tutup,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup_x, 0)) AS tutup_x,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , ditarik, 0)) AS ditarik,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , k_hilang, 0)) AS k_hilang,
                        COUNT(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))

                            ->where(function ($query) use ($kunit, $kota, $rkot, $lev) {
                                if ($lev == 'kacab') {
                                    if ($rkot == "") {
                                        $query->where('id_kantor', $kota)->orWhere('id_kantor', $kunit);
                                    } else {
                                        $query->where('id_kantor', $rkot);
                                    }
                                } else {
                                    $query->where('id_kantor', $kota);
                                }
                            })
                            ->where(function ($query) use ($dari, $sampai, $dari2, $sampai2, $request) {
                                if ($request->vs == 'no') {
                                    $query->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai);
                                } else {
                                    $query->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                                        ->orWhereDate('tanggal', '>=', $dari2)->whereDate('tanggal', '<=', $sampai2);
                                }
                            })
                            ->where('id_jabatan', $tj->kolektor)
                            ->groupBy('name', 'id', 'id_jabatan')->get();
                    } else {
                        $data = \App::make($ppp)->select(\DB::raw("name, id, id_jabatan,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset,
                        SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0)) AS Omset2,
                        ((SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) - SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0))) / SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0)) * 100) as growth,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tdm, 0)) AS tdm, 
                        SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , tdm, 0)) AS tdm2, 
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), honor, 0)) AS honor,
                        SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), bonus_cap, 0)) AS totcap,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , donasi, 0)) AS donasi,
                        SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', t_donasi, 0)) AS t_donasi,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tutup, 0)) AS tutup,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tutup_x, 0)) AS tutup_x,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , ditarik, 0)) AS ditarik,
                        SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , k_hilang, 0)) AS k_hilang,
                        COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                            ->where(function ($query) use ($kunit, $kota, $rkot, $lev) {
                                if ($lev == 'kacab') {
                                    if ($rkot == "") {
                                        $query->where('id_kantor', $kota)->orWhere('id_kantor', $kunit);
                                    } else {
                                        $query->where('id_kantor', $rkot);
                                    }
                                } else {
                                    $query->where('id_kantor', $kota);
                                }
                            })
                            ->where(function ($query) use ($bulan, $tahun, $bulan1, $tahun1, $request) {
                                if ($request->vs == 'no') {
                                    $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
                                } else {
                                    $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                                        ->orwhereMonth('tanggal', $bulan1)->whereYear('tanggal', $tahun1);
                                }
                            })
                            ->where('id_jabatan', $tj->kolektor)
                            ->groupBy('name', 'id', 'id_jabatan')->get();
                    }
                } elseif (Auth::user()->kolekting =='admin') {
                    if ($request->plhtgl == 0) {
                        $data = \App::make($ppp)->select(\DB::raw("name, id, id_jabatan,
                        
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) AS Omset,
                        SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0)) AS Omset2,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tdm, 0)) AS tdm,
                        SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , tdm, 0)) AS tdm2,
                        ((SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) - SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0))) / SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0)) * 100) as growth,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), honor, 0)) AS honor,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), bonus_cap, 0)) AS totcap,
                        
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , donasi, 0)) AS donasi,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , t_donasi, 0)) AS t_donasi,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup, 0)) AS tutup,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup_x, 0)) AS tutup_x,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , ditarik, 0)) AS ditarik,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , k_hilang, 0)) AS k_hilang,
                        COUNT(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                            ->whereRaw("$kot")
                            ->where(function ($query) use ($dari, $sampai, $dari2, $sampai2, $request) {
                                if ($request->vs == 'no') {
                                    $query->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai);
                                } else {
                                    $query->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                                        ->orWhereDate('tanggal', '>=', $dari2)->whereDate('tanggal', '<=', $sampai2);
                                }
                            })
                            ->where('id_jabatan', $tj->kolektor)
                            ->groupBy('name', 'id', 'id_jabatan')->get();
                    } else {
                        $data = \App::make($ppp)->select(\DB::raw("name, id, id_jabatan,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset,
                        SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0)) AS Omset2,
                        ((SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) - SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0))) / SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0)) * 100) as growth,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tdm, 0)) AS tdm, 
                        SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , tdm, 0)) AS tdm2, 
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), honor, 0)) AS honor,
                        SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), bonus_cap, 0)) AS totcap,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , donasi, 0)) AS donasi,
                        SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', t_donasi, 0)) AS t_donasi,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tutup, 0)) AS tutup,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tutup_x, 0)) AS tutup_x,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , ditarik, 0)) AS ditarik,
                        SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , k_hilang, 0)) AS k_hilang,
                        COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                            ->whereRaw("$kot")
                            ->where(function ($query) use ($bulan, $tahun, $bulan1, $tahun1, $request) {
                                if ($request->vs == 'no') {
                                    $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
                                } else {
                                    $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                                        ->orwhereMonth('tanggal', $bulan1)->whereYear('tanggal', $tahun1);
                                }
                            })
                            ->where('id_jabatan', $tj->kolektor)
                            ->groupBy('name', 'id', 'id_jabatan')->get();
                    }
                }

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('omset', function ($data) {

                        $cuk = number_format($data->Omset, 0, ',', '.');
                        $dana = '<a data-bs-toggle="modal" class="dal" id="' . $data->id . '" data-bs-target="#modaldonasi" href="javascript:void(0)" style="color:#1f5daa">Rp. ' . $cuk . '</a>';

                        return $dana;
                    })

                    ->addColumn('jabatan', function ($data) {
                        if ($data->id_jabatan > 0) {
                            $om = Jabatan::where('id', $data->id_jabatan)->where('id_com', Auth::user()->id_com)->first()->jabatan;
                        } else {
                            $om = '';
                        }
                        return $om;
                    })

                    ->addColumn('omset2', function ($data) {
                        $om = 'Rp. ' . number_format($data->Omset2, 0, ',', '.');
                        return $om;
                    })
                    ->addColumn('growth', function ($data) {
                        if ($data->growth < 0) {
                            $cot = '<span class="badge badge-danger">' . round($data->growth, 2) . ' %</span>';
                        } elseif ($data->growth > 0) {
                            $cot = '<span class="badge badge-success">' . round($data->growth, 2) . ' %</span>';
                        } else {
                            $cot = '<span class="badge badge-info">' . round($data->growth, 2) . ' %</span>';
                        }
                        return $cot;
                    })
                    ->addColumn('Tdm', function ($data) {
                        $om = $data->tdm . ' Transaksi';
                        return $om;
                    })
                    ->addColumn('Tdm2', function ($data) {
                        $om = $data->tdm2 . ' Transaksi';
                        return $om;
                    })

                    ->addColumn('tot', function ($data) {
                        $om = $data->tot . ' x';
                        return $om;
                    })
                    ->rawColumns(['omset', 'growth'])
                    ->make(true);
            }

            if ($request->tab == 'tab2') {
                if ($request->approve == 1) {
                    $ppp = 'App\Models\Transaksi_Perhari';
                    $oi = "transaksi_perhari.id_kantor";
                } else if ($request->approve == 2) {
                    $ppp = 'App\Models\Transaksi_Perhari_Pending';
                    $oi = "transaksi_perhari2.id_kantor";
                } else if ($request->approve == 0) {
                    $ppp = 'App\Models\Transaksi_Perhari_All';
                    $oi = "transaksi_perhari_all.id_kantor";
                }else if ($request->approve == 3){
                    $ppp = 'App\Models\Transaksi_Perhari_Reject';
                    $oi = "transaksi_perhari0.id_kantor";
                }

                $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
                $sampai = $request->sampaii == '' ? $dari : $request->sampaii;
                $dari2 = $request->dari2 == '' ? Carbon::now()->toDateString() : $request->dari2;
                $sampai2 = $request->sampai2 == '' ? $dari2 : $request->sampai2;
                $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
                $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
                $bulan1 = Carbon::createFromFormat('m-Y', $bln2)->format('m');
                $tahun1 = Carbon::createFromFormat('m-Y', $bln2)->format('Y');

                $field = $request->field;
                $cit = $request->kotas;
                $kot = $request->kotas == "" ? "id_kantor IS NOT NULL" : "id_kantor = '$request->kotas'";

                $month = date("n", strtotime($dari));
                $datee = date("d", strtotime($dari));
                $year = date("Y", strtotime($dari));

                $darwal = date("Y-m-01", strtotime($dari));
                $darkhir = date("Y-m-t", strtotime($sampai));

                $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
                $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
                $bulan1 = Carbon::createFromFormat('m-Y', $bln2)->format('m');
                $tahun1 = Carbon::createFromFormat('m-Y', $bln2)->format('Y');
                
                $approve = $request->approve == '' ? "approval IS NOT NULL" : "approval = '$request->approve'";

                $kota = Auth::user()->id_kantor;
                $sum = UserKolek::get();
                // dd($request->kota);
                if (Auth::user()->kolekting =='kacab' | Auth::user()->kolekting =='spv') {
                    if ($field == 'kota') {
                        if ($request->plhtgl == 0) {
                            $data = \App::make($ppp)::join('tambahan', $oi, '=', 'tambahan.id')->select(\DB::raw("tambahan.unit, id_jabatan,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai' , jumlah, 0)) AS Omset,
                            SUM(IF( DATE(Tanggal) >= '$dari2' AND DATE(Tanggal) <= '$sampai2' , jumlah, 0)) AS Omset2,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai', donasi, 0)) AS donasi,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai', t_donasi, 0)) AS t_donasi,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai', tutup, 0)) AS tutup,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai', tutup_x, 0)) AS tutup_x,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai', ditarik, 0)) AS ditarik,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai', k_hilang, 0)) AS k_hilang"))
                                ->where(function ($query) use ($kunit, $kota, $rkot) {
                                    if ($rkot == "") {
                                        $query->where('id_kantor', $kota)->orWhere('id_kantor', $kunit);
                                    } else {
                                        $query->where('id_kantor', $rkot);
                                    }
                                })
                                ->where('id_jabatan', $tj->kolektor)
                                ->groupBy('id_kantor')->orderBy('id_kantor', 'ASC')->get();
                        } else {
                           $data = \App::make($ppp)::join('tambahan', $oi, '=', 'tambahan.id')->select(\DB::raw("tambahan.unit, id_jabatan,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset,
                            SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0)) AS Omset2,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', donasi, 0)) AS donasi,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', t_donasi, 0)) AS t_donasi,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tutup, 0)) AS tutup,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tutup_x, 0)) AS tutup_x,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', ditarik, 0)) AS ditarik,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', k_hilang, 0)) AS k_hilang"))
                                // ->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                                ->where(function ($query) use ($kunit, $kota, $rkot) {
                                    if ($rkot == "") {
                                        $query->where('tambahan.id', $kota)->orWhere('tambahan.id', $kunit);
                                    } else {
                                        $query->where('tambahan.id', $rkot);
                                    }
                                })
                                ->where('id_jabatan', $tj->kolektor)
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC')->get();
                        }

                        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('total', function ($data) {
                                $cuk1 = $data->donasi + $data->t_donasi + $data->tutup + $data->tutup_x + $data->ditarik + $data->k_hilang;
                                // $honor = '$cuk1;
                                return $cuk1;
                            })
                            ->rawColumns(['name'])
                            ->make(true);
                    } else {

                        if ($request->plhtgl == 0) {
                            $data = \App::make($ppp)->select(\DB::raw("name, id, id_jabatan, 
                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) AS Omset,
                    SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0)) AS Omset2,
                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tdm, 0)) AS tdm, 
                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , honor, 0)) AS honor,
                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , bonus_cap, 0)) AS totcap,
                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , donasi, 0)) AS donasi,
                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , t_donasi, 0)) AS t_donasi,
                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup, 0)) AS tutup,
                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup_x, 0)) AS tutup_x,
                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , ditarik, 0)) AS ditarik,
                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , k_hilang, 0)) AS k_hilang,
                    COUNT(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                    COUNT(IF(MONTH(tanggal) = '$month' AND YEAR(tanggal) = '$year' AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                    COUNT(IF(MONTH(tanggal) = '$month' AND YEAR(tanggal) = '$year' AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                                ->where(function ($query) use ($kunit, $kota, $rkot, $lev) {
                                    if ($lev == 'kacab') {
                                        if ($rkot == "") {
                                            $query->where('id_kantor', $kota)->orWhere('id_kantor', $kunit);
                                        } else {
                                            $query->where('id_kantor', $rkot);
                                        }
                                    } else {
                                        $query->where('id_kantor', $kota);
                                    }
                                })
                                ->whereDate('tanggal', '>=', $darwal)->whereDate('tanggal', '<=', $darkhir)
                                ->where('id_jabatan', $tj->kolektor)
                                ->groupBy('name', 'id')->get();
                        } else {
                            $data = \App::make($ppp)->select(\DB::raw("name, id, id_jabatan, 
                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset,
                    SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0)) AS Omset2,
                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tdm, 0)) AS tdm, 
                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , honor, 0)) AS honor,
                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , bonus_cap, 0)) AS totcap,
                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , donasi, 0)) AS donasi,
                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , t_donasi, 0)) AS t_donasi,
                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tutup, 0)) AS tutup,
                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tutup_x, 0)) AS tutup_x,
                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , ditarik, 0)) AS ditarik,
                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , k_hilang, 0)) AS k_hilang,
                    COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                    COUNT(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                    COUNT(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                                ->where(function ($query) use ($kunit, $kota, $rkot, $lev) {
                                    if ($lev == 'kacab') {
                                        if ($rkot == "") {
                                            $query->where('id_kantor', $kota)->orWhere('id_kantor', $kunit);
                                        } else {
                                            $query->where('id_kantor', $rkot);
                                        }
                                    } else {
                                        $query->where('id_kantor', $kota);
                                    }
                                })
                                ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                                ->where('id_jabatan', $tj->kolektor)
                                ->groupBy('name', 'id')->get();
                        }
                        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('name', function ($data) {
                                if ($data->conwar >= 3) {
                                    if (date('H') >= 16) {
                                        $ju = $data->conwar;
                                    } else {
                                        $ju = $data->conwarnow;
                                    }
                                    $btn = '<a data-bs-toggle="modal" class="dalwar table-danger" id="' . $data->id . '" data-bs-target="#modalwar" href="javascript:void(0)">
                                       ' . $data->name . ' | ' . $ju . 'x
                                    </a>';
                                } else {
                                    $btn = '<a data-bs-toggle="modal"  class="dalwar" id="' . $data->id . '" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa">' . $data->name . '</a>';
                                }
                                return $btn;
                            })
                            ->addColumn('total', function ($data) {
                                $cuk1 = $data->donasi + $data->t_donasi + $data->tutup + $data->tutup_x + $data->ditarik + $data->k_hilang;
                                // $honor = '$cuk1;
                                return $cuk1;
                            })
                            ->rawColumns(['name'])
                            ->make(true);
                    }
                } elseif (Auth::user()->kolekting =='admin') {
                    if ($field == 'kota') {
                        if ($request->plhtgl == 0) {
                            
                            $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("tambahan.unit,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    // ->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                            ->whereRaw("$approve")
                            ->where('tambahan.id_com', Auth::user()->id_com)
                            ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                        } else {
                            
                            $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("tambahan.unit,
                                COUNT(DISTINCT IF( MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    // ->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                            ->whereRaw("$approve")
                            ->where('tambahan.id_com', Auth::user()->id_com)
                            ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                        }

                        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('total', function ($data) {
                                $cuk1 = $data->donasi + $data->t_donasi + $data->tutup + $data->tutup_x + $data->ditarik + $data->k_hilang;
                                // $honor = '$cuk1;
                                return $cuk1;
                            })
                            ->rawColumns(['name'])
                            ->make(true);
                    } else {
                        if ($request->plhtgl == 0) {
                            
                            // $data = User::join('transaksi', 'transaksi.id_koleks', '=', 'users.id')->where('transaksi.via_input', 'transaksi')
                            //         ->select(\DB::raw("users.name, users.id_jabatan, transaksi.id_koleks as id,
                            //     COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                            //     COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                            //     COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                            //     COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                            //     COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                            //     COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                            //     // COUNT(IF(MONTH(transaksi.tanggal) = MONTH(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , transaksi.tanggal, NULL)) AS conwar,
                            //     // COUNT(IF(MONTH(transaksi.tanggal) = MONTH(NOW()) AND DATE(transaksi.tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , transaksi.tanggal, NULL)) AS conwarnow
                            //         // ->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                            // ->whereRaw("$approve AND $kot AND users.aktif = '1'")
                            // ->where('id_jabatan', $tj->kolektor)
                            // ->groupBy('name','id');
                            
                            
                            $data = \App::make($ppp)->select(\DB::raw("name,id, id_jabatan, 
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) AS Omset,
                                SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0)) AS Omset2,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tdm, 0)) AS tdm, 
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , honor, 0)) AS honor,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , bonus_cap, 0)) AS totcap,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , donasi, 0)) AS donasi,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , t_donasi, 0)) AS t_donasi,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup, 0)) AS tutup,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup_x, 0)) AS tutup_x,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , ditarik, 0)) AS ditarik,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , k_hilang, 0)) AS k_hilang,
                                COUNT(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                                COUNT(IF(MONTH(tanggal) = '$month' AND YEAR(tanggal) = '$year' AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                                COUNT(IF(MONTH(tanggal) = '$month' AND YEAR(tanggal) = '$year' AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                                ->whereRaw("$kot")
                                ->whereDate('tanggal', '>=', $darwal)->whereDate('tanggal', '<=', $darkhir)
                                ->where('id_jabatan', $tj->kolektor)
                                ->groupBy('name', 'id')->get();
                        } else {
                            
                            // $data = User::join('transaksi', 'transaksi.id_koleks', '=', 'users.id')->where('transaksi.via_input', 'transaksi')
                            //         ->select(\DB::raw("users.name, users.id_jabatan, transaksi.id_koleks as id,
                            //     COUNT(DISTINCT IF( MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                            //     COUNT(DISTINCT IF( MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                            //     COUNT(DISTINCT IF( MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                            //     COUNT(DISTINCT IF( MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                            //     COUNT(DISTINCT IF( MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                            //     COUNT(DISTINCT IF( MONTH(transaksi.tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                            //         // ->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                            // ->whereRaw("$approve AND $kot AND users.aktif = '1'")
                            // ->where('id_jabatan', $tj->kolektor)
                            // ->groupBy('name','id');
                            
                            $data = \App::make($ppp)->select(\DB::raw("name,id, id_jabatan, 
                                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset,
                                SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0)) AS Omset2,
                                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tdm, 0)) AS tdm, 
                                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , honor, 0)) AS honor,
                                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , bonus_cap, 0)) AS totcap,
                                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , donasi, 0)) AS donasi,
                                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , t_donasi, 0)) AS t_donasi,
                                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tutup, 0)) AS tutup,
                                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tutup_x, 0)) AS tutup_x,
                                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , ditarik, 0)) AS ditarik,
                                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , k_hilang, 0)) AS k_hilang,
                                COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                                COUNT(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                                COUNT(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                                ->whereRaw("$kot")
                                ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                                ->where('id_jabatan', $tj->kolektor)
                                ->groupBy('name', 'id')->get();
                        }

                        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('name', function ($data) {
                                if ($data->conwar >= 3) {
                                    if (date('H') >= 16) {
                                        $ju = $data->conwar;
                                    } else {
                                        $ju = $data->conwarnow;
                                    }
                                    $btn = '<a data-bs-toggle="modal" class="dalwar table-danger" id="' . $data->id . '" data-bs-target="#modalwar" href="javascript:void(0)">
                                       ' . $data->name . ' | ' . $ju . 'x
                                    </a>';
                                } else {
                                    $btn = '<a data-bs-toggle="modal"  class="dalwar" id="' . $data->id . '" data-bs-target="#modalwar" href="javascript:void(0)" style="color:#1f5daa">' . $data->name . '</a>';
                                }
                                return $btn;
                            })
                            ->addColumn('total', function ($data) {
                                $cuk1 = $data->donasi + $data->t_donasi + $data->tutup + $data->tutup_x + $data->ditarik + $data->k_hilang;
                                // $honor = '$cuk1;
                                return $cuk1;
                            })
                            ->rawColumns(['name'])
                            ->make(true);
                    }
                }
            }
            
            if ($request->tab == 'tab3') {
                
                if ($request->approve == 1) {
                    $ppp = 'App\Models\Transaksi_Perhari';
                    $oi = "transaksi_perhari.id_kantor";
                } else if ($request->approve == 2) {
                    $ppp = 'App\Models\Transaksi_Perhari_Pending';
                    $oi = "transaksi_perhari2.id_kantor";
                } else if ($request->approve == 0) {
                    $ppp = 'App\Models\Transaksi_Perhari_All';
                    $oi = "transaksi_perhari_all.id_kantor";
                }else if ($request->approve == 3){
                    $ppp = 'App\Models\Transaksi_Perhari_Reject';
                    $oi = "transaksi_perhari0.id_kantor";
                }
                
                $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
                $sampai = $request->sampaii == '' ? Carbon::now()->toDateString() : $request->sampaii;
                $dari2 = $request->dari2 == '' ? Carbon::now()->toDateString() : $request->dari2;
                $sampai2 = $request->sampai2 == '' ? Carbon::now()->toDateString() : $request->sampai2;
                $field = $request->field;
                $cit = $request->kotas;
                $kot = $request->kotas == "" ? "id_kantor != 'hahaha'" : "id_kantor = '$request->kotas'";
                $kot2 = $request->kotas == "" ? "transaksi.id_kantor != 'hahaha'" : "transaksi.id_kantor = '$request->kotas'";

                $kota = Auth::user()->id_kantor;
                $sum = UserKolek::get();
                if (Auth::user()->kolekting =='admin') {
                    if ($field == 'kota') {
                        if ($request->plhtgl == 0) {
                            $data = \App::make($ppp)::join('tambahan', $oi, '=', 'tambahan.id')->select(\DB::raw("tambahan.unit, id_jabatan,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai' , jumlah, 0)) AS Omset,
                            SUM(IF( DATE(Tanggal) >= '$dari2' AND DATE(Tanggal) <= '$sampai2' , jumlah, 0)) AS Omset2,
                            
                            COUNT(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai' AND jumlah > 0, tdm, 0)) AS jum1,
                            COUNT(IF( DATE(Tanggal) >= '$dari2' AND DATE(Tanggal) <= '$sampai2' AND jumlah > 0 , tdm, 0)) AS jum2,
                            
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai', donasi, 0)) AS donasi,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai', t_donasi, 0)) AS t_donasi,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai', tutup, 0)) AS tutup,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai', tutup_x, 0)) AS tutup_x,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai', ditarik, 0)) AS ditarik,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai', k_hilang, 0)) AS k_hilang"))
                            ->where('id_jabatan', $tj->kolektor)
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                        } else {
                            $data = \App::make($ppp)::join('tambahan', $oi, '=', 'tambahan.id')->select(\DB::raw("tambahan.unit, id_jabatan,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset,
                            SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0)) AS Omset2,
                            
                            COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND jumlah > 0, tdm, 0)) AS jum1,
                            COUNT(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' AND jumlah > 0 , tdm, 0)) AS jum2,
                            
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', donasi, 0)) AS donasi,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', t_donasi, 0)) AS t_donasi,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tutup, 0)) AS tutup,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tutup_x, 0)) AS tutup_x,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', ditarik, 0)) AS ditarik,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', k_hilang, 0)) AS k_hilang"))
                            ->where('id_jabatan', $tj->kolektor)
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                        }
                    }
                } elseif (Auth::user()->kolekting =='kacab' | Auth::user()->kolekting =='spv') {
                    if ($field == 'kota') {
                        if ($request->plhtgl == 0) {
                            $data = \App::make($ppp)::join('tambahan', $oi, '=', 'tambahan.id')->select(\DB::raw("tambahan.unit, id_jabatan,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai' , jumlah, 0)) AS Omset,
                            SUM(IF( DATE(Tanggal) >= '$dari2' AND DATE(Tanggal) <= '$sampai2' , jumlah, 0)) AS Omset2,
                            
                            COUNT(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai' AND jumlah > 0, tdm, 0)) AS jum1,
                            COUNT(IF( DATE(Tanggal) >= '$dari2' AND DATE(Tanggal) <= '$sampai2' AND jumlah > 0 , tdm, 0)) AS jum2,
                            
                            ((SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai' , jumlah, 0)) - SUM(IF( DATE(Tanggal) >= '$dari2' AND DATE(Tanggal) <= '$sampai2' , jumlah, 0))) / SUM(IF( DATE(Tanggal) >= '$dari2' AND DATE(Tanggal) <= '$sampai2' , jumlah, 0)) * 100) as growth,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai' , donasi, 0)) AS donasi,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai' , t_donasi, 0)) AS t_donasi,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai' , tutup, 0)) AS tutup,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai' , tutup_x, 0)) AS tutup_x,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai' , ditarik, 0)) AS ditarik,
                            SUM(IF( DATE(Tanggal) >= '$dari' AND DATE(Tanggal) <= '$sampai' , k_hilang, 0)) AS k_hilang"))
                                ->where(function ($query) use ($kunit, $kota, $rkot) {
                                    if ($rkot == "") {
                                        $query->where('id_kantor', $kota)->orWhere('id_kantor', $kunit);
                                    } else {
                                        $query->where('id_kantor', $rkot);
                                    }
                                })
                                ->where('id_jabatan', $tj->kolektor)
                                ->groupBy('id_kantor')->orderBy('id_kantor', 'ASC');
                        } else {
                            $data = \App::make($ppp)::join('tambahan', $oi, '=', 'tambahan.id')->select(\DB::raw("tambahan.unit, id_jabatan,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset,
                            SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0)) AS Omset2,
                            
                            COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND jumlah > 0, tdm, 0)) AS jum1,
                            COUNT(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' AND jumlah > 0 , tdm, 0)) AS jum2,
                            
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', donasi, 0)) AS donasi,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', t_donasi, 0)) AS t_donasi,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tutup, 0)) AS tutup,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tutup_x, 0)) AS tutup_x,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', ditarik, 0)) AS ditarik,
                            SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', k_hilang, 0)) AS k_hilang"))
                                ->where(function ($query) use ($kunit, $kota, $rkot) {
                                    if ($rkot == "") {
                                        $query->where('tambahan.id', $kota)->orWhere('tambahan.id', $kunit);
                                    } else {
                                        $query->where('tambahan.id', $rkot);
                                    }
                                })
                                ->where('id_jabatan', $tj->kolektor)
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                        }
                    }
                }
                
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('oomset', function ($data) {


                        $cuk = number_format($data->Omset, 0, ',', '.');
                        $dana = 'Rp. ' . $cuk;

                        return $dana;
                    })
                    ->addColumn('oomsets', function ($data) {
                        $om = 'Rp. ' . number_format($data->Omset2, 0, ',', '.');
                        return $om;
                    })
                    ->addColumn('growth', function ($data) {
                        if ($data->growth < 0) {
                            $cot = '<span class="badge badge-danger">' . round($data->growth, 2) . ' %</span>';
                        } elseif ($data->growth > 0) {
                            $cot = '<span class="badge badge-success">' . round($data->growth, 2) . ' %</span>';
                        } else {
                            $cot = '<span class="badge badge-info">' . round($data->growth, 2) . ' %</span>';
                        }
                        return $cot;
                    })
                    
                    ->addColumn('jum1', function($data){
                        $om =$data->jum1;
                        return $om;
                    })
                    
                    ->addColumn('jum2', function($data){
                        $om =$data->jum2;
                        return $om;
                    })

                    ->rawColumns(['growth'])

                    ->make(true);
            }

            return $dat;
        }


        $rincian = \DB::select("SELECT DATE_FORMAT(tanggal, '%m/%Y') as bulan, DATE_FORMAT(tanggal, '%M, %Y') as namebulan from rincian_belum GROUP BY DATE_FORMAT(tanggal, '%m/%Y'), DATE_FORMAT(tanggal, '%M, %Y')");
        $au = Auth::user()->id_com;
        $datacabang = \DB::select("SELECT * from tambahan WHERE id_com = '$au'");



        $belum = \DB::select("SELECT users.name, users.kota,
         COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totkun,
         COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totup
         FROM users LEFT JOIN donatur ON donatur.petugas = users.name
         GROUP BY users.name, users.kota, users.kolektor, users.aktif HAVING users.kolektor = 'kolektor' AND users.aktif = 1");



        $belumass = \DB::select("SELECT users.name, users.kota,
         COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totkun
         FROM users LEFT JOIN donatur ON donatur.petugas = users.name
         GROUP BY users.name, users.kota, users.kolektor, users.aktif HAVING users.kolektor = 'kolektor' AND users.aktif = 1");

        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        $kot = Auth::user()->id_kantor;

        if(Auth::user()->kolekting == 'admin'){
            $kotas = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else if(Auth::user()->kolekting == 'kacab'){
            if($k == null){
                $kotas = Kantor::where('id',$kot)->where('id_com', Auth::user()->id_com)->get();
            }else{
                $kotas = Kantor::whereRaw("(id = '$kot' OR id = '$k->id')")->where('id_com', Auth::user()->id_com)->get();
            }
        }
        $progs = Prog::all();
        //  $progs = \DB::select("SELECT distinct subprogram from transaksi where kota != 'null'");

        $tahunn = Transaksi::select(\DB::raw("YEAR(created_at) AS date"))->where('via_input', 'transaksi')->groupBy('date')->get();
        $pem = Transaksi::selectRaw("DISTINCT(pembayaran) as pembayaran")->whereRaw("pembayaran IS NOT NULL AND via_input = 'transaksi'")->get();

        return view('kolekting.index', compact('rincian', 'datacabang', 'dari', 'sampai', 'dari2', 'sampai2', 'field', 'kotas', 'progs', 'belum', 'belumass', 'tahunn','pem'));
    }
    
    
    public function testing(Request $request){
        
        $kan = Auth::user()->id_kantor;
        $k = Kantor::where('kantor_induk', $kan)->first();
        $tj = Tunjangan::first();
        
        if($request->ajax()){
            
            $dari = $request->darii == '' ? date('Y-m-d') : $request->darii;
            $sampai = $request->sampaii == '' ? date('Y-m-d') : $request->sampaii;
            
            $bln = $request->bln == '' ? date('m-Y') : $request->bln;
            $bln2 = $request->bln2 == '' ? date('m-Y') : $request->bln2;
            
            if($request->plhtgl == 0){
                $jadi = "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'";
            }else{
                $jadi = "DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln'";
            }
            
            $approve = $request->approve == '' || $request->approve == 0 ? "approval IS NOT NULL" : "approval = '$request->approve'";
            
            $data = Transaksi::selectRaw("COUNT(id) as jumlah_transaksi, id_donatur, id_koleks, tanggal, status, donatur, kolektor")
                    ->whereRaw("$jadi AND id_donatur IS NOT NULL AND via_input = 'transaksi' AND $approve")
                    ->where(function($q) use ($k, $kan){
                        if(Auth::user()->kolekting == 'kacab'){
                            if($k == null){
                                $q->whereRaw("id_kantor = '$kan'");
                            }else{
                                $q->whereRaw("id_kantor = '$kan' OR id_kantor = '$k->id'");
                            }
                        }else if(Auth::user()->kolekting == 'admin'){
                            $q->whereRaw("id_kantor IS NOT NULL");
                        }
                    })
                    
                    ->where(function($query) use ($request) {
                        if(isset($request->bayar)){
                            $query->whereIn('pembayaran', $request->bayar);
                        }
                    })
                    
                    ->whereRaw("id_koleks IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor')")
                                
                    ->groupBy('id_donatur', 'id_koleks')
                    ->orderBy('kolektor','asc')
                    ->get();
            
            $ya = Transaksi::selectRaw("*")
                ->whereRaw("$jadi AND id_donatur IS NOT NULL AND via_input = 'transaksi' AND $approve")
                ->where(function($q) use ($k, $kan){
                    if(Auth::user()->kolekting == 'kacab'){
                        if($k == null){
                            $q->whereRaw("id_kantor = '$kan'");
                        }else{
                            $q->whereRaw("id_kantor = '$kan' OR id_kantor = '$k->id'");
                        }
                    }else if(Auth::user()->kolekting == 'admin'){
                        $q->whereRaw("id_kantor IS NOT NULL");
                    }
                })
                
                ->whereRaw("id_koleks IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor')")
                
                ->where(function($query) use ($request) {
                    if(isset($request->bayar)){
                        $query->whereIn('pembayaran', $request->bayar);
                    }
                })
                
                ->get();
            
            $aya = [];
            $don = 0;
            $t_don = 0;
            $tut = 0;
            $tut_x = 0;
            $dit = 0;
            $k_hil = 0;
            $totol = 0;
            
            foreach($data as $da){
                
                if($da->jumlah_transaksi > 1){
                    $don = $da->id_donatur;
                    $kol = $da->id_koleks;
                    $tgl = $da->tanggal;
                    
                    $filteredData = $ya->filter(function ($item) use ($kol, $don) {
                        return $item->id_donatur == $don && $item->id_koleks == $kol;
                    });
                    
                    $sortedData = collect($filteredData)->sortByDesc('tanggal');
    
                    // Get the first element from the sorted array, which now contains the latest data
                    $sttsoke = $sortedData->first()->status;
                    $tgloke = $sortedData->first()->tanggal;
                    
                    $donasi = $sttsoke == 'Donasi' ? 1 : 0;
                    $t_donasi = $sttsoke == 'Tidak Donasi' ? 1 : 0;
                    $tutup = $sttsoke == 'Tutup' ? 1 : 0;
                    $tutup_x = $sttsoke == 'Tutup 2x' ? 1 : 0;
                    $ditarik = $sttsoke == 'Ditarik' ? 1 : 0;
                    $k_hilang = $sttsoke == 'Kotak Hilang' ? 1 : 0;
                }else{
                    $sttsoke = $da->status;
                    $tgloke = $da->tanggal;
                    
                    $donasi = $da->status == 'Donasi' ? 1 : 0;
                    $t_donasi = $da->status == 'Tidak Donasi' ? 1 : 0;
                    $tutup = $da->status == 'Tutup' ? 1 : 0;
                    $tutup_x = $da->status == 'Tutup 2x' ? 1 : 0;
                    $ditarik = $da->status == 'Ditarik' ? 1 : 0;
                    $k_hilang = $da->status == 'Kotak Hilang' ? 1 : 0;
                }
                
                $aya[] = [
                    'kolektor' => $da->kolektor,
                    'id_koleks' => $da->id_koleks,
                    'donatur' => $da->donatur,
                    'id_donatur' => $da->id_donatur,
                    'jumlah_t' => $da->jumlah_transaksi,
                    'status' => $sttsoke,
                    'tanggal' => $tgloke,
                    'donasi' => $donasi,
                    't_donasi' => $t_donasi,
                    'tutup' => $tutup,
                    'tutup_x' => $tutup_x,
                    'ditarik' => $ditarik,
                    'k_hilang' => $k_hilang,
                ];
            }
            
            $mergedData = collect($aya)->groupBy('kolektor');
            
            $result = [];
            
            foreach ($mergedData as $kolektor => $items) {
                $result[] = [
                    'kolektor' => $kolektor,
                    'id_koleks' => $items[0]['id_koleks'],
                    'donasi' => $items->sum('donasi'),
                    't_donasi' => $items->sum('t_donasi'),
                    'tutup' => $items->sum('tutup'),
                    'tutup_x' => $items->sum('tutup_x'),
                    'ditarik' => $items->sum('ditarik'),
                    'k_hilang' => $items->sum('k_hilang'),
                    'total' => $items->sum('k_hilang') + $items->sum('donasi') + $items->sum('t_donasi') + $items->sum('tutup') + $items->sum('ditarik') + $items->sum('tutup_x')
                ];
            }
            
            if($request->tab == 'tab1'){
                return $result;
            }
            
            return DataTables::of($result)->addIndexColumn()->make(true);
        }
        return view('kolekting.index');
    }
    
    public function detailcapdon(Request $request){
        
        $dari = $request->darii == '' ? date('Y-m-d') : $request->darii;
        $sampai = $request->sampaii == '' ? date('Y-m-d') : $request->sampaii;
            
        $bln = $request->bln == '' ? date('m-Y') : $request->bln;
        
        $approve = $request->approve == '' || $request->approve == 0 ? "approval IS NOT NULL" : "approval = '$request->approve'";
        
        if($request->plhtgl == 0){
            $jadi = "DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai'";
        }else{
            $jadi = "DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln'";
        }
        
        
        $ya = Transaksi::selectRaw("*")
                ->whereRaw("$jadi AND id_donatur IS NOT NULL AND via_input = 'transaksi' AND $approve")
                
                ->whereRaw("id_koleks = '$request->id_kolek' AND status = '$request->status'")
                
                ->groupBy('id_donatur')
                ->get();
        
        
        return $ya;
    }
    
    
    public function totdontran(Request $request)
    {
        // dd('asd');
         $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
        $sampai = $request->sampaii == '' ? Carbon::now()->toDateString() : $request->sampaii;
        $dari2 = $request->dari2 == '' ? Carbon::now()->toDateString() : $request->dari2;
        $sampai2 = $request->sampai2 == '' ? Carbon::now()->toDateString() : $request->sampai2;
        $field = $request->field;
        $cit = $request->kotas;
        
        // dd($field);
        $kota = Auth::user()->id_kantor;
        $sum = UserKolek::get();
        
        if(request()->ajax()){
            
        $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
        $sampai = $request->sampaii == '' ? $dari : $request->sampaii;
        $dari2 = $request->dari2 == '' ? Carbon::now()->toDateString() : $request->dari2;
        $sampai2 = $request->sampai2 == '' ? $dari2 : $request->sampai2;
        $bln = $request->bln == '' ? Carbon::now()->format('m-Y') : $request->bln;
        $bln2 = $request->bln2 == '' ? Carbon::now()->format('m-Y') : $request->bln2;
        
        // $dar = $request->vs == 'no' || $dari <= $dari2 ? $dari : $dari2;
        // $sam = $request->vs == 'no' || $sampai >= $sampai2 ? $sampai : $sampai2;
        
        $field = $request->field;
        // $cit = $request->kotas;
        $kot = $request->kotas == "" ? "id_kantor != ''" : "id_kantor = '$request->kotas'";
        
        $rkot = $request->kotas;
        $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        // dd($k);
        $kunit = $k != null ? $k->id : 'asdfghqwerty';
        $kota = Auth::user()->id_kantor;
        $lev = Auth::user()->kolekting;
        $sum = UserKolek::get();
        
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        $bulan1 = Carbon::createFromFormat('m-Y', $bln2)->format('m');
        $tahun1 = Carbon::createFromFormat('m-Y', $bln2)->format('Y');
        
        
        $dat = [];
        $tj = Tunjangan::first();
        if($request->tab == 'tab1')
        {
            if($request->approve == 1){
                $ppp = 'App\Models\Transaksi_Perhari';
            }else if($request->approve == 2){
                $ppp = 'App\Models\Transaksi_Perhari_Pending';
            }else if($request->approve == 0){
                $ppp = 'App\Models\Transaksi_Perhari_All';
            }
            // return(\App::make($ppp));
            
            if(Auth::user()->kolekting == 'kacab' | Auth::user()->kolekting == 'spv'){
                if($request->plhtgl == 0){
                $data = \App::make($ppp)->select(\DB::raw("name, id,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) AS Omset,
                        SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0)) AS Omset2,
                        
                        COUNT(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND jumlah > 0, tdm, 0)) AS jum1,
                        COUNT(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' AND jumlah > 0 , tdm, 0)) AS jum2,
                        
                        ((SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) - SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0))) / SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0)) * 100) as growth,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tdm, 0)) AS tdm,
                        SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , tdm, 0)) AS tdm2, 
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), honor, 0)) AS honor,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), bonus_cap, 0)) AS totcap,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , donasi, 0)) AS donasi,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , t_donasi, 0)) AS t_donasi,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup, 0)) AS tutup,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup_x, 0)) AS tutup_x,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , ditarik, 0)) AS ditarik,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , k_hilang, 0)) AS k_hilang,
                        COUNT(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                        
                        ->where(function($query) use ($kunit, $kota, $rkot, $lev) {
                            if($lev == 'kacab'){
                                if($rkot == ""){
                                    $query->where('id_kantor', $kota)->orWhere('id_kantor', $kunit);
                                }else{
                                    $query->where('id_kantor', $rkot);
                                }
                            }else{
                                $query->where('id_kantor', $kota);
                            }
                        })
                        ->where(function($query) use ($dari, $sampai, $dari2, $sampai2, $request) {
                            if($request->vs == 'no'){
                            $query->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai);
                            }else{
                            $query->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                                  ->orWhereDate('tanggal', '>=', $dari2)->whereDate('tanggal', '<=', $sampai2);
                            }
                        })
                        ->where('id_jabatan', $tj->kolektor)
                        ->groupBy('name', 'id');
                }else{
                    $data = \App::make($ppp)->select(\DB::raw("name, id,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset,
                        SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0)) AS Omset2,
                        
                        COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND jumlah > 0, tdm, 0)) AS jum1,
                        COUNT(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' AND jumlah > 0 , tdm, 0)) AS jum2,
                        
                        ((SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) - SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0))) / SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0)) * 100) as growth,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tdm, 0)) AS tdm, 
                        SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , tdm, 0)) AS tdm2, 
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), honor, 0)) AS honor,
                        SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), bonus_cap, 0)) AS totcap,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , donasi, 0)) AS donasi,
                        SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', t_donasi, 0)) AS t_donasi,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tutup, 0)) AS tutup,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tutup_x, 0)) AS tutup_x,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , ditarik, 0)) AS ditarik,
                        SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , k_hilang, 0)) AS k_hilang,
                        COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                        ->where(function($query) use ($kunit, $kota, $rkot, $lev) {
                            if($lev == 'kacab'){
                                if($rkot == ""){
                                    $query->where('id_kantor', $kota)->orWhere('id_kantor', $kunit);
                                }else{
                                    $query->where('id_kantor', $rkot);
                                }
                            }else{
                                $query->where('id_kantor', $kota);
                            }
                        })
                        ->where(function($query) use ($bulan, $tahun, $bulan1, $tahun1, $request) {
                            if($request->vs == 'no'){
                            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
                            }else{
                            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                                  ->orwhereMonth('tanggal', $bulan1)->whereYear('tanggal', $tahun1);
                            }
                        })
                        ->where('id_jabatan', $tj->kolektor)
                        ->groupBy('name', 'id')->get();
                }
            
            }elseif(Auth::user()->kolekting == 'admin'){
                if($request->plhtgl == 0){
                    $data = \App::make($ppp)->select(\DB::raw("name, id,
                        
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) AS Omset,
                        SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0)) AS Omset2,
                        
                        COUNT(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND jumlah > 0, tdm, 0)) AS jum1,
                        COUNT(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' AND jumlah > 0 , tdm, 0)) AS jum2,
                        
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tdm, 0)) AS tdm,
                        SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , tdm, 0)) AS tdm2,
                        ((SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) - SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0))) / SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0)) * 100) as growth,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), honor, 0)) AS honor,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), bonus_cap, 0)) AS totcap,
                        
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , donasi, 0)) AS donasi,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , t_donasi, 0)) AS t_donasi,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup, 0)) AS tutup,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup_x, 0)) AS tutup_x,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , ditarik, 0)) AS ditarik,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , k_hilang, 0)) AS k_hilang,
                        COUNT(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                        ->whereRaw("$kot")
                        ->where(function($query) use ($dari, $sampai, $dari2, $sampai2, $request) {
                            if($request->vs == 'no'){
                            $query->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai);
                            }else{
                            $query->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                                  ->orWhereDate('tanggal', '>=', $dari2)->whereDate('tanggal', '<=', $sampai2);
                            }
                        })
                        ->where('id_jabatan', $tj->kolektor)
                        ->groupBy('name', 'id')->get();
                
                }else{
                    $data = \App::make($ppp)->select(\DB::raw("name, id,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) AS Omset,
                        SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0)) AS Omset2,
                        
                        COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND jumlah > 0, tdm, 0)) AS jum1,
                        COUNT(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' AND jumlah > 0 , tdm, 0)) AS jum2,
                        
                        ((SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , jumlah, 0)) - SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0))) / SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , jumlah, 0)) * 100) as growth,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tdm, 0)) AS tdm, 
                        SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , tdm, 0)) AS tdm2, 
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), honor, 0)) AS honor,
                        SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND id IN (SELECT id FROM users WHERE id_jabatan = '$tj->kolektor'), bonus_cap, 0)) AS totcap,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , donasi, 0)) AS donasi,
                        SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', t_donasi, 0)) AS t_donasi,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , tutup, 0)) AS tutup,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun', tutup_x, 0)) AS tutup_x,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , ditarik, 0)) AS ditarik,
                        SUM(IF(MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , k_hilang, 0)) AS k_hilang,
                        COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                        COUNT(IF(MONTH(tanggal) = MONTH(NOW()) AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                        ->whereRaw("$kot")
                        ->where(function($query) use ($bulan, $tahun, $bulan1, $tahun1, $request) {
                            if($request->vs == 'no'){
                            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
                            }else{
                            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                                  ->orwhereMonth('tanggal', $bulan1)->whereYear('tanggal', $tahun1);
                            }
                        })
                        ->where('id_jabatan', $tj->kolektor)
                        ->groupBy('name', 'id')->get();
                }
                
            }
            
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('omset', function($data){
                       
                        $cuk = number_format($data->Omset, 0, ',', '.');
                        $dana = '<a data-bs-toggle="modal" class="dal" id="'.$data->id.'" data-bs-target="#modaldonasi" href="javascript:void(0)" style="color:#1f5daa">Rp. '.$cuk.'</a>';
                       
                        return $dana;
                    })
                    ->addColumn('omset2', function($data){
                        $om ='Rp. '.number_format($data->Omset2, 0, ',', '.');
                        return $om;
                    })
                    ->addColumn('growth', function($data){
                        if($data->growth < 0){
                            $cot = '<span class="badge badge-xs light badge-danger">'.round($data->growth,2).' %</span>';
                        }elseif($data->growth > 0){
                            $cot = '<span class="badge badge-xs light badge-success">'.round($data->growth,2).' %</span>';
                        }else{
                            $cot = '<span class="badge badge-xs light badge-info">'.round($data->growth,2).' %</span>';
                        }
                        return $cot;
                    })
                    ->addColumn('Tdm', function($data){
                        $om =$data->tdm.' Transaksi';
                        return $om;
                    })
                    ->addColumn('Tdm2', function($data){
                        $om =$data->tdm2.' Transaksi';
                        return $om;
                    })
                    
                    ->addColumn('tot', function($data){
                        $om =$data->tot.' x';
                        return $om;
                    })
                    
                    ->addColumn('jum1', function($data){
                        $om =$data->jum1;
                        return $om;
                    })
                    
                    ->addColumn('jum2', function($data){
                        $om =$data->jum2;
                        return $om;
                    })
                    
                    ->rawColumns(['omset', 'growth'])
                    ->make(true);
        }
        
        return $dat;
        }
    }
    
    public function chartprogram(){
        $data = Prog::join('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                ->select(\DB::raw("DATE(tanggal) AS date, SUM(transaksi.jumlah) as jumlah"))
                ->whereIn('transaksi.id_kantor', function($query) use ($id_com){
                            $query->select('id')
                                    ->from('tambahan')
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
                                            $query->where('id_com', Auth::user()->id_com);
                                        }
                                    });
                            
                        })
                ->groupBy('date')->get();
        // dd($data);
        
       
        
        return $data;
    }
    
    public function test(Request $request){
        // dd($request->kot);
        $kot = $request->kot != '' ? "id_kantor = '$request->kot'" : "id_kantor != ''";
        $kot1 = $request->kot1 != '' ? "id_kantor = '$request->kot1'" : "id_kantor != ''";
        $thn = $request->thn != '' ? "YEAR(created_at) = '$request->thn'" : "YEAR(created_at) != ''";
        
        $data = Transaksi::select(\DB::raw("MONTH(created_at) AS date, SUM(jumlah) as jumlah"))->where('via_input', 'transaksi')
                ->whereRaw("$kot")->whereRaw("$thn")->groupBy('date')->get();
        if(!empty($request->kot1)){
            $dataa = Transaksi::select(\DB::raw("MONTH(created_at) AS date, SUM(jumlah) as jumlah"))->whereRaw("$kot1")->whereRaw("$thn")->where('via_input', 'transaksi')
                    ->groupBy('date')->get();        
        }else{
            $dataa = [];
        }

        
        // dd($data);
        $datas = [];
        $datas['bln'] = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      
        foreach($data as $val){
            
            $datas['kot1'][] = [
                $val->jumlah,    
            ];
        }
        
        $datas['start1'] = $data[0]['date'] - 1;
        // $datas['kot1'] = [$jan, $feb, $mar, $apr,$may, $jun, $jul, $aug, $sep, $oct, $nov, $dec];
        
       
        // dd(count($dataa));
        if(count($dataa) != 0){
        foreach($dataa as $val){
            $datas['kot2'][] = [
                $val->jumlah,    
            ];
        }
        
        $datas['start2'] = $dataa[0]['date'] - 1;
        // $datas['kot2'] = [$jan1, $feb1, $mar1, $apr1,$may1, $jun1, $jul1, $aug1, $sep1, $oct1, $nov1, $dec1];
        }else {
            $datas['kot2'] = [];
        }
        
        return $datas;
    }
    
    
    public function chart(Request $request){
        $id_com = $request->com;
        if($request->tab == 'kantor'){
            if(empty($request->kot) && empty($request->kot1)){
                $data = Transaksi::select(\DB::raw("DATE(tanggal) AS date, SUM(jumlah) as jumlah"))->where('via_input', 'transaksi')
                 ->whereIn('id_kantor', function($query) use ($id_com){
                            $query->select('id')
                                    ->from('tambahan')
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
                                            $query->where('id_com', Auth::user()->id_com);
                                        }
                                    });
                            
                        })
                ->whereRaw("approval = 1 AND pembayaran != 'noncash' AND pembayaran != 'mutasi'")
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
                $dataa = [];
            }elseif(!empty($request->kot) && empty($request->kot1)){
                // dd('sad');
                $data = Transaksi::select(\DB::raw("DATE(tanggal) AS date, SUM(jumlah) as jumlah"))->where('id_kantor', $request->kot)->where('via_input', 'transaksi')
                ->whereIn('id_kantor', function($query) use ($id_com){
                            $query->select('id')
                                    ->from('tambahan')
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
                                            $query->where('id_com', Auth::user()->id_com);
                                        }
                                    });
                            
                        })
                ->whereRaw("approval = 1 AND pembayaran != 'noncash' AND pembayaran != 'mutasi'")
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
                $dataa = [];
            }elseif(empty($request->kot) && !empty($request->kot1)){
                $data = Transaksi::select(\DB::raw("DATE(tanggal) AS date, SUM(jumlah) as jumlah"))->where('id_kantor', $request->kot1)->where('via_input', 'transaksi')
                ->whereRaw("approval = 1 AND pembayaran != 'noncash' AND pembayaran != 'mutasi'")
                ->whereIn('id_kantor', function($query) use ($id_com){
                            $query->select('id')
                                    ->from('tambahan')
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
                                            $query->where('id_com', Auth::user()->id_com);
                                        }
                                    });
                            
                        })
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
                $dataa = [];
            }else{
                $data = Transaksi::select(\DB::raw("DATE(tanggal) AS date, SUM(jumlah) as jumlah"))->where('id_kantor', $request->kot)->where('via_input', 'transaksi')
                ->whereIn('id_kantor', function($query) use ($id_com){
                            $query->select('id')
                                    ->from('tambahan')
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
                                            $query->where('id_com', Auth::user()->id_com);
                                        }
                                    });
                            
                        })
                ->whereRaw("approval = 1 AND pembayaran != 'noncash' AND pembayaran != 'mutasi'")
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
                
                $dataa = Transaksi::select(\DB::raw("DATE(tanggal) AS date, SUM(jumlah) as jumlah"))->where('id_kantor', $request->kot1)->where('via_input', 'transaksi')
                ->whereRaw("approval = 1 AND pembayaran != 'noncash' AND pembayaran != 'mutasi'")
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
            }
            
        }elseif($request->tab == 'program'){
            // dd($request->tab);
            if(empty($request->prog) && empty($request->prog1)){
                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                ->select(\DB::raw("DATE(tanggal) AS date, SUM(transaksi.jumlah) as jumlah"))
                ->whereRaw("approval = 1 AND pembayaran != 'noncash' AND pembayaran != 'mutasi'")
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
                // dd($data);
                $dataa = [];
            }elseif(!empty($request->prog) && empty($request->prog1)){
                // dd('sad');
                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                ->select(\DB::raw("DATE(tanggal) AS date, SUM(transaksi.jumlah) as jumlah"))
                ->where('transaksi.subprogram', $request->prog)
                ->whereRaw("approval = 1 AND pembayaran != 'noncash' AND pembayaran != 'mutasi'")
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
                
                $dataa = [];
            }elseif(empty($request->prog) && !empty($request->prog1)){
                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                ->select(\DB::raw("DATE(tanggal) AS date, SUM(transaksi.jumlah) as jumlah"))
                ->where('transaksi.subprogram', $request->prog1)
                ->whereRaw("approval = 1 AND pembayaran != 'noncash' AND pembayaran != 'mutasi'")
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
                $dataa = [];
            }else{
                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                ->select(\DB::raw("DATE(tanggal) AS date, SUM(transaksi.jumlah) as jumlah"))
                ->where('transaksi.subprogram', $request->prog)
                ->whereRaw("approval = 1 AND pembayaran != 'noncash' AND pembayaran != 'mutasi'")
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
                $dataa = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                ->select(\DB::raw("DATE(tanggal) AS date, SUM(transaksi.jumlah) as jumlah"))
                ->where('transaksi.subprogram', $request->prog1)
                ->whereRaw("approval = 1 AND pembayaran != 'noncash' AND pembayaran != 'mutasi'")
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
            }
        }
        
        // return($data);
        
        $datas = [];
        foreach($data as $value){
            $datas['kot1'][] = [
                strtotime('+1days',strtotime($value->date)) * 1000,
                // $value->date,
                $value->jumlah,
            ];
        }
        
        foreach($dataa as $value){
            $datas['kot2'][] = [
                strtotime('+1days',strtotime($value->date)) * 1000,
                // $value->date,
                $value->jumlah,
            ];
        }
        
        
        return $datas;
        
    }
    
    public function getid($id, Request $request){
        if(request()->ajax())
        {
                $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
                $sampai = $request->sampaii == '' ? $dari : $request->sampaii;
                $dari2 = $request->dari2 == '' ? Carbon::now()->toDateString() : $request->dari2;
                $sampai2 = $request->sampai2 == '' ? $dari2 : $request->sampai2;
                $bln = $request->bln == '' ? date('m-Y') : $request->bln;
                $bln2 = $request->bln2 == '' ? date('m-Y') : $request->bln2;
                $bayarin = $request->bayar;
    
                $field = $request->field;
                $cit = $request->kotas;
                $kot = $request->kotas == "" ? "transaksi.id_kantor IS NOT NULL" : "transaksi.id_kantor = '$request->kotas'";
                $bayar = $request->bayar;
                $approve = $request->approve == '' ? "transaksi.approval IS NOT NULL" : "transaksi.approval = '$request->approve'";
                
                $rkot = $request->kotas;
                
                // $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
                // $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
                
                // $bulan1 = Carbon::createFromFormat('m-Y', $bln2)->format('m');
                // $tahun1 = Carbon::createFromFormat('m-Y', $bln2)->format('Y');
                
                $bulan = date('m', strtotime('01-'.$bln));
                $bulan1 = date('m', strtotime('01-'.$bln2));
                
                $tahun = date('Y', strtotime('01-'.$bln));
                $tahun1 = date('Y', strtotime('01-'.$bln2));
                
                // return([$coba1, $coba2]);
                
                // $kunit = $k != null ? $k->id : 'asdfghqwerty';
                $kota = Auth::user()->id_kantor;
                $lev = Auth::user()->kolekting;
                $sum = UserKolek::get();
                
                $bulannew = date('m', strtotime($dari));
                $tahunnew =date('Y', strtotime($dari));
                
                $tahunn = $request->thn == '' ? date('Y') : $request->thn ;
                $tahunn2 = $request->thn1 == '' ? date('Y') : $request->thn1;
                $prd = $request->plhtgl;
                if($prd == 1){
                    $select = "SUM(IF( DATE_FORMAT(tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0))";
                }else if($prd == 2){
                    $select = "SUM(IF(YEAR(tanggal) = '$tahunn' , transaksi.jumlah, 0))";
                }else{
                    $select = "SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai', transaksi.jumlah, 0))";
                }    
                
                // dd($field);
                if($field == ''){
                    $idJoin = 'id_koleks';
                }else if($field == 'program'){
                    $idJoin = 'id_program';
                }else if($field == 'kota'){
                    $idJoin = 'id_kantor';
                }
                
            $data = [];
            $data['dari'] = $request->darii;
            $data['sampai'] = $request->sampaii;
            $data['datdon'] = Donatur::join('transaksi', 'transaksi.id_donatur', '=', 'donatur.id')
                                ->select(\DB::raw("donatur.nama, transaksi.$idJoin, $select AS Omset"))
                                ->whereRaw("$kot AND $approve")
                                ->where(function($query) use ($request, $bayarin) {
                                    if(isset($request->bayar)){
                                        $query->whereIn('transaksi.pembayaran', $bayarin);
                                    }
                                })
                                ->groupByRaw("donatur.nama, transaksi.$idJoin")
                                ->havingRaw("$select > 0 AND transaksi.$idJoin = '$id'")
                                ->get();
                                // dd($data);
            return $data;
        }
    }
    
    public function datdon_getid($id, Request $request){
        if(request()->ajax())
        {
            
            $data = [];
            $data['datdon'] = Donatur::leftjoin('transaksi', 'transaksi.id_donatur', '=', 'donatur.id')
                            ->select(\DB::raw("donatur.nama, transaksi.id_koleks, DATE(transaksi.tanggal) AS date,
                            SUM(transaksi.jumlah) AS Omset"))
                            ->whereDate('transaksi.tanggal', $request->tgl)
                            ->groupBy('donatur.id', 'transaksi.id_koleks', 'date')
                            ->havingRaw("Omset >= 1 AND transaksi.id_koleks = '$id'")->get();
            // $data['datdon'] = \DB::select("SELECT donatur.nama, transaksi.id_koleks, DATE(tanggal) AS date,
            //              SUM(transaksi.jumlah) AS Omset
            //              FROM donatur LEFT JOIN transaksi
            //              ON donatur.id = transaksi.id_donatur WHERE DATE(tanggal) = '$request->tgl'
            //              GROUP BY donatur.id, transaksi.id_koleks, DATE(tanggal) HAVING Omset >= 1 AND transaksi.id_koleks = '$id' ");
            return $data;
        }
    }
    
     public function datranmod_getid($id, Request $request){
        if(request()->ajax())
        {
            
            // dd($request->dari);
                 
            $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
            $sampai = $request->sampaii == '' ? $dari : $request->sampaii;
            $bln = $request->bln == '' ? Carbon::now()->format('m-Y') : $request->bln;
            
            
            $field = $request->field;
            // $cit = $request->kotas;
            $kot = $request->kotas == "" ? "kota != ''" : "kota = '$request->kotas'";
            $kot2 = $request->kotas == "" ? "" : $request->kotas;
            
            $kota = Auth::user()->kota;
            $sum = UserKolek::get();
            
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        
            $month = date("n",strtotime($dari));
            $year = date("Y",strtotime($dari));
            $data = [];
            if($request->plhtgl == 0){
                $data['datranmod'] = \DB::select("SELECT * FROM transaksi_perhari WHERE id = '$id' AND MONTH(tanggal) = '$month' AND YEAR(tanggal) = '$year' ORDER BY tanggal ASC");
            }else{
                $data['datranmod'] = \DB::select("SELECT * FROM transaksi_perhari WHERE id = '$id' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' ORDER BY tanggal ASC");
            }
            return $data;
        }
    }
    
    public function tot(){
        if(request()->ajax())
        {
            
            return $data;
        }
    }
    
   
    
    public function target()
    {
        return view('target.create');
    }

    public function targetedit($id)
{
    $target = Target::findOrFail($id);
    return view('target.edit',compact('target'));
}

public function update($id, Request $request)

{
    $target = Target::findOrFail($id);

    $input = $request->all();
  
    $target->update($input);
    return redirect('home');
}

public function store(Request $request){
   $target = new Target;
   $target->kota =$request->kota;
   $target->target = preg_replace("/[^0-9]/", "", $request->target);
   $target->save();
   return back();

}

public function setting()
{
    $karyawan = Karyawan::get();
    return view('setting.index',compact('karyawan'));
}

function kota(Request $request)
{
        
            $kota = $request->kota;
            $bulan = $request->bulan;
            if($kota != null && $bulan == null){
            $kol = \DB::select("SELECT name, id_kantor, totkun, totup, belkun, beltup FROM belum WHERE id_kantor = '$kota' ");
            }elseif($kota != null && $bulan != null){
            $kol = \DB::select("SELECT * FROM rincian_belum WHERE id_kantor = '$kota' AND DATE_FORMAT(tanggal, '%m/%Y') = '$bulan' ");
            }elseif($kota == null && $bulan != null){
            $kol = \DB::select("SELECT * FROM rincian_belum WHERE DATE_FORMAT(tanggal, '%m/%Y') = '$bulan' ");
            }else{
            $kol = \DB::select("SELECT name, id_kantor, totkun, totup, belkun, beltup FROM belum");  
            }
            
            $potongan = Tunjangan::first();
            if($request->tab == 'kot'){
                
                $data['tb'] = '';
                $data['jumbel'] = 0;
                $data['jumtup'] = 0;
                $data['jumtot'] = 0;
                foreach($kol as $val){
                    $tot = ($val->totkun * $potongan->potongan) + ($val->totup * $potongan->potongan);
                    $e = number_format($tot,0,',','.');
                    $data['tb'] .= '<tr><td>'.$val->name.'</td><td>'.$val->totkun.'</td><td>'.$val->totup.'</td>
                            <td>Rp.'.$e.'</td></tr>';
                            
                    $data['jumbel'] += $val->totkun;
                    $data['jumtup'] += $val->totup;
                    $data['jumtot'] += $tot;
                }
            }else{
                $data['bel'] = '';
                $data['jumbel'] = 0;
                $data['jumtup'] = 0;
                $data['jumtot'] = 0;
                foreach($kol as $val){
                    $tot = ($val->belkun * $potongan->potongan) + ($val->beltup * $potongan->potongan);
                    $e = number_format($tot,0,',','.');
                    $data['bel'] .= '<tr><td>'.$val->name.'</td><td>'.$val->belkun.'</td><td>'.$val->beltup.'</td></tr>';
                    $data['jumbel'] += $val->totkun;
                    $data['jumtup'] += $val->totup;
                    $data['jumtot'] += $tot;
                }
                // $data['jumtot'] =($data['jumbel'] + $data['jumtup']) * $potongan->potongan;
            }
            // dd ($kol);
       
        return response()->json($data);
}

    public function capaianomset(Request $request)
    {
        {
            $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
            $sampai = $request->sampaii == '' ? Carbon::now()->toDateString() : $request->sampaii;
            $dari2 = $request->dari2 == '' ? Carbon::now()->toDateString() : $request->dari2;
            $sampai2 = $request->sampai2 == '' ? Carbon::now()->toDateString() : $request->sampai2;
            $field = $request->field;
            $cit = $request->kotas;
    
            // dd($field);
            $kota = Auth::user()->id_kantor;
            $sum = UserKolek::get();
        
            $k = Kantor::where('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->first();
    
            if (request()->ajax()) {
                // return($request);
                
                $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
                $sampai = $request->sampaii == '' ? $dari : $request->sampaii;
                $dari2 = $request->dari2 == '' ? Carbon::now()->toDateString() : $request->dari2;
                $sampai2 = $request->sampai2 == '' ? $dari2 : $request->sampai2;
                $bln = $request->bln == '' ? date('m-Y') : $request->bln;
                $bln2 = $request->bln2 == '' ? date('m-Y') : $request->bln2;
                $bayarin = $request->bayar;
    
                $field = $request->field;
                $cit = $request->kotas;
                $kot = $request->kotas == "" ? "transaksi.id_kantor != 'hahaha'" : "transaksi.id_kantor = '$request->kotas'";
                $kot2 = $request->kotas == "" ? "transaksi.id_kantor != 'hahaha'" : "transaksi.id_kantor = '$request->kotas'";
                $kot3 = $request->kotas == "" ? "transaksi.id_kantor IS NOT NULL" : "transaksi.id_kantor = '$request->kotas'";
                $bayar = $request->bayar;
                $approve = $request->approve == '' ? "transaksi.approval IS NOT NULL" : "transaksi.approval = '$request->approve'";
                
                $rkot = $request->kotas;
                
                // $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
                // $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
                
                // $bulan1 = Carbon::createFromFormat('m-Y', $bln2)->format('m');
                // $tahun1 = Carbon::createFromFormat('m-Y', $bln2)->format('Y');
                
                $bulan = date('m', strtotime('01-'.$bln));
                $bulan1 = date('m', strtotime('01-'.$bln2));
                
                $tahun = date('Y', strtotime('01-'.$bln));
                $tahun1 = date('Y', strtotime('01-'.$bln2));
                
                // return([$coba1, $coba2]);
                
                $kunit = $k != null ? $k->id : 'asdfghqwerty';
                $kota = Auth::user()->id_kantor;
                $lev = Auth::user()->kolekting;
                $sum = UserKolek::get();
                
                $bulannew = date('m', strtotime($dari));
                $tahunnew =date('Y', strtotime($dari));
                
                $tahunn = $request->thn == '' ? date('Y') : $request->thn ;
                $tahunn2 = $request->thn1 == '' ? date('Y') : $request->thn1;
                
    
                $dat = [];
                
                // $search1 = $request->input('search')['value'];

                // function contohFungsi($search) {
                //     return $search;
                // }
                
                 // Output: "ssssss"

                    // dd($search1); 
                if ($request->tab == 'tab1') {
                    if (Auth::user()->kolekting =='kacab' | Auth::user()->kolekting =='spv') {
                        if ($request->plhtgl == 0) {
                            $data = Transaksi::join('users', 'transaksi.id_koleks', '=', 'users.id')
                            ->leftjoin('targets',function($join) use ($bulannew, $tahunnew) {
                                        $join->on('users.id_karyawan' ,'=', 'targets.id_jenis')
                                            ->where('jenis_target','kar')
                                            ->where('periode','bulan')
                                            ->whereMonth('targets.tanggal', $bulannew)
                                            ->whereYear('targets.tanggal', $tahunnew);
                                    })
                            ->where('transaksi.via_input', 'transaksi')
                                ->select(\DB::raw("transaksi.kolektor as kolektor, transaksi.id_koleks as id, users.id_jabatan, targets.target as target_dana,
                                        SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                        SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                        
                                        COUNT(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND jumlah > 0 , jumlah, NULL)) AS tdm,
                                        COUNT(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' AND jumlah > 0 , jumlah, NULL)) AS tdm2,
                                        
                                        ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , jumlah, 0)) * 100) as growth
                                    "))
    
                                ->where(function ($query) use ($kunit, $kota, $rkot, $lev) {
                                    if ($lev == 'kacab') {
                                        if ($rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    } else {
                                        $query->where('transaksi.id_kantor', $kota);
                                    }
                                })
                                
                                ->whereRaw("$approve")
                                ->where(function($query) use ($request, $bayarin) {
                                    if(isset($request->bayar)){
                                        $query->whereIn('pembayaran', $bayarin);
                                    }
                                })
                                
                                ->where(function ($query) use ($dari, $sampai, $dari2, $sampai2, $request) {
                                    if ($request->vs == 'no') {
                                        $query->whereDate('transaksi.tanggal', '>=', $dari)->whereDate('transaksi.tanggal', '<=', $sampai);
                                    } else {
                                        $query->whereDate('transaksi.tanggal', '>=', $dari)->whereDate('transaksi.tanggal', '<=', $sampai)
                                            ->orWhereDate('transaksi.tanggal', '>=', $dari2)->whereDate('transaksi.tanggal', '<=', $sampai2);
                                    }
                                })
                                
                                ->where('users.id_com', Auth::user()->id_com)
                                ->groupBy('transaksi.kolektor','transaksi.id_koleks','users.id_jabatan')->orderBy('transaksi.kolektor', 'ASC');
                        } else if ($request->plhtgl == 1) {
                            
                            $data = Transaksi::join('users', 'transaksi.id_koleks', '=', 'users.id')
                                ->leftjoin('targets',function($join) use ($bulannew, $tahunnew) {
                                        $join->on('users.id_karyawan' ,'=', 'targets.id_jenis')
                                            ->where('jenis_target','kar')
                                            ->where('periode','bulan')
                                            ->whereMonth('targets.tanggal', $bulannew)
                                            ->whereYear('targets.tanggal', $tahunnew);
                                    })
                                ->where('transaksi.via_input', 'transaksi')
                                ->select(\DB::raw("transaksi.kolektor as kolektor, transaksi.id_koleks as id, users.id_jabatan, targets.target as target_dana,
                                        SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                        SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                        
                                        COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND jumlah > 0 , jumlah, NULL)) AS tdm,
                                        COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' AND jumlah > 0 , jumlah, NULL)) AS tdm2,
                                        
                                        ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , jumlah, 0)) * 100) as growth
                                    "))
    
                                ->where(function ($query) use ($kunit, $kota, $rkot, $lev) {
                                    if ($lev == 'kacab') {
                                        if ($rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    } else {
                                        $query->where('transaksi.id_kantor', $kota);
                                    }
                                })
                                
                                ->whereRaw("$approve")
                                ->where(function($query) use ($request, $bayarin) {
                                    if(isset($request->bayar)){
                                        $query->whereIn('pembayaran', $bayarin);
                                    }
                                })
                                
                                ->where(function ($query) use ($bulan, $tahun, $bulan1, $tahun1, $request) {
                                    if ($request->vs == 'no') {
                                        $query->whereMonth('transaksi.tanggal', $bulan)->whereYear('transaksi.tanggal', $tahun);
                                    } else {
                                        $query->whereMonth('transaksi.tanggal', $bulan)->whereYear('transaksi.tanggal', $tahun)
                                            ->orWhereMonth('transaksi.tanggal', $bulan1)->whereYear('transaksi.tanggal', $tahun1);
                                    }
                                })
                                
                                ->where('users.id_com', Auth::user()->id_com)
                                ->groupBy('transaksi.kolektor','transaksi.id_koleks','users.id_jabatan')->orderBy('transaksi.kolektor', 'ASC');
                        }else {
                            
                            $targets = DB::table('targets')->selectRaw("users.id, id_jenis, SUM(targets.target) as tt")->leftjoin('users', 'users.id_karyawan','=','targets.id_jenis')
                                        ->where('jenis_target','kar')
                                        ->whereYear('targets.tanggal', $tahunn)
                                        ->groupBy('id_jenis');
                            
                            $data = Transaksi::join('users', 'transaksi.id_koleks', '=', 'users.id')
                                   ->leftjoin('targets',function($join) use ($bulannew, $tahunnew) {
                                        $join->on('users.id_karyawan' ,'=', 'targets.id_jenis')
                                            ->where('jenis_target','kar')
                                            ->where('periode','bulan')
                                            ->whereMonth('targets.tanggal', $bulannew)
                                            ->whereYear('targets.tanggal', $tahunnew);
                                    })
                                    // ->leftJoin(DB::raw('(' . $targets->toSql() . ') as targets_subquery'), function ($join) {
                                    //     $join->on('users.id_karyawan', '=', DB::raw('targets_subquery.id_jenis'));
                                    // })
                                    // ->mergeBindings($targets)
                                    ->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("transaksi.kolektor as kolektor, transaksi.id_koleks as id, users.id_jabatan, targets.target as target_dana, 
                                        SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                        SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                        
                                        COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn' AND jumlah > 0 , jumlah, NULL)) AS tdm,
                                        COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn2' AND jumlah > 0 , jumlah, NULL)) AS tdm2,
                                        
                                        ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , jumlah, 0)) * 100) as growth
                                    "))
                                        
                                    ->where(function ($query) use ($kunit, $kota, $rkot, $lev) {
                                        if ($lev == 'kacab') {
                                            if ($rkot == "") {
                                                $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                            } else {
                                                $query->where('transaksi.id_kantor', $rkot);
                                            }
                                        } else {
                                            $query->where('transaksi.id_kantor', $kota);
                                        }
                                    })
                                
                                    ->whereRaw("$approve")
                                    
                                    ->where(function($query) use ($request, $bayarin) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('pembayaran', $bayarin);
                                        }
                                    })
                                    
                                    ->where(function ($query) use ($tahunn2, $tahunn, $request) {
                                        if ($request->vs == 'no') {
                                            $query->whereRaw("YEAR(transaksi.tanggal) = '$tahunn'");
                                        } else {
                                            $query->whereRaw("YEAR(transaksi.tanggal) = '$tahunn' OR YEAR(transaksi.tanggal) = '$tahunn2'");
                                        }
                                    })
                                        
                                    ->where('users.id_com', Auth::user()->id_com)
                                    ->groupBy('transaksi.kolektor','transaksi.id_koleks','users.id_jabatan')->orderBy('transaksi.kolektor', 'ASC');
                        }
                        
                    } elseif (Auth::user()->kolekting =='admin') {
                        if ($request->plhtgl == 0) {
                            
                            $data = Transaksi::join('users','users.id','=','transaksi.id_koleks')
                                    ->leftjoin('targets',function($join) use ($bulannew, $tahunnew) {
                                            $join->on('users.id_karyawan' ,'=', 'targets.id_jenis')
                                                ->where('jenis_target','kar')
                                                ->whereMonth('targets.tanggal', $bulannew)
                                                ->whereYear('targets.tanggal', $tahunnew);
                                        })
                                    // ->leftjoin('jabatan', 'jabatan.id','=','transaksi.id_jabatan')
                                    ->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("transaksi.kolektor as kolektor, transaksi.id_koleks as id, users.id_jabatan, targets.target as target_dana,
                                        SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                        SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                        
                                        COUNT(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND jumlah > 0 , jumlah, NULL)) AS tdm,
                                        COUNT(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' AND jumlah > 0 , jumlah, NULL)) AS tdm2,
                                        
                                        ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , jumlah, 0)) * 100) as growth
                                        "))
                                    // ->where(function($q) use ($cari){
                                    //     if(isset($cari)){
                                    //         $q->where('transaksi.kolektor', 'LIKE', "%$cari%")
                                    //             // ->where('jabatan.jabatan', 'LIKE', "%$cari%")
                                    //             ->where('target_dana', 'LIKE', "%$cari%")
                                    //             ->where('Omset', 'LIKE', "%$cari%")
                                    //             ->where('Omset2', 'LIKE', "%$cari%");
                                    //     }
                                    // })
                                    
                                    ->whereRaw("$kot AND $approve")
                                    ->where(function($query) use ($request, $bayarin) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('pembayaran', $bayarin);
                                        }
                                    })
                                    
                                    ->where(function ($query) use ($dari, $sampai, $dari2, $sampai2, $request) {
                                        if ($request->vs == 'no') {
                                            $query->whereDate('transaksi.tanggal', '>=', $dari)->whereDate('transaksi.tanggal', '<=', $sampai);
                                        } else {
                                            $query->whereDate('transaksi.tanggal', '>=', $dari)->whereDate('transaksi.tanggal', '<=', $sampai)
                                                ->orWhereDate('transaksi.tanggal', '>=', $dari2)->whereDate('transaksi.tanggal', '<=', $sampai2);
                                        }
                                    })
                                        
                                    ->where('users.id_com', Auth::user()->id_com)
                                    ->groupBy('transaksi.kolektor','transaksi.id_koleks','users.id_jabatan')->orderBy('transaksi.kolektor', 'ASC');
                                
                        } else if($request->plhtgl == 1){
                            
                            $data = Transaksi::join('users', 'transaksi.id_koleks', '=', 'users.id')
                                    ->leftjoin('targets',function($join) use ($bulannew, $tahunnew) {
                                            $join->on('users.id_karyawan' ,'=', 'targets.id_jenis')
                                                ->where('jenis_target','kar')
                                                ->whereMonth('targets.tanggal', $bulannew)
                                                ->whereYear('targets.tanggal', $tahunnew);
                                        })
                                    ->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("transaksi.kolektor as kolektor, transaksi.id_koleks as id, users.id_jabatan, targets.target as target_dana,
                                        SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                        SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                        
                                        COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND jumlah > 0 , jumlah, NULL)) AS tdm,
                                        COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' AND jumlah > 0 , jumlah, NULL)) AS tdm2,
                                        
                                        ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , jumlah, 0)) * 100) as growth
                                        "))
                                        
                                    ->whereRaw("$kot AND $approve")
                                    
                                    ->where(function($query) use ($request, $bayarin) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('pembayaran', $bayarin);
                                        }
                                    })
                                    
                                    ->where(function ($query) use ($bulan, $tahun, $bulan1, $tahun1, $request) {
                                        if ($request->vs == 'no') {
                                            $query->whereMonth('transaksi.tanggal', $bulan)->whereYear('transaksi.tanggal', $tahun);
                                        } else {
                                            $query->whereMonth('transaksi.tanggal', $bulan)->whereYear('transaksi.tanggal', $tahun)
                                                ->orWhereMonth('transaksi.tanggal', $bulan1)->whereYear('transaksi.tanggal', $tahun1);
                                        }
                                    })
                                        
                                    ->where('users.id_com', Auth::user()->id_com)
                                    ->groupBy('transaksi.kolektor','transaksi.id_koleks','users.id_jabatan')->orderBy('transaksi.kolektor', 'ASC');
                            
                        }else{
                            
                            $targets = DB::table('targets')->selectRaw("users.id, id_jenis, SUM(targets.target) as tt")->leftjoin('users', 'users.id_karyawan','=','targets.id_jenis')
                                        ->where('jenis_target','kar')
                                        ->whereYear('targets.tanggal', $tahunn)
                                        ->groupBy('id_jenis');
                            
                            $data = Transaksi::join('users', 'transaksi.id_koleks', '=', 'users.id')
                                    ->leftjoin('targets',function($join) use ($bulannew, $tahunnew) {
                                        $join->on('users.id_karyawan' ,'=', 'targets.id_jenis')
                                            ->where('jenis_target','kar')
                                            ->where('periode','bulan')
                                            ->whereMonth('targets.tanggal', $bulannew)
                                            ->whereYear('targets.tanggal', $tahunnew);
                                    })
                                    // ->leftJoin(DB::raw('(' . $targets->toSql() . ') as targets_subquery'), function ($join) {
                                    //     $join->on('users.id_karyawan', '=', DB::raw('targets_subquery.id_jenis'));
                                    // })
                                    // ->mergeBindings($targets)
                                    ->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("transaksi.kolektor as kolektor, transaksi.id_koleks as id, users.id_jabatan,targets.target as target_dana, 
                                        SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                        SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                        
                                        COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn' AND jumlah > 0 , jumlah, NULL)) AS tdm,
                                        COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn2' AND jumlah > 0 , jumlah, NULL)) AS tdm2,
                                        
                                        ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , jumlah, 0)) * 100) as growth
                                        "))
                                        
                                    ->whereRaw("$kot AND $approve")
                                    
                                    ->where(function($query) use ($request, $bayarin) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('pembayaran', $bayarin);
                                        }
                                    })
                                    
                                    ->where(function ($query) use ($tahunn2, $tahunn, $request) {
                                        if ($request->vs == 'no') {
                                            $query->whereRaw("YEAR(transaksi.tanggal) = '$tahunn'");
                                        } else {
                                            $query->whereRaw("YEAR(transaksi.tanggal) = '$tahunn' OR YEAR(transaksi.tanggal) = '$tahunn2'");
                                        }
                                    })
                                        
                                    ->where('users.id_com', Auth::user()->id_com)
                                    ->groupBy('transaksi.kolektor','transaksi.id_koleks','users.id_jabatan')->orderBy('transaksi.kolektor', 'ASC');
                        }
                    }
    
                    return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('omset', function ($data) {
    
                            $cuk = number_format($data->Omset, 0, ',', '.');
                            $dana = '<a data-bs-toggle="modal" class="dal" id="' . $data->id . '" data-bs-target="#modaldonasi" href="#" style="color:#1f5daa">Rp. ' . $cuk . '</a>';
    
                            return $dana;
                        })
    
                        ->addColumn('jabatan', function ($data) {
                            if ($data->id_jabatan > 0) {
                                $om = Jabatan::where('id', $data->id_jabatan)->where('id_com', Auth::user()->id_com)->first()->jabatan;
                            } else {
                                $om = '';
                            }
                            return $om;
                        })
    
                        ->addColumn('omset2', function ($data) {
                            $om = 'Rp. ' . number_format($data->Omset2, 0, ',', '.');
                            return $om;
                        })
                        
                        ->addColumn('target_dana', function ($data) {
                            if($data->target_dana == null){
                                $om = 'Rp. 0';
                            }else{
                                $om = 'Rp. ' . number_format($data->target_dana, 0, ',', '.');
                            }
                            return $om;
                        })
                        
                        ->addColumn('growth', function ($data) {
                            if ($data->growth < 0) {
                                $cot = '<span class="badge badge-danger">' . round($data->growth, 2) . ' %</span>';
                            } elseif ($data->growth > 0) {
                                $cot = '<span class="badge badge-success">' . round($data->growth, 2) . ' %</span>';
                            } else {
                                $cot = '<span class="badge badge-info">' . round($data->growth, 2) . ' %</span>';
                            }
                            return $cot;
                        })
                        
                        ->addColumn('Tdm', function ($data) {
                            $om = $data->tdm . ' Transaksi';
                            return $om;
                        })
                        ->addColumn('Tdm2', function ($data) {
                            $om = $data->tdm2 . ' Transaksi';
                            return $om;
                        })
    
                        ->addColumn('tot', function ($data) {
                            // $om = $data->tot . ' x';
                            $om = '';
                            return $om;
                        })
                        
                        ->addColumn('targets', function ($data) {
                            if($data->target_dana != NULL || $data->target_dana != 0){
                                $om = '<span class="badge badge-primary" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="top" title="'.number_format($data->Omset, 0, ',', '.').' / '.number_format($data->target_dana, 0, ',', '.').'">' .round($data->Omset * 100 / $data->target_dana ) . ' %</span>';
                            }else{
                                $om =  '<span class="badge badge-primary">0 %</span>';
                            }
                            
                            return $om;
                        })
                        
                        ->rawColumns(['omset', 'growth','targets', 'target_dana'])
                        ->make(true);
                }
                
                if ($request->tab == 'tab2') {
                    if ($request->approve == 1) {
                        $ppp = 'App\Models\Transaksi_Perhari';
                        $oi = "transaksi_perhari.id_kantor";
                    } else if ($request->approve == 2) {
                        $ppp = 'App\Models\Transaksi_Perhari_Pending';
                        $oi = "transaksi_perhari2.id_kantor";
                    } else if ($request->approve == 0) {
                        $ppp = 'App\Models\Transaksi_Perhari_All';
                        $oi = "transaksi_perhari_all.id_kantor";
                    }else if ($request->approve == 3){
                        $ppp = 'App\Models\Transaksi_Perhari_Reject';
                        $oi = "transaksi_perhari0.id_kantor";
                    }
    
                    $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
                    $sampai = $request->sampaii == '' ? $dari : $request->sampaii;
                    $dari2 = $request->dari2 == '' ? Carbon::now()->toDateString() : $request->dari2;
                    $sampai2 = $request->sampai2 == '' ? $dari2 : $request->sampai2;
                    
                    // $bulan1 = Carbon::createFromFormat('m-Y', $bln2)->format('m');
                    // $tahun1 = Carbon::createFromFormat('m-Y', $bln2)->format('Y');
    
                    $field = $request->field;
                    $cit = $request->kotas;
                    $kot = $request->kotas == "" ? "id_kantor != ''" : "id_kantor = '$request->kotas'";
    
                    $month = date("n", strtotime($dari));
                    $datee = date("d", strtotime($dari));
                    $year = date("Y", strtotime($dari));
    
                    $darwal = date("Y-m-01", strtotime($dari));
                    $darkhir = date("Y-m-t", strtotime($sampai));
    
                    // $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
                    // $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
                    // $bulan1 = Carbon::createFromFormat('m-Y', $bln2)->format('m');
                    // $tahun1 = Carbon::createFromFormat('m-Y', $bln2)->format('Y');
    
                    $kota = Auth::user()->id_kantor;
                    $sum = UserKolek::get();
                    // dd($request->kota);
                    if (Auth::user()->kolekting =='kacab' | Auth::user()->kolekting =='spv') {
                        if ($field == 'kota') {
                            if ($request->plhtgl == 0) {
                                $data = Kantor::join('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("tambahan.unit,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    // ->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                                    ->where(function ($query) use ($kunit, $kota, $rkot) {
                                        if ($rkot == "") {
                                            $query->where('tambahan.id', $kota)->orWhere('tambahan.id', $kunit);
                                        } else {
                                            $query->where('tambahan.id', $rkot);
                                        }
                                    })
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC')->get();
                            } else {
                                $data = Kantor::join('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("tambahan.unit,
                                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , transaksi.jumlah, 0)) AS Omset2,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    // ->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                                    ->where(function ($query) use ($kunit, $kota, $rkot) {
                                        if ($rkot == "") {
                                            $query->where('tambahan.id', $kota)->orWhere('tambahan.id', $kunit);
                                        } else {
                                            $query->where('tambahan.id', $rkot);
                                        }
                                    })
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC')->get();
                            }
    
                            return DataTables::of($data)
                                ->addIndexColumn()
                                ->addColumn('total', function ($data) {
                                    $cuk1 = $data->donasi + $data->t_donasi + $data->tutup + $data->tutup_x + $data->ditarik + $data->k_hilang;
                                    // $honor = '$cuk1;
                                    return $cuk1;
                                })
                                ->rawColumns(['name'])
                                ->make(true);
                        } else {
    
                            if ($request->plhtgl == 0) {
                                $data = \App::make($ppp)->select(\DB::raw("name, id,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) AS Omset,
                        SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0)) AS Omset2,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tdm, 0)) AS tdm, 
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , honor, 0)) AS honor,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , bonus_cap, 0)) AS totcap,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , donasi, 0)) AS donasi,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , t_donasi, 0)) AS t_donasi,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup, 0)) AS tutup,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup_x, 0)) AS tutup_x,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , ditarik, 0)) AS ditarik,
                        SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , k_hilang, 0)) AS k_hilang,
                        COUNT(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                        COUNT(IF(MONTH(tanggal) = '$month' AND YEAR(transaksi.tanggal) = '$year' AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                        COUNT(IF(MONTH(tanggal) = '$month' AND YEAR(transaksi.tanggal) = '$year' AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                                    ->where(function ($query) use ($kunit, $kota, $rkot, $lev) {
                                        if ($lev == 'kacab') {
                                            if ($rkot == "") {
                                                $query->where('id_kantor', $kota)->orWhere('id_kantor', $kunit);
                                            } else {
                                                $query->where('id_kantor', $rkot);
                                            }
                                        } else {
                                            $query->where('id_kantor', $kota);
                                        }
                                    })
                                    ->whereDate('tanggal', '>=', $darwal)->whereDate('tanggal', '<=', $darkhir)
                                    ->groupBy('name', 'id')->get();
                            } else {
                                $data = \App::make($ppp)->select(\DB::raw("name, id,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , jumlah, 0)) AS Omset,
                        SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(transaksi.tanggal) = '$tahun1' , jumlah, 0)) AS Omset2,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun', tdm, 0)) AS tdm, 
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , honor, 0)) AS honor,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , bonus_cap, 0)) AS totcap,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , donasi, 0)) AS donasi,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , t_donasi, 0)) AS t_donasi,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , tutup, 0)) AS tutup,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , tutup_x, 0)) AS tutup_x,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , ditarik, 0)) AS ditarik,
                        SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , k_hilang, 0)) AS k_hilang,
                        COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                        COUNT(IF(MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                        COUNT(IF(MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                                    ->where(function ($query) use ($kunit, $kota, $rkot, $lev) {
                                        if ($lev == 'kacab') {
                                            if ($rkot == "") {
                                                $query->where('id_kantor', $kota)->orWhere('id_kantor', $kunit);
                                            } else {
                                                $query->where('id_kantor', $rkot);
                                            }
                                        } else {
                                            $query->where('id_kantor', $kota);
                                        }
                                    })
                                    ->whereMonth('transaksi.tanggal', $bulan)->whereYear('transaksi.tanggal', $tahun)
                                    ->groupBy('name', 'id')->get();
                            }
                            return DataTables::of($data)
                                ->addIndexColumn()
                                ->addColumn('name', function ($data) {
                                    if ($data->conwar >= 3) {
                                        if (date('H') >= 16) {
                                            $ju = $data->conwar;
                                        } else {
                                            $ju = $data->conwarnow;
                                        }
                                        $btn = '<a data-bs-toggle="modal" class="dalwar" id="' . $data->id . '" data-bs-target="#modalwar" href="#" style="color:#FFF">
                                           ' . $data->name . ' | ' . $ju . 'x
                                        </a>';
                                    } else {
                                        $btn = '<a data-bs-toggle="modal"  class="dalwar" id="' . $data->id . '" data-bs-target="#modalwar" href="#" style="color:#1f5daa">' . $data->name . '</a>';
                                    }
                                    return $btn;
                                })
                                ->addColumn('total', function ($data) {
                                    $cuk1 = $data->donasi + $data->t_donasi + $data->tutup + $data->tutup_x + $data->ditarik + $data->k_hilang;
                                    // $honor = '$cuk1;
                                    return $cuk1;
                                })
                                ->rawColumns(['name'])
                                ->make(true);
                        }
                    } elseif (Auth::user()->kolekting =='admin') {
                        if ($field == 'kota') {
                            if ($request->plhtgl == 0) {
                                $data = Kantor::join('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("tambahan.unit,
                                SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    // ->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC')->get();
                            } else {
                                $data = Kantor::join('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("tambahan.unit,
                                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , transaksi.jumlah, 0)) AS Omset2,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    // ->whereDate('tanggal', '>=', $dari)->whereDate('tanggal', '<=', $sampai)
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC')->get();
                            }
    
                            return DataTables::of($data)
                                ->addIndexColumn()
                                ->addColumn('total', function ($data) {
                                    $cuk1 = $data->donasi + $data->t_donasi + $data->tutup + $data->tutup_x + $data->ditarik + $data->k_hilang;
                                    // $honor = '$cuk1;
                                    return $cuk1;
                                })
                                ->rawColumns(['name'])
                                ->make(true);
                        } else {
                            if ($request->plhtgl == 0) {
                                $data = \App::make($ppp)->select(\DB::raw("name,id,
                                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , jumlah, 0)) AS Omset,
                                    SUM(IF( DATE(tanggal) >= '$dari2' AND DATE(tanggal) <= '$sampai2' , jumlah, 0)) AS Omset2,
                                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tdm, 0)) AS tdm, 
                                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , honor, 0)) AS honor,
                                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , bonus_cap, 0)) AS totcap,
                                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , donasi, 0)) AS donasi,
                                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , t_donasi, 0)) AS t_donasi,
                                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup, 0)) AS tutup,
                                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , tutup_x, 0)) AS tutup_x,
                                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , ditarik, 0)) AS ditarik,
                                    SUM(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' , k_hilang, 0)) AS k_hilang,
                                    COUNT(IF( DATE(tanggal) >= '$dari' AND DATE(tanggal) <= '$sampai' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                                    COUNT(IF(MONTH(tanggal) = '$month' AND YEAR(transaksi.tanggal) = '$year' AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                                    COUNT(IF(MONTH(tanggal) = '$month' AND YEAR(transaksi.tanggal) = '$year' AND DATE(tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                                    ->whereRaw("$kot")
                                    ->whereDate('tanggal', '>=', $darwal)->whereDate('tanggal', '<=', $darkhir)
                                    ->groupBy('name', 'id')->get();
                            } else {
                                $data = \App::make($ppp)->select(\DB::raw("name,id,
                                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , jumlah, 0)) AS Omset,
                                    SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(transaksi.tanggal) = '$tahun1' , jumlah, 0)) AS Omset2,
                                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun', tdm, 0)) AS tdm, 
                                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , honor, 0)) AS honor,
                                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , bonus_cap, 0)) AS totcap,
                                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , donasi, 0)) AS donasi,
                                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , t_donasi, 0)) AS t_donasi,
                                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , tutup, 0)) AS tutup,
                                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , tutup_x, 0)) AS tutup_x,
                                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , ditarik, 0)) AS ditarik,
                                    SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' , k_hilang, 0)) AS k_hilang,
                                    COUNT(IF( MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND bonus_cap > 0, bonus_cap, NULL)) AS tot,
                                    COUNT(IF(MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwar,
                                    COUNT(IF(MONTH(tanggal) = '$bulan' AND YEAR(transaksi.tanggal) = '$tahun' AND DATE(transaksi.tanggal) != DATE(NOW()) AND target = 'Tidak' AND kunjungan = 'Tidak' , tanggal, NULL)) AS conwarnow"))
                                    ->whereRaw("$kot")
                                    ->whereMonth('transaksi.tanggal', $bulan)->whereYear('transaksi.tanggal', $tahun)
                                    ->groupBy('name', 'id')->get();
                            }
    
                            return DataTables::of($data)
                                ->addIndexColumn()
                                ->addColumn('name', function ($data) {
                                    if ($data->conwar >= 3) {
                                        if (date('H') >= 16) {
                                            $ju = $data->conwar;
                                        } else {
                                            $ju = $data->conwarnow;
                                        }
                                        $btn = '<a data-bs-toggle="modal" class="dalwar" id="' . $data->id . '" data-bs-target="#modalwar" href="#" style="color:#FFF">
                                           ' . $data->name . ' | ' . $ju . 'x
                                        </a>';
                                    } else {
                                        $btn = '<a data-bs-toggle="modal"  class="dalwar" id="' . $data->id . '" data-bs-target="#modalwar" href="#" style="color:#1f5daa">' . $data->name . '</a>';
                                    }
                                    return $btn;
                                })
                                ->addColumn('total', function ($data) {
                                    $cuk1 = $data->donasi + $data->t_donasi + $data->tutup + $data->tutup_x + $data->ditarik + $data->k_hilang;
                                    // $honor = '$cuk1;
                                    return $cuk1;
                                })
                                ->rawColumns(['name'])
                                ->make(true);
                        }
                    }
                }
                
                if ($request->tab == 'tab3') {
                    
                    if (Auth::user()->kolekting =='admin') {
                        if ($field == 'program') {
                            if ($request->plhtgl == 0) {
                                $data = Prog::join('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth"))
                                    ->whereRaw("$kot2 AND $approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->where('transaksi.via_input', 'transaksi')
                                    ->groupBy('prog.program');
                            } else if($request->plhtgl == 1) {
                                $data = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program, transaksi.id_program as id,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100 ) as growth"))
    
                                    ->whereRaw("$kot2 AND $approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            }else{
                                $data = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) * 100 ) as growth"))
    
                                    ->whereRaw("$kot2 AND $approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            }
                        } elseif ($field == 'kota') {
                            if ($request->plhtgl == 0) {
                                
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                ->leftjoin('targets',function($join) use ($bulan, $tahun) {
                                    $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                        ->whereMonth('targets.tanggal', $bulan)
                                        ->whereYear('targets.tanggal', $tahun)
                                        ->where('periode', 'bulan')
                                        ->where('jenis_target', 'kan');
                                })
                                ->select(\DB::raw("tambahan.unit, targets.target, transaksi.id_kantor as id,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' AND transaksi.jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                ->where('tambahan.id_com', Auth::user()->id_com)
                                ->whereRaw("$approve AND $kot3")
                                ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                                
                            } else if(($request->plhtgl == 1)) {
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bln) {
                                            $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                // ->whereMonth('targets.tanggal', $bulan)
                                                // ->whereYear('targets.tanggal', $tahun);
                                                ->whereRaw("DATE_FORMAT(targets.tanggal, '%m-%Y') = '$bln'")
                                                ->where('periode', 'bulan')
                                                ->where('jenis_target', 'kan');
                                        })
                                    ->select(\DB::raw("tambahan.unit, targets.target,transaksi.id_kantor as id,
                                SUM(IF(DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF(DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                ->where('tambahan.id_com', Auth::user()->id_com)
                                ->whereRaw("$approve AND $kot3")
                                ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }else{
                                $targets = DB::table('targets')->selectRaw("id_jenis, SUM(targets.target) as tt")->leftjoin('tambahan', 'tambahan.id','=','targets.id_jenis')
                                        ->where('jenis_target','kan')
                                        ->whereYear('targets.tanggal', $tahunn)
                                        ->groupBy('id_jenis');
                                
                                // return($targets->get());
                                
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')
                                    ->leftJoin(DB::raw('(' . $targets->toSql() . ') as targets_subquery'), function ($join) {
                                        $join->on('tambahan.id', '=', DB::raw('targets_subquery.id_jenis'));
                                    })
                                    ->mergeBindings($targets)
                                    // ->leftjoin('targets',function($join) use ($tahunn) {
                                    //     $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                    //         ->whereYear('targets.tanggal', $tahunn);
                                    // })
                                    ->select(\DB::raw("tambahan.unit, tt as target,transaksi.id_kantor as id,
                                        SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                        SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                        
                                        COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                        COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                        
                                        ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) * 100) as growth,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                ->where('transaksi.via_input', 'transaksi')
                                ->where('tambahan.id_com', Auth::user()->id_com)
                                ->whereRaw("$approve AND $kot3")
                                ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }
                        }
                    } elseif (Auth::user()->kolekting == 'kacab' || Auth::user()->kolekting =='spv') {
                        if ($field == 'program') {
                            if ($request->plhtgl == 0) {
                                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth"))
                                    ->whereRaw("$approve")
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            } else if($request->plhtgl == 1) {
                                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100 ) as growth"))
                                    ->whereRaw("$approve")
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            }else{
                                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) * 100 ) as growth"))
                                    ->whereRaw("$approve")
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            }
                        } elseif ($field == 'kota') {
                            if ($request->plhtgl == 0) {
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bulan, $tahun) {
                                            $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                ->whereMonth('targets.tanggal', $bulan)
                                                ->whereYear('targets.tanggal', $tahun);
                                        })
                                    ->select(\DB::raw("tambahan.unit, id_transaksi, targets.target, transaksi.id_kantor as id,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                             $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->whereRaw("$approve AND $kot3")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            } else if($request->plhtgl == 1){
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bln) {
                                            $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                    // ->whereMonth('targets.tanggal', $bulan)
                                                    // ->whereYear('targets.tanggal', $tahun);
                                                    ->whereRaw("DATE_FORMAT(targets.tanggal, '%m-%Y') = '$bln'")
                                                    ->where('periode', 'bulan')
                                                    ->where('jenis_target', 'kan');
                                            // $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                    
                                            //         ->whereRaw("DATE_FORMAT(targets.tanggal, '%m-%Y') = '$bln' AND jenis_target = 'kan'");
                                        })
                                    ->select(\DB::raw("tambahan.unit, id_transaksi, targets.target, transaksi.id_kantor as id,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                             $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->whereRaw("$approve AND $kot3")
                                    
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }else{
                                $targets = DB::table('targets')->selectRaw("id_jenis, SUM(targets.target) as tt")->leftjoin('tambahan', 'tambahan.id','=','targets.id_jenis')
                                        ->where('jenis_target','kan')
                                        ->whereYear('targets.tanggal', $tahunn)
                                        ->groupBy('id_jenis');
                                
                                // return($targets->get());
                                
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')
                                    ->leftJoin(DB::raw('(' . $targets->toSql() . ') as targets_subquery'), function ($join) {
                                        $join->on('tambahan.id', '=', DB::raw('targets_subquery.id_jenis'));
                                    })
                                    ->mergeBindings($targets)
                                    ->select(\DB::raw("tambahan.unit, id_transaksi, tt as target, transaksi.id_kantor as id,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->whereRaw("$approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }
                        }
                    }
                    
                    $euy = $data->get();
                    
                    if($request->toggle == "true"){
                        $aha = [];
                        foreach($euy as $e){
                            if($request->vs == 'no'){
                                if($e->Omset > 0){
                                    $aha[] = $e;
                                }
                            }else{
                                if( $e->Omset2 != 0 || $e->Omset != 0){
                                    $aha[] = $e;
                                }
                            }
                        }
                    }else{
                        $aha = $data->get();
                    }
                    
                    return DataTables::of($aha)
                        ->addIndexColumn()
                        ->addColumn('oomset', function ($aha) {
                            $cuk = number_format($aha->Omset, 0, ',', '.');
                            $dana = '<a data-bs-toggle="modal" class="dal" id="' . $aha->id . '" data-bs-target="#modaldonasi" href="#" style="color:#1f5daa">Rp. ' . $cuk . '</a>';
    
                            return $dana;
                        })
                        ->addColumn('oomsets', function ($aha) {
                            $om = 'Rp. ' . number_format($aha->Omset2, 0, ',', '.');
                            return $om;
                        })
                        ->addColumn('growth', function ($aha) {
                            if ($aha->growth < 0) {
                                $cot = '<span class="badge badge-danger">' . round($aha->growth, 2) . ' %</span>';
                            } elseif ($aha->growth > 0) {
                                $cot = '<span class="badge badge-success">' . round($aha->growth, 2) . ' %</span>';
                            } else {
                                $cot = '<span class="badge badge-info">' . round($aha->growth, 2) . ' %</span>';
                            }
                            return $cot;
                        })
                        
                        ->addColumn('target_dana', function ($aha) {
                            if($aha->target == null){
                                $om = 'Rp. 0';
                            }else{
                                $om = 'Rp. ' . number_format($aha->target, 0, ',', '.');
                            }
                            return $om;
                        })
                        
                        ->addColumn('targets', function ($aha) {
                            if($aha->target != NULL || $aha->target != 0){
                                $om = '<span class="badge badge-primary" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="top" title="'.number_format($aha->Omset, 0, ',', '.').' / '.number_format($aha->target, 0, ',', '.').'">' .round($aha->Omset * 100 / $aha->target ) . ' %</span>';
                            }else{
                                $om =  '<span class="badge badge-primary">0 %</span>';
                            }
                            
                            return $om;
                        })
    
                        ->rawColumns(['growth','targets','target_dana','oomset'])

                        ->make(true);
                }
                
                if ($request->tab == 'tab4') {
                    // dd(contohFungsi($search1));
                    $bayar = $request->bayar;
                    $approve = $request->approve == '' ? "transaksi.approval IS NOT NULL" : "transaksi.approval = '$request->approve'";
                    $cari = $request->cari;
                    if (Auth::user()->kolekting =='admin') {
                        
                        if ($field == 'program') {
                            if ($request->plhtgl == 0) {
                                $data = Prog::join('transaksi', 'transaksi.id_program', '=', 'prog.id_program')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth"))
                                    ->whereRaw("$kot2 AND $approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->where('transaksi.via_input', 'transaksi')
                                    ->groupBy('prog.program');
                            } else if($request->plhtgl == 1) {
                                $data = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program, transaksi.id_program as id,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100 ) as growth"))
    
                                    ->whereRaw("$kot2 AND $approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            }else{
                                $data = Prog::leftjoin('transaksi', 'transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) * 100 ) as growth"))
    
                                    ->whereRaw("$kot2 AND $approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            }
                        } elseif ($field == 'kota') {
                            if ($request->plhtgl == 0) {
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bulan, $tahun) {
                                        $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                            ->whereMonth('targets.tanggal', $bulan)
                                            ->whereYear('targets.tanggal', $tahun);
                                    })
                                    ->select(\DB::raw("tambahan.unit, targets.target, transaksi.id_kantor as id,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' AND transaksi.jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                ->where('tambahan.id_com', Auth::user()->id_com)
                                ->whereRaw("$approve")
                                ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            } else if(($request->plhtgl == 1)) {
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bln) {
                                            $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                // ->whereMonth('targets.tanggal', $bulan)
                                                // ->whereYear('targets.tanggal', $tahun);
                                                ->whereRaw("DATE_FORMAT(targets.tanggal, '%m-%Y') = '$bln'");
                                        })
                                    ->select(\DB::raw("tambahan.unit, targets.target,transaksi.id_kantor as id,
                                SUM(IF(DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF(DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                ->where('tambahan.id_com', Auth::user()->id_com)
                                ->whereRaw("$approve")
                                ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }else{
                                $targets = DB::table('targets')->selectRaw("id_jenis, SUM(targets.target) as tt")->leftjoin('tambahan', 'tambahan.id','=','targets.id_jenis')
                                        ->where('jenis_target','kan')
                                        ->whereYear('targets.tanggal', $tahunn)
                                        ->groupBy('id_jenis');
                                
                                // return($targets->get());
                                
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')
                                    ->leftJoin(DB::raw('(' . $targets->toSql() . ') as targets_subquery'), function ($join) {
                                        $join->on('tambahan.id', '=', DB::raw('targets_subquery.id_jenis'));
                                    })
                                    ->mergeBindings($targets)
                                    // ->leftjoin('targets',function($join) use ($tahunn) {
                                    //     $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                    //         ->whereYear('targets.tanggal', $tahunn);
                                    // })
                                    ->select(\DB::raw("tambahan.unit, tt as target,transaksi.id_kantor as id,
                                        SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                        SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                        
                                        COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                        COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                        
                                        ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) * 100) as growth,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                        COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                ->where('transaksi.via_input', 'transaksi')
                                ->where('tambahan.id_com', Auth::user()->id_com)
                                ->whereRaw("$approve")
                                ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }
                        }else{
                            if ($request->plhtgl == 0) {
                                
                                $data = Transaksi::join('users', 'transaksi.id_koleks', '=', 'users.id')->where('transaksi.via_input', 'transaksi')
                                        ->select(\DB::raw("transaksi.kolektor as kolektor, transaksi.id_koleks as id, users.id_jabatan, users.target as target_dana,
                                            SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                            SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                            
                                            COUNT(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND jumlah > 0 , jumlah, NULL)) AS tdm,
                                            COUNT(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' AND jumlah > 0 , jumlah, NULL)) AS tdm2,
                                            
                                            ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , jumlah, 0)) * 100) as growth
                                            "))
                                            
                                        ->whereRaw("$kot AND $approve")
                                        ->where(function($query) use ($request, $bayar) {
                                            if(isset($request->bayar)){
                                                $query->whereIn('transaksi.pembayaran', $bayar);
                                            }
                                        })
                                        
                                        ->where(function ($query) use ($dari, $sampai, $dari2, $sampai2, $request) {
                                            if ($request->vs == 'no') {
                                                $query->whereDate('transaksi.tanggal', '>=', $dari)->whereDate('transaksi.tanggal', '<=', $sampai);
                                            } else {
                                                $query->whereDate('transaksi.tanggal', '>=', $dari)->whereDate('transaksi.tanggal', '<=', $sampai)
                                                    ->orWhereDate('transaksi.tanggal', '>=', $dari2)->whereDate('transaksi.tanggal', '<=', $sampai2);
                                            }
                                        })
                                        ->whereRaw("$approve")
                                        ->where('transaksi.kolektor', 'LIKE', "%$cari%")
                                        ->where('users.id_com', Auth::user()->id_com)
                                        ->groupBy('transaksi.kolektor','transaksi.id_koleks','users.id_jabatan')->orderBy('transaksi.kolektor', 'ASC');
                                    
                            } else if($request->plhtgl == '1') {
                                
                                $data = Transaksi::join('users', 'transaksi.id_koleks', '=', 'users.id')->where('transaksi.via_input', 'transaksi')
                                        ->select(\DB::raw("transaksi.kolektor as kolektor, transaksi.id_koleks as id, users.id_jabatan, users.target as target_dana,
                                            SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                            SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                            
                                            COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND jumlah > 0 , jumlah, NULL)) AS tdm,
                                            COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' AND jumlah > 0 , jumlah, NULL)) AS tdm2,
                                            
                                            ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , jumlah, 0)) * 100) as growth
                                            "))
                                            
                                        ->whereRaw("$kot AND $approve")
                                        ->where('transaksi.kolektor', 'LIKE', "%$cari%")
                                        ->where(function($query) use ($request, $bayar) {
                                            if(isset($request->bayar)){
                                                $query->whereIn('transaksi.pembayaran', $bayar);
                                            }
                                        })
                                        
                                        ->where(function ($query) use ($bulan, $tahun, $bulan1, $tahun1, $request) {
                                            if ($request->vs == 'no') {
                                                $query->whereMonth('transaksi.tanggal', $bulan)->whereYear('transaksi.tanggal', $tahun);
                                            } else {
                                                $query->whereMonth('transaksi.tanggal', $bulan)->whereYear('transaksi.tanggal', $tahun)
                                                    ->orwhereMonth('transaksi.tanggal', $bulan1)->whereYear('transaksi.tanggal', $tahun1);
                                            }
                                        })
                                        ->whereRaw("$approve")
                                        ->where('users.id_com', Auth::user()->id_com)
                                        ->groupBy('transaksi.kolektor','transaksi.id_koleks','users.id_jabatan')->orderBy('transaksi.kolektor', 'ASC');
                                
                            }else{
                                $targets = DB::table('targets')->selectRaw("users.id, id_jenis, SUM(targets.target) as tt")->leftjoin('users', 'users.id_karyawan','=','targets.id_jenis')
                                        ->where('jenis_target','kar')
                                        ->whereYear('targets.tanggal', $tahunn)
                                        ->groupBy('id_jenis');
                            
                                $data = Transaksi::join('users', 'transaksi.id_koleks', '=', 'users.id')
                                        ->leftJoin(DB::raw('(' . $targets->toSql() . ') as targets_subquery'), function ($join) {
                                            $join->on('users.id_karyawan', '=', DB::raw('targets_subquery.id_jenis'));
                                        })
                                        ->mergeBindings($targets)
                                        ->where('transaksi.via_input', 'transaksi')
                                        ->select(\DB::raw("transaksi.kolektor as kolektor, transaksi.id_koleks as id, users.id_jabatan, tt AS target_dana, 
                                            SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                            SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                            
                                            COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn' AND jumlah > 0 , jumlah, NULL)) AS tdm,
                                            COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn2' AND jumlah > 0 , jumlah, NULL)) AS tdm2,
                                            
                                            ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , jumlah, 0)) * 100) as growth
                                            "))
                                            
                                        ->whereRaw("$kot AND $approve")
                                        ->where('transaksi.kolektor', 'LIKE', "%$cari%")
                                        ->where(function($query) use ($request, $bayarin) {
                                            if(isset($request->bayar)){
                                                $query->whereIn('pembayaran', $bayarin);
                                            }
                                        })
                                        
                                        ->where(function ($query) use ($tahunn2, $tahunn, $request) {
                                            if ($request->vs == 'no') {
                                                $query->whereRaw("YEAR(transaksi.tanggal) = '$tahunn'");
                                            } else {
                                                $query->whereRaw("YEAR(transaksi.tanggal) = '$tahunn' OR YEAR(transaksi.tanggal) = '$tahunn2'");
                                            }
                                        })
                                            
                                        ->where('users.id_com', Auth::user()->id_com)
                                        ->groupBy('transaksi.kolektor','transaksi.id_koleks','users.id_jabatan')->orderBy('transaksi.kolektor', 'ASC');
                            }
                        }
                        
                        
                    }else if (Auth::user()->kolekting =='kacab' | Auth::user()->kolekting =='spv') {
                        
                        if ($field == 'program') {
                            if ($request->plhtgl == 0) {
                                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth"))
                                    ->whereRaw("$approve")
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            } else if($request->plhtgl == 1) {
                                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100 ) as growth"))
                                    ->whereRaw("$approve")
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            }else{
                                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,transaksi.id_program as id,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) * 100 ) as growth"))
                                    ->whereRaw("$approve")
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            }
                        } elseif ($field == 'kota') {
                            if ($request->plhtgl == 0) {
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bulan, $tahun) {
                                            $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                ->whereMonth('targets.tanggal', $bulan)
                                                ->whereYear('targets.tanggal', $tahun);
                                        })
                                    ->select(\DB::raw("tambahan.unit, id_transaksi, targets.target, transaksi.id_kantor as id,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                             $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->whereRaw("$approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            } else if($request->plhtgl == 1){
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bln) {
                                            $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                    ->whereRaw("DATE_FORMAT(targets.tanggal, '%m-%Y') = '$bln'");
                                        })
                                    ->select(\DB::raw("tambahan.unit, id_transaksi, targets.target, transaksi.id_kantor as id,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                             $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->whereRaw("$approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }else{
                                $targets = DB::table('targets')->selectRaw("id_jenis, SUM(targets.target) as tt")->leftjoin('tambahan', 'tambahan.id','=','targets.id_jenis')
                                        ->where('jenis_target','kan')
                                        ->whereYear('targets.tanggal', $tahunn)
                                        ->groupBy('id_jenis');
                                
                                // return($targets->get());
                                
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')
                                    ->leftJoin(DB::raw('(' . $targets->toSql() . ') as targets_subquery'), function ($join) {
                                        $join->on('tambahan.id', '=', DB::raw('targets_subquery.id_jenis'));
                                    })
                                    ->mergeBindings($targets)
                                    ->select(\DB::raw("tambahan.unit, id_transaksi, tt as target, transaksi.id_kantor as id,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( YEAR(transaksi.tanggal) = '$tahunn' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    ->where(function ($query) use ($k, $kunit, $kota, $rkot) {
                                        if ($rkot == null || $rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->whereRaw("$approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }
                        }else {
                            
                            $targets = DB::table('targets')->selectRaw("users.id, id_jenis, SUM(targets.target) as tt")->leftjoin('users', 'users.id_karyawan','=','targets.id_jenis')
                                        ->where('jenis_target','kar')
                                        ->whereYear('targets.tanggal', $tahunn)
                                        ->groupBy('id_jenis');
                            
                            $data = Transaksi::join('users', 'transaksi.id_koleks', '=', 'users.id')
                                    // ->leftjoin('targets',function($join) use ($tahunn) {
                                    //         $join->on('users.id_karyawan' ,'=', 'targets.id_jenis')
                                    //             ->where('jenis_target','kar')
                                    //             ->whereYear('targets.tanggal', $tahunn);
                                    // })
                                    ->leftJoin(DB::raw('(' . $targets->toSql() . ') as targets_subquery'), function ($join) {
                                        $join->on('users.id_karyawan', '=', DB::raw('targets_subquery.id_jenis'));
                                    })
                                    ->mergeBindings($targets)
                                    ->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("transaksi.kolektor as kolektor, transaksi.id_koleks as id, users.id_jabatan, tt AS target_dana, 
                                        SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , transaksi.jumlah, 0)) AS Omset,
                                        SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , transaksi.jumlah, 0)) AS Omset2,
                                        
                                        COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn' AND jumlah > 0 , jumlah, NULL)) AS tdm,
                                        COUNT(IF( YEAR(transaksi.tanggal) = '$tahunn2' AND jumlah > 0 , jumlah, NULL)) AS tdm2,
                                        
                                        ((SUM(IF( YEAR(transaksi.tanggal) = '$tahunn' , jumlah, 0)) - SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , jumlah, 0))) / SUM(IF( YEAR(transaksi.tanggal) = '$tahunn2' , jumlah, 0)) * 100) as growth
                                    "))
                                        
                                    ->where(function ($query) use ($kunit, $kota, $rkot, $lev) {
                                        if ($lev == 'kacab') {
                                            if ($rkot == "") {
                                                $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                            } else {
                                                $query->where('transaksi.id_kantor', $rkot);
                                            }
                                        } else {
                                            $query->where('transaksi.id_kantor', $kota);
                                        }
                                    })
                                
                                    ->whereRaw("$approve")
                                    
                                    ->where(function($query) use ($request, $bayarin) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('pembayaran', $bayarin);
                                        }
                                    })
                                    
                                    ->where(function ($query) use ($tahunn2, $tahunn, $request) {
                                        if ($request->vs == 'no') {
                                            $query->whereRaw("YEAR(transaksi.tanggal) = '$tahunn'");
                                        } else {
                                            $query->whereRaw("YEAR(transaksi.tanggal) = '$tahunn' OR YEAR(transaksi.tanggal) = '$tahunn2'");
                                        }
                                    })
                                        
                                    ->where('users.id_com', Auth::user()->id_com)
                                    ->groupBy('transaksi.kolektor','transaksi.id_koleks','users.id_jabatan')->orderBy('transaksi.kolektor', 'ASC');
                        }
                        
                    }
                    // return($data);
                    
                    
                    // dd($data->toSql());
                    // $data->getBindings();
                    $dah = [];
                    $om1 = 0;
                    $om2 = 0;
                    $tdm1 = 0;
                    $tdm2 = 0;
                    // dd($data->get());
                    foreach($data->get() as $dd){
                        $om1 += $dd->Omset;
                        $om2 += $dd->Omset2;
                        $tdm1 += $dd->tdm;
                        $tdm2 += $dd->tdm2;
                    };
                        
                    $dah = [
                        'om1' => $om1,
                        'om2' => $om2,
                        'tdm1' => $tdm1,
                        'tdm2' => $tdm2,
                    ];
                    // dd($om1);
                    return $dah;
                }
                
                if ($request->tab == 'tab5') {
                    
                    if (Auth::user()->kolekting =='admin') {
                        if ($field == 'program') {
                            if ($request->plhtgl == 0) {
                                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth"))
                                    ->whereRaw("$kot2")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->whereRaw("$approve")
                                    ->groupBy('prog.program');
                            } else {
                                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100 ) as growth"))
    
                                    ->whereRaw("$kot2")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->whereRaw("$approve")
                                    ->groupBy('prog.program');
                            }
                        } elseif ($field == 'kota') {
                            if ($request->plhtgl == 0) {
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bulan, $tahun) {
                                        $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                            ->whereMonth('targets.tanggal', $bulan)
                                            ->whereYear('targets.tanggal', $tahun);
                                    })
                                    ->select(\DB::raw("tambahan.unit, targets.target,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' AND transaksi.jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                ->where('tambahan.id_com', Auth::user()->id_com)
                                ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->whereRaw("$approve")
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            } else {
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bulan, $tahun) {
                                            $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                ->whereMonth('targets.tanggal', $bulan)
                                                ->whereYear('targets.tanggal', $tahun);
                                        })
                                    ->select(\DB::raw("tambahan.unit, targets.target,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                ->where('tambahan.id_com', Auth::user()->id_com)
                                ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->whereRaw("$approve")
                                ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }
                        }
                    } elseif (Auth::user()->kolekting =='kacab' | Auth::user()->kolekting =='spv') {
                        if ($field == 'program') {
                            if ($request->plhtgl == 0) {
                                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth"))
                                    ->whereRaw("$kot2 AND $bayar")
                                    ->where(function ($query) use ($kunit, $kota, $rkot) {
                                        if ($rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->whereRaw("$approve")
                                    ->groupBy('prog.program');
                            } else {
                                $data = Prog::leftjoin('transaksi','transaksi.id_program', '=', 'prog.id_program')->where('transaksi.via_input', 'transaksi')
                                    ->select(\DB::raw("prog.program,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) AS Omset2,
                                ((SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' , transaksi.jumlah, 0)) - SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0))) / SUM(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' , transaksi.jumlah, 0)) * 100 ) as growth"))
    
                                    ->whereRaw("$kot2 AND $bayar")
                                    ->where(function ($query) use ($kunit, $kota, $rkot) {
                                        if ($rkot == "") {
                                            $query->where('transaksi.id_kantor', $kota)->orWhere('transaksi.id_kantor', $kunit);
                                        } else {
                                            $query->where('transaksi.id_kantor', $rkot);
                                        }
                                    })
                                    ->whereRaw("$approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->groupBy('prog.program');
                            }
                        } elseif ($field == 'kota') {
                            if ($request->plhtgl == 0) {
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bulan, $tahun) {
                                            $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                ->whereMonth('targets.tanggal', $bulan)
                                                ->whereYear('targets.tanggal', $tahun);
                                        })
                                    ->select(\DB::raw("tambahan.unit, id_transaksi, targets.target,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' , transaksi.jumlah, 0)) - SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0))) / SUM(IF( DATE(transaksi.tanggal) >= '$dari2' AND DATE(transaksi.tanggal) <= '$sampai2' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( DATE(transaksi.tanggal) >= '$dari' AND DATE(transaksi.tanggal) <= '$sampai' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    ->where(function ($query) use ($kunit, $kota, $rkot) {
                                        if ($rkot == "") {
                                            $query->where('tambahan.id', $kota)->orWhere('tambahan.id', $kunit);
                                        } else {
                                            $query->where('tambahan.id', $rkot);
                                        }
                                    })
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->whereRaw("$approve")
                                    ->groupBy('tambahan.unit','id_transaksi')->orderBy('tambahan.unit', 'ASC')->get();
                                    // return($data);
                            } else {
                                $data = Kantor::leftjoin('transaksi', 'transaksi.id_kantor', '=', 'tambahan.id')->where('transaksi.via_input', 'transaksi')
                                    ->leftjoin('targets',function($join) use ($bulan, $tahun) {
                                            $join->on('tambahan.id' ,'=', 'targets.id_jenis')
                                                ->whereMonth('targets.tanggal', $bulan)
                                                ->whereYear('targets.tanggal', $tahun);
                                        })
                                    ->select(\DB::raw("tambahan.unit, id_transaksi, targets.target,
                                SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , transaksi.jumlah, 0)) AS Omset,
                                SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , transaksi.jumlah, 0)) AS Omset2,
                                
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln' AND jumlah > 0, transaksi.id, NULL)) AS jum1,
                                COUNT(IF( DATE_FORMAT(transaksi.tanggal, '%m-%Y') = '$bln2' AND jumlah > 0 , transaksi.id, NULL)) AS jum2,
                                
                                ((SUM(IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' , transaksi.jumlah, 0)) - SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , transaksi.jumlah, 0))) / SUM(IF( MONTH(tanggal) = '$bulan1' AND YEAR(tanggal) = '$tahun1' , transaksi.jumlah, 0)) * 100) as growth,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.jumlah >= 5000 AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS jumlah,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS donasi,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Tidak Donasi' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS t_donasi,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Tutup'  AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Tutup 2x' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS tutup_x,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Ditarik' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS ditarik,
                                COUNT(DISTINCT IF( MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND transaksi.status = 'Kotak Hilang' AND transaksi.pembayaran = 'dijemput', transaksi.id_donatur, NULL)) AS k_hilang"))
                                    ->where(function ($query) use ($kunit, $kota, $rkot) {
                                        if ($rkot == "") {
                                            $query->where('tambahan.id', $kota)->orWhere('tambahan.id', $kunit);
                                        } else {
                                            $query->where('tambahan.id', $rkot);
                                        }
                                    })
                                    ->whereRaw("$approve")
                                    ->where(function($query) use ($request, $bayar) {
                                        if(isset($request->bayar)){
                                            $query->whereIn('transaksi.pembayaran', $bayar);
                                        }
                                    })
                                    ->where('tambahan.id_com', Auth::user()->id_com)
                                    ->groupBy('tambahan.unit')->orderBy('tambahan.unit', 'ASC');
                            }
                        }
                    }
                    
                    $ewean = $data->get();
                    $send = [];
                    $om1 = 0;
                    $om2 = 0;
                    foreach($ewean as $e){
                        $om1 += $e->Omset;
                        $om2 += $e->Omset2;
                        
                    }
                    $send = [
                        'Omset1' => $om1,
                        'Omset2' => $om2,
                    ];
                    
                    return $send;
                    
                }
    
                // return $dat;
            }
            
            $y = Auth::user()->id_com;
    
            $rincian = \DB::select("SELECT DATE_FORMAT(tanggal, '%m/%Y') as bulan, DATE_FORMAT(tanggal, '%M, %Y') as namebulan from rincian_belum GROUP BY DATE_FORMAT(tanggal, '%m/%Y'), DATE_FORMAT(tanggal, '%M, %Y')");
    
            $datacabang = \DB::select("SELECT * from tambahan WHERE id_com = '$y'");
    
    
    
            $belum = \DB::select("SELECT users.name, users.kota,
             COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totkun,
             COUNT(IF(donatur.status = 'Tutup' AND donatur.acc = 1 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totup
             FROM users LEFT JOIN donatur ON donatur.petugas = users.name WHERE users.id_com = '$y' 
             GROUP BY users.name, users.kota, users.kolektor, users.aktif HAVING users.kolektor = 'kolektor' AND users.aktif = 1 ");
    
            
            $belumass = \DB::select("SELECT users.name, users.kota,
             COUNT(IF(donatur.status = 'belum dikunjungi' AND donatur.acc = 0 AND donatur.pembayaran = 'dijemput', donatur.id, NULL)) AS totkun
             FROM users LEFT JOIN donatur ON donatur.petugas = users.name WHERE users.id_com = '$y' 
             GROUP BY users.name, users.kota, users.kolektor, users.aktif HAVING users.kolektor = 'kolektor' AND users.aktif = 1");
    
            $kk = Kantor::where('kantor_induk', Auth::user()->id_kantor)->where('tambahan.id_com', Auth::user()->id_com)->first();
            $kots = Auth::user()->id_kantor;
    
            if(Auth::user()->kolekting =='admin'){
                $kotas = Kantor::where('id_com', Auth::user()->id_com)->get();
            }else if(Auth::user()->kolekting =='kacab'){
                if($kk == null){
                    $kotas = Kantor::where('id',$kots)->where('id_com', Auth::user()->id_com)->get();
                }else{
                    $kotas = Kantor::whereRaw("(id = '$kots' OR id = '$kk->id') AND id_com = '$y'")->get();
                }
            }
            $progs = Prog::all();
            //  $progs = \DB::select("SELECT distinct subprogram from transaksi where kota != 'null'");
    
            $tahunn = Transaksi::select(\DB::raw("YEAR(created_at) AS date"))->where('via_input', 'transaksi')->groupBy('date')->get();
            
            $pem = Transaksi::selectRaw("DISTINCT(pembayaran) as pembayaran")->whereRaw("pembayaran IS NOT NULL AND via_input = 'transaksi'")->get();
            
    
            return view('core.capaian', compact('rincian', 'datacabang', 'dari', 'sampai', 'dari2', 'sampai2', 'field', 'kotas', 'progs', 'belum', 'belumass', 'tahunn','k','pem'));
        }
    }
    
    public function assign_tot(Request $request)
    {
        if (request()->ajax()) {
            $bln = $request->bln == '' ? Carbon::now()->format('m-Y') : $request->bln;
            
            $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
            $sampai = $request->sampaii == '' ? $dari : $request->sampaii;
            
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            $kan = Auth::user()->id_kantor;
            $k = Kantor::where('kantor_induk', $kan)->first();
            
            $tj = Tunjangan::first();
            
            if($request->tab == 'tab1'){
                if(Auth::user()->kolekting =='admin'){
                    if($request->plhtgl == 0){
                        $data = Donatur::selectRaw("COUNT(IF(DATE(tgl_kolek) >= '$dari' AND DATE(tgl_kolek) <= '$sampai', id, NULL)) as ass_all, COUNT(IF(DATE(tgl_kolek) >= '$dari' AND DATE(tgl_kolek) <= '$sampai' AND acc = 1, id, NULL)) as tgl_kol, COUNT(IF(acc = 1, id, NULL)) as tot, id_koleks, petugas")
                                ->whereIn('id_koleks', function($query) use ($tj){
                                        $query->select('id')->from('users')->where('aktif', 1)->where('id_jabatan', $tj->kolektor);
                                    })
                                ->groupBy('id_koleks','petugas');
                    }else if($request->plhtgl == 1){
                        $data = Donatur::selectRaw("COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun', id, NULL)) as ass_all, COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun' AND acc = 1, id, NULL)) as tgl_kol, COUNT(IF(acc = 1, id, NULL)) as tot, id_koleks, petugas")
                                ->whereIn('id_koleks', function($query) use ($tj){
                                        $query->select('id')->from('users')->where('aktif', 1)->where('id_jabatan', $tj->kolektor);
                                    })
                                ->groupBy('id_koleks','petugas');
                    }
                }else if(Auth::user()->kolekting =='kacab'){
                    if($request->plhtgl == 0){
                        if($k == null){
                            $data = Donatur::selectRaw("COUNT(IF(DATE(tgl_kolek) >= '$dari' AND DATE(tgl_kolek) <= '$sampai', id, NULL)) as ass_all, COUNT(IF(DATE(tgl_kolek) >= '$dari' AND DATE(tgl_kolek) <= '$sampai' AND acc = 1, id, NULL)) as tgl_kol, COUNT(id) as tot, id_koleks, petugas")
                                    ->whereIn('id_koleks', function($query) use ($tj){
                                        $query->select('id')->from('users')->where('aktif', 1)->where('id_jabatan', $tj->kolektor);
                                    })
                                    ->whereRaw("id_kantor = '$kan'")->groupBy('id_koleks','petugas');
                        }else{
                            $data = Donatur::selectRaw("COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun', id, NULL)) as ass_all, COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun' AND acc = 1, id, NULL)) as tgl_kol, COUNT(IF(acc = 1, id, NULL)) as tot, id_koleks, petugas")
                                    ->whereIn('id_koleks', function($query) use ($tj){
                                        $query->select('id')->from('users')->where('aktif', 1)->where('id_jabatan', $tj->kolektor);
                                    })
                                    ->whereRaw("(id_kantor = '$kan' OR id_kantor = '$k->id')")->groupBy('id_koleks','petugas');
                        }
                    }else if($request->plhtgl == 1){
                        if($k == null){
                            $data = Donatur::selectRaw("COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun', id, NULL)) as ass_all, COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun' AND acc = 1, id, NULL)) as tgl_kol, COUNT(IF(acc = 1, id, NULL)) as tot, id_koleks, petugas")
                                    ->whereIn('id_koleks', function($query) use ($tj){
                                        $query->select('id')->from('users')->where('aktif', 1)->where('id_jabatan', $tj->kolektor);
                                    })
                                    ->whereRaw("id_kantor = '$kan'")->groupBy('id_koleks','petugas');
                        }else{
                            $data = Donatur::selectRaw("COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun', id, NULL)) as ass_all, COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun' AND acc = 1, id, NULL)) as tgl_kol, COUNT(IF(acc = 1, id, NULL)) as tot, id_koleks, petugas")
                                    ->whereIn('id_koleks', function($query) use ($tj){
                                        $query->select('id')->from('users')->where('aktif', 1)->where('id_jabatan', $tj->kolektor);
                                    })
                                    ->whereRaw("(id_kantor = '$kan' OR id_kantor = '$k->id')")->groupBy('id_koleks','petugas');
                        }
                    }
                }
                
                $aha = $data->get();
                $haday = [];
                
                $f1 = 0;
                $f2 = 0;
                $f3 = 0;
                foreach($aha as $y){
                    
                    $f1 += $y->ass_all;
                    $f2 += $y->tgl_kol;
                    $f3 += $y->tot;
                    
                }
                
                $haday = [
                    'f1' => $f1,
                    'f2' => $f2,
                    'f3' => $f3
                ];
                
                return $haday;
            }
            
            if(Auth::user()->kolekting =='admin'){
                if($request->plhtgl == 0){
                    $data = Donatur::selectRaw("COUNT(IF(DATE(tgl_kolek) >= '$dari' AND DATE(tgl_kolek) <= '$sampai', id, NULL)) as ass_all, COUNT(IF(DATE(tgl_kolek) >= '$dari' AND DATE(tgl_kolek) <= '$sampai' AND acc = 1, id, NULL)) as tgl_kol, COUNT(IF(acc = 1, id, NULL)) as tot, id_koleks, petugas")
                            ->whereIn('id_koleks', function($query) use ($tj){
                                    $query->select('id')->from('users')->where('aktif', 1)->where('id_jabatan', $tj->kolektor);
                                })
                            ->groupBy('id_koleks','petugas');
                }else if($request->plhtgl == 1){
                    $data = Donatur::selectRaw("COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun', id, NULL)) as ass_all, COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun' AND acc = 1, id, NULL)) as tgl_kol, COUNT(IF(acc = 1, id, NULL)) as tot, id_koleks, petugas")
                            ->whereIn('id_koleks', function($query) use ($tj){
                                    $query->select('id')->from('users')->where('aktif', 1)->where('id_jabatan', $tj->kolektor);
                                })
                            ->groupBy('id_koleks','petugas');
                }
            }else if(Auth::user()->kolekting =='kacab'){
                if($request->plhtgl == 0){
                    if($k == null){
                        $data = Donatur::selectRaw("COUNT(IF(DATE(tgl_kolek) >= '$dari' AND DATE(tgl_kolek) <= '$sampai', id, NULL)) as ass_all, COUNT(IF(DATE(tgl_kolek) >= '$dari' AND DATE(tgl_kolek) <= '$sampai' AND acc = 1, id, NULL)) as tgl_kol, COUNT(IF(acc = 1, id, NULL)) as tot, id_koleks, petugas")
                                ->whereIn('id_koleks', function($query) use ($tj){
                                    $query->select('id')->from('users')->where('aktif', 1)->where('id_jabatan', $tj->kolektor);
                                })
                                ->whereRaw("id_kantor = '$kan'")
                                ->groupBy('id_koleks','petugas');
                    }else{
                        $kkk = $k->id;
                        $data = Donatur::selectRaw("COUNT(IF(DATE(tgl_kolek) >= '$dari' AND DATE(tgl_kolek) <= '$sampai', id, NULL)) as ass_all, COUNT(IF(DATE(tgl_kolek) >= '$dari' AND DATE(tgl_kolek) <= '$sampai' AND acc = 1, id, NULL)) as tgl_kol, COUNT(IF(acc = 1, id, NULL)) as tot, id_koleks, petugas")
                                ->whereIn('id_koleks', function($query) use ($tj){
                                    $query->select('id')->from('users')->where('aktif', 1)->where('id_jabatan', $tj->kolektor);
                                })
                                ->whereRaw("(id_kantor = '$kan' OR id_kantor = '$kkk')")->groupBy('id_koleks','petugas');
                    }
                }else if($request->plhtgl == 1){
                    if($k == null){
                        $data = Donatur::selectRaw("COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun', id, NULL)) as ass_all, COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun' AND acc = 1, id, NULL)) as tgl_kol, COUNT(IF(acc = 1, id, NULL)) as tot, id_koleks, petugas")
                                ->whereIn('id_koleks', function($query) use ($tj){
                                    $query->select('id')->from('users')->where('aktif', 1)->where('id_jabatan', $tj->kolektor);
                                })
                                ->whereRaw("id_kantor = '$kan'")->groupBy('id_koleks','petugas');
                    }else{
                        $kkk = $k->id;
                        $data = Donatur::selectRaw("COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun', id, NULL)) as ass_all, COUNT(IF(MONTH(tgl_kolek) = '$bulan' AND YEAR(tgl_kolek) = '$tahun' AND acc = 1, id, NULL)) as tgl_kol, COUNT(IF(acc = 1, id, NULL)) as tot, id_koleks, petugas")
                                ->whereIn('id_koleks', function($query) use ($tj){
                                    $query->select('id')->from('users')->where('aktif', 1)->where('id_jabatan', $tj->kolektor);
                                })
                                ->whereRaw("(id_kantor = '$kan' OR id_kantor = '$kkk')")->groupBy('id_koleks','petugas');
                    }
                }
            }
            
            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
        }
    }
    
     public function targetgetcab(){
        if(request()->ajax())
        {
            $kota = Auth::user()->kota;
            $data = Target::where('kota', $kota)->orderBy('id','desc')->first();
            return $data;
        }
    }
    
    public function targetkacc(Request $request){
        $target = new Target;
        $target->kota = Auth::user()->kota;
        $target->target = preg_replace("/[^0-9]/", "", $request->targetkac);
        $target->save();
        return back();
    }
    
    public function export_dulu(Request $request){
        
        $dari = $request->darii == '' ? Carbon::now()->toDateString() : $request->darii;
        $sampai = $request->sampaii == '' ? Carbon::now()->toDateString() : $request->sampaii;
        
        $thn = $request->thn == '' ? Carbon::now()->format('Y') : $request->thn;
        $bln = $request->bln == '' ? Carbon::now()->format('m-Y') : $request->bln;
        
        $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
        $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
        
         $field = $request->field;
        
        if($request->plhtgl == '0'){
            $ee = 'periode-'.$dari.'-s.d-'.$sampai;
            $request['priod'] = 'Periode '.$dari.' s.d '.$sampai;
        }else if($request->plhtgl == '1'){
            $ee = 'bulan-'.$bulan.'-tahun-'.$tahun;
            $request['priod'] = 'Bulan '.$bulan.' Tahun '.$tahun;
        }else{
            $ee = 'tahun-'.$thn;
            $request['priod'] = 'Tahun '.$tahun;
        }
        
        if($field == 'program') {
            $request['jdl'] = 'Berdasarkan Program';
            $pw = 'berdasarkan-program';
        }else if($field == 'kota'){
            $request['jdl'] = 'Berdasarkan Kota'; 
            $pw = 'berdasarkan-kota';
        }
        
        $response =  Excel::download(new HomeExport($request), 'report-transaksi-'.$pw.'-'.$ee.'.xlsx');
        ob_end_clean();
        
        return $response;
    }
}
