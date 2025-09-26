<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="9" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company->name }}</b></h1></th></tr>
    @if($inbuku == 1)
        <tr><th colspan="9" style="text-align: center;"><h1>Buku Kas [{{ $judul->coa }} {{ $judul->nama_coa }}]</h1></th></tr>
    @else
        <tr><th colspan="9" style="text-align: center;"><h1>[{{ $judul }}]</h1></th></tr>
    @endif
    <tr><th colspan="9" style="text-align: center;"><h1>Periode {{ $priode }}</h1></th></tr>

    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">COA</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jenis Transaksi</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Keterangan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Debet</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kredit</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Saldo</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">No Resi</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $no = 1; 
        @endphp
        
        @foreach($data as $i => $item)
                
            <tr>
                <!--<td style="border: 1px solid black;">{{ $no++}}</td>-->
                <td style="border: 1px solid black;">{{ $item['no'] }}</td>
                <td style="border: 1px solid black;">{{ $item['tanggal'] }}</td>
                <td style="border: 1px solid black;">{{ $item['coa'] }}</td>
                <td style="border: 1px solid black;">{{ $item['jentran'] }}</td>
                <td style="border: 1px solid black;">{{ $item['ket'] }}</td>
                <td style="border: 1px solid black;">{{ $item['debit'] }}</td>
                <td style="border: 1px solid black;">{{ $item['kredit'] }}</td>
                <td style="border: 1px solid black;">{{ $item['saldo'] }}</td>
                <td style="border: 1px solid black;">{{ $item['id_tran'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>