@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Setting</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Management User</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <?php
        $id_com = Auth::user()->id_com;
        $kyn = \DB::select("SELECT * from tambahan WHERE id_com = '$id_com'  ");
        $kyns = \DB::select("SELECT * from tambahan  ");
        $jabatan = \DB::select("SELECT * from jabatan WHERE id_com = '$id_com' ");
        $user = \DB::select("SELECT * from users WHERE id_com = '$id_com' ");
        ?>

        <!-- modal -->
        <div class="modal fade" id="exampleModalo">
            <div class="modal-dialog modal-dialog-centered" style="width:560px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-header">
                        <h4>Form Registrasi User</h4>
                    </div>
                    <form method="post" action="{{url('cobakan')}}">
                    <div class="modal-body">
                            @csrf
                            <div class="form">
                                
                                   @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                <div class="form-group mb-3">
                                    <label for="name">Perusahaan</label>
                                    <select required id="perus" name="perus" class="form-control js-example-basic-singles cek3" style="width: 100%;">
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
                                </div>
                                @endif
                                
                                <div class="form-group mb-3">
                                    <label >Nama Lengkap</label>
                                    <select required id="namekar" name="namekar" class="form-control js-example-basic-singles" style="width: 100%;">
                                                @if(count($karyawan) > 0)
                                                <option selected="selected" value="">Pilih Karyawan</option>
                                                @foreach($karyawan as $c)
                                                <option value="{{ $c->id_karyawan }}">{{$c->nama}} ( {{$c->jabatan}} )</option>
                                                @endforeach
                                                @else
                                              
                                                @endif
                                                
                                        <!--<option selected="selected" value="">- Pilih Karyawan -</option>-->
                                        <!--@foreach ($karyawan as $kar)-->
                                        <!--<option value="{{$kar->id_karyawans}}">{{$kar->nama}} ( {{$kar->jabatan}} )</option>-->
                                        <!--@endforeach-->
                                    </select>
                                </div>

                                <div id="datauser">

                                </div>
                                <div class="form-group mb-3">
                                    <label>Password Login</label>
                                    <input type="text" name="password" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Jam Kerja</label>
                                    <select class="form-control  wide" id="shift" name="shift" required>
                                        @if(count($shift) > 0)
                                            <option selected="selected" value="">Pilih Shift</option>
                                            @foreach($shift as $c)
                                                <option value="{{ $c->shift }}">Shift {{$c->shift}} </option>
                                            @endforeach
                                        @else
                                        @endif
                                        
                                        <!--<option value="">- Pilih Shift -</option>-->
                                        <!--@foreach ($shift as $val)-->
                                        <!--<option value="{{$val->shift}}">Shift {{$val->shift}}</option>-->
                                        <!--@endforeach-->
                                    </select>
                                </div>
                            @if($company[0]->client != 1)
                                <h5 style="margin: 80px 0px -10px 0px;"><b>HAK AKSES</b></h5>
                                <hr style="margin-bottom: 15px;" />
                               
                            
                                <div id="hidelevel">
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">CORE</label>
                                    <div class="col-sm-8">
                                        <select class="form-control  wide"  id="level" name="level">
                                            @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($level as $c)
                                               @if ($c->level != null)
                                                <option value="{{ $c->level }}">Shift {{$c->level}} </option>
                                               @endif
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->level != null)-->
                                            <!--<option value="{{$lev->level}}">{{$lev->level}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                </div>
                                
                                <div id="hidekeuangan">
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">FINS</label>
                                    <div class="col-sm-8">
                                        <select class="form-control  wide" id="keuangan" name="keuangan">
                                            @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($level as $c)
                                               @if ($c->keuangan != null)
                                                <option value="{{ $c->keuangan }}">{{$c->keuangan}} </option>
                                               @endif
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->keuangan != null)-->
                                            <!--<option value="{{$lev->keuangan}}">{{$lev->keuangan}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                </div>
                                
                                <div id="hidekepegawaian">
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">HCM</label>
                                    <div class="col-sm-8">
                                        <select class="form-control  wide" id="kepegawaian" name="kepegawaian">
                                            @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($level as $c)
                                               @if ($c->kepegawaian != null)
                                                <option value="{{ $c->kepegawaian }}">{{$c->kepegawaian}} </option>
                                               @endif
                                            @endforeach
                                            @else
                                            @endif
                                           
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->kepegawaian != null)-->
                                            <!--<option value="{{$lev->kepegawaian}}">{{$lev->kepegawaian}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                </div>
                                
                                <div id="hidekolekting">
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">Kolekting</label>
                                    <div class="col-sm-8">
                                        <select class="form-control  wide" id="kolekting" name="kolekting">
                                           @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($level as $c)
                                               @if ($c->kolekting != null)
                                                <option value="{{ $c->kolekting }}">{{$c->kolekting}} </option>
                                               @endif
                                            @endforeach
                                            @else
                                            @endif
                                           
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->kolekting != null)-->
                                            <!--<option value="{{$lev->kolekting}}">{{$lev->kolekting}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                </div>
                                
                              <div id='hidepengaturan'>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">Setting</label>
                                    <div class="col-sm-8">
                                        <select class="form-control  wide" id="pengaturan" name="pengaturan">
                                            @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($level as $c)
                                               @if ($c->pengaturan != null)
                                                <option value="{{ $c->pengaturan }}">{{$c->pengaturan}} </option>
                                               @endif
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->pengaturan != null)-->
                                            <!--<option value="{{$lev->pengaturan}}">{{$lev->pengaturan}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                               </div> 
                               
                               <div id='hidependidikan'>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">Pendidikan</label>
                                    <div class="col-sm-8">
                                        <select class="form-control  wide" id="pendidikan" name="pendidikan">
                                            @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($level as $c)
                                               @if ($c->pendidikan != null)
                                                <option value="{{ $c->pendidikan }}">{{ str_replace('_', ' ', $c->pendidikan) }}</option>
                                               @endif
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->pengaturan != null)-->
                                            <!--<option value="{{$lev->pengaturan}}">{{$lev->pengaturan}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                               </div> 
                            @endif
                               
                                <h5 style="margin: 20px 0px -10px 0px;"><b>Mobile Apps</b></h5>
                                <hr style="margin-bottom: 15px;" />
                                
                                 <div id='hidepresensi'>
                                    <div class="form-group row mb-3">
                                        <label class="col-sm-4">Presence Apps</label>
                                        <div class="col-sm-8">
                                            <select id="presensi" class="form-control  wide" name="presensi">
                                                @if(count($level) > 0)
                                                    <option selected="selected" value="">Pilih Akses</option>
                                                    @foreach($level as $c)
                                                       @if ($c->presensi != null)
                                                        <option value="{{ $c->presensi }}">{{$c->presensi}} </option>
                                                       @endif
                                                    @endforeach
                                                @else
                                                    <option selected="selected" value="">Pilih Akses</option>
                                                    <option value="admin">Admin</option>
                                                    <option value="kacab">Kepala</option>
                                                    <option value="karyawan">Karyawan</option>
                                                @endif
                                                
                                                <!--<option value="">- Pilih Akses -</option>-->
                                                <!--@foreach ($level as $lev)-->
                                                <!--@if ($lev->presensi != null)-->
                                                <!--<option value="{{$lev->presensi}}">{{$lev->presensi}}</option>-->
                                                <!--@endif-->
                                                <!--@endforeach-->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-3" id="jenis" hidden>
                                    <label class="col-sm-4">Jenis </label>
                                    <div class="col-sm-8">
                                        <select class="form-control  wide" name="jenis">
                                            <option value="">- Pilih Jenis -</option>
                                            <option value="staf">Staf</option>
                                            <option value="lapangan">Lapangan</option>
                                        </select>
                                    </div>
                                </div>
                                 @if($company[0]->client != 1)
                                <div id='hidekolek'>
                                    <div class="form-group row mb-3">
                                        <label class="col-sm-4">Collecting Apps</label>
                                        <div class="col-sm-8">
                                            <select id="kolek" class="form-control  wide" name="kolektor">
                                                @if(count($level) > 0)
                                                    <option selected="selected" value="">Pilih Akses</option>
                                                @foreach($level as $c)
                                                   @if ($c->kolektor != null)
                                                    <option value="{{ $c->kolektor }}">{{$c->kolektor}} </option>
                                                   @endif
                                                @endforeach
                                                @else
                                                @endif
                                                
                                                <!--<option value="">- Pilih Akses -</option>-->
                                                <!--@foreach ($level as $lev)-->
                                                <!--@if ($lev->kolektor != null)-->
                                                <!--<option value="{{$lev->kolektor}}">{{$lev->kolektor}}</option>-->
                                                <!--@endif-->
                                                <!--@endforeach-->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="form-group row mb-3" id="minimal" hidden>
                                    <p class="col-sm-4">Minimal Transaksi</p>
                                    <div class="col-sm-8">
                                        <input type="text" name="minimal" class="form-control" onkeyup="convertToRupiah(this);" placeholder="contoh : Rp. 5.000">
                                    </div>
                                </div>
                                <div class="form-group row mb-3" id="kunjungan" hidden>
                                    <p class="col-sm-4">Target Kunjungan</p>
                                    <div class="col-sm-8">
                                        <input type="text" name="kunjungan" class="form-control" onkeyup="Angka(this);" placeholder="contoh : 40">
                                    </div>
                                </div>
                                <div class="form-group row mb-3" id="qty" hidden>
                                    <p class="col-sm-4">Target Transaksi</p>
                                    <div class="col-sm-8">
                                        <input type="text" name="qty" class="form-control" onkeyup="Angka(this);" placeholder="contoh : 30">
                                    </div>
                                </div>
                                <div class="form-group row mb-3" id="target" hidden>
                                    <p class="col-sm-4">Target Dana</p>
                                    <div class="col-sm-8">
                                        <input type="text" name="target" class="form-control" onkeyup="convertToRupiah(this);" placeholder="contoh : Rp. 25.000.000">
                                    </div>
                                </div>
                                <div class="form-group row mb-3" id="honor" hidden>
                                    <p class="col-sm-4">Honor Per-Transaksi</p>
                                    <div class="col-sm-8">
                                        <input type="text" name="honor" class="form-control" onkeyup="convertToRupiah(this);" placeholder="contoh : Rp. 1.350">
                                    </div>
                                </div>
                                <div class="form-group row mb-3" id="bonus" hidden>
                                    <p class="col-sm-4">Bonus Harian</p>
                                    <div class="col-sm-8">
                                        <input type="text" name="bonus" class="form-control" onkeyup="convertToRupiah(this);" placeholder="contoh : Rp. 30.000">
                                    </div>
                                </div>
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

        <div class="modal fade" id="edkar">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Form Edit User</h4>
                    </div>
                    <!--<span id="form_result"></span>-->
                    <form method="post" id="sample_form">
                        <div class="modal-body">
                            <div class="basic-form">
                                <div class="row">
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="name">Nama Lengkap</label>
                                        <input type="text" id="nama_kar" name="name" class="form-control" aria-describedby="name" value="">
                                    </div>
    
                                    <div class="col-md-12 mb-3">
                                        <label>Unit Kerja</label>
                                        <select required class="form-control noob-select wide" name="id_kantor" id="unit_kerja">
                                            @if(count($kyn) > 0)
                                                <option selected="selected" value="">Pilih Unit Kerja</option>
                                            @foreach($kyn as $c)
                                                <option value="{{ $c->id }}">{{$c->unit}} </option>
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">- Pilih Unit Kerja -</option>-->
                                            <!--@foreach ($kyn as $kan)-->
                                            <!--<option value="{{$kan->id}}">{{$kan->unit}}</option>-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label>Jabatan</label>
                                        <select required class="form-control noob-select wide" name="id_jabatan" id="jabatan">
                                            @if(count($jabatan) > 0)
                                                <option selected="selected" value="">Pilih Jabatan</option>
                                            @foreach($jabatan as $c)
                                                <option value="{{ $c->id }}">{{$c->jabatan}} </option>
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">- Pilih Jabatan -</option>-->
                                            <!--@foreach ($jabatan as $jab)-->
                                            <!--<option value="{{$jab->id}}">{{$jab->jabatan}}</option>-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label>Email</label>
                                        <input type="text" name="email" id="email" class="form-control" value="">
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="hidden_id" id="hidden_id" />

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="exampleModal">
            <div class="modal-dialog modal-dialog-centered " style="width:560px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Form Edit User</h4>
                    </div>
                    <form method="post" id="sample_form1">
                        <div class="modal-body">

                            <div class="form">
                                <div class="form-group mb-3">
                                    <label for="name">Nama Lengkap</label>
                                    <input type="hidden" name="api_token" id="api_token" class="form-control " value="" disabled>
                                    <input type="text" name="name" class="form-control form-control-sm" id="name1" aria-describedby="name" value="" readonly>
                                </div>



                                @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                    <div class="form-group mb-3">
                                    <label>Unit Kerja</label>
                                    <select required class="form-control form-control-sm" name="kota" id="unit_kerja1" disabled>
                                        <option value="">- Pilih Unit Kerja -</option>
                                        @foreach ($kyns as $kar)
                                        <option value="{{$kar->unit}}">{{$kar->unit}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @else
                                <div class="form-group mb-3">
                                    <label>Unit Kerja</label>
                                    <select required class="form-control form-control-sm" name="kota" id="unit_kerja1" disabled>
                                        <option value="">- Pilih Unit Kerja -</option>
                                        @foreach ($kyn as $kar)
                                        <option value="{{$kar->unit}}">{{$kar->unit}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                 @endif
                                <div class="form-group mb-3">
                                    <label>Email Login</label>
                                    <input type="text" name="email" id="email1" class="form-control form-control-sm" value="" disabled>
                                </div>

                                <br />
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">Password</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm  wide" name="pass" id="pw">
                                            <option value="tetap">- Pilih Aksi -</option>
                                            <option value="reset">- Reset Password -</option>
                                            <option value="ganti">- Ganti Password -</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="myDIV" style="display:none">
                                    <div class="form-group row mb-3">
                                        <p class="col-sm-4">Password lama</p>
                                        <div class="col-sm-8">
                                            <input type="password" name="pwlama" class="form-control form-control-sm" placeholder="Password lama milik user ">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <p class="col-sm-4">Password baru</p>
                                        <div class="col-sm-8">
                                            <input type="password" name="pwbaru" class="form-control form-control-sm" placeholder="Masukan password baru untuk user ">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <p class="col-sm-4">Konfirmasi Password baru</p>
                                        <div class="col-sm-8">
                                            <input type="password" name="konpwbaru" class="form-control form-control-sm" placeholder="Masukan kembali password baru">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">Jam Kerja</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm wide" name="shift" id="jaker">
                                            @if(count($shift) > 0)
                                                <option selected="selected" value="">Pilih Shift</option>
                                            @foreach($shift as $c)
                                                <option value="{{ $c->shift }}">Shift {{$c->shift}} </option>
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">- Pilih Shift -</option>-->
                                            <!--@foreach ($shift as $val)-->
                                            <!--<option value="{{$val->shift}}">Shift {{$val->shift}}</option>-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                @if($company[0]->client != 1)
                                <h5 style="margin: 20px 0px -10px 0px;" ><b>HAK AKSES</b></h5>
                                <hr style="margin-bottom: 15px;" />
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">CORE</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm wide" id="level1" name="level">
                                             @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($level as $c)
                                               @if ($c->level != null)
                                                <option value="{{ $c->level }}">{{$c->level}} </option>
                                               @endif
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->level != null)-->
                                            <!--<option value="{{$lev->level}}">{{$lev->level}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">FINS</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm  wide" id="keuangan1" name="keuangan">
                                            @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($level as $c)
                                               @if ($c->keuangan != null)
                                                <option value="{{ $c->keuangan }}">{{$c->keuangan}} </option>
                                               @endif
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->keuangan != null)-->
                                            <!--<option value="{{$lev->keuangan}}">{{$lev->keuangan}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">HCM</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm  wide" id="kepegawaian1" name="kepegawaian">
                                            @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($level as $c)
                                               @if ($c->kepegawaian != null)
                                                <option value="{{ $c->kepegawaian }}">{{$c->kepegawaian}} </option>
                                               @endif
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->kepegawaian != null)-->
                                            <!--<option value="{{$lev->kepegawaian}}">{{$lev->kepegawaian}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">Kolekting</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm wide" id="kolekting1" name="kolekting">
                                            @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($level as $c)
                                               @if ($c->kolekting != null)
                                                <option value="{{ $c->kolekting }}">{{$c->kolekting}} </option>
                                               @endif
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->kolekting != null)-->
                                            <!--<option value="{{$lev->kolekting}}">{{$lev->kolekting}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">Setting</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm wide" id="pengaturan1" name="pengaturan">
                                            @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($level as $c)
                                               @if ($c->pengaturan != null)
                                                <option value="{{ $c->pengaturan }}">{{$c->pengaturan}} </option>
                                               @endif
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->pengaturan != null)-->
                                            <!--<option value="{{$lev->pengaturan}}">{{$lev->pengaturan}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">Pendidikan</label>
                                    <div class="col-sm-8">
                                        <select class="form-control  wide" id="pendidikan1" name="pendidikan">
                                            @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($level as $c)
                                               @if ($c->pendidikan != null)
                                                <option value="{{ $c->pendidikan }}">{{ str_replace('_', ' ', $c->pendidikan )}} </option>
                                               @endif
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->pengaturan != null)-->
                                            <!--<option value="{{$lev->pengaturan}}">{{$lev->pengaturan}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                @endif
                                <h5 style="margin: 20px 0px -10px 0px;"><b>Mobile Apps</b></h5>
                                <hr style="margin-bottom: 15px;" />
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4">Presence Apps</label>
                                    <div class="col-sm-8">
                                        <select id="presensi1" class="form-control form-control-sm wide" name="presensi">
                                            @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                                    @foreach($level as $c)
                                                       @if ($c->presensi != null)
                                                        <option value="{{ $c->presensi }}">{{$c->presensi}} </option>
                                                       @endif
                                                    @endforeach
                                             @else
                                                <option selected="selected" value="">Pilih Akses</option>
                                                <option value="admin">Admin</option>
                                                <option value="kacab">Kepala</option>
                                                <option value="karyawan">Karyawan</option>
                                            @endif
                                            
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->presensi != null)-->
                                            <!--<option value="{{$lev->presensi}}">{{$lev->presensi}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                <div id="myDIV1" style="display:none">
                                    <div class="form-group row mb-3">
                                        <label class="col-sm-4">Jenis</label>
                                        <div class="col-sm-8">
                                            <select class="form-control form-control-sm wide" id="jenis1" name="jenis">
                                                <option value="">- Pilih Jenis -</option>
                                                <option value="staf">Staf</option>
                                                <option value="lapangan">Lapangan</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($company[0]->client != 1)
                                <div class="form-group row mb-3">
                                    <label class="col-sm-4 ">Collecting Apps</label>
                                    <div class="col-sm-8">
                                        <select id="kolektor1" class="form-control form-control-sm wide" name="kolektor">
                                            @if(count($level) > 0)
                                                <option selected="selected" value="">Pilih Akses</option>
                                            @foreach($level as $c)
                                               @if ($c->kolektor != null)
                                                <option value="{{ $c->kolektor }}">{{$c->kolektor}} </option>
                                               @endif
                                            @endforeach
                                            @else
                                            @endif
                                            
                                            <!--<option value="">- Pilih Akses -</option>-->
                                            <!--@foreach ($level as $lev)-->
                                            <!--@if ($lev->kolektor != null)-->
                                            <!--<option value="{{$lev->kolektor}}">{{$lev->kolektor}}</option>-->
                                            <!--@endif-->
                                            <!--@endforeach-->
                                        </select>
                                    </div>
                                </div>
                                <div id="myDIV2" style="display:none">
                                    <div class="form-group row mb-3">
                                        <p class="col-sm-4">Minimal Transaksi</p>
                                        <div class="col-sm-8">
                                            <input type="text" name="minimal" id="minimal1" class="form-control form-control-sm" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);" value="" placeholder="contoh : Rp. 5.000">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <p class="col-sm-4">Target Kunjungan</p>
                                        <div class="col-sm-8">
                                            <input type="text" name="kunjungan" id="kunjungan1" class="form-control form-control-sm" onkeyup="Angka(this);" value="" placeholder="contoh : 40">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <p class="col-sm-4">Target Transaksi</p>
                                        <div class="col-sm-8">
                                            <input type="text" name="qty" id="qty1" class="form-control form-control-sm" onkeyup="Angka(this);" value="" placeholder="contoh : 30">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <p class="col-sm-4">Target Dana</p>
                                        <div class="col-sm-8">
                                            <input type="text" name="target" id="target1" class="form-control form-control-sm" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);" value="" placeholder="contoh : Rp. 25.000.000">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <p class="col-sm-4">Honor Per-Transaksi</p>
                                        <div class="col-sm-8">
                                            <input type="text" name="honor" id="honor1" class="form-control form-control-sm" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);" value="" placeholder="contoh : Rp. 1.350">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <p class="col-sm-4">Bonus Harian</p>
                                        <div class="col-sm-8">
                                            <input type="text" name="bonus" id="bonus1" class="form-control form-control-sm" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);" value="" placeholder="contoh : Rp. 30.000">
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <input type="hidden" name="hidden_id1" id="hidden_id1" />
                            <input type="hidden" name="hidden_id_com" id="hidden_id_com" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end modal-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                        <div class="col-md-4">
                           
                        </div>
                        @else
                        <h4 class="card-title">Management User</h4>
                        @endif
                        <div class="pull-right">
                            @if(Auth::user()->name == 'Management')
                            <a href="javascript:void(0)" class="btn btn-warning btn-sm waitt" style="margin-right: 10px">Cek User SSO</a>
                            @endif
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" id="flty" style="margin-right: 10px">Filter</button>
                            <a href="javascript:void(0)" class="btn btn-primary btn-sm weko" data-bs-toggle="modal" data-bs-target="#exampleModalo" onclick="getkar()">Registrasi Hak Akses</a>
                            <!--@if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)-->
                            <!--    <a href="javascript:void(0)" class="btn btn-primary btn-sm " data-bs-toggle="modal" data-bs-target="#exampleModalo12">Registrasi akun perusahaan</a>-->
                            <!--@endif-->
                        </div>
                    </div>
                    
                    <div class="card-body">
                        
                        <div class="row d-flex justify-content-center mb-3" style="display: none" id="hasas">
                            <div class="bg-collaps rounded" style="width: 97%;">
                                <div class="collapse" id="collapseExample" >
                                    <div class="row">
                                        @if(Auth::user()->pengaturan == 'admin' && Auth::user()->level_hc == 1)
                                                 <div class="col-md-3 mb-3">
                                                        <label class="form-label mt-3">Pilih Perusahaan</label> 
                                                     <button type="button" class="btn btn-primary btn-block btn-sm " id="button-perusahaan" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">Pilih Perusahaan</button>
                                                </div>
                                        @endif
                                        
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label mt-3">Tanggal Registrasi</label> 
                                            <input type="text" name="daterange" class="form-control ceks" id="daterange" autocomplete="off" />
                                        </div>
                                        
                                        @if(count($unit) > 0)
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label mt-3">Unit</label> 
                                            <select required class="cek1 multi" name="unit[]" id="unit" multiple="multiple">
                                                @foreach($unit as $u)
                                                <option value="{{$u->id}}">{{$u->unit}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif
                                        
                                        <div class=" mb-3 col-md-3">
                                            <label class="form-label mt-3">Status User</label> 
                                            <select id="stts" class="cek2 multi" multiple="multiple" style="width:100%" name="stts[]">
                                                <option value="1">Aktif</option>
                                                <option value="0">Nonaktif</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-12 col-sm-12 col-md-12">
                                <div class="table-responsive">
                                    <table id="user_table" class="table table-striped" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th hidden>#</th>
                                                <th>Nama Akun</th>
                                                <th>Email Login</th>
                                                <th>Unit Kerja</th>
                                                <!--<th>Level</th>-->
                                                <th>Edit</th>
                                                <th>Akses</th>
                                                <th>Hapus</th>
                                                <th hidden></th>
                                                @if(Auth::user()->level == 'admin' || Auth::user()->level_hc == 1)
                                                <th>Ganti Akun</th>
                                                @endif
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
</div>
@endsection