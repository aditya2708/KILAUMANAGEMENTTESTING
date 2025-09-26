@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!--<div class="row page-titles">-->
        <!--    <ol class="breadcrumb">-->
        <!--        <li class="breadcrumb-item active"><a href="javascript:void(0)">Donatur</a></li>-->
        <!--        <li class="breadcrumb-item"><a href="javascript:void(0)">Riwayat Kunjungan</a></li>-->
        <!--    </ol>-->
        <!--</div>-->

        <?php
        $data = DB::select("SELECT distinct status from donatur");
        $k = App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
        if ($k != null) {
            $datdon =  App\Models\Transaksi::where('kota', Auth::user()->kota)->orWhere('kota', $k->unit)->select('kota')->distinct()->get();
        }
        ?>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Notice</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <span id="form_result"></span>
                        <form method="post" id="alasan_form">
                            @csrf
                            <div class="form">
                                <label for="name">Alasan Transaksi Ditolak</label>
                                <input type="text" name="alasan" class="form-control" id="alasan" aria-describedby="name">
                                <input type="hidden" name="approval" class="form-control" id="name" aria-describedby="name" value="0">
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
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Kwitansi</h4>
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

        <div class="modal fade" id="modkwi" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Kwitansi</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
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
        <!-- End Modal -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Riwayat Kunjungan</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="user_table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <!--<th>id</th>-->
                                        <th>No</th>
                                        <th>ID Transaksi</th>
                                        <th>Kolektor</th>
                                        <th>Donatur</th>
                                        <!--<th>Program</th>-->
                                        <th>Sub Program</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <th>Jumlah</th>
                                        <th width="30px">Alamat Donatur</th>
                                        <th>Tgl</th>
                                        <!--<th>Hapus</th>-->
                                        <!--<th>Akses</th>-->
                                        <!--<th>Kwitansi</th>-->

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
@endsection