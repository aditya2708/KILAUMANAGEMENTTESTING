<?php

namespace App\Exports;

use Auth;
use App\Models\Prog;
use DB;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class ProgPenyaluranExport implements FromView
{
      use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     
    public function __construct($request)
    {
        
        $this->request = $request ;
        return $this;
    }

    public function view(): View
    {
        
        $request = $this->request;
        $data = Prog::all();
        foreach($data as $key => $val){
            
            $inArray[] = [
                "id_program" => $val->id_program,
                "coa" => $val->coa,
                "program" => $val->program,
                "id_program_parent" => $val->id_program_parent,
                "coa1" => $val->coa1,
                "coa2" => $val->coa2,
                "sumber_dana" => $val->sumber_dana,
                "dp" => $val->dp,
                "coa_individu" => $val->coa_individu,
                "coa_entitas" => $val->coa_entitas,
                "aktif" => $val->aktif,
                "parent" => $val->parent,
                "spc" => $val->spc,
                "jp" => $val->jp,
                "valid" => $val->valid,
            ];
        }
        
        $filRay = array_filter($inArray, function ($p) use ($request) {
            
            $filspc = $request->validPenyaluran == '' ? $p['valid'] != 'haha' : $p['valid'] == $request->validPenyaluran;
            $filakt = $request->aktifPenyaluran == '' ? $p['aktif'] != 'haha' : $p['aktif'] == $request->aktifPenyaluran;
            $filprnt = $request->parentPenyaluran == '' ? $p['parent'] != 'haha' : $p['parent'] == $request->parentPenyaluran;
            if($request->jenisPenyaluran != '' || !empty($request->jenisPenyaluran)){
                $filjns = $p['jp'] == $request->jenisPenyaluran;
            }else{
                $filjns = $p['jp'] == 0 || $p['jp'] == 1 || $p['jp'] == 2;
                // $filjns = $p['jp'] != 'hahaha';
            }
            
            return $filspc && $filakt && $filprnt && $filjns;
        });
           
        $inArray = array_values($filRay);
        
        
        
        $arid = array_column($inArray, 'id_program');
        
    
        foreach ($inArray as $key => $obj) {
            if (!in_array($obj['id_program_parent'], $arid)) {
                $inArray[$key]['id_program_parent'] = '';
            }
        }
        
        return view('ekspor.progpenyaluranexport',[
            'data' => $inArray,
            'company' => DB::table('company')->selectRaw('name')->where('id_com', 1)->first()
        ]);
            
    }
}