@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
        <div class="modal fade" id="modal" >
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <!--<form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">-->
                        <div class="modal-body">
                            <div class="basic-form" id="boday">
                                
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div id="footay">
                                
                            </div>
                        </div>
                    <!--</form>-->
                </div>
            </div>
        </div>
        
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                     <form method="GET" action="{{url('pengajuan-anggaran/export')}}" >
                    <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Filter Tanggal</label>
                                        <select id="periodenya" class="default-select form-control wide" name="periodenya">
                                            <option value="">Pilih</option>
                                            <option value="harian">Periode</option>
                                            <option value="bulan">Bulan</option>
                                            <option value="tahun">Tahun</option>
                                        </select>
                                </div>
    
                                    <div class="col-lg-3 mb-3" hidden id="harian_hide" >
                                        <label>Dari  :</label>
                                        <input type="date" class="form-control cek1" id="dari" name="dari">
                                    </div>
                            
                                    <div class="col-lg-3 mb-3" hidden id="harian_hidek" >
                                        <label>Sampai:</label>
                                        <input type="date" class="form-control cek2" id="sampai" name="sampai">
                                    </div>

                                 <div class="mb-3 col-md-3" id="bulanan_hide" hidden>
                                     <label class="form-label">Dari Bulan</label>
                                     <input type="text" id="darib" name="darib" class="goa form-control cek3 bulan" autocomplete="off" placeholder="{{date('m-Y') }}" />
                                </div>
    
    
                                <div class="mb-3 col-md-3" id="bulanan_hidek" hidden>
                                     <label class="form-label">Sampai Bulan</label>
                                     <input type="text" id="sampaib" name="sampaib" class="goa form-control cek4 bulan" autocomplete="off" placeholder="{{date('m-Y') }}" />
                                </div>
                                
    
                                <div class="mb-3 col-md-3" id="tahunan_hide" hidden>
                                    <label class="form-label">Tahun :</label>
                                    <input type="text" class="form-control tahun cek5" name="thnn" id="thnn" autocomplete="off" placeholder="{{date('Y') }}">
                                </div>
   
                       
                         </div>
                        
                        <!--<div class="basic-form">-->
                        <!--    <div class="row">-->
                               
                        <!--    <div class="col-lg-3 mb-3">-->
                        <!--            <label >Periode :</label>-->
                        <!--                <select class="form-control cekp" name="periodenya" id="periodenya">-->
                        <!--                     <option value="">Pilih</option>-->
                        <!--                     <option value="harian">harian</option>-->
                        <!--                     <option value="bulan">bulan</option>-->
                        <!--                     <option value="tahun">tahun</option>-->
                        <!--                </select>-->
                        <!--        </div>-->
                         
                            
                            <!--    <div class="col-lg-3 mb-3">-->
                            <!--        <label>Via </label>-->
                            <!--        <select class="form-control default-select wide ceks" name="via" id="via">-->
                            <!--            <option value="">All</option>-->
                            <!--            <option value="transaksi">Transaksi</option>-->
                            <!--            <option value="pengeluaran">Pengeluaran</option>-->
                            <!--        </select>-->
                            <!--    </div>-->

                            <!--<div hidden id="harian_hide" class="row">-->
                            <!--    <div class="col-lg-3 mb-3" id="tgldari">-->
                            <!--        <label>Dari  :</label>-->
                            <!--        <input type="date" class="form-control cekd" id="dari" name="dari">-->
                            <!--    </div>-->
                            
                            <!--    <div class="col-lg-3 mb-3" id="tglke">-->
                            <!--        <label>Sampai:</label>-->
                            <!--        <input type="date" class="form-control cekt" id="sampai" name="sampai">-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                        <!--    <div hidden id="bulanan_hide" class="row">-->
                        <!--        <div class="col-lg-3 mb-3" id="tgldari">-->
                        <!--            <label>Dari Bulan:</label>-->
                        <!--            <input type="text" class="col-lg-3 mb-3 form-control bulan cekd" name="darib" id="darib" autocomplete="off" placeholder="{{date('Y-m') }}">-->
                        <!--        </div>-->
                            
                            
                        <!--        <div class="col-lg-3 mb-3" id="tglke">-->
                        <!--            <label>Sampai Bulan:</label>-->
                        <!--            <input type="text" class="col-lg-3 mb-3 form-control bulan cekt" name="sampaib" id="sampaib" autocomplete="off" placeholder="{{date('Y-m') }}">-->
                        <!--        </div>-->
                        <!--    </div>-->
                            
                        <!--    <div hidden id="tahunan_hide" class="row">-->
                        <!--        <div class="col-lg-3 mb-3" id="tgldari">-->
                        <!--            <label>dari Tahuan:</label>-->
                        <!--            <input type="text" class="col-lg-3 mb-3 form-control tahun cekd" name="darit" id="darit" autocomplete="off" placeholder="{{date('Y') }}">-->
                        <!--        </div>-->

                        <!--        <div class="col-lg-3 mb-3" id="tglke">-->
                        <!--            <label>Sampai Tahuan:</label>-->
                        <!--            <input type="text" class="col-lg-3 mb-3 form-control tahun cekt" name="sampait" id="sampait" autocomplete="off" placeholder="{{date('Y') }}">-->
                        <!--        </div>-->
                        <!--    </div>      -->
                            
                        <!--    </div>-->
                        <!--</div>-->
                     
                        
                         <div class="table-responsive">
                                <table id="user_table" class="table table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                             <th>ID Data</th>
                                             <th>Keterangan</th>
                                             <th>Via</th>
                                             <th>Jenis Aksi</th>
                                             <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                   
                                </table>
                            </div>
                        </div>
                        
                    </form>    
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection





