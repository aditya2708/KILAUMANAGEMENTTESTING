@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
       <div class="modal fade" id="modal-default1" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Jenis Laporan Keuangan</h5>
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
                                             <th>id</th>
                                             <th>Deskripsi</th>
                                             <th>Aktif</th>
                                             <!--<th></th>-->
                                             <!--<th></th>-->
                                             <!--<th></th>-->
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
        
          <div class="modal fade" id="modalrum" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Rumus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="POST" id="sample_formr" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="basic-form" id="bohay">
                                                </div>
                                        

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>

                        <div class=" mb-3 row">
                            <b class="red">*Catatan.</b>
                            <label class="red">Petunjuk pengisian rumus yang ingin di pakai</label>
                            <label id="textnya" class="red"></label>
                         </div>


                        </div>
                         <div class="modal-footer" id="fohay">
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
        
        
        <div class="modal fade" id="modal-tambah" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Laporan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-3 row">
                                            <label class="col-sm-3 ">Deksripsi</label>
                                            <div class="col-sm-9">
                                                <input  type="text" class=" form-control " id="deskripsi" name="deskripsi">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 row">
                                            <label class="col-sm-3 ">Status</label>
                                            <div class="col-sm-9">
                                                 <select required class="form-control" name="status" id="status" style="width:100%">
                                                    <option value="">Pilih Status</option>
                                                    <option value="y">y</option>
                                                    <option value="n">n</option>
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


       <div class="modal fade" id="modal-rumus" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tamus">Tambah Rumus Laporan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                    </div>
                 
                   
                    <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">
                        <div class="modal-body">
                             <div class="basic-form">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-3 row">
                                            <label class="col-sm-3 ">Status Parent</label>
                                            <div class="col-sm-9">
                                                 <select required class="form-control" name="parent" id="parent" style="width:100%">
                                                    <option value="">Pilih</option>
                                                    <option value="y">Parent</option>
                                                    <option value="n">Bukan Parent</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                          <div  class="mb-3 row">
                                            <label class="col-sm-3">Parent:</label>
                                            <div class="col-sm-9">
                                                  <select class="js-example-basic-singlex " name="id_parent" id="id_parent" style="width: 100%">
                                                        <option value="">Pilih</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-sm-3 ">Nama Penyajian</label>
                                            <div class="col-sm-6">
                                                <input  type="text" class=" form-control " id="nampe" name="nampe">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 row">
                                            <label class="col-sm-3 ">Level</label>
                                            <div class="col-sm-9">
                                                 <select required class="form-control" name="level" id="level" style="width:100%">
                                                    <option value="">Pilih level</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                         <div class="mb-3 row">
                                            <label class="col-sm-3 ">Sort</label>
                                            <div class="col-sm-6">
                                                <input  type="text" class=" form-control " id="urutan" name="urutan">
                                            </div>
                                        </div>
                                        
                                      
                                        
                                        <!-- <div class="mb-3 row">-->
                                        <!--    <label class="col-sm-3 ">Kode</label>-->
                                        <!--    <div class="col-sm-6">-->
                                        <!--        <input  type="text" class=" form-control " id="kode" name="kode">-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!-- <div class="mb-3 row">-->
                                        <!--    <label class="col-sm-3 ">Rumus yang dipakai</label>-->
                                        <!--    <div class="col-sm-9">-->
                                        <!--         <select required class="form-control" name="indikator" id="indikator" style="width:100%">-->
                                        <!--            <option value="">Pilih </option>-->
                                        <!--            <option value="coa">COA</option>-->
                                        <!--            <option value="urutan">Urutan</option>-->
                                        <!--        </select>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        
                                        
                                        <div class="mb-3 row">
                                            <label class="col-sm-3 ">Rumus COA</label>
                                            <div class="col-sm-6 sm">
                                                <textarea id="rumus" name="rumus" class="form-control" height="150px"></textarea>
                                                <!--<input  type="text" class=" form-control " id="nampe" name="nampe">-->
                                            </div>
                                        </div>
                                        
                            <div class="row">
                                    <b class="red">*Catatan.</b>
                                    <label class="red">Petunjuk pengisian rumus yang ingin di pakai</label>
                                    <label id="petunjuk" class="red"></label>
                             </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                             <!--<button type="button" class="btn btn-success btn-sm simpan" id="simpan" >Simpan</button>-->
                             <!--  <button type="button" class="btn btn-success btn-sm update" idp="` + idp + `" id="smp1" >Simpan</button>-->
                            <button type="button" class="btn btn-success cok2"  id="simpan">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <form method="GET" action="{{ url('jenis-laporan/export') }}" >
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                        <div class="pull-right">
                        <button type="button" id="tambah1" class="btn btn-success btn-xxs" >Tambah Rumus</button>
                        <button type="button" id="tambah" class="btn btn-success btn-xxs" data-bs-toggle="modal" data-bs-target="#modal-default1">Laporan Keuangan</button>
                        </div>
                        
                        
                    </div>
                    
                  
                    <div class="card-body">
                        <div class="basic-form">
                   
                            <div class="row">
                                
                          


                                <!--<div class="col-lg-3 mb-3" id="tgldari">-->
                                <!--    <label>Periode :</label>-->
                                <!--    <input type="text" class="form-control tgl dates" id="dari" name="dari">-->
                                <!--</div>-->


                                
                                 <div class="col-lg-3 mb-3">
                                    <label >Jenis :</label>
                                        <select class="form-control cekj carijenis" name="jns" id="jns">
                                                @foreach($jenislap as $val)
                                                <option value="{{$val->id}}"  data-value="{{$val->deskripsi}}">{{$val->deskripsi}}</option>
                                                @endforeach
                                        </select>
                                </div>


                                <div class="col-lg-2 mb-3">
                                        <label>&nbsp;</label>
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="margin-top: 30px">
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
                        </div>
                        <br>
                 
                        <h1 class="card-title">Rumus Laporan</h4>
                   
                        <div class="table-responsive">
                            <!--<button id="aksis">buka</button>-->
                        <!--<div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">-->
                                <table id="user_table" class="table table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                             <!--<th class=" hide_column">id</th>-->
                                             <th>Nama Peyajian</th>
                                             <th>Rumus</th>
                                             <th>Level</th>
                                             <!--<th>Keterangan</th>-->
                                             <!--<th>Kode</th>-->
                                             <th>Sort</th>
                                             <th>Aksi</th>
                                             <th>Naik</th>
                                             <th>Turun</th>
                                             <th>Hapus</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        
                    </form>    
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection