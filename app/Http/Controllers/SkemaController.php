<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SkemaGaji;
use App\Models\KomponenGaji;
use App\Models\KomponenGj;
use DataTables;
use Auth;

class SkemaController extends Controller{
    public function index (Request $req) 
    {
        $com = $req->com == '' ? Auth::user()->id_com : $req->com ;
        $kom =  KomponenGj::where('id_com', $com)->get();
        
        if($req->ajax()){
            $id_com = $req->com == '' ? Auth::user()->id_com : $req->com ;
            $skema = $req->skema == '' ? "id_skema IS NOT NULL" : "id_skema = '$req->skema'" ;
            $ada = KomponenGj::selectRaw("komponen_gj.*, komponen_gj.nama as komponen")->get();
            $data = [];
            
            foreach($ada as $a){
                $aya = KomponenGaji::whereRaw("id_com = '$id_com' AND id_komponen = '$a->id' AND $skema AND aktif = 1");
                
                $hai = $aya->pluck('id_skema')->toArray();
                
                $data[] = [
                    'id' => count($aya->get()) > 0 ? $aya->first()->id : 0,
                    'id_komponen' => $a->id,
                    'komponen' => $a->nama,
                    'grup' => $a->grup == 'bpjs' ? strtoupper($a->grup) : ucfirst($a->grup),
                    'skema' => count($aya->get()) > 0 ? $hai : 0,
                    'modal' => $a->modal,
                    'aktif' => count($aya->get()) > 0 ? $aya->first()->aktif : 0,
                    'bisa_edit' => count($aya->get()) > 0 ? $aya->first()->bisa_edit : 0,
                    'id_skema'  => count($aya->get()) > 0 ? $aya->first()->id_skema : 0,
                    'urutan' => $a->urutan
                ];
            }
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('skemas', function($data){
                    if($data['skema'] == 0){
                        $y = '<label class="badge badge-xss badge-warning">Kosong</label>';
                    }else{
                        
                        $y = [];
                        foreach($data['skema'] as $x){
                            $y[] = ' Skema '.$x; 
                        }
                    }
                    
                    
                    return $y;
                })
                ->addColumn('action', function($data){
                    $button = '<label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'), this.getAttribute(\'data-komponen\'))" id="checkbox" class="toggle-class" data-komponen = "'.$data['id_komponen'].'"   data-id="'. $data['id'] . '"  data-value="'. $data['aktif'] . '" type="checkbox" '.($data['aktif'] == 1 ? "checked" : "").' /> <div class="slider round"> </div> </label>';
                    return $button;
                })
                
                ->addColumn('edits', function($data){
                    $button = '<label class="switch"> <input onchange="change_status_bisa(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'), this.getAttribute(\'data-komponen\'))" id="checkboex" class="toggle-class" data-komponen = "'.$data['aktif'].'"   data-id="'. $data['id'] . '"  data-value="'. $data['bisa_edit'] . '" type="checkbox" '.($data['bisa_edit'] == 1 ? "checked" : "").' /> <div class="slider round"> </div> </label>';
                    return $button;
                })
                
                ->addColumn('aksi', function($data){
                    // $button = '<a href="javascript:void(0)" class="text-blue capcup"  data-modal="'.$data['modal'].'">'.$data['komponen'].'</a>';
                    $button = $data['komponen'];
                    return $button;
                })
                
                ->addColumn('up', function($data){
                    $button = '<div class="button-group"><a href="javascript:void(0)" class="btn btn-sm btn-success tombol" id="tambah" data-id="'.$data['id_komponen'].'" style="margin-right: 10px"><i class="fa fa-arrow-up"></i></a>';
                    $button .= '<a href="javascript:void(0)" class="btn btn-sm btn-danger tombol" id="kurang" data-id="'.$data['id_komponen'].'"><i class="fa fa-arrow-down"></i></a></div>';
                    return $button;
                })
                
                ->rawColumns(['action','skemas', 'edits', 'aksi', 'up'])
                ->make(true);
        }
        
        return view('skema.index', compact('kom'));
    }
    
    public function getSkema(Request $req){
        $id_com = $req->id_com == '' ? Auth::user()->id_com : $req->id_com ;
        $data = SkemaGaji::selectRaw(" '' as aksi, nama as skema, id, '' as id_kantor")->whereRaw("id_com = '$id_com'")->get();
        return $data;
    }
    
    public function add(Request $req){
        foreach($req->arr as $val){
            
            if($val['aksi'] == 'add'){
            
                $skema = $val['skema'];
                $id_com = Auth::user()->id_com;
                $id_kan = Auth::user()->id_kantor;
                $aktif = 1;
                
                $input['nama'] = $skema;
                $input['aktif'] = $aktif;
                $input['id_com'] = $id_com;
                SkemaGaji::create($input);
            }else{
                $id = $val['id'];
                $skema = $val['skema'];
                
                $input['nama'] = $skema;
                SkemaGaji::where('id', $id)->update($input);
            }
        }
        
        return response()->json(['success' => 'Data Added successfully.', 'response'=> 1]);
    }
    
    public function getKom(Request $req){
        $id_com = $req->id_com == '' ? Auth::user()->id_com : $req->id_com ;
        $data = KomponenGj::selectRaw(" '' as aksi, nama as nama, id, '' as id_kantor, grup, '' as text_grup")->whereRaw("id_com = '$id_com'")->get();
        return $data;
    }
    
