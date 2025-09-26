@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
        <!--Modal-->
        <div class="modal fade" id="modals">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="judul"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form class="form-horizontal" method="post" id="co_form" onkeydown="return event.key != 'Enter';">
                        <div class="modal-body">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="basic-form">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">ID Karyawan :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="ID.." id="id_kar_co" name="id_kar_co" disabled>
                                         <input type="hidden" id="id_kar_co_hide" name="id_kar_co_hide" />
                                        <input type="hidden" id="tipe" name="tipe" value="CO">
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">User Input : </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Nama.." id="nama_co" name="nama_co" disabled>
                                        <input type="hidden" id="nama_co_hide" name="nama_co_hide" />
                                    </div>
                                </div>
                                            
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Periode <select id="periods" name="periods" style="outline: none; border: none;" readonly>
                                        <option value="tgl" selected>Tanggal</option>
                                        <option value="bln">Bulan</option>
                                        <option value="thn">Tahun</option>
                                    </select>: </label>
                                    <div class="col-sm-8" id="tgs" style="display: block">
                                        <input type="date" class="form-control" id="tanggal_co" name="tanggal_co" autocomplete="off" placeholder="{{date('Y-m-d')}}" readonly>
                                    </div>
                                    
                                    <div class="col-sm-8" id="bls" style="display: none">
                                        <input type="text" class="form-control goa" id="bulan_co" name="bulan_co" autocomplete="off" placeholder="{{date('m-Y')}}" readonly>
                                    </div>
                                    
                                    <div class="col-sm-8" id="ths" style="display: none">
                                        <input type="text" class="form-control year" id="tahun_co" name="tahun_co" autocomplete="off" placeholder="{{date('Y')}}" readonly>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Kantor : </label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="kntr_co" name="kntr_co" readonly>
                                            @foreach($kantor as $b)
                                            <option value="{{$b->id_coa}}" {{ Auth::user()->id_kantor == $b->id ? 'selected' : '' }} </option>{{$b->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                            
                                
                                            
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Saldo Awal : </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="0" id="saldo_awal_co" name="saldo_awal_co" onkeyup="rupiah(this);" onclick="rupiah(this);" readonly>
                                    </div>
                                </div>
                                            
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">∑ Penerimaan : </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="0" id="penerimaan_co" name="penerimaan_co" onkeyup="rupiah(this);" onclick="rupiah(this);" readonly>
                                    </div>
                                </div>
                                            
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">∑ Pengeluaran  : </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="0" id="pengeluaran_co" name="pengeluaran_co" onkeyup="rupiah(this);" onclick="rupiah(this);" readonly>
                                    </div>
                                </div>
                                            
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Penyesuaian  : </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" readonly placeholder="0" id="penyesuaian_co" name="penyesuaian_co" onkeyup="rupiah(this);" onclick="rupiah(this);" readonly>
                                    </div>
                                </div>
                                            
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Saldo Akhir  : </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="0" id="saldo_akhir_co" name="saldo_akhir_co" onkeyup="rupiah(this);" onclick="rupiah(this);" readonly>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Saldo Fisik  : </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="0" id="s_fisik" name="s_fisik" readonly>
                                        <input type="hidden" id="s_fisik_hide" name="s_fisik_hide" onkeyup="rupiah(this);" onclick="rupiah(this);">
                                        <label id="ihi" class="mt-2"></label>
                                    </div>
                                </div>
                                            
                            </div> 
                                </div>
                                
                                <input type="hidden" id="inputk1" name="inputk1">
                                <input type="hidden" id="inputk2" name="inputk2">
                                <input type="hidden" id="inputk3" name="inputk3">
                                <input type="hidden" id="inputk4" name="inputk4">
                                <input type="hidden" id="inputk5" name="inputk5">
                                <input type="hidden" id="inputk6" name="inputk6">
                                <input type="hidden" id="inputk7" name="inputk7">
                                <input type="hidden" id="inputk8" name="inputk8">
                                <!--<input type="hidden" id="inputk9" name="inputk9">-->
                                <!--<input type="hidden" id="inputk10" name="inputk10">-->
                                
                                <input type="hidden" id="inputl1" name="inputl1">
                                <input type="hidden" id="inputl2" name="inputl2">
                                <input type="hidden" id="inputl3" name="inputl3">
                                <input type="hidden" id="inputl4" name="inputl4">
                                <!--<input type="hidden" id="inputl5" name="inputl5">-->
                                <!--<input type="hidden" id="inputl6" name="inputl6">-->
                                
                                <div class="col-lg-4">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="kert">
                                            <thead>
                                                <tr>
                                                    <td>#</td>
                                                    <td>Pecahan Kertas</td>
                                                    <td>Quantity</td>
                                                    <td>Total</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>100.000</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>2</td>
                                                    <td>75.000</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>3</td>
                                                    <td>50.000</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>4</td>
                                                    <td>20.000</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>5</td>
                                                    <td>10.000</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>6</td>
                                                    <td>5.000</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>7</td>
                                                    <td>2.000</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>8</td>
                                                    <td>1.000</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                
                                                <!--<tr>-->
                                                <!--    <td>9</td>-->
                                                <!--    <td>500</td>-->
                                                <!--    <td></td>-->
                                                <!--    <td></td>-->
                                                <!--</tr>-->
                                                <!--<tr>-->
                                                <!--    <td>10</td>-->
                                                <!--    <td>100</td>-->
                                                <!--    <td></td>-->
                                                <!--    <td></td>-->
                                                <!--</tr>-->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="logs">
                                            <thead>
                                                <tr>
                                                    <td>#</td>
                                                    <td>Pecahan Logam</td>
                                                    <td>Quantity</td>
                                                    <td>Total</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>1.000</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>2</td>
                                                    <td>500</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>3</td>
                                                    <td>200</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>4</td>
                                                    <td>100</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <!--<tr>-->
                                                <!--    <td>5</td>-->
                                                <!--    <td>50</td>-->
                                                <!--    <td></td>-->
                                                <!--    <td></td>-->
                                                <!--</tr>-->
                                                <!--<tr>-->
                                                <!--    <td>6</td>-->
                                                <!--    <td>25</td>-->
                                                <!--    <td></td>-->
                                                <!--    <td></td>-->
                                                <!--</tr>-->
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
                                
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="kih">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modal_aja">
            <div class="modal-dialog modal-dialog-centered" role="document" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="juduls"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form class="form-horizontal" method="post" id="bo_form" onsubmit="return false;">
                        <div class="modal-body">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="basic-form">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">ID Karyawan :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="ID.." id="id_kar_bo" name="id_kar_bo" disabled>
                                        <input type="hidden" id="id_kar_bo_hide" name="id_kar_bo_hide" />
                                        <input type="hidden" id="tipe" name="tipe" value="BO">
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">User Input : </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Nama.." id="nama_bo" name="nama_bo" disabled>
                                        <input type="hidden" id="nama_bo_hide" name="nama_bo_hide" />
                                    </div>
                                </div>
                                            
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Periode <select id="period" name="period" style="outline: none; border: none;">
                                        <option value="tgl" selected>Tanggal</option>
                                        <option value="bln">Bulan</option>
                                        <option value="thn">Tahun</option>
                                    </select>: </label>
                                    <div class="col-sm-8" id="tg" style="display: block">
                                        <input type="date" class="form-control" id="tanggal_bo" name="tanggal_bo" autocomplete="off" placeholder="{{date('Y-m-d')}}">
                                    </div>
                                    
                                    <div class="col-sm-8" id="bl" style="display: none">
                                        <input type="text" class="form-control goa" id="bulan_bo" name="bulan_bo" autocomplete="off" placeholder="{{date('m-Y')}}">
                                    </div>
                                    
                                    <div class="col-sm-8" id="th" style="display: none">
                                        <input type="text" class="form-control year" id="tahun_bo" name="tahun_bo" autocomplete="off" placeholder="{{date('Y')}}">
                                    </div>
                                </div>
                                            
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Bank : </label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="bank_bo" name="bank_bo">
                                            @foreach($bank as $b)
                                            <option value="{{$b->coa}}">{{$b->nama_coa}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                            
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Saldo Awal : </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="0" id="saldo_awal_bo" name="saldo_awal_bo" onkeyup="rupiah(this);" onclick="rupiah(this);" readonly>
                                    </div>
                                </div>
                                            
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">∑ Penerimaan : </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="0" id="penerimaan_bo" name="penerimaan_bo" onkeyup="rupiah(this);" onclick="rupiah(this);" readonly>
                                    </div>
                                </div>
                                            
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">∑ Pengeluaran  : </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="0" id="pengeluaran_bo" name="pengeluaran_bo" onkeyup="rupiah(this);" onclick="rupiah(this);" readonly>
                                    </div>
                                </div>
                                            
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Penyesuaian  : </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" readonly placeholder="0" id="penyesuaian_bo" name="penyesuaian_bo" onkeyup="rupiah(this);" onclick="rupiah(this);" readonly>
                                    </div>
                                </div>
                                            
                                <div class="mb-3 row">
                                    <label class="col-sm-4 col-form-label">Saldo Akhir  : </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="0" id="saldo_akhir_bo" name="saldo_akhir_bo" onkeyup="rupiah(this);" onclick="rupiah(this);" readonly>
                                    </div>
                                </div>
                                            
                            </div> 
                                </div>
                                
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" >Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--End MOdal-->
        
        <form action="{{url('penutupan_ekspor')}}" method="get">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="basic-form">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label>Penutupan</label>
                                    <select class="form-control cek1" name="pen" id="pen">
                                        <option selected value="tanggal">Tanggal</option>
                                        <option value="bulan">Bulan</option> 
                                        <option value="tahun">Tahun</option> 
                                    </select>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label>Tanggal</label>
                                        <!-- <label class="form-label">Range Tanggal</label> -->
                                        <input type="text" name="daterange" class="form-control cek2" id="daterange" placeholder="Range Tanggal" autocomplete="off"/>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <?php $aha = Auth::user()->id_kantor ?>
                                    <label>Kantor</label>
                                        <select class="form-control cek3" name="kans" id="kans">
                                            @foreach($kantor as $k)
                                            <option value="{{$k->id}}" {{$k->id == $aha ? 'selected' : ''  }}>{{$k->unit}}</option>
                                            @endforeach
                                        </select>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label>Akun</label>
                                        <select class="form-control cek4" name="akun" id="akun">
                                            
                                        </select>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label>Buku</label>
                                        <select class="form-control cek2" name="buk" id="buk">
                                            <option value="">Semua</option>
                                            <option value="bank">Bank</option> 
                                            <option value="kas">Kas</option>
                                        </select>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="title-header">Penutupan</h4>
                        <!--<div class="d-flex justify-content-end">-->
                        <!--    <button type="button" class="btn btn-rounded btn-success" id="eksd" data-bs-toggle="modal" data-bs-target="#eksdata" style="margin-right: 10px"><span class="btn-icon-start text-success"><i class="fa fa-download  color-success"></i></span>Ekspor Data</button>-->
                        <!--    <button type="button" class="btn btn-rounded btn-info" id="eksp" data-bs-toggle="modal" data-bs-target="#ekspay" style="margin-right: 10px"><span class="btn-icon-start text-info"><i class="fa fa-download  color-info"></i></span>Ekspor Payroll</button>-->
                        <!--    <button type="button" class="btn btn-rounded btn-warning" id="eksb" data-bs-toggle="modal" data-bs-target="#eksbpjs"><span class="btn-icon-start text-warning"><i class="fa fa-download  color-warning"></i></span>Ekspor BPJS</button>-->
                            <!--<a href="#" id="eksd" data-bs-toggle="modal" data-bs-target="#eksdata" class="btn btn-success btn-xs">Eksport Data</a>-->
                        <button class="btn btn-info btn-xs" type="submit">Ekspor</button>
                            <!--<a href="#" id="eksb" data-bs-toggle="modal" data-bs-target="#eksbpjs" class="btn btn-warning btn-xs">Eksport BPJS</a>-->
                        <!--</div>-->
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table id="user_table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Aksi</th>
                                                <th>Tanggal</th>
                                                <th>Akun</th>
                                                <th>Saldo Akhir</th>
                                                <th>Saldo Awal</th>
                                                <th>Debit</th>
                                                <th>Kredit</th>
                                                <th>Adjustment</th>
                                                <th>COA</th>
                                                <th>User Input</th>
                                                <th>User Update</th>
                                                <th>K100000</th>
                                                <th>K750000</th>
                                                <th>K50000</th>
                                                <th>K20000</th>
                                                <th>K10000</th>
                                                <th>K5000</th>
                                                <th>K2000</th>
                                                <th>K1000</th>
                                                <!--<th>K500</th>-->
                                                <!--<th>K100</th>-->
                                                <th>L1000</th>
                                                <th>L500</th>
                                                <th>L200</th>
                                                <th>L100</th>
                                                <!--<th>L50</th>-->
                                                <!--<th>L25</th>-->
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
        </form>
    </div>
</div>
@endsection