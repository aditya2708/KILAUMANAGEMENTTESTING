<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\Prosp;
use App\Models\Prog;
use Auth;
use League\Fractal\TransformerAbstract;

class DonSpvTransformer extends TransformerAbstract
{
    protected $extra;

     public function __construct($extra) {
         $this->ex = $extra;
     }
     
    public function transform($user){
        $ptg = User::where('id_spv', $this->ex['id'])->get();
        foreach($ptg as $z => $pt){
            $idptg[] = $pt->id;
        }
        // dd($this->ex['ket'], $this->ex['id']);
            $prosp = Prosp::where('id_don', $user->id)->get();
            // dd($prosp);
            if(count($prosp) == 0){
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
            }else{
            foreach($prosp as $x => $v){
                $y = Prog::where('id_program', $v->id_prog)->first();
                // dd($y[]);
                if(in_array($v->id_peg, $idptg) && $v->ket == $this->ex['ket'] && $v->konprog == $this->ex['konprog']){
                
                $nama_prog[] = $y->program;
                $id_prosp[] = $v->id;
                $id_peg[] = $v->id_peg;
                $program[] = $v->id_prog;
                $sumdan[] = $v->id_sumdan;
                $kotak[] = $y->kotak;
                $ket[] = $v->ket;
                $tglfol[] = $v->tgl_fol;
                $statprog[] = $v->status;
                $konprog[] = $v->konprog;
                $hide[] = 0;
                $ket_prog[] = $y->ket;
                // }else{
                // $id_prosp = [];
                // $nama_prog = [];
                // $id_peg = [];
                // $program = [];
                // $sumdan = [];
                // $ket = [];
                // $tglfol = [];
                // $statprog = [];
                // $konprog = [];
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
            'progres' => $this->ex['ket'],
            'jenis_donatur' => $user->jenis_donatur,
            'gambar' => $user->gambar_donatur,
            'jk' => $user->jk,
            'tahun_lahir' => $user->tahun_lahir,
            'pekerjaan' => $user->pekerjaan,
            'tgl_fol' => $tglfol,
            'hide' => $hide,
            'ket_prog' => $ket_prog,
            'id_koleks' => $user->id_koleks,
            'kotak' => $kotak,
        ];
    }

}