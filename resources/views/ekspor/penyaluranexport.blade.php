<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="8" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
    <tr><th colspan="8" style="text-align: center;"><h1>Priode {{ $priode }}</h1></th></tr>

    <tr></tr>
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">ID Salur</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">ID PM</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Penerima Manfaat</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Program</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nominal</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tgl Mohon</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Tgl Salur</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kantor Salur</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $no = 1; 
        @endphp
        
        @foreach($data as $i => $item)
                
            <tr>
                <!--<td style="border: 1px solid black;">{{ $no++}}</td>-->
                <td style="border: 1px solid black;">{{ $item['IDSalur'] }}</td>
                <td style="border: 1px solid black;">{{ $item['IDPM'] }}</td>
                <td style="border: 1px solid black;">{{ $item['penerimaManfaat'] }}</td>
                <td style="border: 1px solid black;">{{ $item['program'] }}</td>
                <td style="border: 1px solid black;">{{ $item['nominal'] }}</td>
                <td style="border: 1px solid black;">{{ $item['tglMohon'] }}</td>
                <td style="border: 1px solid black;">{{ $item['tglSalur'] }}</td>
                <td style="border: 1px solid black;">{{ $item['kantorSalur'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>