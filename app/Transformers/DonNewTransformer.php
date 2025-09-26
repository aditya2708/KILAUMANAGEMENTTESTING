<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\Prosp;
use App\Models\Prog;
use App\Models\UpNoLoc;
use Auth;
use League\Fractal\TransformerAbstract;

class DonNewTransformer extends TransformerAbstract
{
    protected $extra;

     public function __construct($extra) {
         $this->ex = $extra;
     }
     
    public function transform($user){
       
        if($this->ex['kon'] == 'Cab' | $this->ex['kon'] == 'Spv'){
            if($this->ex['kon'] == 'Cab'){
                $ptg = User::where('id_kantor', $this->ex['id'])->orWhere('kantor_induk', $this->ex['id'])->get();
            }else{
                $ptg = User::where('id_spv', $this->ex['id'])->get();
            }
            foreach($ptg as $z => $pt){
                $idptg[] = $pt->id;
            }
        }
        
        if($this->ex['kon'] == 'Up'){
            $up = UpNoLoc::where('id_don', $user->id)->orderBy('id', 'DESC')->get();
        }else{
            $prosp = Prosp::where('id_don', $user->id)->get();
        }
        
        $no_hp = [];
        $lokasi = [];
        $gambar = [];
        
        $id_prosp = [];
        $nama_prog = [];
        $id_peg = [];
        $program = [];
        $sumdan = [];
        $ket = [];
        $tglfol = [];
        $statprog = [];
        $konprog = [];
        $hide = [];
        $ket_prog = [];
        $kotak = [];
        $arprospek = [];
        $ex_ket = 'closing';
        
        if($this->ex['kon'] == 'Up'){
            if(count($up) > 0){
            foreach($up as $x => $v){
                if($this->ex['acc'] == 2){
                    $kondisi = $v->acc == $this->ex['acc'];
                    $ex_ket  = 'closing';
                }else{
                    $kondisi = $v->acc == $this->ex['acc'] && $v->tgl_acc >= $this->ex['tgl1'] && $v->tgl_acc <= $this->ex['tgl2'];
                    $ex_ket  = 'closing';
                }
                
                if($kondisi){
                    if($v->jenis == 'no'){
                    $no_hp[] = ['id' => $v->id, 
                                'petugas' => $v->petugas, 
                                'tanggal' => $v->created_at->format('Y-m-d'), 
                                'tgl_acc' => $v->tgl_acc, 
                                'no_hp' => $v->no_hp,
                                'jenis' => $v->jenis];
                    }else if($v->jenis == 'lok'){
                    $lokasi[] = ['id' => $v->id, 
                                'petugas' => $v->petugas, 
                                'tanggal' => $v->created_at->format('Y-m-d'), 
                                'tgl_acc' => $v->tgl_acc, 
                                'latitude' => $v->latitude, 
                                'longitude' => $v->longitude,
                                'jenis' => $v->jenis];
                    }else if($v->jenis == 'gam'){
                    $gambar[] = ['id' => $v->id, 
                                'petugas' => $v->petugas, 
                                'tanggal' => $v->created_at->format('Y-m-d'), 
                                'tgl_acc' => $v->tgl_acc, 
                                'gambar' => $v->gambar_donatur,
                                'jenis' => $v->jenis];
                    }
                }
                    
            }
            }
        }else{
            if(count($prosp) > 0){
            foreach($prosp as $x => $v){
                $y = Prog::where('id_program', $v->id_prog)->first();
                
                if($this->ex['kon'] == 'Donatur'){
                    $kondisi = $v->id_peg == $this->ex['id'] && $v->ket == $this->ex['ket'];
                    $ex_ket  = $this->ex['ket'];
                }else if($this->ex['kon'] == 'Cab' | $this->ex['kon'] == 'Spv'){
                    $kondisi = in_array($v->id_peg, $idptg) && $v->ket == $this->ex['ket'] && $v->konprog == $this->ex['konprog'];
                    $ex_ket  = $this->ex['ket'];
                }else if($this->ex['kon'] == 'Range'){
                    $kondisi = $v->id_peg == $this->ex['id'] && $v->ket == $this->ex['ket'] && $v->tgl_fol >= $this->ex['tgl1'] && $v->tgl_fol <= $this->ex['tgl2'];
                    $ex_ket  = $this->ex['ket'];
                }else if($this->ex['kon'] == 'Id'){
                    $kondisi = $v->id_peg == $this->ex['id'];
                    $ex_ket  = 'closing';
                }else if($this->ex['kon'] == 'DonKet'){
                    $kondisi = $v->ket == $this->ex['ket'];
                    $ex_ket  = $this->ex['ket'];
                }else{
                    $kondisi = 0 == 0;
                    $ex_ket  = 'closing';
                }
    
                if($kondisi){                
                    $id_prosp[] = $v->id;
                    $nama_prog[] = $y->program;
                    $id_peg[] = $v->id_peg;
                    $program[] = $v->id_prog;
                    $sumdan[] = $v->id_sumdan;
                    $ket[] = $v->ket;
                    $tglfol[] = $v->tgl_fol;
                    $statprog[] = $v->status;
                    $konprog[] = $v->konprog;
                    $hide[] = 0;
                    $ket_prog[] = $y->ket;
                    $kotak[] = $y->kotak;
                    $arprospek[] = [
                                    'id_program'    => $v->id_prog,
                                    'program'       => $y->program,
                                    'id_sumber_dana'=> $v->id_sumdan
                                ];
                }
            }
            }
        }
        
            
            // $kantor = ['id_kantor' => $user->id_kantor, 'unit' => $user->unit];
        return [
            'id' => $user->id,
            'id_laphub' => $user->id_laphub,
            'id_kantor' => $user->id_kantor,
            // 'kantor' => ['id_kantor' => $user->id_kantor, 'unit' => $user->unit],
            'id_transaksi' => $user->id_transaksi,
            'tanggal' => $user->tanggal,
            'petugas' => $user->petugas != NULL ? $user->petugas : 'Kosong',
            'nama' => $user->nama,
            'email' => $user->email,
            'deskripsi' => $user->deskripsi,
            'unit' => $user->unit,
            'np' => $user->np,
            'upno' => $user->no_hp == null ? 1 : 0,
            'uplok' => $user->latitude == null | $user->longitude == null ? 1 : 0,
            'upgam' => $user->gambar_donatur == null ? 1 : 0,
            'status' => $user->status,
            'jalur' => $user->jalur,
            'id_jalur' => $user->id_jalur,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'alamat' => $user->alamat,
            'tgl_kolek' => $user->tgl_kolek,
            'pembayaran' => $user->pembayaran,
            'id_pros' => $id_prosp,
            'program' => $program,
            'statprog' => $statprog,
            'program' => $program,
            'nama_program' => $nama_prog,
            'id_peg' => $id_peg,
            'id_sumdan' => $sumdan,
            'konprog' => $konprog,
            'feedback' => $user->feedback,
            'retup' => $user->retup,
            'ketup' => $user->ketup,
            'no_hp' => $user->no_hp,
            'nohap' => $user->nohap,
            'orng_dihubungi' => $user->orng_dihubungi,
            'jabatan' => $user->jabatan,
            'setoran' => $user->setoran,
            'provinsi' => $user->provinsi,
            'kota' => $user->kota,
            'bukti' => $user->bukti,
            'acc' => $user->acc,
            'dikolek' => $user->dikolek,
            'gambar_donatur' => $user->gambar_donatur,
            'registered' => $user->created_at->format('d M Y'),
            'updated' => $user->updated_at->format('d M Y'),
            'date' => $user->created_at->format('Y-m-d'),
            'warning' => $user->warning,
            'ket' => $ket,
            'progres' => $ex_ket,
            'jenis_donatur' => $user->jenis_donatur,
            'gambar' => $user->gambar_donatur,
            'jk' => $user->jk,
            'tahun_lahir' => $user->tahun_lahir,
            'pekerjaan' => $user->pekerjaan,
            'tgl_fol' => $tglfol,
            'hide' => $hide,
            'ket_prog' => $ket_prog,
            'id_koleks' => $user->id_koleks,
            'arprospek' => $arprospek,
            'kotak' => $kotak,
            'up_nohp' => $no_hp,
            'up_lokasi' => $lokasi,
            'up_gambar' => $gambar,
            'ar_id' => $id_prosp,
        ];
    }

}