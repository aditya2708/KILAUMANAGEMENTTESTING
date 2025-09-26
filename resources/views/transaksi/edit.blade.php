@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">

        
        <!--<div id="myModal" class="modal">-->
        <!--    <span class="btn-close tutup"></span>-->

            <!-- Modal Content (The Image) -->
        <!--    <img class="modal-content" id="img01">-->
        <!--    <div id="caption"></div>-->
        <!--</div>-->
        
        <!--<div id="myModal1" class="modal">-->
        <!--    <span class="btn-close tutup2"></span>-->

            <!-- Modal Content (The Image) -->
        <!--    <img class="modal-content" id="img02">-->

            <!-- Modal Caption (Image Text) -->
        <!--    <div id="caption1"></div>-->
        <!--</div>-->
        
        <!--<div id="myModal2" class="modal">-->
        <!--    <span class="btn-close tutup3"></span>-->

            <!-- Modal Content (The Image) -->
        <!--    <img class="modal-content" id="img03">-->

            <!-- Modal Caption (Image Text) -->
        <!--    <div id="caption2"></div>-->
        <!--</div>-->

        <div class="row">
            <!--<form class="form-horizontal" method="post" id="simple_form">-->
                <!--@csrf-->
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="basic-form">
                                                <input type="hidden" name="wowwww" class="form-control " id="wowwww" value="{{$transaksicari->id}}" >
                                                <input type="hidden" name="pembr" class="form-control " id="pembr" value="{{$transaksicari->pembayaran}}" >
                                                <input type="hidden" name="proggg" class="form-control " id="proggg" value="{{$transaksicari->subprogram}}" >
                                                <div class="row">
                                                    <div class="mb-3 col-md-4">
                                                        <label class="form-label">Donatur</label>
                                                        <select id="donat" class="pildon " name="donat">
                                                            <option value="{{$transaksicari->id_donatur}}" data-value="{{$transaksicari->donatur}}" >{{$transaksicari->donatur}} - {{$transaksicari->alamat}}</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group">
                                                            <div class="col-md-12">
                                                                <label>Tanggal</label>
                                                                <input type="date" name="ttl" class="form-control " id="ttl" value="{{$transaksicari->tanggal}}" placeholder="Tanggal">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group ">
                                                            <div class="col-md-12">
                                                                <label for="">Nominal</label>
                                                                <input type="text" name="ednom" onkeyup="rupiah(this);" class="form-control " id="ednom" value="{{$transaksicari->jumlah}}" aria-describedby="" placeholder="Nominal">

                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group ">
                                                            <div class="col-md-12">
                                                                <label>Petugas</label>
                                                                <select required class="asssa" style="width: 100%;" name="petugas" id="petugas">
                                                                    <option value="{{$transaksicari->id_koleks}}" data-value="{{$transaksicari->kolektor}} ">{{$transaksicari->kolektor}}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-4 mb-3">
                                                        <div class="form-group ">
                                                            <div class="col-md-12">
                                                                <label>Pembayaran</label>
                                                                <select class="form-control "   id="pbyr" name="pbyr">
                                                                    <!--<option value=" {{$transaksicari->pembayaran}}" data-value="{{$transaksicari->pembayaran}}"> {{$transaksicari->pembayaran}}</option>-->
                                                                      <option value="">- Pilih Pembayaran -</option>
                                                                     <option value="teller"<?= $transaksicari->pembayaran == 'teller' ? ' selected="selected"' : ''; ?>>Teller</option>
                                                                     <option value="transfer"<?= $transaksicari->pembayaran == 'transfer' ? ' selected="selected"' : ''; ?>>Transfer</option>
                                                                     <option value="noncash"<?= $transaksicari->pembayaran == 'noncash' ? ' selected="selected"' : ''; ?>>Noncash</option>
                                                                     <option value="dijemput"<?= $transaksicari->pembayaran == 'dijemput' ? ' selected="selected"' : ''; ?>>Dijemput</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                    
                                                   <div class="col-md-4 mb-3">
                                                        <div class="form-group ">
                                                            <div class="col-md-12">
                                                                <label for="">Program</label>
                                                                <select  id="edprog" class="form-control  js-example-basic-single" style="width: 100%;" name="edprog">
                                                                    <option value="{{$transaksicari->id_program}}" data-value="{{$transaksicari->subprogram}}" >{{$transaksicari->subprogram}}</option>
                                                                    @foreach ($data1 as $j)
                                                                   <option value="{{$j->id_program}}" data-value="{{$j->program}}"  data-valueindi="{{$j->coa_individu}}" data-valueentitas="{{$j->coa_entitas}}" >{{$j->program}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                    
                                                    
                                                    <!--<div class="col-md-4 mb-3">-->
                                                    <!--    <div class="form-group ">-->
                                                    <!--        <div class="col-md-12">-->
                                                    <!--            <label>tet</label>-->
                                                    <!--            <select class="form-control "  id="pbyr" name="pbyr">-->
                                                    <!--                <option value=" {{$transaksicari->pembayaran}}"> {{$transaksicari->pembayaran}}</option>-->
                                                    <!--                 <option value="teller">Teller</option>-->
                                                    <!--                 <option value="transfer">Transfer</option>-->
                                                    <!--                 <option value="noncash">Noncash</option>-->
                                                    <!--                 <option value="dijemput">Dijemput</option>-->
                                                    <!--            </select>-->
                                                    <!--        </div>-->
                                                    <!--    </div>-->
                                                    <!--</div>-->
                                   
                                   
                                                <div class="row">      
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Bukti Nota / Nominal :</label>
                                                        <div class="input-group">
                                                            <div class="form-file">
                                                                <input type="file" class="form-file-input form-control" onchange="encodeImageFileAsURL(this)" name="buktinota" id="buktinota">
                                                            </div>
                                                        </div>
                                                        <input type="hidden" id="nama_file" value="">
                                                        <input type="hidden" id="base64" value="">
                                                    
                                                        <label style="font-size: 11px" class="mt-2">(Note) wajib diisi jika pembayaran transfer dan non cash</label>
                                                    </div>
                                                    
                                                    <div class="col-md-4 mb-3" >
                                                        <div id="plhbank" hidden>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="form-label">Bank :</label>
                                                                    <select required class="form-control " style="width: 100%;" name="ed_bank" id="ed_bank">
                                                                        @foreach($bank as $val)
                                                                        <option value="{{$val->id_bank}}" {{ $transaksicari->id_bank == $val->id_bank ? 'selected' : '' }}>{{$val->no_rek}} ({{$val->nama_bank}}) </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                            
                                                        <div id="plhcoa" hidden>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="form-label">Non Cash :</label>
                                                                    <?php $aw = App\Models\COA::where('coa', $transaksicari->coa_debet); ?>
                                                                    @if( count($aw->get()) > 0 )
                                                                        <?php $ehe = $aw->first()->nama_coa; ?>
                                                                    @else
                                                                        <?php $ehe = ''; ?>
                                                                    @endif
                                                                    <select  class="bumm" style="width: 100%;" name="non_cash" id="non_cash">
                                                                        <option value="{{$transaksicari->coa_debet}}">{{ $ehe }}</option>
                                                                        <option value="">- Pilih Coa -</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>    
                                                    <!--</div>-->
                                                      
                                                    <div class="col-md-4 mb-3">
                                                        <div id="bkt" hidden>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="form-label">Bukti Barang / Kegiatan:</label>
                                                                    <div class="input-group">
                                                                        <div class="form-file">
                                                                            <input type="file" class="form-file-input form-control" onchange="encodexImageFileAsURL(this)" name="buktinya" id="buktinya">
                                                                        </div>
                                                                    </div>
                                                                
                                                                    <input type="hidden" id="nama_filex" value="">
                                                                    <input type="hidden" id="base64x" value="">
                                                                    <label style="font-size: 11px" class="mt-2">(Note) wajib diisi jika pembayaran non cash</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    
                                                    
                                            <div class="col-md-4 mb-3">
                                                <label for="">Keterangan :</label>
                                                <input value="{{$transaksicari->keterangan}}" id="edket" class="form-control input-sm" name="edket" rows="4" cols="100"></input>
                                            </div>
                                                    
                                                    <!--<div class="col-md-4 mb-3">-->
                                                    <!--        <div class="form-group">-->
                                                    <!--            <div class="col-md-12">-->
                                                    <!--                <label for="">Keterangan :</label>-->
                                                    <!--                <textarea id="alamat" class="form-control input-sm" name="alamat" rows="4" cols="50">{{$transaksicari->keterangan}}</textarea>-->
                                                    <!--            </div>-->
                                                    <!--        </div>-->
                                                    <!--    </div>-->
                                                   
                                                        
                                                </div>
                                                 
                                                      
                                                        
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        
                                                        <div class="form-group">
                                                            <div class="col-md-12 mb-3">
                                                            <button type="button" class="btn btn-success btn-sm editod"
                                                            id="simpan"
                                                            id2="{{$transaksicari->id}}" 
                                                            akun="{{$transaksicari->akun}}"
                                                            alamat="{{$transaksicari->alamat}}"
                                                            approval="{{$transaksicari->approval}}"
                                                            bukti="{{$transaksicari->bukti}}"
                                                            bukti2="{{$transaksicari->bukti2}}"
                                                            coa_debet="{{$transaksicari->coa_debet}}"
                                                            coa_kredit="{{$transaksicari->coa_kredit}}"
                                                            created_at="{{$transaksicari->created_at}}"
                                                            donatur="{{$transaksicari->donatur}}"
                                                            id_bank="{{$transaksicari->id_bank }}"
                                                            id_camp="{{$transaksicari->id_camp}}"
                                                            id_donatur="{{$transaksicari->id_donatur}}"
                                                            id_kantor="{{$transaksicari->id_kantor}}"
                                                            id_koleks="{{$transaksicari->id_koleks}}"
                                                            id_program="{{$transaksicari->id_program}}"
                                                            id_pros="{{$transaksicari->id_pros}}"
                                                            id_sumdan="{{$transaksicari->id_sumdan}}"
                                                            id_transaksi="{{$transaksicari->id_transaksi}}"
                                                            jumlah="{{$transaksicari->jumlah}}"
                                                            kantor_induk="{{$transaksicari->kantor_induk}}"
                                                            ket_penerimaan="{{$transaksicari->ket_penerimaan}}"
                                                            keterangan="{{$transaksicari->keterangan}}"
                                                            kolektor="{{$transaksicari->kolektor}}"
                                                            kota="{{$transaksicari->kota}}"
                                                            via_input="{{$transaksicari->via_input}}"
                                                            name="{{$transaksicari->name}}"
                                                            notif="{{$transaksicari->notif}}"
                                                            pembayaran="{{$transaksicari->pembayaran}}"
                                                            program="{{$transaksicari->program}}"
                                                            qty="{{$transaksicari->qty}}"
                                                            status="{{$transaksicari->status}}"
                                                            subprogram="{{$transaksicari->subprogram}}"
                                                            subtot="{{$transaksicari->subtot}}"
                                                            tanggal="{{$transaksicari->tanggal}}"
                                                            user_approve="{{$transaksicari->user_approve}}"
                                                            user_insert="{{$transaksicari->user_insert}}"
                                                            user_update="{{$transaksicari->user_update}}"
                                                            updated_at="{{$transaksicari->updated_at}}"
                                                            style="width:100%; margin-bottom: -20px">Simpan</button>
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
                </div>
            <!--</form>-->
        </div>

    </div>
</div>
@endsection