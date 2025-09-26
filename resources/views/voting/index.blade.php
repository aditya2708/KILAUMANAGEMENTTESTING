@extends('template')

@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
        <!-- Modal -->
        <div class="modal fade pengumuman"  id="modalPerusahaan">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">TAMBAH VOTING</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form method="post" id="entri-pengumuman" enctype="multipart/form-data">
                        <div class="modal-body gap-4">
                            
                            <div class="form-group mb-3">
                                <label for="inputPassword" class="">Keterangan</label>
                                <textarea class="form-control isi" name="ket" id="ketVot" style="height:100px;"></textarea>
                            </div>
                            
                            <hr>
                            <div class="d-flex">
                                <div class="p-2 flex-grow-1 bd-highlight">
                                    <h3>Voting</h3>
                                </div>
                                <div class="p-2 bd-highlight">
                                    <button type="button" class="btn btn-sm btn-success" id="addrule2">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div id="newrule2">
                                
                            </div>
                            
                            <hr>
                            
                            <div class="form-group mb-3 select_kantor">
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
                            
                            <div class="form-group mb-3 select_kantor">
                                <label for="" class=" col-form-label">Jabatan</label>
                                <select class="form-control konkon" id="select_jabatan"  name="jabatan[]" multiple="multiple">
                                    @foreach ($jabatan as $j)
                                    <option value="{{ $j->id }}">{{ $j->jabatan }}</option>
                                    @endforeach
                                </select>
                                <div class="form-check mt-3">
                                  <input class="form-check-input check2" type="checkbox" value="" id="defaultCheck2">
                                  <label class="form-check-label" for="defaultCheck2">
                                    Pilih semua Jabatan
                                  </label>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3 " >
                                <label for="" class=" col-form-label">Tanggal</label>
                                <div class="form-group d-flex align-items-center mb-3">
                                    <div class="col-5 s">
                                      <input type="date" id="date1" class="form-control" name="date1" placeholder="Awal">
                                      <span style="font-size: 11px; margn-left: 10px" class="text-danger">*tanggal awal</span>
                                    </div>
                                    <div class="col-2 sd ">
                                      <div class="text-center">s/d</div>
                                    </div>
                                    <div class="col-5 sd ">
                                      <input type="date" id="date2" class="form-control" name="date2" placeholder="Akhir">
                                      <span style="font-size: 11px; margn-left: 10px" class="text-danger">*tanggal akhir</span>
                                    </div>
                                </div>
                            </div>
                            <span id="text" class="text-danger"></span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger " data-bs-dismiss="modal" aria-label="Close">Batal</button>
                            <button type="submit" class="btn btn-primary" id="simpanKet">Simpan</button>
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
                            <button class="btn btn-primary entry-pen  btn-xxs" id=""  data-bs-toggle="modal" data-bs-target=".pengumuman">Buat Voting</button>
                            @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                <button type="button" class="btn btn-primary btn-xxs " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                            @endif
                    </div>
                    <div class="card-body">
                        
                        <div class="table-responsive">
                            <table id="tablesih" class="table  table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul</th>
                                        <th>Voting</th>
                                        <th>Ditunjukan</th>
                                        <th>Kantor</th>
                                        <th>Tanggal awal</th>
                                        <th>Tanggal akhir</th>
                                        <th>Tayang</th>
                                        <th>Aksi</th>
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