<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="4" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
    <tr><th colspan="4" style="text-align: center; text-transform: uppercase;"><h1 ><b> Data Rencana Unit {{$kantor}} {{$bulan}}</b></h1></th></tr>
    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">ID Karyawan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nama</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Bulan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jumlah Hari</th>
        </tr>
    </thead>
    
    <tbody>
        @foreach($data as $item)
            <tr>
                <td style="border: 1px solid black;">{{$item['id_karyawan']}}</td>
                <td style="border: 1px solid black;">{{$item['nama']}}</td>
                <td style="border: 1px solid black;">{{$item['tgl']}}</td>
                <td style="border: 1px solid black;">{{$item['jumlah_hari']}}</td>
            </tr>
        @endforeach
    </tbody>

  
</table>