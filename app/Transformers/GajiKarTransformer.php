<?php

namespace App\Transformers;


use League\Fractal\TransformerAbstract;

class GajiKarTransformer extends TransformerAbstract
{
    public function transform($gaji){
    $data       = [];
    $datgaji    = [];
    
        // $for_ar = [
        //             'total', 'potab', 'potongan'
        //             ];
        $for_ar = [
                    'gapok', 'tj_jabatan', 'tj_daerah', 'tj_p_daerah', 'tj_anak', 'tj_pasangan', 'tj_beras', 'transport', 
                    'ketenagakerjaan', 'kesehatan', 'bonus', 'bokin', 'arrinbon', 'bpjs', 'potdis', 'potdll', 'potlaptab', 'arpotkol', 'jml_hari'
                    ];
        $ser_ar = ['potlaptab', 'arpotkol', 'arrinbon'];
        $as_ar  = [
                    'gapok'             => 'Gaji Pokok',
                    'tj_jabatan'        => 'Tunjangan Fungsional',
                    'tj_daerah'         => 'Tunjangan Daerah',
                    'tj_p_daerah'       => 'Tunjangan Pejabat Daerah',
                    'tj_anak'           => 'Tunjangan Anak',
                    'tj_pasangan'       => 'Tunjangan Pasangan',
                    'tj_beras'          => 'Tunjangan Beras',
                    'transport'         => 'Uang Transport',
                    'jml_hari'          => 'Hari Kerja',
                    'ketenagakerjaan'   => 'Ketenagakerjaan',
                    'kesehatan'         => 'Kesehatan',
                    'bonus'             => 'Bonus',
                    'bokin'             => 'Kinerja',
                    'bpjs'              => 'BPJS',
                    'potdis'            => 'Disiplin',
                    'potdll'            => 'DLL',
                    ];
        $ar_bon = ['bonus', 'bokin', 'arrinbon'];
        $ar_pot = ['bpjs', 'potdis', 'potdll', 'potlaptab', 'arpotkol'];
        $ar_bjs = ['ketenagakerjaan', 'kesehatan'];
        $id     = 0;
        
        // foreach ($gaji->getAttributes() as $f => $v) {
        foreach ($gaji->only($for_ar) as $f => $v) {
            
            if(in_array($f, $ar_bon)){
                $grup = 'bonus';
            }else if(in_array($f, $ar_pot)){
                $grup = 'potongan';
            }else if(in_array($f, $ar_bjs)){
                $grup = 'bpjs';
            }else{
                $grup = 'utama';
            }
                    
            if ($v != null) {
                if(in_array($f, $ser_ar)){
                    $un_ar  = $v === 'b:0;' || @unserialize($v) !== false ? unserialize($v) : [];
                    foreach ($un_ar as $v_un) {
                        $id ++;
                        $nama   = $v_un->nampot;
                        $datgaji[] = [
                                    'id'    => $id,
                                    'nama'  => $v_un->nampot,
                                    'nilai' => number_format($v_un->jumlah,0, ',' , '.'),
                                    'koned' => 0,
                                    'grup'  => $grup,
                                ];
                    }
                }else{
                    $id ++;
                    $nama   = $f;
                    $datgaji[] = [
                                'id'    => $id,
                                'nama'  => $as_ar[$f],
                                'nilai' => number_format($v,0, ',' , '.'),
                                'koned' => 0,
                                'grup'  => $grup,
                            ];
                }
                
            }
        }
        
        // if (!empty($datgaji)) {
            $data = [
                        'id_gaji'       => $gaji->id_gaji,
                        'id_karyawan'   => $gaji->id_karyawan,
                        'nama'          => $gaji->nama,
                        'no_rek'        => $gaji->no_rek,
                        'nik'           => $gaji->nik,
                        'status_kerja'  => $gaji->status_kerja,
                        'masa_kerja'    => $gaji->masa_kerja,
                        'golongan'      => $gaji->golongan,
                        'id_jabatan'    => $gaji->id_jabatan,
                        'id_kantor'     => $gaji->id_kantor,
                        'tgl_gaji'      => $gaji->created_at->format('Y-m-d'),
                        'jabatan'       => $gaji->jabatan,
                        'status'        => $gaji->status,
                        'created'       => $gaji->created,
                        'jml_hari'      => $gaji->jml_hari,
                        'thp'           => number_format($gaji->thp,0, ',' , '.'),
                        'datgaji'       => $datgaji
                    ];
        // }
        
    return $data;
    
    }

}