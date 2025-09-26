<?php

namespace App\Imports;

use App\Karyawan;
use Maatwebsite\Excel\Concerns\ToModel;

class KaryawanImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Karyawan([
            'id_karyawan'     => $row[0],
            'nama'    => $row[1],
            'email'    => $row[2],
            'no_hp'    => $row[3],
            'ttl'    => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[4])->format('Y-m-d'),
            'nik'    => $row[5],
            'hobi'    => $row[6],
            'alamat' => $row[7],
            'pendidikan' => $row[8],
            'nm_sekolah' => $row[9],
            'th_lulus' => $row[10],
            'ijazah' => $row[11],
            'jurusan' => $row[12],
            'gelar' => $row[13],
            'status_nikah' => $row[14],
            'no_kk' => $row[15],
            'scan_kk' => $row[16],
            'nm_pasangan' => $row[17],
            'tgl_lahir' => $row[18],
            'tgl_nikah' => $row[19],
            'nm_anak1' => $row[20],
            'nm_anak2' => $row[21],
            'nm_anak3' => $row[22],
            'nm_anak4' => $row[23],
            'tgl_kerja' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[24])->format('Y-m-d'),
            'unit_kerja' => $row[25],
            'id_kantor' => $row[26],
            'kantor_induk' => $row[27],
            'jabatan' => $row[28],
            'pr_jabatan' => $row[29],
            'durasi_kerja' => $row[30],
            'k_nama1' =>$row[31],
            'k_jabatan1' => $row[32],
            'k_durasi1' => $row[33],
            'k_nama2' =>$row[34],
            'k_jabatan2' => $row[35],
            'k_durasi2' => $row[36],
            'o_nama1' => $row[37],
            'o_jabatan1' => $row[38],
            'o_nama2' => $row[39],
            'o_jabatan2' => $row[40],
            'p_nama1' => $row[41],
            'p_durasi1' => $row[42],
            'p_nama2' => $row[43],
            'p_durasi2' => $row[44],
            'kecocokan' => $row[45],
            'seharusnya' => $row[46],
            'alasan' => $row[47],
            'jk' => $row[48],
            'gambar_identitas' => $row[49],
            'password' => $row[50],
            'aktif' => $row[51],
        ]);
    }
}
