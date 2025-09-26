<?php

namespace App\Exports;

use App\DonaturSumedang;
use Maatwebsite\Excel\Concerns\FromCollection;

class DonaturSumedangExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DonaturSumedang::orderBy('created_at','desc')->get();
    }
}
