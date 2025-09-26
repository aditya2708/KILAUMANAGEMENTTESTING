<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="13" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
    <tr><th colspan="13" style="text-align: center; text-transform: uppercase;">Periode {{ $priode }}</th></tr>

    <tr></tr>
    <thead>
        <tr>
            <th  style="border: 1px solid black; background-color: #96D4D4;">#</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Bulan</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Nama</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Jabatan</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Kantor</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Status Karyawan</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Gaji Pokok</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Tunjangan Jabatan</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Tunjangan Daerah</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Tunjangan Anak</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Tunjangan Pasangan</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Tunjangan Beras</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Transport</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Bonus</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Jumlah Hari</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Total</th>
            <th  style="border: 1px solid black; background-color: #96D4D4;">Take Home Pay</th>
        </tr>
    </thead>
        <tbody>
        @php $no = 1; @endphp
        @foreach($data as $item)
            <tr>
                <td style="border: 1px solid black;">{{$no++}}</td>
                <td style="border: 1px solid black;">{{ ($item->created_at)->isoFormat('MMMM Y') }}</td>
                <td style="border: 1px solid black;">{{ $item->nama }}</td>
                <td style="border: 1px solid black;">{{ $item->jabatan }}</td>
                <td style="border: 1px solid black;">{{ $item->unit }}</td>
                <td style="border: 1px solid black;">{{ $item->status_kerja}}</td>
                <td style="border: 1px solid black;">{{ $item->gapok }}</td>
                <td style="border: 1px solid black;">{{ $item->tj_jabatan }}</td>
                <td style="border: 1px solid black;">{{ $item->tj_daerah }}</td>
                <td style="border: 1px solid black;">{{ $item->tj_anak }}</td>
                <td style="border: 1px solid black;">{{ $item->tj_pasangan }}</td>
                <td style="border: 1px solid black;">{{ $item->tj_beras }}</td>
                <td style="border: 1px solid black;">{{ $item->transport }}</td>
                <td style="border: 1px solid black;">{{ $item->bonus }}</td>
                <td style="border: 1px solid black;">{{ $item->jml_hari }}</td>
                <td style="border: 1px solid black;">{{ $item->total }}</td>
                <td style="border: 1px solid black;">{{ $item->thp }}</td>
            </tr>
        @endforeach
        </tbody>
</table>