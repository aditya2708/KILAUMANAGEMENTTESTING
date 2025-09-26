


<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="5" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
    <tr><th colspan="5" style="text-align: center; text-transform: uppercase;"><h1 >Detail Transaksi COA {{ $nama_coa }} Priode {{ $b }}-{{ $t }}</h1></th></tr>
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
        @php $arr=[]; @endphp
        @foreach($data as $i => $item)
            <tr>
                <td style="border: 1px solid black;">{{ $item->tanggal }}</td>
                <td style="border: 1px solid black;">{{ $item->coa_debet }}</td>
                <td style="border: 1px solid black;">{{ $item->ket_penerimaan }}</td>
                <td style="border: 1px solid black;">{{ $item->total }}</td>
                <td style="border: 1px solid black;">{{ $item->coa_kredit }}</td>
            </tr>
        @endforeach
    </tbody>
    <!--<tfoot>-->
    <!--    <tr>-->
    <!--        <th></th>-->
    <!--        <th></th>-->
    <!--        <th></th>-->
    <!--        <th></th>-->
    <!--        <th></th>-->
    <!--    </tr>-->
    <!--</tfoot>-->
</table>