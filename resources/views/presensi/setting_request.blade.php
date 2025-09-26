@extends('template')

@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!-- Modal -->
        <div class="modal fade" id="edit">
            <div id="modal-ukuran" class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Setting Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form method="post" id="sett_req" enctype="multipart/form-data">
                        <div class="modal-body gap-4">
                            <div class="row">
                                <div class="col">
                                    
                                    <div class="form-group" id="addJenisLayout" >
                                        <label for="" class="">Nama</label>
                                        <input type="text" class="form-control" name="jenis" id="jenis">
                                    </div>
                                 
                                    <div class="form-group">
                                        <label for="" class=" col-form-label">Kategory</label>
                                        <select class="form-control" name="kategori"  id="kategory">
                                            <option value="presensi">Presensi</option>
                                            <option value="dana">Dana</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="statpresLayout">
                                        <label for="" class=" col-form-label">Status Presensi</label>
                                        <select class="form-control" name="statpres"  id="statpres">
                                            <option value="tanpa">Tanpa Presensi</option>
                                            <option value="dengan">Dengan Presensi</option>
                                            <option value="opsional">Opsional</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="wajibLaporan">
                                        <label for="" class="col-form-label">Wajib Laporan</label>
                                        <select class="form-control" id="laporan" name="walap">
                                            <option value="1">Ya</option>
                                            <option value="0">Tidak</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="" class=" col-form-label">Sub Request</label>
                                        <select class="form-control" id="subRequest" name="statsub">
                                            <option value="tanpa">Tanpa Sub Request</option>
                                            <option value="dengan">Dengan Sub Request</option>
                                            <option value="sub">Sub Request</option>
                                        </select>
                                    </div>
                                    
                                    <div hidden id="layoutParRequest" class="form-group">
                                        <label for="" class=" col-form-label">Parent Request</label>
                                        <select class="form-control" id="pRequest" name="id_parent">
                                        </select>
                                    </div>
                                    
                                </div>
                                <div class="col" id="col-sec">
                                    <div id="layoutBatasan" class="form-group">
                                        <label for="" class=" col-form-label">Batasan</label>
                                        <select class="form-control" id="batasan" name="per_limit">
                                            <option value="tanpa">Tanpa Batasan</option>
                                            <option value="bulan">Per Bulan</option>
                                            <option value="tahun">Per Tahun</option>
                                        </select>
                                    </div>
                                    <div hidden id="layoutJumlah" class="form-group">
                                        <label for="" class=" col-form-label">Jumlah <span id="rp"></span></label>
                                        <input type="text" autocomplete="off" min="0.0" class="form-control" id="jumlah" name="jum_limit">
                                    </div>
                                    
                                    <div id="layoutBatasHari" class="form-group">
                                        <label for="" class=" col-form-label">Batas Hari</label>
                                        <input type="number" class="form-control" id="batasHari" name="req_limit">
                                    </div>
                                      
                                    <div class="form-group">
                                        <label for="" class=" col-form-label">Lampiran</label>
                                        <select class="form-control" id="lam" name="lam">
                                            <option value="">-- PILIH --</option>
                                            <option value="1">Ya</option>
                                            <option value="0">Tidak</option>
                                            <option value="2">Opsional</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="" class=" col-form-label">Foto</label>
                                        <select class="form-control" id="foto" name="foto">
                                            <option value="">-- PILIH --</option>
                                            <option value="1">Ya</option>
                                            <option value="0">Tidak</option>
                                            <option value="2">Opsional</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="" class=" col-form-label">Lokasi</label>
                                        <select class="form-control" id="lok" name="lok">
                                            <option value="">-- PILIH --</option>
                                            <option value="1">Ya</option>
                                            <option value="0">Tidak</option>
                                            <option value="2">Opsional</option>
                                        </select>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Batal</button>
                            <button type="button" class="btn btn-primary save" >Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal -->
        
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-success" id="entryRequest">Entry</button>
                    @if(Auth::user()->level_hc == '1')
                    <button type="button" class="btn btn-primary btn-sm " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                    @endif
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="idCom">
                        <div class="table-responsive">
                            <table id="user_table" class="table  table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis</th>
                                        <th>Kategory</th>
                                        <th>Status Sub</th>
                                        <th>Jumlah Limit</th>
                                        <th>Lampiran</th>
                                        <th>Foto</th>
                                        <th>Lokasi</th>
                                        <th>Batas Pembuatan</th>
                                        <th></th>
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