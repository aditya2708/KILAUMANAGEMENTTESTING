<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Gapok;
use App\Models\Golongan;
use DataTables;
use Auth;

class GajipokokController extends Controller
{
    public function index (Request $request) 
    {
        if($request->ajax())
        {
            $id_com = $request->com ;
            
            // $fil_com = function($query) use ($request, $id_com){
            //     if(empty($request->com) && $request->com == null){
            //         $query->where('id_com', Auth::user()->id_com);
            //     }else{
            //         $query->whereRaw($id_com);
            //     }               
            // };
            $data = Gapok::orderBy('th', 'ASC')
                    ->where(function($query) use ($id_com){
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
                    ->addColumn('th', function($data){
                        $gol = $data->th.' Tahun';
                        return $gol;
                    })
                    ->addColumn('IA', function($data){
                        $gol = 'Rp.'.number_format($data->IA, 0, ',', '.');
                        return $gol;
                    })
                    ->addColumn('IB', function($data){
                        $gol = 'Rp.'.number_format($data->IB, 0, ',', '.');
                        return $gol;
                    })
                    ->addColumn('IC', function($data){
                        $gol = 'Rp.'.number_format($data->IC, 0, ',', '.');
                        return $gol;
                    })
                    ->addColumn('ID', function($data){
                        $gol = 'Rp.'.number_format($data->ID, 0, ',', '.');
                        return $gol;
                    })
                    ->addColumn('IIA', function($data){
                        $gol = 'Rp.'.number_format($data->IIA, 0, ',', '.');
                        return $gol;
                    })
                    ->addColumn('IIB', function($data){
                        $gol = 'Rp.'.number_format($data->IIB, 0, ',', '.');
                        return $gol;
                    })
                    ->addColumn('IIC', function($data){
                        $gol = 'Rp.'.number_format($data->IIC, 0, ',', '.');
                        return $gol;
                    })
                    ->addColumn('IID', function($data){
                        $gol = 'Rp.'.number_format($data->IID, 0, ',', '.');
                        return $gol;
                    })
                    ->addColumn('IIE', function($data){
                        $gol = 'Rp.'.number_format($data->IIE, 0, ',', '.');
                        return $gol;
                    })
                    ->addColumn('IIIA', function($data){
                        $gol = 'Rp.'.number_format($data->IIIA, 0, ',', '.');
                        return $gol;
                    })
                    ->addColumn('IIIB', function($data){
                        $gol = 'Rp.'.number_format($data->IIIB, 0, ',', '.');
                        return $gol;
                    })
                    ->addColumn('IIIC', function($data){
                        $gol = 'Rp.'.number_format($data->IIIC, 0, ',', '.');
                        return $gol;
                    })
                    ->addColumn('IIID', function($data){
                        $gol = 'Rp.'.number_format($data->IIID, 0, ',', '.');
                        return $gol;
                    })
                        // ->addColumn('IVA', function($data){
                        //     $gol = 'Rp.'.number_format($data->IVA, 0, ',', '.');
                        //     return $gol;
                        // })
                        // ->addColumn('IVB', function($data){
                        //     $gol = 'Rp.'.number_format($data->IVB, 0, ',', '.');
                        //     return $gol;
                        // })
                        // ->addColumn('IVC', function($data){
                        //     $gol = 'Rp.'.number_format($data->IVC, 0, ',', '.');
                        //     return $gol;
                        // })
                        // ->addColumn('IVD', function($data){
                        //     $gol = 'Rp.'.number_format($data->IVD, 0, ',', '.');
                        //     return $gol;
                        // })
                        // ->addColumn('IVE', function($data){
                        //     $gol = 'Rp.'.number_format($data->IVE, 0, ',', '.');
                        //     return $gol;
                        // })
                    // ->addColumn('action', function($data){
                    //     $button = ' <div class="btn-group">
                    //                     <a href="javascript:void(0)" class="btn btn-success btn-sm edit" id="'.$data->id_gapok.'" data-toggle="modal" data-target="#ModalEdit">Edit</a>
                    //                 </div>';
                    //     return $button;
                    // })
                    // ->rawColumns(['action'])
                    ->make(true);
        }
        return view ('fins.gaji_pokok');
    }
    
    public function getgapok(Request $request){
        // dd($request->com);
        if($request->com == "0"){
            $id_com = "id_com != '9990' ";
        }else{
            $id_com = "id_com = '$request->com'";
        }
        
        $fil_com = function($query) use ($request, $id_com){
            if(empty($request->com) && $request->com == null){
                $query->where('id_com', Auth::user()->id_com);
            }else{
                $query->whereRaw($id_com);
            }               
        };
        $data = Gapok::where($fil_com)->first();
        return response()->json($data);
    }
    
    public function upgapok(Request $request) {
        
        if($request->com == "0"){
                $id_com = "id_com != '9990' ";
        }else{
            $id_com = "id_com = '$request->com'";
        }
        
        $fil_com = function($query) use ($request, $id_com){
            if(empty($request->com) && $request->com == null){
                $query->where('id_com', Auth::user()->id_com);
            }else{
                $query->whereRaw($id_com);
            }               
        };
        $reqIA = $request->IA != '' ? preg_replace("/[^0-9]/", "", $request->IA) : 0;
        $reqIB = $request->IB != '' ? preg_replace("/[^0-9]/", "", $request->IB) : 0;
        $reqIC = $request->IC != '' ? preg_replace("/[^0-9]/", "", $request->IC) : 0;
        $reqID = $request->ID != '' ? preg_replace("/[^0-9]/", "", $request->ID) : 0;
        $reqIIA = $request->IIA != '' ? preg_replace("/[^0-9]/", "", $request->IIA) : 0;
        $reqIIB = $request->IIB != '' ? preg_replace("/[^0-9]/", "", $request->IIB) : 0;
        $reqIIC = $request->IIC != '' ? preg_replace("/[^0-9]/", "", $request->IIC) : 0;
        $reqIID = $request->IID != '' ? preg_replace("/[^0-9]/", "", $request->IID) : 0;
        $reqIIE = $request->IIE != '' ? preg_replace("/[^0-9]/", "", $request->IIE) : 0;
        $reqIIIA = $request->IIIA != '' ? preg_replace("/[^0-9]/", "", $request->IIIA) : 0;
        $reqIIIB = $request->IIIB != '' ? preg_replace("/[^0-9]/", "", $request->IIIB) : 0;
        $reqIIIC = $request->IIIC != '' ? preg_replace("/[^0-9]/", "", $request->IIIC) : 0;
        $reqIIID = $request->IIID != '' ? preg_replace("/[^0-9]/", "", $request->IIID) : 0;
        // $reqIVA = $request->IVA != '' ? preg_replace("/[^0-9]/", "", $request->IVA) : 0;
        // $reqIVB = $request->IVB != '' ? preg_replace("/[^0-9]/", "", $request->IVB) : 0;
        // $reqIVC = $request->IVC != '' ? preg_replace("/[^0-9]/", "", $request->IVC) : 0;
        // $reqIVD = $request->IVD != '' ? preg_replace("/[^0-9]/", "", $request->IVD) : 0;
        // $reqIVE = $request->IVE != '' ? preg_replace("/[^0-9]/", "", $request->IVE) : 0;
        
        $IA = Golongan::where('golongan','IA')->where($fil_com)->first();
        $IB = Golongan::where('golongan','IB')->where($fill_com)->first();
        $IC = Golongan::where('golongan','IC')->where($fill_com)->first();
        $ID = Golongan::where('golongan','ID')->where($fill_com)->first();
        $IIA = Golongan::where('golongan','IIA')->where($fill_com)->first();
        $IIB = Golongan::where('golongan','IIB')->where($fill_com)->first();
        $IIC = Golongan::where('golongan','IIC')->where($fill_com)->first();
        $IID = Golongan::where('golongan','IID')->where($fill_com)->first();
        $IIE = Golongan::where('golongan','IIE')->where($fill_com)->first();
        $IIIA = Golongan::where('golongan','IIIA')->where($fill_com)->first();
        $IIIB = Golongan::where('golongan','IIIB')->where($fill_com)->first();
        $IIIC = Golongan::where('golongan','IIIC')->where($fill_com)->first();
        $IIID = Golongan::where('golongan','IIID')->where($fill_com)->first();
        // $IVA = Golongan::where('golongan','IVA')->first();
        // $IVB = Golongan::where('golongan','IVB')->first();
        // $IVC = Golongan::where('golongan','IVC')->first();
        // $IVD = Golongan::where('golongan','IVD')->first();
        // $IVE = Golongan::where('golongan','IVE')->first();
        
        $count = count(Gapok::all());
        
        for($i = 0; $i < $count; $i++){
        $form_data = array(
            'IA' => $reqIA + ($IA->kenaikan * $i),
            'IB' => $reqIB + ($IB->kenaikan * $i),
            'IC' => $reqIC + ($IC->kenaikan * $i),
            'ID' => $reqID + ($ID->kenaikan * $i),
            'IIA' => $reqIIA + ($IIA->kenaikan * $i),
            'IIB' => $reqIIB + ($IIB->kenaikan * $i),
            'IIC' => $reqIIC + ($IIC->kenaikan * $i),
            'IID' => $reqIID + ($IID->kenaikan * $i),
            'IIE' => $reqIIE + ($IIE->kenaikan * $i),
            'IIIA' => $reqIIIA + ($IIIA->kenaikan * $i),
            'IIIB' => $reqIIIB + ($IIIB->kenaikan * $i),
            'IIIC' => $reqIIIC + ($IIIC->kenaikan * $i),
            'IIID' => $reqIIID + ($IIID->kenaikan * $i),
            // 'IVA' => $reqIVA + ($IVA->kenaikan * $i),
            // 'IVB' => $reqIVB + ($IVB->kenaikan * $i),
            // 'IVC' => $reqIVC + ($IVC->kenaikan * $i),
            // 'IVD' => $reqIVD + ($IVD->kenaikan * $i),
            // 'IVE' => $reqIVE + ($IVE->kenaikan * $i),
        );
        Gapok::where('th',$i)->update($form_data);
        }
        
        Gapok::where('th',0)->update(['acc_up'=>1]);
        \LogActivity::addToLog(Auth::user()->name.' Mengupdate Data Gaji Pokok');
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function intahun(Request $request){
        $count = count(Gapok::all());
        $data = Gapok::first();
         if($request->com == "0"){
                $id_com = "golongan.id_com != '9990' ";
        }else{
            $id_com = "golongan.id_com = '$request->com'";
        }
        
        $fil_com = function($query) use ($request, $id_com){
            if(empty($request->com) && $request->com == null){
                $query->where('golongan.id_com', Auth::user()->id_com);
            }else{
                $query->whereRaw($id_com);
            }               
        };
        $IA = Golongan::where('golongan','IA')->where($fil_com)->first();
        $IB = Golongan::where('golongan','IB')->where($fil_com)->first();
        $IC = Golongan::where('golongan','IC')->where($fil_com)->first();
        $ID = Golongan::where('golongan','ID')->where($fil_com)->first();
        $IIA = Golongan::where('golongan','IIA')->where($fil_com)->first();
        $IIB = Golongan::where('golongan','IIB')->where($fil_com)->first();
        $IIC = Golongan::where('golongan','IIC')->where($fil_com)->first();
        $IID = Golongan::where('golongan','IID')->where($fil_com)->first();
        $IIE = Golongan::where('golongan','IIE')->where($fil_com)->first();
        $IIIA = Golongan::where('golongan','IIIA')->where($fil_com)->first();
        $IIIB = Golongan::where('golongan','IIIB')->where($fil_com)->first();
        $IIIC = Golongan::where('golongan','IIIC')->where($fil_com)->first();
        $IIID = Golongan::where('golongan','IIID')->where($fil_com)->first();
        // $IVA = Golongan::where('golongan','IVA')->first();
        // $IVB = Golongan::where('golongan','IVB')->first();
        // $IVC = Golongan::where('golongan','IVC')->first();
        // $IVD = Golongan::where('golongan','IVD')->first();
        // $IVE = Golongan::where('golongan','IVE')->first();
        
        $input = [];
        for($i = 0; $i < $request->jumlah; $i++){
            $input['th'] = $count + $i;
            $input['IA'] = $data->IA + ($IA->kenaikan * ($count + $i));
            $input['IB'] = $data->IB + ($IB->kenaikan * ($count + $i));
            $input['IC'] = $data->IC + ($IC->kenaikan * ($count + $i));
            $input['ID'] = $data->ID + ($ID->kenaikan * ($count + $i));
            $input['IIA'] = $data->IIA + ($IIA->kenaikan * ($count + $i));
            $input['IIB'] = $data->IIB + ($IIB->kenaikan * ($count + $i));
            $input['IIC'] = $data->IIC + ($IIC->kenaikan * ($count + $i));
            $input['IID'] = $data->IID + ($IID->kenaikan * ($count + $i));
            $input['IIE'] = $data->IIE + ($IIE->kenaikan * ($count + $i));
            $input['IIIA'] = $data->IIIA + ($IIIA->kenaikan * ($count + $i));
            $input['IIIB'] = $data->IIIB + ($IIIB->kenaikan * ($count + $i));
            $input['IIIC'] = $data->IIIC + ($IIIC->kenaikan * ($count + $i));
            $input['IIID'] = $data->IIID + ($IIID->kenaikan * ($count + $i));
            // $input['IVA'] = $data->IVA + ($IVA->kenaikan * ($count + $i));
            // $input['IVB'] = $data->IVB + ($IVB->kenaikan * ($count + $i));
            // $input['IVC'] = $data->IVC + ($IVC->kenaikan * ($count + $i));
            // $input['IVD'] = $data->IVD + ($IVD->kenaikan * ($count + $i));
            // $input['IVE'] = $data->IVE + ($IVE->kenaikan * ($count + $i));
        Gapok::create($input);
        }
    
        Gapok::where('th',0)->update(['acc_up'=>1]);
        \LogActivity::addToLog(Auth::user()->name.' Menambahkan Data Gaji Pokok '.$data->golongan);
        return response()->json(['success' => 'Data Added successfully.']);
    }
    
    public function naikper(Request $request){
        $persen = $request->persen;
        $upnominal = \DB::select("UPDATE gapok SET 
                    IA = IA+($persen/100*IA),
                    IB = IB+($persen/100*IB),
                    IC = IC+($persen/100*IC),
                    ID = ID+($persen/100*ID),
                    IIA = IIA+($persen/100*IIA),
                    IIB = IIB+($persen/100*IIB),
                    IIC = IIC+($persen/100*IIC),
                    IID = IID+($persen/100*IID),
                    IIE = IIE+($persen/100*IIE),
                    IIIA = IIIA+($persen/100*IIIA),
                    IIIB = IIIB+($persen/100*IIIB),
                    IIIC = IIIC+($persen/100*IIIC),
                    IIID = IIID+($persen/100*IIID)  
                    ");

        Gapok::where('th',0)->update(['acc_up'=>0]);
        \LogActivity::addToLog(Auth::user()->name.' Menaikan Data Gaji Pokok');
        return response()->json(['success' => 'Data is successfully updated']);
    }
    
    public function turunper(Request $request){
        $persen = $request->persen;
        $upnominal = \DB::select("UPDATE gapok SET 
                    IA = IA-($persen/100*IA),
                    IB = IB-($persen/100*IB),
                    IC = IC-($persen/100*IC),
                    ID = ID-($persen/100*ID),
                    IIA = IIA-($persen/100*IIA),
                    IIB = IIB-($persen/100*IIB),
                    IIC = IIC-($persen/100*IIC),
                    IID = IID-($persen/100*IID),
                    IIE = IIE-($persen/100*IIE),
                    IIIA = IIIA-($persen/100*IIIA),
                    IIIB = IIIB-($persen/100*IIIB),
                    IIIC = IIIC-($persen/100*IIIC),
                    IIID = IIID-($persen/100*IIID)  
                    ");

        Gapok::where('th',0)->update(['acc_up'=>0]);
        \LogActivity::addToLog(Auth::user()->name.' Menurunkan Data Gaji Pokok');
        return response()->json(['success' => 'Data is successfully updated']);
    }
}