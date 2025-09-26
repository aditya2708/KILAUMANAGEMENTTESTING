@extends('template')
@section('konten')

<div class="content-body">
    <div class="container-fluid">

        <!--<div class="modal fade" id="modal-default1" >-->
        <!--    <div class="modal-dialog modal-lg" role="document">-->
        <!--        <div class="modal-content">-->
        <!--            <div class="modal-header">-->
        <!--                <h5 class="modal-title" id="exampleModalLabel">List Program BSZ</h5>-->
        <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
        <!--                </button>-->
        <!--            </div>-->
        <!--                <div class="modal-body">-->
        <!--                 <div class="pull-right">-->
        <!--                 <button type="button" id="tambah" class="btn btn-success btn-xxs" data-bs-toggle="modal" data-bs-target="#modal-default1">Tambah Program</button>-->
        <!--                </div>-->

        <!--                    <div class="table-responsive">-->
        <!--                        <table id="user_table_1" class="table table-bordered ">-->
        <!--                            <thead>-->
        <!--                                <tr>-->
        <!--                                    <th>COA</th>-->
        <!--                                    <th>Jenis Transaksi</th>-->
        <!--                                    <th>Qty</th>-->
        <!--                                    <th>Nominal</th>-->
        <!--                                    <th>Total</th>-->
        <!--                                    <th>Keterangan</th>-->
        <!--                                    <th>Kantor</th>-->
        <!--                                    <th>Aksi</th>-->

        <!--                                </tr>-->
        <!--                            </thead>-->
        <!--                            <tbody id="table">-->

        <!--                            </tbody>-->
        <!--                            <tfoot id="foot">-->

        <!--                            </tfoot>-->
        <!--                        </table>-->
        <!--                    </div>-->

        <!--                </div>-->
                     
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->

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
        
        <!-- <div class="card">-->
        <!--        <div class="col-lg-12">-->
        <!--            <div class="col-lg-6 mb-3 row">-->
        <!--                <label class="col-sm-4">Diterima Oleh:</label>-->
        <!--                <div class="col-sm-8">-->
        <!--                    <select class="js-example-basic-single1" style="width: 100%;" name="petugas" id="petugas">-->
        <!--                        <option value="">Pilih</option>-->
        <!--                    </select>-->
        <!--                </div>-->
        <!--            </div>-->
                
        <!--            <div class="col-lg-6 mb-3 row">-->
        <!--                <label class="col-sm-4">Program BSZ:</label>-->
        <!--                <div class="col-sm-8">-->
        <!--                    <select class="form-control" style="width: 100%;" name="bsz" id="bsz">-->
        <!--                        @foreach($progbsz as $val)-->
        <!--                            <option value="{{$val->id}}" data-value="{{$val->nama}}">{{$val->nama}}</option>-->
        <!--                        @endforeach-->
        <!--                    </select>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
                
        <!--    <div >-->
        <!--       <button type="button" class="btn btn-success btn-sm progpasang" aksi="pasangkan" id="` + id + `"  id="smp1" >Simpan</button>-->
        <!--    </div>-->
        <!--</div>-->

        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                      <form>
                      
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                        <div class="pull-right">
                         <button type="button" id="tambah" class="btn btn-success btn-xxs" data-bs-toggle="modal" data-bs-target="#modal-default1">List Program BSZ</button>
                        </div>
                    </div>
           
                    
                <div class="card-body">
                        <div class="col-lg-6 mb-3 row">
                            <label class="col-sm-4">Diterima Oleh:</label>
                            <div class="col-sm-8">
                                <select class="js-example-basic-single1" style="width: 100%;" name="petugas" id="petugas">
                                    <option value="">Pilih</option>
                                </select>
                            <label class="col-sm-12" style="font-size: 9px;">*Jika kosong maka default signature adalah user yang sedang dipakai. </label>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-3 row">
                            <label class="col-sm-4">Program BSZ:</label>
                            <div class="col-sm-8">
                                <select class="form-control" style="width: 100%;" name="bsz" id="bsz">
                                    @foreach($progbsz as $val)
                                        <option value="{{$val->id}}" data-value="{{$val->nama}}">{{$val->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-3 row">
                            <label class="col-sm-4">Sumber Dana:</label>
                            <div class="col-sm-8">
                                <select class="form-control cekj" name="jns" id="jns">
                                    @foreach($sumber as $val)
                                        <option value="{{$val->id_sumber_dana}}"  data-value="{{$val->sumber_dana}}">{{$val->sumber_dana}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    <div class="pull-right " >
                        <button type="button" class="btn btn-success btn-sm progpasang" aksi="pasangkan">Simpan</button>
                    </div>
                        
                    </div>
                        


                    
                    
                    
                        
                        <!--<div class="pull-right" style="display: none" id="one">-->
                        <!--    <button type="button" class="btn btn-success btn-sm progpasang" aksi="pasangkan">Simpan</button>-->
                        <!--</div>-->
                        
                        <!--  <div class="col-lg-3 mb-3">-->
                        <!--    <label >Sumber Dana :</label>-->
                        <!--        <select class="form-control cekj" name="jns" id="jns">-->
                        <!--            @foreach($sumber as $val)-->
                        <!--                <option value="{{$val->id_sumber_dana}}"  data-value="{{$val->sumber_dana}}">{{$val->sumber_dana}}</option>-->
                        <!--            @endforeach-->
                        <!--        </select>-->
                        <!--</div>-->
                    </div>
                     </form>
                </div>
            </div>
            
              <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                            <table id="user_table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="cari">ID</th>
                                        <th class="cari">Program Penerimaan</th>
                                        <th class="cari">Program BSZ</th>
                                        <th class="cari">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
        </div>
        
       
       

            
            
        </div>
       
       
          <!--<div class="row">-->
          <!--  <div class="col-lg-12">-->
          <!--      <div class="card">-->
          <!--            <form>-->
          <!--              <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">-->
          <!--                  <table id="user_table" class="table table-striped">-->
          <!--                      <thead>-->
          <!--                          <tr>-->
          <!--                              <th class="cari">ID</th>-->
          <!--                              <th class="cari">Program Penerimaan</th>-->
          <!--                              <th class="cari">Program BSZ</th>-->
          <!--                              <th class="cari">Aksi</th>-->
          <!--                          </tr>-->
          <!--                      </thead>-->
          <!--                  </table>-->
          <!--              </div>-->
          <!--          </div>-->
          <!--           </form>-->
          <!--      </div>-->
          <!--  </div>-->
            
            
        </div>
    </div>
</div>


@endsection