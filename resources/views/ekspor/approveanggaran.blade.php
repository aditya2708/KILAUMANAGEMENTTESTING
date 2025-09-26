


<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>


<table>
    <tr><th colspan="16" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
    <tr><th colspan="16" style="text-align: center; text-transform: uppercase;"><h1 >{{ $priode }}</h1></th></tr>
    <tr></tr>
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nama Akun</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">COA</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Keterangan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Anggaran</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Relokasi</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tambahan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">ID Kantor</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jabatan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Referensi</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">User Input</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Keuangan Approver</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Direktur Approver</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">User Reject</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Alasan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Acc</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $item)
            <tr>
                <td style="border: 1px solid black;">{{ $item->tanggal }}</td>
                <td style="border: 1px solid black;">{{ $item->nama_akun }}</td>
                <td style="border: 1px solid black;">{{ $item->coa }}</td>
                <td style="border: 1px solid black;">{{ $item->keterangan }}</td>
                <td style="border: 1px solid black;">{{ $item->anggaran }}</td>
                <td style="border: 1px solid black;">{{ $item->relokasi }}</td>
                <td style="border: 1px solid black;">{{ $item->tambahan }}</td>
                <td style="border: 1px solid black;">{{ $item->unit }}</td>
                <td style="border: 1px solid black;">{{ $item->jabatan }}</td>
                <td style="border: 1px solid black;">{{ $item->referensi }}</td>
                <td style="border: 1px solid black;">{{ $item->user_input }}</td>
                <td style="border: 1px solid black;">{{ $item->user_approve }}</td>
                <td style="border: 1px solid black;">{{ $item->user_approve2 }}</td>
                <td style="border: 1px solid black;">{{ $item->user_reject }}</td>
                <td style="border: 1px solid black;">{{ $item->alasan }}</td>
                <td style="border: 1px solid black;">{{ $item->acc }}</td>
            </tr>
        @endforeach
    </tbody>
</table>