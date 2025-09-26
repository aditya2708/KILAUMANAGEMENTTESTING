@extends('template')

@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
    <!--<div class="row" style="padding-top:70px">-->
    <!--    <div class="col-md-12">-->
    <!--        <div id="map" style="width: 100%; height: 400px"></div>-->
    <!--    </div>-->
    <!--</div>-->
    
    <div class="modal fade" id="markerModal" tabindex="-1" aria-labelledby="markerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span id="markerModalLabel"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="markerData"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                
                
                <div class="modal fade" id="modaldetail">
                  <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail <span id="uyuh"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                          
                        </button>
                      </div>
                      <div class="modal-body">
                          <div class="table-responsive">
                              
                           <table class="table table-striped" id="bitt">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Donatur</th>
                                    <th>Alamat</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody id="hehh">
                                 
                                </tbody>
                                <tfoot>
                              
                                </tfoot>
                            </table>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                           <div class="basic-form">
                               <div class="row">
                                    <?php
                                        
                                        if(Auth::user()->level == 'spv'){
                                            $k = null;
                                        }else{
                                            $k = App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->first();
                                            
                                        }
                                        
                                        if(Auth::user()->level == 'admin'){
                                                $datdon = App\Models\Kantor::where('id_com', Auth::user()->id_com)->get();
                                                $thn = App\Models\Donatur::selectRaw("YEAR(created_at) as tahun")->distinct()->get();
                                        }else{
                                            if($k != null){
                                                $datdon =  App\Models\Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->select('unit', 'id')->get();
                                                $thn = App\Models\Donatur::selectRaw("YEAR(created_at) as tahun")->where('id_kantor', Auth::user()->id_kantor)->distinct()->get();
                                            }else{
                                                $datdon =  App\Models\Kantor::where('id', Auth::user()->id_kantor)->where('id_com', Auth::user()->id_com)->select('unit', 'id')->get();
                                                $thn = App\Models\Donatur::selectRaw("YEAR(created_at) as tahun")->where('id_kantor', Auth::user()->id_kantor)->distinct()->get();
                                            }
                                        }
                                        ?>
                                   <div class="col-md-2 mb-3">
                                        <label>Tahun</label>
                                        <div class="form-group">
                                            <!--<input type="text" class="form-control cek1 multi" name="tahun" id="tahun" autocomplete="off">-->
                                            <select id="tahun" class="cek1 multi" style="width:100%" name="tahun[]" multiple="multiple">
                                               @foreach ($thn as $th)
                                                    <option value="{{$th->tahun}}">{{$th->tahun}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="col-md-2 mb-3">
                                        <label>Unit</label>
                                        <div class="form-group">
                                            <select id="kotal" class="cekss multi" style="width:100%" name="kotal[]" multiple="multiple">
                                                @foreach ($datdon as $item)
                                                    <option value="{{$item->id}}">{{$item->unit}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 mb-3">
                                        <label>Jalur</label>
                                        <div class="form-group">
                                            <select id="jalur" class="cekcok multi" style="width:100%" name="jalur[]" multiple="multiple">
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 mb-3">
                                        <label for="lmt">Jumlah Donatur</label>
                                        <div class="form-group">
                                            <input name="lmt" id="lmt" class="form-control cek44" type="text" placeholder="100">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 mb-3">
                                        <label for="lmt">Donatur AKtif</label>
                                        <div class="form-group">
                                            <select class="form-control cek55" name="aktif" id="aktif">
                                                <option value="">Semua</option>
                                                <option value="1">Aktif</option>
                                                <option value="0">Nonaktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                               </div>
                               <div class="row">
                                    <div class="col-md-6 mb-3">
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="ceki" id="ceki">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Tampilkan hanya donatur yang memiliki Koordinat
                                            </label>
                                        </div>
                                        <!--<div class="form-group">-->
                                        <!--    <select class="form-control ceka5" name="aktif" id="aktif">-->
                                        <!--        <option value="">Semua</option>-->
                                        <!--        <option value="1">Ada</option>-->
                                        <!--        <option value="0">kosong</option>-->
                                        <!--    </select>-->
                                        <!--</div>-->
                                    </div>
                               </div>
                           </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <!--<h6 class="title-header">-->
                                <!--<div class="row">-->
                                <!--    <div class="col-md-3"><a href="javascript:void(0)" id="don">Donatur : <span id="kon"></span></a></div>-->
                                <!--    <div class="col-md-3"><a href="javascript:void(0)" id="ada">Ada Koordinat : <span id="ak"></span></a></div>-->
                                <!--    <div class="col-md-3"><a href="javascript:void(0)" id="gada">Tidak ada koordinat : <span id="tak"></span></a></div>-->
                                <!--    <div class="col-md-3"><a href="javascript:void(0)" id="him">Penghimpunan : <span id="peng"></span></a></div>-->
                                <!--</div>-->
                                
                                  <div class="bd-highlight"><h5><a href="javascript:void(0)" id="don" class="cok" data-bs-toggle="modal" data-bs-target="#modaldetail">Donatur : <span id="kon"></span></a></h5></div>
                                  <div class="bd-highlight"><h5><a href="javascript:void(0)" id="ada" class="cok" data-bs-toggle="modal" data-bs-target="#modaldetail">Ada Koordinat : <span id="ak"></span></a></h5></div>
                                  <div class="bd-highlight"><h5><a href="javascript:void(0)" id="gada" class="cok" data-bs-toggle="modal" data-bs-target="#modaldetail">Tidak ada koordinat : <span id="tak"></span></a></h5></div>
                                  <div class="bd-highlight"><h5><a href="javascript:void(0)" id="him" class="cok" data-bs-toggle="modal" data-bs-target="#modaldetail">Penghimpunan : <span id="peng"></span></a></h5></div>
                                
                            <!--</h6>-->
                        </div>
                        <div class="card-body">
                            <div class="row">
                                   
                                    <div class="col-md-3 mb-3">
                                        <!--<label for="lmt">Donatur</label>-->
                                        <div class="form-group">
                                            <select class="form-control cek90 auhh" name="dntr" id="dntr">
                                                
                                            </select>
                                            <small>*hanya donatur yang memiliki koordinat</small>
                                        </div>
                                    </div>
                                    
                                    <!--<div class="col-md-3 mb-3">-->
                                        <!--<label for="lmt">Donatur</label>-->
                                    <!--    <div class="form-group">-->
                                    <!--        <select class="form-control caid" name="ds" id="ds">-->
                                                
                                    <!--        </select>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                               </div>
                            <div class="row" >
                                <div class="col-md-12">
                                    <div id="map" style="width:100%;height:380px;"></div>
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