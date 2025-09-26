<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="11" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $kompani }}</b></h1></th></tr>
    <tr><th colspan="11" style="text-align: center;"><h1>Donatur {{ $ahhh }}</h1></th></tr>
    <tr><th colspan="11" style="text-align: center;"><h1>{{ $periode }}</h1></th></tr>

    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Petugas</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Donatur</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Program</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nomor HP</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Alamat</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Jalur</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kota</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Status</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Dikolek</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Registrasi</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $no = 1; 
            $outputArray = [];
            $ih = [];
        @endphp
        
        
        @foreach($data as $item)
            <?php $xx = App\Models\Prosp::select('prosp.id_prog')->where('id_don', $item->id)->where('prosp.ket', 'closing')->get(); ?>
            
            @if(count($xx) > 0)
                
                @foreach($xx as $val => $key)
                            <?php $aw = App\Models\Prog::select('program')->where('id_program',  $key->id_prog)->first(); ?>
                            <?php $ih = [$aw->program];  ?>
                @endforeach
                
                @php $eh = implode(",", $ih); @endphp
            @else
                @php $eh = ''; @endphp
            @endif
            
            
            <tr>
                <td style="border: 1px solid black;">{{ $no++}}</td>
                <td style="border: 1px solid black;">{{ $item->petugas }}</td>
                <td style="border: 1px solid black;">{{ $item->nama }}</td>
                <td style="border: 1px solid black;">{{$eh}}</td>
                <td style="border: 1px solid black;">{{ $item->no_hp }}</td>
                <td style="border: 1px solid black;">{{ $item->alamat }}</td>
                <td style="border: 1px solid black;">{{ $item->jalur }}</td>
                <td style="border: 1px solid black;">{{ $item->kota }}</td>
                <td style="border: 1px solid black;">{{ $item->status }}</td>
                <td style="border: 1px solid black;">{{ $item->dikolek }}</td>
                <td style="border: 1px solid black;">{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>