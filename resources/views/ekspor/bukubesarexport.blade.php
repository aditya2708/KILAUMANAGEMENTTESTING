<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="{{ $grup == '' ? 8 : 6 }}" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company->name }}</b></h1></th></tr>
    <tr><th colspan="{{ $grup == '' ? 8 : 6 }}" style="text-align: center; text-transform: uppercase;"><h1 ><b>BUKU BESAR</b></h1></th></tr>
    <tr><th colspan="{{ $grup == '' ? 8 : 6 }}" style="text-align: center; text-transform: uppercase;"><h1 >{{ $priode }}</h1></th></tr>

    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">COA</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jenis Transaksi</th>
            @if($grup == '')
            <th style="border: 1px solid black; background-color: #96D4D4;">Keterangan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Debet</th> 
            <th style="border: 1px solid black; background-color: #96D4D4;">Kredit</th> 
            <th style="border: 1px solid black; background-color: #96D4D4;">Saldo</th> 
            <th style="border: 1px solid black; background-color: #96D4D4;">ID Transaksi</th> 
            @else
            <th style="border: 1px solid black; background-color: #96D4D4;">Debet</th> 
            <th style="border: 1px solid black; background-color: #96D4D4;">Kredit</th> 
            <th style="border: 1px solid black; background-color: #96D4D4;">Saldo</th> 
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $item)
             <tr>
                <td style="border: 1px solid black;">{{ $item['tanggal']}}</td>
                <td style="border: 1px solid black;">{{ $item['coa_debet'] }}</td>
                <td style="border: 1px solid black;">{{ $item['nama_coa'] }}</td>
                @if($grup == '')
                <td style="border: 1px solid black;">{{ $item['ket_penerimaan']}}</td>
                <td style="border: 1px solid black;">{{ $item['debit'] }}</td>
                <td style="border: 1px solid black;">{{ $item['kredit'] }}</td>
                <td style="border: 1px solid black;">{{ $item['jumlahs'] }}</td>
                <td style="border: 1px solid black;">{{ $item['id_transaksi'] }}</td>
                @else
                <td style="border: 1px solid black;">{{ $item['debit'] }}</td>
                <td style="border: 1px solid black;">{{ $item['kredit'] }}</td>
                <td style="border: 1px solid black;">{{ $item['jumlahs'] }}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>