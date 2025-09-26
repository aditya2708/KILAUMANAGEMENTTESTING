@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

   <div class="modal fade" id="modaldet" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ekspor Data </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="POST" id="sample_formd" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="basic-form" id="bod">
                                                </div>
                                        

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>


                        </div>
                         <div class="modal-footer" id="foot">
                        </div>
                        <!--<div class="modal-footer">-->
                        <!--    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>-->
                        <!--     <button type="button" class="btn btn-success btn-sm update" id="smp1" >Simpan</button>-->
                            <!--<button type="submit" class="btn btn-primary blokkk" id="smpn">Simpan</button>-->
                        <!--</div>-->
                    </form>
                </div>
            </div>
        </div>

       <div class="modal fade" id="modal-default1" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">List Program BSZ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                    </div>
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                        <div class="pull-right">
                        <button type="button" id="tambah" class="btn btn-success btn-xxs" data-bs-toggle="modal" data-bs-target="#modal-tambah">Tambah</button>
                        </div>
                    </div>
                   
                    <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="user_table_1" class="table table-striped" style="width:100%">
                                        <thead>
                                        <tr>
                                             <th>Nama Program</th>
                                             <th>Keterangan</th>
                                             <th>Status</th>
                                        </tr>
                                    </thead>
                                    </table>
                                </div>
                            </div> 
                            <br>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
         <div class="modal fade" id="modal-tambah" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="col-lg-12">
                                          <div class="mb-3 row">
                                            <label class="col-sm-3 ">Nama Program</label>
                                            <div class="col-sm-9">
                                                <input  type="text" class=" form-control " id="program" name="program">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 row">
                                            <label class="col-sm-3 ">Keterangan</label>
                                            <div class="col-sm-9">
                                                <input  type="text" class=" form-control " id="keterangan" name="keterangan">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 row">
                                            <label class="col-sm-3 ">Status</label>
                                            <div class="col-sm-9">
                                                 <select required class="form-control" name="status" id="status" style="width:100%">
                                                    <option value="">Pilih Status</option>
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Non-Aktif</option>
                                                </select>
                                                <!--<input  type="text" class=" form-control " id="deskripsi" name="deskripsi">-->
                                            </div>
                                        </div>
                                        
                                        
                                       
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                             <!--<button type="button" class="btn btn-success btn-sm simpan" id="simpan" >Simpan</button>-->
                             <!--  <button type="button" class="btn btn-success btn-sm update" idp="` + idp + `" id="smp1" >Simpan</button>-->
                            <button type="button" class="btn btn-success cok1"  id="simpan">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
        
        
        <div class="modal fade" id="modaldet" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="POST" id="sample_formd" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="basic-form" id="bod">
                                                </div>
                                        

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>


                        </div>
                         <div class="modal-footer" id="foot">
                        </div>
                        <!--<div class="modal-footer">-->
                        <!--    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>-->
                        <!--     <button type="button" class="btn btn-success btn-sm update" id="smp1" >Simpan</button>-->
                            <!--<button type="submit" class="btn btn-primary blokkk" id="smpn">Simpan</button>-->
                        <!--</div>-->
                    </form>
                </div>
            </div>
        </div>
        
        
         <div class="modal fade" id="modalpasang" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="POST" id="sample_formzz" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="basic-form" id="bodi">
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>


                        </div>
                         <div class="modal-footer" id="footi">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        

                         
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    
                              <!--<div class="col-lg-2 mb-3">-->
                              <!--          <label>&nbsp;</label>-->
                              <!--          <div class="btn-group pull-right">-->
                              <!--              <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="margin-top: 30px">-->
                              <!--                  Ekspor-->
                              <!--              </button>-->
                              <!--              <ul class="dropdown-menu">-->
                              <!--                  <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>-->
                              <!--                  <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>-->
                                                <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                              <!--              </ul>-->
                              <!--          </div>-->
                              <!--      </div> -->
                    <form method="GET" action="{{ url('bukti-setor-zakat/export') }}" >
                      
                    <!--<div class="card-header">-->
                    <!--    <h4 class="card-title"></h4>-->
                    <!--    <div class="pull-right">-->
                    <!--     <button type="button" id="tambah" class="btn btn-success btn-xxs" data-bs-toggle="modal" data-bs-target="#modal-default1">List Program BSZ</button>-->
                    <!--    </div>-->
                    <!--</div>-->
                    
                    
                    
                    <div class="card-body">
                        <div class="row">
                              <div class="row">
                                
                          


                                <!--<div class="col-lg-3 mb-3" id="tgldari">-->
                                <!--    <label>Periode :</label>-->
                                <!--    <input type="text" class="form-control tgl dates" id="dari" name="dari">-->
                                <!--</div>-->


                                
                                 <div class="col-lg-11 mb-3">
                                  
                                </div>


                                <div class="col-lg-1 mb-3">
                                        <label>&nbsp;</label>
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                Ekspor
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>
                                                <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>
                                                <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                            </ul>
                                        </div>
                                    </div> 
                                <!--<div class="col-lg-3 mb-3">-->
                                <!--    <label >Pembayaran :</label>-->
                                <!--        <select class="form-control cekb" name="byr" id="byr">-->
                                <!--             <option value="bank">Bank</option>-->
                                <!--              <option value="cash">Cash</option>-->
                                <!--        </select>-->
                                <!--</div>-->
                                
                                
                              

                                
                        <div class="pull-right" style="display: none" id="one">
                            <h3 style="float:right; margin-top:0px"><label class="badge badge-xl badge-info totaltr"></label></h3>
                            
                            <button type="button" class="btn btn-success btn-sm" style=" margin-right: 20px;" id="acc_all"><span class="btn-icon-start text-success"><i class="fa fa-check-double color-success"></i></span>Approve All</button>

                                </div>
                        
                            <div class="pull-right"> 

                            </div>
                              
                            </div>
                            
                            <div class="col-lg-3 mb-3">
                                <label>Kantor:</label>
                                <select multiple="multiple" class="form-control multi cekk" name="kantor[]" id="kantor">
                                    @foreach($kantor as $val)
                                        <option value="{{$val->id}}" data-value="{{$val->id}}">{{$val->unit}}</option>
                                    @endforeach
                                </select>
                            </div>
                        
                            <div class="col-lg-3 mb-3">
                                <label>Jenis Zakat:</label>
                                <select class="form-control cekz" name="jenis_zakat" id="jenis_zakat">
                                    <option value="1">Zakat</option>
                                    <option value="0">Non-Zakat</option>
                                </select>
                            </div>
                        
                            <div class="col-lg-3 mb-3">
                                <label class="form-label">Tahun:</label>
                                <input type="text" class="form-control year cekt" name="thn" id="thn" autocomplete="off" placeholder="{{date('Y')}}">
                            </div>
                            
                            
                             <div class="col-lg-3 mb-3">
                                   <label class="form-label">Bulan:</label>
                                    <select multiple="multiple" id="bln" class="form-control multi cekb" name="bln[] "placeholder="{{date('Y')}}">
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
                                    
                            <!--<div class="col-lg-3 mb-3">-->
                            <!--    <label class="form-label">Bulan:</label>-->
                            <!--    <input type="text" class="form-control blns cekb" name="bln" id="bln" autocomplete="off" placeholder="{{date('m')}}">-->
                            <!--</div>-->
                        </div>
    
                        <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                            <table id="user_table" class="table table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="cari">ID Donatur</th>
                                        <th class="cari">Tahun</th>
                                        <th class="cari">Nama Donatur</th>
                                        <th class="cari">Pendapatan</th>
                                        <th class="cari">Zakat</th>
                                        <th class="cari">Tanggal Transaksi</th>
                                    </tr>
                                </thead>
                                 <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                     </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


  