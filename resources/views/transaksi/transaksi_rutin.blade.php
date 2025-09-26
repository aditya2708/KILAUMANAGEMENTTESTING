@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="GET" action="{{ url('transaksi-rutin/ekspor') }}" >
                            <div class="row">
                                <!--<div class="col-md-3 md-3">-->
                                <!--    <label class="form-label">Bulan</label> -->
                                <!--    <select id="coba" class="cek0 multi" style="width:100%" name="coba[]" multiple="multiple">-->
                                <!--        @for ($i = 1; $i <= 12; $i++)-->
                                <!--        <option value="{{$i}}">{{$i}}</option>-->
                                <!--        @endfor-->
                                <!--    </select>-->
                                <!--</div>-->
                                
                                
                                <div class="col-md-3 mb-3">
                                    
                                    <!--<label> -->
                                    <!--    <select id="periods" name="periods" style="outline: none; border: none;">-->
                                    <!--        <option value="bln">Bulan</option>-->
                                    <!--        <option value="thn" selected>Tahun</option>-->
                                    <!--    </select>-->
                                    <!--</label>-->
                                    
                                    <!--<div class="row" >-->
                                    <!--    <div class="col-lg-3" id="tu" style="display: block">-->
                                    <!--        <input type="text" class="form-control month cek1" name="bulan" id="bulan" autocomplete="off" placeholder="{{date('Y') }}">-->
                                    <!--    </div>-->
                                        
                                        
                                    <!--</div>-->
                                    
                                    <label>Bulan</label>
                                    
                                    <?php $bul = ['1'=> 'januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus' , 'September', 'Oktober', 'November', 'Desember'] ?>
                                    
                                    
                                    <select class="multi cek11" name="bln[]" multiple="multiple" id="bln" >
                                        @for($i = 1; $i <= count($bul); $i++)
                                        <option value="{{$i}}">{{$bul[$i]}}</option>
                                        @endfor
                                    </select>
                                    
                                </div>
                                
                                <div class="col-md-2 mb-3">
                                    <label>Tahun</label>
                                    <input type="text" class="form-control month cek1" name="bulan" id="bulan" autocomplete="off" placeholder="{{date('Y') }}">
                                </div>
                                        
                                <!--    </div>-->
                                    <!--<input type="month" class="form-control cek11" name="bln" id="bln" autocomplete="off"  style="display: none">-->
                                <!--</div>-->
                                
                                
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
                                        
                                <div class="col-md-3 md-3">
                                    <label class="form-label">Unit</label> 
                                    <select id="kota" class="cek2 multi" style="width:100%" name="kota[]" multiple="multiple">
                                        @foreach ($datdon as $item)
                                        <option value="{{$item->id}}">{{$item->unit}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3 md-3">
                                    <label class="form-label">Program</label> 
                                    <select id="program" class="crot cek3" style="width:100%" name="program">
                                        <option value=""></option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2 md-3">
                                    <label>&nbsp;</label>
                                    <div class="btn-group">
                                         <button type="button" class="btn btn-primary btn-sm dropdown-toggle mt-4" data-bs-toggle="dropdown" aria-expanded="false">
                                            Ekspor
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>
                                            <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>
                                            <!--<li><button class="dropdown-item" type="submit" value="pdf" name="tombol">.PDF</button></li>-->
                                        </ul>
                                    </div>
                                </div>
                                
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!--modal-->
            
            <div class="modal fade" id="modalwar">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="nono"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <!--<form>-->
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="samplex">
                                        <thead id="head">
                                            
                                        </thead>
                                        <tbody id="body">
        
                                        </tbody>
                                        <tfoot id="foot">
                                            
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
            
            <!--end modal-->
            
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Data Transaksi Rutin</h4>
                        <!--<div class="pull-right">-->
                        <!--    <h3 style="float:right; margin-top:0px"><label class="badge badge-xl badge-info totaltr"></label></h3>-->
                        <!--    <button type="button" class="btn btn-success btn-sm" style=" margin-right: 20px;" id="acc_all"><span class="btn-icon-start text-success"><i class="fa fa-check-double color-success"></i></span>Approve All</button>-->
                        <!--    <a href="javascript:void(0)" class="btn btn-primary btn-sm light filtt  mt-9" style="float:right; margin-right:15px">Adv Search</a>-->
                        <!--</div>-->
                    </div>
                    <div class="card-body">
                            <!--<input type="hidden" id="advsrc" value="tutup">-->
                        <div class="table-responsive">
                            <table id="user_table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Donatur</th>
                                        <th>Total</th>
                                        
                                        <th>Jan</th>
                                        <th>Feb</th>
                                        <th>Mar</th>
                                        <th>Apr</th>
                                        <th>Mei</th>
                                        <th>Jun</th>
                                        <th>Jul</th>
                                        <th>Ags</th>
                                        <th>Sep</th>
                                        <th>Okt</th>
                                        <th>Nov</th>
                                        <th>Des</th>
                                        <th hidden></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th style="font-size: 12px"></th>
                                        <th style="font-size: 12px"></th>
                                        <th style="font-size: 12px"></th>
                                        
                                        <th style="font-size: 12px"></th>
                                        <th style="font-size: 12px"></th>
                                        <th style="font-size: 12px"></th>
                                        <th style="font-size: 12px"></th>
                                        <th style="font-size: 12px"></th>
                                        <th style="font-size: 12px"></th>
                                        <th style="font-size: 12px"></th>
                                        <th style="font-size: 12px"></th>
                                        <th style="font-size: 12px"></th>
                                        <th style="font-size: 12px"></th>
                                        <th style="font-size: 12px"></th>
                                        <th style="font-size: 12px"></th>
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
</div>
@endsection