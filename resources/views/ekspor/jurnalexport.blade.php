<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="{{ $grup == '' ? 8 : 6 }}" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company->name }}</b></h1></th></tr>
    <tr><th colspan="{{ $grup == '' ? 8 : 6 }}" style="text-align: center; text-transform: uppercase;"><h1 ><b>REKAP JURNAL</b></h1></th></tr>
    <tr><th colspan="{{ $grup == '' ? 8 : 6 }}" style="text-align: center; text-transform: uppercase;"><h1 >{{ $priode }}</h1></th></tr>

    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">COA</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jenis Transaksi</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Debit</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kredit</th>
            @if($grup == '')
            <th style="border: 1px solid black; background-color: #96D4D4;">Keterangan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Via Jurnal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">No. Resi</th>
            @else
            <th style="border: 1px solid black; background-color: #96D4D4;"></th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $item)
             <tr>
                <td style="border: 1px solid black;">{{ $item['tanggal'] }}</td>
                <td style="border: 1px solid black;">{{ $item['coa_debet'] }}</td>
                <td style="border: 1px solid black;">{{ $item['nama_coa'] }}</td>
                <td style="border: 1px solid black;">{{ $item['debit'] }}</td>
                <td style="border: 1px solid black;">{{ $item['kredit'] }}</td>
                @if($grup == '')
                <td style="border: 1px solid black;">{{ $item['ket_penerimaan'] }}</td>
                <td style="border: 1px solid black;">{{ $item['via_jurnal'] }}</td>
                <td style="border: 1px solid black;">{{ $item['id_transaksi'] }}</td>
                @else
                <td style="border: 1px solid black;">{{ $item['ket_penerimaan'] }}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>