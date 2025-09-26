<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>

<table>
    <tr><th colspan="13" style="text-align: center; "><h1><b>{{ strtoupper($kompani) }}</b></h1></th></tr>
    <tr><th colspan="13" style="text-align: center;"><h1>Pengeluaran</h1></th></tr>
    <tr><th colspan="13" style="text-align: center;"><h1>{{ $periode }}</h1></th></tr>
    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">#</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jenis Transaksi</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Keterangan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Qty</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nominal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">User Input</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">User Approve</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Referensi</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Program</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kantor</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">COA Debit</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">COA Kredit</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($data as $item)
            <tr>
                <td style="border: 1px solid black;">{{$no++}}</td>
                <td style="border: 1px solid black;">{{ $item->tgl }}</td>
                <td style="border: 1px solid black;">{{ $item->jenis_transaksi }}</td>
                <td style="border: 1px solid black;">{{ $item->keterangan }}</td>
                <td style="border: 1px solid black;">{{ $item->qty }}</td>
                <td style="border: 1px solid black;">{{ $item->nominal }}</td>
                <td style="border: 1px solid black;">{{ $item->uiname }}</td>
                <td style="border: 1px solid black;">{{ $item->uaname }}</td>
                <td style="border: 1px solid black;">{{ $item->referensi }}</td>
                <td style="border: 1px solid black;">{{ $item->program }}</td>
                <td style="border: 1px solid black;">{{ $item->unit }}</td>
                <td style="border: 1px solid black;">{{ $item->coa_debet }}</td>
                <td style="border: 1px solid black;">{{ $item->coa_kredit }}</td>
            </tr>
        @endforeach
    </tbody>
</table>