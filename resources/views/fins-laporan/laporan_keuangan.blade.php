@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
        <div class="modal fade" id="modal" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="duarr"> </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form1" enctype="multipart/form-data">
                        <div class="modal-body">
                            
                              <div class="card-body">
                                <div class="table-responsive">
                                    <table id="user_table_2023" class="table table-striped">
                                        <thead>
                                        <tr>
                                             <th>Kode Akun</th>
                                             <th>Nama Akun</th>
                                             <th>Saldo Awal</th>
                                             <th>Debet Mutasi</th>
                                             <th>Kredit Mutasi</th>
                                             <th>Neraca Saldo</th>
                                             <th>Debet Disesuaikan</th>
                                             <th>Kredit Disesuaikan</th>
                                             <th>Neraca Disesuaikan</th>
                                             <th>Clossed</th>
                                        </tr>
                                    </thead>
                                    
                                        <!-- <tbody id="tableag">-->

                                        <!--</tbody>-->
                                        <!--<tfoot id="footag">-->

                                        <!--</tfoot>-->
                                    </table>
                                </div>
                            </div>
                            
                            <div class="basic-form" id="bodai">
                            </div>
                        </div>
                          <div class="modal-footer">
                            <div id="footai">
                            </div>
                        </div>    
                        
                    </form>
                </div>
            </div>
        </div>
        
        
            <div class="modal fade" id="modaldebet" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="coadebet"> </h5>
                            <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-target="#modal" data-bs-toggle="modal">-->
                             <div class="btn-group mb-3">
                                 <button type="button" class="btn btn-success btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ekspor
                                 </button>
                                  <ul class="dropdown-menu">
                                        <li><button class="dropdown-item expFile" data-value="xls" value="xls" name="tombol" id="xls" pembeda="debet" pembedathn="sekarang">.XLS</button></li>
                                        <li><button class="dropdown-item expFile" data-value="csv" value="csv" name="tombol" id="csv" pembeda="debet" pembedathn="sekarang">.CSV</button></li>
                                  </ul>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                               <table id="user_table_debet_2023" class="table table-striped">
                                    <thead>
                                        <tr>
                                             <th>Tanggal</th>
                                             <th>COA</th>
                                             <th>Keterangan</th>
                                             <th>Nominal</th>
                                             <th>COA Buku</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
        
        
        
        <!-- <div class="modal fade" id="modaldebet" >-->
        <!--    <div class="modal-dialog modal-lg" role="document">-->
        <!--        <div class="modal-content">-->
        <!--            <div class="modal-header">-->
        <!--                <h5 class="modal-title" id="coadebet"> </h5>-->
        <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
        <!--                </button>-->
        <!--            </div>-->
        <!--                <div class="modal-body">-->
        <!--                    <div class="btn-group mb-3">-->
        <!--                         <button type="button" class="btn btn-success btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">-->
        <!--                            Ekspor-->
        <!--                         </button>-->
        <!--                          <ul class="dropdown-menu">-->
        <!--                                <li><button class="dropdown-item expFile" data-value="xls" value="xls" name="tombol" id="xls" pembeda="debet" pembedathn="sekarang">.XLS</button></li>-->
        <!--                                <li><button class="dropdown-item expFile" data-value="csv" value="csv" name="tombol" id="csv" pembeda="debet" pembedathn="sekarang">.CSV</button></li>-->
        <!--                          </ul>-->
        <!--                    </div>-->
        <!--                      <div class="card-body">-->
        <!--                        <div class="table-responsive">-->
        <!--                            <table id="user_table_debet_2023" class="table table-striped">-->
        <!--                                <thead>-->
        <!--                                <tr>-->
        <!--                                     <th>Tanggal</th>-->
        <!--                                     <th>COA</th>-->
        <!--                                     <th>Keterangan</th>-->
        <!--                                     <th>Nominal</th>-->
        <!--                                     <th>COA Buku</th>-->
        <!--                                </tr>-->
        <!--                            </thead>-->
                                    
                                    
        <!--                            </table>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        
        <div class="modal fade" id="modalkredit" >
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="coakredit"> </h5>
                        <div class="btn-group mb-3">
                             <button type="button" class="btn btn-success btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">
                                Ekspor
                             </button>
                              <ul class="dropdown-menu">
                                    <li><button class="dropdown-item expFile" data-value="xls" value="xls" name="tombol" id="xls" pembeda="debet" pembedathn="sekarang">.XLS</button></li>
                                    <li><button class="dropdown-item expFile" data-value="csv" value="csv" name="tombol" id="csv" pembeda="debet" pembedathn="sekarang">.CSV</button></li>
                              </ul>
                        </div>
                        
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <!--<form class="form-horizontal" method="post" id="sample_form1" enctype="multipart/form-data">-->
                        <div class="modal-body">
                            <!--<div class="btn-group mb-3">-->
                            <!--     <button type="button" class="btn btn-success btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">-->
                            <!--        Ekspor-->
                            <!--     </button>-->
                            <!--      <ul class="dropdown-menu">-->
                            <!--            <li><button class="dropdown-item expFile" data-value="xls" value="xls" name="tombol" id="xls" pembeda="kredit" pembedathn="sekarang">.XLS</button></li>-->
                            <!--            <li><button class="dropdown-item expFile" data-value="csv" value="csv" name="tombol" id="csv" pembeda="kredit" pembedathn="sekarang">.CSV</button></li>-->
                            <!--      </ul>-->
                            <!--</div>-->
                              <div class="card-body">
                                <div class="table-responsive">
                                    <table id="user_table_kredit_2023" class="table table-striped">
                                        <thead>
                                        <tr>
                                             <th>Tanggal1</th>
                                             <th>COA</th>
                                             <th>Keterangan</th>
                                             <th>Nominal</th>
                                             <th>COA Buku</th>
                                        </tr>
                                    </thead>
                                    
                                        <!-- <tbody id="tableag">-->

                                        <!--</tbody>-->
                                        <!--<tfoot id="footag">-->

                                        <!--</tfoot>-->
                                    </table>
                                </div>
                            </div>
                            
                            <div class="basic-form" id="bodai">
                            </div>
                        </div>
                          <div class="modal-footer">
                            <div id="footai">
                            </div>
                        </div>    
                        
                    <!--</form>-->
                </div>
            </div>
        </div>
        
        
         <!--</form>-->
        
          <div class="modal fade" id="modalsebelumnya" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="duarsebelumnya"> </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form1" enctype="multipart/form-data">
                        <div class="modal-body">
                            
                              <div class="card-body">
                                <div class="table-responsive">
                                    <table id="user_table_sebelumnya" class="table table-striped">
                                        <thead>
                                        <tr>
                                             <th>Kode Akun</th>
                                             <th>Nama Akun</th>
                                             <th>Saldo Awal</th>
                                             <th>Debet Mutasi</th>
                                             <th>Kredit Mutasi</th>
                                             <th>Neraca Saldo</th>
                                             <th>Debet Disesuaikan</th>
                                             <th>Kredit Disesuaikan</th>
                                             <th>Neraca Disesuaikan</th>
                                             <th>Clossed</th>
                                        </tr>
                                    </thead>
                                    
                                        <!-- <tbody id="tableag">-->

                                        <!--</tbody>-->
                                        <!--<tfoot id="footag">-->

                                        <!--</tfoot>-->
                                    </table>
                                </div>
                            </div>
                            
                            <div class="basic-form" id="bodai">
                            </div>
                        </div>
                          <div class="modal-footer">
                            <div id="footai">
                            </div>
                        </div>    
                        
                    </form>
                </div>
            </div>
        </div>
        
        
          <div class="modal fade" id="modaldebetsebelumnya" >
             <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="coadebetsebelumnya"> </h5>
                          <div class="btn-group">
                             <button type="button" class="btn btn-primary btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">
                                Ekspor
                             </button>
                              <ul class="dropdown-menu">
                                    <li><button class="dropdown-item expDebit" data-value="xls" value="xls" name="tombol" id="xls" pembeda="debet" pembedathn="lalu">.XLS</button></li>
                                    <li><button class="dropdown-item expDebit" data-value="csv" value="csv" name="tombol" id="csv" pembeda="debet" pembedathn="lalu">.CSV</button></li>
                              </ul>
                        </div>
                    </div>
                        <div class="modal-body">
                              <div class="card-body">
                                <div class="table-responsive">
                                    <table id="user_table_sss" class="table table-striped">
                                        <thead>
                                        <tr>
                                             <th>Tanggal</th>
                                             <th>COA</th>
                                             <th>Keterangan</th>
                                             <th>Nominal</th>
                                             <th>COA Buku</th>
                                        </tr>
                                    </thead>
                                    
                                        <!-- <tbody id="tableag">-->

                                        <!--</tbody>-->
                                        <!--<tfoot id="footag">-->

                                        <!--</tfoot>-->
                                    </table>
                                </div>
                            </div>
                            
                            <div class="basic-form" id="bodai">
                            </div>
                        </div>
                          <div class="modal-footer">
                            <div id="footai">
                            </div>
                        </div>    
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modalkreditsebelumnya" >
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="coakreditsebelumnya"> </h5>
                        <div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ekspor
                                </button>
                                
                                <ul class="dropdown-menu">
                                    <li><button class="dropdown-item expDetail" data-value="xls" value="xls" name="tombol" id="xls" pembeda="kredit" pembedathn="lalu">.XLS</button></li>
                                    <li><button class="dropdown-item expDetail" data-value="csv" value="csv" name="tombol" id="csv" pembeda="kredit" pembedathn="lalu">.CSV</button></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                        <div class="modal-body">
                              <div class="card-body">
                                <div class="table-responsive">
                                    <table id="user_table_kredit_sebelumnya" class="table table-striped">
                                        <thead>
                                        <tr>
                                             <th>Tanggal</th>
                                             <th>COA</th>
                                             <th>Keterangan</th>
                                             <th>Nominal</th>
                                             <th>COA Buku</th>
                                        </tr>
                                    </thead>
                                    
                                        <!-- <tbody id="tableag">-->

                                        <!--</tbody>-->
                                        <!--<tfoot id="footag">-->

                                        <!--</tfoot>-->
                                    </table>
                                </div>
                            </div>
                            
                            <div class="basic-form" id="bodai">
                            </div>
                        </div>
                          <div class="modal-footer">
                            <div id="footai">
                            </div>
                        </div>    
                        
                </div>
            </div>
        </div>
        
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="basic-form">
                            <div class="d-flex bd-highlight mb-3">
                                <div class="me-auto p-2 bd-highlight">
                                    <label class="form-label">Filter :</label>
                                    <select id="jenis" class="form-control cek1" name="jenis">
                                         @foreach($jenis as $val)
                                            <option value="{{$val->id}}">{{$val->deskripsi}}</option>
                                            @endforeach
                                        <!--<option value="0">Laporan Posisi Keuangan</option>-->
                                        <!--<option value="1">Laporan Perubahan Dana</option>    -->
                                        <!--<option value="2">Lapora Arus Kas</option>    -->
                                    </select>
                                </div>
                                
                                
                                <div class="p-2 bd-highlight col-md-3">
                                    <label class="form-label">Tahun :</label>
                                    <input type="text" class="form-control year cek4" name="thn" id="thn" autocomplete="off" placeholder="{{date('Y') }}" >
                                </div>
                                
                                   <div class="p-2 bd-highlight">
                                    <label class="form-label">Bulan</label>
                                    <div id="bulone" style="width: 200px;">
                                    <select id="bln" class="form-control cek3" name="bln" style="float: right">
                                          <option value="">Pilih Bulan</option>
                                          <option value="01">Januari</option>
                                          <option value="02">Febuari</option>
                                          <option value="03">Maret</option>
                                          <option value="04">April</option>
                                          <option value="05">Mei</option>
                                          <option value="06">Juni</option>
                                          <option value="07">Juli</option>
                                          <option value="08">Agustus</option>
                                          <option value="09">September</option>
                                          <option value="10">Oktober</option>
                                          <option value="11">November</option>
                                          <option value="12">Desember</option>
                                              
                                    </select> 
                                    </div>
                                    <div id="bulmul" style="width: 200px;">
                                    <select multiple="multiple" id="bln2" class="form-control blns cek3" name="bln2[]" style="float: right;">
                                          <option value="">Pilih Bulan</option>
                                          <option value="01">Januari</option>
                                          <option value="02">Febuari</option>
                                          <option value="03">Maret</option>
                                          <option value="04">April</option>
                                          <option value="05">Mei</option>
                                          <option value="06">Juni</option>
                                          <option value="07">Juli</option>
                                          <option value="08">Agustus</option>
                                          <option value="09">September</option>
                                          <option value="10">Oktober</option>
                                          <option value="11">November</option>
                                          <option value="12">Desember</option>
                                              
                                    </select> 
                                    </div>
                                    <div class="checkbox" style="margin-top: 5px; margin-left: 0px">
                                          <label>
                                            <input type="checkbox" class="cek3" name="mulbul" id="mulbul"> Multiple
                                          </label>
                                    </div>
                                </div>
                                
                                
                                <!--<div class="p-2 bd-highlight">-->
                                <!--    <label class="form-label">Unit</label>-->
                                <!--    <select id="kota" class="form-control cek5" name="kota" style="float: right">-->
                                <!--        <option value="">Pilih Unit</option>-->
                                <!--            @foreach($kantor as $val)-->
                                <!--            <option value="{{$val->id}}">{{$val->unit}}</option>-->
                                <!--            @endforeach-->
                                <!--        </select>     -->
                                <!--</div>-->
                                
                                <div class="p-2 bd-highlight">
                                    <label class="form-label">Via</label>
                                    <select id="via" class="form-control cek6" name="via" style="float: right">
                                        <option value="1">Clossing</option>
                                        <option value="0">Realtime</option>    
                                    </select>
                                </div>
                                
                                 <div class="col-lg-2 mb-3">
                                    <label>&nbsp;</label>
                                    <div class="btn-group pull-right">
                                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="margin-top: 30px">
                                            Ekspor
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><button class="dropdown-item exp" value="xls" name="tombol">.XLS</button></li>
                                            <li><button class="dropdown-item exp" value="csv" name="tombol">.CSV</button></li>
                                            <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                        </ul>
                                    </div>
                                </div> 
                                
                                <!--<div class="mb-3 col-md-2">-->
                                <!--    <label class="form-label mb-4">&nbsp;</label>-->
                                <!--    <div class="btn-group">-->
                                <!--      <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">-->
                                <!--        Ekspor-->
                                <!--      </button>-->
                                <!--      <ul class="dropdown-menu">-->
                                <!--        <li><a class="dropdown-item" href="#">Action</a></li>-->
                                <!--        <li><a class="dropdown-item" href="#">Another action</a></li>-->
                                <!--        <li><a class="dropdown-item" href="#">Something else here</a></li>-->
                                <!--        <li><hr class="dropdown-divider"></li>-->
                                <!--        <li><a class="dropdown-item" href="#">Separated link</a></li>-->
                                <!--      </ul>-->
                                <!--    </div>-->
                                <!--</div>-->
                            </div>
                        </div>
                        
                          <div class="d-flex justify-content-center">
                            <b>KILAU INDONESIA</b>
                        </div>
                        <div class="d-flex justify-content-center">
                            <b id="piljen"></b>
                        </div>
                        <div class="d-flex justify-content-center">
                            <span id="totem"></span>
                        </div>
                        <hr>
                        
                        
                        
                        
                         <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                            <table id="user_table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Urutan</th>
                                        <th>Nama</th>
                                        <th id = 'thnn'></th>
                                        <th id = 'thnnn'></th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
            
            <!--<div class="col-lg-12">-->
            <!--    <div class="card">-->
            <!--         <div class="card-header">-->
            <!--            <h4 class="card-title d-flex justify-content-center" id="totem"></h4>-->
            <!--        </div>-->
            <!--        <div class="card-body">-->
            <!--            <div class="row">-->
            <!--                <div class="col-md-2"></div>-->
            <!--                <div class="col-md-8">-->
            <!--                    <div class="table-responsive">-->
            <!--                        <div id="load"></div>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--                <div class="col-md-2"></div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            
               
        </div>
        
     
    </div>
</div>
@endsection