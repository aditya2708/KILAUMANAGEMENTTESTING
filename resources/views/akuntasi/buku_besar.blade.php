@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
        <div class="row">
        
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{url('buku-besar-export')}}">
                            <div class="basic-form">
                                <div class="row">
                                      
                                    <?php
                                    $hari_ini = date('Y-m-d');
                                    $dari = date('Y-m-d', strtotime($hari_ini));
                                    $sampai = date('Y-m-d', strtotime($hari_ini));
                                    ?>
                                    
                                    <?php
                                    $tahun = date('Y');
                                    $m = date('m');
                                    $months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des');
                                    $bulan = $months[(int)$m];
                                    ?>
                                    
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Pilih</label>
                                        <select class="form-control cek0" name="prd" id="plhtgl" >
                                            <option value="0" selected>Hari</option>
                                            <option value="1">Bulan</option>
                                            <option value="2">Tahun</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3 col-md-4" id="rangetgl">
                                        <label class="form-label">Range Tanggal</label>
                                        <div id="hide_tgl" style="display: block">
                                            <input type="text" class="form-control cek1" autocomplete="off" id="daterange" name="daterange" placeholder="{{$dari.' s.d. '.$sampai}}" />
                                        </div>
                                        <div id="hide_thn" style="display: none">
                                            <input type="month" class="form-control cek0" id="month" name="month" style="width: 100%" onchange="handler(event);">
                                        </div>
                                        <div id="hides" style="display: none">
                                            <input type="" class="form-control cek12 ttttt" id="years" name="years" autocomplete="off" value="{{ date('Y') }}" placeholder="{{ date('Y') }}">
                                        </div>
                                    </div>
                                        
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Kantor</label>
                                        <select class="form-control cek7" name="kota" id="unit">
                                            <option value="">Pilih kantor</option>
                                            @foreach($kantor as $ka)
                                            <option value="{{$ka->id}}">{{$ka->unit}}</option>
                                            @endforeach
                                            
                                        </select>
                                    </div>
                                    <!--<div class="mb-3 col-md-3">-->
                                    <!--    <label class="form-label">Kantor</label>-->
                                    <!--    <select class="form-control " name="kantor" id="kantor" >-->
                                    <!--       @foreach($kantor as $k)-->
                                    <!--        <option value="{{ $k->id_kantor }}">{{ $k->unit }}</option>-->
                                    <!--       @endforeach-->
                                    <!--    </select>-->
                                    <!--</div>-->
                                  
                                    
                                    
                                    
                               
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Group By</label>
                                    <select class="groupby" name="groupby" id="groupby">
                                        <option value="">-Pilih-</option>
                                        <option value="0">Transkasi Tanggal</option>
                                        <option value="1">Transkasi Perbulan</option>
                                        <option value="2">Transkasi Pertahun</option>
                                    </select>
                                </div>
                                
                                 <div class="mb-3 col-md-4">
                                    <label class="form-label">Jenis Transaksi</label>
                                    <select class="form-control cek6 buook" name="buku" id="buku">
                                    
    
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label class="form-label">Jenis</label>
                                    <select class="form-control cek6 jen" name="jen" id="jen">
                                        <option value="">- Pilih -</option>
                                        <option value="0">Debet</option>
                                        <option value="1">Kredit</option>
                                    </select>
                                </div>
                                <!--<div class="mb-3 col-md-3">-->
                                <!--    <label class="form-label">Status</label>-->
                                <!--    <select class="form-control cek8" name="stts" id="stts">-->
                                <!--        <option value="">All</option>-->
                                <!--        <option value="1" selected>Approved</option>-->
                                <!--        <option value="0">Rejected</option>-->
                                <!--        <option value="2">Pending</option>-->
                                <!--    </select>-->
                                <!--</div>-->
                            
                                
                                <!--<div class="mb-3 col-md-4">-->
                                <!--    <label class="form-label">Buku</label>-->
                                <!--    <select class="form-control cek6 buook" name="buku" id="buku">-->
                                <!--    </select>-->
                                <!--</div>-->
                            </div>
                        
                    </div>
                </div>
            </div>
            
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Buku Besar <span id="miaw"></span></h4>
                        
                        <div class="mb-3 col-md-2 d-grid">
                            <div class="btn-group">
                                 <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" >
                                    Ekspor
                                </button>
                                <ul class="dropdown-menu">
                                    <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>
                                    <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>
                                    <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table id="user_tablex" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th >Tanggal</th>
                                                <th >COA</th>
                                                <th >Jenis Transaksi</th>
                                                <th >Keterangan</th>
                                                <th >Debet</th>
                                                <th >Kredit</th>
                                                <th >Saldo</th>
                                                <th >ID Transaksi</th>
                                                <th hidden>urut</th>
                                                <th hidden>ids</th>
                                                <th hidden>urut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th style="text-align:center">Total:</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th hidden></th>
                                                <th hidden></th>
                                                <th hidden></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        </div>
        
    </div>
</div>
@endsection