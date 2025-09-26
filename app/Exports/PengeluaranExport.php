<?php

namespace App\Exports;

use Auth;
use DB;
use App\Models\Kantor;
use App\Models\Bank;
use App\Models\COA;
use App\Models\Pengeluaran;
use App\Models\Jabatan;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Anggaran;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PengeluaranExport implements FromView
{
    public function __construct( $request, $jdl)
    {
    
        $this->jdl = $jdl;
        $this->request = $request;
        return $this;
    }

    public function view(): View
    {
        $jdl = $this->jdl;
        $request = $this->request;
        
        $kz = Auth::user()->id_kantor;
        $cek = Kantor::where('kantor_induk', $kz)->first();
        $jabat = Jabatan::where('id_com', Auth::user()->id_com)->get();
        if(Auth::user()->level === 'admin' || Auth::user()->keuangan == 'keuangan pusat'){
            $kantor = Kantor::where('id_com', Auth::user()->id_com)->get();
        }else{
            if($cek == null){
                $kantor = Kantor::where('id',Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->get();
                
            }else{
                $kantor = Kantor::whereRaw("(id = $kz OR id = $cek->id)")->where('id_com', Auth::user()->id_com)->get();
            }
        }
        
        $bank = Bank::all();
   
        $dari = $request->dari != '' ? $request->dari : date('Y-m-d');
        $sampai = $request->sampai != '' ? $request->sampai : $dari;
        
        $via = $request->via != '' ? "via_input = '$request->via'": "via_input IS NOT NULL";
        $stts = $request->stts != '' ? "acc = '$request->stts'": "acc IS NOT NULL";
        $kntr = $request->kntr != '' ? "kantor = '$request->kntr'": "kantor IS NOT NULL";
            
            if ($request->daterange != '') {
                $tgl = explode(' - ', $request->daterange);
                $dari = date('Y-m-d', strtotime($tgl[0]));
                $sampai = date('Y-m-d', strtotime($tgl[1]));
            }
            
            $bln = $request->bulan == '' ? Carbon::now()->format('m-Y') : $request->bulan;
            $bulan = Carbon::createFromFormat('m-Y', $bln)->format('m');
            $tahun = Carbon::createFromFormat('m-Y', $bln)->format('Y');
            
            $filt = $request->filt;
            
            $now = date('Y-m-d');
            $tgls = $request->daterange != '' ? "DATE(tgl) >= '$dari' AND DATE(tgl) <= '$sampai'" : "DATE(tgl) >= '$now' AND DATE(tgl) <= '$now'";
            $bln = "MONTH(tgl) = '$bulan' AND YEAR(tgl) = '$tahun'";
            
            $pilihan = $request->pembayaran;
            $pembayaran = function ($query) use ($pilihan) {
                if($pilihan != '' && !empty($pilihan)){
                    if (in_array('mutasi', $pilihan)) {
                        $query->orWhere('pengeluaran.pembayaran', 'mutasi');
                    }
                    if (in_array('noncash', $pilihan)) {
                        $query->orWhere('pengeluaran.pembayaran', 'noncash');
                    }
                    if (in_array('bank', $pilihan)) {
                        $query->orWhere('pengeluaran.pembayaran', 'transfer')
                              ->orWhere('pengeluaran.pembayaran', 'bank');
                    }
                    if (in_array('cash', $pilihan)) {
                        $query->orWhere('pengeluaran.pembayaran', 'dijemput')
                              ->orWhere('pengeluaran.pembayaran', 'teller')
                              ->orWhere('pengeluaran.pembayaran', 'cash');
                    }
                }
            };
            
            
            if(Auth::user()->keuangan == 'admin' ||  Auth::user()->keuangan == 'keuangan pusat' ){
                if($filt == 'p'){
                   $data = Pengeluaran::whereRaw("$via AND $tgls AND $kntr AND $stts")->where($pembayaran)->get();
                }else{
                    $data = Pengeluaran::whereRaw("$via AND $bln AND $kntr AND $stts")->where($pembayaran)->get();
                }
            }else{
                if($cek == null){
                    if($filt == 'p'){
                        $data = Pengeluaran::whereRaw("$via AND $tgls AND kantor = '$kz' AND $stts")->where($pembayaran)->get();
                    }else{
                        $data = Pengeluaran::whereRaw("$via AND $bln AND kantor = '$kz' AND $stts")->where($pembayaran)->get();
                    }
                }else{
                    if($request->kntr != ''){
                        if($filt == 'p'){
                            $data = Pengeluaran::whereRaw("$via AND $tgls AND kantor = '$request->kntr' AND $stts")->where($pembayaran)->get();
                        }else{
                            $data = Pengeluaran::whereRaw("$via AND $bln AND kantor = '$request->kntr' AND $stts")->where($pembayaran)->get();
                        }
                    }else{
                        if($filt == 'p'){
                            $data = Pengeluaran::whereRaw("$via AND $tgls AND (kantor = '$kz' OR kantor = '$cek->id') AND $stts")->where($pembayaran)->get();
                        }else{
                            $data = Pengeluaran::whereRaw("$via AND $bln AND (kantor = '$kz' OR kantor = '$cek->id') AND $stts")->where($pembayaran)->get();
                        }
                    }
                }
            }
            //   dd($data);
        return view('ekspor.pengeluaranexport',[
                    'data' => $data,
                    'periode' => $jdl,
                    'kompani' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name
        ]);
    }
    
  
}


