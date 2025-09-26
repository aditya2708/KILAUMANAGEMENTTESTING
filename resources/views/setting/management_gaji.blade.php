@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Setting</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Management Gaji</a></li>-->
        <!--    </ol>-->
        <!--</div>-->
        <?php $countjabat = App\Models\Jabatan::get()->count(); ?>

        <!-- Modal -->
        <div class="modal fade" id="tunjangan">
            <div class="modal-dialog modal-lg" role="document" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Tunjangan Jabatan</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <span id="form_edit"></span>
                    <form method="post" id="tunjangan_form">
                        <div class="modal-body">

                            @csrf
                            <div id="cobb">

                            </div>
                            <!--<div id='map1' style='width: auto; height: 500px;'></div>-->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="daerah">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 700px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Tunjangan Daerah</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <span id="form_editt"></span>
                    <form method="post" id="daerah_form">
                        <div class="modal-body">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="">Provinsi :</label>
                                        <select class="js-example-basic-single" style="width: 100%;" name="provinsi" id="provinsi">
                                            <option value=""></option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="">Kota :</label>
                                        <select class="js-example-basic-single" style="width: 100%;" name="kota" id="kota">
                                            <option value=""></option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <label for="">Tunjangan Daerah :</label>
                                        <input type="text" name="tunjangan_daerah" id="tunjangan_daerah" class="form-control input-sm" value="" placeholder="Rp. 0">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <label for="">Tunjangan Pejabat Daerah :</label>
                                        <input type="text" name="tj_pejabat_daerah" id="tj_pejabat_daerah" class="form-control input-sm" value="" placeholder="Rp. 0">
                                        <input type="hidden" name="id" id="id" value="0">
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <label for="">UMK :</label>
                                        <input type="text" name="umk" id="umk" class="form-control input-sm" value="" placeholder="Rp. 0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">

                                    <div class="form-group">
                                        <button type="button" class="btn btn-success btn-sm " id="add_daerah" style="width:100%"> Submit</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="user_table_1" class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="vertical-align: middle;">Kota</th>
                                            <th colspan="2">Tunjangan</th>
                                            <th rowspan="2" style="vertical-align: middle;">UKM</th>
                                            <th colspan="2" rowspan="2"></th>
                                        </tr>

                                        <tr>
                                            <th>Daerah</th>
                                            <th>Pejabat Daerah</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table">

                                    </tbody>
                                    <!--<tfoot id="foot">-->

                                    <!--</tfoot>-->
                                </table>
                            </div>

                            <!--</div>-->
                            <input type="hidden" name="action" id="action" value="add" />
                            <input type="hidden" name="index" id="index" value="" />
                        </div>
                        <!--<div id='map1' style='width: auto; height: 500px;'></div>-->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="bpjs">
            <div class="modal-dialog modal-lg" role="document" style="max-width: 600px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Presentase BPJS</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>

                    <span id="form_result"></span>
                    <form method="post" id="bpjs_form">
                        <div class="modal-body">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Nama Jenis BPJS</label>
                                </div>
                                <div class="col-md-4">
                                    <label>Presentase Perusahaan</label>
                                </div>
                                <div class="col-md-4">
                                    <label>Presentase Karyawan</label>
                                </div>
                            </div>
                            
                
                            <div class="row">
                                <div class='id_com' ></div>
                                <div class="col-md-4" >
                                    @foreach($bpjsnama as $val)
                                         <div style="margin-top:20px">
                                            <!--<input type="hidden" id="com" name="com" class="form-control" value="">-->
                                            <input type="text" id="jenis" name="jenis[]" class="form-control" value="{{$val->nama_jenis}}" ReadOnly>
                                        </div>

                                        <!--<div class="col-md-4">-->
                                        <!--    <div class="input-group">-->
                                        <!--        <input type="text" id="persen_perusahaan" name="perusahaan[]" value="{{$val->perusahaan}}" class="form-control" onkeyup="btn()" placeholder="contoh 2.5">-->
                                        <!--        <span class="input-group-text" style="background:#777; color:#FFF">%</span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-4">-->
                                        <!--    <div class="input-group">-->
                                        <!--        <input type="text" id="persen_karyawan" name="karyawan[]" class="form-control" value="{{$val->karyawan}}" onkeyup="btn()" placeholder="contoh 2.5">-->
                                        <!--        <span class="input-group-text" style="background:#777; color:#FFF">%</span>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                                    @endforeach
                                </div>
                                <div class="col-md-4" id= 'datapersen_perus' ></div>
                                <div class="col-md-4" id= 'datapersen_karyawan'></div>
                            </div>
                         
                      
                            
                          
                            <!--<div class="row" style="margin-top:20px">-->
                            <!--     <div class="col-md-4">-->
                            <!--        <div class="input-group">-->
                            <!--            <input type="text" id="persen_karyawan" name="karyawan[]" class="form-control" value="{{$val->karyawan}}" onkeyup="btn()" placeholder="contoh 2.5">-->
                            <!--            <span class="input-group-text" style="background:#777; color:#FFF">%</span>-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--</div>-->
                          
                            
                            <input type="hidden" name="id_hehe" id="id_hehe" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="updatebpjs" class="btn btn-primary" disabled>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="usermobile">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 600px">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>

                    <span id="form_result"></span>
                    <form method="post" id="user_form">
                        <div class="modal-body">
                            @csrf
                            <div class='id_com' ></div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>SPV Kolektor</label></td>
                                    <div class="form">
                                        <select required class="js-example-basic-single1" style="width: 100%;" name="spv" id="spv">
                                            @if(count($jabatan) > 0)
                                                <option selected="selected" value="">Pilih SPVKolektor</option>
                                            @foreach($jabatan as $c)
                                                <option value="{{ $c->id }}"> {{$c->jabatan}}</option>
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">Pilih SPV Kolektor</option>-->
                                            <!--@foreach ($jabatan as $j)-->
                                            <!--<option value="{{$j->id}}">{{$j->jabatan}}</option>-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Kolektor</label></td>
                                    <div class="form">

                                        <select required class="js-example-basic-single" style="width: 100%;" name="jabatan" id="jabatan">
                                            @if(count($jabatan) > 0)
                                                <option selected="selected" value="">Pilih Jabatan</option>
                                            @foreach($jabatan as $c)
                                                <option value="{{ $c->id }}"> {{$c->jabatan}}</option>
                                            @endforeach
                                            @else
                                            @endif
                                            <!--<option value="">Pilih Jabatan</option>-->
                                            <!--@foreach ($jabatan as $j)-->
                                            <!--<option value="{{$j->id}}">{{$j->jabatan}}</option>-->
                                            <!--@endforeach-->
                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Sales Officer</label></td>
                                    <div class="form">

                                        <select required class="js-example-basic-single" style="width: 100%;" name="sokotak" id="sokotak">
                                            @if(count($jabatan) > 0)
                                                <option selected="selected" value="">Pilih Jabatan</option>
                                            @foreach($jabatan as $c)
                                                <option value="{{ $c->id }}"> {{$c->jabatan}}</option>
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">Pilih Jabatan</option>-->
                                            <!--@foreach ($jabatan as $j)-->
                                            <!--<option value="{{$j->id}}">{{$j->jabatan}}</option>-->
                                            <!--@endforeach-->
                                        </select>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>SPV Program</label></td>
                                    <div class="form">
                                        <select required class="js-example-basic-single1" style="width: 100%;" name="spv_so" id="spv_so">
                                            @if(count($jabatan) > 0)
                                                <option selected="selected" value="">Pilih SPV Kolektor</option>
                                            @foreach($jabatan as $c)
                                                <option value="{{ $c->id }}"> {{$c->jabatan}}</option>
                                            @endforeach
                                            @else
                                            @endif
                                                
                                            <!--<option value="">Pilih SPV Kolektor</option>-->
                                            <!--@foreach ($jabatan as $j)-->
                                            <!--<option value="{{$j->id}}">{{$j->jabatan}}</option>-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Fundraiser Officer</label></td>
                                    <div class="form">
                                        <select required class="js-example-basic-single" style="width: 100%;" name="so" id="so">
                                            @if(count($jabatan) > 0)
                                                <option selected="selected" value="">Pilih Jabatan</option>
                                            @foreach($jabatan as $c)
                                                <option value="{{ $c->id }}"> {{$c->jabatan}}</option>
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">Pilih Jabatan</option>-->
                                            <!--@foreach ($jabatan as $j)-->
                                            <!--<option value="{{$j->id}}">{{$j->jabatan}}</option>-->
                                            <!--@endforeach-->
                                        </select>

                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="id_h" id="id_h" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="terlambat">
            <div class="modal-dialog" style="max-width: 600px">
                <div class="modal-content">
                <form method="post" id="terlambat_form">
                    <div class="modal-header">
                        <h4>Setting Presensi</h4>
                        <button type="button" class="btn btn-sm btn-primary" id="addrule2"><i class="fa fa-plus"></i> Tambah</button>
                        <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->

                        </button>
                    </div>

                        <div class="modal-body">
                            <!--<div class="row">-->
                            <!--    <div class="col-md-12">-->
                            <!--        <button type="button" class="btn btn-sm btn-primary" id="addrule2">Tambah Setting Keterlambatan</button><br><br>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <h5>Setting Keterlambatan</h5>
                            <div id="newrule2">

                            </div>
                            
                            <br>
                            <br>
                            <h5>Setting Potongan Tidak Laporan & Presensi</h5>
                            <div class='id_com' ></div>
                            <div class="basic-form">
                                <div class="mb-3 row">
                                    <label class="col-sm-6 col-form-label">Tidak Laporan / Presensi Pulang</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="pul" name="pul" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                 </div>
                                 
                                 <div class="mb-3 row">
                                    <label class="col-sm-6 col-form-label">Tidak Laporan & Presensi Pulang</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="lappul" name="lappul" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                                    </div>
                                 </div>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="exampleModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Edit Tunjangan</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <span id="form_result"></span>
                    <form method="post" id="sample_form">
                        <div class="modal-body">
                            @csrf
                            <div class="form mb-3">
                                <label>Tunjangan Beras / kg</label>
                                <input type="text" id="tj_beras" name="tj_beras" class="form-control" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">

                            </div>
                            <div class="form mb-3">
                                <label>Jumlah Beras</label>
                                <div class="input-group">
                                    <input type="text" id="jml_beras" name="jml_beras" class="form-control" onkeyup="Angka(this);">
                                    <span class="input-group-text" style="background:#777; color:#FFF">kg</span>
                                </div>
                            </div>
                            <div class="form mb-3">
                                <label>Tunjangan Pasangan</label>
                                <div class="input-group">
                                    <input type="text" id="tj_pasangan" name="tj_pasangan" class="form-control" onkeyup="Angka(this);">
                                    <span class="input-group-text" style="background:#777; color:#FFF">%</span>
                                </div>
                            </div>
                            <div class="form mb-3">
                                <label>Tunjangan Anak</label>
                                <div class="input-group">
                                    <input type="text" id="tj_anak" name="tj_anak" class="form-control" onkeyup="Angka(this);">
                                    <span class="input-group-text" style="background:#777; color:#FFF">%</span>
                                </div>
                            </div>
                            <div class="form mb-3">
                                <label>Transport Staff</label>
                                <input type="text" id="tj_transport" name="tj_transport" class="form-control" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);" </div>
                            </div>
                            <div class="form mb-3">
                                <label>Potongan Kolekting Per-Donatur</label>
                                <input type="text" id="potongan" name="potongan" class="form-control" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);" </div>
                            </div>
                            <div class="form mb-3">
                                <label>Tunjangan PLT</label>
                                <div class="input-group">
                                    <input type="text" id="tj_plt" name="tj_plt" class="form-control" onkeyup="Angka(this);">
                                    <span class="input-group-text" style="background:#777; color:#FFF">%</span>
                                </div>
                            </div>
                            <input type="hidden" name="id_tj" id="id_tj" />
                            <!--<div id='map1' style='width: auto; height: 500px;'></div>-->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="skemaGaji">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" >Skema Gaji</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="table-responsive">
                                <div class="row ">
                                    <div class="col-md-6 mb-3">
                                        <label>Unit</label>
                                        <select class="form-control aa" name="unite" id="unite" style="width: 100%">
                                            @if(count($kota) > 0)
                                                <option value="">Pilih Unit</option>
                                                @foreach($kota as $kk)
                                                <option value="{{$kk->id}}">{{$kk->unit}}</option>
                                                @endforeach
                                            @else
                                                <option value="">Tidak ada</option>
                                            @endif
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label>Skema Gaji mb-3 </label>
                                        
                                        
                                        
                                        <select class="form-control bb" name="skema" id="skema" style="width: 100%">
                                        </select>
                                        
                                    </div>
                                </div>
                                <table width="100%" id="tableku" style="margin-left: 20px">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama</th>
                                            <th>Skema Gaji</th>
                                        </tr>
                                    </thead>
                                    <tbody id="isinya">
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--<div class="modal-footer">-->
                        <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>-->
                        <!--<button type="submit" class="btn btn-primary">Simpan</button>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="detailSkema">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" >Detail SKema Gaji <span id="sutor"></span></h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-target="#skemaGaji" data-bs-toggle="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <select id="skemaIN" name="skemaIN" class="form-control mb-3 ahu">
                                        
                                </select>
                            </div>
                            <input type="hidden" id="id_karr">
                            <input type="hidden" id="id_skemaa">
                            
                        </div>
                        <div class="row">
                            <input id="simpenSkema" type="hidden">
                            <div class="card" style="overflow-y: scroll; height: 350px">
                                <div class="card-body border rounded border-success mb-3" id="card1" style="display: none">
                                    <h4>Bonus</h4>
                                    <div id="bon"></div>
                                </div>
                                
                                <div class="card-body border rounded border-primary mb-3" id="card2" style="display: none">
                                    <h4>Gaji Utama</h4>
                                    <div id="muns"></div>
                                </div>
                                
                                <div class="card-body border rounded border-info mb-3" id="card3" style="display: none">
                                    <h4>BPJS</h4>
                                    <div id="bpjsss"></div>
                                </div>
                                
                                <div class="card-body border rounded border-danger mb-3" id="card4" style="display: none">
                                    <h4>Potongan</h4>
                                    <div id="pot"></div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="solo">
                                        
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <!--<h4 class="card-title">-->
                            <!--Management Gaji-->
                        <!--</h4>-->
                     
                        
                        <div class="pull-left"> 
                            <a href="javascript:void(0)" class="btn btn-sm btn-primary editoo tomjab"  style="float: left; margin-right: 10px">Tunjangan Jabatan</a>
                            <a href="javascript:void(0)" class="btn btn-sm btn-info sotoy tomdae"   style="float: left; margin-right: 10px">Tunjangan Daerah</a>
                            <a href="javascript:void(0)" class="btn btn-sm btn-warning gurih tombpjs"    style="float: left; margin-right: 10px">Presentasi BPJS</a>
                            <a href="javascript:void(0)" class="btn btn-sm btn-success userrr tomidjab"  style="float: left; margin-right: 10px">Id Jabatan</a>
                            <a href="javascript:void(0)" class="btn btn-sm btn-danger terlambat hukuman tompres"   style="float: left; margin-right: 10px" data-bs-toggle="tooltip" data-bs-placement="top" title="Setting Prsensi">Presensi</a>
                            <a href="javascript:void(0)" class="btn btn-sm btn-primary silsil tomji"   style="float: left; margin-right: 10px">Skema Gaji</a>
                            @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                <!--<button type="button" class="btn btn-primary btn-xxs " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>-->
                                <a href="javascript:void(0)" id="button-perusahaan" class="btn btn-sm btn-primary pencet" data-bs-toggle="modal" data-bs-target="#modalPerusahaan" style="float: left; margin-right: 10px">Pilih Perusahaan</a>

                            @endif
                            <!--<a href="javascript:void(0)" class="btn btn-sm btn-danger hukuman" data-bs-toggle="modal" data-bs-target="#hukuman" style="float: right; margin-left: 10px">Presensi</a>-->
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <!--<div class="table-responsive">-->
                                    <table id="user_table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tunjangan Beras / kg</th>
                                                <th>Jumlah Beras</th>
                                                <th>Tunjangan Pasangan</th>
                                                <th>Tunjangan Anak</th>
                                                <th>Transport Staff</th>
                                                <th>Potongan Kolekting Per-Donatur</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>

                                        </tfoot>
                                    </table>
                                <!--</div>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection