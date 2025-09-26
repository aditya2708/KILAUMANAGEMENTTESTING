<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Donatur</th>
            <th>Total</th>
            <th>Jan</th>
            <th>Feb</th>
            <th>Mar</th>
            <th>Apr</th>
            <th>Mei</th>
            <th>Jun</th>
            <th>Jul</th>
            <th>Agu</th>
            <th>Sep</th>
            <th>Okt</th>
            <th>Nov</th>
            <th>Des</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
    @foreach($data as $item)
        <tr>
            <td>{{$no++}}</td>
            <td>{{ $item->donatur }}</td>
            <td>{{ $item->jumlah }}</td>
            <td>{{ $item->jumlah1 }}</td>
            <td>{{ $item->jumlah2 }}</td>
            <td>{{ $item->jumlah3 }}</td>
            <td>{{ $item->jumlah4 }}</td>
            <td>{{ $item->jumlah5 }}</td>
            <td>{{ $item->jumlah6 }}</td>
            <td>{{ $item->jumlah7 }}</td>
            <td>{{ $item->jumlah8 }}</td>
            <td>{{ $item->jumlah9 }}</td>
            <td>{{ $item->jumlah10 }}</td>
            <td>{{ $item->jumlah11 }}</td>
            <td>{{ $item->jumlah12 }}</td>
        </tr>
    @endforeach
    </tbody>
</table>