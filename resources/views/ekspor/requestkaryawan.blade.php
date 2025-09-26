
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
					
                <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Nama</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Jabatan</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Status</th>
        </tr>
    </thead>
    <tbody>
        @php $number = 1; 
        @endphp
        @foreach($data as $i => $item)
            <tr>
                <td style="border: 1px solid black;">{{ $number++ }}</td>
                <td style="border: 1px solid black;">{{ $item->tg_mulai }}</td>
                <td style="border: 1px solid black;">{{ $item->nama }}</td>
                <td style="border: 1px solid black;">{{ $item->jabatan }}</td>
                <td style="border: 1px solid black;">{{ $item->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
