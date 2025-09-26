<?php

namespace App\Exports;

use Auth;
use DB;

use App\Models\Program;
use App\Models\COA;
use App\Models\Prog;
use App\Models\SumberDana;
use App\Models\Progpenyaluran;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class ProgramExport implements FromView
{
    public function __construct( $request )
    {
    
        $this->request = $request;
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
            ];
        }
        
        $filRay = array_filter($inArray, function ($p) use ($request) {
            // $grup = explode(",",$p['grup']);
            $filspc = $request->spesial == '' ? $p['spc'] != 'haha' : $p['spc'] == $request->spesial;
            $filakt = $request->aktif == '' ? $p['aktif'] != 'haha' : $p['aktif'] == $request->aktif;
            $filprnt = $request->parent == '' ? $p['parent'] != 'haha' : $p['parent'] == $request->parent;
            // $filjns = $request->jenis == '' ? $p['jp'] != 'haha' : $p['jp'] == $request->jenis;
            if($request->jenis != '' || $request->jenis != null){
                $filjns = $p['jp'] == $request->jenis;
            }else{
                $filjns = $p['jp'] == 0 || $p['jp'] == 1 || $p['jp'] == 2;
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
        
        return view('ekspor.program_penerimaan_export',[
                    'data' => $inArray,
                    // 'berdasarkan' => $request->jdl,
                    'kompani' => DB::table('company')->where('id_com',Auth::user()->id_com)->first()->name
        ]);
    }
    
  
}


