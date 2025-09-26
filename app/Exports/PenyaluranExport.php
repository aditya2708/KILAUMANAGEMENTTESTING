<?php

namespace App\Exports;

use Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use App\Models\Pengeluaran;
use App\Models\Asnaf;
use App\Models\Bank;
use App\Models\Kantor;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
class PenyaluranExport implements FromView
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     
    public function __construct($request, $prdss)
    {
        
        $this->request = $request ;
        $this->prdss = $prdss ;
        return $this;
    }

    public function view(): View
    {
        $request = $this->request;
        $prdss = $this->prdss;
        
        if ($request->daterange != '') {
            $tgl = explode(' - ', $request->daterange);
            $dari = date('Y-m-d', strtotime($tgl[0]));
            $sampai = date('Y-m-d', strtotime($tgl[1]));
        }else{
            $hari_ini = date('Y-m-d');
            $dari = date('Y-m-d', strtotime($hari_ini));
            $sampai = date('Y-m-d', strtotime($hari_ini));
        }
        if ($request->bln != '') {
            $tgl = explode('-', $request->bln);
            $t = date($tgl[0]);
            $b = date($tgl[1]);
        }else{
            $b = date('m');
            $t = date('Y');
        }
        
        if ($request->tglSalur != '') {
            $tgl1 = explode(' - ', $request->tglSalur);
            $dari1 = date('Y-m-d', strtotime($tgl1[0]));
            $sampai1 = date('Y-m-d', strtotime($tgl1[1]));
        }else{
            $hari_ini1 = date('Y-m-d');
            $dari1 = date('Y-m-d', strtotime($hari_ini1));
            $sampai1 = date('Y-m-d', strtotime($hari_ini1));
        }
        if ($request->bln != '') {
            $tgl = explode('-', $request->bln);
            $t = date($tgl[0]);
            $b = date($tgl[1]);
        }else{
            $b = date('m');
            $t = date('Y');
        }
        
        
        if($request->mohon == '0' || $request->mohon == null){
             $mohon = "pengeluaran.tgl_mohon";
        }
        else if($request->mohon == '1'){
             $mohon = "pengeluaran.tgl_salur";
        }
        else if($request->mohon == '2'){
             $mohon = "pengeluaran.tgl";
        }
        
        
        if($request->prd == 0){
            $prd = "DATE($mohon) >= '$dari' AND DATE($mohon) <= '$sampai'";
            $prdFile = $dari . '-' . $sampai;
            $tglSalur = "DATE(pengeluaran.tgl_salur) >= '$dari' AND DATE(pengeluaran.tgl_salur) <= '$sampai'";
        }else if($request->prd == 1){
            $tglSalur = "DATE(pengeluaran.tgl_salur >= '$dari' AND DATE(pengeluaran.tgl_salur) <= '$sampai'";
            $prdFile = $b . '-' . $t;
            $prd = "MONTH($mohon) = '$b' AND YEAR($mohon) = '$t'";
        }

        $r_prog = $request->program;
         
        $nom_tran = $request->dari_nominal != ''  || !empty($request->dari_nominal) ? "pengeluaran.nominal >= '$request->dari_nominal' AND pengeluaran.nominal <= '$request->sampai_nominal'" : "pengeluaran.nominal > 0";
        
        $jenisTransaksi = function($q) use ($request) {
                            if($request->jenis_transaksi != ''){ $q->whereIn('pengeluaran.pembayaran', $request->jenis_transaksi); }
                        };
                        
        $jenisPM = function($q) use ($request) {
                            if( $request->jenisPM != ''){ $q->where('pmnf.jenis_pm', $request->jenisPM); }
                        };
                        
        $PJ = function($q) use ($request) {
                            if($request->PJ != ''){ $q->where('pmnf.nama_pj', $request->PJ); }
                        };
                        
        $status = function($q) use ($request) {
                            if($request->status != ''){ $q->where('pengeluaran.acc', $request->status); }
                        };
                        
        $filAsnaf = function($q) use ($request) {
                        if($request->asnaf != ''){
                            // $q->whereRaw('pmnf.asnaf IS NOT NULL');
                            $q->whereIn('pmnf.asnaf', $request->asnaf);
                        }
                        // else{
                        // }
                    };
                    
        $advKantor = function($q) use ($request) {
                        if($request->advKantor == '' || empty($request->advKantor)){
                            $q->whereRaw('pengeluaran.kantor IS NOT NULL');
                        }else{
                            $q->whereIn('pengeluaran.kantor', $request->advKantor);
                        }
                    };
        
        $backdate    = $request->backdate == '' || empty($request->backdate) ? "DATE(pengeluaran.tgl) IS NOT NULL AND DATE(pengeluaran.created_at) IS NOT NULL" : ($request->backdate == 0 ? "DATE(pengeluaran.tgl_mohon) != DATE(pengeluaran.created_at) " : "DATE(pengeluaran.tgl_mohon) = DATE(pengeluaran.created_at)");
        
            $data = Pengeluaran::selectRaw("pengeluaran.*, pmnf.penerima_manfaat as pm, pmnf.jenis_pm as jenis_pm")
            ->whereRaw("$prd AND via_input = 'penyaluran' AND $nom_tran AND $backdate")
                ->leftjoin('penerima_manfaat AS pmnf', 'pmnf.id','=','pengeluaran.id_pm')
                ->leftjoin('tambahan as kantor','kantor.id','=','pengeluaran.kantor')
                        ->where(function($q) use ($request) {
                            if($request->user_insert != ''){ $q->where('pengeluaran.user_input', $request->user_insert); }
                        })
                        ->where(function($q) use ($r_prog) {
                            if($r_prog != ''){ $q->where('pengeluaran.program', $r_prog); }
                        }) 
                        ->where($jenisPM)
                        ->where($PJ)
                        ->where($status)
                        // ->where($mohon)
                        ->where($filAsnaf)
                        ->where($advKantor)
                        ->where($jenisTransaksi)
                        ->where(function($query) use ($request){
                            if ($request->cartext != '' && !empty($request->cartext)) {
                                $searchTerm = '%' . $request->cartext . '%';
                                $query->where('pengeluaran.id', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.id_pm', 'LIKE', $searchTerm)
                                      ->orWhere('pmnf.penerima_manfaat', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.jenis_transaksi', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.nominal', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.tgl_mohon', 'LIKE', $searchTerm)
                                      ->orWhere('pengeluaran.tgl_salur', 'LIKE', $searchTerm)
                                      ->orWhere('kantor.unit', 'LIKE', $searchTerm)
                                      ;
                            }
                        })
                        // ->havingRaw($selhave)
                        ->orderBy('pengeluaran.created_at', 'desc')->get();
                
                    $result = [];
                    foreach($data as $i => $item){
                        $result[] =[
                            'IDSalur' => $item->id ?? null,
                            'IDPM' => $item->id_pm ?? null,
                            'penerimaManfaat' => $item->pm ?? null,
                            'program' => $item->jenis_transaksi ?? null,
                            'nominal' => number_format($item->nominal, 0, ',', '.') ?? null,
                            'tglMohon' => date('d-m-Y', strtotime($item->tgl_mohon)) ?? null,
                            'tglSalur' => date('d-m-Y', strtotime($item->tgl_salur)) ?? null,
                            'kantorSalur' => Kantor::select('unit')->where('id', $item->kantor)->first()->unit ?? null,
                            ];
                        
                    }
                     return view('ekspor.penyaluranexport',[
                        'data' => $result,
                        'priode' => $prdss,
                        'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()->name
                    ]);
        }
    
}
