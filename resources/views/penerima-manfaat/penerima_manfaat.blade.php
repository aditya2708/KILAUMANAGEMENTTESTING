@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">CORE</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Penerima Manfaat</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                      <form method="GET" action="{{url('pm/export')}}" >
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                        <div class="pull-right">
                        <a href="{{ url('add-pm')}}" type="button" class="btn btn-rounded btn-success btn-sm"><span class="btn-icon-start text-success"><i class="fa fa-plus color-Primary"></i></span>Entri PM</a>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Filter</button>
                   <div class="btn-group">
                                     <button type="button" class="btn btn-info btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">
                                        Ekspor
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><button class="dropdown-item" type="submit" id="xls" value="xls" name="tombol">.XLS</button></li>
                                        <li><button class="dropdown-item" type="submit" id="csv" value="csv" name="tombol">.CSV</button></li>
                                        <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                    </ul>
                                </div>
                        </div>
                    </div>
                       <div class="row d-flex justify-content-center ">
                            <div class="bg-collaps rounded" style="width: 97%;">
                                <div class="collapse" id="collapseExample" >
                                    <div class="row">
                                        <div class=" mb-3 col-md-3">
                                            <label class="form-label mt-3">Jenis</label>
                                                <select required class="form-control input-sm cek" name="jenis" id="jenis">
                                                    <option value="">Pilih Jenis</option>
                                                    <option value="personal">Perorangan</option>
                                                    <option value="entitas">Entitas</option>
                                                </select>
                                        </div>
                                        
                                         <div class=" mb-3 col-md-3">
                                            <label class="form-label mt-3">Status</label>
                                                <select  class="form-control input-sm cek1" name="status" id="status">
                                                    <option value="">Pilih Status</option>
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Non-Aktif</option>
                                                </select>
                                        </div>
                                        
                                         <div class=" mb-3 col-md-3">
                                            <label class="form-label mt-3">Jenis Kelamin</label>
                                                <select  class="form-control input-sm cek2" name="jk" id="jk">
                                                    <option value="">Pilih JK</option>
                                                    <option value="laki-laki">Laki-Laki</option>
                                                    <option value="perempuan">Perempuan</option>
                                                </select>
                                        </div>
                                        
                                        
                                        
                                        <div class=" mb-3 col-md-3">
                                            <br>
                                                <label></label> 
                                                <label class="form-label">Asnaf</label> 
                                                <select id="asnaf" class="cek3 multi"  name="asnaf[]" multiple="multiple" >
                                                    @foreach ($getasnaf as $item)
                                                    <option value="{{$item->id}}">{{$item->asnaf}}</option>
                                                    @endforeach
                                                 </select>
                                        </div>
                                        
                                              
                                                    <div class="col-lg-3 mb-3" id="tgldari">
                                                        <label>Registrasi Dari  :</label>
                                                        <input type="date" class="form-control cek4" id="dari" name="dari">
                                                    </div>
                                                
                                                    <div class="col-lg-3 mb-3" id="tglke">
                                                        <label>Registrasi Sampai:</label>
                                                        <input type="date" class="form-control cek5" id="sampai" name="sampai">
                                                    </div>

                                         <div class="mb-3 col-md-3">
                                                    <label class="form-label">Nomor Hp</label>
                                                    <input type="number" min="0" class="form-control pew cek6" name="nohp" id="nohp" placeholder="Contoh 0851 ">
                                                </div>
                                                
                                                
                                         <div class=" mb-3 col-md-3">
                                                            <label class="form-label">Kantor</label> 
                                                            <select id="kantorz" class="form-control input-sm cek7" name="kantorz"  >
                                                                @foreach ($kantor as $item)
                                                                <option value="{{$item->id}}">{{$item->unit}}</option>
                                                                @endforeach
                                                             </select>
                                                    </div>
                                        
                                        <div class=" mb-3 col-md-3">
                                                <label></label> 
                                                <label class="form-label">PJ</label> 
                                                   <select class="js-example-basic-single1 cek8 multi" style="width: 100%;" name="pj[]" multiple="multiple" id="pj">
                                                        <option value="">Pilih Petugas</option>
                                                    </select>
                                        </div>
                                        
                                  
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    
                    <div class="card-body">
                        <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                            <table id="user_table" class="table table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="cari">Penerima Manfaat</th>
                                        <th class="cari">PJ</th>
                                        <th class="cari">Alamat</th>
                                        <th class="cari">Hp</th>
                                        <th class="cari">Asnaf</th>
                                        <th class="cari">Registrasi</th>
                                        <th class="cari">Kantor</th>
                                        <th class="cari">Aksi</th>
                                        <th></th>
                                        <th></th>
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
@endsection