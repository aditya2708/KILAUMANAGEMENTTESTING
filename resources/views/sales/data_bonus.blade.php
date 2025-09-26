@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Sales</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Data Bonus Sales</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- Modal -->
        <div class="modal fade" id="modalbonus">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Bonus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Donatur</th>
                                    <th>Program</th>
                                    <th>Omset</th>
                                    <th>Minimal Poin</th>
                                    <th>Poin</th>
                                    <th>Honor Poin</th>
                                    <th>Bonus Poin</th>
                                    <th>Bonus Omset</th>
                                </tr>
                            </thead>
                            <tbody id="div1">

                            </tbody>
                        </table>
                    </div>
                    <!--<div class="modal-footer">-->
                    <!--    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Tutup</button>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
        
        
        <div class="modal fade" id="yahhha" >
            <div class="modal-dialog modal-dialog-centered modal-xl" style="overflow-y: initial !important">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Data Bonus <span id="petugas"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="kodikit" enctype="multipart/form-data">
                        <div class="modal-body" style="height: 450px;overflow-y: auto;">
                            <div class="basic-form" id="bod">
                                
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div id="fot">
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal -->
        <?php
        $k = App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        if ($k != null) {
            $datdon = App\Models\Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->select('unit', 'id')->get();
        }
        $kota = App\Models\Kantor::all();
        ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Data Bonus</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3" id="blnbln">
                                <label>Bulan&Tahun :</label>
                        	    <input type="text" class="form-control daterange cek4 mb-4" name="blns" id="blns" autocomplete="off" placeholder="contoh {{date('m-Y') }}">
                            </div>
                            
                            @if(Auth::user()->level == 'kacab')
                                @if($k != null)
                                <div class="col-md-3 mb-3" id="kotas" style="display:block;">
                                    <label>Unit</label>
                                    <select class="form-control cek5" id="unit" name="unit">
                                        <option value="">- Pilih Kota -</option>
                                        @foreach ($datdon as $item)
                                        <option value="{{$item->id}}">{{$item->unit}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                @endif
                                
                                @if(Auth::user()->level == 'admin')
                                <div class="col-md-3 mb-3">
                                    <label>Unit</label>
                                    <select class="form-control cek5" id="unit" name="unit">
                                        <option value="">- Pilih Kota -</option>
                                        @foreach ($kota as $item)
                                        <option value="{{$item->id}}">{{$item->unit}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                
                                <div class="col-lg-3 mb-3">
                                    <label>Jabatan</label>
                                    <select class="form-control cek6" name="jabat" id="jabat" >
                                        <option value="">Pilih Jabatan</option>
                                        @foreach($jabatan as $j)
                                        <option value="{{$j->id}}">{{$j->jabatan}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        <div class="table-responsive">
                            <table id="user_table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Petugas</th>
                                        <th>Jabatan</th>
                                        <th>Kantor</th>
                                        <!--<th>Total Honor</th>-->
                                        <!--<th>Total Bonus Capaian</th>-->
                                        <th>Bonus Poin</th>
                                        <th>Bonus Omset</th>
                                        <th>Honor Poin</th>
                                        <th>Poin</th>
                                        <th>Total Bonus</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" style="text-align:center; font-size: 12px"><b>Total :</b></th>
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

    </div>
</div>
@endsection