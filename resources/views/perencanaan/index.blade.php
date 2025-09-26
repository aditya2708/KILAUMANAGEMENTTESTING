@extends('template')
@section('konten')

<!--<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css" rel="stylesheet">-->

<div class="content-body">
    
    <style>
        #calendar {
            max-width: 1100px;
            margin: 0 auto;
        }
        .fc-event {
            cursor: pointer;
        }
        .fc .fc-view-harness{
            /*height: 650px !important;*/
            overflow-y: none;
        }
    </style>
    
    <!-- Modal -->
        <div class="modal fade"  id="rencana"  data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"  style="max-width: 90%;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Modal Rencana <span id="names"></span></h3>
                        <div style="float: right">
                            <!--<a class="btn btn-sm btn-info" id="tmbhmrkt" data-bs-target="#marketings" data-bs-toggle="modal" data-bs-dismiss="modal"><i class="fa fa-plus"></i> Marketing</a>-->
                            <!--<button type="button" class="btn btn-sm btn-primary" id="tambahTugas"><i class="fa fa-plus"></i> Tambah</button>-->
                            <a class="btn btn-sm btn-primary tambahrencana" data-bs-target="#tugastambah" data-bs-toggle="modal" data-bs-dismiss="modal"><i class="fa fa-plus"></i> Tambah</a>
                        </div>
                    </div>
                        <div class="modal-body">
                            <input type="hidden" id="nyimpen">
                            <input type="hidden" id="nyimtgl">
                            <input type="hidden" id="tgljadi">
                            <div id="calendar"></div>
                            
                            <!--<div class="d-flex flex-wrap" id="curut">-->
                    			
                    			
                            <!--</div>-->
                        </div>
                        
                      <!--  <div class="modal-body" >-->
                            <!--id="cirit"  style="display: none"-->
                    		<!--<div class="table-responsive">-->
                    		<!--    <table class="table table-striped" width="100%" id="hg">-->
                    		<!--        <thead>-->
                    		            
                    		<!--            <tr>-->
                    		<!--                <th>#</th>-->
                    		<!--                <th>Tugas</th>-->
                    		<!--                <th>Parent</th>-->
                    		<!--                <th>Durasi</th>-->
                    		<!--                <th>Tanggal</th>-->
                    		<!--                <th>Tanggal AKhir</th>-->
                    		<!--                <th>Capian</th>-->
                    		<!--                <th>Target</th>-->
                    		<!--	            <th>Tanggal Selesai</th>-->
                    		<!--	            <th>Pemberi Tugas</th>-->
                    		<!--	            <th>Aksi</th>-->
                    		<!--	            <th></th>-->
                    		<!--	        </tr>-->
                    		<!--	      </thead>-->
                    		<!--	      <tbody id="vcc">-->
                    			            
                    		<!--	      </tbody>-->
                    		<!--	</table>-->
                      <!--      </div>-->
                      <!--  </div>-->
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal" aria-label="Close" >Tutup</button>
                            <!--id="ttps" style="display: block"-->
                            <!--<button type="button" class="btn btn-danger btn-sm" id="syus" style="display: none">kembali</button>-->
                            <!--<button type="submit" class="btn btn-primary" id="simpanTugas">Simpan</button>-->
                        </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade"  id="detailss" >
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"  style="max-width:60%;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Detail Rencana <span id="namess"></span></h3>
                        <div class="pull-right">
                            
                                <!--<div class="btn-group me-2">-->
                                <!--    <button type="button" class="btn btn-rounded btn-success btn-xxs dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Ekspor Data" style="width:100%;">-->
                                <!--        <i class="fa fa-download"></i> Export-->
                                <!--    </button>-->
                                <!--    <ul class="dropdown-menu" style="margin: 0px;">-->
                                <!--        <li><button class="dropdown-item exp" type="submit" id="xls" value="xls" name="tombol" >.XLS</button></li>-->
                                        <!--<li><button class="dropdown-item exp" type="submit" id="pdf" value="pdf" name="tombol" >.PDF</button></li>-->
                                        <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                <!--    </ul>-->
                                <!--</div>-->
                        </div>
                    </div>
                        
                        <div class="modal-body" >
                            <div class="row">
                                <style>
                                    .table-bordered > tbody > tr > td, 
                                    .table-bordered > tbody > tr > th, 
                                    .table-bordered > tfoot > tr > td, 
                                    .table-bordered > tfoot > tr > th, 
                                    .table-bordered > thead > tr > td, 
                                    .table-bordered > thead > tr > th 
                                    {
                                        border: 1px solid #373737; 
                                    }
                                </style>
                                <div class="col-md-12">
                                    <div class="table-responsive">
                            		    <table class="table table-bordered " width="100%" id="hoxxx">
                            		        <thead>
                            		            <tr>
                                                    <td>Bagian</td>
                                                    <td>Jenis</td>
                                                    <td>Rencana</td>
                                                    <td>Satuan</td>
                                                    <td>Metode</td>
                                                    <td>Target</td>
                                                    <td width="10%">Mulai</td>
                                                    <!--<td width="10%">Selesai</td>-->
                                                    
                                                </tr>
                            			      </thead>
                            			      <tbody id="hoccc">
                            			            
                            			      </tbody>
                            			</table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal" aria-label="Close" >Tutup</button>
                        </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="laporan" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h3 class="modal-title">Modal Laporan</h3>
                        <button type="button" class="btn-close" id="semestay" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-success">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Rencana</h4>
                                        <!--<div class="box-tools pull-right">-->
                                        <!--    <div id="ehe">-->

                                        <!--    </div>-->
                                        <!--</div>-->
                                    </div>
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="hehed" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Tanggal</th>
                                                        <th>Tugas</th>
                                                        <th>Capaian</th>
                                                        <th>Target</th>
                                                        <th>Status</th>
                                                        <th>Aksi</th>s
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="ooh">
                                                    
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="box box-success">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Laporan</h4>
                                        <!--<div class="box-tools pull-right">-->
                                        <!--    <div id="tgl">-->

                                        <!--    </div>-->
                                        <!--</div>-->
                                    </div>
                                    <div class="box-body">
                                        <div id="lapo">

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Progress Kerjaan</h4>
                                    </div>
                                    <div class="box-body">
                                        <div id="progres">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="box box-primary collapsed-box">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Feedback Atasan</h4>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div id="cons">

                                        </div>
                                        
                                        <form method="post" id="aplodfeed">
                                            @csrf
                                            <div id="couu">
                                            
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!--<div class="modal-footer">-->
                        <!--<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Kembali</button>-->
                        <!--<button type="button" class="btn btn-primary">Save changes</button>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
        
        <div class="modal fade"  id="tugastambah" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"  style="max-width: 75%;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Tambah Tugas</h3>
                    </div>
                    <form id="formy">
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group mb-3 col-sm-12 col-md-4">
                                    <label>Tugas Proses</label>
                                    <select class="form-control ewwwwws" name="tugas_bl" id="tugas_bl">
                                        <option value="" ></option>
                                    </select>
                                    <input type="hidden" id="rumus">
                                </div>
                                <style>
                                    
                                    .form-control-color{width:3rem;height:calc(1.5em + .75rem + calc(var(--bs-border-width) * 2));padding:.375rem}
                                </style>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-1">
                                    <label>Warna</label>
                                    <input type="color" class="form-control form-control-color" id="warna" name="warna" value="#5d4286" title="Choose a color">
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-4">
                                    <label>Deskripsi</label>
                                    <textarea class="form-control tugasTextarea" name="tugas" id="tugas" oninput="checkMaxLength()"></textarea>
                                    <small>*tuliskan secara singkat</small>
                                </div>
                                
                                <!--<div class="form-group mb-3 col-sm-12 col-md-2">-->
                                <!--    <label>Target</label>-->
                                <!--    <input type="number" min="0.0" name="target" max="100" class="form-control" id="target">-->
                                <!--</div>-->
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2" id="targss1">
                                    <label>Total Target</label>
                                    <input class="form-control" type="number" min="0"  name="totar" id="totar" readonly/>
                                    <input name="totar_hide" class="uhu"  id="totar_hide" type="hidden"/>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2" id="targss2">
                                    <label>Sisa Target</label>
                                    <input class="form-control" type="number" min="0" max="100"  step="1" name="sistar" id="sistar" readonly/>
                                    <input name="sistar_hide" class="cuek" id="sistar_hide" type="hidden"/>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2" id="targss11" style="display: none">
                                    <label>Total Target</label>
                                    <div class="input-group">
                                      <input type="number" min="0" max="100" step="0.1" class="form-control" name="totar2" id="totar2" onchange="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" oninput="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" readonly>
                                      <span class="input-group-text">%</span>
                                    </div>
                                    <input name="totar2_hide" class="uhu" id="totar2_hide" type="hidden"/>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2" id="targss22" style="display: none">
                                    <label>Sisa Target</label>
                                    <div class="input-group">
                                      <input type="number" min="0" max="100" step="0.1" class="form-control" name="sistar2" id="sistar2" onchange="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" oninput="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" readonly>
                                      <span class="input-group-text">%</span>
                                    </div>
                                    <input name="sistar2_hide" class="cuek"  id="sistar2_hide" type="hidden"/>
                                </div>
                                
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2" id="targ1">
                                    <label>Target</label>
                                    <input class="form-control cieh" type="number" min="0"  name="target" id="target" autocomplete="off"/>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2" id="targ2" style="display: none">
                                    <label>Target</label>
                                    <div class="input-group">
                                      <input type="number" min="0" max="100" step="0.1" class="form-control cieh" name="target2" id="target2" onchange="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" oninput="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" autocomplete="off">
                                      <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                
                                <!--<div class="form-group mb-3 col-sm-12 col-md-2">-->
                                <!--    <label>Bobot</label>-->
                                <!--    <input class="form-control " type="number" min="0"  name="bobot" id="bobot" autocomplete="off"/>-->
                                <!--</div>-->
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2">
                                    <label>Durasi</label>
                                    <select class="form-control" name="durasi" id="durasi">
                                        <option value="daily">Daily</option>
                                        <option value="range">Range</option>
                                    </select>
                                </div>
                                
                                <!--<div class="form-group mb-3 col-sm-12 col-md-2">-->
                                <!--    <label>Disable Weekend</label>-->
                                <!--    <select class="form-control" name="day" id="day">-->
                                <!--        <option value="0">Ya</option>-->
                                <!--        <option value="1">Tidak</option>-->
                                <!--    </select>-->
                                <!--</div>-->
                                
                                <div class="form-group mb-3 col-sm-12 col-md-3" id="dailyy">
                                    <label>Tanggal</label>
                                    <div class="input-group">
                                        <input type="date" name="tglAwal" class="form-control tglAwal" id="tglAwal">
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-3" id="rangee">
                                    <label>Tanggal</label>
                                    <input type="hidden" id="tgl_Awal" name="tgl_Awal[]" multiple="multiple">
                                    <input class="form-control" name="daterange" id="daterange" autocomplete="off">
                                    <div class="form-check">
                                      <input class="form-check-input" type="checkbox" checked id="disableweekends">
                                      <label class="form-check-label">
                                        Hilangkan Hari Libur
                                      </label>
                                    </div>
                                    <!--<div class="input-group">-->
                                    <!--    <input type="date" name="tglAwals" class="form-control tglAwal" id="tglAwals">-->
                                    <!--    <span class="input-group-text" style="background:#fff; color:#777">s/d</span>-->
                                    <!--    <input type="date" name="tglAkhir" class="form-control tglAkhir" id="tglAkhir">-->
                                    <!--</div>-->
                                </div>
                                
                                
                                
                                <input type="hidden" name="id_k" id="id_k">
                                
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-bs-target="#rencana" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="ttps">Kembali</button>
                            <button type="button" class="btn btn-primary btn-sm" id="simpsimp">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal fade"  id="tugasedit" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"  style="max-width: 75%;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" >Edit Tugas</h3>
                    </div>
                    <form id="formy">
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group mb-3 col-sm-12 col-md-4">
                                    <label>Tugas Proses</label>
                                    <select class="form-control" name="tugas_ble" id="tugas_ble">
                                        
                                    </select>
                                    <input type="hidden" id="rumuse">
                                </div>
                                <div class="form-group mb-3 col-sm-12 col-md-4">
                                    <label>Tugas</label>
                                    <textarea class="form-control" name="tugase" id="tugase" ></textarea>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2" id="targss1e">
                                    <label>Total Target</label>
                                    <input class="form-control" type="number" min="0" name="totare" id="totare" readonly/>
                                    <input name="totar_hidee" id="totar_hidee" type="hidden"/>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2" id="targss2e">
                                    <label>Sisa Target</label>
                                    <input class="form-control" type="number" min="0" max="100"  step="1" name="sistare" id="sistare" readonly/>
                                    <input name="sistar_hidee" id="sistar_hidee" type="hidden"/>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2" id="targss11e" style="display: none">
                                    <label>Total Target</label>
                                    <div class="input-group">
                                      <input type="number" min="0" max="100" step="0.1" class="form-control" name="totar2e" id="totar2e" onchange="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" oninput="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" readonly>
                                      <span class="input-group-text">%</span>
                                    </div>
                                    <input name="totar2_hidee" id="totar2_hidee" type="hidden"/>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2" id="targss22e" style="display: none">
                                    <label>Sisa Target</label>
                                    <div class="input-group">
                                      <input type="number" min="0" max="100" step="0.1" class="form-control" name="sistar2e" id="sistar2e" onchange="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" oninput="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" readonly>
                                      <span class="input-group-text">%</span>
                                    </div>
                                    <input name="sistar2_hidee"  id="sistar2_hidee" type="hidden"/>
                                </div>
                                
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2" id="targ1e">
                                    <label>Target</label>
                                    <input class="form-control ciehe" type="number" min="0" name="targete" id="targete" autocomplete="off"/>
                                </div>
                                <input type="hidden" id="target_hide">
                                <div class="form-group mb-3 col-sm-12 col-md-2" id="targ2e" style="display: none">
                                    <label>Target</label>
                                    <div class="input-group">
                                      <input type="number" min="0" max="100" step="0.1" class="form-control ciehe" name="target2e" id="target2e" onchange="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" oninput="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" autocomplete="off">
                                      <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2">
                                    <label>Durasi</label>
                                    <select class="form-control" name="durasie" id="durasie">
                                        <option value="daily">Daily</option>
                                        <option value="range">Range</option>
                                    </select>
                                    <!--<input type="text" name="durasie" class="form-control" id="durasie">-->
                                </div>
                                
                                <!--<div class="form-group mb-3 col-sm-12 col-md-5">-->
                                <!--    <div class="input-group">-->
                                <!--        <input type="date" name="tglAwale" class="form-control" id="tglAwale" >-->
                                <!--        <span class="input-group-text" style="background:#fff; color:#777">s/d</span>-->
                                <!--        <input type="date" name="tglAkhire" class="form-control" id="tglAkhire" >-->
                                <!--    </div>-->
                                <!--</div>-->
                                
                                <div class="form-group mb-3 col-sm-12 col-md-3" id="dailyys">
                                    <label>Tanggal</label>
                                    <div class="input-group">
                                        <input type="date" name="tglAwale" class="form-control" id="tglAwale">
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-4" id="rangees">
                                    <label>Tanggal</label>
                                    <div class="input-group">
                                        <input type="date" name="tglAwale" class="form-control" id="tglAwale">
                                        <span class="input-group-text" style="background:#fff; color:#777">s/d</span>
                                        <input type="date" name="tglAkhire" class="form-control" id="tglAkhire">
                                    </div>
                                </div>
                                
                                
                                <!--<div class="form-group mb-3 col-sm-12 col-md-2">-->
                                <!--    <label>Target</label>-->
                                <!--    <input type="number" min="0.0" max="100" name="targete" class="form-control" id="targete">-->
                                <!--</div>-->
                                
                                <input type="hidden" name="id_ke" id="id_ke">
                                <input type="hidden" name="id_hidee" id="id_hidee">
                                
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" id="semesta">Kembali</button>
                             <!--data-bs-target="#rencana" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close"-->
                            <button type="button" class="btn btn-primary btn-sm" id="simpdit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal fade"  id="marketings" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Modal Rencana Marketing Harian</h3>
                        <!--<div class="pull-right">-->
                            <a class="btn btn-sm btn-primary" data-bs-target="#tambahs" data-bs-toggle="modal" data-bs-dismiss="modal"><i class="fa fa-plus"></i> tambah</a>
                            <!--<a class="btn btn-sm btn-info" data-bs-target="#edits" data-bs-toggle="modal" data-bs-dismiss="modal"><i class="fa fa-edit"></i> Edit</a>-->
                        <!--</div>-->
                        <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
                        <!--</button>-->
                    </div>
                    <!--<form id="forrrm">-->
                        <input type="hidden" id="id_karr" name="id_karr">
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped" width="100%" id="hty">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tanggal</th>
                                            <th>Kunjungan</th>
                                            <th>Transaksi</th>
                                            <th>Penawaran</th>
                                            <th>Closing</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fii">
                                        
                                    </tbody>
                                </table>
                            </div>
                            
                            <!--<span id="text" class="text-danger"></span>-->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-bs-target="#rencana" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close">Kembali</button>
                            <!--<button type="submit" class="btn btn-primary" id="simprenc">Simpan</button>-->
                        </div>
                    <!--</form>-->
                </div>
            </div>
        </div>
        
        <div class="modal fade"  id="tambahs" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Tambah Rencana Marketing Harian</h3>
                    </div>
                    <form id="forrrmm">
                        <input type="hidden" id="id_kars" name="id_kars">
                        <div class="modal-body" >
                            <div class="basic-form">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Tanggal</label>
                                        <input class="form-control" name="siper" id="siper">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label>Kunjungan </label>
                                        <input class="form-control" name="knjngn" id="knjngn">
                                    </div>
                                    
                                    <div class="col-md-2 ">
                                        <label>Transaksi </label>
                                        <input class="form-control" name="tr" id="tr">
                                    </div>
                                    
                                    <div class="col-md-2 ">
                                        <label>Penawaran </label>
                                        <input class="form-control" name="pnwrn" id="pnwrn">
                                    </div>
                                    
                                    <div class="col-md-2 ">
                                        <label>Closing </label>
                                        <input class="form-control" name="cl" id="cl">
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" id="siaaa" data-bs-target="#marketings" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close">Kembali</button>
                            <button type="button" class="btn btn-primary btn-sm" id="simps">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal fade"  id="edits" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Edit Rencana Marketing Harian</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" data-bs-target="#marketings" data-bs-toggle="modal" aria-label="Close"></button>
                    </div>
                    <form id="forrrmms">
                        <input type="hidden" id="id_karss" name="id_karss">
                        <div class="modal-body" >
                            <div class="basic-form">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Tanggal</label>
                                        <input class="form-control" name="tgz" id="tgz" type="date" readonly>
                                        <input name="tgll" id="tgll" type="hidden" readonly>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label>Kunjungan </label>
                                        <input class="form-control" name="kn" id="kn">
                                    </div>
                                    
                                    <div class="col-md-2 ">
                                        <label>Transaksi </label>
                                        <input class="form-control" name="trn" id="trn">
                                    </div>
                                    
                                    <div class="col-md-2 ">
                                        <label>Penawaran </label>
                                        <input class="form-control" name="pn" id="pn">
                                    </div>
                                    
                                    <div class="col-md-2 ">
                                        <label>Closing </label>
                                        <input class="form-control" name="clo" id="clo">
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <!--<button type="button" class="btn btn-danger" id="siaaa" data-bs-target="#marketings" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close">Kembali</button>-->
                            <button type="button" class="btn btn-primary btn-sm" id="simmm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal fade"  id="tugasTah" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"  role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Tambah Tugas Pertahun</h3>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" id="indexT"/>
                                <input type="hidden" id="id_hiddenT">
                                <input type="hidden" id="id_kantorT">
                                <div class="form-group mb-3 col-sm-12 col-md-4">
                                    <label>Rencana</label>
                                    <textarea class="form-control" name="tugasT" id="tugasT" ></textarea>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-3">
                                    <label>Target</label>
                                    <input class="form-control" type="number" min="0.0" max="100" name="targetT" id="targetT"/>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-3">
                                    <label>Tahun</label>
                                    <input class="form-control yer" name="waktuT" id="waktuT" />
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2">
                                    <button type="button" class="btn btn-sm btn-success mt-4" id="save_renTa"><i class="fa fa-plus"></i></button>
                                    <button type="button" class="btn btn-sm btn-success mt-4" id="saved_renTa" style="display: none"><i class="fa fa-arrow-down"></i></button>
                                </div>
                                
                            </div>
                            <hr class="mt-3 mb-3">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Tugas</th>
                                                <th>Target</th>
                                                <th>Tahun</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="suuu">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal" aria-label="Close">Kembali</button>
                            <button type="button" class="btn btn-primary btn-sm" id="naise">Simpan</button>
                        </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade"  id="detailnya" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" >Detail Tugas <span></span></h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-target="#rencana" data-bs-toggle="modal">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <!--<div id="sdds">-->
                                <!--</div>-->
                                 <table width="100%">
                                    <tbody id="yowaimo">
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" id="dsds">
                        
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade"  id="tugasBul" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Tambah Tugas Perbulan <span id="ccc"></span></h3>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            
                                    <input type="hidden" id="id_kankanB">
                            <div id="ppo" style="display: none">
                                <div class="row">
                                    <input type="hidden" id="id_output">
                                    <h4>Pilih Hasil Tugas</h4>
                                    <div class="form-group mb-3 col-sm-12 col-md-3">
                                        <label>Bulan</label>
                                        <input class="form-control lan" name="blno" id="blno" autocomplete="off"/>
                                    </div>
                                    
                                    <div class="form-group mb-3 col-sm-12 col-md-4">
                                        <label>Hasil</label>
                                        <select class="form-control" id="output" name="output">
                                            
                                        </select>
                                    </div>
                                    
                                    
                                    <div class="form-group mb-3 col-sm-12 col-md-3" id="bagianShow">
                                        <label>Bagian</label>
                                        <select class="form-control" id="bagian_hasil" name="bagian_hasil">
                                        </select>
                                    </div>
                                    
                                </div>
                                <hr>
                            </div>
                            
                            <span id="ll"></span>
                            
                            <input type="hidden" id="aksi">
                            
                            <input type="hidden" id="indexB"/>
                            <input type="hidden" id="id_hiddenB">
                            <!--<input type="hidden" id="id_kantorB">-->
                            <input type="hidden" id="jentagB">
                            
                            <div class="form-group mb-3 col-sm-12 col-md-5" id="bagianB">
                                <label>Bagian</label>
                                <select class="form-control" id="parentB" name="parentB">
                                    
                                </select>
                            </div>
                            
                            <input type="hidden" id="progBB" name="progBB">
                            
                            <!--<div >-->
                                <div class="form-group mb-3 col-sm-12 col-md-5" id="hide_prog" style="display: none">
                                    <label>Program</label>
                                    <select class="form-control partoff" id="progB" name="progB">
                                        <option value="">Pilih Program</option>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-12 col-md-2" id="hide_cash" style="display: none">
                                    <label>Jenis</label>
                                    <select class="form-control" id="cashB" name="cashB">
                                        <option value="">Pilih</option>
                                        <option value="cash">Cash</option>
                                        <option value="noncash">Noncash</option>
                                    </select>
                                    <small>(Optional)</small>
                                </div>
                            <!--</div>-->
                            
                            <div class="form-group mb-3 col-sm-12 col-md-7">
                                <label>Rencana</label>
                                <textarea class="form-control" name="tugasB" id="tugasB" ></textarea>
                            </div>
                            
                            
                            <div class="form-group mb-3 col-sm-12 col-md-4">
                                <label>Satuan</label>
                                <select class="form-control" id="satuanB" name="satuanB">
                                    <option value="">Pilih Satuan</option>
                                </select>
                            </div>
                                
                            <input type="hidden" id="cariSatuan">
                            
                            <div class="form-group mb-3 col-sm-12 col-md-4">
                                    <label>Metode</label>
                                    <select class="form-control " id="rumusB" name="rumusB">
                                        <option value="" selected disabled>Pilih Metode</option>
                                        <option value="kuantitatif">Kuantitatif</option>
                                        <option value="kualitatif">Kualitatif</option>
                                    </select>
                                </div>
                                
                            <div class="form-group mb-3 col-sm-12 col-md-4" id="trgt1">
                                <label>Target</label>
                                <!--<input class="form-control" type="number" min="0.0" max="100" name="targetB1" id="targetB1" onchange="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" oninput="this.value = (parseInt(this.value) > 100) ? 100 : this.value;"/>-->
                                <input class="form-control" type="number" min="0" name="targetB1" id="targetB1"/>
                            </div>
                            
                            <div class="form-group mb-3 col-sm-12 col-md-4" id="trgt2" style="display: none">
                                <label>Target</label>
                                <div class="input-group">
                                  <input type="number" min="0.0" max="100" step="0.1" class="form-control" name="targetB2" id="targetB2" onchange="this.value = (parseInt(this.value) > 100) ? 100 : this.value;" oninput="this.value = (parseInt(this.value) > 100) ? 100 : this.value;">
                                  <span class="input-group-text">%</span>
                                </div>
                            </div>
                            
                            <style>
                                .ui-datepicker-calendar {
                                    display: none;
                                }
                            </style>
                            
                                
                            <div class="form-group mb-3 col-sm-12 col-md-4">
                                <label>Dari Bulan</label>
                                <input class="form-control lan" name="waktuB" id="waktuB" autocomplete="off"/>
                            </div>
                            
                            <div class="form-group mb-3 col-sm-12 col-md-4">
                                <label>Sampai Bulan</label>
                                <input class="form-control lan" name="waktu_B" id="waktu_B" autocomplete="off"/>
                            </div>
                                
                            <div class="form-group mb-3 col-sm-12 col-md-2">
                                <button type="button" class="btn btn-sm btn-success mt-4" id="save_renBu"><i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-sm btn-success mt-4" id="saved_renBu" style="display: none"><i class="fa fa-arrow-down"></i></button>
                            </div>
                                
                        </div>
                        <hr class="mt-3 mb-3">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tugas</th>
                                            <th>Bagian</th>
                                            <th>Satuan</th>
                                            <th>Metode</th>
                                            <th>Target</th>
                                            <th>Dari Bulan</th>
                                            <th>Sampai Bulan</th>
                                            <th style="display:none" id="asc">Hasil</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="yuuu">
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                        
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal" aria-label="Close">Kembali</button>
                        <button type="button" class="btn btn-primary btn-sm" id="naisu">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade"  id="tambahParent" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"  role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Tambah Bagian</h3>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" id="indexTP"/>
                                <input type="hidden" id="id_hiddenTP">
                                <input type="hidden" id="id_kantorTP">
                                <input type="hidden" id="aksiTP">
                                <div class="form-group mb-3 col-sm-5 col-md-5">
                                    <label>Unit</label>
                                    <select class="form-control " id="unitTP" name="unitTP">
                                        <option value="">Pilih Unit</option>
                                        <!--<option value="0">Umum</option>-->
                                        @if(Auth::user()->level == 'admin' || Auth::user()->level == 'keuangan pusat')
                                        <option value="0">Umum</option>
                                        @endif
                                        @foreach($kota as $kk)
                                        <option value="{{$kk->id}}">{{ $kk->unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-5 col-md-5">
                                    <label>Bagian</label>
                                    <textarea class="form-control" name="parentTP" id="parentTP" ></textarea>
                                </div>
                                
                                
                                <div class="form-group mb-3 col-sm-2 col-md-2">
                                    <button type="button" class="btn btn-sm btn-success mt-4" id="save_renTP"><i class="fa fa-plus"></i></button>
                                    <button type="button" class="btn btn-sm btn-success mt-4" id="saved_renTP" style="display: none"><i class="fa fa-arrow-down"></i></button>
                                </div>
                                
                            </div>
                            <hr class="mt-3 mb-3">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Bagian</th>
                                                <th>Unit</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sutu">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal" aria-label="Close">Kembali</button>
                            <button type="button" class="btn btn-primary btn-sm" id="tptp">Simpan</button>
                        </div>
                </div>
            </div>
        </div>
        
        
        <div class="modal fade"  id="tambahS" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"  role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Tambah Satuan</h3>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" id="indexS"/>
                                <input type="hidden" id="id_hiddenS">
                                <input type="hidden" id="id_kantorS">
                                <input type="hidden" id="unit_text">
                                
                                <input type="hidden" id="aksiS">
                                
                                <div class="form-group mb-3 col-sm-4 col-md-4">
                                    <label>Unit</label>
                                    <select class="form-control " id="unitS" name="unitS">
                                        <option value="">Pilih Unit</option>
                                        @if(Auth::user()->level == 'admin' || Auth::user()->level == 'keuangan pusat')
                                        <option value="0">Umum</option>
                                        @endif
                                        
                                        @foreach($kota as $kk)
                                        <option value="{{$kk->id}}">{{ $kk->unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-3 col-md-4">
                                    <label>Jenis Target</label>
                                    <select class="form-control " id="jenisTargetS" name="jenisTargetS">
                                        <option value="all">Pilih Jenis</option>
                                        <option value="hasil">Hasil</option>
                                        <option value="proses">Proses</option>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3 col-sm-3 col-md-4">
                                    <label>Bagian</label>
                                    <select class="form-control " id="bagianS" name="bagianS">
                                        <option value="all">Pilih Bagian</option>
                                        <!--<option value="hasil">Hasil</option>-->
                                        <!--<option value="proses">Proses</option>-->
                                    </select>
                                </div>
                                
                                
                                <!--<div class="form-group mb-3 col-sm-3 col-md-4">-->
                                <!--    <label>Rumus</label>-->
                                <!--    <select class="form-control " id="rumusS" name="rumusS">-->
                                <!--        <option value="">Pilih Jenis</option>-->
                                <!--        <option value="kuantitatif">Kuantitatif</option>-->
                                <!--        <option value="kualitatif">Kualitatif</option>-->
                                <!--    </select>-->
                                <!--</div>-->
                                
                                <div class="form-group mb-3 col-sm-3 col-md-4">
                                    <label>Satuan</label>
                                    <input class="form-control" name="parentS" id="parentS" />
                                </div>
                                
                                
                                <div class="form-group mb-3 col-sm-2 col-md-2">
                                    <button type="button" class="btn btn-sm btn-success mt-4" id="save_renS"><i class="fa fa-plus"></i> </button>
                                    <button type="button" class="btn btn-sm btn-success mt-4" id="saved_renS" style="display: none"><i class="fa fa-arrow-down"></i> </button>
                                </div>
                                
                            </div>
                            <hr class="mt-3 mb-3">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Satuan</th>
                                                <th>Jenis Target</th>
                                                <th>Bagian</th>
                                                <th>Unit</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="satt">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal" aria-label="Close">Kembali</button>
                            <button type="button" class="btn btn-primary btn-sm" id="stst">Simpan</button>
                        </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->
    
    
    
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                        <div class="pull-right">
                            <div class="btn-group">
                                @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                    <button type="button" class="btn btn-primary btn-sm btn-rounded" id="button-perusahaan" style="margin-right: 10px" data-bs-toggle="modal" data-bs-target="#modalPerusahaan" style="float: left">Pilih Perusahaan</button>
                                @endif
                                <!--<button class="btn btn-sm btn-success btn-rounded" style="margin-right: 10px" id="imp" type="button">Import</button>-->
                                <button class="btn btn-sm btn-primary btn-rounded" style="margin-right: 10px" id="prnt" type="button">Set Bagian</button>
                                <button class="btn btn-sm btn-primary btn-rounded" style="margin-right: 10px" id="satuan" type="button">Set Satuan</button>
                               
                                <div class="btn-group me-2">
                                    <button type="button" class="btn btn-rounded btn-success btn-xxs dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Ekspor Data" style="width:100%;">
                                        <i class="fa fa-download"></i> Export
                                    </button>
                                    <ul class="dropdown-menu" style="margin: 0px;">
                                        <li><button class="dropdown-item expor" type="submit" id="xls" value="xls" name="tombol" >.XLS</button></li>
                                        <!--<li><button class="dropdown-item expor" type="submit" id="pdf" value="pdf" name="tombol" >.PDF</button></li>-->
                                        <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                    </ul>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row d-flex justify-content-center mb-3">
                            <div class="row">
                                
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Tugas Perbulan</label>
                                    <select class="form-control cerr" id="periode" name="periode">
                                        <option value="" disabled>Pilih</option>
                                        <!--<option value="tahun" selected>Tahun</option>-->
                                        <option value="bulan" selected>Kantor</option>
                                        <option value="tanggal">Karyawan</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3 col-md-3" id="bln">
                                    <label class="form-label">Bulan</label>
                                    <input class="form-control bul" name="bulan" id="bulan" autocomplete="off" placeholder="YYYY-MM">
                                </div>
                                
                                <div class="mb-3 col-md-3" id="thn">
                                    <label class="form-label">Tahun</label>
                                    <input class="form-control yer" name="tahun" id="tahun" autocomplete="off" placeholder="YYYY">
                                </div>
                                
                                <div class="mb-3 col-md-3" id="tgl">
                                    <!--<label class="form-label">Tanggal</label>-->
                                    <!--<input class="form-control " name="tanggal" id="tanggal" autocomplete="off">-->
                                    <label class="form-label">Bulan .</label>
                                    <input class="form-control bul" name="tanggal" id="tanggal" autocomplete="off" placeholder="YYYY-MM">
                                </div>
                                
                                <div class="mb-3 col-md-3" id="unite">
                                    <label class="form-label">Unit</label>
                                    <select class="form-control units" name="unit" id="unit">
                                        <option value="">pilih unit</option>
                                        @foreach($kota as $k)
                                        <option value="{{ $k->id }}" {{ $k->id == Auth::user()->id_kantor ? 'selected' : '' }}>{{ $k->unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                            <table id="user_table" class="table table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="cari" id="idw" width="5%"></th>
                                        <th class="cari" id="namaw"></th>
                                        <th class="cari" id="awww"></th>
                                        <th class="cari" id="tgs1">Tugas Hasil</th>
                                        <th class="cari" id="tgs2">Tugas Proses</th>
                                        <th class="cari" id="tgs">Jumlah Tugas</th>
                                        <th id="aksi">Target Program</th>
                                        <th id="aksi">Detail</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection