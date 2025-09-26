<?php

namespace App\Exports;

use Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use App\Models\SaldoAw;
use App\Models\Kantor;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
class SaldoAwalExport implements FromView
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     
    public function __construct($bln,$lvl,$pr,$bulan,$tahun,$query)
    {
        
        $this->bln = $bln ;
        $this->lvl = $lvl ;
        $this->pr = $pr ;
        $this->bulan = $bulan ;
        $this->tahun = $tahun ;
        $this->query = $query;
        return $this;
    }

    public function view(): View
    {
        
        $bln = $this->bln   ;
        $lvl = $this->lvl ;
        $pr = $this->pr ;
        $bulan = $this->bulan ;
        $tahun = $this->tahun ;
        $query = $this->query ;
        
         if($bln == ''){
                
                
                $saldo = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.id_parent, t.id, t.level ,t.nama_coa,  0 as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor,0 as closing")
                        ->withRecursiveExpression('b', $query)
                        ->whereRaw("t.level $lvl AND t.parent $pr")
                        ->groupBy('root');
                    
            }else{
                 
                $terbaru = SaldoAw::whereRaw("MONTH(bulan) = '$bulan' AND YEAR(bulan) = '$tahun' AND (closing = '1' OR closing = '0')")->get();
                $ngecek = count($terbaru);
                
                if($ngecek > 0){
                    $saldo = DB::table('b as t')
                        ->selectRaw("root, t.coa, t.id_parent, t2.id, t.id as coa_coy, t.level ,t.nama_coa,  IF(SUM(t2.saldo_awal) IS NOT NULL, SUM(t2.saldo_awal), 0)  as saldo_awal, '$bln' as tanggals,  t.parent, t.id_kantor, t2.closing")
                        ->withRecursiveExpression('b', $query)
                        
                        ->leftjoin('saldo_awal as t2',function($join) use ($bulan, $tahun) {
                            $join->on('t2.coa' ,'=', 't.coa')
                                ->select('t2.saldo_awal')
                                ->whereMonth('t2.bulan', $bulan)
                                ->whereYear('t2.bulan', $tahun)
                                ;
                        })
                        ->whereRaw("t.parent $lvl AND t.parent $pr")
                        ->groupBy('root');
                    
                }else{
                    $saldo = DB::table('b as t')
                    ->selectRaw("root, t.coa, t.id_parent, t.id, t.level ,t.nama_coa,  0 as saldo_awal, '$bln' as tanggals, t.parent, t.id_kantor, 0 as closing")
                    ->withRecursiveExpression('b', $query)
                    ->whereRaw("t.level $lvl AND t.parent $pr")
                    ->groupBy('root');
                }
                
            }
            $inArray=[];
            $datas = $saldo->get();
            foreach($datas as $data ){
                $p = Kantor::where('id', $data->id_kantor)->first();
                if($p == null ){
                    $jml = '';
                }else{
                    $jml = $p->unit;
                }
                $inArray[] = [
                    'coa' => $data->coa,
                    'nama_coa'=> $data->nama_coa,
                    'tanggals'=> $data->tanggals,
                    'saldo_awal'=> $data->saldo_awal,
                    'parent'=> $data->parent,
                    'kantor'=> $jml,
                    ];
            }
        return view('ekspor.saldoawalexport',[
            'data' => $inArray,
            'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()
        ]);

            
    }    
    
}
