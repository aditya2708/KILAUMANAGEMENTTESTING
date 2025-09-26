@extends('template')

@section('konten')
<div class="content-body">
    <div class="container-fluid">

          <div class="modal fade" id="detail_donatur">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Donatur <span id="mmy"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    
                    <!--<form>-->
                        <div class="modal-body">
                            <div class="btn-group mb-3">
                                 <button type="button" class="btn btn-success btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ekspor
                                 </button>
                                  <ul class="dropdown-menu">
                                        <input type="hidden" id="nyimpen">
                                        <li><button class="dropdown-item expFile" data-value="xls" value="xls" name="tombol" id="xls" >.XLS</button></li>
                                        <li><button class="dropdown-item expFile" data-value="csv" value="csv" name="tombol" id="csv" >.CSV</button></li>
                                  </ul>
                            </div>
                            <div class="table-responsive">
                                 <table id="user_table_det" class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th >Alamat</th>
                                            <th >Kantor</th>
                                            <th >Jalur</th>
                                            <th >Pembayaran</th>
                                            <th >Jenis Donatur</th>
                                            <th >No HP</th>
                                            <th>Jumlah</th>
                                            <th id="blnnn" style="display: none">Bulan</th>
                                            <th id="jmlhhh" style="display: none">Jumlah</th>
                                        </tr>
                                    </thead>
                                    
                                    </table>
                            </div>
                        </div>
                    <!--</form>-->
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="basic-form">
                                    <div class="row">
                                        <div class="col-md-2 mb-3">
                                            <div class="form-group">
                                                <label>Analisis dengan </label>
                                                <select required class="form-control default-select wide cek1" name="analis" id="analis" style="width: 100%">
                                                    <option value="" disable selected="selected">Pilih Analisis</option>
                                                    <option value="cara_bayar">Cara Bayar</option>
                                                    <!--<option value="bulan">Bulan Transaksi</option>-->
                                                    <option value="kantor" {{ Auth::user()->level == 'admin' ? "selected" : "" }}>Kantor</option>
                                                    <option value="jenis">Jenis Donatur</option>
                                                    <!--<option value="status">Status</option>-->
                                                    <option value="jalur">Jalur</option>
                                                    <option value="warn">Data Merah</option>
                                                    <!--<option value="petugas" {{ Auth::user()->level == 'kacab' ? "selected" : "" }}>Petugas</option>-->
                                                    <!--<option value="status">Status Transaksi</option>-->
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <input id="tahunini" value="{{date('Y')}}" type="hidden">

                                        <div class="col-md-2 mb-3" id="kntrd" style="display: none">
                                            <div class="form-group">
                                                <label>Kantor</label>
                                                <select required class="form-control cek3" name="kntr" id="kntr" style="width: 100%">
                                                    @foreach($kantor as $k)
                                                    <option value="{{$k->id}}">{{$k->unit}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                <div class="col-md-3 mb-3" id="progg" style="display: none">
                                    <label>Program</label>
                                    <div class="form-group">
                                        <select id="program" class="crot ceksi" style="width:100%" name="program">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2 mb-3" id="jumbull" style="display: none">
                                    <label>Jumlah Bulan</label>
                                    <div class="form-group">
                                        <input type="number" min="0" name="jumbul" id="jumbul" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="col-md-2 mb-3" id="mindonn" style="display: none">
                                    <label>Minimal Donasi</label>
                                    <div class="form-group">
                                        <input type="number" min="0" name="mindon" id="mindon" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="col-md-2 mb-3" id="btnn" style="display: none">
                                    <label>&nbsp;</label>
                                    <div class="form-group">
                                        <button type="button" id="upp" class="btn btn-sm btn-success" >Filter</button>
                                    </div>
                                </div>

                                <div class="col-md-2 mb-3" id="thnn">
                                    <label>&nbsp;</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control year cek2" name="tahun" id="tahun" autocomplete="off" placeholder="{{date('Y') }}">
                                    </div>
                                </div>
                                        
                                                                           
                                <div class="p-2 bd-highlight" id="kntrr">
                                    <label class="form-label">Kantor</label>
                                    <div id="kanone" style="width: 200px;">
                                        <select id="skntr" class="form-control cek5" name="skntr" >
                                               @foreach($kantor as $k)
                                                    <option value="{{$k->id}}">{{$k->unit}}</option>
                                                @endforeach
                                        </select> 
                                    </div>
                                    <div id="kanmul" style="width: 200px;">
                                        <select multiple="multiple" id="mulkntr" class="form-control cek5" name="mulkntr[]" >
                                                @foreach($kantor as $k)
                                                    <option value="{{$k->id}}">{{$k->unit}}</option>
                                                @endforeach
                                        </select> 
                                    </div>
                                    
                                    <div class="checkbox" style="margin-top: 5px; margin-left: 0px">
                                          <label><input type="checkbox" class="cek5" name="mulkan" id="mulkan"> Multiple</label>
                                    </div>
                                </div>
                                        
                                        
                                        <!-- <div class="col-md-2 mb-3" >-->
                                        <!--    <div >-->
                                        <!--        <label>Kantor</label>-->
                                             
                                                
                                        <!--        <select class="form-control cek5" name="mulkntr[]" id="mulkntr" style="width: 100% " multiple="multiple">-->
                                        <!--            @foreach($kantor as $k)-->
                                        <!--            <option value="{{$k->id}}">{{$k->unit}}</option>-->
                                        <!--            @endforeach-->
                                        <!--        </select>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Grafik Analis</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <figure class="highcharts-figure">
                                                    <div id="container" style="height: 450px; min-width: 310px; display: block"></div>
                                                </figure>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection