


<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>



<table>
    <tr><th colspan="4" style="text-align: center; text-transform: uppercase;"><h1 ><b>{{ $company }}</b></h1></th></tr>
    <tr><th colspan="4" style="text-align: center; text-transform: uppercase;"><h1 >{{ $priode }}</h1></th></tr>
    <tr></tr>
    
    <thead>
        <tr>
            <th style="border: 1px solid black; background-color: #96D4D4;">Program</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Transaksi</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Quantity</th>
            <th style="border: 1px solid black; background-color: #96D4D4;">Dana Pengelola</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $item)
            <tr>
                <td style="border: 1px solid black;">{{ $item->program }}</td>
                <td style="border: 1px solid black;">{{ $item->jumlah }}</td>
                <td style="border: 1px solid black;">{{ $item->jmls }}</td>
                <td style="border: 1px solid black;">{{ $item->tot }}</td>
            </tr>
        @endforeach
    </tbody>
</table>