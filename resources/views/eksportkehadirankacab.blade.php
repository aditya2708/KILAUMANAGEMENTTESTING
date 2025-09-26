<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Keterlambatan</th>
            <th>Status</th>
            <th>Jumlah_Hari</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
    @foreach($data as $item)
        <tr>
            <td>{{$no++}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>