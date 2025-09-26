@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-sm">

     <form  id="tambahcom" enctype="multipart/form-data">
         @csrf
        <div class="row">
            <!--<input type="hidden" value="{{ Auth::user()->id_com }}" id="id" name="id">-->
            <div class="col-xl-8">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="basic-form">
                                     <h5>Informasi Perushaan</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group ">
                                                <div class="col-md-12">
                                                    <label for="">Nama Perusahaan</label>
                                                    <input type="text" name="nama" class="form-control input-sm" id="nama" aria-describedby="" placeholder="Nama Perusahaan">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Alias</label>
                                                    <input type="text" name="alias" class="form-control input-sm" id="alias" aria-describedby="" placeholder="Alias">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>SK Perusahaan</label>
                                                    <input type="text" name="sk" class="form-control input-sm" id="sk" placeholder="SK">
                                                </div>
                                            </div>
                                        </div>
                                        
                                          <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>NPWP</label>
                                                    <input type="text" name="npwp" class="form-control input-sm" id="npwp" placeholder="NPWP">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>SMS Center</label>
                                                    <input type="text" name="sms" class="form-control input-sm" id="sms" placeholder="SMS">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>WA Center</label>
                                                    <input type="text" name="wa" class="form-control input-sm" id="wa" placeholder="WA">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>Email Center</label>
                                                    <input type="text" name="email" class="form-control input-sm" id="email" placeholder="Email">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>Website</label>
                                                    <input type="text" name="web" class="form-control input-sm" id="web" placeholder="Web">
                                                </div>
                                            </div>
                                        </div>
                                         
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>Tanggal Berdiri</label>
                                                    <input type="text" name="berdiri" class="form-control input-sm" id="berdiri" placeholder="Tanggal Berdiri">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>Access</label>
                                                    <input type="text" name="akses" class="form-control input-sm" id="akses" placeholder="Akses">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>Jenis Perusahaan</label>
                                                    <input type="text" name="jenis" class="form-control input-sm" id="jenis" placeholder="Jenis Perusahaan">
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>

                               
                                     <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>Upload Logo</label>
                                                    <img id= "logo" style="height:80px; margin-bottom:10px;">
                                                </div>
                                                <div class="input-group ">
                                                    <div class="form-file ">
                                                        <input type="file" name="logo" class="form-file-input form-controll" value="" accept="image/*" onchange="loadFile(this)">
                                                    </div>
                                                        <span class="input-group-text">Upload</span>
                                                </div>
                                                     <input type="hidden" id="nama_file" value="">
                                                     <input type="hidden" id="base64" value="">
                                            </div>
                                        </div>
                               
                                     
                                 

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="">Alamat</label>
                                                    <textarea id="alamat" class="form-control " name="alamat" rows="4" cols="50" placeholder="Alamat" style="height: 200px;"></textarea>
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

            <div class="col-xl-4">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body" id="pbjs">
                                <h5>BPJS Ketenagakerjaan</h5>
                                <div class="row">
                                    <div class="col-md-12 mb-3" >
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label>JKK</label>
                                                <select required id="jkk" class="form-control input-sm js-example-basic-single" style="width: 100%;" name="jkk">
                                                    <option selected="selected" value="">Pilih</option>
                                                    <option value="1">Ikut Serta</option>
                                                    <option value="0">Tidak Ikut Serta</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3" >
                                        <div class="form-group">
                                             <div class="col-md-12">
                                                <label>JKM</label>
                                                <select required id="jkm" class="form-control input-sm js-example-basic-single" style="width: 100%;" name="jkm">
                                                    <option selected="selected" value="">Pilih</option>
                                                    <option value="1">Ikut Serta</option>
                                                    <option value="0">Tidak Ikut Serta</option>
                                                </select>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    
                                     <div class="col-md-12 mb-3" >
                                        <div class="form-group">
                                             <div class="col-md-12">
                                                <label>JKM</label>
                                                <select required id="jkm" class="form-control input-sm js-example-basic-single" style="width: 100%;" name="jkm">
                                                    <option selected="selected" value="">Pilih</option>
                                                    <option value="1">Ikut Serta</option>
                                                    <option value="0">Tidak Ikut Serta</option>
                                                </select>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    
                                     <div class="col-md-12 mb-3" >
                                        <div class="form-group">
                                             <div class="col-md-12">
                                                <label>JHT</label>
                                                <select required id="jht" class="form-control input-sm js-example-basic-single" style="width: 100%;" name="jht">
                                                    <option selected="selected" value="">Pilih</option>
                                                    <option value="1">Ikut Serta</option>
                                                    <option value="0">Tidak Ikut Serta</option>
                                                </select>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    
                                     <div class="col-md-12 mb-3" >
                                        <div class="form-group">
                                             <div class="col-md-12">
                                                <label>JPN</label>
                                                <select required id="jpn" class="form-control input-sm js-example-basic-single" style="width: 100%;" name="jpn">
                                                    <option selected="selected" value="">Pilih</option>
                                                    <option value="1">Ikut Serta</option>
                                                    <option value="0">Tidak Ikut Serta</option>
                                                </select>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                  <h5>BPJS Kesehatan</h5>
                                  
                                   <div class="row">
                                    <div class="col-md-12 mb-3" >
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label>Kesehatan</label>
                                                <select required id="kesehatan" class="form-control input-sm js-example-basic-single" style="width: 100%;" name="kesehatan">
                                                    <option selected="selected" value="">Pilih</option>
                                                    <option value="1">Ikut Serta</option>
                                                    <option value="0">Tidak Ikut Serta</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                            
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <button class="btn btn-primary" type="submit" id="buttons">Simpan</button>
        </div>
      </form>
    </div>
</div>

@endsection