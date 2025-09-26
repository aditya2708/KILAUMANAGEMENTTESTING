@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

                    
        <!-- row -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Setting Warning</h4>
                        
                    </div>
                    
                    
                    <div class="card-body">
                            <table id="user_table" class="table table-striped" >
                                <thead>
                                    <tr>
                                        <th hidden>created_at</th>
                                        <th></th>
                                        <th class="cari">Petugas</th>
                                        <th class="cari">Donatur</th>
                                        <th class="cari">Program</th>
                                        <th class="cari">Nomor HP</th>
                                        <th class="cari">Alamat</th>
                                        <th class="cari">Jalur Kolekting</th>
                                        <th class="cari">Kota</th>
                                        <th class="cari">Status</th>
                                        <th>Dikolek</th>
                                        <th>Registrasi</th>
                                        <th>Aksi</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection