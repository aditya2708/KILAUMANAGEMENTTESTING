
<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>


    <table>
        <tr><th colspan="16" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
        <tr><th colspan="16" style="text-align: center; text-transform: uppercase;"><h1 >DATA KARYAWAN</h1></th></tr>
        <tr></tr>
        

        
        <thead>
            <tr>
                <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">NIK</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">ID Karyawan</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Nama</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Jabatan</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Email</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">NO HP</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">TGL Lahir</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">JK</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Alamat</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Pendidikan</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Sekolah</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Tahun Lulus</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Jurusan</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Unit Kerja</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">ID Kantor</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($data as $i => $item)
                <tr>
                    <td style="border: 1px solid black;">{{ $no++ }}</td>
                    <td style="border: 1px solid black;">{{ $item['nik'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['id'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['nama'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['jabatan'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['email'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['nomerhp'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['tgl_lahir'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['jk'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['alamat'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['pendidikan'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['nm_sekolah'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['th_lulus'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['jurusan'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['unit_kerja'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['id_kantor'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>