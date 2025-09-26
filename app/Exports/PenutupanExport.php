<?php

namespace App\Exports;

use Auth;
use DB;
use App\Models\Kantor;
use App\Models\Bank;
use App\Models\COA;
use App\Models\Pengeluaran;
use App\Models\Jabatan;
use DateTime;
use App\Models\User;
use App\Models\Anggaran;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PenutupanExport implements FromView
{
    public function __construct( $request, $k_period )
    {
    
        $this->request = $request;
        $this->k_period = $k_period;
        return $this;
    }

    public function view(): View
    {
        $request = $this->request;
        $k_period = $this->k_period;
        
        $buku = $request->buk;
            
        if ($request->daterange != '') {
            $tgl = explode(' - ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }
    
        $now = date('Y-m-d');
            
        if($request->daterange != ''){
            $begin = new DateTime($dari); 
            $end = new DateTime($sampai);
        }else{
            $begin = new DateTime($now); 
            $end = new DateTime($now);
        }  
            
            
        if($buku != ''){
            if($buku == 'kas'){
                $buk = "grup = 3";
            }else{
                $buk = "grup = 4";
            }
        }else{
            $buk = "(grup = 3 OR grup = 4)";
        }
        
        $akun = $request->akun == '' ? "coa.coa IS NOT NULL" : "coa.coa = '$request->akun'";
        
        $waduw = $request->daterange != '' ? "DATE(penutupan.created_at) >= '$dari' AND DATE(penutupan.created_at) <= '$sampai'" : "DATE(penutupan.created_at) >= '$now' AND DATE(penutupan.created_at) <= '$now'";
            
        $datas = COA::selectRaw("coa.*, penutupan.*, penutupan.created_at as pawon, '$request->pen' as p")->leftJoin('penutupan','penutupan.coa_pen','=','coa.coa')->whereRaw("id_kantor = '$request->kans' AND $buk AND parent = 'n' AND $akun")->get();
            
        for($i = $begin; $i <= $end; $i->modify('+1 day')){
            foreach($datas as $d){
                $c_tgl = $d->pawon != '' ? date('Y-m-d',strtotime($d->pawon)) : '';
                
                if($i->format("Y-m-d") == $c_tgl){
                    $saldo_akhir = $d->saldo_akhir;
                    $saldo_awal = $d->saldo_awal;
                    $debit = $d->debit;
                    $kredit = $d->kredit;
                    $adjustment = $d->adjustment;
                    $user_input = $d->user_input;
                    $user_update = $d->user_update;
                    $k100000 = $d->k100000;
                    $k75000 = $d->k75000;
                    $k50000 = $d->k50000;
                    $k20000 = $d->k20000;
                    $k10000 = $d->k10000;
                    $k5000 = $d->k5000;
                    $k2000 = $d->k2000;
                    $k1000 = $d->k1000;
                    // $k500 = $d->k500;
                    // $k100 = $d->k100;
                    $l1000 = $d->l1000;
                    $l500 = $d->l500;
                    $l200 = $d->l200;
                    $l100 = $d->l100;
                    // $l50 = $d->l50;
                    // $l25 = $d->l25;
                }else {
                    $saldo_akhir = '';
                    $saldo_awal = '';
                    $debit = '';
                    $kredit = '';
                    $adjustment = '';
                    $user_input = '';
                    $user_update = '';
                    $k100000 = '';
                    $k75000 = '';
                    $k50000 = '';
                    $k20000 = '';
                    $k10000 = '';
                    $k5000 = '';
                    $k2000 = '';
                    $k1000 = '';
                    // $k500 = '';
                    // $k100 = '';
                    $l1000 = '';
                    $l500 = '';
                    $l200 = '';
                    $l100 = '';
                    // $l50 = '';
                    // $l25 = '';
                }
    
                $data[]  = [
                            'tanggal' => $i->format("Y-m-d"),
                            'coa' => $d->coa,
                            'id_kantor' => $d->id_kantor,
                            'nama_coa' => $d->nama_coa,
                            'saldo_akhir' => $saldo_akhir,
                            'saldo_awal' => $saldo_awal ,
                            'debit' => $debit,
                            'kredit' => $kredit,
                            'adjustment' => $adjustment,
                            'user_input' => $user_input,
                            'user_update' => $user_update,
                            'k100000' =>  $k100000 ,
                            'k75000' => $k75000,
                            'k50000' => $k50000,
                            'k20000' => $k20000,
                            'k10000' => $k10000,
                            'k5000' => $k5000,
                            'k2000' => $k2000,
                            'k1000' => $k1000,
                            // 'k500' => $k500,
                            // 'k100' => $k100,
                            'l1000' => $l1000,
                            'l500' => $l500,
                            'l200' => $l200,
                            'l100' => $l100,
                            // 'l50' => $l50,
                            // 'l25' => $l25,
                            'grup' => $d->grup,
                            'p' => $d->p
                        ];
            }
        }
              
        return view('ekspor.penutupanexport',[
                    'data' => $data,
                    'periode' => $k_period,
                    'kant' => DB::table('tambahan')->where('id', $request->kans)->first()->unit,
                    'kompani' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name
        ]);
    }
    
  
}