    public function add_kom(Request $req){
        // return($req);
        foreach($req->arr as $val){
            
            if($val['aksi'] == 'add'){
            
                $nama = $val['nama'];
                $id_com = Auth::user()->id_com;
                $id_kan = Auth::user()->id_kantor;
                $grup = $val['grup'];
                $aktif = 1;
                $modal = str_replace(' ', '', strtolower($nama));
                
                $input['nama'] = $nama;
                $input['aktif'] = $aktif;
                $input['id_com'] = $id_com;
                $input['modal'] = $modal;
                $input['grup'] = $grup;
                KomponenGj::create($input);
            }
        }
        
        return response()->json(['success' => 'Data Added successfully.', 'response'=> 1]);
    }
    
    public function setKomponen(Request $req){
        if($req->id == 0){
            
            $input['id_skema'] = $req->skema;
            $input['id_komponen'] = $req->komponen;
            $input['aktif'] = $req->aktif;
            $input['bisa_edit'] = 0;
            $input['id_com'] = Auth::user()->id_com;
            KomponenGaji::create($input);
        }else{
            $input['aktif'] = $req->aktif;
            KomponenGaji::where('id', $req->id)->update($input);
        }
        
        return response()->json(['success' => 'Update Berhasil']);
    }
    
    public function setEdit(Request $req){
        $input['bisa_edit'] = $req->aktif;
        KomponenGaji::where('id', $req->id)->update($input);
        
        return response()->json(['success' => 'Update Berhasil']);
    }
    
    public function ubahPosisi(Request $req){
        $aq = $req->kondis;
        // $val = $aq == 'tambah' ? +1 : -1;
        
        if($aq == 'tambah'){
            $item = KomponenGj::find($req->id);
            $itemBelow = KomponenGj::where('urutan', '<', $item->urutan)->orderBy('urutan', 'desc')->first();
            
            
            if ($itemBelow) {
                $newOrder = $itemBelow->urutan;
                $itemBelow->urutan = $item->urutan;
                $itemBelow->save();
        
                $item->urutan = $newOrder;
                $item->save();
            }
        }else if($aq == 'kurang'){
            $item = KomponenGj::find($req->id);
            $itemBelow = KomponenGj::where('urutan', '>', $item->urutan)->orderBy('urutan', 'asc')->first();
            
            if ($itemBelow) {
                $newOrder = $itemBelow->urutan;
                $itemBelow->urutan = $item->urutan;
                $itemBelow->save();
        
                $item->urutan = $newOrder;
                $item->save();
            }
        }
        
        
        // return $itemBelow;
        // $input['urutan'] = $sipit;
        // KomponenGj::where('id', $req->id)->update($input);
        
        return response()->json(['success' => 'Update Berhasil']);
    }
    
    public function getkomp(Request $req){
        $arr_kom = ['tunjangananak','tunjanganpasangan'];
        $arr_ber = ['umk','gajipokok'];
        
        $skema = $req->skema;
        $id_com = $req->id_com == null ? Auth::user()->id_com : $req->id_com;
        $data = KomponenGaji::selectRaw("komponen_gaji.*, komponen_gj.id as ids, komponen_gj.nama, komponen_gj.modal")->join('komponen_gj','komponen_gj.id','=','komponen_gaji.id_komponen')->whereRaw("komponen_gaji.id_com = '$id_com' AND id_skema = '$skema' AND komponen_gaji.aktif = 1")->get();
        
        $yeah = [];
        $yeah['berdasarkan'] = [];
        $yeah['komponen'] = [];
        
        foreach($data as $d){
            if($d->modal == "tunjangananak" || $d->modal == "tunjanganpasangan"){
                $yeah['komponen'][] = $d;
            }
            
            if($d->modal == "umk" || $d->modal == "gajipokok"){
                $yeah['berdasarkan'][] = $d;
            }
        }
        
        
        return $yeah;
    }
    
    public function  postPersentase(Request $req){
        
        
        // return($req->presentase); 
        
        foreach($req->presentase as $val){
            
            if($val['aksi'] == 'hapus'){
                $skema = $val['skema'];
                $id = $val['komponen'];
                $pers = $val['persentase'];
                    
                // $input['nama'] = $skema;
                $input['id_persen'] = null;
                // $input['id_com'] = $id_com;
                KomponenGaji::where('id', $id)->update($input);
            }else{
                
                $skema = $val['skema'];
                $id = $val['komponen'];
                $pers = $val['persentase'];
                    
                // $input['nama'] = $skema;
                $input['id_persen'] = $pers;
                // $input['id_com'] = $id_com;
                KomponenGaji::where('id', $id)->update($input);
            }
            
            
        }
        
        return response()->json(['success' => 'Update Berhasil']);
    }
    
    public function getPers(Request $req){
        $skema = $req->skema;
        $id_com = $req->id_com == null ? Auth::user()->id_com : $req->id_com;
        
        $data = KomponenGaji::selectRaw("'' as aksi, '' as indeks, id_komponen as komponen, (SELECT nama FROM komponen_gj WHERE id = id_komponen) as text_komponen, id_persen as persentase, (SELECT nama FROM komponen_gj WHERE id = id_persen) as text_pres, id_skema as skema")
                                ->whereRaw("komponen_gaji.id_com = '$id_com' AND id_skema = '$skema' AND komponen_gaji.aktif = 1 AND id_persen IS NOT NULL")->get();
        
        return $data;
    }
    
}