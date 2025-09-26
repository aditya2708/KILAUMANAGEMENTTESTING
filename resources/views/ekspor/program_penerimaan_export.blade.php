<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>

<table>
    <tr><th colspan="7" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $kompani }}</b></h1></th></tr>
    <tr><th colspan="7" style="text-align: center;"><h1>Data Program Penerimaan</h1></th></tr>

    <tr></tr>
    
    <thead>
        <tr>
        
            <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Program</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Sumber Dana</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Bagian(%)</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">COA Individu</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">COA Entitas</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Aktif</th>
        
        </tr>
    </thead>
    <tbody>
        @php 
            $no = 1;
        @endphp
        
        
        @foreach($data as $item)
                
            <tr>
                <td style="border: 1px solid black;">{{ $no++}}</td>
                <td style="border: 1px solid black;">{{ $item['program'] }}</td>
                <td style="border: 1px solid black;">{{ $item['sumber_dana'] }}</td>
                <td style="border: 1px solid black;">{{ $item['dp'] }}</td>
                <td style="border: 1px solid black;">{{ $item['coa1'] }}</td>
                <td style="border: 1px solid black;">{{ $item['coa2'] }}</td>
                <td style="border: 1px solid black;">{{ $item['aktif'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>