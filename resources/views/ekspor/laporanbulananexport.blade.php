<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="15" style="text-align: center; "><h1><b>{{ strtoupper($kompani) }}</b></h1></th></tr>
    <tr><th colspan="15" style="text-align: center;"><h1>{{ $judul }}</h1></th></tr>
    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">#</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nama</th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>{{ $tahunini }}</h1></th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>Januari - {{ $tahunini }}</h1></th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>Febuari - {{ $tahunini }}</h1></th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>Maret - {{ $tahunini }}</h1></th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>April - {{ $tahunini }}</h1></th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>Mei - {{ $tahunini }}</h1></th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>Juni - {{ $tahunini }}</h1></th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>Juli - {{ $tahunini }}</h1></th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>Agustus - {{ $tahunini }}</h1></th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>September - {{ $tahunini }}</h1></th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>Oktober - {{ $tahunini }}</h1></th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>Novermber - {{ $tahunini }}</h1></th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>Desember - {{ $tahunini }}</h1></th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @for($i = 0; $i < count($nama); $i++)
            <tr>
                <td style="border: 1px solid black;">{{$no++}}</td>
                <td style="border: 1px solid black;">{{ $nama[$i]->nama }}</td>
                <td style="border: 1px solid black;">{{ $tahundipilih[$i] }}</td>
                <td style="border: 1px solid black;">{{ $januari[$i] }}</td>
                <td style="border: 1px solid black;">{{ $febuari[$i] }}</td>
                <td style="border: 1px solid black;">{{ $maret[$i] }}</td>
                <td style="border: 1px solid black;">{{ $april[$i] }}</td>
                <td style="border: 1px solid black;">{{ $mei[$i] }}</td>
                <td style="border: 1px solid black;">{{ $juni[$i] }}</td>
                <td style="border: 1px solid black;">{{ $juli[$i] }}</td>
                <td style="border: 1px solid black;">{{ $agustus[$i] }}</td>
                <td style="border: 1px solid black;">{{ $september[$i] }}</td>
                <td style="border: 1px solid black;">{{ $oktober[$i] }}</td>
                <td style="border: 1px solid black;">{{ $november[$i] }}</td>
                <td style="border: 1px solid black;">{{ $desember[$i] }}</td>

            </tr>
        @endfor
            
        
    </tbody>
</table>


  