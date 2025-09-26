
<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>


    <table>
        <tr><th colspan="10" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
        <tr><th colspan="10" style="text-align: center; text-transform: uppercase;"><h1 >DETAIL BATAL CLOSING</h1></th></tr>
        <tr><th colspan="10" style="text-align: center; text-transform: uppercase;"><h1 >{{ $priode }}</h1></th></tr>
        <tr></tr>

        <thead>
            <tr>
                <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Tanggal</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Nominal</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">COA Debit</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">COA Kredit</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Via Input</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Penanggung Jawab</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Dibuat</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Diubah</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Dihapus</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($data as $i => $item)
                <tr>
                    <td style="border: 1px solid black;">{{ $no++ }}</td>
                    <td style="border: 1px solid black;">{{ $item['tgl'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['nominal'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['coa_debet'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['coa_kredit'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['via_input'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['user_pj'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['dibuat'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['diubah'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['dihapus'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>