<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="9" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
    <tr><th colspan="9" style="text-align: center;"><h1>{{ $periode }}</h1></th></tr>

    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">ID donatur</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tahun</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nama Donatur</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Pendapatan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Zakat</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal Transaksi</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $no = 1; 
        @endphp
        
        @foreach($data as $i => $item)
                
            <tr>
                <td style="border: 1px solid black;">{{ $no++}}</td>
                <td style="border: 1px solid black;">{{ $item['id_donatur'] }}</td>
                <td style="border: 1px solid black;">{{ $item['tahun'] }}</td>
                <td style="border: 1px solid black;">{{ $item['donatur'] }}</td>
                <td style="border: 1px solid black;">{{ $item['penghasilan'] }}</td>
                <td style="border: 1px solid black;">{{ $item['jumlah'] }}</td>
                <td style="border: 1px solid black;">{{ $item['tanggal'] }}</td>

            </tr>
        @endforeach
    </tbody>
</table>