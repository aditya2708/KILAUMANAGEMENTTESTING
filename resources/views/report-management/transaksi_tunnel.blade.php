@extends('template')

@section('konten')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            
            <!-- Modal -->
            <div class="modal fade" id="modalin">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="nana"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <!--<form>-->
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="samplex">
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
            
            <div class="modal fade" id="modalineco">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="nanaeco"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <!--<form>-->
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="samplexeco">
                                        <thead id="divaeco">
                                            
                                        </thead>
                                        <tbody id="div1eco">
    
                                        </tbody>
                                        <tfoot id="divdiveco">
                                            
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
            <!--End Modal-->
            
            <div class="col-lg-12">
                <div class="row">

                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Grafik Transaksi Funnel</h4>
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
                        </div>
                    </div>
                    
                    <div class="col-lg-4" style="height: 50%">
                        <div class="card">
                            <div class="card-body">
                                <div class="basic-form">
                                    
                                    <div class="row">
                                        
                                        <!--<div class="col-md-2 mb-3">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label>Status Approval</label>-->
                                        <!--        <select required class="form-control  cek11" name="approv" id="approv" style="width: 100%">-->
                                        <!--            <option value="">Pilih Status Approval</option>-->
                                        <!--            <option value="2">Pending</option>-->
                                        <!--            <option value="1" selected>Approved</option>-->
                                        <!--            <option value="3">Rejected</option>-->
                                        <!--            <option value="0">All</option>-->
                                        <!--        </select>-->
                                        <!--    </div>-->
                                        <!--</div>-->

                                        <div class="col-md-6 mb-3">
                                            <select id="plhtgl" class="form-control " name="plhtgl">
                                                <option value="0">Periode</option>
                                                <option value="1">Bulan</option>
                                                <option value="2">Tahun</option>
                                            </select>
                                        </div>
                                        
                                        <input type="hidden" value="{{date('d-m-Y').' s.d. '.date('d-m-Y')}}" id="txttgl">
                                        <input type="hidden" value="{{date('m-Y') }}" id="txtbln">
                                        <input type="hidden" value="{{date('Y') }}" id="txtthn">

                                        <div class="col-md-12 mb-3" id="tanggal_hide">
                                            <label>Tanggal</label>
                                            <input type="text" name="daterange" class="form-control datess ceks " id="daterange" placeholder="{{date('d-m-Y').' s.d. '.date('d-m-Y')}}" autocomplete="off">
                                        </div>

                                        <div class="col-md-6 mb-3" id="bulan_hide" hidden>
                                            <!--<label>Bulan :</label>-->
                                            <input type="text" class="form-control month cek3" name="bulan" id="bulan" autocomplete="off" placeholder="{{date('m-Y') }}">
                                        </div>

                                        <div class="col-md-6 mb-3" hidden id="tahun_hide">
                                            <!--<label>Tahun :</label>-->
                                            <!--<div class="form-group">-->
                                                <input type="text" class="form-control year cek4" name="tahun" id="tahun" autocomplete="off" placeholder="{{date('Y') }}">
                                            <!--</div>-->
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label>Sumber Dana</label>
                                            <select id="sumber" class="cek1 multi" style="width:100%" name="sumber">
                                                <option value="">Pilih</option>
                                                @foreach ($sumber as $item)
                                                <option value="{{$item->id_sumber_dana}}" {{$item->id_sumber_dana == 1 ? 'selected' : ''}}>{{$item->sumber_dana}}</option>
                                                @endforeach
                                            </select>
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
                                            if($k != null){
                                              $datdon =  App\Models\Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->select('unit', 'id')->get();
                                            }else{
                                                $datdon =  App\Models\Kantor::where('id', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->select('unit', 'id')->get();
                                            }
                                        }
                                        ?>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label>Kantor</label>
                                            <select id="kotal" class="cek2 multi" style="width:100%" name="kotal[]" multiple="multiple">
                                                @foreach ($datdon as $item)
                                                <option value="{{$item->id}}">{{$item->unit}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <!--<div class="col-md-2 md-3">-->
                                        <!--    <label class="form-label">Pembayaran</label> -->
                                        <!--    <select id="bay" class="form-control cekuu" name="bay">-->
                                        <!--        <option value="" selected>Semua</option>-->
                                        <!--        <option value="cash">Cash</option>-->
                                        <!--        <option value="noncash">Non Cash</option>-->
                                        <!--    </select>-->
                                        <!--</div>-->
                                        
                                        <!--<div class="col-md-2 md-3">-->
                                        <!--    <label>&nbsp;</label>-->
                                        <!--    <div class="btn-group">-->
                                        <!--      <button type="button" class="btn btn-primary btn-sm dropdown-toggle mt-4" data-bs-toggle="dropdown" aria-expanded="false">-->
                                        <!--        Ekspor-->
                                        <!--      </button>-->
                                        <!--      <ul class="dropdown-menu">-->
                                        <!--        <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>-->
                                        <!--        <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>-->
                                        <!--        <li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                        <!--      </ul>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
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