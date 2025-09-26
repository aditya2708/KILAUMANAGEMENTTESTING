<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="4" style="text-align: center; "><h1><b>{{ strtoupper($kompani) }}</b></h1></th></tr>
    <tr><th colspan="4" style="text-align: center;"><h1>{{ $judul }}</h1></th></tr>
    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">Nama Peyajian</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Rumus</th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>Level</h1></th>
            <th style="border: 1px solid black; background-color: #96D4D4;"><h1>Urutan</h1></th>
        </tr>
    </thead>
    
      <tbody>
        @foreach($data as $i => $item)
            <tr>
                <td style="border: 1px solid black;">{{ $item->nama }}</td>
                <td style="border: 1px solid black;">{{ $item->rumus }}</td>
                <td style="border: 1px solid black;">{{ $item->level }}</td>
                <td style="border: 1px solid black;">{{ $item->urutan }}</td>
            </tr>
        @endforeach
    </tbody>
</table>


  