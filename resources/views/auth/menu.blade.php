@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        <!-- Modal -->
        <div class="modal fade" id="exampleModal">
            <div class="modal-dialog modal-dialog-centered" style="max-width:600px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Akses Menu</h4>
                        <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
                        <!--</button>-->
                    </div>
                    <span id="form_result"></span>
                    <form method="post" id="uwuw">
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="id_hide"  id="id_hide">
                            <select class="form-control multi" id="akses" name="akses[]" multiple="multiple">
                                @foreach ($kkk as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                            
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
                    <!--<div class="card-header">-->
                    <!--    <h4 class="card-title">Data Jabatan</h4>-->
                    <!--    <div class="pull-right">-->
                    <!--        <a href="javascript:void(0)" class="btn btn-primary btn-xxs editoo" data-bs-toggle="modal" id="record" data-bs-target="#exampleModal" >Tambah Jabatan</a>-->
                    <!--    </div>-->
                    <!--</div>-->
    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="user_table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th hidden>id</th>
                                        <th>Menu</th>
                                        <th>Parent</th>
                                        <th>URL</th>
                                        <th>Akses</th>
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