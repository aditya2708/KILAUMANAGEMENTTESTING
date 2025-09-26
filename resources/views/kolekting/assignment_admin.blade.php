@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Kolekting</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Assignment Kolekting</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <?php
        $kot = Auth::user()->id_kantor;
        
        if(Auth::user()->level == 'admin'){
            $countnot = \DB::select("SELECT COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS totkun,
             COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS tottup from donatur WHERE DATE_FORMAT(created_at, '%Y-%m') <> DATE_FORMAT(CURDATE(), '%Y-%m') AND id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))");
        }else if(Auth::user()->level == 'kacab'){
            if($k == null){
                $countnot = \DB::select("SELECT COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS totkun,
                 COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS tottup from donatur WHERE id_kantor = '$kot'
                AND DATE_FORMAT(created_at, '%Y-%m') <> DATE_FORMAT(CURDATE(), '%Y-%m') AND id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))");
            }else{
                $countnot = \DB::select("SELECT COUNT(IF(status = 'belum dikunjungi' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS totkun,
                 COUNT(IF(status = 'Tutup' AND acc = 0 AND pembayaran = 'dijemput', id, NULL)) AS tottup from donatur WHERE (id_kantor = '$kot' OR id_kantor = '$k->id')
                AND DATE_FORMAT(created_at, '%Y-%m') <> DATE_FORMAT(CURDATE(), '%Y-%m') AND id IN (SELECT id_don FROM prosp WHERE ket = 'closing' AND (id_prog = 82 OR id_prog = 83))");
            }
        }
        $data = \DB::select("SELECT distinct jalur, kota from donatur ORDER BY jalur ASC ");
        
        ?>
        @foreach ($countnot as $countnot)
        <div class="row mb-3">
            <div class="col-lg-12">
                <a data-bs-toggle="modal" data-bs-target="#belumdiassignment" href="#" class="btn btn-primary btn-md light" style="margin-bottom: 10px; float: right">Belum di Assignment : <b>{{$countnot->totkun + $countnot->tottup}} Donatur</b></a>
            </div>
        </div>
        @endforeach

        <!-- modal -->
        <div class="modal fade" id="belumdiassignment">
            <div class="modal-dialog modal-dialog-centered" role="document" style="overflow-y: initial; ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Rincian petugas yang belum mengunjungi donatur per <?php $dt = new DateTime();
                                                                                                                            echo $dt->format('M/Y'); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="height: 440px;overflow-y: auto;">
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Unit</label>
                                <select id="unt" name="unt[]" class="rora meu" multiple="multiple">
                                    @foreach($kota as $d)
                                    <option value="{{$d->id}}">{{$d->unit}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-striped" id="wew">
                                <thead>
                                    <tr>
                                        <th>Nama Jalur</th>
                                        <th>Belum Dikunjungi</th>
                                        <th>Tutup 1x</th>
                                    </tr>
                                </thead>
                                <tbody id="bod">
                                   
                                </tbody>
                                <tfoot id="fot">
                                    
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="modal fade" id="jadwalass">
            <div class="modal-dialog modal-dialog-centered" role="document" style="overflow-y: initial;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Penjadwalan Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
                    </div>
                    <!--<div class="modal-body" style="height: 440px;overflow-y: auto;">-->
                        <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-9">
                                <label>Tanggal</label>
                                <input type="date" name="tgll" id="tgll" class="form-control">
                            </div>
                            <input type="hidden" id="tip" name="tip">
                            <input type="hidden" id="warn" name="warn">
                            <input type="hidden" id="donnn" name="donn[]">
                            
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="button" id="wekwek" class="btn btn-sm btn-success">Jadwalkan</button>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="petugasss">
            <div class="modal-dialog modal-dialog-centered" role="document" style="overflow-y: initial;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Perubahan Petugas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
                    </div>
                        <div class="modal-body">
                            
                        <?php
                        
                            $k = Auth::user()->id_kantor;
                            $com = Auth::user()->id_com;
                            if (Auth::user()->kolekting == ('admin')) {
                                $da = \DB::select("SELECT * from users where kolektor IS NOT NULL and aktif = 1 AND id_com = '$com' ");
                            } else {
                                $da = \DB::select("SELECT * from users where (id_kantor = '$k' OR kantor_induk = '$k') and kolektor IS NOT NULL and aktif = 1 AND id_com = '$com' ");
                            }
            
                        ?>

                        <div class="row mb-3">
                            <div class="col-md-9">
                                <label>Petugas</label>
                                <!--<input type="date" name="tgll" id="tgll" class="form-control">-->
                                <select name="ptg" class="form-control" id="ptg">
                                    <option value="">Pilih Petugas</option>
                                    @foreach($da as $d)
                                    <option value="{{$d->id}}">{{$d->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <input type="hidden" id="tip" name="tip">
                            <input type="hidden" id="warn" name="warn">
                            <input type="hidden" id="donnn" name="donn[]">
                            
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="button" id="wikwik" class="btn btn-sm btn-success">Set Perubahan</button>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="setW">
            <div class="modal-dialog modal-dialog-centered" role="document" style="overflow-y: initial;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Set Donatur Warning </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Jumlah Bulan</label>
                                    <input type="number"min="0" name="jumbul" id="jumbul" class="form-control" required>
                                </div>
                                
                                <div class="col-md-4">
                                    <label>Minimal Donasi</label>
                                    <input type="number" min="0" name="mindon" id="mindon" class="form-control" required>
                                </div>
                                
                                <div class="col-md-4">
                                    <label></label>
                                    <button type="button" id="upp" class="btn btn-sm btn-success" style="margin-top: 20%">Update</button>
                                </div>
                                
                            </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="detailwarning">
            <div class="modal-dialog modal-dialog-centered" role="document" style="overflow-y: initial;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Donatur Warning</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-4"><h6>Jumlah Bulan : <span id="jum"></span></h6></div>
                            <div class="col-sm-5"><h6>Minimal Donasi : <span id="min"></span></h6></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="ngga">
                                <thead>
                                    <tr>
                                        <td>Bulan</td>
                                        <td>ID</td>
                                        <td>Donatur</td>
                                        <td>Donasi</td>
                                    </tr>
                                </thead>
                                <tbody id="kesed">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!--end modal-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filter</h3>
                        
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <div class="row">
                                
                                @if(Auth::user()->level == 'admin')
                                    <div class="col-lg-3 mb-3">
                                        <label>Unit</label>
                                        <select id="kota" class="form-control jaljal cek" name="kota">
                                            <option value="">Pilih Unit</option>
                                            @foreach($kota as $op)
                                            <option value="{{$op->id}}">{{$op->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
    
                                    <div class="col-lg-3 mb-3">
                                        <label>Jalur</label>
                                        <select id="jalurah" class="form-control jaljal cek2" name="jalurah">
                                            <option value="">Pilih Jalur</option>
                                        </select>
                                    </div>
                                @elseif(Auth::user()->level == 'kacab')
                                    @if($k == null)
                                    <?php
                                        $jaluras = App\Models\Jalur::where('id_kantor', $kot)->get();
                                    ?>
                                    <div class="col-lg-3 mb-3">
                                        <label>Jalur</label>
                                        <select id="jalurah" class="form-control jaljal cek2" name="jalurah">
                                            <option value="">Pilih Jalur</option>
                                            @foreach($jaluras as $op)
                                            <option value="{{$op->id_jalur}}">{{$op->nama_jalur}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @else
                                    <div class="col-lg-3 mb-3">
                                        <label>Unit</label>
                                        <select id="kota" class="form-control jaljal cek" name="kota">
                                            <option value="">Pilih Unit</option>
                                            @foreach($kota as $op)
                                            <option value="{{$op->id}}">{{$op->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
    
                                    <div class="col-lg-3 mb-3">
                                        <label>Jalur</label>
                                        <select id="jalurah" class="form-control jaljal cek2" name="jalurah">
                                            <option value="">Pilih Jalur</option>
                                        </select>
                                    </div>
                                    @endif
                                @else
                                <?php
                                    $jalur = App\Models\Jalur::where('id_kantor', $kot)->get();
                                ?>
                                    <div class="col-lg-3 mb-3">
                                        <label>Jalur</label>
                                        <select id="jalurah" class="form-control jaljal cek2" name="jalurah">
                                            <option value="">Pilih Jalur</option>
                                            @foreach ($jalur as $item)
                                            <option value="{{$item->id_jalur}}">{{$item->nama_jalur}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                
                                <div class="col-lg-3 mb-3">
                                    <label>Petugas</label>
                                    <select id="petugas" class="form-control jaljal cek2" name="petugas">
                                        <option value="">Pilih Petugas</option>
                                        @foreach ($datao as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                

                                <div class="col-lg-3 mb-3">
                                    <label>Pembayaran</label>
                                    <select id="pembayaran" class="form-control default-select wide cek3" name="pembayaran">
                                        <option value="">Pilih Pembayaran</option>
                                        @foreach($pemb as $op)
                                        <option value="{{$op->pembayaran}}">{{ucfirst($op->pembayaran)}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-3 mb-3">
                                    <label>Status Kunjungan</label>
                                    <select id="stts" class="form-control default-select wide cek4" name="stts">
                                        <option value="">Pilih Status Kunjungan</option>
                                        @foreach($stat as $op)
                                        <option value="{{$op->status}}">{{ucfirst($op->status)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Program</label>
                                    <select id="program" class="crot ceksi" style="width:100%" name="program">
                                        <option value=""></option>
                                    </select>
                                </div>
                                
                               <div class="col-lg-3 mb-3">
                                    <label>Donatur Warning</label>
                                    <select id="warnings" class="form-control cfk" name="warnings">
                                        <option value="">Pilih</option>
                                        <option value="1">Iya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                </div>
                               
                                <div class="col-lg-3 mb-3">
                                    <label>Aksi</label>
                                    <select class="form-control ahhh" id="pilihhh">
                                        <option value="">Pilih Aksi</option>
                                        <option value="jadwal_ass">Penjadwalan Assignment</option>
                                        <option value="ganti_petugas">Ganti Petugas</option>
                                        
                                        <option value="set_warning">Set warning donatur </option>
                                        
                                    </select>
                                    <button class="btn btn-xxs btn-danger mt-2" style="margin-left: 145px; display: none" id="muklis">batalkan</button>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Data Assignment</h4>
                        <div class="pull-right">
                            <a href="javascript:void(0)" class="btn btn-primary btn-sm light filtt  mt-9" style="float:right; margin-right:15px">Adv Search</a>
                            <a href="javascript:void(0)" class="btn btn-success btn-sm assign_all  mt-9" style="float:right; margin-right:15px">Assign All</a>
                            
                            <!--jadwal assignment-->
                            <a href="javascript:void(0)" class="btn btn-primary btn-sm mt-9" style="float:right; margin-right:15px; display: none" data-value="pilihan" id="jdwl_bbrp">Jadwalkan beberapa</a>
                            <a href="javascript:void(0)" class="btn btn-primary btn-sm mt-9" style="float:right; margin-right:15px; display: none" data-value="pilihan" id="dd">Jadwalkan</a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm mt-9" style="float:right; margin-right:15px; display: none" id="btl">Batal</a>
                            <a href="javascript:void(0)" class="btn btn-primary btn-sm mt-9" style="float:right; margin-right:15px; display: none" data-value="semua" id="jdwl_semua">Jadwalkan Semua</a>
                            <!--ganti petugas-->
                            <a href="javascript:void(0)" class="btn btn-primary btn-sm mt-9" style="float:right; margin-right:15px; display: none" data-value="pilihan" id="ptgs_bbrp">Ubah beberapa Petugas</a>
                            <a href="javascript:void(0)" class="btn btn-primary btn-sm mt-9" style="float:right; margin-right:15px; display: none" data-value="pilihan" id="ptgs_dd">Simpan  Perubahan</a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm mt-9" style="float:right; margin-right:15px; display: none" id="ptgs_btl">Batal</a>
                            <a href="javascript:void(0)" class="btn btn-primary btn-sm mt-9" style="float:right; margin-right:15px; display: none" data-value="semua" id="ptgs_semua">Ubah Semua Petugas</a>
                            <!--set warning-->
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm mt-9" style="float:right; margin-right:15px; display: none" id="setd">Set Warning Donatur</a>
                            
                        </div>
                    </div>"
                    <div class="card-body">
                        <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                            <table id="user_table" class="table table-striped display" width="100%">
                                <thead>
                                    <tr>
                                        <th display="none" id="yeye"></th>
                                        <th style="display: none">urut</th>
                                        <th style="display: none">id</th>
                                        <th scope="col">No</th>
                                        <th scope="col" class="cari">Nama Petugas</th>
                                        <th scope="col" class="cari">Nama Donatur</th>
                                        <th scope="col" class="cari">Jalur</th>
                                        <th scope="col" class="cari">Kota</th>
                                        <th scope="col" class="cari">Cara Pembayaran</th>
                                        <th scope="col">Tanggal Regis</th>
                                        <th scope="col">Status Kunjungan</th>
                                        <th scope="col">Terakhir Dikunjungi</th>
                                        <th scope="col">Tanggal Assignment</th>
                                        <th scope="col">Status Assignment</th>
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
@endsection