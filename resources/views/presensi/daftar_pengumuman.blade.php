@extends('template')

@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!-- Modal -->
        <div class="modal fade pengumuman"  id="">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">ENTRY PENGUMUMAN</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form method="post" id="entri-pengumuman" enctype="multipart/form-data">
                        <div class="modal-body gap-4">
                            <div class="form-group mb-3">
                                @if(Auth::user()->kepegawaian != 'admin')
                                    <label for="" class="">Jenis</label>
                                    <select class="form-control" id="jenis">
                                        <option value="">-- PILIH --</option>
                                        <option value="Info">Info</option>
                                        <option value="Lembur">Lembur</option>
                                    </select>
                                @else
                                <label for="" class="">Jenis</label>
                                    <select class="form-control" id="jenis">
                                        <option value="">-- PILIH --</option>
                                        <option value="Info">Info</option>
                                        <option value="Libur">Libur</option>
                                        <option value="Lembur">Lembur</option>
                                        <option value="Cuti bersama">Cuti bersama</option>
                                    </select>
                                @endif
                            </div>
                            
                            <div class="form-group  mb-3" hidden id="jen_lembur">
                                <label for="" class="">Jenis Lembur</label>
                                <select class="form-control" id="j_lembur">
                                    <option value="">-- Jenis Lembur --</option>
                                    <option value="hari">Perhari</option>
                                    <option value="jam">Perjam</option>
                                </select>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="inputPassword" class="">Keterangan</label>
                                <textarea class="form-control isi" id="exampleFormControlTextarea1" style="height:100px;"></textarea>
                            </div>
                            
                             <div class="form-group mb-3">
                                <label for="" class="">Peruntukan</label>
                                <select class="form-control" id="prtuk">
                                    <option value="">-- PILIH --</option>
                                    <option value="1">Perkantor</option>
                                    <option value="2">Perkaryawan</option>
                                </select>
                            </div>
                            
                            <div class="form-group mb-3 select_kantor" hidden>
                                <label for="" class=" col-form-label">Kantor</label>
                                <select class="form-control kankan" id="select_kantor"  name="kantor[]" multiple="multiple">
                                     @foreach ($kantor as $k)
                                        <option value="{{ $k->id }}">{{ $k->unit }}</option>
                                    @endforeach     
                                </select>
                                <div class="form-check mt-3">
                                  <input class="form-check-input check" type="checkbox" value="" id="defaultCheck1">
                                  <label class="form-check-label" for="defaultCheck1">
                                    Pilih semua kantor
                                  </label>
                                </div>
                            </div>
                            
                            <div class="form-group  mb-3 sel_kar" hidden>
                                <label for="" class=" col-form-label">Pilih Karyawan</label>
                               <select class="form-control" id="sel_kar"  name="kar[]" multiple="multiple">
                                   
                                </select>
                                <div class="form-check mt-3">
                                  <input class="form-check-input all_kar" type="checkbox" value="" id="defaultCheck1">
                                  <label class="form-check-label" for="defaultCheck1">
                                    Pilih semua Karyawan
                                  </label>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3" id="jam" hidden>
                                <label for="" class=" col-form-label">Jam </label>
                                <div class="form-group d-flex align-items-center mb-3">
                                    <div class="col-5">
                                      <input type="time" id="jam_awal" class="form-control" placeholder="Awal">
                                    </div>
                                    <div class="col-2">
                                      <div class="text-center">s/d</div>
                                    </div>
                                    <div class="col-5">
                                      <input type="time" id="jam_akhir" class="form-control" placeholder="Awal">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3 " id="tgl_2">
                                <label for="" class=" col-form-label">Tanggal</label>
                                <div class="form-group d-flex align-items-center mb-3">
                                    <div class="col-5 s">
                                      <input type="date" id="date1" class="form-control" placeholder="Awal">
                                    </div>
                                    <div class="col-2 sd ">
                                      <div class="text-center">s/d</div>
                                    </div>
                                    <div class="col-5 sd ">
                                      <input type="date" id="date2" class="form-control" placeholder="Akhir">
                                    </div>
                                </div>
                            </div>
                            <span id="text" class="text-danger"></span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger " data-bs-dismiss="modal" aria-label="Close">Batal</button>
                            <button type="button" class="btn btn-primary" id="simpanKet">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal -->
        <!-- Modal -->
        <div class="modal fade pengumuman3"  id="">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">EDIT PENGUMUMAN</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form method="post" id="entri-pengumuman3" enctype="multipart/form-data">
                        <div class="modal-body gap-4">
                            <div class="form-group d-flex align-items-center justify-content-between">
                                <div class="form-group mb-3 " id="hed">
                                    @if(Auth::user()->kepegawaian != 'admin')
                                        <label for="" class="">Jenis</label>
                                        <select class="form-control" id="jenis1">
                                            <option value="">-- PILIH --</option>
                                            <option value="Info">Info</option>
                                            <option value="Lembur">Lembur</option>
                                        </select>
                                    @else
                                    <label for="" class="">Jenis</label>
                                        <select class="form-control" id="jenis1">
                                            <option value="">-- PILIH --</option>
                                            <option value="Info">Info</option>
                                            <option value="Libur">Libur</option>
                                            <option value="Lembur">Lembur</option>
                                            <option value="Cuti bersama">Cuti bersama</option>
                                        </select>
                                    @endif
                                </div>
                            
                            <div class="form-group col-6 mb-3" hidden id="jen_lembur1">
                                <label for="" class="">Jenis Lembur</label>
                                <select class="form-control" id="j_lembur1">
                                    <option value="">-- Jenis Lembur --</option>
                                    <option value="hari">Perhari</option>
                                    <option value="jam">Perjam</option>
                                </select>
                            </div>
                            </div>
                           
                            
                            <div class="form-group mb-3">
                                <label for="inputPassword" class="">Keterangan</label>
                                <textarea class="form-control isi1" id="exampleFormControlTextarea1" style="height:100px;"></textarea>
                            </div>
                            <div class="form-group d-flex justify-content-between">
                                <div class="form-group col-5 mb-3">
                                    <label for="" class="">Peruntukan</label>
                                    <select class="form-control" id="prtuk1">
                                        <option value="">-- PILIH --</option>
                                        <option value="1">Perkantor</option>
                                        <option value="2">Perkaryawan</option>
                                    </select>
                                </div>
                                 <div class="form-group mb-3 select_kantor1  col-6" >
                                    <label for="" class=" col-form-label">Kantor</label>
                                    <select class="form-control kankan1" id="select_kantor1"  name="kantor[]" multiple="multiple">
                                         @foreach ($kantor as $k)
                                            <option value="{{ $k->id }}">{{ $k->unit }}</option>
                                        @endforeach     
                                    </select>
                                    <div class="form-check mt-3">
                                      <input class="form-check-input check1" type="checkbox" value="" id="defaultCheck1">
                                      <label class="form-check-label" for="defaultCheck1">
                                        Pilih semua kantor
                                      </label>
                                    </div>
                                </div>
                            </div>
                           
                            
                            <div class="form-group  mb-3 sel_kar1" >
                                <label for="" class=" col-form-label">Pilih Karyawan</label>
                               <select class="form-control" id="sel_kar1"  name="kar[]" multiple="multiple">
                                   
                                </select>
                                <div class="form-check mt-3">
                                  <input class="form-check-input all_ka1r" type="checkbox" value="" id="defaultCheck1">
                                  <label class="form-check-label" for="defaultCheck1">
                                    Pilih semua Karyawan
                                  </label>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3" id="jam1" >
                                <label for="" class=" col-form-label">Jam </label>
                                <div class="form-group d-flex align-items-center mb-3">
                                    <div class="col-5">
                                      <input type="time" id="jam_awal1" class="form-control" placeholder="Awal">
                                    </div>
                                    <div class="col-2">
                                      <div class="text-center">s/d</div>
                                    </div>
                                    <div class="col-5">
                                      <input type="time" id="jam_akhir1" class="form-control" placeholder="Awal">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3 " id="tgl_2">
                                <label for="" class=" col-form-label">Tanggal</label>
                                <div class="form-group d-flex align-items-center mb-3">
                                    <div class="col-5 s">
                                      <input type="date" id="date11" class="form-control" placeholder="Awal">
                                    </div>
                                    <div class="col-2 sd ">
                                      <div class="text-center">s/d</div>
                                    </div>
                                    <div class="col-5 sd ">
                                      <input type="date" id="date21" class="form-control" placeholder="Akhir">
                                    </div>
                                </div>
                            </div>
                            <span id="text" class="text-danger">Data tidak sesuai? Batal lalu masuk kembali!</span></span>
                        </div>
                       <div class="modal-footer d-flex justify-content-end edit-but">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal -->
          <!-- Modal -->
        <!--<div class="modal fade pengumuman3" id="">-->
        <!--    <div class="modal-dialog modal-dialog-centered" role="document">-->
        <!--        <div class="modal-content">-->
        <!--            <div class="modal-header">-->
        <!--                <h3 class="modal-title" id="exampleModalLabel">ENTRY PENGUMUMAN</h3>-->
        <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
        <!--                </button>-->
        <!--            </div>-->
        <!--            <form method="post" id="entri-pengumuman3" enctype="multipart/form-data">-->
        <!--                <div class="modal-body gap-4">-->
        <!--                    <div class="form-group mb-3">-->
        <!--                        @if(Auth::user()->kepegawaian != 'admin')-->
        <!--                            <label for="" class="">Jenis</label>-->
        <!--                            <select class="form-control" id="jenis1">-->
        <!--                                <option value="">-- PILIH --</option>-->
        <!--                                <option value="Info">Info</option>-->
        <!--                                <option value="Lembur">Lembur</option>-->
        <!--                            </select>-->
        <!--                        @else-->
        <!--                        <label for="" class="">Jenis</label>-->
        <!--                            <select class="form-control" id="jenis1">-->
        <!--                                <option value="">-- PILIH --</option>-->
        <!--                                <option value="Info">Info</option>-->
        <!--                                <option value="Libur">Libur</option>-->
        <!--                                <option value="Lembur">Lembur</option>-->
        <!--                                <option value="Cuti bersama">Cuti bersama</option>-->
        <!--                            </select>-->
        <!--                        @endif-->
        <!--                    </div>-->
        <!--                    <div class="form-group  mb-3" hidden id="jen_lembur1">-->
        <!--                        <label for="" class="">Jenis Lembur</label>-->
        <!--                        <select class="form-control" id="j_lembur1">-->
        <!--                            <option value="">-- Jenis Lembur --</option>-->
        <!--                            <option value="hari">Perhari</option>-->
        <!--                            <option value="jam">Perjam</option>-->
        <!--                        </select>-->
        <!--                    </div>-->
        <!--                    <div class="form-group mb-3">-->
        <!--                        <label for="inputPassword" class="">Keterangan</label>-->
        <!--                        <textarea class="form-control isi1" id="exampleFormControlTextarea1" style="height:100px;"></textarea>-->
        <!--                    </div>-->
        <!--                     <div class="form-group mb-3">-->
        <!--                        <label for="" class="">Peruntukan</label>-->
        <!--                        <select class="form-control" id="prtuk1">-->
        <!--                            <option value="">-- PILIH --</option>-->
        <!--                            <option value="1">Perkantor</option>-->
        <!--                            <option value="2">Perkaryawan</option>-->
        <!--                        </select>-->
        <!--                    </div>-->
        <!--                    <div class="form-group mb-3 select_kantor1" hidden>-->
        <!--                        <label for="" class=" col-form-label">Kantor</label>-->
        <!--                       <select class="form-control kankan1" id="select_kantor1"  name="kantor[]" multiple="multiple">-->
        <!--                             @foreach ($kantor as $k)-->
        <!--                                <option value="{{ $k->id }}">{{ $k->unit }}</option>-->
        <!--                            @endforeach     -->
        <!--                        </select>-->
        <!--                        <div class="form-check mt-3">-->
        <!--                          <input class="form-check-input check" type="checkbox" value="" id="defaultCheck1">-->
        <!--                          <label class="form-check-label" for="defaultCheck1">-->
        <!--                            Pilih semua kantor-->
        <!--                          </label>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <div class="form-group  mb-3 sel_kar1" hidden>-->
        <!--                        <label for="" class=" col-form-label">Pilih Karyawan</label>-->
        <!--                        <select class="form-control" id="sel_kar1"  name="kar[]" multiple="multiple">-->
        <!--                                @foreach ($user as $k)-->
        <!--                                    <option value="{{ $k->id }}">{{ $k->name }}</option>-->
        <!--                                @endforeach -->
        <!--                        </select>-->
        <!--                        <div class="form-check mt-3">-->
        <!--                          <input class="form-check-input all_kar1" type="checkbox" value="" id="defaultCheck1">-->
        <!--                          <label class="form-check-label" for="defaultCheck1">-->
        <!--                            Pilih semua Karyawan-->
        <!--                          </label>-->
        <!--                        </div>-->
        <!--                    </div>-->
                            
        <!--                      <div class="form-group mb-3" id="jam1" hidden>-->
        <!--                        <label for="" class=" col-form-label">Jam </label>-->
        <!--                        <div class="form-group d-flex align-items-center mb-3">-->
        <!--                            <div class="col-5">-->
        <!--                              <input type="time" id="jam_awal1" class="form-control" placeholder="Awal">-->
        <!--                            </div>-->
        <!--                            <div class="col-2">-->
        <!--                              <div class="text-center">s/d</div>-->
        <!--                            </div>-->
        <!--                            <div class="col-5">-->
        <!--                              <input type="time" id="jam_akhir1" class="form-control" placeholder="Awal">-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->
                            
        <!--                    <div class="form-group mb-3">-->
        <!--                        <label for="" class=" col-form-label">Tanggal</label>-->
        <!--                        <div class="form-group d-flex align-items-center mb-3">-->
        <!--                            <div class="col-5">-->
        <!--                              <input type="date" id="date11" class="form-control" placeholder="Awal">-->
        <!--                            </div>-->
        <!--                            <div class="col-2">-->
        <!--                              <div class="text-center">s/d</div>-->
        <!--                            </div>-->
        <!--                            <div class="col-5">-->
        <!--                              <input type="date" id="date21" class="form-control" placeholder="Akhir">-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <span id="text" class="text-danger"></span>-->
        <!--                </div>-->
        <!--                <div class="modal-footer d-flex justify-content-end edit-but">-->
        <!--                </div>-->
        <!--            </form>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        <!-- End Modal -->
        
           <!-- Modal -->
        <div class="modal fade pengumuman2" id="">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Pengumuman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form id="entri-pengumuman" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="pen">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table width="100%">
                                                <tbody>
                                                    <tr style="height: 30px;">
                                                        <td style="vertical-align:top; width:25%;">Keterangan</td>
                                                        <td style="vertical-align:top; width:5%;"> : </td>
                                                        <td style="vertical-align:top; width:50%;" id="detail_ket"></td>
                                                    </tr>
                                                    <tr style="height: 30px;">
                                                        <td style="vertical-align:top; width:25%;">Jenis</td>
                                                        <td style="vertical-align:top; width:5%;"> : </td>
                                                        <td style="vertical-align:top;"  id="detail_jenis"></td>
                                                    </tr>
                                                   
                                                    <tr style="height: 30px;" id="hidlem" hidden>
                                                        <td style="vertical-align:top; width:25%;">Jam Lembur</td>
                                                        <td style="vertical-align:top; width:5%;"> : </td>
                                                        <td style="vertical-align:top;"><div class="d-flex flex-wrap gap-2 mb-2" id="detail_jam"></div></td>
                                                    </tr>
                                                    <tr style="height: 30px;" class='mt-1'>
                                                        <td style="vertical-align:top; width:25%;" id="get_jenis"></td>
                                                        <td style="vertical-align:top; width:5%;"> : </td>
                                                        <td style="vertical-align:top;" id="detail_tgl"></td>
                                                    </tr>
                                                     <tr style="height: 30px;">
                                                        <td style="vertical-align:top; width:25%;">Untuk Kantor</td>
                                                        <td style="vertical-align:top; width:5%;"> : </td>
                                                        <td style="vertical-align:top;"><div class="d-flex flex-wrap gap-2 mb-2" id="detail_kantor"></div></td>
                                                    </tr>
                                                     <tr style="height: 30px;">
                                                        <td style="vertical-align:top; width:25%;">Kepada</td>
                                                        <td style="vertical-align:top; width:5%;"> : </td>
                                                        <td style="vertical-align:top;"><div class="d-flex flex-wrap gap-2 mb-2" id="karr"></div></td>
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer button_foot">
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal -->
        
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header justify-content-start gap-2" >
                            <button class="btn btn-primary entry-pen  btn-xxs" id=""  data-bs-toggle="modal" data-bs-target=".pengumuman">Entri pengumuman</button>
                            @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                <button type="button" class="btn btn-primary btn-xxs " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                            @endif
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="basic-form mb-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <input type="hidden" id="idCom" name="com"/>
                                                <div class="col-lg-3 mb-3">
                                                    <label>Jenis</label>
                                                    <select class="form-control" id="fil_jenis">
                                                        <option value="">Pilih Jenis</option>
                                                        <option value="Info">Info</option>
                                                        <option value="Libur">Libur</option>
                                                        <option value="Lembur">Lembur</option>
                                                        <option value="Cuti bersama">Cuti bersama</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-3  mb-3 d-flex flex-column">
                                                    <label>Kantor</label>
                                                    <select class="form-control memek" id="f_kantor" name="kantor[]" multiple="multiple">
                                                        @foreach ($kantor as $k)
                                                            <option value="{{ $k->id }}">{{ $k->unit }}</option>
                                                        @endforeach     
                                                    </select>
                                                </div>
                                                <div class="col-lg-3  mb-3">
                                                    <label>Tanggal awal</label>
                                                    <input class="form-control" id="fil_awal" type='date' />
                                                </div>
                                                <div class="col-lg-3  mb-3">
                                                    <label>Tanggal akhir</label>
                                                    <input class="form-control" id="fil_akhir"  type='date' />
                                                </div>
                                                <div class="col-lg-3  mb-3 mt-4">
                                                    <button type="button" class="btn btn-primary light" id="fil_reset">Reset Filter</button>
                                                </div>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                         </form>
                        <div class="table-responsive">
                            <table id="data-pengumuman" class="table  table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Keterangan</th>
                                        <th>Jenis</th>
                                        <th>Kantor</th>
                                        <th>Tanggal awal</th>
                                        <th>Tanggal akhir</th>
                                        <th></th>
                                        <!--<th></th>-->
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection