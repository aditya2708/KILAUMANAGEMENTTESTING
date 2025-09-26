@extends('template')
@section('konten')
<style>
    .btn-selectable .check {
        display: none;
    }

    .btn-selectable.selected .check {
        display: flex; /* atau inline-block jika SVG-nya tidak fleksibel */
    }

    .btn-selectable.selected {
        border-color: #007bff;
        background-color: #e6f0ff;
    }

    .check svg {
        width: 24px;
        height: 24px;
        stroke: #007bff;
    }
</style>


    <div class="content-body">
        <div class="container-fluid">
              <div class="modal fade" id="exampleModalAktivasi">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-header">
                        <h4>Aktivasi Perusahaan</h4>
                    </div>
                    <!--<form method="post" action="{{url('cobakan')}}">-->
                    <!--<form>-->
                        <div class="modal-body">
                            <p>List Perusahaan Nonaktive </p>
                           @foreach ($comNonAktif ?? [] as $data)
                            @php
                                $image = ($data->logo != '') ? 'https://kilauindonesia.org/kilau/upload/'.$data->logo : 'https://kilauindonesia.org/kilau/upload/BT-LOGO.png';
                                $signs = ($data->ttd != '') ? 'https://kilauindonesia.org/kilau/upload/'.$data->ttd : 'https://kilauindonesia.org/kilau/upload/v.jpg';
                            @endphp
                        
                            <div class="btn-perusahaan-wrapper col-lg-12 p-2">
                                <div class="border card mb-3 p-3 d-flex shadow-md perusahaan cursor-pointer w-90 btn-selectable" 
                                     data-id="{{ $data->id_com }}">
                                    <div class="row g-5">
                                        <div class="col-md-4 d-flex align-items-center p-3">
                                            <img src="{{ $image }}" class="img-fluid" alt="Logo" style="max-width: 100px;">
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card-body mt-4">
                                                <div class="company-info">
                                                    <h5>{{ $data->name }}</h5>
                                                    <p class="ml-4">{{ $data->alamat }}</p>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="col-md-2 align-items-center p-3 check">
                                           <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4 12.6111L8.92308 17.5L20 6.5" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="valPerus">Aktivasi</button>
                        </div>
                    <!--</form>-->
                </div>
            </div>
        </div>

      
        @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == '1')
        
         <div id="nenen">
            <div class="card-header row align-items-center">
                <div class="col">
                    <h5><b>List Perusahaan</b></h5>
                </div>
                
                <div class="col-auto">
                    <a href="{{url('entry-company')}}" class="btn btn-primary btn-xxs" data-bs-toggle="tooltip" data-bs-placement="top" title="Entri Perusahaan">Entri Perusahaan</a>
                </div>
                
                <div class="col-auto">
                     <a href="javascript:void(0)" class="btn btn-primary btn-xxs" id="getNonaktifCompany"  data-bs-toggle="modal" data-bs-target="#exampleModalAktivasi">Aktivasi Perusahaan</a>
                    <!--<a  class="btn btn-primary btn-xxs" data-bs-toggle="tooltip"  data-bs-toggle="modal" data-bs-target="#exampleModalAktivasi" data-bs-placement="top" title="Aktivasi Perusahaan">Aktivasi Perusahaan</a>-->
                </div>
            </div>
    
    
             @foreach ($prog as $company)
                @php
                
                    $image = ($company->logo != '') ? 'https://kilauindonesia.org/kilau/upload/'.$company->logo : 'https://kilauindonesia.org/kilau/upload/BT-LOGO.png';
                @endphp
                
                @php 
                    $signs = ($company->logo != '') ? 'https://kilauindonesia.org/kilau/upload/'.$company->ttd :'https://kilauindonesia.org/kilau/upload/v.jpg';
                @endphp
                
                <button type="button" class="btn-perusahaan col-lg-12 pencet p-2" value="{{$company->id_com}}" data-nama="" id="com" name="com"  >
                    <div class="border card mb-3 d-flex shadow-md perusahaan cursor-pointer w-90">
                        <div class="row g-0">
                            <div class="col-md-1 d-flex align-items-center p-3">
                                <img src="{{$image}}" class="img-fluid" alt="Logo" style="max-width: 100px;">
                            </div>
                            <div class="col-md-6">
                                <div class="card-body mt-4">
                                    <div class="company-info">
                                        <h5>{{$company->name}}</h5>
                                        <p class="ml-4">{{$company->alamat}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="card-body mt-4">
                                    <div class="company-info">
                                         <h5>Jumlah Karyawan</h5>
                                    <p class="ml-4">{{$company->jumlah}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </button>
              @endforeach
              
                 </div>
                 
                 <div id="suka" style="display:none">
                      <div class="pull-right">
                            <button type="button" class="btn btn-primary btn-sm mamah"id="mamah" value="1" style="margin-right: 10px">List Perusahaan</button>
                            <!--<a href="javascript:void(0)" class="btn btn-primary btn-sm mamah" id="mamah" value="1">List Perusahaan</a>-->
                        </div>
                        
                <div class="row">
                    <div class="col-lg-12">
                        <div class="profile card card-body px-3 pt-3 pb-0">
                            <div class="profile-head">
                                <div class="photo-content">
                                    <div class="cover-photo rounded"></div>
                                </div>
                                <div class="profile-info">
                                    <div class="profile-photo">
                                       <img id= "output"  class="rounded-circle" alt="" style="background-color: white; padding: 5px; height: 100px; width: 100px">
                                    </div>
                                    <div class="profile-details">
                                        <div class="profile-name px-3 pt-2">
                                            <!--<label class="col-sm-4" id="nama_com"></label>-->
                                            <!--<label class="col-sm-4" id="jenis"></label>-->

                                            <h4 class="text-primary mb-0 "id="nama_com"></h4>
                                            <p id="jenis"></p>
                                        </div>
                                        <div class="profile-email px-2 pt-2">
                                            <h4 class="text-muted mb-0" id="email"></h4>
                                            <!--<p>Email</p>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="profile-tab">
                                    <div class="custom-tab-1">
                                        <ul class="nav nav-tabs">
                                            <li class="nav-item"><a href="#about-me" data-bs-toggle="tab" class="nav-link active show">Informasi</a></li>
                                            <li class="nav-item"><a href="#profile-settings" data-bs-toggle="tab" class="nav-link">Update Perusahan</a></li>
                                            <li class="nav-item"><a href="#bpjs" data-bs-toggle="tab" class="nav-link">Keikutsertaan BPJS</a></li>
                                            <li class="nav-item"><a href="#ttd" data-bs-toggle="tab" class="nav-link">Setting Pimpinan</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div id="about-me" class="tab-pane fade active show">
                                                <div class="profile-personal-info pt-3">
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Nama Lembaga <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span id="name"></span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Alamat <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span id="alamat"></span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Alias <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span id="alias"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">SK Lembaga <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span id="sk"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">NPWP <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span id="npwp"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">SMS Center <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span id="sms"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">WA Center <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span id="wa"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Email Center <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span id="mail"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Website <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span id="web" ></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Tanggal Berdiri <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span id="berdiri"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Access <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span id="akses"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Jenis <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span id="jenis1"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="profile-settings" class="tab-pane fade">
                                                <div class="pt-3">
                                                    <div class="settings-form">
                                                        <form method="post" id='settingprofilehc' enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="basic-form">
                                                                 <input type="hidden" name="idcom" id="idcom" value="">
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Nama Lembaga</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="name" class="form-control" id="name1" aria-describedby="name" value="">
                                                                    </div>
                                                                </div>
        
        
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Alamat</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="alias" class="form-control" id="alamat1" aria-describedby="name" value="">
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Alias</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="alias" class="form-control" id="alias1" aria-describedby="name" value="">
                                                                    </div>
                                                                </div>
        
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">SK Lembaga</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="sk" class="form-control" id="sk1" aria-describedby="name" value="">
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">NPWP</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="npwp" class="form-control" id="npwp1" aria-describedby="name" value="">
                                                                    </div>
                                                                </div>
        
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">SMS Center</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="sms" class="form-control" id="sms1" aria-describedby="name" value="">
                                                                    </div>
                                                                </div>
        
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">WA Center</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="wa" class="form-control" id="wa1" aria-describedby="name" value="">
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Email Center</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="email" class="form-control" id="email1" aria-describedby="name" value="">
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Website</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="web" class="form-control" id="web1" aria-describedby="name" value="">
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Tanggal Berdiri</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="berdiri" class="form-control" id="berdiri1" aria-describedby="name" value="">
                                                                    </div>
                                                                </div>
        
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Access</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="akses" class="form-control" id="akses1" aria-describedby="name" value="">
                                                                    </div>
                                                                </div>
        
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Jenis</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="jenis" class="form-control" id="jenis2" aria-describedby="name" value="">
                                                                    </div>
                                                                </div>
                

                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Upload Logo</label>
                                                                    <div class="col-sm-5">
                                                                       <img id= "output" style="height:80px; margin-bottom:10px;">
                                                                        <div class="input-group">
                                                                            <div class="form-file">
                                                                                <input type="file" name="logo" class="form-file-input form-controll" value="" accept="image/*" onchange="loadFilehc(this)">
                                                                            </div>
                                                                            <span class="input-group-text">Upload</span>
                                                                        </div>
                                                                         <input type="hidden" id="nama_file" value="">
                                                                         <input type="hidden" id="base64" value="">
                                                                    </div>
                                                                </div>
        
        
                                                            </div>
                                                            <button class="btn btn-primary" type="submit">Simpan</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="bpjs" class="tab-pane fade">
                                                <div class="pt-3">
                                                    <div class="settings-form">
                                                        <form method="post" id="post_bpjshc">
                                                            @csrf
                                                         <input type="hidden" name="idcom" id="idcom" value="">
                                                            <table class="table" width="80%">
                                                            
                                                                <tr>
                                                                    <th colspan="2">
                                                                        <h4>BPJS Ketenagakerjaan</h4>
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <td>JKK</td>
                                                                    <td>
                                                                        <select class="form-control" style="width: 70%" id="jkk" name="jkk">
                                                                            <option value="1">Ikut Serta</option>
                                                                            <option  value="0">Tidak Ikut Serta</option>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>JKM</td>
                                                                    <td>
                                                                        <select class="form-control" style="width: 70%" id="jkm" name="jkm">
                                                                            <option value="1">Ikut Serta</option>
                                                                            <option value="0">Tidak Ikut Serta</option>
                                                                        </select>
                                                                    </td>
                                                                </tr>
        
                                                                <tr>
                                                                    <td>JHT</td>
                                                                    <td>
                                                                        <select class="form-control" style="width: 70%" id="jht" name="jht">
                                                                            <option value="1">Ikut Serta</option>
                                                                            <option value="0">Tidak Ikut Serta</option>
                                                                        </select>
                                                                    </td>
                                                                </tr>
        
                                                                <tr>
                                                                    <td>JPN</td>
                                                                    <td>
                                                                        <select class="form-control" style="width: 70%" id="jpn" name="jpn">
                                                                            <option  value="1">Ikut Serta</option>
                                                                            <option  value="0">Tidak Ikut Serta</option>
                                                                        </select>
                                                                    </td>
                                                                    <input type="hidden" name="id_hide" id="id" value="">
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="2">
                                                                        <h4>BPJS Kesehatan</h4>
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <td>Kesehatan</td>
                                                                    <td>
                                                                        <select class="form-control" style="width: 70%"  id="kesehatan" name="kesehatan">
                                                                            <option  value="1">Ikut Serta</option>
                                                                            <option  value="0">Tidak Ikut Serta</option>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                              
                                                            </table>
                                                            <button class="btn btn-primary" type="submit">Simpan</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="ttd" class="tab-pane fade">
                                                <div class="pt-3">
                                                    <form   method="post"  id="signhc">
                                                        <div class="settings-form">
                                                            @csrf
                                                           <input type="hidden" name="idcom" id="idcom" value="">
                                                            <div class="basic-form">
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Jabatan Pimpinan</label>
                                                                    <div class="col-sm-5">
                                                                        <select class="form-control piljab" name="id_jabdir" id="piljab">
                                                                            @if(count($jab) > 0)
                                                                                <option selected="selected" value="">Pilih Jabatan</option>
                                                                            @foreach($jab as $c)
                                                                                <option value="{{ $c->id }}">{{$c->jabatan}} </option>
                                                                            @endforeach
                                                                            @else
                                                                            @endif
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Nama Pimpinan</label>
                                                                    <div class="col-sm-5">
                                                                        <select class="form-control direktur" name="direkturs"  id="direkturs">
                                                                                @if(count($karyawan) > 0)
                                                                                <option selected="selected" value="">Pilih Karyawan</option>
                                                                                @foreach($karyawan as $c)
                                                                                <option  data-name="{{ $c->nama }}" value="{{ $c->id_karyawans }}">{{$c->nama}}</option>
                                                                                @endforeach
                                                                                @else
                                                                              
                                                                                @endif
                                                                        </select>
                                                                    </div>
                                                                    <div id="id_kar">
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Upload TTD</label>
                                                                    <div class="col-sm-5">
                                                                        <img id= "outputttd" style="height:80px; margin-bottom:10px;">
                                                                        <div class="input-group">
                                                                            <div class="form-file">
                                                                                <!--<input type="file" name="ttd" class="form-file-input form-controll" value="" accept="image/*" onchange="loadFilettdhc(event)">-->
                                                                                <input type="file" name="ttd" class="form-file-input form-controll" value="" accept="image/*" onchange="loadFilettdhc(this)">

                                                                            </div>
                                                                            <span class="input-group-text">Upload</span>
                                                                        </div>
                                                                         <input type="hidden" id="nama_file_0" value="">
                                                                         <input type="hidden" id="base64_0" value="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="submit" id="updthc" class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </form>
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
                 
            @else
                @foreach ($prog as $karyawanbandung)
                <div class="row">
                    <div class="col-lg-12">
                        <div class="profile card card-body px-3 pt-3 pb-0">
                            <div class="profile-head">
                                <div class="photo-content">
                                    <div class="cover-photo rounded"></div>
                                </div>
                                <div class="profile-info">
                                    <div class="profile-photo">
                                        @if($karyawanbandung->logo != null)
                                        <img src="{{asset('upload/'.$karyawanbandung->logo)}}" class="rounded-circle" alt="" style="background-color: white; padding: 5px; height: 100px; width: 100px">
                                        @else
                                        <img src="{{asset('upload/v.jpg')}}" class="rounded-circle" alt="" style="background-color: white; padding: 5px; height: 100px; width: 100px">
                                        @endif
                                    </div>
                                    <div class="profile-details">
                                        <div class="profile-name px-3 pt-2">
                                            <h4 class="text-primary mb-0">{{$karyawanbandung->name}}</h4>
                                            <p>{{$karyawanbandung->jenis}}</p>
                                        </div>
                                        <div class="profile-email px-2 pt-2">
                                            <h4 class="text-muted mb-0">{{$karyawanbandung->email}}</h4>
                                            <p>Email</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="profile-tab">
                                    <div class="custom-tab-1">
                                        <ul class="nav nav-tabs">
                                            <li class="nav-item"><a href="#about-me" data-bs-toggle="tab" class="nav-link active show">Informasi</a></li>
                                            <li class="nav-item"><a href="#profile-settings" data-bs-toggle="tab" class="nav-link">Update Perusahan</a></li>
                                            <li class="nav-item"><a href="#bpjs" data-bs-toggle="tab" class="nav-link">Keikutsertaan BPJS</a></li>
                                            <li class="nav-item"><a href="#ttd" data-bs-toggle="tab" class="nav-link">Setting Pimpinan</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div id="about-me" class="tab-pane fade active show">
                                                <div class="profile-personal-info pt-3">
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Nama Lembaga <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span>{{$karyawanbandung->name}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Alias <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span>{{$karyawanbandung->alias}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">SK Lembaga <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span>{{$karyawanbandung->sk}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">NPWP <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span>{{$karyawanbandung->npwp}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">SMS Center <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span>{{$karyawanbandung->sms}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">WA Center <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span>{{$karyawanbandung->wa}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Email Center <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span>{{$karyawanbandung->email}}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Website <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span>{{$karyawanbandung->web}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Tanggal Berdiri <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span>{{$karyawanbandung->berdiri}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Access <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span>{{$karyawanbandung->akses}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 col-5">
                                                            <h5 class="f-w-500">Jenis <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-5 col-7"><span>{{$karyawanbandung->jenis}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div id="profile-settings" class="tab-pane fade">
                                                <div class="pt-3">
                                                    <div class="settings-form">
                                                        <form method="post" id="settingprofile" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('patch')
                                                            <div class="basic-form">
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Nama Lembaga</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="name" class="form-control" id="name" aria-describedby="name" value="{{$karyawanbandung->name}}">
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Alamat</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="name" class="form-control" id="alamat" aria-describedby="name" value="{{$karyawanbandung->alamat}}">
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Alias</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="alias" class="form-control" id="alias" aria-describedby="name" value="{{$karyawanbandung->alias}}">
                                                                    </div>
                                                                </div>
        
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">SK Lembaga</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="sk" class="form-control" id="sk" aria-describedby="name" value="{{$karyawanbandung->sk}}">
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">NPWP</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="npwp" class="form-control" id="npwp" aria-describedby="name" value="{{$karyawanbandung->npwp}}">
                                                                    </div>
                                                                </div>
        
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">SMS Center</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="sms" class="form-control" id="sms" aria-describedby="name" value="{{$karyawanbandung->sms}}">
                                                                    </div>
                                                                </div>
        
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">WA Center</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="wa" class="form-control" id="wa" aria-describedby="name" value="{{$karyawanbandung->wa}}">
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Email Center</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="email" class="form-control" id="email" aria-describedby="name" value="{{$karyawanbandung->email}}">
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Website</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="web" class="form-control" id="web" aria-describedby="name" value="{{$karyawanbandung->web}}">
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Tanggal Berdiri</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="berdiri" class="form-control" id="berdiri" aria-describedby="name" value="{{$karyawanbandung->berdiri}}">
                                                                    </div>
                                                                </div>
        
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Access</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="akses" class="form-control" id="akses" aria-describedby="name" value="{{$karyawanbandung->akses}}">
                                                                    </div>
                                                                </div>
        
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Jenis</label>
                                                                    <div class="col-sm-5">
                                                                        <input type="text" name="jenis" class="form-control" id="jenis" aria-describedby="name" value="{{$karyawanbandung->jenis}}">
                                                                    </div>
                                                                </div>
        
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Upload Logo</label>
                                                                    <div class="col-sm-5">
                                                                        @if($karyawanbandung->logo != null)
                                                                        <img id="output" style="height:80px; margin-bottom:10px;" src="{{asset('upload/'.$karyawanbandung->logo)}}">
                                                                        @else
                                                                        <img id="output" style="height:80px; margin-bottom:10px;" src="{{asset('upload/v.jpg')}}">
                                                                        @endif
                                                                        <div class="input-group">
                                                                            <div class="form-file">
                                                                                <input type="file" name="logo" class="form-file-input form-controll" value="{{$karyawanbandung->logo}}" accept="image/*" onchange="loadFile(this)">
                                                                            </div>
                                                                            <span class="input-group-text">Upload</span>
                                                                        </div>
                                                                         <input type="hidden" id="nama_file_adm" value="">
                                                                         <input type="hidden" id="base64_adm" value="">
                                                                    </div>
                                                                </div>
        
        
                                                            </div>
                                                            <button class="btn btn-primary" type="submit">Simpan</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="bpjs" class="tab-pane fade">
                                                <div class="pt-3">
                                                    <div class="settings-form">
                                                        <form method="post" id="post_bpjs">
                                                            @csrf
                                                            <table class="table" width="80%">
                                                                @foreach($prog as $dd)
                                                                <tr>
                                                                    <th colspan="2">
                                                                        <h4>BPJS Ketenagakerjaan</h4>
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <td>JKK</td>
                                                                    <td>
                                                                        <select class="form-control" style="width: 70%" name="jkk">
                                                                            <option {{ ($dd->jkk) == "1"? 'selected':''}} value="1">Ikut Serta</option>
                                                                            <option {{ ($dd->jkk) == "0"? 'selected':''}} value="0">Tidak Ikut Serta</option>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>JKM</td>
                                                                    <td>
                                                                        <select class="form-control" style="width: 70%" name="jkm">
                                                                            <option {{ ($dd->jkm) == "1"? 'selected':''}} value="1">Ikut Serta</option>
                                                                            <option {{ ($dd->jkm) == "0"? 'selected':''}} value="0">Tidak Ikut Serta</option>
                                                                        </select>
                                                                    </td>
                                                                </tr>
        
                                                                <tr>
                                                                    <td>JHT</td>
                                                                    <td>
                                                                        <select class="form-control" style="width: 70%" name="jht">
                                                                            <option {{ ($dd->jht) == "1"? 'selected':''}} value="1">Ikut Serta</option>
                                                                            <option {{ ($dd->jht) == "0"? 'selected':''}} value="0">Tidak Ikut Serta</option>
                                                                        </select>
                                                                    </td>
                                                                </tr>
        
                                                                <tr>
                                                                    <td>JPN</td>
                                                                    <td>
                                                                        <select class="form-control" style="width: 70%" name="jpn">
                                                                            <option {{ ($dd->jpn) == "1"? 'selected':''}} value="1">Ikut Serta</option>
                                                                            <option {{ ($dd->jpn) == "0"? 'selected':''}} value="0">Tidak Ikut Serta</option>
                                                                        </select>
                                                                    </td>
                                                                    <input type="hidden" name="id_hide" id="id_hide" value="{{$dd->id}}">
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="2">
                                                                        <h4>BPJS Kesehatan</h4>
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <td>Kesehatan</td>
                                                                    <td>
                                                                        <select class="form-control" style="width: 70%" name="kesehatan">
                                                                            <option {{ ($dd->kesehatan) == "1"? 'selected':''}} value="1">Ikut Serta</option>
                                                                            <option {{ ($dd->kesehatan) == "0"? 'selected':''}} value="0">Tidak Ikut Serta</option>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </table>
                                                            <button class="btn btn-primary" type="submit">Simpan</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="ttd" class="tab-pane fade">
                                                <div class="pt-3">
                                                    <form method="post" id="sign" enctype="multipart/form-data">
                                                        <div class="settings-form">
                                                            @csrf
                                                            @method('patch')
                                                            <div class="basic-form">
                                                                <!--<div class="row mb-3">-->
                                                                <!--    <label class="col-sm-4">Nama Direktur</label>-->
                                                                <!--    <div class="col-sm-5">-->
                                                                <!--        <input type="text" name="direktur" class="form-control" id="direktur" aria-describedby="direktur" value="{{$karyawanbandung->direktur}}">-->
                                                                <!--    </div>-->
        
                                                                <!--</div>-->
                                                                
                                                                
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Jabatan Pimpinan</label>
                                                                    <div class="col-sm-5">
                                                                        <select class="form-control piljab" name="id_jabdir" id="piljab">
                                                                            @foreach($jab as $j)
                                                                                @if($j->id == $dd->id_jabdir)
                                                                                    <option data-nama="{{ $j->jabatan }}" value="{{ $j->id }}" selected>{{ $j->jabatan }}</option>
                                                                                @else
                                                                                    <option data-nama="{{ $j->jabatan }}" value="{{ $j->id }}">{{ $j->jabatan }}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                
                                                                <!--<div class="row mb-3">-->
                                                                <!--    <label class="col-sm-4">Jabatan Pimpinan</label>-->
                                                                <!--    <div class="col-sm-5">-->
                                                                <!--        <select class="form-control piljab" name="id_jabdir" id="piljab">-->
                                                                <!--            @foreach($jab as $j)-->
                                                                <!--            <option data-nama="{{ $j->jabatan }}" value="{{ $j->id }}">{{ $j->jabatan }}</option>-->
                                                                <!--            @endforeach-->
                                                                <!--        </select>-->
                                                                <!--    </div>-->
                                                                <!--</div>-->
                                                                
                                                               
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Nama Pimpinan</label>
                                                                    <div class="col-sm-5">
                                                                        <select class="form-control direktur" name="direktur" id="direktur">
                                                                            @foreach($karyawan as $j)
                                                                                @if($j->id_karyawan == $dd->id_direktur)
                                                                                    <option data-nama="{{ $j->nama }}" value="{{ $j->id_karyawan }}" selected>{{ $j->nama }}</option>
                                                                                @else
                                                                                    <option data-nama="{{ $j->nama }}" value="{{ $j->id_karyawan }}">{{ $j->nama }}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div id="id_kar">
                                                                    </div>
                                                                </div>

                                                               
                                                               
                                                                <!--<div class="row mb-3">-->
                                                                <!--    <label class="col-sm-4">Nama Pimpinan</label>-->
                                                                <!--    <div class="col-sm-5">-->
                                                                <!--        <select class="form-control direktur" name="direktur" id="direktur">-->
                                                                <!--          @foreach($karyawan as $j)-->
                                                                <!--            <option data-nama="{{ $j->nama }}" value="{{ $j->id_karyawan }}">{{ $j->nama }}</option>-->
                                                                <!--          @endforeach-->
                                                                <!--        </select>-->
                                                                <!--    </div>-->
                                                                <!--    <div id="id_kar">-->
                                                                <!--    </div>-->
                                                                <!--</div>-->
                                                                
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-4">Upload TTD</label>
                                                                    <div class="col-sm-5">
                                                                        @if($karyawanbandung->ttd == null)
                                                                        <img id="outputttd" style="height:80px; margin-bottom:10px;" src="{{asset('upload/v.jpg')}}">
                                                                        @else
                                                                        <img id="outputttd" style="height:80px; margin-bottom:10px;" src="{{asset('upload/'.$karyawanbandung->ttd)}}">
                                                                        @endif
                                                                        <div class="input-group">
                                                                            <div class="form-file">
                                                                                <input type="file" name="ttd" class="form-file-input form-controll" value="{{$karyawanbandung->ttd}}" accept="image/*" onchange="loadFilettd(this)">
                                                                            </div>
                                                                            <span class="input-group-text">Upload</span>
                                                                        </div>
                                                                         <input type="hidden" id="nama_file_adm_0" value="">
                                                                         <input type="hidden" id="base64_adm_0" value="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button class="btn btn-primary" type="submit">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection



