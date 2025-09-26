@extends('template')

@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Report Management</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Analisis Transaksi</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- Modal -->
        <div class="modal fade" id="modalkacab">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Target Bulan Ini</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                      
                    </button>
                  </div>
                  <div class="modal-body">
                   <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                          <th>Nama Karyawan</th>
                          <th>Total Kolekting</th>
                          <th>Bulan</th>
                        </tr>
                        </thead>
                        <tbody id="tab_target">
                         
                        </tbody>
                        <tfoot>
                      
                        </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="modal fade" id="modaltarget">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Target Anda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                      
                    </button>
                  </div>
                  <div class="modal-body">
                    <form method="post" action="{{url('targetkacc')}}" >
                      @csrf
                          <div class="form">
                                <label for="name">Target Bulan Ini</label>
                                <input type="text" name="targetkac" id="targetkac" class="form-control" aria-describedby="name" placeholder="Contoh: 100.000.000" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                          </div>
                          
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                  </div>
                </form>
                </div>
              </div>
            </div>
            <!--</div>-->
            
        <div class="modal fade" id="detail_kunjungan">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Kunjungan Donatur <span id="mmy"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <!--<form>-->
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>ID Transaksi</th>
                                            <th>Petugas</th>
                                            <!--<th>Donatur</th>-->
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody id="piw1">
    
                                    </tbody>
                                    <tfoot id="piw2">
                                        
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    <!--</form>-->
                </div>
            </div>
        </div>
        
        <!-- End Modal -->
        <form method="GET" action="{{ url('analisis-transaksi/ekspor') }}" >
             <input type="hidden" class="form-control" name="idTai" id="idTai">
             <input type="hidden" class="form-control" name="kondisi" id="kondisi">
            <div class="modal fade" id="modalwar">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Rincian <span id="nana"></span></h5>
                                    <div class="btn-group">
                                      <button type="button" id="exp" class="btn btn-primary ms-2 btn-xs dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        Ekspor
                                      </button>
                                      <ul class="dropdown-menu">
                                        <li><button class="dropdown-item" type="submit" value="xlss" name="tombol">.XLS</button></li>
                                        <li><button class="dropdown-item" type="submit" value="csvv" name="tombol">.CSV</button></li>
                                      </ul>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    </button>
                                </div>
                                <!--<form>-->
                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="oyyo">
                                                <thead id="diva">
                                                    
                                                </thead>
                                                <tbody id="div1">
                
                                                </tbody>
                                                <tfoot id="divdiv">
                                                    
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                <!--</form>-->
                            </div>
                        </div>
                    </div>
                    
                    
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="basic-form">
                                    <div class="row">
                                        <div class="col-md-2 mb-3">
                                            <div class="form-group">
                                                <label>Analisis dengan <span id="jdl"></span></label>
                                                <select required class="form-control kondisi cek1" name="analis" id="analis" style="width: 100%">
                                                    <option value="" disable selected="selected">Pilih Analisis</option>
                                                    <option value="bank">Bank</option>
                                                    <!--<option value="bulan">Bulan Transaksi</option>-->
                                                    <option value="jam">Jam Transaksi</option>
                                                    <option value="tanggal">Tanggal Transaksi</option>
                                                    <option value="bulan">Bulan Transaksi</option>
                                                    <option value="tahun">Tahun Transaksi</option>
                                                    <option value="kantor" {{ Auth::user()->level == 'admin' ? "selected" : "" }}>Kantor</option>
                                                    <option value="donatur">Donatur</option>
                                                    <option value="program">Program</option>
                                                    <option value="petugas" {{ Auth::user()->level == 'kacab' ? "selected" : "" }}>Petugas</option>
                                                    <option value="status">Status Transaksi</option>
                                                    <option value="user">User Insert</option>
                                                    <option value="bayar">Via Bayar</option>
                                                    <!--<option value="bayar">Jabatan</option>-->
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <div class="form-group">
                                                <label>Status Approval</label>
                                                <select required class="form-control  kondisi cek11" name="approv" id="approv" style="width: 100%">
                                                    <option value="">Pilih Status Approval</option>
                                                    <option value="2">Pending</option>
                                                    <option value="1" selected>Approved</option>
                                                    <option value="3">Rejected</option>
                                                    <option value="0">All</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label>&nbsp;</label>
                                            <select id="plhtgl" class="form-control  kondisi" name="plhtgl">
                                                <option value="0">Periode</option>
                                                <option value="1">Bulan</option>
                                                <option value="2">Tahun</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3 mb-3" id="tanggal_hide">
                                            <label>&nbsp;</label>
                                            <input type="text" name="daterange" class="form-control datess ceks  kondisi" id="daterange" placeholder="{{date('d-m-Y').' s.d. '.date('d-m-Y')}}" autocomplete="off">
                                        </div>
                                        <input type="hidden" value="{{date('d-m-Y').' s.d. '.date('d-m-Y')}}" id="texttgl" name="texttgl">
                                        <input type="hidden" value="{{date('m-Y') }}" id="textbln" name="textbln">

                                        <div class="col-md-3 mb-3" id="bulan_hide" hidden>
                                            <label>Dari :</label>
                                            <input type="text" class="form-control goa cek9 kondisi" name="bulan" id="bulan" autocomplete="off" placeholder="{{date('m-Y') }}">
                                        </div>
                                        
                                        <div class="col-md-3 mb-3" id="bulan_hide2" hidden>
                                            <label>Sampai :</label>
                                            <input type="text" class="form-control goa2 cek92 kondisi" name="bulan2" id="bulan2" autocomplete="off" >
                                        </div>

                                        <div class="col-md-3 mb-3" hidden id="tahun_hide">
                                            <label>Dari :</label>
                                            <div class="form-group">
                                                <input type="text" class="form-control year cek2 kondisi" name="tahun" id="tahun" autocomplete="off" placeholder="{{date('Y') }}">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 mb-3" hidden id="tahun_hide2">
                                            <label>Sampai :</label>
                                            <div class="form-group">
                                                <input type="text" class="form-control year2 cek22 kondisi" name="tahun2" id="tahun2" autocomplete="off" >
                                            </div>
                                        </div>
                                        
                                        <?php
                                        
                                        if(Auth::user()->level == 'spv'){
                                            $k = null;
                                        }else{
                                            $k = App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->first();
                                            
                                        }
                                        
                                        if(Auth::user()->level == 'admin'){
                                            $datdon = App\Models\Kantor::where('id_com', Auth::user()->id_com)->get();
                                        }else{
                                            // if($k != null){
                                              $datdon =  App\Models\Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->select('unit', 'id')->get();
                                            // }
                                        }
                                        ?>
                                        
                                        <div class="col-md-2 md-3">
                                            <label class="form-label">Unit</label> 
                                            <select id="kotal" class="cekss multi kondisi" style="width:100%" name="kotal[]" multiple="multiple">
                                                
                                                    @foreach ($datdon as $item)
                                                    <option value="{{$item->id}}">{{$item->unit}}</option>
                                                    @endforeach
                                                
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-2 md-3" id="ptgs_hide">
                                            <label class="form-label">Petugas</label> 
                                            <select id="petugas" class="form-control multix" style="width:100%" name="petugas">
                                                <option value="">Pilih Petugas</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-2 md-3">
                                            <label class="form-label">Pembayaran</label> 
                                            <select id="bay" class="form-control multis cekuu kondisi" name="bay[]" multiple="multiple">
                                                <!--<option value="" selected>Semua</option>-->
                                                <!--<option value="cash">Cash</option>-->
                                                <!--<option value="noncash">Non Cash</option>-->
                                                @foreach($pem as $p)
                                                <option value="{{$p->pembayaran}}">{{ Ucfirst($p->pembayaran) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="hidden" id="toggleVal" name="toggleData">
                                        <div class="col-md-2 md-3">
                                            <label>&nbsp;</label>
                                            <div class="btn-group">
                                              <button type="button" id="exp" class="btn btn-primary btn-sm dropdown-toggle mt-4" data-bs-toggle="dropdown" aria-expanded="false">
                                                Ekspor
                                              </button>
                                              <ul class="dropdown-menu">
                                                <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>
                                                <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>
                                                <li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>
                                              </ul>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if(Auth::user()->kolekting == 'kacab')
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="pull-right" style="margin-left: 10px">
                                        <!-- pake class uwuq /// data-bs-toggle="modal" data-bs-target="#modaltarget" href="javascript:void(0)"-->
                                        <label class="badge badge-success badge-xxs mb-3 mt-3" id="tot_target"></label>
                                        <span class="badge badge-primary badge-xxs mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#modalkacab" href="javascript:void(0)" id="sum"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-12" >
                        <div clas="row">
                            <div class="col-lg-12" id="dasst">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="title-header">Data Analis <span id="galer"></span></h4>
                                        <div class="pull-right">
                                            <select id="sang" style="display: block;" class="cek88 form-control wide" name="sang" >
                                                <option value="nominal" selected>% Nominal</option>
                                                <option value="donatur">% Donatur</option>
                                                <option value="transaksi">% Transaksi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch ">
                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" style="height : 20px; width : 40px;">
                                            <label for="flexSwitchCheckChecked" class="mt-1 ms-1">Show/Hide Column Nontransaksi dan Donatur tanpa transaksi</label>
                                        </div>
                                        <div class="table-responsive22">
                                            <table id="user_table" class="table table-striped " style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th id="uwe"></th>
                                                        <th class="cari" data-toggle="tooltip" data-placement="top" title="Tooltip">∑ Nominal</th>
                                                        <th class="cari" data-toggle="tooltip" data-placement="top" title="Tooltip">∑ Transaksi</th>
                                                        <th class="cari" data-toggle="tooltip" data-placement="top" title="Tooltip">∑ Non Transaksi</th>
                                                        <th class="cari" data-toggle="tooltip" data-placement="top" title="Tooltip">∑ Donatur</th>
                                                        <th class="cari" data-toggle="tooltip" data-placement="top" title="Tooltip">∑ Donatur Tanpa Transaksi</th>
                                                        <th class="cari" data-toggle="tooltip" data-placement="top" title="Tooltip">
                                                            <div id="cari_persen"></div>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td>Σ Total</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(Auth::user()->level == 'admin')
                            <div class="col-lg-12" id="donkan" style="display: none">
                                <div class="card card-danger">
                                    <div class="card-header with-border">
                                        <h3 class="card-title">Data Donatur Per kantor</h3>
                                        <div class="pull-right">
                                            <select id="seng" style="display: block;" class="cek99 form-control wide" name="seng">
                                                <option value="ak" selected>% Aktif</option>
                                                <option value="non">% Nonaktif</option>
                                                <option value="tran">% Bertransaksi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="table-responsive">
                                                    <table id="user_table2" class="table table-striped ">
                                                        <thead>
                                                            <tr>
                                                                <th>Kantor</th>
                                                                <th>∑ Aktif</th>
                                                                <th>∑ Nonaktif</th>
                                                                <th>∑ Betransaksi</th>
                                                                <th>
                                                                    <div id="cardonper"></div>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td>Σ Total</td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    </div>
                    
                    </form>
                    <div class="row">
                        <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12" id="sss">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Grafik Analis</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <figure class="highcharts-figure">
                                                    <div id="container" style="height: 450px; min-width: 310px; display: block"></div>
                                                </figure>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if(Auth::user()->level == 'admin')
                            <div class="col-lg-12" id="donkun" style="display: none">
                                <div class="card card-danger">
                                    <!--<div class="card-header with-border">-->
                                    <!--    <h3 class="card-title">Data Donatur Per Kunjungan</h3>-->
                                    <!--    <div class="pull-right">-->
                                    <!--        <select id="pembay" class="cek909 form-control " name="pembay">-->
                                    <!--            <option value="" selected>All</option>-->
                                    <!--            <option value="dijemput">Dijemput</option>-->
                                    <!--            <option value="transfer">Transfer</option>-->
                                    <!--        </select>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="table-responsive">
                                                    <table id="user_table3" class="table table-striped ">
                                                        <thead>
                                                            <tr>
                                                                <th>Donatur</th>
                                                                <th>Donasi</th>
                                                                <th>Tidak Donasi</th>
                                                                <th>Tutup</th>
                                                                <th>Tutup 2x</th>
                                                                <th>Ditarik</th>
                                                                <th>Kotak Hilang</th>
                                                                <th>Total</th>
                                                                <!--<th>-->
                                                                <!--    <div id="cardonper"></div>-->
                                                                <!--</th>-->
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
                            @endif
                        </div>
                    </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection