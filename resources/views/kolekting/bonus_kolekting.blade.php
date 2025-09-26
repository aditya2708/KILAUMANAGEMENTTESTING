@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Filter</h3>
                                    </div>

                                    <div class="card-body">
                                        <div class="basic-form">
                                            <div class="row">

                                                <?php
                                                $k = \App\Models\Kantor::where('kantor_induk', Auth::user()->id_kantor)->first();
                                                if ($k != null) {
                                                    $datdon =  \App\Models\Kantor::where('id', Auth::user()->id_kantor)->orWhere('kantor_induk', Auth::user()->id_kantor)->select('unit', 'id')->get();
                                                }
                                                ?>
                                                @if(Auth::user()->level == 'kacab')
                                                @if($k != null)
                                                <div class="col-md-6 mb-3">
                                                    <select id="plhtgl" class="form-control default-select wide" name="plhtgl">
                                                        <option value="0">Periode</option>
                                                        <option value="1">Bulan</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3" id="kotas" style="display:block;">
                                                    <select class="form-control default-select wide" id="val_kot" name="kotas">
                                                        <option value="">Pilih Kota</option>
                                                        @foreach ($datdon as $item)
                                                        <option value="{{$item->id}}">{{$item->unit}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @else
                                                <div class="col-md-6 mb-3">
                                                    <select id="plhtgl" class="form-control default-select wide" name="plhtgl">
                                                        <option value="0">Periode</option>
                                                        <option value="1">Bulan</option>
                                                    </select>
                                                </div>
                                                @endif
                                                @elseif(Auth::user()->level == 'admin')
                                                <div class="col-md-6 mb-3">
                                                    <select id="plhtgl" class="form-control default-select wide" name="plhtgl">
                                                        <option value="0">Periode</option>
                                                        <option value="1">Bulan</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3" id="kotas" style="display:block;">
                                                    <select class="form-control default-select wide" id="val_kot" name="kotas">
                                                        <option value="">Pilih Kota</option>
                                                        @foreach ($kotas as $item)
                                                        <option value="{{$item->id}}">{{$item->unit}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @endif
                                                <input type="hidden" id="val">
                                                <input type="hidden" id="val1">
                                                <!--</div>-->
                                            </div>
                                            <div class="row">

                                                <div class="col-md-12 mb-3" id="blnbln" hidden>
                                                    <label>Bulan&Tahun :</label>
                                                    <input type="text" class="form-control daterange cek4" name="blns" id="blns" autocomplete="off" placeholder="contoh {{date('m-Y') }}">
                                                </div>
                                                <div class="col-md-6 mb-3" id="tgldari">
                                                    <label>Dari</label>
                                                    <input type="date" class="form-control" id="darii" name="dari">
                                                </div>
                                                <div class="col-md-6 mb-3" id="tglke">
                                                    <label>Ke</label>
                                                    <input type="date" class="form-control" id="sampaii" name="sampai">
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button class="btn btn-primary btn-sm col-md-12" id="filterr">Filter</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                            @if(Auth::user()->level == 'admin')
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <!-- /.box -->
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Bonus Kantor</h3>
                                        <!-- /.box-tools -->
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="user_table2" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>kantor</th>
                                                        <th>Total Bonus</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tab">

                                                </tbody>
                                                <tfoot>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                        </div>

                    </div>
                    
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Bonus Kolekting</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="user_table" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama Kolektor</th>
                                                        <th>Total 1</th>
                                                        <th>Total 2</th>
                                                        <th>Total Honor</th>
                                                        <th>Total Bonus Capaian</th>
                                                        <th>Total 1</th>
                                                        <th>Total 2</th>
                                                        <th>Total Bonus Omset</th>
                                                        <th>Total Bonus</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th id="totbonnn"></th>

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
    </div>
</div>
@endsection