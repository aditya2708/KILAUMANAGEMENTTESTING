<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\COA;
use App\Models\Transaksi;
use App\Models\Prog;
use App\Models\SumberDana;
use Auth;
use DataTables;
use DB;
use App\Models\Progpenyaluran;
use Illuminate\Support\Facades\Http;

use Excel;
use App\Exports\ProgPenyaluranExport;
use App\Exports\ProgramExport;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        // $prog = Program::orderBy('created_at','desc')->get();
        if ($request->ajax()) {
            $data = DB::table('program')->where('id_com',Auth::user()->id_com);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('prioritass', function ($data) {
                    if ($data->prioritas == 1) {
                        $wow = '<label class="label label-success">Prioritas</label>';
                    } else {
                        $wow = '<label class="label label-danger">Non-Prioritas</label>';
                    }
                    return $wow;
                })

                ->addColumn('edit', function ($data) {
                    $button = '<a href="javascript:void(0)" class="btn btn-warning btn-sm edd" data-bs-toggle="modal" data-bs-target="#exampleModal" id="' . $data->id . '"><i class="fa fa-edit"></i></a>';
                    return $button;
                })

                ->addColumn('hapus', function ($data) {
                    $button = ' <button type="submit" id="' . $data->id . '"  class="btn btn-danger btn-sm happ"><i class="fa fa-trash"></button>';
                    return $button;
                })
                ->rawColumns(['hapus', 'edit', 'prioritass'])
                ->toJson();
        }
        return view('program.index');
    }

    public function create()
    {
        return view('program.create');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $input['id_com'] = Auth::user()->id_com;
        $request->validate([
            'program' => 'required|string|max:40',

        ]);
        \LogActivity::addToLog(Auth::user()->name . ' Menambahkan Data Program ' . $request->program);
        Program::create($input);
        // return back();
        return response()->json(['success' => 'Data is successfully updated']);
    }

    //     public function edit($id)

    //     {
    //         $trayek = Kolektor::findOrFail($id);
    //         return view('trayek.edit',compact('trayek'));
    //     }

    public function update(Request $request)
    {
        $id = $request->hidden_id;
        // $program = Program::findOrFail($id);

        $input = [
            'program' => $request->program,
            'subprogram' => $request->subprogram,
            'keterangan' => $request->keterangan,
            'prioritas' => $request->prioritas,
        ];
        \LogActivity::addToLog(Auth::user()->name . ' Mengubah Data Program ' . $request->program);
        $gg = Program::where('id', $id)->update($input);

        // $program->update($input);
        // return back();
        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function getprog($id)
    {
        if (request()->ajax()) {
            $data = Program::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function destroy($id)
    {
        $program = Program::findOrFail($id);
        \LogActivity::addToLog(Auth::user()->name . ' Menghapus Data Program ' . $program->program);
        $program->delete();
        //   return back();
        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function makeParentChildRelations(&$inArray, &$outArray, $currentParentId = 0)
    {
        if (!is_array($inArray)) {
            return;
        }

        if (!is_array($outArray)) {
            return;
        }

        foreach ($inArray as $key => $tuple) {
            if ($tuple['id_program_parent'] == $currentParentId) {
                $tuple['children'] = array();
                $this->makeParentChildRelations($inArray, $tuple['children'], $tuple['id_program']);
                $outArray[] = $tuple;
            }
        }
    }

    public function getProgram(Request $request)
    {
        // $response = Http::get('https://berbagibahagia.org/api/getcat')['data'];
        $id_com = $request->com;
        $response = null;
        // return($response);
        $prog_parent = Prog::where('parent', 'y')->where('id_com', Auth::user()->id_com)->get();
        $sum_dana = SumberDana::where('id_com',Auth::user()->id_com)->get();
        $coa = COA::where('parent', 'n')->where('id_com', Auth::user()->id_com)->get();

        if ($request->ajax()) {
            if ($request->tab == "tab1") {
                $data = SumberDana::where(function($query) use ($id_com){
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
                return DataTables::of($data)
                    ->addIndexColumn()

                    ->addColumn('kelola', function ($data) {
                        $button = '<button id="' . $data->id_sumber_dana . '"  class="btn btn-warning btn-sm edit" data-bs-toggle="modal" data-bs-target="#modal-default1"><i class="fa fa-edit"></i></button> <button id="' . $data->id_sumber_dana . '"  class="btn btn-danger btn-sm happ"><i class="fa fa-trash"></button>';

                        return $button;
                    })
                    ->rawColumns(['kelola'])
                    ->toJson();
            }

            if ($request->tab == "tab2") {
                if ($request->jenis_pem != '') {
                    $data = Prog::whereRaw("jp = '$request->jenis_pem'")->where('id_com', Auth::user()->id_com)->get();
                } else {
                    $data = Prog::where('id_com', Auth::user()->id_com)->get();
                }

                $inArray = [];
                foreach ($data as $val) {
                    $dot = Prog::whereRaw("id_program = $val->id_program_parent")->where('id_com', Auth::user()->id_com)->first();
                    $sum = SumberDana::where('id_sumber_dana', $val->id_sumber_dana)->where('id_com', Auth::user()->id_com)->first();
                    $coa_inv = COA::where('id', $val->coa_individu)->first();
                    $coa_ent = COA::where('id', $val->coa_entitas)->first();
                    $pndp = COA::where('id', $val->coa1)->first();
                    $pngdp = COA::where('id', $val->coa2)->first();
                    if (empty($sum)) {
                        $aw = '-';
                    } else {
                        $aw = $sum->sumber_dana;
                    }

                    $inArray[] = [
                        'id_program' => $val->id_program,
                        'id_program_parent' => $val->id_program_parent,
                        'program' => ($val->spc == 'y' ? "<span style='font-size: 20px'>&#9829;</span>" : "") . ($val->parent == 'y' ? "<b>" . $val->program . "</b>" : $val->program),
                        // 'program_parent' => $val->id_progam_parent != 0 ? $dot->program : 0,
                        'program_parent' => $val->id_progam_parent,
                        'jenis' => $val->jenis != "r" ? "Non Cash" : "Cash",
                        'bagian' => $val->dp,
                        'coa_individu' => $val->coa_individu,
                        'coa_entitas' => $val->coa_entitas,
                        // 'coa_individu' => $val->coa_individu == null ? '' : $coa_inv->coa,
                        // 'coa_entitas' => $val->coa_entitas == null ? '' :$coa_ent->coa,
                        // 'coa1' => $val->coa1 == null ? '' :$pndp->coa,
                        // 'coa2' => $val->coa2 == null ? '' :$pngdp->coa,
                        'parent' => $val->parent,
                        'coa1' => $val->coa1,
                        'coa2' => $val->coa2,
                        'aktif' => $val->aktif,
                        'sumber_dana' => $aw
                    ];
                }

                // dd($inArray);


                $outArray = array();
                $this->makeParentChildRelations($inArray, $outArray);
                // dd($outArray);
                // print_r($outArray);
                // return response()->json(['data' => $outArray]);
                $data = Prog::all();
                return $data;
            }

            if ($request->tab == "tab3") {
                $data = Progpenyaluran::where('id_com', Auth::user()->id_com)->get();
                // dd($data);
                $inArray = [];
                foreach ($data as $val) {
                    $dot = Prog::where('id_program', $val->id_program_parent)->first();
                    $sum = SumberDana::where('id_sumber_dana', $val->id_sumber_dana)->first();
                    $coa_inv = COA::where('id', $val->coa_individu)->first();
                    $coa_ent = COA::where('id', $val->coa_entitas)->first();
                    $pndp = COA::where('id', $val->coa1)->first();
                    $pngdp = COA::where('id', $val->coa2)->first();
                    $inArray[] = [
                        'id_program' => $val->id_program,
                        'id_program_parent' => $val->id_program_parent,
                        'program' => ($val->spc == 'y' ? "<span style='font-size: 20px'>&#9829;</span>" : "") . ($val->parent == 'y' ? "<b>" . $val->program . "</b>" : $val->program),
                        'program_parent' => $val->id_progam_parent,
                        'jenis' => $val->jenis != "r" ? "Non Cash" : "Cash",
                        'bagian' => $val->dp,
                        'coa_individu' => $val->coa_individu,
                        'coa_entitas' => $val->coa_entitas,
                        'coa1' => $val->coa1,
                        'coa2' => $val->coa2,
                        'aktif' => $val->aktif,
                        'sumber_dana' => $sum->sumber_dana
                    ];
                }

                $outArray = array();
                $this->makeParentChildRelations($inArray, $outArray);
                // dd($outArray);
                // print_r($outArray);
                return response()->json(['data' => $outArray]);
            }
        }
        return view('program.indexs', compact('sum_dana', 'prog_parent', 'coa','response'));
    }
    
    public function getcamp(Request $request, $id)
    {
        // $response = Http::get('https://berbagibahagia.org/api/getcampcat/'.$id)['data'];
        $response = null;
        return $response;
    }

    public function program_penerimaan(Request $request)
    {
        $data = Prog::where('id_com', Auth::user()->id_com)->get();
        
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
            
            $filspc = $request->spesial == '' ? $p['spc'] != 'haha' : $p['spc'] == $request->spesial;
            $filakt = $request->aktif == '' ? $p['aktif'] != 'haha' : $p['aktif'] == $request->aktif;
            $filprnt = $request->parent == '' ? $p['parent'] != 'haha' : $p['parent'] == $request->parent;
            if($request->jenis != '' || $request->jenis != null){
                $filjns = $p['jp'] == $request->jenis;
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
        
        return $inArray;
    }
    
    public function ekspor_program_penerimaan(Request $request){
        $response =  Excel::download(new ProgramExport($request), 'data-program-penerimaan.xlsx');
        ob_end_clean();
        
        return $response;
    }
    
    // Penyaluran

    public function program_penyaluran(Request $request)
    {
        
            if($request->tombol != '' || !empty($request->tombol)){
                if($request->tombol == "xls"){
                    $response =  Excel::download(new ProgPenyaluranExport($request), 'data-program-penyaluran.xlsx');
                    ob_end_clean();
                }else{
                    $response =  Excel::download(new ProgPenyaluranExport($request), 'data-program-penyaluran.csv');
                    ob_end_clean();
                }
                return $response;
            }
        
        
        
        $data = Progpenyaluran::where('id_com', Auth::user()->id_com)->get();
        
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
        
        return response()->json($inArray);
    }
    public function add_program_penyaluran(Request $request)
    {
        // dd($request);
        $input = $request->all();
        $input['id_program_parent'] = $request->id_program_pp;
        $input['level'] = $request->level_penyaluran;
        $input['program'] = $request->nama_program;
        $input['dp'] = 0.0;
        $input['aktif'] = $request->aktif_penyaluran;
        // $input['id_program_parent'] = $request->id_program_parent_penyaluran;
        $input['id_sumber_dana'] = $request->id_sumber_dana_penyaluran;
        $input['coa_individu'] = $request->coa_penyaluran;
        $input['parent'] = $request->parent_penyaluran;
        $input['coa1'] = $request->coa_penerimaan;
        $input['id_com'] = Auth::user()->id_com;
        // dd($input);
        // dd($request);
        Progpenyaluran::create($input);
        return response()->json(['success' => 'Data is successfully added']);
    }
    
    public function edit_program_penyaluran($id)
    {
        if (request()->ajax()) {
            $data = Progpenyaluran::findOrFail($id);


            return response()->json(['result' => $data]);
        }
    }
    
    public function sumdit($id){
        $data = SumberDana::findOrFail($id);
        return response()->json(['result' => $data]);
    }

    public function update_program_penyaluran(Request $request)
    {
        // $form_data = $request->all();
        // dd($request);
        $form_data = array(
            'program' => $request->nama_program,
            'id_program_parent' => $request->id_program_pp,
            'coa_individu' => $request->coa_penyaluran,
            // 'coa_entitas' => $request->coa_entitas,
            'coa1' => $request->coa_penerimaan,
            'id_sumber_dana' => $request->id_sumber_dana_penyaluran,
            // 'dp' => $request->dp,
            'parent' => $request->parent_penyaluran,
            'level' => $request->level_penyaluran,
            // 'spc' => $request->spc,
            'aktif' => $request->aktif_penyaluran,
        );
        Progpenyaluran::where('id_program', '=', $request->hidden_idprog_penyaluran)->update($form_data);
        // // \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Kantor '.$request->unit);
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function destroy_program_penyaluran($id)
    {
        $program = Progpenyaluran::findOrFail($id);
        // \LogActivity::addToLog(Auth::user()->name.' Menghapus Data Kantor '.$kantor->unit);
        $program->delete();
        // return back();
         return response()->json(['success' => 'Data is successfully deleted']);
    }
    
     // END Penyaluran
    
    public function add_program(Request $request)
    {
        // return($request);
        $input = $request->all();

        // $coa_inv = COA::where('id', $request->coa_individu)->first();
        // $coa_ent = COA::where('id', $request->coa_entitas)->first();

        // $pndp = COA::where('id', $request->coa1)->first();
        // $pngdp = COA::where('id', $request->coa2)->first();
        
        $progpar = COA::where('id', $request->id_program_parent)->first();
        $sdana = SumberDana::where('id_sumber_dana', $request->id_sumber_dana)->first();
        
        $pp = $request->ket_ada != '' ? $request->ket_ada : 0;

        $input['coa_individu'] = $request->coa_individu;
        $input['coa_entitas'] = $request->coa_entitas;
        $input['id_catcamp'] = $request->camps;
        $input['coa1'] = $request->coa1;
        $input['coa2'] = $request->coa2;
        $input['sumber_dana'] = $sdana->sumber_dana;
        $input['program_parent'] = $progpar->nama_coa;
        $input['jp'] = $request->jp;
        $input['ket'] = $pp;
        $input['prenoncash'] = $request->pnc;
        $input['id_com'] = Auth::user()->id_com;
        // dd($input);
        Prog::create($input);
        return response()->json(['success' => 'Data is successfully added']);
    }

    public function edit_program($id)
    {
        if (request()->ajax()) {
            $data = Prog::findOrFail($id);

            // $honor = $data->honpo == '' ? [0] : unserialize($data->honpo);
            // $bonpoin = $data->bonpo == '' ? [0] : unserialize($data->bonpo);
            // $bomset = $data->bomset == '' ? [0] : unserialize($data->bomset);

            return response()->json(['result' => $data]);
        }
    }

    public function update_program(Request $request)
    {
        // $form_data = $request->all();
        // dd($form_data);
        $form_data = array(
            'program' => $request->program,
            'coa_individu' => $request->coa_individu,
            'id_program_parent' => $request->id_program_parent,
            'coa_entitas' => $request->coa_entitas,
            'id_sumber_dana' => $request->id_sumber_dana,
            'dp' => $request->dp,
            'parent' => $request->parent,
            'level' => $request->level,
            'spc' => $request->spc,
            'aktif' => $request->aktif,
            'jp' => $request->jp,
            'ket' => $request->ket_ada,
            'prenoncash' => $request->pnc,
            'id_catcamp' => $request->camps
        );
        Prog::where('id_program', '=', $request->hidden_idprog)->update($form_data);
        // // \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Kantor '.$request->unit);
        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function destroy_program($id)
    {
        $program = Prog::findOrFail($id);
        // \LogActivity::addToLog(Auth::user()->name.' Menghapus Data Kantor '.$kantor->unit);
        $program->delete();
        // return back();
    }

    public function add_sumberdana(Request $request)
    {
        $input = $request->all();
        $input['id_com'] = Auth::user()->id_com;
        return $input;
        SumberDana::create($input);
        return response()->json(['success' => 'Data is successfully added']);
    }

    public function edit_sumberdana($id)
    {
        if (request()->ajax()) {
            $data = SumberDana::findOrFail($id);

            return response()->json(['result' => $data]);
        }
    }

    public function update_sumberdana(Request $request)
    {
        // $form_data = $request->all();
        // dd($form_data);
        $form_data = array(
            'sumber_dana' => $request->sumber_dana,
            'active' => $request->active
        );
        SumberDana::where('id_sumber_dana', '=', $request->hidden_id)->update($form_data);
        // // \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Kantor '.$request->unit);
        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function get_sumberdana()
    {
        $data = SumberDana::where('id_com', Auth::user()->id_com)->get();
        return response()->json($data);
    }

    public function getid_program($id)
    {
        $p = Prog::where('id_sumber_dana', $id)->where('parent', 'n')->where('id_com', Auth::user()->id_com);
        if (count($p->get()) > 0) {
            $data = $p->get();
        } else {
            $data = [];
        }
        return response()->json($data);
    }

    public function getprogramparent(Request $request)
    {
        
        $prog = Prog::where('id_com', Auth::user()->id_com)->get();
        $thn = date('Y');
        if($request->tab == 'lain'){
            $hawa = Prog::whereRaw("id_program = 82 OR id_program = 83")->get();
            
            foreach ($hawa as $key => $val) {
                $sd = SumberDana::where('id_sumber_dana', $val->id_sumber_dana)->first();
                if (empty($sd)) {
                    $aw = '-';
                } else {
                    $aw =  $sd->sumber_dana;
                }
    
                $h3[] = [
                    "text" => $val->parent . "-" . $val->level . " " . $val->program,
                    "program" => $val->program,
                    "id" => $val->id_program,
                    "parent" => $val->parent,
                    "sumberdana" => $aw,
                    'coa' => $val->coa_individu
                ];
            }
            
            
            return response()->json($h3);
        }
        
        foreach ($prog as $key => $val) {
            $sd = SumberDana::where('id_sumber_dana', $val->id_sumber_dana)->first();
            if (empty($sd)) {
                $aw = '-';
            } else {
                $aw =  $sd->sumber_dana;
            }

            $h1[] = [
                "text" => $val->parent . "-" . $val->level . " " . $val->program,
                "program" => $val->program,
                "id" => $val->id_program,
                "parent" => $val->parent,
                "sumberdana" => $aw,
                'coa' => $val->coa_individu
            ];
        }
        return response()->json($h1);
    }

    public function getprogramparentsalur()
    {
        $prog = Progpenyaluran::where('id_com', Auth::user()->id_com)->get();
        foreach ($prog as $key => $val) {
            $sd = SumberDana::where('id_sumber_dana', $val->id_sumber_dana)->first();
            $h1[] = [
                "text" => $val->parent . "-" . $val->level . '' . $val->program,
                "program" => $val->program,
                "id" => $val->id_program,
                "parent" => $val->parent,
                "sumberdana" => $sd->sumber_dana,
                "coa" => '-'
            ];
        }
        return response()->json($h1);
    }

    public function destroy_sumberdana($id)
    {
        $sumberdana = SumberDana::findOrFail($id);
        // \LogActivity::addToLog(Auth::user()->name.' Menghapus Data Kantor '.$kantor->unit);
        $sumberdana->delete();
        // return back();
    }

  

    

    public function getProgs($id)
    {
        if (request()->ajax()) {
            $data['all'] = Prog::findOrFail($id);

            $data['honor'] = $data['all']->inhonpo === 'b:0;' || @unserialize($data['all']->inhonpo) !== false ? unserialize($data['all']->inhonpo) : [];
            $data['bonpoin'] = $data['all']->inbonpo === 'b:0;' || @unserialize($data['all']->inbonpo) !== false ? unserialize($data['all']->inbonpo) : [];
            $data['bomset'] = $data['all']->inbonset === 'b:0;' || @unserialize($data['all']->inbonset) !== false ? unserialize($data['all']->inbonset) : [];

            $data['inp_honor'] = $data['all']->honpo === 'b:0;' || @unserialize($data['all']->honpo) !== false ? unserialize($data['all']->honpo) : [];
            $data['inp_bonpoin'] = $data['all']->bonpo === 'b:0;' || @unserialize($data['all']->bonpo) !== false ? unserialize($data['all']->bonpo) : [];
            $data['inp_bomset'] = $data['all']->bonset === 'b:0;' || @unserialize($data['all']->bonset) !== false ? unserialize($data['all']->bonset) : [];

            $data['range2'] = $data['all']->minpo2 === 'b:0;' || @unserialize($data['all']->minpo2) !== false ? unserialize($data['all']->minpo2) : [];
            $data['range3'] = $data['all']->bonset2 === 'b:0;' || @unserialize($data['all']->bonset2) !== false ? unserialize($data['all']->bonset2) : [];
            return response()->json(['result' => $data]);
        }
    }

    public function set_bon(Request $request)
    {
        // dd($request->arr['honor']);
        $honor = [];
        $inp_honor = [];
        $inp_honor_lipat = [];
        $bonpoin = [];
        $inp_bonpoin = [];
        $inp_bonpoin_lipat = [];
        $bomset = [];
        $inp_bomset = [];
        $inp_bomset_presentase = [];
        $range_bomset2 = [];
        $range_bomset3 = [];


        foreach ($request->honor as $i => $val) {
            // dd($request->inp_honor ,$i, $val);
            if ($val == 1) {
                $inp_honor[$i] = preg_replace("/[^0-9]/", "", $request->inp_honor[$i]);
            } else if ($val == 2) {
                $inp_honor[$i] = preg_replace("/[^0-9]/", "", $request->inp_honor_lipat[$i]);
            } else {
                $inp_honor[$i] = 0;
            }
        }

        foreach ($request->bonpoin as $i => $val) {
            if ($val == 1) {
                $inp_bonpoin[$i] = preg_replace("/[^0-9]/", "", $request->inp_bonpoin[$i]);
            } else if ($val == 2) {
                $inp_bonpoin[$i] = preg_replace("/[^0-9]/", "", $request->inp_bonpoin_lipat[$i]);
            } else {
                $inp_bonpoin[$i] = 0;
            }
        }

        foreach ($request->bomset as $i => $val) {
            if ($val == 1) {
                $inp_bomset[$i] = preg_replace("/[^0-9]/", "", $request->inp_bomset[$i]);
                $range_bomset2[$i] = preg_replace("/[^0-9]/", "", $request->range_bomset2[$i]);
                $range_bomset3[$i] = preg_replace("/[^0-9]/", "", $request->range_bomset3[$i]);
            } else if ($val == 2) {
                $inp_bomset[$i] = $request->inp_bomset_presentase[$i];
                $range_bomset2[$i] = preg_replace("/[^0-9]/", "", $request->range_bomset2[$i]);
                $range_bomset3[$i] = preg_replace("/[^0-9]/", "", $request->range_bomset3[$i]);
            } else if ($val == 3) {
                $inp_bomset[$i] = preg_replace("/[^0-9]/", "", $request->inp_bomset[$i]);
                $range_bomset2[$i] = preg_replace("/[^0-9]/", "", $request->range_bomset2[$i]);
                $range_bomset3[$i] = preg_replace("/[^0-9]/", "", $request->range_bomset3[$i]);
            } else {
                $inp_bomset[$i] = 0;
                $range_bomset2[$i] = 0;
                $range_bomset3[$i] = 0;
            }
        }
        // dd($request->inp_honor, $inp_honor);

        // foreach($request->arr as $val){
        //     dd($val);
        //     $honor[] = $val['honor'];
        //     $inp_honor[] = $val['inp_honor'];
        //     $inp_honor_lipat[] = $val['inp_honor_lipat'];
        //     $bonpoin[] = $val['bonpoin'];
        //     $inp_bonpoin[] = $val['inp_bonpoin'];
        //     $inp_bonpoin_lipat[] = $val['inp_bonpoin_lipat'];
        //     $bomset[] = $val['bomset'];
        //     $inp_bomset[] = $val['inp_bomset'];
        //     $inp_bomset_presentase[] = $val['inp_bomset_presentase'];
        //     $range_bomset1[] = $val['range_bomset1'];
        //     $range_bomset2[] = $val['range_bomset2'];
        //     $range_bomset3[] = $val['range_bomset3'];

        // }
        // dd($honor[0]);
        // error_log('Some message here.');
        $data = Prog::find($request->id_nih);
        $data->honpo = serialize($inp_honor);
        $data->bonpo = serialize($inp_bonpoin);
        $data->inhonpo = serialize($request->honor);
        $data->inbonpo = serialize($request->bonpoin);
        $data->inbonset = serialize($request->bomset);
        $data->bonset = serialize($inp_bomset);
        $data->minpo2 = serialize($range_bomset2);
        $data->bonset2 = serialize($range_bomset3);
        $data->minpo = $request->omsetmin;

        $data->update();

        return response()->json(['success' => 'Data is successfully added', 'data' => $data]);
    }
}
