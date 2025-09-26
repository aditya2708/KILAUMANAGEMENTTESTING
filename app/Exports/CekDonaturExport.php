<?php

namespace App\Exports;

use App\Models\Donatur;
use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CekDonaturExport implements FromQuery, WithChunkReading
{
    use Exportable;

    public function query()
    {
        // return Donatur::query()->whereYear('created_at', 2023);
        return Donatur::query();
    }
    
    public function chunkSize(): int
    {
        return 1000;
    }
}