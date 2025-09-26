<?php

namespace App\Exports;

use App\Models\Kantor;
use App\Models\Bank;
use App\Models\COA;
use App\Models\Transaksi;
use App\Models\Penutupan;
use DB;
use Auth;
use App\Models\Pengeluaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class KasbankExport implements WithHeadings ,FromCollection
{

   public function __construct( string $kntr, string $a)
    {
        
        // $this->bulan = $bulan;
        $this->tanggal = $a;
        $this->kntr = $kntr;
        return $this;
    }

    public function collection()
    {
          
           
       
            
             $thisk = $this->kntr;
             $id = Auth::user()->id_kantor;
            $kantor = $this->kntr != '' ? "id_kantor = $this->kntr" : "id_kantor != ''";  
           $tanggal = $this->tanggal == '' ? Carbon::now()->format('Y-m-d') : $this->tanggal;
           
            $union = transaksi::selectRaw("coa_debet,coa_kredit,tanggal,jumlah, 0 as nominal")
                ->unionAll(pengeluaran::selectRaw("coa_kredit,coa_debet,tgl,0 as jumlah,nominal"));
            
            $datas = COA::selectRaw("coa.coa, coa.nama_coa, coa.konak, SUM(sub.jumlah) as debet, SUM(sub.nominal) as kredit, coa.konak + sub.jumlah - sub.nominal , sub.tanggal , tambahan.unit ")
            ->leftJoin('penutupan','penutupan.coa_pen','=','coa.coa')
            ->leftJoin('tambahan','tambahan.id','=','coa.id_kantor')
            ->leftjoin(DB::raw("({$union->toSql()}) as sub"),function($join) use ($tanggal,$kantor) {
                        $join->on('sub.coa_debet' ,'=', 'coa.coa')
                                ->whereRaw("DATE(sub.tanggal) = '$tanggal'")
                                // ->whereRaw("coa.id_kantor = '$kantor'")
                                //   ->whereRaw("id_kantor = '$request->kntr' AND (grup = 3 OR grup = 4) AND parent = 'n' ")
                                // ->whereDate('sub.tanggal', $hari)
                                // ->whereMonth('sub.tanggal', $thisb)
                                // ->whereYear('sub.tanggal', $thist)
                                
                                ;
                        })
          ->groupBy('coa.coa')
            ->orderBy('sub.tanggal', 'DESC')
            ->whereRaw("$thisk AND (grup = 3 OR grup = 4) AND parent = 'n' ")->get();
           
              return $datas;
        

    }
        public function headings(): array
    {
        return [
            'Coa', 'Nama Coa', 'Saldo Awal', 'Debet', 'Kredit', 'Saldo Akhir','Last Opname', 'Unit'
        ];
    }
    
   
    
}







