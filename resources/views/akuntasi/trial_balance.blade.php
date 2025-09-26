@extends('template')
@section('konten')
<style>

.fixed-table-container .fixed-table-body table tr td:nth-child(1){
    max-width: 500px; 
    overflow: hidden;
    div-overflow: ellipsis;
    white-space: nowrap; 
}
.fixed-table-container .fixed-table-body table tr td:nth-child(2){
    max-width: 300px; 
    overflow: hidden;
    div-overflow: ellipsis;
    white-space: nowrap; 
}

.select2-container .select2-selection--multiple,
.select2-container .select2-selection--single {
    min-height: 3rem; /* or your desired height */
    border-radius: 1rem;
}
.under:hover {
    div-decoration-line: underline;
    
}
.ph-item,
  .ph-picture {
    width: 100%;
    height: 100%;
    padding: 0;
    margin: 0;
  }

  .table-contain {
      position: relative;
      /*overflow-x: hidden;*/
      /*div-overflow: ellipsis;*/
      
      /*white-space: nowrap;*/
    /*width: 100%;*/
   
  }
</style>
<div class="content-body">
    
    <div class="container-fluid">
        <!--modal detail-->
        <div class="modal fade" id="modal-batal-closing"> 
                <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="card-title">Detail Data&nbsp;</h4><h4 class="card-title" id="head"></h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                            <div class="modal-body">
                                
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Tanggal</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                        <div style="display: block" >
                                            <div id="valTgl"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">User Input</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                        <div style="display: block" >
                                            <div id="valUsIn"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Pembayaran</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                        <div style="display: block" >
                                            <div id="valPemb"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-sm-4 " id="pengirim">Pengirim</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                        <div style="display: block" >
                                            <div id="valPeng"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-4 " id="penerima">Penerima</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                        <div style="display: block" >
                                            <div id="valPen"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Nominal</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                        <div style="display: block" >
                                            <div id="valNom"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Keterangan</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                        <div style="display: block" >
                                            <div id="valKet"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Bukti</label>
                                    <label class="col-sm-1 ">:</label>
                                   <div class="col-sm-6">
                                        <div style="display: block">
                                            <div id="valBuk"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-sm-4 " id="user"></label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                        <div style="display: block" >
                                            <div id="valUsCon"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 row"id="alasan">
                                    <label class="col-sm-4 " >Alasan</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                        <div style="display: block" >
                                            <div id="valAlasan"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        
        <!--Modal-->
        <div class="modal fade" id="modal_canclos">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="card-title">Detail Batal Closing&nbsp;</h4><div class="card-title" id="tit_canclos"></div> 
                        <div class="btn-group">
                             <button type="button" class="btn btn-success ms-2 btn-xs dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false" fdprocessedid="as2bbg">
                                Ekspor
                            </button>
                            <ul class="dropdown-menu">
                                <li><button class="dropdown-item" value="xlsBatClos" name="tombol" id="xlsDet">.XLS</button></li>
                                <li><button class="dropdown-item" value="csvBatClos" name="tombol" id="csvDet">.CSV</button></li>
                            </ul>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                        <div class="modal-body">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <!--<div class="btn-group mb-3">-->
                                    <!--     <button type="button" class="btn btn-success btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">-->
                                    <!--        Ekspor-->
                                    <!--    </button>-->
                                    <!--    <ul class="dropdown-menu">-->
                                    <!--        <li><button class="dropdown-item" value="xls" name="tombol" id="xls">.XLS</button></li>-->
                                    <!--        <li><button class="dropdown-item"  value="csv" name="tombol" id="csv">.CSV</button></li>-->
                                    <!--    </ul>-->
                                    <!--</div>-->
                                    <table id="canclos_table" class="table table-striped" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>Tanggal</th>
                                                <th>Nominal</th>
                                                <th>COA Debit</th>
                                                <th>COA Kredit</th>
                                                <th>Via Input</th>
                                                <th>Penanggung Jawab</th>
                                                <th>Dibuat</th>
                                                <th>Diubah</th>
                                                <th>Dihapus</th>
                                                <!--<th>ID Buku</th>-->
                                                <!--<th>#ID</th>-->
                                            </tr>
                                        </thead>
                                        <tbody id="tab_canclos">
                                        </tbody>
                                        <tfoot>
                                        <tr><th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <!--<th id="totals1"></th>-->
                                        </tr>
                                    </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modal-default2"> 
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                             <h4 class="card-title">Detail Debit&nbsp;</h4> <div class="card-title" id="dDebit"></div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                            <div class="modal-body">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="btn-group mb-3">
                                             <button type="button" class="btn btn-success btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">
                                                Ekspor
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><button class="dropdown-item expFile" data-value="xls" value="xls" name="tombol" id="xls">.XLS</button></li>
                                                <li><button class="dropdown-item expFile" data-value="csv" value="csv" name="tombol" id="csv">.CSV</button></li>
                                                <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                            </ul>
                                        </div>
                                        <table id="user_table_2" class="table table-striped" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>Tanggal</th>
                                                    <th>COA</th>
                                                    <th>Keterangan</th>
                                                    <th>nominal</th>
                                                    <th>COA Buku</th>
                                                    <!--<th>ID Buku</th>-->
                                                    <!--<th>#ID</th>-->
                                                </tr>
                                            </thead>
                                            <tbody id="tablex">
                                            </tbody>
                                             <tfoot>
                                            <tr><th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th id="totals"></th>
                                            </tr>
                                        </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            
        <div class="modal fade" id="modal-default1">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="card-title">Detail Kredit&nbsp;</h4><div class="card-title" id="dKredit"></div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                            <div class="modal-body">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="btn-group mb-3">
                                             <button type="button" class="btn btn-success btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">
                                                Ekspor
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><button class="dropdown-item" value="xls" name="tombol" id="xls">.XLS</button></li>
                                                <li><button class="dropdown-item"  value="csv" name="tombol" id="csv">.CSV</button></li>
                                                <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                            </ul>
                                        </div>
                                        <table id="user_table_1" class="table table-striped" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>Tanggal</th>
                                                    <th>COA</th>
                                                    <th>Keterangan</th>
                                                    <th>nominal</th>
                                                    <th>COA Buku</th>
                                                    <!--<th>ID Buku</th>-->
                                                    <!--<th>#ID</th>-->
                                                </tr>
                                            </thead>
                                            <tbody id="tablex">
                                            </tbody>
                                            <tfoot>
                                            <tr><th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th id="totals1"></th>
                                            </tr>
                                        </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Trial Balance</h4>
                            <div class="pull-right d-flex gap-2">
                                <div id="coba" style="display: block"></div>
                    <form action="{{ url('trial_balance_export') }}">
                                <div class="btn-group">
                                     <button type="button" class="btn btn-success btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">
                                        Ekspor
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>
                                        <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>
                                        <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                    </ul>
                                </div>
                                
                                <!--    <button class="btn btn-primary btn-sm" onclick="return(alert('sudah closing'))">Closing</button>-->
                                <!--    <button id="save" class="btn btn-primary btn-sm">Closing</button>-->
                            </div>
                        </div>
    
                        <div class="card-body">
                            <!--<div class="table-responsive">-->
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <select class="mb-2" id="inper" name="inper" panelheight="auto" style="width:100px; border:0;">
                                        <option value="b" selected="selected">Bulan</option>
                                        <option value="t">Tahun</option>
                                    </select>
                                    <input type="div" class="form-control dates cek4" name="blns" id="blns" value="{{$tgfil}}" autocomplete="off" placeholder="MM-YYYY" style="display: block">
                                    <input type="div" class="form-control" name="thns" id="thns" value="{{$thfil}}" autocomplete="off" placeholder="YYYY" style="display: none">
                                </div>
                                <!--<div class="col-md-3 mb-3">-->
                                <!--    <label>Bulan&Tahun :</label>-->
                                <!--    <input type="div" class="form-control dates cek4" name="blns" id="blns" value="{{$tgfil}}" autocomplete="off" placeholder="MM-YYYY">-->
                                <!--</div>-->
                                <div class="col-md-3 mb-3">
                                    <label>COA</label>
                                    <select class="form-control cek5" name="coa" id="coa">
                                        <option value="">All</option>
                                        <option value="y">Parent</option>
                                        <option value="n">Child</option>
                                    </select>
                                    <!--<input type="div" class="form-control dates cek4" name="blns" id="blns" autocomplete="off" placeholder="contoh {{date('m-Y') }}">-->
                                </div>
                                
                                @if(Auth::user()->name == 'Management')
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Unit</label>
                                    <select class="sel2 form-control cek7" name="unit" id="unit">
                                        @if(Auth::user()->keuangan == 'admin' || Auth::user()->keuangan == 'keuangan pusat')
                                            <option value="all_kan">- Semua Unit -</option>
                                        @else
                                            <option value="">- Pilih -</option>
                                        @endif
                                        @foreach($kantor as $ka)
                                        <option value="{{$ka->id}}" {{ Auth::user()->id_kantor == $ka->id ? 'selected' : ''  }}>{{$ka->unit}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                
                                <div class="col-md-3 mb-3">
                                    <label>Level</label>
                                    <select class="form-control cek3" name="lvl" id="lvl">
                                        <option value="">All</option>
                                        @foreach($lev as $l)
                                        <option value="{{$l->level}}">{{$l->level}}</option>
                                        @endforeach
                                    </select>
                                    <!--<input type="div" class="form-control dates cek4" name="blns" id="blns" autocomplete="off" placeholder="contoh {{date('m-Y') }}">-->
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label>Cari</label>
                                    <input type="div" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Cari Akun...">
                                </div>
                                <div class="col-md-3 mb-3" style="position:relative;" >
                                    
                                    <label>Grup</label>
                                    <select class="form-control muah cek33 custom-height" name="grup[]" id="grup" multiple="multiple">
                                        @foreach($grup as $g)
                                        <option value="{{$g->id}}">{{ $g->name}}</option>
                                        @endforeach
                                    </select>
                                    <!--<input type="div" class="form-control dates cek4" name="blns" id="blns" autocomplete="off" placeholder="contoh {{date('m-Y') }}">-->
                                </div>
                            </div>
                            <div class="row" style="position:relative">
                                <div class="col-md-7 mt-5" style="position:absolute;">
                                    <div class="form-check form-switch ">
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" style="height : 20px; width : 40px;">
                                        <label for="flexSwitchCheckChecked" class="mt-1 ms-1">Show/Hide COA dengan debit dan kredit nya 0</label>
                                    </div>
                                </div>
                            </div>
                            <!--<input type="div" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Cari Program..." style="float: right; margin-top : 10px; margin-left: 10px; width: 20%; height: 2.5rem">-->
                                <!--<div class="col-md-3 mb-3">-->
                                <!--    <input type="div" class="form-control" id="myInput"  style="margin-top: 50px" onkeyup="myFunction()" placeholder="Cari Akun...">-->
                                <!--</div>-->
                            <div class="table-responsive">    
                                    <!--data-height="350" -->
                                <table
                                    data-show-export="true" 
                                    id="user_table" 
                                    class="table table-bordered" 
                                    data-show-refresh="true"  
                                    data-search="false" 
                                    data-visible-search="false" 
                                    data-query-params="queryParams"  
                                    data-regex-search="true" 
                                    data-show-search-clear-button="false"
                                    data-resizable="true"
                                    >
                                   
                                </table>
                            </div>
                            <!--<div class="table-responsive">-->
                                <!--<table id="user_table" class="table table-striped">-->
                                <!--    <thead>-->
                                <!--        <tr>-->
                                            <!--<th>#</th>-->
                                <!--            <th>Kode Akun</th>-->
                                <!--            <th>Nama Akun</th>-->
                                <!--            <th>Saldo Awal</th>-->
                                <!--            <th>Debet Mutasi</th>-->
                                <!--            <th>Kredit Mutasi</th>-->
                                <!--            <th>Neraca Saldo</th>-->
                                <!--            <th>Debet Disesuaikan</th>-->
                                <!--            <th>Kredit Disesuaikan</th>-->
                                <!--            <th>Neraca Disesuaikan</th>-->
                                <!--            <th>Clossed</th>-->
                                <!--        </tr>-->
                                <!--    </thead>-->
                                <!--    <tbody>-->
                                        
                                <!--    </tbody>-->
                                <!--    <tfoot>-->
                                <!--        <tr>-->
                                <!--            <td></td>-->
                                <!--            <td style="font-size: 12px"><b>Saldo Awal [Asset Vs (Kewajiban+SD)]</b></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td></td>-->
                                <!--            <td></td>-->
                                <!--            <td></td>-->
                                <!--            <td></td>-->
                                <!--        </tr>-->
                                    
                                <!--        <tr>-->
                                <!--            <td></td>-->
                                <!--            <td style="font-size: 12px"><b>Mutasi [Debet Vs Kredit]</b></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td></td>-->
                                <!--            <td></td>-->
                                <!--            <td></td>-->
                                <!--            <td></td>-->
                                <!--        </tr>-->
                                    
                                <!--        <tr>-->
                                <!--            <td></td>-->
                                <!--            <td style="font-size: 12px"><b>Penyesuaian [Debet Vs Kredit]</b></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td></td>-->
                                <!--            <td></td>-->
                                <!--            <td></td>-->
                                <!--            <td></td>-->
                                <!--        </tr>-->
                                <!--        <tr>-->
                                <!--            <td></td>-->
                                <!--            <td style="font-size: 12px"><b>Saldo Akhir [Asset Vs (Kewajiban+SD)]</b></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td style="font-size: 12px"></td>-->
                                <!--            <td></td>-->
                                <!--            <td></td>-->
                                <!--            <td></td>-->
                                <!--            <td></td>-->
                                <!--        </tr>-->
                                <!--    </tfoot>-->
                                <!--</table>-->
                            <!--</div>-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>
    </div>
</div>
@endsection