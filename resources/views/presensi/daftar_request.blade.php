@extends('template')

@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!-- Modal -->
        <div class="modal fade" id="rincian">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Rincian Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form method="post" id="upload_form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div id="getoh">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" id="mem">

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal -->
        
        <?php
        $k = App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->first();
        if ($k != null) {
            $datdon = App\Models\Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->select('unit', 'id')->get();
        }
        $kota = App\Models\Kantor::where('id_com', Auth::user()->id_com)->get();
        ?>

        <div class="row">
            <div class="col-lg-12">
                    <form method="GET" action="{{url('daftar-request/exportdr')}}">
                <div class="card">
                    <div class="card-header justify-content-end gap-2">
                     @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                        <div class="">
                            <button type="button" class="btn btn-primary btn-block btn-xxs " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                        </div>
                    @endif
                    <div class="btn-group">
                         <button type="button" class=" btn btn-success btn-xxs dropdown-toggle exp" data-bs-toggle="dropdown" aria-expanded="false" title="Ekspor Data Laporan" style="width:100%;">
                            <i class="fa fa-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item exp" type="submit" value="xls" name="tombol">.XLS</button></li>
                            <li><button class="dropdown-item exp" type="submit" value="csv" name="tombol">.CSV</button></li>
                            <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                        </ul>
                    </div>
                </div>

                    <div class="card-body">
                        
                        <div class="basic-form mb-4">
                            
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        
                                        <input type="hidden" id="idCom" name="com"/>
                                        <div class="col-lg-3 mb-3">
                                            <label>Range Tanggal</label>
                                            <input type="text" name="daterange" class="form-control datess ceks" id="daterange" placeholder="mm/dd/yyyy - mm/dd/yyyy" autocomplete="off" value="" />
                                        </div>
                                        
                                        
                                        <div class="col-lg-3 mb-3">
                                            <label>Unit</label>
                                            <select class="form-control ululu cek5" id="unit" name="unit">
                                                @if(count($kota) > 0)
                                                <option value="">Pilih Unit</option>
                                                @foreach ($kota as $item)
                                                <option value="{{$item->id}}">{{$item->unit}}</option>
                                                @endforeach
                                                @else
                                                <option value="">Tidak ada</option>
                                                @endif
                                            </select>
                                        </div>
                                        
                                        <div class="col-lg-3 mb-3">
                                            <label>Jabatan</label>
                                            <select class="form-control ululu cek6" name="jabat" id="jabat" >
                                                 @if(count($jabatan) > 0)
                                                <option value="">Pilih Jabatan</option>
                                                @foreach($jabatan as $j)
                                                <option value="{{$j->id}}">{{$j->jabatan}}</option>
                                                @endforeach
                                                @else
                                                <option value="">Tidak ada</option>
                                                @endif
                                            </select>
                                        </div>
                                        
                                        
                                        <div class="col-lg-3 mb-3">
                                            <label>Status</label>
                                            <select class="form-control ululu cek7" name="status" id="status" >
                                                <option value="">Pilih Status</option>
                                                @foreach($status as $s)
                                                <option value="{{$s->status}}">{{$s->status}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="col-lg-3 mb-3">
                                            <label>Keterangan</label>
                                            <select class="form-control cek8" name="kett" id="kett" >
                                                <option value="">Pilih Keterangan</option>
                                                <option value="1">Acc</option>
                                                <option value="0">Pending</option>
                                            </select>
                                        </div>
                                        
                                        @if(Auth::user()->kepegawaian != null)
                                        <div class="col-lg-3 mb-3">
                                            <label>Reject / Acc</label>
                                            <select class="form-control kol" name="accx" id="accx" >
                                                <option value="">Pilih Reject / Acc</option>
                                                <option value="reject">Reject</option>
                                                <option value="acc">Acc</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-lg-3 mt-4  mb-3" style="display: none" id="one">
                                            <button type="button" class="btn btn-success btn-sm w-100" id="accalll"><span class=" btn-icon-start text-success"><i class="fa fa-check-double color-success"></i></span>Approve All</button>
                                        </div>
                                        
                                        <div class="col-lg-3 mt-4  mb-3" style="display: none" id="two">
                                            <button type="button" class="btn btn-success btn-sm  w-100" id="fire"><span class=" btn-icon-start text-success"><i class="fa fa-check-double color-success"></i></span>Approve Selected</button>
                                        </div>
                                        
                                        <div class="col-lg-3 mt-4 mb-3" style="display: none" id="three">
                                             <button type="button" class="btn btn-danger btn-sm w-100" id="rejectall"><span class=" btn-icon-start text-danger"><i class="fa fa-check-double color-success"></i></span>Reject All</button>
                                        </div>
                                        
                                        <div class="col-lg-3 mt-4 mb-3" style="display: none" id="four">
                                            <button type="button" class="btn btn-danger btn-sm  w-100" id="fire2"><span class=" btn-icon-start text-danger"><i class="fa fa-check-double color-success"></i></span>Reject Selected</button>
                                        </div>
                                        
                                        
                                        <!--<div class="col-sm-2 mb-3" style="display: block" >-->
                                                <!--<label>Ekspor Data</label>-->
                                        <!--        <button type="submit" class="btn btn-primary light" id="export"  style="width: 100%">Export</button>-->
                                        <!--</div>-->
                                        @endif
                                    
                                        
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        </form>
                        
                        <div class="table-responsive">
                            <table id="user_table" class="table  table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th hidden></th>
                                        <!--<th></th>-->
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <!--<th>Hubungi</th>-->
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