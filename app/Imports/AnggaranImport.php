<?php

namespace App\Imports;

use App\Models\Anggaran;
use App\Models\COA;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Carbon\Carbon;
use Auth;
use DateTime;
class AnggaranImport implements OnEachRow,WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
 public function onRow(Row $row)
{
    $pes = date('Y-m-d'); 
    $NewDate = date('Y-m-d', strtotime($pes . " +3 days"));
   $id = Auth::user()->id;
    $jenis = 'anggaran';
   
    $masuk = [
            'tanggal'       => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0])->format('Y-m-d'),
            'coa'           => $row[1],
            'nama_akun'     => $row[2],
            'jenis'         =>$row[3],
            'anggaran'      => $row[4],
            'relokasi'      => $row[5],
            'tambahan'      => $row[6],
            'kantor'        => $row[7],
            'jabatan'       => $row[8],
            'id_program'    => $row[9],
            'id_referensi'  => $row[10],
            'keterangan'    => $row[11],
            'alasan'        => $row[13],
            'acc'           => $row[12],
    ];
    
    //   foreach($row as $val){
    //             $tanggal[] = $val[$row[0]];
    //             $nama_akun[] = $val[$row[1]];
    //             $anggaran[] =  $val[$row[2]];
    //             $relokasi[] = $val[$row[3]];
    //             $tambahan[] = $val[$row[4]];
    //             $kantor[] = $val[$row[5]];
    //             $jabatan[]= $val[$row[6]];
    //             $id_program[] = $val[$row[7]];
    //             $id_referensi[] = $val[$row[8]];
    //             $keterangan[] = $val[$row[8]];
    //             $acc[] = $val[$row[9]];
    //             $alasan[] = $val[$row[10]];
    //             dd($tanggal);
    //         }
      
  
    // $coaid = COA::select('coa.coa')->where('nama_coa', '=' , $masuk['nama_akun'])->first();
    
    if (COA::select('coa.*')->where('grup','like','%2%')->where('coa', '=' , $masuk['coa'])->exists()) {
        if($NewDate <= $masuk['tanggal'] ){
            $user = anggaran ::create([
            'tanggal'       => $masuk['tanggal'],
            'coa'           => $masuk['coa'],
            'nama_akun'     => $masuk['nama_akun'],
            'jenis'         => $masuk['jenis'],
            'anggaran'      => $masuk['anggaran'],
            'relokasi'      => $masuk['relokasi'],
            'tambahan'      => $masuk['tambahan'],
            'kantor'        => $masuk['kantor'],
            'jabatan'       => $masuk['jabatan'],
            'id_program'    => $masuk['id_program'],
            'id_referensi'  => $masuk['id_referensi'],
            'keterangan'    => $masuk['keterangan'],
            'alasan'        => $masuk['alasan'],
            'acc'           => $masuk['acc'],
            'user_input'    => $id,
        ]);
        }
           
     }
}



     public function startRow(): int
    {
        return 2;
    }
}
