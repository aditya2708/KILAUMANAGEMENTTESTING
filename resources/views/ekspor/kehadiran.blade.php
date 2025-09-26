

<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>


@if($kondisi == 'kehadiran')
    <table>
        <tr><th colspan="5" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
        <tr><th colspan="5" style="text-align: center; text-transform: uppercase;"><h1 >KEHADIRAN</h1></th></tr>
        <tr><th colspan="5" style="text-align: center; text-transform: uppercase;"><h1 >{{ $priode }}</h1></th></tr>
        <tr></tr>
        
        <thead>
            <tr>
                <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">ID Karyawan</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Nama Karyawan</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($data as $i => $item)
                <tr>
                    <td style="border: 1px solid black;">{{ $no++ }}</td>
                    <td style="border: 1px solid black;">{{ $item['id_karyawan'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['created_at'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['nama'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($kondisi == 'rekap')
    <table>
        <tr><th colspan="10" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
        <tr><th colspan="10" style="text-align: center; text-transform: uppercase;"><h1 >KEHADIRAN</h1></th></tr>
        <tr><th colspan="10" style="text-align: center; text-transform: uppercase;"><h1 >{{ $priode }}</h1></th></tr>
        <tr></tr>
        
        <thead>
            <tr>
                <th style="border: 1px solid black; background-color: #96D4D4;">Nama Karyawan</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">ID Karyawan</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Jabatan</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Hadir</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Sakit</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Terlambat</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Perdin</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Bolos</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Cuti</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Cuti Penting</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $item)
                <tr>
                    <td style="border: 1px solid black;">{{ $item['nama'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['id_karyawan'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['jabatan'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['jum_hadir'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['jum_sakit'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['jum_telambat'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['jum_perdin'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['jum_bolos'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['jum_cuti'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['jum_cuti_penting'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($kondisi == 'detail')

    <table>
        <tr><th colspan="6" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
        <tr><th colspan="6" style="text-align: center; text-transform: uppercase;"><h1 >KEHADIRAN {{ $nama }}</h1></th></tr>
        <tr><th colspan="6" style="text-align: center; text-transform: uppercase;"><h1 >{{ $priode }}</h1></th></tr>
        <tr></tr>
        
        <thead>
            <tr>
                <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Jam Masuk</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Jam Pulang</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Status</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $number = 1; 
            @endphp
            @foreach($data as $i => $item)
            
             @php
            $tanggal = date('d-m-Y', strtotime($item['created_at']));
            @endphp
                <tr>
                    <td style="border: 1px solid black;">{{ $number++ }}</td>
                    <td style="border: 1px solid black;">{{ $tanggal }}</td>
                    <td style="border: 1px solid black;">{{ $item['cek_in'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['cek_out'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['status'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['ket'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif