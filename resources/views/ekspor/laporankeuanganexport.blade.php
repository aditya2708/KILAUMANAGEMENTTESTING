<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>


    <table>
        <tr><th colspan="4" style="text-align: center; "><h1><b>{{ strtoupper($kompani) }}</b></h1></th></tr>
        <tr><th colspan="4" style="text-align: center;"><h1>{{ $title }}</h1></th></tr>
        <tr><th colspan="4" style="text-align: center;"><h1>Tahun {{ $tahunlalu }} Dan Tahun {{ $tahunini }}</h1></th></tr>
    <tr></tr>
    <thead>
    <tr>
        <th style="border: 1px solid black; background-color: #96D4D4;">#</th>
        <th style="border: 1px solid black; background-color: #96D4D4;">Nama</th>
        <th style="border: 1px solid black; background-color: #96D4D4;"><h1> {{ $tahunini }} </h1></th>
        <th style="border: 1px solid black; background-color: #96D4D4;"><h1> {{ $tahunlalu }} </h1></th>
    </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($data as $val)
            <tr>
                <td style="border: 1px solid black;">{{$no++}}</td>
                <td style="border: 1px solid black;">
                @if($val->perent == 'y') 
                    <b>{{$val->nama}}</b> 
                @else 
                    {{$val->nama}}
                @endif</td>
                <td style="border: 1px solid black;">{{$val->saldo1}}</td>
                <td style="border: 1px solid black;">{{$val->saldo2}}</td>
            </tr>
        @endforeach
            
        
    </tbody>
</table>




  