


<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>

    <tr><th colspan="7" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company->name }}</b></h1></th></tr>
    <tr><th colspan="7" style="text-align: center; text-transform: uppercase;"><h1 ></h1></th></tr>
    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nama Petugas</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jabatan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Open</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Closing</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Cancel</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Total</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($data as $i => $item)
            <tr>
                <td style="border: 1px solid black;">{{ $no++ }}</td>
                <td style="border: 1px solid black;">{{ $item->name }}</td>
                <td style="border: 1px solid black;">{{ $jabatan[$i]['jabatan'] }}</td>
                <td style="border: 1px solid black;">{{ $item->open }}</td>
                <td style="border: 1px solid black;">{{ $item->closing }}</td>
                <td style="border: 1px solid black;">{{ $item->cancel }}</td>
                <td style="border: 1px solid black;">{{ $item->open + $item->cancel + $item->closing; }}</td>
            </tr>
        @endforeach
    </tbody>
</table>