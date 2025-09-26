@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ url('laporan-bulanan/ekspor') }}" >
                        <div class="basic-form">
                            <div class="d-flex bd-highlight mb-3">
                                <div class="me-auto p-2 bd-highlight">
                                    <label class="form-label">Filter :</label>
                                    <select id="jenis" class="form-control cek1" name="jenis">
                                         @foreach($jenis as $val)
                                            <option value="{{$val->id}}">{{$val->deskripsi}}</option>
                                            @endforeach
                                      
                                    </select>
                                </div>
                                
                                <div class="p-2 bd-highlight col-md-2">
                                    <label class="form-label">Tahun :</label>
                                    <input type="text" class="form-control year cek4" name="thn" id="thn" autocomplete="off" placeholder="{{date('Y') }}" >
                                </div>
                                
                          
                                
                                <div class="p-2 bd-highlight">
                                    <label class="form-label">Via</label>
                                    <select id="via" class="form-control cek6" name="via" style="float: right">
                                        <option value="1">Clossing</option>
                                        <option value="0">Realtime</option>    
                                    </select>
                                </div>
                            
                                <div class="col-lg-2 mb-3">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="margin-top: 30px">
                                            Ekspor
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><button class="dropdown-item" type="submit" value="xls" name="tombol">.XLS</button></li>
                                            <li><button class="dropdown-item" type="submit" value="csv" name="tombol">.CSV</button></li>
                                        </ul>
                                    </div>
                                </div>
                            
                       
                            </div>
                            
                        </div>
                        
                        
                     
                        <div class="d-flex justify-content-center">
                            <b>KILAU INDONESIA</b>
                        </div>
                        <div class="d-flex justify-content-center">
                            <b id="piljen"></b>
                        </div>
                        <div class="d-flex justify-content-center">
                            <span id="totem"></span>
                        </div>
                       
                        
                        <hr>
                        
                         <div class="table-responsive"><input type="hidden" id="advsrc" value="tutup">
                            <table id="user_table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Urutan</th>
                                        <th>Nama</th>
                                        <th id = 'thnnn'></th>
                                        <th id = 'thnn'></th>
                                        <th id = 'bul1'></th>
                                        <th id = '2'></th>
                                        <th id = '3'></th>
                                        <th id = '4'></th>
                                        <th id = '5'></th>
                                        <th id = '6'></th>
                                        <th id = '7'></th>
                                        <th id = '8'></th>
                                        <th id = '9'></th>
                                        <th id = '10'></th>
                                        <th id = '11'></th>
                                        <th id = '12'></th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!--<div class="col-md-12">-->
            <!--    <div class="card">-->
            <!--        <div class="card-header">-->
            <!--            <h4 class="card-title d-flex justify-content-center" id="totem"></h4>-->
            <!--        </div>-->
            <!--        <div class="card-body">-->
            <!--            <div class="table-responsive">-->
            <!--                <div id="hash"></div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            
            
               
        </div>
    </div>
</div>
@endsection