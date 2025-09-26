@extends('template')
@section('konten')


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Petunjuk variabel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Variable untuk Nama Direktur gunakan : <mark>{NAMA_DIREKTUR}</mark></p>
        <p>Variable untuk Nama Karyawan gunakan :<mark>{NAMA_KARYAWAN}</mark></p>
        <p>Variable untuk Alamat Karyawan gunakan :<mark>{ALAMAT_KARYAWAN}</mark></p>
        <p>Variable untuk NIK Karyawan gunakan : <mark>{NIK_KARYAWAN}</mark></p>
        <p>Variable untuk Alamat Perusahaan gunakan : <mark>{ALAMAT_PERUSAHAAN}</mark></p>
        <p>Variable untuk Bulan Romawi gunakan : <mark>{BULAN_ROMAWI}</mark></p>
        <p>Variable untuk Bulan gunakan : <mark>{BULAN}</mark></p>
        <p>Variable untuk Tahun gunakan : <mark>{TAHUN}</mark></p>
        <p>Variable untuk Nomor urut gunakan : <mark>{NOMOR_URUT}</mark></p>
        <p class="ms-3" style="color:#cacfcc;">Example : Halo nama saya {NAMA_KARYAWAN}</p>
      </div>
    </div>
  </div>
</div>


<div class="content-body">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <a href="javascript:void()"  data-bs-toggle="modal" data-bs-target="#exampleModal" class="text-secondary" id="petunjut">Petunjuk variable <i class="fa fa-question-circle" aria-hidden="true"></i></a>
                @if(Auth::user()->level_hc == '1')
                <button type="button" class="btn btn-primary btn-sm " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                @endif
            </div>
            <div class="card-body">
                <input type="hidden" id="idCom">
            <form id="summernote-form">
                <div id="summernote" name="summernote"></div>
            </form>
                <div class="row d-flex justify-content-between">
                    <div class="col-6 d-flex gap-2">
                        <div class="mt-3 col-md-4" id="col-tipesurat">
                            <select class="form-control" id="tipe_surat">
                            </select>
                        </div>
                        <div class="mt-3 col-md-4" id="susss" hidden>
                            <input type="text" class="form-control" name="addTipeSurat" id="addTipeSurat" placeholder="Tambah Surat"/>
                        </div>
                        <div class="col-md-2 d-flex gap-2 mt-3">
                            <div id="hapusJenisSurat">
                                <button type="button" class="btn btn-sm btn-danger center-block" title="Hapus surat"><i class="fa fa-trash"></i></button>
                            </div>
                            <div id="tambahJenisSurat">
                                <button type="button" class="btn btn-sm btn-success center-block" title="Tambah surat"><i class="fa fa-plus"></i></button>
                            </div>
                            <div id="batalJenisSurat" hidden>
                                <button type="button" class="btn btn-sm btn-warning center-block" title="Batal"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3 d-flex gap-2 justify-content-end">
                        <div class="col-lg-1">
                            <label class="btn btn-info btn-sm center-block btn-file" title="Upload Text PDF">
                              <i class="fa fa-upload" aria-hidden="true"></i>
                              <input type="file"  id="pdfFile" accept=".pdf"  style="display: none;">
                            </label>
                        </div>
                        <div class="col-lg-1">
                            <button type="button" class="btn center-block btn-success" id="simpan"  title="Simpan"><i class="fa fa-check"></i></button>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection