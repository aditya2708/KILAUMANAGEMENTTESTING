@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Setting</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">HCM</a></li>-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Data Jabatan</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <!-- Modal -->
        <div class="modal fade" id="exampleModal">
            <div class="modal-dialog modal-dialog-centered" style="max-width:600px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Tambah Jabatan</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <span id="form_result"></span>
                    <form method="post" id="sample_form">
                        <div class="modal-body">
                            @csrf
                            
                            
                                
                            <div class="form mb-3">
                                
                                 @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                    <label>Pilih Perusahaan</label>
                                    <select required class="form-control cek2 mb-3" name="perus" id="perus">
                                           @if(count($company) > 0)
                                                <option selected="selected" value="">Pilih Perusahaan</option>
                                                @foreach($company as $c)
                                                <option value="{{ $c->id_com }}">{{$c->name}}</option>
                                                @endforeach
                                                @else
                                                <!--<option selected="selected" value="">Pilih jabatan</option>-->
                                                <!--<option value="">Tidak Ada</option>-->
                                                @endif
                                    </select>
                                @endif
                                
                                <label>Jabatan</label>
                                <input type="text" name="jabatan" id="jabatan" class="form-control mb-3" required placeholder="Jabatan...">
                                
                                <label>Parent Jabatan</label>
                                    <select class="form-control" name="pr_jabatan" id="pr_jabatan" 
                                    @if(count($jab) > 0) required @endif>
                                        <option selected="selected" value="">- Pilih Parent Jabatan -</option>
                                        @if(count($jab) > 0)
                                            <option selected="selected" value="">Pilih Parent Jabatan</option>
                                            @foreach($jab as $kolek)
                                                <option value="{{$kolek->id}}">{{$kolek->jabatan}}</option>
                                            @endforeach
                                        @else
                                            <!--<option value="">Tidak Ada</option>-->
                                        @endif
                                    </select>


                                <!--<select class="form-control" name="pr_jabatan" id="pr_jabatan" required>-->
                                    <!--<option selected="selected" value="">- Pilih Parent Jabatan -</option>-->
                                <!--        @if(count($jab) > 0)-->
                                <!--        <option selected="selected" value="">Pilih Parent Jabatan</option>-->
                                <!--        @foreach($jab as $kolek)-->
                                <!--        <option value="{{$kolek->id}}">{{$kolek->jabatan}}</option>-->
                                <!--        @endforeach-->
                                <!--        @else-->
                                <!--        <option value="">Tidak Ada</option>-->
                                <!--        @endif-->
                                  
                                <!--</select>-->
                                

                                <!--<label class="label-control">Tunjangan Jabatan</label>-->
                                <!--<input type="text" name="tj_jabatan" id="tj_jabatan" class="form-control" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);"  placeholder="">-->
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="hidden" name="action" id="action" value="add" />
                            </div>
                            <div id="okks" style="display: none">
                                <table class="table" id="ttbl">
                                    <thead>
                                        <tr>
                                            <th>Tunjangan Jabatan</th>
                                            <th>Tunjangan Training</th>
                                            <th>Jenis Tunjangan PLT</th>
                                            <th>Tunjangan PLT</th>
                                        </tr>
                                    <thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" id="tj_jabatan" name="tj_jabatan" class="form-control" placeholder="0" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);"></td>
                                            <td><label class="switch"> <input id="checkbox" class="toggle-class" type="checkbox" name="cek" /> <div class="slider round"> </div> </label></td>
                                            <td>
                                                <select class="form-control" id="kon_plt" name="kon_plt">
                                                    <option value="" >Jenis Tunjangan PLT</option>
                                                    <option value="n" >Nominal</option>
                                                    <option value="p" >Presentase</option>
                                                </select>
                                            </td>
                                            <td>
                                                <div style="display: none" id="nn">
                                                    <input class="form-control" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);" name="nom" id="nom" type="text" placeholder="0">
                                                </div>
                                                <div style="display: none" id="pp">
                                                    <div class="input-group">
                                                        <input class="form-control" name="pres" id="pres" min="0.0" max="100" type="number"  placeholder="0">
                                                        <span class="input-group-text" style="background: #777; color: #fff">%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                        </tr>
                                  </tbody>
                              </table>
                                
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Data Jabatan</h4>
                        <div class="pull-right">
                            @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                            <button type="button" class="btn btn-primary btn-xxs " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>                        
                            @endif
                            <a href="javascript:void(0)" class="btn btn-primary btn-xxs editoo" data-bs-toggle="modal" id="record" data-bs-target="#exampleModal" >Tambah Jabatan</a>
                        </div>
                    </div>
    
                    <div class="card-body">
                        <div class="row">
                           
                        <div class="table-responsive">
                            <table id="user_table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jabatan</th>
                                        <th>Parent Jabatan</th>
                                        <th>Aksi</th>
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
</div>
@endsection