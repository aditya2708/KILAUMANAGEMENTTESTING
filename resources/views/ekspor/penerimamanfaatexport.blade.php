<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="9" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company->name }}</b></h1></th></tr>
    <tr><th colspan="9" style="text-align: center;"><h1>Priode {{ $periode }}</h1></th></tr>

    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jenis</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Penerima Manfaa</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Penangung Jawab</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Alamat</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">HP</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Asnaf</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Registrasi</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kantor</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $no = 1; 
        @endphp
        
        @foreach($data as $i => $item)
                
            <tr>
                <!--<td style="border: 1px solid black;">{{ $no++}}</td>-->
                <td style="border: 1px solid black;">{{ $item['jenis_pm'] }}</td>
                <td style="border: 1px solid black;">{{ $item['penerima_manfaat'] }}</td>
                <td style="border: 1px solid black;">{{ $item['nama_pj'] }}</td>
                <td style="border: 1px solid black;">{{ $item['alamat'] }}</td>
                <td style="border: 1px solid black;">{{ $item['hp'] }}</td>
                <td style="border: 1px solid black;">{{ $item['asnaf'] }}</td>
                <td style="border: 1px solid black;">{{ $item['tgl_reg'] }}</td>
                <td style="border: 1px solid black;">{{ $item['unit'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>