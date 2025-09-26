<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="4" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ strtoupper($company) }}</b></h1></th></tr>
        <tr><th colspan="4" style="text-align: center; text-transform: uppercase;"><h1 ><b> Detail Transaksi {{ $title }} {{ $pembeda }} Mutasi COA {{ $nama }} Bulan {{ $bulan }} Tahun {{ $tahun }}</b></h1></th></tr>

    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">COA</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Keterangan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nominal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">COA Buku</th>
        </tr>
    </thead>
    
    <tbody>
        @foreach($data as $i => $item)
            <tr>
                <td style="border: 1px solid black;">{{ $item->tanggal }}</td>
                <td style="border: 1px solid black;">
                     @if ($pembeda == 'kredit')
                            {{ $item->coa_kredit }}
                     @else
                            {{ $item->coa_debet }}
                     @endif
                   </td>
                <td style="border: 1px solid black;">{{ $item->ket_penerimaan }}</td>
                <td style="border: 1px solid black;">
                    @if($pembeda == 'Kredit')
                        {{ $item->kredit }}
                    @elseif($pembeda == 'Debet')
                        {{ $item->debit }}
                    @endif
                </td>
                <td style="border: 1px solid black;">
                     @if($pembeda == 'Kredit')
                            {{ $item->coa_debet }}
                     @elseif($pembeda == 'Debet')
                            {{ $item->coa_kredit }}
                     @endif
                    </td>
            </tr>
        @endforeach
    </tbody>

  
</table>


  

