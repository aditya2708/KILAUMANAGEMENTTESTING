<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\LogActivity as LogActivityModel;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Donatur;
use App\Models\Program;
use App\Models\Prog;
use App\Models\Kantor;
use App\Models\Bank;
use App\Models\COA;
use App\Models\User;
use App\Models\GrupCOA;
use App\Models\LogActivity;
use App\Models\SaldoAw;
use App\Models\Penutupan;
use App\Models\Penerimaan;
use App\Models\Pengeluaran;
use App\Models\C_advance;
use App\Models\Anggaran;
use App\Models\Transaksi;
use App\Models\SumberDana;
use App\Models\Tunjangan;
use App\Models\Jurnal;
use App\Exports\ResumeanggaranExport;
use App\Exports\KasbankExport;
use App\Exports\AnggaranExport;
use App\Exports\downloadformat;
use App\Exports\DPExport;
use DataTables;
use DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Staudenmeir\LaravelCte;
use App\Imports\c_advanceImport;
use App\Imports\AnggaranImport;
use Excel;

class RiwayatPerubahanController extends Controller
{
      public function riwayat_perubahan(Request $request){
          
        if($request->ajax())
        {
        // $vias = $request->via == '' ? "via = ''" : "via = '$request->via'";
           $vias = $request->via == '' ? "via IS NOT NULL" : "via = '$request->via'";
           
            $hari = $request->dari == '' ? Carbon::now()->format('Y-m-d') : $request->dari;
            $hari2 = $request->sampai == '' ? $hari : $request->sampai;
           
            $bln1 = $request->darib == '' ? Carbon::now()->format('Y-m') : $request->darib;
            $bln2 = $request->sampaib == '' ? $bln1 : $request->sampaib;
            $thn = $request->thnn == '' ? Carbon::now()->format('Y') : $request->thnn;
       
            if ($request->periodenya == 'harian') {
                $plugin =  "DATE_FORMAT(created_at, '%Y-%m-%d') >= '$hari' AND DATE_FORMAT(created_at,'%Y-%m-%d') <= '$hari2'";
            }else if($request->periodenya == 'bulan'){
                $plugin = "DATE_FORMAT(created_at, '%Y-%m') >= '$bln' AND DATE_FORMAT(created_at,'%Y-%m') <= '$bln2'";
            }else{
                $plugin = "YEAR(created_at) = '$thn' ";
            }
        $data = LogActivity::selectRaw("id_data,keterangan,jenis_aksi,via,DATE_FORMAT(created_at, '%d-%m-%Y') as tanggal")->whereRaw("id_data != '' AND $vias AND $plugin AND via != 'donatur'")->get();
         
            return DataTables::of($data)
                    ->make(true);
        }
        return view('report-management.riwayat_perubahan');
        // return view('logActivity',compact('logs'));
    }


      public function detail_perubahan(Request $request){
          

        $id = $request->id_data;
        $via = $request->via;


        if($via == 'transaksi'){
            
        $data['d'] = Transaksi::selectRaw("transaksi.id,transaksi.ket_penerimaan as keterangan,transaksi.bukti,transaksi.jumlah as nominal,transaksi.pembayaran,transaksi.user_insert,transaksi.coa_debet,transaksi.coa_kredit")->whereRaw("id = '$id'")->first();
        $data['ui'] = User::select('name')->where('id', $data['d']->user_insert)->first();
        }else{
        $data['d'] = Pengeluaran::selectRaw("pengeluaran.id,pengeluaran.keterangan,pengeluaran.bukti,pengeluaran.nominal,pengeluaran.pembayaran,pengeluaran.user_input,pengeluaran.coa_debet,pengeluaran.coa_kredit")->whereRaw("id = '$id'")->first();
        $data['ui'] = User::select('name')->where('id', $data['d']->user_input)->first();
        }
        $data['p'] = COA::selectRaw("nama_coa,coa")->where('coa', $data['d']->coa_kredit)->first();
        $data['z'] = COA::selectRaw("nama_coa,coa")->where('coa', $data['d']->coa_debet)->first();
        
        return $data;
        // return view('logActivity',compact('logs'));
    }


     public function perubahan_donatur(Request $request){
        if($request->ajax())
        {
        // $vias = $request->via == '' ? "via = ''" : "via = '$request->via'";
        //   $vias = $request->via == '' ? "via IS NOT NULL" : "via = '$request->via'";
        
            $dari = $request->dari == '' ? Carbon::now()->format('Y-m-d') : $request->dari;
            $sampai = $request->sampai == '' ? $dari : $request->sampai;
        
            $bln1 = $request->darib == '' ? Carbon::now()->format('Y-m') : $request->darib;
            $bln2 = $request->sampaib == '' ? $bln1 : $request->sampaib;
            $thn = $request->thnn == '' ? Carbon::now()->format('Y') : $request->thnn;
       
            if ($request->periodenya == 'harian' || $request->periodenya == '') {
                $plugin =  "DATE_FORMAT(created_at, '%Y-%m-%d') >= '$dari' AND DATE_FORMAT(created_at,'%Y-%m-%d') <= '$sampai'";
            }else if($request->periodenya == 'bulan'){
                $plugin = "DATE_FORMAT(created_at, '%Y-%m') >= '$bln' AND DATE_FORMAT(created_at,'%Y-%m') <= '$bln2'";
            }else if ($request->periodenya == 'tahun'){
                $plugin = "YEAR(created_at) = '$thn' ";
            }
            
        $data = LogActivity::selectRaw("id_data,keterangan,jenis_aksi,via,DATE_FORMAT(created_at, '%Y-%m-%d') as tanggal ")
        ->whereRaw("id_data != '' AND via = 'donatur' AND $plugin ")->orderBy('id', 'DESC')->get();
         
            return DataTables::of($data)
                    ->make(true);
        }
        return view('report-management.perubahan_donatur');
        // return view('logActivity',compact('logs'));
    }



      public function detail_perbdon(Request $request){
          

        $id = $request->id_data;
        $data['d'] = Donatur::selectRaw("donatur.*")->whereRaw("id = '$id'")->first();

        
        return $data;
        // return view('logActivity',compact('logs'));
    }


    }
    
    
      

