@extends('template')
@section('konten')
<div class="content-body">
    
    <!-- Modal -->
    <div class="modal fade"  id="rencana">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Update Jam Kerja</h3>
                    <!--<button type="button" class="btn btn-sm btn-primary" id="tambahTugas"><i class="fa fa-plus"></i> Tambah</button>-->
                    <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
                    <!--</button>-->
                </div>
                <form id="myForm">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <input type="hidden" class="form-control" name="id" id="idJamker"/>
                            <div class="col-sm-12 col-md-6" id="">
                                <label for="" class="col-form-label">Nama Hari</label>
                                <select class="form-control" disabled name="hari" id="hari">
                                    @foreach($val_hari as $val)
                                    <option value="{{$val->hari}}">{{$val->hari}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-sm-12 col-md-6">
                                <label for="" class="col-form-label">Cek In</label>
                                <input type="time" class="form-control" name="cek_in" id="cekin"/>
                            </div>
                            
                            <div class="col-sm-12 col-md-6">
                                <label for="" class="col-form-label">Shift</label>
                                <input  class="form-control" disabled name="shift" id="shift"/>
                            </div>
                            
                            <!--<div class="col-sm-12 col-md-6"-->
                            <!--    <label for="" class="col-form-label">Shift</label>-->
                            <!--    <input type="text" class="form-control" name="shift" id="shift">-->
                            <!--</div>-->
                            
                            <div class="col-sm-12 col-md-6">
                                <label for="" class="col-form-label">Cek Out</label>
                                <input type="time" class="form-control" name="cek_out" id="cekout"/>
                            </div>
                            
                            <div class="col-sm-12 col-md-6" >
                                <label for="" class="col-form-label">Terlambat</label>
                                <input type="time" class="form-control" name="terlambat" id="terlambat"/>
                            </div>
                            
                            <div class="col-sm-12 col-md-6">
                                <label for="" class="col-form-label">Break In</label>
                                <input type="time" class="form-control" name="break_in" id="breakin"/>
                            </div>
                            
                            <div class="col-sm-12 col-md-6">
                                <label for="" class="col-form-label">Status</label>
                                <select class="form-control status" name="status" id="statusForm">
                                    <option value="">Pilih</option>
                                    @foreach($val_status as $val)
                                    <option value="{{$val->status}}">{{ucfirst($val->status)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-sm-12 col-md-6" >
                                <label for="" class="col-form-label">Break Out</label>
                                <input type="time" class="form-control" name="break_out" id="breakout"/>
                            </div>
                            
                        </div>
                        
                        <!--<span id="text" class="text-danger"></span>-->
                    </div>
                    <div class="modal-footer mt-5">
                        <button type="button" class="btn btn-danger " data-bs-dismiss="modal" aria-label="Close">Batal</button>
                        <button type="button" class="btn btn-primary" id="editJamKerja">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal -->
    
    <!-- Modal -->
    <div class="modal fade"  id="entryShift">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 90%;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Entry Jam Kerja</h3>
                    <!--<button type="button" class="btn btn-sm btn-primary" id="tambahTugas"><i class="fa fa-plus"></i> Tambah</button>-->
                    <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
                    <!--</button>-->
                </div>
                <form id="formEntryShift">
                    <div class="modal-body">
                        @php 
                            $days = array(
                                'Monday' => 'Senin',
                                'Tuesday' => 'Selasa',
                                'Wednesday' => 'Rabu',
                                'Thursday' => 'Kamis',
                                'Friday' => 'Jumat',
                                'Saturday' => 'Sabtu',
                                'Sunday' => 'Minggu',
                            );

                        @endphp
                        
                        <input type="hidden" class="form-control" name="id_coms" id="id_coms"/>
                        
                        @foreach($days as $ENG => $IND)
                        <div class="row mb-3">
                            <div class="col-sm-12 col-md-2 mb-2" id="" >
                                <label for="" class="col-form-label">Nama Hari</label>
                                <input  class="form-control" type="hidden" value="{{ $ENG }}" required name="addhari[]" id="addhari"/>
                                <input  class="form-control" value="{{ $IND }}" disabled name="dumpHari[]" id="dumpHari" required/>
                            </div>
                            
                            <div class="col-sm-12 col-md-2 mb-2" >
                                <label for="" class="col-form-label">Shift</label>
                                <input  class="form-control addshift" type="hidden" name="addshift[]" id="addshift" required/>
                                <input  class="form-control addshift" disabled name="dumpShift[]" id="dumpShift" required/>
                            </div>

                            <div class="col-sm-12 col-md-2 mb-2"  >
                                <label for="" class="col-form-label">Terlambat</label>
                                <input type="time" class="form-control" name="addterlambat[]" id="addterlambat" required/>
                            </div>

                            <div class="col-sm-12 col-md-2 mb-2" >
                                <label for="" class="col-form-label">Cek In</label>
                                <input type="time" class="form-control" name="addcek_in[]" placeholder="Cek In" id="addcekin"/>
                            </div>
                            
                            <div class="col-sm-12 col-md-2 mb-2" >
                                <label for="" class="col-form-label">Cek Out</label>
                                <input type="time" class="form-control" name="addcek_out[]" id="addcekout" required/>
                            </div>
                            
                            
                            <div class="col-sm-12 col-md-2 mb-2" >
                                <label for="" class="col-form-label">Break In</label>
                                <input type="time" class="form-control" name="addbreak_in[]" id="addbreakin" required/>
                            </div>
                            
                            <div class="col-sm-12 col-md-2 mb-2"  >
                                <label for="" class="col-form-label">Break Out</label>
                                <input type="time" class="form-control" name="addbreak_out[]" id="addbreakout" required/>
                            </div>
                            
                            <div class="col-sm-12 col-md-2 mb-2" >
                                <label for="" class="col-form-label">Status</label>
                                <select class="form-control status" name="addstatus[]" id="addstatusForm" required>
                                    <option value="">Pilih</option>
                                    @foreach($val_status as $val)
                                    <option value="{{$val->status}}" required>{{ucfirst($val->status)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            
                        </div>
                        <hr/>
                        @endforeach
                        <!--<span id="text" class="text-danger"></span>-->
                    </div>
                    <div class="modal-footer mt-5">
                        <button type="button" class="btn btn-danger " data-bs-dismiss="modal" aria-label="Close">Batal</button>
                        <button type="submit" class="btn btn-primary" id="simpanJamKerja">Simpan</button>
                    </div>
                </form>
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
                            <!--<div class="btn-group">-->
                            <!--     <button type="button" class="btn btn-info btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">-->
                            <!--        Ekspor-->
                            <!--    </button>-->
                            <!--    <ul class="dropdown-menu">-->
                            <!--        <li><button class="dropdown-item" type="submit" id="xls" value="xls" name="tombol">.XLS</button></li>-->
                            <!--        <li><button class="dropdown-item" type="submit" id="csv" value="csv" name="tombol">.CSV</button></li>-->
                                    <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                            <!--    </ul>-->
                            <!--</div>-->
                            @if(Auth::user()->level_hc == '1')
                            <button type="button" class="btn btn-primary btn-xxs " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                            @endif
                             <button type="button" class="btn btn-success btn-xxs" data-bs-toggle="modal" data-bs-target="#entryShift"><i class="fa fa-plus"></i> Tambah</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row d-flex justify-content-center ">
                            <div class="row">
                                <div class="mb-3 col-md-3">
                                    <label class="form-label mt-3">Status</label>
                                    <select class="form-control refresh" id="status">
                                        <option value="">Pilih</option>
                                        @foreach($val_status as $val)
                                        <option value="{{$val->status}}">{{ucfirst($val->status)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label mt-3">Shift</label>
                                    <select class="form-control refresh" id="filter_shift">
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                            <table id="user_table" class="table table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="cari">Nama Hari</th>
                                        <th class="cari">Cek In</th>
                                        <th class="cari">Terlambat</th>
                                        <th class="cari">Break Out</th>
                                        <th class="cari">Break In</th>
                                        <th class="cari">Cek Out</th>
                                        <th class="cari">Status</th>
                                        <th class="cari">Shift</th>
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