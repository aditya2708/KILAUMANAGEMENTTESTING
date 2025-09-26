@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
         <div class="modal fade" id="modal-default1" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Set Dana Pengelola</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form">
                               
                                <div class="row">
                                    <div class="col-lg-12">
                                        
                                        <!-- <div class="mb-3 row">-->
                                        <!--    <label class="col-sm-3">Periode Dari :</label>-->
                                        <!--    <div class="col-sm-9">-->
                                        <!--        <input  type="date" class=" col-lg-3 mb-3 form-control " id="dari1" name="dari1">-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        
                                        
                                        <!--  <div class="mb-3 row">-->
                                        <!--    <label class="col-sm-3">Sampai :</label>-->
                                        <!--    <div class="col-sm-9">-->
                                        <!--    <input type="date" class=" col-lg-3 mb-3  form-control " id="sampai1" name="sampai1">-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                    
                               
                                        
                                        <div class="mb-3 row">
                                            <label class="col-sm-3">Program :</label>
                                            <div class="col-sm-9">
                                                <select class="js-example-basic-single bawa" name="prog" id="prog" style="width:100%">
                                                    <option value="">- Pilih -</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!--<div class="mb-3 row">-->
                                        <!--    <label class="col-sm-3">Transaksi :</label>-->
                                        <!--    <div class="col-sm-9">-->
                                        <!--         <text id="jmlsnya" ></text>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                    
                            <div class="mb-3 row">
                                <label class="col-sm-3 ">DP Lama</label>
                                 <div class="col-sm-9">
                                  <text id="dpprog" ></text>
                                </div>
                            </div>
                            
                                       
                            <div class="mb-3 row">
                                <label class="col-sm-3 ">DP Baru</label>
                                 <div class="col-sm-9">
                                 <input  type="text" class=" form-control " id="dpbaruoi" name="dpbaruoi">
                                </div>
                            </div>
                                        
                       
                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="red">*Catatan.</label>
                                    <label class="red">Perubahan DP ini Dapat Merubah DP Program yang Dipilih kedepannya</label>
                                </div>
                                
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                             <!--<button type="button" class="btn btn-success btn-sm simpan" id="simpan" >Simpan</button>-->
                             <!--  <button type="button" class="btn btn-success btn-sm update" idp="` + idp + `" id="smp1" >Simpan</button>-->
                            <button type="button" class="btn btn-success smpnnnnz"  id="smp1">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
        <!--ini modal set dp yang lama-->
        <!-- <div class="modal fade" id="modal-default1" >-->
        <!--    <div class="modal-dialog modal-lg" role="document">-->
        <!--        <div class="modal-content">-->
        <!--            <div class="modal-header">-->
        <!--                <h5 class="modal-title" id="exampleModalLabel">Set Dana Pengelola</h5>-->
        <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
        <!--                </button>-->
        <!--            </div>-->
        <!--            <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">-->
        <!--                <div class="modal-body">-->
        <!--                    <div class="basic-form">-->
                               
        <!--                        <div class="row">-->
        <!--                            <div class="col-lg-12">-->
        <!--                                 <div class="mb-3 row">-->
        <!--                                    <label class="col-sm-3">Periode Dari :</label>-->
        <!--                                    <div class="col-sm-9">-->
        <!--                                        <input  type="date" class=" col-lg-3 mb-3 form-control " id="dari1" name="dari1">-->
        <!--                                    </div>-->
        <!--                                </div>-->
                                        
                                        
                                        
        <!--                                  <div class="mb-3 row">-->
        <!--                                    <label class="col-sm-3">Sampai :</label>-->
        <!--                                    <div class="col-sm-9">-->
        <!--                                    <input type="date" class=" col-lg-3 mb-3  form-control " id="sampai1" name="sampai1">-->
        <!--                                    </div>-->
        <!--                                </div>-->
                                        
                                    
                               
                                        
        <!--                                <div class="mb-3 row">-->
        <!--                                    <label class="col-sm-3">Program :</label>-->
        <!--                                    <div class="col-sm-9">-->
        <!--                                        <select class="js-example-basic-single bawa" name="prog" id="prog" style="width:100%">-->
        <!--                                            <option value="">- Pilih -</option>-->
        <!--                                        </select>-->
        <!--                                    </div>-->
        <!--                                </div>-->
                                        
        <!--                                   <div class="mb-3 row">-->
        <!--                                    <label class="col-sm-3">Transaksi :</label>-->
        <!--                                    <div class="col-sm-9">-->
        <!--                                         <text id="jmlsnya" ></text>-->
        <!--                                    </div>-->
        <!--                                </div>-->
                                        
                                    
                          
                                        
        <!--                    <div class="mb-3 row">-->
        <!--                        <label class="col-sm-3 ">DP Lama</label>-->
        <!--                         <div class="col-sm-4">-->
        <!--                          <text id="dpsnya" ></text>-->
        <!--                        </div>-->
                                
        <!--                        <div class="col-sm-1">-->
        <!--                          <text>% =</text>-->
        <!--                        </div>-->
                                
        <!--                        <div class="col-sm-2">-->
        <!--                          <text id="hslnya"></text>-->
        <!--                        </div>-->
        <!--                    </div>-->
                                
                    
        <!--                    <div class="mb-3 row">-->
        <!--                        <label class="col-sm-3 ">DP Baru</label>-->
        <!--                         <div class="col-sm-4">-->
        <!--                          <input  type="text" class=" form-control sss " id="dpbaru" name="dpbaru">-->
        <!--                        </div>-->
                                
        <!--                        <div class="col-sm-1">-->
        <!--                          <text>% =</text>-->
        <!--                        </div>-->
                                
        <!--                        <div class="col-sm-2">-->
        <!--                          <text id="hasilbaru" ></text>-->
        <!--                        </div>-->
        <!--                     </div>-->
                                        
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->

        <!--                </div>-->

        <!--                <div class="modal-footer">-->
        <!--                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>-->
                             <!--<button type="button" class="btn btn-success btn-sm simpan" id="simpan" >Simpan</button>-->
                               <!--<button type="button" class="btn btn-success btn-sm update" idp="` + idp + `" id="smp1" >Simpan</button>-->
        <!--                    <button type="button" class="btn btn-success smpnnnnz"  id="smp1">Simpan</button>-->
        <!--                </div>-->
        <!--            </form>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        
        <div class="modal fade" id="modals" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="totem"> </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form1" enctype="multipart/form-data">
                        <div class="modal-body">
                            
                              <div class="card-body">
                                <div class="table-responsive">
                                    <table id="user_table_1" class="table table-striped">
                                        <thead>
                                        <tr>
                                             <th>Tanggal</th>
                                             <th>Program</th>
                                             <th>Transaksi</th>
                                             <th>Dana Pengelola [DP]</th>
                                             <th>% DP</th>
                                             <th>Donatur</th>
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
        
        
     
        
        
        
        
        
        
        
                 <div class="col-lg-12">
                    <div class="row">
                         <div class="col-xl-4 col-sm-4">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                        <div class="media-body">
                                            <p class="mb-1">Transaksi</p>
                                                <h4 class="mb-0" id="transaksi">0</h4>
                                        </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-xl-4 col-sm-4">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                        <div class="media-body">
                                            <p class="mb-1">Quantity</p>
                                                <h4 class="mt-0" id="qty">0</h4>
                                        </div>
                                </div>
                            </div>
                        </div>
                       
                        
                        
                        
                        <div class="col-xl-4 col-sm-4">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                        <div class="media-body">
                                            <p class="mb-1">Dana Pengelola</p>
                                                <h4 class="mb-0" id="dp">0</h4>
                                        </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                     <form method="GET" action="{{url('resume-dana-pengelola/export')}}" >
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                            <div class="card-body">
                                <div class="basic-form">
                                    <div class="row">
                               <div class="col-lg-3 mb-3">
                                    <label >Periode :</label>
                                        <select class="form-control cekzzz " name="periode" id="periode">
                                            <option value="hari">Hari</option>
                                            <option value="bulan">Bulan</option>
                                        </select>
                                </div>
                             
                                <div class="col-lg-3 mb-3" id="bln" hidden>
                                    <label>Bulan :</label>
                                    <input type="text" class="form-control  cekm" id="month"  name="month" placeholder="{{ date('Y-m') }}">
                                </div>
                                
                                <div class="col-lg-3 mb-3" id="tgldari">
                                    <label>Periode Dari :</label>
                                    <input type="date" class="form-control cekd" id="dari" name="dari">
                                </div>

                                <div class="col-lg-3 mb-3" id="tglke">
                                    <label>Sampai :</label>
                                    <input type="date" class="form-control cekt" id="sampai" name="sampai">
                                </div>
                                
                                
                                 <div class="col-lg-3 mb-3">
                                    <label >Unit :</label>
                                        <select class="form-control cekk" name="kntr" id="kntr">
                                            <option value=""> Semua </option>
                                            @foreach($kantor as $val)
                                            <option value="{{$val->id}}">{{$val->unit}}</option>
                                            @endforeach
                                        </select>
                                </div>
                                
                                
                                
                                <div class="col-lg-3 mb-3">
                                    <label>Sumber Dana</label>
                                    <select class="form-control ceks" name="sdana" id="sdana">
                                        <option value="">- Semua -</option>
                                            @foreach($sumber as $val)
                                            <option value="{{$val->id_sumber_dana}}">{{$val->sumber_dana}}</option>
                                            @endforeach
                                    </select>
                                </div>

                          
                               <div class="col-lg-3 mb-3">
                                    <label>Jenis Transaksi</label>
                                        <select class="form-control cekjt" name="jenis" id="jenis_t">
                                            <option value="semua">Semua</option>
                                            <option value="cash">Cash</option>
                                            <option value="noncash">Non-Cash</option>
                                        </select>
                                        
        
                                </div>
                                
                              
                        </div>
                        
                        
                                <!--<div class="col-md-3 mb-3">-->
                                <!--    <label>Cari DP</label>-->
                                <!--    <input type="text" class="form-control cekdpd" id="dpdari"  placeholder="Dari">-->
                                <!--</div>-->
                                
                                <!-- <div class="col-md-3 mb-3">-->
                                <!--    <label>Cari DP</label>-->
                                <!--    <input type="text" class="form-control cekdps" id="dpsampai" placeholder="Sampai">-->
                                <!--</div>-->
                                
                                 
                        
                        </div>
                        
                       
                            
                </div>  
                 </div>
            </div>
        
        </div>
        
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title">Resume Transaksi Dana Pengelola</h4>
                        <div>
                            <button type="button" class="btn btn-success btn-sm " name="edit" data-bs-toggle="modal" data-bs-target="#modal-default1" >Set DP Program</button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ekspor
                                </button>
                                <ul class="dropdown-menu">
                                    <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>
                                    <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>
                                    <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                                <div class="table-responsive">
                                    <div class="form-check form-switch mb-3" >
                                        <input class="form-check-input" type="checkbox" id="toggle"style="height : 20px; width : 40px;">
                                        <label for="toggle" style=" margin:4px 0 0 4px;">Show/Hide Program dengan transaksi 0</label>
                                    </div>
                                    <table id="user_table" class="table table-striped">
                                        <thead>
                                        <tr>
                                             <th>Program</th>
                                             <th>∑ Transaksi [T]</th>
                                             <th>∑ Quantity</th>
                                             <th>∑ Dana Pengelola [DP]</th>
                                             <!--<th>% DP/T</th>-->
                                        </tr>
                                    </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
@endsection
