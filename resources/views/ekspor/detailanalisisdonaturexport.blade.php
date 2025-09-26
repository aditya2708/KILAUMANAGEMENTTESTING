<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="4" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
    @if($analis == 'warn')
        <tr><th colspan="4" style="text-align: center; text-transform: uppercase;"><h1 ><b> Detail Analisis Donatur {{ $pilihan }} Unit {{ $bulan }}</b></h1></th></tr>
    @else
        <tr><th colspan="4" style="text-align: center; text-transform: uppercase;"><h1 ><b> Detail Analisis Donatur {{ $pilihan }} Bulan {{ $bulan }} Tahun {{ $tahun }}</b></h1></th></tr>
    @endif
    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nama</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Alamat</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kantor</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jalur</th>
             <th style="border: 1px solid black; background-color: #96D4D4;">Pembayaran</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jenis Donatur</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">No HP</th>
        </tr>
    </thead>
    
    <tbody>
        @foreach($data as $item)
            <tr>
                <td style="border: 1px solid black;">{{$item['nama']}}</td>
                <td style="border: 1px solid black;">{{$item['alamat']}}</td>
                <td style="border: 1px solid black;">{{$item['unit']}}</td>
                <td style="border: 1px solid black;">{{$item['jalur']}}</td>
                <td style="border: 1px solid black;">{{$item['pembayaran']}}</td>
                <td style="border: 1px solid black;">{{$item['jenis_donatur']}}</td>
                <td style="border: 1px solid black;">{{$item['no_hp']}}</td>
            </tr>
        @endforeach
    </tbody>

  
</table>


  

