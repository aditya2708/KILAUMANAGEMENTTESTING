<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="9" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company->name }}</b></h1></th></tr>
    <tr><th colspan="9" style="text-align: center; text-transform: uppercase;"><h1 ><b>Analis Transaksi Berdasarkan {{ $analis }}</b></h1></th></tr>
    <tr><th colspan="9" style="text-align: center; text-transform: uppercase;">Periode {{ $prd }}</th></tr>

    <tr></tr>
    @if($toggle != "detail")
         <thead>
            <tr>
                <th style="border: 1px solid black; background-color: #96D4D4;">Analis {{ $analis }}</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Nominal</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Transaksi</th>
                @if($toggle == "true")
                    <th style="border: 1px solid black; background-color: #96D4D4;">Non Transaksi</th>
                    <th style="border: 1px solid black; background-color: #96D4D4;">Donatur</th>
                    <th style="border: 1px solid black; background-color: #96D4D4;">Donatur Tanpa Transaksi</th>
                @else
                    <th style="border: 1px solid black; background-color: #96D4D4;">Donatur</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $item)
                 <tr>
                    <td style="border: 1px solid black;">{{ $item->nama }}</td>
                    <td style="border: 1px solid black;">{{ $item->data }}</td>
                    <td style="border: 1px solid black;">{{ $item->transaksi }}</td>
                    @if($toggle == "true")
                        <td style="border: 1px solid black;">{{ $item->nontransaksi }}</td>
                        <td style="border: 1px solid black;">{{ $item->don }}</td>
                        <td style="border: 1px solid black;">{{ $item->don1 }}</td>
                    @else
                        <td style="border: 1px solid black;">{{ $item->don }}</td>
                    @endif
                    
                </tr>
            @endforeach
        </tbody>
    @else
        <thead>
            <tr>
                <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">ID Transaksi</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Petugas</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Program</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Nominal</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Donatur</th>
            </tr>
        </thead>
        <tbody>
            @php 
            $no = 1;
            @endphp
            @foreach($data as $i => $item)
                 <tr>
                    <td style="border: 1px solid black;">{{ $no++ }}</td>
                    <td style="border: 1px solid black;">{{ $item->tanggal }}</td>
                    <td style="border: 1px solid black;">{{ $item->id_transaksi }}</td>
                    <td style="border: 1px solid black;">{{ $item->kolektor }}</td>
                    <td style="border: 1px solid black;">{{ $item->program }}</td>
                    @if($kondisi != "")
                        <td style="border: 1px solid black;">{{ $item->status }}</td>
                    @else
                        <td style="border: 1px solid black;">{{ $item->jumlah }}</td>
                    @endif
                    <td style="border: 1px solid black;">{{ $item->donatur }}</td>
                    
                </tr>
            @endforeach
        </tbody>
    @endif
   
</table>