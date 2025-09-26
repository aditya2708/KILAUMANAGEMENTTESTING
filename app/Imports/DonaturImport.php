<?php

namespace App\Imports;

use App\Models\Donatur;
use Maatwebsite\Excel\Concerns\ToModel;

class DonaturImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Donatur([
            'id'     => $row[0],
            'nama'    => $row[1],
            'np'    => $row[2],
            'petugas'    => $row[3],
            'email'    => $row[4],
            'jk'    => $row[5],
            'ttl'    => $row[6],
            'no_hp'    => $row[7],
            'alamat'    => $row[8],
            'agama'    => $row[9],
            'status_nikah'    => $row[10],
            'penghasilan'    => $row[11],
            'pendidikan'    => $row[12],
            'pekerjaan'    => $row[13],
            'gambar_donatur'    => $row[14],
            'program'    => $row[15],
            'pembayaran'    => $row[16],
            'jalur'    => $row[17],
            'kota'    => $row[18],
            'wilayah'    => $row[19],
            'status'    => $row[20],
            'acc'    => $row[21],
            'username'    => $row[22],
            'password'    => $row[23],
            'latitude'    => $row[24],
            'longitude'    => $row[25],
            'setoran'    => @$row[26],
            'total'    => @$row[27],
            'tgl_kolek'    => @$row[28],
            'dikolek'    => @$row[29],
            'bukti'    => @$row[30],
            'created_at'    => @$row[31],
            'updated_at'    => @$row[32],

        ]);
    }
}
