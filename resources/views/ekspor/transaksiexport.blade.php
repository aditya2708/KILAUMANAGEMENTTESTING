<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="12" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company->name }}</b></h1></th></tr>
    <tr><th colspan="12" style="text-align: center; text-transform: uppercase;"><h1 >{{ $priode }}</h1></th></tr>

    <tr></tr>
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">ID Transaksi</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kolektor</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Donatur</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jalur</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Sub Program</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Keterangan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Pembayaran</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Status</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jumlah</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Alamat Donatur</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tgl</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1 ;  @endphp
        @foreach($data as $i => $item)
             <tr>
                <td style="border: 1px solid black;">{{ $no++ }}</td>
                <td style="border: 1px solid black;">{{ $item->id_transaksi }}</td>
                <td style="border: 1px solid black;">{{ $item->kolektor }}</td>
                <td style="border: 1px solid black;">{{ $item->donatur }}</td>
                <td style="border: 1px solid black;">{{ $item->jalur }}</td>
                <td style="border: 1px solid black;">{{ $item->subprogram }}</td>
                <td style="border: 1px solid black;">{{ $item->keterangan }}</td>
                <td style="border: 1px solid black;">{{ $item->pembayaran }}</td>
                <td style="border: 1px solid black;">{{ $item->status }}</td>
                <td style="border: 1px solid black;">{{ $item->jumlah }}</td>
                <td style="border: 1px solid black;">{{ $item->alamat }}</td>
                <td style="border: 1px solid black;">{{ $item->tanggal }}</td>
            </tr>
        @endforeach
    </tbody>
</table>