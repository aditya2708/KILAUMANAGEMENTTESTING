@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
        <!--modal-->
        <div class="modal fade" id="modals" >
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
        
        <div class="modal fade" id="modal-reject" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Alasan Pengeluaran di Reject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="reject_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="basic-form">
                                <div id="rej"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>-->
                            <button type="submit" class="btn btn-primary blokkk" id="smpnz">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end modal-->
        
        
        <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-xl-3 col-sm-3" id="f_salwal">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-primary text-primary">
                                                        <!-- <i class="ti-user"></i> -->
                                            <svg id="icon-revenue" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
    											<line x1="12" y1="1" x2="12" y2="23"></line>
    											<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
    										</svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Saldo Awal</p>
                                                <h4 class="mb-0" id="saldow">0</h4>
                                                    <!-- <span class="badge badge-primary">+3.5%</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-3" id="f_deb">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-success text-success">
                                            <!-- <i class="ti-user"></i> -->
                                            <svg id="icon-revenue" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
    											<line x1="12" y1="1" x2="12" y2="23"></line>
    											<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
    										</svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Debit</p>
                                                <h4 class="mb-0" id="debits">0</h4>
                                                    <!-- <span class="badge badge-info">+3.5%</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-3" id="f_kre">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-danger text-danger">
                                            <!-- <i class="ti-user"></i> -->
                                            <svg id="icon-revenue" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
    											<line x1="12" y1="1" x2="12" y2="23"></line>
    											<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
    										</svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Kredit</p>
                                                <h4 class="mb-0" id="kredits">0</h4>
                                                    <!-- <span class="badge badge-success">+3.5%</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-sm-3" id="f_salakh">
                            <div class="widget-stat card">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-warning text-warning">
                                        <!-- <i class="ti-user"></i> -->
                                            <svg id="icon-revenue" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
    											<line x1="12" y1="1" x2="12" y2="23"></line>
    											<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
    										</svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">Saldo Akhir</p>
                                                <h4 class="mb-0" id="saldoakhir">0</h4>
                                                        <!-- <span class="badge badge-warning">+3.5%</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                       
                <form action="{{url('buku_harian_export')}}" method="">
                    
                    <div class="col-lg-12">
                        <div class="card">
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="row">
                                    
                                    
                                        
                                    <div class="mb-3 col-md-3">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <label class="form-label">Pilih</label>
                                            </div>
                                            <div id="hide_rbln" class="col-md-5" style="display: none">
                                                <input type="checkbox" id="rmonth" name="rmonth"> Range Bulan</input>
                                            </div>
                                        </div>
                                        <select class="sel2 form-control cek0" name="plhtgl" id="plhtgl" >
                                            <option value="0" selected>Periode</option>
                                            <option value="1">Bulan</option>
                                            <option value="2">Tahun</option>
                                        </select>
                                        
                                    </div>
                                    
                                    <div class="mb-3 col-md-3" id="hide_tgl" style="display: block">
                                        <label class="form-label">Range Tanggal</label>
                                        <input type="text" class="form-control cek1" autocomplete="off" id="daterange" name="daterange" placeholder="{{date('d-m-Y').' s.d. '.date('d-m-Y')}}" />
                                    </div>
                                    
                                    <div class="mb-3 col-md-3" id="hide_bln" style="display: none">
                                        <label class="form-label" id="l_bln">Bulan dan Tahun</label>
                                        <input type="month" class="form-control cekcok" id="month" name="month" style="width: 100%">
                                    </div>
                                        
                                    <div class="mb-2 col-md-2" id="hide_tobln" style="display: none">
                                        <label class="form-label">Sampai Bulan</label>
                                        <input type="month" class="form-control cekcok2" id="tomonth" name="tomonth" style="width: 100%">
                                    </div>
                                    
                                    
                                    <div class="mb-3 col-md-3" id="hide_thn" style="display: none">
                                        <label class="form-label">Tahun</label>
                                        <input type="text" class="form-control" id="year" name="year" style="width: 100%">
                                    </div>
                                    
                                    <!--<div class="mb-3 col-md-3">-->
                                    <!--    <label class="form-label">Pilih</label>-->
                                    <!--    <select class="sel2 form-control cek0" name="plhtgl" id="plhtgl" >-->
                                    <!--        <option value="0" selected>Periode</option>-->
                                    <!--        <option value="1">Bulan</option>-->
                                    <!--    </select>-->
                                    <!--</div>-->
                                    
                                    <!--<div class="mb-3 col-md-3">-->
                                        
                                    <!--    <div id="hide_tgl" style="display: block">-->
                                    <!--        <label class="form-label">Range Tanggal</label>-->
                                    <!--        <input type="text" class="form-control cek1" autocomplete="off" id="daterange" name="daterange" placeholder="{{date('d-m-Y').' s.d. '.date('d-m-Y')}}" />-->
                                    <!--    </div>-->
                                        
                                    <!--    <div id="hide_bln" style="display: none">-->
                                    <!--        <label class="form-label">Bulan dan Tahun</label>-->
                                    <!--        <input type="month" class="form-control cekcok" id="month" name="month" style="width: 100%">-->
                                    <!--    </div>-->
                                    <!--</div>    -->
                                    
                                    
                                    <div class="mb-3 col-md-3" id='via1'>
                                        <label class="form-label">Input Via</label>
                                        <select class="multi cek9" name="via[]" multiple="multiple" id="multiple" >
                                            <!--<option value="">- Pilih -</option>-->
                                            <option class="other" value="transaksi">Transaksi</option>
                                            <option class="other" value="penerimaan">Penerimaan</option>
                                            <option class="other" value="mutasi">Mutasi</option>
                                            <option class="other" value="pengeluaran">Pengeluaran</option>
                                            <option class="other" value="penyaluran">Penyaluran</option>
                                            <option class="other" value="penyesuaian">Penyesuaian</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-3" id='divbayar'>
                                        <label class="form-label">Pembayaran</label>
                                        <select class="pembayaran" name="pembayaran[]" multiple="multiple" id="pembayaran" >
                                            <option class="other" value="cash">Cash</option>
                                            <option class="other" value="noncash">Non Cash</option>
                                            <option class="other" value="bank">Bank</option>
                                            <option class="other" value="mutasi">Mutasi</option>
                                        </select>
                                    </div>
                                    <!--<div class="mb-3 col-md-3" style="display:none;" id="via2">-->
                                    <!--    <label class="form-label">Input Via</label>-->
                                    <!--    <select class="inputVia cek9" name="inputVia[]" multiple="multiple" id="inputVia">-->
                                    <!--        <option value="transaksi">Transaksi</option>-->
                                    <!--    </select>-->
                                    <!--</div>-->
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Status</label>
                                    <select class="sel2 form-control cek8" name="stts" id="stts">
                                        <option value="">- Pilih -</option>
                                        <option value="1" selected>Approved</option>
                                        <option value="0">Rejected</option>
                                        <option value="2">Pending</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Unit</label>
                                    <select class="sel2 form-control cek7" name="unit" id="unit">
                                        @if(Auth::user()->keuangan == 'admin' || Auth::user()->keuangan == 'keuangan pusat')
                                            <option value="all_kan">- Semua Kantor -</option>
                                        @else
                                            <option value="">- Pilih -</option>
                                        @endif
                                        @foreach($kantor as $ka)
                                        <option value="{{$ka->id}}" {{ Auth::user()->id_kantor == $ka->id ? 'selected' : ''  }}>{{$ka->unit}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Buku</label>
                                    <select class="form-control cek6 buook" name="buku" id="buku">
                                    </select>
                                </div>
                                <div class="mb-3 col-md-3 d-grid">
                                    <label class="form-label">Advance</label>
                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle"  data-bs-toggle="collapse" href="#multiCollapseExample2" role="button" aria-expanded="false" aria-controls="multiCollapseExample2">
                                    Advance
                                    </button>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center ">
                                <div class="bg-collaps rounded mt-4" style="width: 97%;">
                                    <div class="collapse multi-collapse " id="multiCollapseExample2" >
                                        <div class="row">
                                            <div class=" mb-3 col-md-6">
                                                <label class="form-label mt-3">Nominal</label>
                                                <div class="d-flex gap-2">
                                                    <input type="number" class="form-control dari_nominal" name="dari_nominal" id="dari_nominal" placeholder="Dari nominal"/> 
                                                    <input type="number" class="form-control sampai_nominal" name="sampai_nominal" id="sampai_nominal" placeholder="Sampai nominal" /> 
                                                </div>
                                            </div>
                                            <div id="f_view" class="mb-3 col-md-3 mt-3">
                                                <label class="form-label">View</label>
                                                <select class="view_multi cek9" name="view[]" multiple="multiple" id="view_multi">
                                                    <option value="0">Normal</option>
                                                    <option value="1">DP</option>
                                                </select>
                                            </div>
                                            <div class="mb-3 col-md-3 mt-3">
                                                <label class="form-label">Group By</label>
                                                <select class="sel2 form-control groupby" name="groupby" id="groupby">
                                                    <option value="">- Pilih -</option>
                                                    <option value="0">Tanggal</option>
                                                    <option value="1">Bulan</option>
                                                    <option value="2">Tahun</option>
                                                </select>
                                            </div>
                                            
                                            
                                        </div>
                                            
                                        <div class="row">
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Jenis Transaksi</label>
                                                <select class="sel2 form-control jenis_transaksi" name="jenis_transaksi" id="jenis_transaksi">
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label">Program</label>
                                                <select class="sel2 form-control program" name="prog" id="program">
                                                </select>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label ">User Insert</label>
                                                <select class="sel2 form-control user_insert" name="user_insert" id="user_insert">
                                                        <option value="">- Pilih -</option>
                                                    @foreach($user_insert as $val)
                                                        <option value="{{ $val->id }}"> {{ $val->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label class="form-label ">User Approve</label>
                                                <select class="sel2 form-control user_approve" name="user_approve" id="user_approve">
                                                         <option value="">- Pilih -</option>
                                                    @foreach($user_approve as $val)
                                                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3 col-md-3">
                                                <label class="form-label ">Backdate</label>
                                                <select class="sel2 form-control backdate" name="backdate" id="backdate">
                                                    <option value="">- Pilih -</option>
                                                    <option value="0">Ya</option>
                                                    <option value="1">No</option>
                                                </select>
                                            </div>

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
                        <h4 class="card-title">Buku <span id="ohoh"></span></h4>
                        <div class=" col-md-2 d-grid">
                            <div class="btn-group">
                                 <!--data-bs-toggle="dropdown" aria-expanded="false" -->
                                 <button type="button" class="btn btn-primary btn-sm dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ekspor
                                </button>
                                <ul class="dropdown-menu">
                                    <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>
                                    <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>
                                    <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                </ul>
                            </div>
                        </div>
                        <!--<div class="pull-right">-->
                        <!--    <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Cari Data...">-->
                        <!--</div>-->
                    </div>
                    
                    <div class="card-body">
                                <div class="table-responsive">
                                    <table id="user_table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th >Tanggal</th>
                                                <th >COA</th>
                                                <th >Jenis Transaksi</th>
                                                <th >Keterangan</th>
                                                <th >Debet</th>
                                                <th >Kredit</th>
                                                <th >Saldo</th>
                                                <th >ID Transaksi</th>
                                            </tr>
                                            <tr id="barsal" >
                                                <td colspan="3"></td>
                                                <!--<td></td>-->
                                                <!--<td></td>-->
                                                <td colspan="2">Saldo Awal :</td>
                                                <td colspan="2"></td>
                                                <!--<td></td>-->
                                                <!--<td></td>-->
                                                <td colspan="2" id="salwal"></td>
                                                <!--<td></td>-->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr><th></th>
                                                <th></th>
                                                <th></th>
                                                <th>Total :</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 </form>
              
            <!--</div>-->
        </div>
        
    </div>
</div>
@endsection