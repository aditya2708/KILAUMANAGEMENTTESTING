<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="10" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company->name }}</b></h1></th></tr>

    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kode Akun</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nama Akun</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Saldo Awal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Debet Mutasi</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kredit Mutasi</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Neraca Saldo</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Debet Disesuaikan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kredit Disesuaikan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Neraca Disesuaikan</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Clossed</th>
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
                <td style="border: 1px solid black;">{{ $item['saldo_awal'] }}</td>
                <td style="border: 1px solid black;">{{ $item['debit'] }}</td>
                <td style="border: 1px solid black;">{{ $item['kredit'] }}</td>
                <td style="border: 1px solid black;">{{ $item['neraca_saldo'] }}</td>
                <td style="border: 1px solid black;">{{ $item['debit_s'] }}</td>
                <td style="border: 1px solid black;">{{ $item['kredit_s'] }}</td>
                <td style="border: 1px solid black;">{{ $item['neraca_s'] }}</td>
                <td style="border: 1px solid black;">{{ $item['closed'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>