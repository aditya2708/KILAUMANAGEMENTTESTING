@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        <!--modal-->
        <div class="modal fade" id="tambah">
            <div class="modal-dialog modal-dialog-sm modal-dialog-centered" style="max-width: 700px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Tambah Target <span id="p"></span></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <span id="form_editt"></span>
                    <form method="post" id="form">
                        <div class="modal-body">
                            @csrf
                            <div id="ou">
                                
                            </div>
                        </div>
                        <!--<div id='map1' style='width: auto; height: 500px;'></div>-->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
        <div class="modal fade" id="modalprogram">
            <div class="modal-dialog modal-dialog-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Target Program</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <!--<form method="post" id="form">-->
                        <div class="modal-body">
                            <!--@csrf-->
                            <div class="basic-form">
                                <input type="hidden" id="id_hide" name="id_hide">
                                <input type="hidden" id="unit" name="unit">
                                <!--<div class="mb-3 row">-->
                                <!--    <label for="staticEmail" class="col-sm-2 col-form-label">Program : </label>-->
                                <!--    <div class="col-sm-4">-->
                                <!--      <input type="text" class="form-control" id="pr" name="pr">-->
                                <!--    </div>-->
                                <!--</div>-->
                                
                                <div class="mb-3 row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">Target : </label>
                                    <div class="col-sm-10">
                                      <input type="text" class="form-control" id="trgt" name="trgt" onkeyup="rupiah(this);">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="form">Simpan</button>
                        </div>
                    <!--</form>-->
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modalTarget">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Set Target</h4>
                        <div id="tutupin"></div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                        <div class="modal-body">
                            <div class="basic-form">
                                <input type="hidden" id="jenisnya">
                                <input type="hidden" id="id_units">
                                
                                <div class="col-md-12 mb-3">
                                    <label>Target Pertahun</label>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control mb-3" id="targetss" name="targetss" onkeyup="rupiah(this);" onclick="rupiah(this);">
                                            <input type="hidden" id="target_hide">
                                        </div>
                                        <div class="col-md-3">
                                            <button class="btn btn-sm btn-success" id="syu">Set Target</button>
                                        </div>
                                        
                                        <div class="col-md-1"></div>
                                        <div class="col-md-3">
                                            <button style="float: right; cursor: auto" disabled class="btn btn-block btn-sm btn-danger">Sisa : <span id="siss">0</span></button>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                @php
                                    $bulan = [ 1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei' , 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                                @endphp
                                
                                <input type="hidden" id="sisana">
                                <input type="hidden" id="sisa2">
                                
                                <div class="mb-3 row">
                                @for($i = 1; $i <= 12; $i++ )
                                    <div class="col-sm-4">
                                    <label for="staticEmail" class="col-sm-3 col-form-label">{{ $bulan[$i] }}</label>
                                      <input type="text" class="form-control mb-3 asssh" id="input{{$i}}" data-index="{{$i}}" data-sisa="0" onkeyup="rupiahw(this);" onclick="rupiahw(this);" onload="rupiahw(this);" style="width: 60%">
                                    </div>
                                @endfor
                                </div>
                                
                                <hr>
                                <div class="d-flex justify-content-end">
                                    <div><button type="button" class="btn btn-sm btn-primary" style="width: 150px" id="roar">Simpan</button></div>
                                    
                                </div>
                                
                            </div>
                        </div>
                        
                        <!--<div class="modal-footer">-->
                            <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>-->
                        <!--    <button type="button" class="btn btn-sm btn-primary" id="roar">Simpan</button>-->
                        <!--</div>-->
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="setwarning">
            <div class="modal-dialog modal-dialog-sm modal-dialog-centered" style="max-width: 500px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Setting Warning Donatur</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <form method="post" id="eheh">
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="id_tjj" id="id_tjj">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Bulan</label>
                                    <input class="form-control" name="bulll" id="bulll" type="number" min=0>
                                </div>
                                <div class="col-md-6">
                                    <label>Minimal Donasi</label>
                                    <input class="form-control" name="donnn" id="donnn" type="number" min=0>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>-->
                            <button type="button" class="btn btn-primary" id="ohoh">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
        <!--<div class="modal fade" id="modprogser"  data-bs-backdrop="static" data-bs-keyboard="false">-->
        <!--    <div class="modal-dialog modal-lg modal-dialog-centered">-->
        <!--        <div class="modal-content">-->
        <!--            <div class="modal-header">-->
        <!--                <h4>Set Target Program Per User <span id="dino"></span></h4>-->
        <!--                <div id="tutupin"></div>-->
        <!--            </div>-->
        <!--                <div class="modal-body">-->
        <!--                    <div class="d-flex">-->
        <!--                        <div class="bd-highlight">-->
        <!--                            <div class="form-check form-switch ">-->
        <!--                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked style="height : 20px; width : 40px;">-->
        <!--                                    <label for="flexSwitchCheckChecked" class="mt-1 ms-1">Show/Hide Program yang memiliki Target Omset</label>-->
        <!--                                </div>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <input type="hidden" id="toggleVal" name="toggleData"> -->
        <!--                    <div class="d-flex bd-highlight mt-3">-->
        <!--                        <div class="bd-highlight"><span class="badge bg-info">Target Omset : <span id="targetku"></span></span></div>-->
        <!--                    </div>-->
                            
        <!--                    <br>-->
                            
        <!--                    <div class="table-responsive">-->
                                
        <!--                        <table class="table table-striped" id="ttbbll">-->
        <!--                            <thead>-->
        <!--                                <tr>-->
        <!--                                    <th>No</th>-->
        <!--                                    <th>Program</th>-->
        <!--                                    <th>Total Target</th>-->
        <!--                                    <th class="sisget">Sisa Target</th>-->
        <!--                                    <th class="gett">Target</th>-->
        <!--                                    <th>Penawaran</th>-->
        <!--                                    <th>Follow Up</th>-->
        <!--                                    <th>Closing</th>-->
        <!--                                </tr>-->
        <!--                            </thead>-->
        <!--                            <tbody id="progBod">-->
                                        
        <!--                            </tbody>-->
        <!--                            <tfoot id="progFoot">-->
                                        
        <!--                            </tfoot>-->
        <!--                        </table>-->
        <!--                    </div>-->
        <!--                </div>-->
                        
        <!--                <div class="modal-footer">-->
        <!--                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>-->
        <!--                    <button type="button" class="btn btn-primary" id="ezzz">Simpan</button>-->
        <!--                </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        
        <!--end-->
        
        <!--isi-->
        <div class="row">
            
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="basic-form">
                            <div class="row">
                                
                                <div class="col-md-3">
                                    <label class="form-label">Periode : </label>
                                    <select class="form-control cerr" id="periode" name="periode">
                                        <option value="" disabled>Pilih Periode</option>
                                        <option value="tahun" selected>Tahun</option>
                                        <option value="bulan">Bulan</option>
                                    </select>
                                </div>
                                
                                
                                <div class="col-md-3" id="thns">
                                    <label class="form-label">Tahun :</label>
                                    <input type="text" class="form-control years ceky" name="tahun" id="tahun" autocomplete="off" placeholder="yyyy" >
                                </div>
                                
                                <div class="col-md-3" id="blns">
                                    <label class="form-label">Bulan :</label>
                                    <input type="text" class="form-control year ceky" name="thn" id="thn" autocomplete="off" placeholder="yyyy-mm" >
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="form-label">Jenis Target :</label>
                                    <select id="jenis" class="form-control cek1" name="jenis">
                                        <!--<option value="id_kan" selected>Kantor</option>--> 
                                        <!--<option value="id_kar">Petugas</option>-->
                                        
                                        <!--<option value="prog">Program (Penerimaan)</option>-->
                                    </select>
                                </div>
                                
                                <div class="col-md-3" style="display: none" id="unit_hide">
                                    <label class="form-label">Unit :</label>
                                    <select class="form-control c_unit" id="units" name="units" >
                                        <option value="">Pilih Unit</option>
                                        @foreach($kota as $k)
                                        <option value="{{ $k->id }}" {{ Auth::user()->id_kantor == $k->id ? 'selected' : '' }} >{{ $k->unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!--<div class="col-md-3" style="display: none" id="unit2_hide">-->
                                <!--    <label class="form-label">Unit :</label>-->
                                <!--    <select class="form-control c_unit" id="units2" name="units2">-->
                                <!--        <option value="">Pilih Semua Unit</option>-->
                                <!--        @foreach($kota as $k)-->
                                <!--        <option value="{{ $k->id }}" {{ Auth::user()->id_kantor == $k->id ? 'selected' : '' }}>{{ $k->unit }}</option>-->
                                <!--        @endforeach-->
                                <!--    </select>-->
                                <!--</div>-->
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-12">
                <div class="card">
                    
                    <!--@if(Auth::user()->kolekting == 'admin')-->
                    <!--<div class="card-header" id="hidee_btn" style="display: block">-->
                    <!--<div class="p-2 bd-highlight "><a class="btn btn-danger btn-xs" data-bs-toggle="modal" data-bs-target="#setwarning">Setting Warning Donatur</a></div>-->
                    <!--</div>-->
                    <!--@endif-->
                    
                    
                    <div class="card-header" id="hide_btn" style="display: none">
                        <div class="d-flex bd-highlight" >
                          <div class="p-2 bd-highlight "><span class="badge bg-info">Target Kantor : <span id="tk"></span></span></div>
                          <div class="p-2 bd-highlight "><span class="badge bg-success">Target Terpakai : <span id="tt"></span></span></div>
                          <div class="p-2 bd-highlight "><span class="badge bg-danger">Sisa Target : <span id="st"></span></span></div>
                          @if(Auth::user()->kolekting == 'admin')
                          <div class="p-2 bd-highlight con" style="display: none"><a class="btn btn-danger btn-xs " data-bs-toggle="modal" data-bs-target="#setwarning">Setting Warning Donatur</a></div>
                          @endif
                        </div>
                        <input type="hidden" id="sisatarget">
                        <input type="hidden" id="targetterpakai">
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="table-responsive">
                               <div id="breng"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end-->
        
    </div>
</div>
@endsection