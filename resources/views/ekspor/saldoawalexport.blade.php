<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="9" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company->name }}</b></h1></th></tr>

    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">COA</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nama COA</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Saldo Akhir</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kantor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $item)
             <tr>
                <td style="border: 1px solid black;">{{ $item['coa'] }}</td>
                @if($item['parent'] == 'y')
                    <td style="border: 1px solid black;"><b>{{ $item['nama_coa'] }}</b></td>
                @else
                    <td style="border: 1px solid black;">{{ $item['nama_coa'] }}</td>
                @endif
                <td style="border: 1px solid black;">{{ $item['tanggals'] }}</td>
                <td style="border: 1px solid black;">{{ $item['saldo_awal'] }}</td>
                <td style="border: 1px solid black;">{{ $item['kantor'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>