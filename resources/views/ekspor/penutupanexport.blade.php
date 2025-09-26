<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="26" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $kompani }}</b></h1></th></tr>
    <tr><th colspan="26" style="text-align: center;"><h1>Penutupan {{ $kant }}</h1></th></tr>
    <tr><th colspan="26" style="text-align: center;"><h1>{{ $periode }}</h1></th></tr>

    <tr></tr>
    
    <thead>
        <!--<tr>-->
        <!--    <th style="border: 1px solid black; background-color: #96D4D4;">No</th>-->
        <!--    <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>-->
        <!--    <th style="border: 1px solid black; background-color: #96D4D4;">COA</th>-->
        <!--    <th style="border: 1px solid black; background-color: #96D4D4;">Jenis Transaksi</th>-->
        <!--    <th style="border: 1px solid black; background-color: #96D4D4;">Keterangan</th>-->
        <!--    <th style="border: 1px solid black; background-color: #96D4D4;">Debet</th>-->
        <!--    <th style="border: 1px solid black; background-color: #96D4D4;">Kredit</th>-->
        <!--    <th style="border: 1px solid black; background-color: #96D4D4;">Saldo</th>-->
        <!--    <th style="border: 1px solid black; background-color: #96D4D4;">No Resi</th>-->
        <!--</tr>-->
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Akun</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Saldo Akhir</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Saldo Awal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Debit</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kredit</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Adjustment</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">COA</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">User Input</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">User Update</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">K100000</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">K750000</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">K50000</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">K20000</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">K10000</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">K5000</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">K2000</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">K1000</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">K500</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">K100</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">L1000</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">L500</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">L200</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">L100</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">L50</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">L25</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $no = 1; 
        @endphp
        
        
        @foreach($data as $item)
                
            <tr>
                <td style="border: 1px solid black;">{{ $no++}}</td>
                <td style="border: 1px solid black;">{{$item['tanggal']}}</td>
                <td style="border: 1px solid black;">{{$item['nama_coa']}}</td>
                <td style="border: 1px solid black;">{{$item['saldo_akhir']}}</td>
                <td style="border: 1px solid black;">{{$item['saldo_awal']}}</td>
                <td style="border: 1px solid black;">{{$item['debit']}}</td>
                <td style="border: 1px solid black;">{{$item['kredit']}}</td>
                <td style="border: 1px solid black;">{{$item['adjustment']}}</td>
                <td style="border: 1px solid black;">{{$item['coa']}}</td>
                <td style="border: 1px solid black;">{{$item['user_input']}}</td>
                <td style="border: 1px solid black;">{{$item['user_update']}}</td>
                <td style="border: 1px solid black;">{{$item['k100000']}}</td>
                <td style="border: 1px solid black;">{{$item['k75000']}}</td>
                <td style="border: 1px solid black;">{{$item['k50000']}}</td>
                <td style="border: 1px solid black;">{{$item['k20000']}}</td>
                <td style="border: 1px solid black;">{{$item['k10000']}}</td>
                <td style="border: 1px solid black;">{{$item['k5000']}}</td>
                <td style="border: 1px solid black;">{{$item['k2000']}}</td>
                <td style="border: 1px solid black;">{{$item['k1000']}}</td>
                <td style="border: 1px solid black;">{{$item['k500']}}</td>
                <td style="border: 1px solid black;">{{$item['k100']}}</td>
                <td style="border: 1px solid black;">{{$item['l1000']}}</td>
                <td style="border: 1px solid black;">{{$item['l500']}}</td>
                <td style="border: 1px solid black;">{{$item['l200']}}</td>
                <td style="border: 1px solid black;">{{$item['l100']}}</td>
                <td style="border: 1px solid black;">{{$item['l50']}}</td>
                <td style="border: 1px solid black;">{{$item['l25']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>