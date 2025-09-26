<?php

namespace App\Exports;

use App\Models\Kantor;
use App\Models\Bank;
use App\Models\COA;
use App\Models\Penutupan;
use App\Models\Jabatan;
use App\Models\User;
use App\Models\Anggaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class downloadformat implements  WithHeadings
{
    
        public function headings(): array
    {
                return [
            'Tanggal','COA','Nama COA' ,'Jenis','Anggaran','Relokasi','Tambahan','ID kantor','jabatan' ,'ID Program','ID Referensi','Keterangan','Acc','Alasan Tidak di Terima'
        ];
    }
    
   
    
}


