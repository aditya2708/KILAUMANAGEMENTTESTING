@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">HCM</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Data Karyawan</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- modal -->
        <!-- row -->
        
      


        
        
        <div class="modal row fade" id="modals" >
            
            <div class="modal-dialog modal-lg col-md-6" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Karyawan Saat Ini</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form1" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form" id="bodi2">
                            </div>
                            <div id="tab_pasangan_dulu" style="display: none">
                                <div class="col-md-12 mb-3">
                                    <table class="table table-bordered ">
                                        <thead>
                                            <tr>
                                            <th width="40%">Nama Suami / Istri</th>
                                            <th width="25%">Tanggal Lahir</th>
                                            <th width="25%">Tanggal Nikah</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table_dulu">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div id="tab_anak_dulu" style="display: none">
                                    <div class="col-md-12">
                                        <table class="table table-bordered ">
                                            <thead>
                                                <tr>
                                                <th width="40%">Nama Anak</th>
                                                <th width="25%">Tanggal Lahir</th>
                                                <th width="25%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_anak_dulu">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
            <div class="modal-dialog modal-lg col-md-6 " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="judul"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="sample_form1" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form" id="bodai">
                            </div>
                            <div id="tab_pasangan" style="display: none">
                                <div class="col-md-12 mb-3">
                                    <table class="table table-bordered ">
                                        <thead>
                                            <tr>
                                            <th width="40%">Nama Suami / Istri</th>
                                            <th width="25%">Tanggal Lahir</th>
                                            <th width="25%">Tanggal Nikah</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div id="tab_anak" style="display: none">
                                    <div class="col-md-12">
                                        <table class="table table-bordered ">
                                            <thead>
                                                <tr>
                                                    <th width="40%">Nama Anak</th>
                                                    <th width="25%">Tanggal Lahir</th>
                                                    <th width="25%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_anak">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center ">
                                <div class="bg-collaps rounded mt-4" style="width: 97%;">
                                    <div class="collapse multi-collapse " id="multiCollapseExample2" >
                                       <div class="row">
                                           <div  id="proses" hidden>
                                               <div  class="d-flex justify-content-center align-items-center m-5">
                                                   <div class="spinner-border" style="width: 5rem; height: 5rem;" role="status">
                                                      <span class="visually-hidden">Loading...</span>
                                                    </div>
                                               </div>
                                           </div>
                                           <div  id="berhasil" hidden class="">
                                                <div class="d-flex justify-content-center align-items-center m-5 flex-column"> <!-- Tambahkan flex-column di sini -->
                                                    <div class="bg-success d-flex justify-content-center align-items-center" style="border: 1px solid #fff; border-radius:50%; width: 5rem; height: 5rem;">
                                                        <i class="fa fa-check color-success text-white" style="font-size: 3rem;"></i>
                                                    </div>
                                                    <br/>
                                                    <span class="">Berhasil!, Silahkan Approve.</span>
                                                </div>

                                           </div>
                                           <div  id="gagal" hidden class="">
                                                <div class="d-flex justify-content-center align-items-center m-5 flex-column"> <!-- Tambahkan flex-column di sini -->
                                                    <div class="bg-danger d-flex justify-content-center align-items-center" style="border: 1px solid #fff; border-radius:50%; width: 5rem; height: 5rem;">
                                                        <i class="fa fa-times color-success text-white" style="font-size: 3rem;"></i>
                                                    </div>
                                                    <br/>
                                                    <span class="">Gagal!, Silahkan Cob Lagi.</span>
                                                </div>

                                           </div>
                                           <div id="pilihSurat" hidden>
                                                <div class="d-flex justify-content-start row mx-auto" id="elementPilihSurat">
                                                    
                                                </div>
                                           </div>
                                           <div id="formSK">
                                                <div class="row-12 mb-3 mt-3">
                                                    <div class="d-flex justify-content-between">
                                                        <label class="form-label">Upload SK</label>
                                                        <a href="javascript:void(0)" class="text-success" id="generateSK">Generate SK <i class="fa fa-download"></i></a>
                                                    </div>
                                                    <input type="file" class="form-control" id="upload_sk" name=""/>
                                                </div>
                                                <div class="row-12 mb-3">
                                                    <label class="form-label">Nomor SK</label>
                                                    <input type="text" autocomplete="off" class="form-control" id="nomor_sk" name=""/>
                                                </div>
                                           </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center ">
                                <div class="bg-collaps rounded mt-4" style="width: 97%;">
                                    <div class="collapse multi-collapse " id="multiCollapseExample" >
                                        <div class="row">
                                            <div class="row-12 mb-3 mt-3">
                                                <label class="form-label">Alasan</label>
                                                <textarea class="form-control" id="alasanReject"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

        <!--<div class="modal row fade" id="modalsConfirm" >-->
        <!--    <div class="modal-dialog col-md-6 " role="document">-->
        <!--        <div class="modal-content">-->
        <!--            <div class="modal-header">-->
        <!--                <h5 class="modal-title">Konfirmasi</h5>-->
        <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
        <!--                </button>-->
        <!--            </div>-->
        <!--                <div class="modal-body">-->
                            
        <!--                </div>-->
        <!--            <div class="modal-footer">-->
        <!--                <button id="secApp" class="btn btn-primary ">Approve</button>-->
        <!--            </div>    -->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->

      
    <!--<div class="modal-container">-->
    <!--    <div class="modal fade modal-left" id="modals" tabindex="-1" role="dialog" aria-labelledby="modal1Label" aria-hidden="true">-->
    <!--        <div class="modal-dialog modal-lg" role="document">-->
    <!--            <div class="modal-content">-->
    <!--                    <div class="modal-header">-->
    <!--                        <h5 class="modal-title" id="exampleModalLabel">Detail Pengajuan</h5>-->
    <!--                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
    <!--                        </button>-->
    <!--                    </div>-->
    <!--                    <form class="form-horizontal" method="post" id="sample_form1" enctype="multipart/form-data">-->
    <!--                        <div class="modal-body">-->
    <!--                            <div class="basic-form" id="bodai">-->
    <!--                            </div>-->
    <!--                            <div id="tab_pasangan" style="display: none">-->
    <!--                                <div class="col-md-12 mb-3">-->
    <!--                                    <table class="table table-bordered ">-->
    <!--                                        <thead>-->
    <!--                                            <tr>-->
    <!--                                            <th width="40%">Nama Suami / Istri</th>-->
    <!--                                            <th width="25%">Tanggal Lahir</th>-->
    <!--                                            <th width="25%">Tanggal Nikah</th>-->
    <!--                                            </tr>-->
    <!--                                        </thead>-->
    <!--                                        <tbody id="table">-->
    <!--                                        </tbody>-->
    <!--                                    </table>-->
    <!--                                </div>-->
    <!--                            </div>-->
                                
    <!--                            <div class="row">-->
    <!--                                                <div id="tab_anak" style="display: none">-->
    <!--                                                    <div class="col-md-12">-->
    <!--                                                        <table class="table table-bordered ">-->
    <!--                                                            <thead>-->
    <!--                                                                <tr>-->
    <!--                                                                    <th width="40%">Nama Anak</th>-->
    <!--                                                                    <th width="25%">Tanggal Lahir</th>-->
    <!--                                                                    <th width="25%">Status</th>-->
    <!--                                                                </tr>-->
    <!--                                                            </thead>-->
    <!--                                                            <tbody id="table_anak">-->
    
    <!--                                                            </tbody>-->
    <!--                                                        </table>-->
    <!--                                                    </div>-->
    <!--                                                </div>-->
    <!--                                            </div>-->
    <!--                        </div>-->
    <!--                          <div class="modal-footer">-->
    <!--                            <div id="footai">-->
    <!--                            </div>-->
    <!--                        </div>    -->
                            
    <!--                    </form>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <div class="modal fade modal-right" id="modals2" tabindex="-1" role="dialog" aria-labelledby="modal2Label" aria-hidden="true">-->
    <!--        <div class="modal-dialog modal-lg" role="document">-->
    <!--            <div class="modal-content">-->
    <!--                    <div class="modal-header">-->
    <!--                        <h5 class="modal-title" id="exampleModalLabel">Detail Pengajuan ah ah</h5>-->
    <!--                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
    <!--                        </button>-->
    <!--                    </div>-->
    <!--                    <form class="form-horizontal" method="post" id="sample_form1" enctype="multipart/form-data">-->
    <!--                        <div class="modal-body">-->
    <!--                            <div class="basic-form" id="bodai">-->
    <!--                            </div>-->
    <!--                                    <div id="tab_pasangan" style="display: none">-->
    <!--                                <div class="col-md-12 mb-3">-->
    <!--                                    <table class="table table-bordered ">-->
    <!--                                        <thead>-->
    <!--                                            <tr>-->
    <!--                                            <th width="40%">Nama Suami / Istri</th>-->
    <!--                                            <th width="25%">Tanggal Lahir</th>-->
    <!--                                            <th width="25%">Tanggal Nikah</th>-->
    <!--                                            </tr>-->
    <!--                                        </thead>-->
    <!--                                        <tbody id="table">-->
    <!--                                        </tbody>-->
    <!--                                    </table>-->
    <!--                                </div>-->
    <!--                            </div>-->
                                
    <!--                                    <div class="row">-->
    <!--                                                <div id="tab_anak" style="display: none">-->
    <!--                                                    <div class="col-md-12">-->
    <!--                                                        <table class="table table-bordered ">-->
    <!--                                                            <thead>-->
    <!--                                                                <tr>-->
    <!--                                                                    <th width="40%">Nama Anak</th>-->
    <!--                                                                    <th width="25%">Tanggal Lahir</th>-->
    <!--                                                                    <th width="25%">Status</th>-->
    <!--                                                                </tr>-->
    <!--                                                            </thead>-->
    <!--                                                            <tbody id="table_anak">-->
    
    <!--                                                            </tbody>-->
    <!--                                                        </table>-->
    <!--                                                    </div>-->
    <!--                                                </div>-->
    <!--                                            </div>-->
    <!--                        </div>-->
    <!--                          <div class="modal-footer">-->
    <!--                            <div id="footai">-->
    <!--                            </div>-->
    <!--                        </div>    -->
                            
    <!--                    </form>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
        
    <!--</div>-->

      


      
      
        
        <div class="row">
            <div class="col-xl-12">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-primary btn-block btn-xxs " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <div class="basic-form mb-3">
                                            <div class="row">
                                                <div class="col-lg-3 mb-3">
                                                    <label>Jenis Perubahan</label>
                                                    <select required class="form-control cek1" name="perubahan" id="perubahan">
                                                         <option value="">Tampilkan Semua</option>
                                                        <option value="pangkat">Kenaikan Pangkat</option>
                                                        <option value="jabatan">Kenaikan Jabatan</option>
                                                        <option value="keluarga">Perubahan Keluarga</option>
                                                        <option value="mutasi">Mutasi Karyawan</option>
                                                    </select>
                                                </div>
                                                
                                                    <div class="col-lg-3 mb-3" id="tgldari">
                                                        <label>Dari  :</label>
                                                        <input type="date" class="form-control cek2" id="dari" name="dari">
                                                    </div>
                                                
                                                    <div class="col-lg-3 mb-3" id="tglke">
                                                        <label>Sampai:</label>
                                                        <input type="date" class="form-control cek3" id="sampai" name="sampai">
                                                    </div>
                                         
                                                 <div class="col-lg-3 mb-3">
                                                    <label>Status</label>
                                                    <select required class="form-control cek4" name="sts" id="sts">
                                                        <option value="2">Pending</option>
                                                        <option value="1">Approved</option>
                                                        <option value="0">Rejected</option>
                
                                                    </select>
                                                </div>

                                             
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="user_table" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Tanggal Pengajuan</th>
                                                        <th>Nama Karyawan</th>
                                                        <th>Pengajuan</th>
                                                        <th>User approve</th>
                                                        <th>Status</th>
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
            </div>
        </div>
    </div>
</div>
@endsection