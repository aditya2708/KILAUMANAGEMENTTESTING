@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!-- Modal -->
        <div class="modal fade" id="modal-default">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h4 class="modal-title">Bukti Transaksi</h4>
                    </div>
                    <div class="modal-body">
                        <h1 align="center">
                            <img id="uploadPreview" width="550px" height="500px" style="object-fit: cover;" />
                        </h1>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        
        
        <!--Modal-->
        <div class="modal fade" id="modalso" >
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">List Campaign berkaitan dengan program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form class="form-horizontal" method="post" id="oyeh" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div id="DZ_W_Notification1" class="widget-media" style="height:380px;">
                                <ul class="timeline">
                                    <div id="mudeng">
                                
                                    </div>
                                </ul>
					        </div>
                        </div>
                        <!--<div class="modal-footer">-->
                        <!--    <div id="footay">-->
                                
                        <!--    </div>-->
                        <!--</div>-->
                    </form>
                </div>
            </div>
        </div>
        <!--End Modal-->

        <div class="modal fade" id="exampleModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form method="post" id="alasan_form">
                    @csrf
                    <div class="modal-body">
                        <span id="form_result"></span>
                            <div class="form">
                                <label for="name">Alasan Transaksi Ditolak</label>
                                <input type="text" name="alasan" class="form-control" id="alasan" aria-describedby="name">
                                <input type="hidden" name="approval" class="form-control" id="approval" aria-describedby="name" value="0">
                                <input type="hidden" name="notif" class="form-control" id="notif" aria-describedby="name" value="1">
                                <input type="hidden" name="id_hidden" id="id_hidden" />
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

        <div class="modal fade" id="modalkwitansi">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form>
                        <div class="modal-body">
                            <div class="form">
                                <label>Klik kirim nomor donatur lalu klik kirim kwitansi</label>
                                <input type="hidden" id="id_hide" name="id_hide">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div id="kon">

                            </div>
                            <!--<a class="btn btn-success" target="_blank" href="#">Kirim Langsung ke Donatur</a>-->
                            <!--<a class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#modkwi" href="">Kirim Melalui Admin</a>-->
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modkwi">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form>
                        <div class="modal-body">
                                <div class="form">
                                    <label>Klik kirim nomor donatur lalu klik kirim kwitansi</label>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <div id="keks">
    
                            </div>
                            <!--<a class="btn btn-primary" target="_blank" href="#">Kirim Kwitansi</a>-->
                            <!--<a class="btn btn-success" target="_blank" href="#">Kirim Nomor</a>-->
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="exampleModal3">
            <div class="modal-dialog modal-dialog-centered " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <form>
                    <div class="modal-body">
                            <div class="form">
                                <label>Pilih Akses</label>
                                <select class="form-control">
                                    <option>Approve</option>
                                    <option>Reject</option>
                                </select>
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
        <!-- END Modal -->

        <form class="form-horizontal" method="post" id="sample_form" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Informasi Donatur <a id="edit" class="btn btn-primary btn-xs" style="float:right; display:none; margin-left: 20px">Update</a></h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="basic-form">
                                                <div class="row">
                                                    <div class="mb-3 col-md-12">
                                                        <label class="form-label">Donatur :<sup>(1)</sup></label>
                                                        <select required class="select2" name="donatur" id="donatur" >
                                                            <option value="">- Pilih Donatur -</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3 col-md-12">
                                                        <label class="form-label">Id Donatur :</label>
                                                        <input type="text" name="id_donatur" id="id_donatur" class="form-control" value="" disabled>
                                                    </div>

                                                    <div class="mb-3 col-md-12">
                                                        <label class="form-label">Nomor Hp :</label>
                                                        <input type="text" name="nohp" id="nohp" class="form-control" value="" disabled>
                                                    </div>
                                                    <div class="mb-3 col-md-12">
                                                        <label class="form-label">Alamat :</label>
                                                        <textarea id="alamat" class="form-control" name="alamat" rows="4" cols="50" style="height: 100px;" disabled></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="btn_update" style="display: none;float:right">
                                                    <div class="col-md-12">
                                                        <a id="update" class="btn btn-primary btn-sm">Simpan</a>
                                                        <a id="btlupdate" class="btn btn-danger btn-sm">Batal</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="id_camp" name="id_camp">

                        <div class="col-xl-8 col-lg-8">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Entry Transaksi</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="basic-form">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="row">
                                                            <div class="mb-3 col-md-6">
                                                                <label class="form-label">Petugas :<sup>(2)</sup></label>
                                                                <select required class="js-example-basic-single" name="petugas" id="petugas">
                                                                    <option value="">- Pilih Petugas -</option>
                                                                    @foreach ($petugas as $j)
                                                                    <option value="{{$j->id}}" data-value="{{$j->name}}">{{$j->name}} ({{$j->jabatan}})</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="mb-3 col-md-6">
                                                                <label class="form-label">Pembayaran :<sup>(3)</sup></label>
                                                                <select required class="form-control " name="pembayaran" id="pembayaran">
                                                                    <option value="">- Pilih Pembayaran -</option>
                                                                    <option value="teller">Teller</option>
                                                                    <option value="transfer">Transfer</option>
                                                                    <option value="noncash">Noncash</option>
                                                                    <option value="dijemput">Dijemput</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="mb-3 col-md-6">
                                                                <label class="form-label">Program :<sup>(4)</sup></label>
                                                                <select required class="select3 get_pros wewe" style="width: 100%;" name="program" id="program" disabled>
                                                                    <option value="">- Pilih Program -</option>
                                                                </select>
                                                                <!--<p style="font-size: 10px">*pilih nama donatur lebih dahulu</p>-->
                                                            </div>

                                                            <div class="mb-3 col-md-6">
                                                                <label id="judulnih" class="form-label">Jumlah :<sup>(5)</sup></label>
                                                                <input type="text" name="jumlah" id="jumlah" class="form-control" onkeyup="rupiah(this);" value="" placeholder="contoh : Rp. 0">
                                                                <p id="terbilang" style="font-size:12px"></p>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="mb-3 col-md-6">
                                                                <label class="form-label">Keterangan :<sup>(6)</sup></label>
                                                                <input type="text" name="keterangan" id="keterangan" class="form-control" value="" placeholder="Keterangan">
                                                            </div>

                                                            <div class="mb-3 col-sm-6">
                                                                <label class="form-label">Tanggal :<sup>(7)</sup><span class="badge badge-xs badge-danger light" style="float: right; cursor: pointer" id="backdate">back date</label></label>

                                                                <div id="cobain" class="mt-3"></div>
                                                                <input type="hidden" id="bck" value="tutup">
                                                                <div id="jam" hidden>
                                                                    <input type="text" name="tgl" class="form-control datess ceks" id="tgl" autocomplete="off" placeholder="Isi Tanggal">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input name="id_pros_hide_hide" id="id_pros_hide_hide" type="hidden">
                                                    <input name="jp_hide_hide" id="jp_hide_hide" type="hidden">
                                                    <div class="col-md-4">
                                                        <div id="plhcoa" style="display: none">
                                                            <div class="row">
                                                                <div class="col-md-12 mb-3">
                                                                    <label class="form-label">Non Cash :</label>
                                                                    <select required class="js-example-basic-singlex" name="non_cash" id="non_cash">
                                                                        <option value="">- Pilih Coa -</option>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="plhbank" style="display: none">
                                                            <div class="row">
                                                                <div class="col-md-12 mb-3">
                                                                    <label class="form-label">Bank :</label>
                                                                    <select required class="form-control default-select wide " style="width: 100%;" name="id_bank" id="id_bank">
                                                                        <option value="">- Pilih Bank -</option>
                                                                        @foreach($bank as $val)
                                                                        <option value="{{$val->id_bank}}">{{$val->nama_bank}} ( {{$val->unit}} )</option>
                                                                        @endforeach

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-12 mb-3">
                                                                <label class="form-label">Bukti Nota / Nominal :</label>
                                                                <div class="input-group">
                                                                    <div class="form-file">
                                                                        <input type="file" class="form-file-input form-control" onchange="encodeImageFileAsURL(this)" name="bukti" id="bukti">
                                                                    </div>
                                                                </div>

                                                                <input type="hidden" id="nama_file" value="">
                                                                <input type="hidden" id="base64" value="">
                                                                <label style="font-size: 11px" class="mt-2">(Note) wajib diisi jika pembayaran transfer dan non cash</label>
                                                            </div>
                                                        </div>
                                                            
                                                            <div id="bkt" style="display: none">
                                                                <div class="row">
                                                                    <div class="col-md-12 mb-3">
                                                                        <label class="form-label">Bukti Barang / Kegiatan:</label>
                                                                        <div class="input-group">
                                                                            <div class="form-file">
                                                                                <input type="file" class="form-file-input form-control" onchange="encodexImageFileAsURL(this)" name="buktix" id="buktix">
                                                                            </div>
                                                                        </div>
    
                                                                        <input type="hidden" id="nama_filex" value="">
                                                                        <input type="hidden" id="base64x" value="">
                                                                        <label style="font-size: 11px" class="mt-2">(Note) wajib diisi jika pembayaran non cash</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <div class="row"></div>
                                                        <div class="row"></div>
                                                        <div class="row"></div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <a id="add" class="btn btn-primary btn-sm" style="margin-top: 25px"> Tambah</a>
                                                            </div>
                                                            <div id="gmb" class="col-md-6" style="display:none">
                                                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modal-default" style="margin-top: 25px">Lihat Bukti</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="user_table_1" class="table table-responsive-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Donatur</th>
                                                            <th>Petugas</th>
                                                            <th>Program</th>
                                                            <th>Pembayaran</th>
                                                            <th>Keterangan</th>
                                                            <th>Jumlah</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody id="table">

                                                    </tbody>
                                                    <tfoot id="foot">

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
        </form>
        <div class="row" id="datatrs" style="display: none">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Transaksi Terbaru</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="user_table" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Transaksi</th>
                                        <th>Kolektor</th>
                                        <th>Donatur</th>
                                        <th>Sub Program</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <th>Jumlah</th>
                                        <th>Alamat Donatur</th>
                                        <th>Tgl</th>
                                        <th>Status Transaksi</th>
                                        <th>Hapus</th>
                                        <th>Akses</th>
                                        <th>Kwitansi</th>
                                    </tr>
                                </thead>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection