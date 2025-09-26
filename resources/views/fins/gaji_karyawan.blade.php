@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">FINS</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Gaji Karyawan</a></li>-->
        <!--    </ol>-->
        <!--</div>-->
        
        <!--Modal-->
        <div class="modal fade" id="eksdata" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form method="get" action="{{url('eksportdata')}}">
                    <input type="hidden" name="com" id="idCom"/>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Eksport Data</h5>
                        </div>
                        <div class="modal-body">

                            @csrf
                            <div class="row">
                                <div class="col-lg-4">
                                    <label>Pilih Bulan</label>
                                    <div class="form-group">
                                        <input type="text" name="tgl" id="tgl" class="form-control cobain" placeholder="diisi ya.." autocomplete="off" required>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <label>Pilih Unit</label>
                                    <div class="form-group">
                                        <select name="unit" id="unit" class="form-control unit">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label>Pilih Status Kerja</label>
                                    <div class="form-group">
                                        <select class="form-control" name="status" id="status">
                                            <option value="">Semua Status Kerja</option>
                                            <option value="Contract">Contract</option>
                                            <option value="Training">Training</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Eksport</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="ekspay" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form method="get" action="{{url('eksportpay')}}">
                    <input type="hidden" name="com" id="idCom"/>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Eksport Payroll</h5>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label>Pilih Tanggal</label>
                                    <div class="form-group">
                                        <input type="text" name="tgl" id="tgl" class="form-control daterange" placeholder="{{ date('m-Y') }}" autocomplete="off" required>
                                    </div>
                                </div>

                                <div class="col-lg-4 mb-3">
                                    <label>Pilih Unit</label>
                                    <div class="form-group">
                                        <select name="unit" class="form-control unit" id="unit">
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 mb-3">
                                    <label>Pilih Status Kerja</label>
                                    <div class="form-group">
                                        <select class="form-control" name="status" id="status">
                                            <!--<option value="">-- Pilih Status Kerja</option>-->
                                            <option value="">Semua Status</option>
                                            <option value="Contract">Contract</option>
                                            <option value="Training">Training</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="ezz">Eksport</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="eksbpjs" >
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <form method="get" action="{{url('eksportbpjs')}}">
                    <input type="hidden" name="com" id="idCom"/>
                    
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Eksport BPJS</h5>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Pilih Tanggal</label>
                                    <div class="form-group">
                                        <input type="text" name="tgl" id="tgl" class="form-control daterange" required="required" placeholder="{{ date('m-Y') }}" autocomplete="off" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label>Pilih BPJS</label>
                                    <div class="form-group">
                                        <select required="required" name="bpjs" id="bpjs" class="form-control">
                                            <!--<option selected disabled>-- Pilih BPJS --</option>-->
                                            <option value="kesehatan">Kesehatan</option>
                                            <option value="ketenagakerjaan">Ketenagakerjaan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label>Pilih Unit</label>
                                    <div class="form-group">
                                        <select name="unit" id="unit" class="form-control unit">
                                        </select>
                                    </div>
                                </div>



                                <div class="col-md-3">
                                    <label>Pilih Status Kerja</label>
                                    <div class="form-group">
                                        <select class="form-control" name="status" id="status">
                                            <!--<option>-- Pilih Status Kerja</option>-->
                                            <option value="">Semua Status Kerja</option>
                                            <option value="Contract">Contract</option>
                                            <option value="Training">Training</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Eksport</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Modal -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <!--Gaji Karyawan-->
                        <h4 class="title-header"></h4>
                        <div class="d-flex justify-content-end gap-2">
                         @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                        <button type="button" class="btn btn-xxs btn-primary" id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                        @endif
                            <button type="button" class="btn btn-xxs btn-rounded btn-success" id="eksd" data-bs-toggle="modal" data-bs-target="#eksdata" ><i class="fa fa-download  color-success"></i> Ekspor Data(Gaji)</button>
                            @if(Auth::user()->level == 'admin' || Auth::user()->keuangan == 'keuangan pusat')
                            <button type="button" class="btn btn-xxs btn-rounded btn-info" id="eksp" data-bs-toggle="modal" data-bs-target="#ekspay"><i class="fa fa-download  color-info"></i> Ekspor Payroll</button>
                            <button type="button" class="btn btn-xxs btn-rounded btn-warning" id="eksb" data-bs-toggle="modal" data-bs-target="#eksbpjs"><i class="fa fa-download  color-warning"></i> Ekspor BPJS</button>
                            @endif
                            <!--<a href="#" id="eksd" data-bs-toggle="modal" data-bs-target="#eksdata" class="btn btn-success btn-xs">Eksport Data</a>-->
                            <!--<a href="#" id="eksp" data-bs-toggle="modal" data-bs-target="#ekspay" class="btn btn-info btn-xs">Eksport Payroll</a>-->
                            <!--<a href="#" id="eksb" data-bs-toggle="modal" data-bs-target="#eksbpjs" class="btn btn-warning btn-xs">Eksport BPJS</a>-->
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="basic-form">
                                    <div class="row">
                                        <form method="GET">
                                            <input type="hidden" id="#idCom"/>
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-lg-3 mb-3">
                                                        <label>Bulan dan Tahun</label>
                                                        <input type="text" class="form-control cek" name="bln" id="bln" placeholder="contoh {{date('m-Y') }}">
                                                    </div>
                                                    <div class="col-lg-2 mb-3">
                                                        <label>Jabatan</label>
                                                        <select id="jab" class="form-control cek1" name="jab">
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 mb-3">
                                                        <label>Kantor</label>
                                                        <select id="kan" class="form-control cek2" name="kan">
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 mb-2">
                                                        <a href="javascript:void(0)" class="btn btn-danger btn-sm reset" style="margin-top:25px">Reset Filter</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <br>
                                <br>
                                <div class="table-responsive">
                                    <table id="user_table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Nama</th>
                                                <th>Jabatan</th>
                                                <th>Kantor</th>
                                                <th>Gaji Pokok</th>
                                                <th>Tunjangan Jabatan</th>
                                                <th>Tunjangan Daerah</th>
                                                <th>Tunjangan Anak</th>
                                                <th>Tunjangan Pasangan</th>
                                                <th>Tunjangan Beras</th>
                                                <th>Transport</th>
                                                <th>Jumlah Hari</th>
                                                <th>Total</th>
                                                <!--<th>Aksi</th>-->
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection