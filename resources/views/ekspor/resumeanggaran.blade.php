


<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>


<table>
    <tr><th colspan="12" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
    <tr><th colspan="12" style="text-align: center; text-transform: uppercase;"><h1 >{{ $priode }}</h1></th></tr>
    <tr></tr>
    <thead>
        
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">COA</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nama Akun</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Anggaran</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Relokasi</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tambahan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Total[T]</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Realisasi[R]</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">%[R/T]</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Sisa Anggaran</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jabatan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kantor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $item)
            <tr>
                <td style="border: 1px solid black;">{{ $item->tanggal }}</td>
                <td style="border: 1px solid black;">{{ $item->coa }}</td>
                <td style="border: 1px solid black;">{{ $item->nama_akun }}</td>
                <td style="border: 1px solid black;">{{ $item->anggaran }}</td>
                <td style="border: 1px solid black;">{{ $item->relokasi }}</td>
                <td style="border: 1px solid black;">{{ $item->tambahan }}</td>
                <td style="border: 1px solid black;">{{ $item->tot}}</td>
                <td style="border: 1px solid black;">{{ $item->realisasi }}</td>
                <td style="border: 1px solid black;">{{ $item->realisasi / $item->tot * 100 }}</td>
                <td style="border: 1px solid black;">{{ $item->anggaran + $item->tambahan + $item->relokasi - $item->realisasi }}</td>
                <td style="border: 1px solid black;">{{ $item->jabatan }}</td>
                <td style="border: 1px solid black;">{{ $item->unit }}</td>
            </tr>
        @endforeach
    </tbody>
</table>