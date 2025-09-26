@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        <!--modal-->
        <div class="modal fade" id="modaleditsaldo">
            <div class="modal-dialog modal-dialog-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Edit Saldo Dana</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <form method="post" id="form">
                        <div class="modal-body">
                            @csrf
                            <div class="basic-form">
                                <input type="hidden" id="id_hide" name="id_hide">
                                <div class="mb-3 row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">Saldo Dana : </label>
                                    <div class="col-sm-4">
                                      <input type="text" class="form-control" id="sd" name="sd">
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">Dana Expense : </label>
                                    <div class="col-sm-10">
                                      <select class="form-control multi" id="de" name="de[]" multiple="multiple">
                                          
                                      </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">Dana Revenue : </label>
                                    <div class="col-sm-10">
                                      <select class="form-control multi" id="dr" name="dr[]" multiple="multiple">
                                          
                                      </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">Operasi : </label>
                                    <div class="col-sm-4">
                                      <select class="form-control" id="op" name="op">
                                          <option value="y">Iya</option>
                                          <option value="t">Tidak</option>
                                      </select>
                                    </div>
                                </div>
                                
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
        <!--end-->
        
        <!--isi-->
        <div class="row">
            
            <div class="col-lg-12">
                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-striped" id="user_table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>COA</th>
                                            <th>Nama Akun</th>
                                            <th>COA Expense</th>
                                            <th>COA Revenue</th>
                                            <th>Operasi</th>
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
        <!--end-->
        
    </div>
</div>
@endsection