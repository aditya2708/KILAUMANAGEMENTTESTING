<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>

@if($vs == 'no')
    @if($field == 'program')
        <?php $aw = 4 ?>
    @else
        <?php $aw = 5 ?>
    @endif
@else
    @if($field == 'program')
        <?php $aw = 6 ?>
    @else
        <?php $aw = 8 ?>
    @endif
@endif

<table>
    <tr><th colspan="{{ $aw }}" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $kompani }}</b></h1></th></tr>
    <tr><th colspan="{{ $aw }}" style="text-align: center;"><h1>Report Transaksi {{$berdasarkan}}</h1></th></tr>
    <tr><th colspan="{{ $aw }}" style="text-align: center;"><h1>{{ $period }}</h1></th></tr>

    <tr></tr>
    
    <thead>
        <tr>
        @if($vs == 'no')
            @if($field == 'program')
            <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nama Program</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Omset 1</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">% Growth</th>
            @else
            <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kantor</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Target</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Omset 1</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">∑ Transaksi 1</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">% Growth</th>
            @endif
        @else
            @if($field == 'program')
            <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nama Program</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Omset 1</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Omset 2</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">% Growth</th>
            @else
            <th style="border: 1px solid black; background-color: #96D4D4;">No</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Kantor</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Target</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Omset 1</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">∑ Transaksi 1</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Omset 2</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">∑ Transaksi 2</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">% Growth</th>
            @endif
        @endif
        </tr>
    </thead>
    <tbody>
        @php 
            $no = 1;
        @endphp
        
        
        @foreach($data as $item)
                
            <tr>
                @if($vs == 'no')
                    @if($field == 'program')
                    <td style="border: 1px solid black;">{{ $no++}}</td>
                    <td style="border: 1px solid black;">{{ $item->program }}</td>
                    <td style="border: 1px solid black;">{{ $item->Omset }}</td>
                    <td style="border: 1px solid black;">{{ $item->growth }}</td>
                    @else
                    <td style="border: 1px solid black;">{{ $no++}}</td>
                    <td style="border: 1px solid black;">{{ $item->unit }}</td>
                    <td style="border: 1px solid black;">{{ $item->target_dana }}</td>
                    <td style="border: 1px solid black;">{{ $item->Omset }}</td>
                    <td style="border: 1px solid black;">{{ $item->jum1 }}</td>
                    <td style="border: 1px solid black;">{{ $item->growth }}</td>
                    @endif
                @else
                    @if($field == 'program')
                    <td style="border: 1px solid black;">{{ $no++}}</td>
                    <td style="border: 1px solid black;">{{ $item->program }}</td>
                    <td style="border: 1px solid black;">{{ $item->Omset }}</td>
                    <td style="border: 1px solid black;">{{ $item->Omset2 }}</td>
                    <td style="border: 1px solid black;">{{ $item->growth }}</td>
                    @else
                    <td style="border: 1px solid black;">{{ $no++}}</td>
                    <td style="border: 1px solid black;">{{ $item->unit }}</td>
                    <td style="border: 1px solid black;">{{ $item->target_dana }}</td>
                    <td style="border: 1px solid black;">{{ $item->Omset }}</td>
                    <td style="border: 1px solid black;">{{ $item->Omset2 }}</td>
                    <td style="border: 1px solid black;">{{ $item->jum1 }}</td>
                    <td style="border: 1px solid black;">{{ $item->jum2 }}</td>
                    <td style="border: 1px solid black;">{{ $item->growth }}</td>
                    @endif
                @endif
            </tr>
        @endforeach
    </tbody>
</table>