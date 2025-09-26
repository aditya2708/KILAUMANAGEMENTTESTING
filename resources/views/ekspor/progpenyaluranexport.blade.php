<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="4" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company->name }}</b></h1></th></tr>
    <tr></tr>
         <thead>
            <tr>
                <th style="border: 1px solid black; background-color: #96D4D4;">Program</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Sumber Dana</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">COA Individu</th>
                <th style="border: 1px solid black; background-color: #96D4D4;">Aktif</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $item)
                 <tr>
                    <td style="border: 1px solid black;">{{ $item['program'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['sumber_dana'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['coa_individu'] }}</td>
                    <td style="border: 1px solid black;">{{ $item['aktif'] }}</td>
                </tr>
            @endforeach
        </tbody>
   
</table>